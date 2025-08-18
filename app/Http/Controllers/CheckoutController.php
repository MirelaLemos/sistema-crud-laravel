<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Stripe\Stripe;
use Stripe\Checkout\Session as StripeSession;

class CheckoutController extends Controller {
    public function checkout() {
        $cart = session('cart', []);
        if (empty($cart)) return redirect()->route('cart.index');

        Stripe::setApiKey(config('services.stripe.secret'));

        $lineItems = [];
        foreach ($cart as $item) {
            $lineItems[] = [
                'price_data' => [
                    'currency' => 'brl',
                    'product_data' => ['name' => $item['name']],
                    'unit_amount' => (int) round($item['price'] * 100),
                ],
                'quantity' => $item['qty'],
            ];
        }

        $session = StripeSession::create([
            'mode' => 'payment',
            'payment_method_types' => ['card'],
            'line_items' => $lineItems,
            'success_url' => route('checkout.success') . '?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url' => route('cart.index'),
        ]);

        return redirect($session->url);
    }

    public function success(Request $request) {
        // Aqui você pode buscar a session no Stripe e gravar um "pedido"
        session()->forget('cart');
        return view('checkout.success');
    }
}
