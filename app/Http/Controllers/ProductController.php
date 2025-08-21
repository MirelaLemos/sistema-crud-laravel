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

    // SALVAR (admin)
    public function store(Request $request)
    {
        $data = $request->validate([
            'name'        => ['required', 'string', 'min:3', 'max:150'],
            'price'       => ['required', 'numeric', 'min:0'],
            'description' => ['nullable', 'string', 'max:2000'],
            'photo'       => ['nullable', 'image', 'max:2048'], // 2MB
        ]);

        // usa o disco padrão (public ou s3), conforme FILESYSTEM_DISK
        $disk = config('filesystems.default');

        if ($request->hasFile('photo')) {
            // salva com visibilidade pública (útil no S3)
            $data['photo_path'] = $request->file('photo')->storePublicly('products', $disk);
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

    // ATUALIZAR (admin)
    public function update(Request $request, Product $product)
    {
        $data = $request->validate([
            'name'        => ['required', 'string', 'min:3', 'max:150'],
            'price'       => ['required', 'numeric', 'min:0'],
            'description' => ['nullable', 'string', 'max:2000'],
            'photo'       => ['nullable', 'image', 'max:2048'],
        ]);

        $disk = config('filesystems.default');

        if ($request->hasFile('photo')) {
            // remove a foto antiga (no mesmo disk atual)
            if ($product->photo_path) {
                try {
                    Storage::disk($disk)->delete($product->photo_path);
                } catch (\Throwable $e) {
                    Log::warning('Falha ao remover imagem antiga', [
                        'path' => $product->photo_path,
                        'error' => $e->getMessage(),
                    ]);
                }
            }

            $data['photo_path'] = $request->file('photo')->storePublicly('products', $disk);
        }

        $product->update($data);

        return redirect()
            ->route('products.show', $product)
            ->with('ok', 'Produto atualizado com sucesso!');
    }

    // EXCLUIR (admin)
    public function destroy(Product $product)
    {
        $disk = config('filesystems.default');

        try {
            if ($product->photo_path) {
                Storage::disk($disk)->delete($product->photo_path);
            }

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
}
