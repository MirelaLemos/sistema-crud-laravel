<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
{
    $products = Product::latest()->paginate(12);

    $cart = session('cart', []);
    $cartTotal = collect($cart)->sum(fn($i) => $i['price'] * $i['qty']);
    $cartQty   = collect($cart)->sum(fn($i) => $i['qty']);

    return view('products.index', compact('products', 'cartTotal', 'cartQty'));
}

public function create() { return view('products.create'); }

public function store(Request $request) {
    $data = $request->validate([
        'name'=>'required|string|max:255',
        'description'=>'nullable|string',
        'price'=>'required|numeric|min:0',
        'photo'=>'nullable|image|max:2048'
    ]);
    if ($request->hasFile('photo')) {
        $data['photo_path'] = $request->file('photo')->store('products','public');
    }
    Product::create($data);
    return redirect()->route('products.index')->with('ok','Produto criado!');
}

public function edit(Product $product) { return view('products.edit', compact('product')); }

public function update(Request $request, Product $product) {
    $data = $request->validate([
        'name'=>'required|string|max:255',
        'description'=>'nullable|string',
        'price'=>'required|numeric|min:0',
        'photo'=>'nullable|image|max:2048'
    ]);
    if ($request->hasFile('photo')) {
        $data['photo_path'] = $request->file('photo')->store('products','public');
    }
    $product->update($data);
    return redirect()->route('products.index')->with('ok','Produto atualizado!');
}

public function destroy(Product $product) {
    $product->delete();
    return back()->with('ok','Produto removido!');
}

public function show(Product $product) {
    return view('products.show', compact('product'));
}

}
