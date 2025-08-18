<?php

namespace App\Http\Controllers;
use App\Models\Product;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function index() {
        $cart = session('cart', []);
        $total = collect($cart)->sum(fn($i) => $i['price'] * $i['qty']);
        return view('cart.index', compact('cart','total'));
    }
    public function add(Product $product) {
        $cart = session('cart', []);
        if (isset($cart[$product->id])) {
            $cart[$product->id]['qty']++;
        } else {
            $cart[$product->id] = [
                'id'   => $product->id,
                'name' => $product->name,
                'price'=> $product->price,
                'photo'=> $product->photo_path,
                'qty'  => 1,
            ];
        }
        session(['cart'=>$cart]);
        return back()->with('ok','Adicionado!');
    }
    public function updateQty(Request $r, int $id) {
        $qty = max(1, (int) $r->input('qty', 1));
        $cart = session('cart', []);
        if (isset($cart[$id])) {
            $cart[$id]['qty'] = $qty;
            session(['cart'=>$cart]);
            $itemTotal = $cart[$id]['price'] * $cart[$id]['qty'];
            $cartTotal = collect($cart)->sum(fn($i) => $i['price'] * $i['qty']);
        } else {
            
            $itemTotal = 0;
            $cartTotal = collect($cart)->sum(fn($i) => $i['price'] * $i['qty']);
        }
        return response()->json([
            'ok' => true,
            'qty' => $qty,
            'item_total' => $itemTotal,
            'cart_total' => $cartTotal,
        ]);
    }

    
    public function remove(int $id) {
        $cart = session('cart', []);
        unset($cart[$id]);
        session(['cart'=>$cart]);
        return back()->with('ok','Item removido!');
    }

    public function clear() {
        session()->forget('cart');
        return back()->with('ok','Carrinho limpo!');
    }
}

