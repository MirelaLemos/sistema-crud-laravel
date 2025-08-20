<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Stripe\StripeClient;

class CheckoutController extends Controller
{
    public function show(Request $req)
    {
        // Carrinho: [product_id => qty]
        $cart = $req->session()->get('cart', []);
        if (!$cart) {
            return view('checkout.empty');
        }

        // Monta itens e totais
        $products = Product::whereIn('id', array_keys($cart))->get();
        $items = [];
        $subtotal = 0;
        foreach ($products as $p) {
            $qty  = max(1, (int) ($cart[$p->id] ?? 1));
            $line = $p->price * $qty;
            $items[] = compact('p', 'qty', 'line');
            $subtotal += $line;
        }
        $shipping = 0;
        $total    = $subtotal + $shipping;
        $amount   = (int) round($total * 100); // centavos

        if ($amount <= 0) {
            return back()->withErrors('Carrinho vazio ou total inválido.');
        }

        // Stripe
        $stripe   = new StripeClient(config('services.stripe.secret'));
        $piId     = $req->session()->get('pi_id');
        $cartHash = md5(json_encode($cart));

        try {
            if ($piId) {
                $stripe->paymentIntents->update($piId, [
                    'amount'   => $amount,
                    'currency' => config('services.stripe.currency', 'brl'),
                    'metadata' => ['cart_hash' => $cartHash],
                ]);
            } else {
                $pi = $stripe->paymentIntents->create([
                    'amount'   => $amount,
                    'currency' => config('services.stripe.currency', 'brl'),
                    'payment_method_types' => ['card'],
                    'metadata' => ['cart_hash' => $cartHash],
                ]);
                $piId = $pi->id;
                $req->session()->put('pi_id', $piId);
            }

            $pi = $stripe->paymentIntents->retrieve($piId, []);
            $clientSecret = $pi->client_secret;
        } catch (\Throwable $e) {
            Log::error('checkout.show', ['error' => $e->getMessage()]);
            return back()->withErrors('Falha ao preparar pagamento: ' . $e->getMessage());
        }

        return view('checkout.show', compact('items', 'subtotal', 'shipping', 'total', 'clientSecret', 'pi'));
    }

    public function complete(Request $req)
    {
        $expectsJson = $req->expectsJson(); // true quando vier do fetch() com Accept: application/json

        $data = $req->validate([
            'payment_intent' => ['required', 'string'],
            'customer_name'  => ['required', 'string', 'max:255'], // usado só para billing_details
            'customer_email' => ['required', 'email'],
        ]);

        $cart = $req->session()->get('cart', []);
        if (!$cart) {
            if ($expectsJson) {
                return response()->json(['ok' => false, 'message' => 'Carrinho vazio.'], 422);
            }
            return redirect()->route('checkout.show')->withErrors('Carrinho vazio.');
        }

        try {
            $stripe = new StripeClient(config('services.stripe.secret'));
            $pi = $stripe->paymentIntents->retrieve($data['payment_intent']);

            if (($pi->status ?? null) !== 'succeeded') {
                $msg = 'Pagamento não aprovado. Status: ' . ($pi->status ?? 'desconhecido');
                if ($expectsJson) {
                    return response()->json(['ok' => false, 'message' => $msg], 422);
                }
                return redirect()->route('checkout.show')->withErrors($msg);
            }

            // Recalcula valores no servidor
            $products = Product::whereIn('id', array_keys($cart))->lockForUpdate()->get();
            $subtotal = 0; $itemsData = [];
            foreach ($products as $p) {
                $qty  = max(1, (int) ($cart[$p->id] ?? 1));
                $line = $p->price * $qty;
                $subtotal += $line;
                $itemsData[] = ['product' => $p, 'qty' => $qty, 'unit' => $p->price, 'total' => $line];
            }
            $shipping = 0;
            $total    = $subtotal + $shipping;
            $amount   = (int) round($total * 100); // centavos

            // Confere com Stripe
            if ((int)($pi->amount_received ?? 0) !== $amount) {
                $msg = 'Valor divergente. Compra não finalizada.';
                if ($expectsJson) {
                    return response()->json(['ok' => false, 'message' => $msg], 422);
                }
                return redirect()->route('checkout.show')->withErrors($msg);
            }

            // Persiste pedido + itens
            $order = DB::transaction(function () use ($data, $itemsData, $amount, $pi) {
                $order = Order::create([
                    'status'            => 'paid',
                    'total_cents'       => $amount,            // em centavos
                    'stripe_session_id' => $pi->id ?? null,    // guardando o id do PaymentIntent aqui
                    'customer_email'    => $data['customer_email'],
                    'customer_name'     => $data['customer_name'],
                ]);

                foreach ($itemsData as $it) {
                    OrderItem::create([
                        'order_id'   => $order->id,
                        'product_id' => $it['product']->id,

                        // tabela tem as duas variações; preenche ambas
                        'qty'        => $it['qty'],
                        'quantity'   => $it['qty'],

                        'unit_price' => $it['unit'],
                        'price'      => $it['unit'],

                        'total'      => $it['total'],
                    ]);
                }

                return $order; // <<< IMPORTANTÍSSIMO
            }); // <<< fecha a transaction aqui

            // Limpa sessão
            $req->session()->forget(['cart', 'pi_id']);

            // → JSON (AJAX) com redirect para rota com {order}
            if ($expectsJson) {
                return response()->json([
                    'ok'       => true,
                    'order_id' => $order->id,
                    'redirect' => route('checkout.success', $order),
                ]);
            }

            // Fallback (submit tradicional)
            return redirect()
                ->route('checkout.success', $order)
                ->with('ok', 'Pagamento aprovado! Pedido #' . $order->id);

        } catch (\Throwable $e) {
            Log::error('checkout.complete', ['error' => $e->getMessage()]);
            if ($expectsJson) {
                return response()->json(['ok' => false, 'message' => 'Falha ao finalizar: ' . $e->getMessage()], 500);
            }
            return redirect()->route('checkout.show')->withErrors('Falha ao finalizar: ' . $e->getMessage());
        }
    }

    // recebe o Order via route model binding: /checkout/success/{order}
    public function success(Order $order)
    {
        $order->load(['items.product']); // carregue 'payment' só se tiver a relação/model
        return view('checkout.success', compact('order'));
    }
}
