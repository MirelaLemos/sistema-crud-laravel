<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Checkout
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            {{-- Alerts --}}
            @if ($errors->any())
                <div class="mb-4 rounded-lg border border-red-300/40 bg-red-50 p-3 text-sm text-red-700 dark:border-red-600/40 dark:bg-red-900/20 dark:text-red-200">
                    @foreach ($errors->all() as $e) <div>{{ $e }}</div> @endforeach
                </div>
            @endif
            @if (session('ok'))
                <div class="mb-4 rounded-lg border border-emerald-300/40 bg-emerald-50 p-3 text-sm text-emerald-700 dark:border-emerald-600/40 dark:bg-emerald-900/20 dark:text-emerald-200">
                    {{ session('ok') }}
                </div>
            @endif

            <div class="grid grid-cols-1 gap-6 lg:grid-cols-5">
                {{-- COL LEFT: Form --}}
                <div class="lg:col-span-3">
                    <div class="rounded-xl bg-white p-6 shadow-sm ring-1 ring-black/5 dark:bg-gray-800 dark:ring-white/10">
                        <form id="pay-form" class="space-y-5" novalidate>
                            @csrf

                            <div class="grid gap-4 sm:grid-cols-2">
                                <div>
                                    <label for="customer_name" class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">
                                        Nome
                                    </label>
                                    <input id="customer_name" autocomplete="cc-name" required
                                           class="w-full rounded-lg border-gray-300 text-gray-900 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100"/>
                                </div>
                                <div>
                                    <label for="customer_email" class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">
                                        E-mail
                                    </label>
                                    <input id="customer_email" type="email" autocomplete="email" required
                                           class="w-full rounded-lg border-gray-300 text-gray-900 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100"/>
                                </div>
                            </div>

                            {{-- Stripe Elements (separados) --}}
                            <div class="pt-2">
                                <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Cartão</label>

                                <div class="grid grid-cols-12 gap-3">
                                    <div class="col-span-12 md:col-span-7">
                                        <div class="rounded-lg border border-gray-300 bg-white px-3 py-2 min-h-[44px] dark:border-gray-700 dark:bg-gray-900">
                                            <div id="card-number"></div>
                                        </div>
                                    </div>
                                    <div class="col-span-6 md:col-span-3">
                                        <div class="rounded-lg border border-gray-300 bg-white px-3 py-2 min-h-[44px] dark:border-gray-700 dark:bg-gray-900">
                                            <div id="card-expiry"></div>
                                        </div>
                                    </div>
                                    <div class="col-span-6 md:col-span-2">
                                        <div class="rounded-lg border border-gray-300 bg-white px-3 py-2 min-h-[44px] dark:border-gray-700 dark:bg-gray-900">
                                            <div id="card-cvc"></div>
                                        </div>
                                    </div>
                                </div>

                                <p id="card-errors" class="mt-2 text-sm text-red-600 dark:text-red-400"></p>
                            </div>

                            <button id="pay-btn" type="submit"
                                    class="inline-flex w-full items-center justify-center rounded-lg bg-indigo-600 px-4 py-3 text-base font-semibold text-white shadow-sm transition hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-70">
                                <svg id="spinner" class="mr-2 hidden h-5 w-5 animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                                </svg>
                                Pagar agora
                            </button>
                        </form>
                    </div>
                </div>

                {{-- COL RIGHT: Summary --}}
                <div class="lg:col-span-2">
                    <div class="rounded-xl bg-white p-6 shadow-sm ring-1 ring-black/5 dark:bg-gray-800 dark:ring-white/10">
                        <h3 class="mb-4 text-lg font-semibold text-gray-900 dark:text-gray-100">Resumo</h3>

                        <div class="space-y-4">
                            @foreach($items as $it)
                                <div class="flex items-center gap-3">
                                    <div class="min-w-0 flex-1">
                                        <div class="truncate text-sm font-medium text-gray-900 dark:text-gray-100">{{ $it['p']->name }}</div>
                                        <div class="text-xs text-gray-500 dark:text-gray-400">Qtd: {{ $it['qty'] }}</div>
                                    </div>
                                    <div class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                        R$ {{ number_format($it['line'], 2, ',', '.') }}
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="my-5 border-t border-gray-200 dark:border-gray-700"></div>

                        <dl class="space-y-2 text-sm">
                            <div class="flex justify-between">
                                <dt class="text-gray-600 dark:text-gray-300">Subtotal</dt>
                                <dd class="font-medium text-gray-900 dark:text-gray-100">R$ {{ number_format($subtotal,2,',','.') }}</dd>
                            </div>
                            <div class="flex justify-between">
                                <dt class="text-gray-600 dark:text-gray-300">Frete</dt>
                                <dd class="font-medium text-gray-900 dark:text-gray-100">R$ {{ number_format($shipping,2,',','.') }}</dd>
                            </div>
                            <div class="flex justify-between border-t pt-2 border-gray-200 dark:border-gray-700 text-base">
                                <dt class="font-semibold text-gray-900 dark:text-gray-100">Total</dt>
                                <dd class="font-semibold text-gray-900 dark:text-gray-100">R$ {{ number_format($total,2,',','.') }}</dd>
                            </div>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Stripe --}}
    <script src="https://js.stripe.com/v3"></script>
    <script>
      const isDark = document.documentElement.classList.contains('dark');
      const stripe = Stripe(@json(config('services.stripe.key')));

      const elements = stripe.elements({ appearance: { theme: isDark ? 'night' : 'stripe' } });
      const style = { base: { fontSize: '16px' } };

      const cardNumber = elements.create('cardNumber', { style });
      const cardExpiry = elements.create('cardExpiry', { style, placeholder: 'MM/AA' });
      const cardCvc    = elements.create('cardCvc',    { style, placeholder: 'CVC' });

      cardNumber.mount('#card-number');
      cardExpiry.mount('#card-expiry');
      cardCvc.mount('#card-cvc');

      [cardNumber, cardExpiry, cardCvc].forEach(el => {
        el.on('change', ({ error }) => {
          document.getElementById('card-errors').textContent = error ? error.message : '';
        });
      });

      const form    = document.getElementById('pay-form');
      const payBtn  = document.getElementById('pay-btn');
      const spinner = document.getElementById('spinner');
      const errEl   = document.getElementById('card-errors');

      const clientSecret = @json($clientSecret);
      const completeUrl  = @json(route('checkout.complete'));
      const csrf         = document.querySelector('meta[name="csrf-token"]')?.content;

      function setBusy(b){ payBtn.disabled = b; spinner.classList.toggle('hidden', !b); }

      console.log('Stripe init', { hasStripe: !!window.Stripe, clientSecret });

      if (!clientSecret) errEl.textContent = 'Erro: clientSecret não definido. Recarregue a página.';
      form.setAttribute('novalidate','novalidate');

      window.addEventListener('unhandledrejection', (e) => {
        console.error('Unhandled promise', e.reason);
        errEl.textContent = e.reason?.message ?? 'Erro inesperado.';
        setBusy(false);
      });

      form.addEventListener('submit', async (e) => {
        e.preventDefault();
        errEl.textContent = '';
        setBusy(true);

        try {
          const name  = document.getElementById('customer_name').value.trim();
          const email = document.getElementById('customer_email').value.trim();

          const result = await stripe.confirmCardPayment(clientSecret, {
            payment_method: {
              card: cardNumber,
              billing_details: { name, email }
            }
          });
          console.log('confirm result', result);

          if (result.error) {
            errEl.textContent = result.error.message || 'Falha no pagamento.';
            setBusy(false);
            return;
          }

          const { paymentIntent } = result;
          if (paymentIntent && paymentIntent.status === 'succeeded') {
            // FINALIZA NO BACKEND VIA AJAX (sem redirect do servidor)
            const resp = await fetch(completeUrl, {
              method: 'POST',
              credentials: 'same-origin',
              headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrf
              },
              body: JSON.stringify({
                payment_intent: paymentIntent.id,
                customer_name: name,
                customer_email: email
              })
            });

            const data = await resp.json().catch(() => ({}));
            if (!resp.ok || data.ok === false) {
              throw new Error(data.message || 'Falha ao finalizar no servidor.');
            }

            // redireciona usando a URL completa enviada pelo backend (já com {order})
            if (data.redirect) {
              window.location.href = data.redirect;
            } else if (data.order_id) {
              // fallback só por garantia
              window.location.href = `${window.location.origin}/checkout/success/${encodeURIComponent(data.order_id)}`;
            } else {
              throw new Error('Resposta sem redirect/order_id.');
            }
            return;
          }

          errEl.textContent = 'Pagamento não aprovado. Status: ' + (paymentIntent?.status ?? 'desconhecido');
          setBusy(false);
        } catch (err) {
          console.error(err);
          errEl.textContent = err.message ?? String(err);
          setBusy(false);
        }
      });
    </script>

    <style>
      #card-number [data-testid="LinkSwitch"],
      #card-expiry  [data-testid="LinkSwitch"],
      #card-cvc     [data-testid="LinkSwitch"] { display:none !important; }
    </style>
</x-app-layout>
