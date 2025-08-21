<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    // LISTAR (público)
    public function index(Request $request)
    {
        $products = Product::latest()->paginate(12);
        return view('products.index', ['produtos' => $products]);
    }

    // VER (público)
    public function show(Product $product)
    {
        return view('products.show', compact('product'));
    }

    // NOVO (admin)
    public function create()
    {
        return view('products.create');
    }

    // SALVAR (admin) — força upload no S3
    public function store(Request $request)
    {
        $data = $request->validate([
            'name'        => ['required', 'string', 'min:3', 'max:150'],
            'price'       => ['required', 'numeric', 'min:0'],
            'description' => ['nullable', 'string', 'max:2000'],
            'photo'       => ['nullable', 'image', 'max:2048'], // 2MB
        ]);

        if ($request->hasFile('photo')) {
            $disk = $this->preferredUploadDisk(); // 's3' se existir, senão default
            // não setamos ACL/visibility (evita erro em bucket com ACL desativada)
            $path = $request->file('photo')->store('products', $disk);
            $data['photo_path'] = $path; // ex: products/abc.png
        }

        $product = Product::create($data);

        return redirect()
            ->route('products.show', $product)
            ->with('ok', 'Produto criado com sucesso!');
    }

    // EDITAR (admin)
    public function edit(Product $product)
    {
        return view('products.edit', compact('product'));
    }

    // ATUALIZAR (admin) — apaga antiga e sobe nova (S3 por padrão)
    public function update(Request $request, Product $product)
    {
        $data = $request->validate([
            'name'        => ['required', 'string', 'min:3', 'max:150'],
            'price'       => ['required', 'numeric', 'min:0'],
            'description' => ['nullable', 'string', 'max:2000'],
            'photo'       => ['nullable', 'image', 'max:2048'],
        ]);

        if ($request->hasFile('photo')) {
            // tenta remover do S3 e do local (caso antigos estejam em outro disco)
            $this->safeDeleteFromKnownDisks($product->photo_path);

            $disk = $this->preferredUploadDisk();
            $path = $request->file('photo')->store('products', $disk);
            $data['photo_path'] = $path;
        }

        $product->update($data);

        return redirect()
            ->route('products.show', $product)
            ->with('ok', 'Produto atualizado com sucesso!');
    }

    // EXCLUIR (admin)
    public function destroy(Product $product)
    {
        try {
            $this->safeDeleteFromKnownDisks($product->photo_path);

            $product->delete();

            return redirect()
                ->route('products.index')
                ->with('ok', 'Produto excluído com sucesso!');
        } catch (\Throwable $e) {
            Log::error('products.destroy', [
                'msg'   => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return back()->withErrors('Falha ao excluir: ' . $e->getMessage());
        }
    }

    /* =========================
     * Helpers
     * ========================= */

    /**
     * Retorna o disco preferido para upload.
     * Usa 's3' se estiver configurado; senão, cai no default do app.
     */
    protected function preferredUploadDisk(): string
    {
        return array_key_exists('s3', config('filesystems.disks', []))
            ? 's3'
            : config('filesystems.default', 'public');
    }

    /**
     * Tenta apagar um caminho conhecido em múltiplos discos (s3 e public).
     */
    protected function safeDeleteFromKnownDisks(?string $path): void
    {
        if (!$path) return;

        foreach (['s3', 'public'] as $disk) {
            try {
                if (array_key_exists($disk, config('filesystems.disks', []))) {
                    Storage::disk($disk)->delete($path);
                }
            } catch (\Throwable $e) {
                Log::warning('Falha ao deletar arquivo', [
                    'disk' => $disk,
                    'path' => $path,
                    'err'  => $e->getMessage(),
                ]);
            }
        }
    }
}
