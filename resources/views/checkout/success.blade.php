<x-app-layout>
  <x-slot name="header">
    <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
      Compra finalizada 🎉
    </h2>
  </x-slot>

  @php
    $money = fn($cents) => 'R$ '.number_format($cents/100, 2, ',', '.');
  @endphp

  <div class="py-8">
    <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
      <div class="rounded-xl bg-white p-6 shadow-sm ring-1 ring-black/5 dark:bg-gray-800 dark:ring-white/10">
        <div class="mb-5">
          <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">
            Pedido #{{ str_pad($order->id, 6, '0', STR_PAD_LEFT) }}
          </h1>
          <p class="mt-1 text-gray-600 dark:text-gray-300">
            Realizado em {{ $order->created_at->format('d/m/Y H:i') }}
          </p>
        </div>

        <div class="grid gap-6 lg:grid-cols-2">
          {{-- Resumo --}}
          <div>
            <h3 class="mb-3 font-semibold text-gray-900 dark:text-gray-100">Resumo</h3>
            <dl class="space-y-2 text-sm">
              <div class="flex justify-between">
                <dt class="text-gray-600 dark:text-gray-300">Status</dt>
                <dd class="font-medium text-gray-900 dark:text-gray-100">{{ $order->status }}</dd>
              </div>
              <div class="flex justify-between">
                <dt class="text-gray-600 dark:text-gray-300">E-mail</dt>
                <dd class="font-medium text-gray-900 dark:text-gray-100">{{ $order->customer_email }}</dd>
              </div>
              <div class="flex justify-between border-t pt-2 border-gray-200 dark:border-gray-700 text-base">
                <dt class="font-semibold text-gray-900 dark:text-gray-100">Total</dt>
                <dd class="font-semibold text-gray-900 dark:text-gray-100">{{ $money($order->total_cents) }}</dd>
              </div>
            </dl>
          </div>

          {{-- Itens --}}
          <div>
            <h3 class="mb-3 font-semibold text-gray-900 dark:text-gray-100">Itens do pedido</h3>
            <div class="space-y-3">
              @forelse($order->items as $it)
                <div class="flex items-center justify-between">
                  <div class="min-w-0">
                    <div class="truncate text-sm font-medium text-gray-900 dark:text-gray-100">
                      {{ $it->product->name ?? ('Produto #'.$it->product_id) }}
                    </div>
                    <div class="text-xs text-gray-500 dark:text-gray-400">
                      Qtd: {{ $it->qty ?? $it->quantity }} • Unit: R$ {{ number_format(($it->unit_price ?? $it->price),2,',','.') }}
                    </div>
                  </div>
                  <div class="text-sm font-medium text-gray-900 dark:text-gray-100">
                    R$ {{ number_format($it->total,2,',','.') }}
                  </div>
                </div>
              @empty
                <div class="text-sm text-gray-500 dark:text-gray-400">Nenhum item encontrado.</div>
              @endforelse
            </div>
          </div>
        </div>

        <div class="mt-6">
          <a href="{{ route('products.index') }}"
             class="inline-flex items-center rounded-lg bg-indigo-600 px-4 py-3 text-white font-semibold hover:bg-indigo-700">
            Voltar aos produtos
          </a>
        </div>
      </div>
    </div>
  </div>
</x-app-layout>
