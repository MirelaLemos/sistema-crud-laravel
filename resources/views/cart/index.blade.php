@extends('layout')

@section('title','Carrinho')

@section('content')
<h1 class="fw-bold mb-3">Seu Carrinho</h1>

@if(empty($cart))
  <div class="alert alert-info"><i class="bi bi-info-circle"></i> Carrinho vazio.</div>
@else
  <div class="row g-3">
    <div class="col-lg-8">
      <div class="card shadow-sm">
        <div class="card-body p-0">
          <table class="table align-middle mb-0">
            <thead class="table-light">
              <tr>
                <th>Produto</th>
                <th class="text-center">Preço</th>
                <th class="text-center">Qtd</th>
                <th class="text-end">Total</th>
                <th class="text-center">Ações</th>
              </tr>
            </thead>
            <tbody>
              @foreach($cart as $item)
              <tr data-product-id="{{ $item['id'] }}" data-url="{{ route('cart.qty', $item['id']) }}">
                <td class="fw-medium">{{ $item['name'] }}</td>
                <td class="text-center">R$ {{ number_format($item['price'],2,',','.') }}</td>
                <td class="text-center">
                  <div class="btn-group" role="group" aria-label="Qtd">
                    <button type="button" class="btn btn-sm btn-outline-secondary qty-btn" data-delta="-1">
                      <i class="bi bi-dash"></i>
                    </button>
                    <input type="number" class="form-control form-control-sm qty-input" min="1"
                           value="{{ $item['qty'] }}" style="width:70px">
                    <button type="button" class="btn btn-sm btn-outline-secondary qty-btn" data-delta="1">
                      <i class="bi bi-plus"></i>
                    </button>
                  </div>
                </td>
                <td class="text-end item-total">
                  R$ {{ number_format($item['price'] * $item['qty'],2,',','.') }}
                </td>
                <td class="text-center">
                  <form action="{{ route('cart.remove',$item['id']) }}" method="POST" class="d-inline">
                    @csrf @method('DELETE')
                    <button class="btn btn-sm btn-outline-danger"><i class="bi bi-x-lg"></i> Remover</button>
                  </form>
                </td>
              </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <div class="col-lg-4">
      <div class="card shadow-sm sticky-summary">
        <div class="card-body">
          <h5 class="card-title mb-3">Resumo</h5>
          <div id="cart-total" class="fs-5 fw-semibold mb-3">
            Total: R$ {{ number_format($total,2,',','.') }}
          </div>
          <form method="POST" action="{{ route('checkout') }}" class="d-grid">
            @csrf
            <button class="btn btn-success btn-lg">
              <i class="bi bi-credit-card"></i> Finalizar Compra
            </button>
          </form>
        </div>
      </div>
    </div>
  </div>
@endif
@endsection

@section('scripts')
<script>
const csrf = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
const moneyBR = v => new Intl.NumberFormat('pt-BR',{style:'currency',currency:'BRL'}).format(v);

function updateQty(row, qty){
  const url = row.dataset.url;
  fetch(url, {
    method: 'POST',
    credentials: 'same-origin',
    headers: {
      'X-CSRF-TOKEN': csrf,
      'X-Requested-With': 'XMLHttpRequest',
      'Accept': 'application/json',
      'Content-Type': 'application/json'
    },
    body: JSON.stringify({ qty })
  })
  .then(r => r.json())
  .then(d => {
    row.querySelector('.qty-input').value = d.qty;
    row.querySelector('.item-total').textContent = moneyBR(d.item_total);
    document.getElementById('cart-total').textContent = `Total: ${moneyBR(d.cart_total)}`;
  })
  .catch(err => alert('Erro ao atualizar: ' + err.message));
}

document.addEventListener('click', e => {
  const btn = e.target.closest('.qty-btn');
  if (!btn) return;
  const row = btn.closest('tr');
  const input = row.querySelector('.qty-input');
  const delta = parseInt(btn.dataset.delta || '0', 10);
  const newQty = Math.max(1, (parseInt(input.value || '1', 10) + delta));
  updateQty(row, newQty);
});

document.addEventListener('change', e => {
  if (!e.target.classList.contains('qty-input')) return;
  const row = e.target.closest('tr');
  const newQty = Math.max(1, parseInt(e.target.value || '1', 10));
  updateQty(row, newQty);
});
</script>
@endsection
