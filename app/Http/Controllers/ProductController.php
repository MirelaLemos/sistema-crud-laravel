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

    // SALVAR (admin) — força upload no S3 sempre
    public function store(Request $request)
    {
        $data = $request->validate([
            'name'        => ['required', 'string', 'min:3', 'max:150'],
            'price'       => ['required', 'numeric', 'min:0'],
            'description' => ['nullable', 'string', 'max:2000'],
            'photo'       => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
        ]);

        if ($request->hasFile('photo')) {
            $this->assertS3Configured();
            // não usamos ACL (compatível com bucket sem ACL)
            $path = $request->file('photo')->store('products', 's3'); // >>> S3
            $data['photo_path'] = $path; // ex: products/xyz.png
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

    // ATUALIZAR (admin) — apaga antiga e sobe nova (sempre no S3)
    public function update(Request $request, Product $product)
    {
        $data = $request->validate([
            'name'        => ['required', 'string', 'min:3', 'max:150'],
            'price'       => ['required', 'numeric', 'min:0'],
            'description' => ['nullable', 'string', 'max:2000'],
            'photo'       => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
        ]);

        if ($request->hasFile('photo')) {
            $this->assertS3Configured();

            // remove imagem antiga (tenta em s3 e public)
            $this->safeDeleteFromKnownDisks($product->photo_path);

            $path = $request->file('photo')->store('products', 's3'); // >>> S3
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

    /** Garante que o disco s3 exista/configurado; lança erro claro se não. */
    protected function assertS3Configured(): void
    {
        if (!array_key_exists('s3', config('filesystems.disks', []))) {
            abort(500, 'Disco S3 não está configurado em config/filesystems.php');
        }
    }

    /** Tenta apagar um caminho conhecido em múltiplos discos (s3 e public). */
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
