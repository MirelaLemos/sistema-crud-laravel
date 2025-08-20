<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>@yield('title','Loja')</title>

  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootswatch@5.3.3/dist/lux/bootstrap.min.css">
  {{-- Ícones --}}
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">

  <style>
    body { background: #f7f8fb; }
    .navbar { box-shadow: 0 4px 18px rgba(0,0,0,.06); }
    .card-hover{ transition:.2s all; }
    .card-hover:hover{ transform: translateY(-2px); box-shadow: 0 10px 24px rgba(0,0,0,.08); }
    .price-badge{ font-weight:600; }
    .thumb{
  width: 100%;
  height: 200px;        /* altura fixa p/ todas */
  object-fit: contain;  /* mantém proporção, sem cortar */
  background: #fff;     /* fundo branco para produtos com PNG */
  padding: 10px;        /* respiro nas bordas */
  border-top-left-radius: .5rem;
  border-top-right-radius: .5rem;
}
    .qty-input{ width:70px; text-align:center; }
    .sticky-summary{ position: sticky; bottom: 12px; z-index: 10; }
  </style>

  @yield('head')
</head>

<body>
  <nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
    @php
  $cart = session('cart', []);
  $cartTotal = collect($cart)->sum(fn($i) => $i['price'] * $i['qty']);
  $cartQty   = collect($cart)->sum(fn($i) => $i['qty']);
@endphp

<ul class="navbar-nav ms-auto align-items-center gap-3">
  <li class="nav-item">
    <a class="nav-link" href="{{ route('products.index') }}">
      <i class="bi bi-grid"></i> Produtos
    </a>
  </li>

  {{-- Pill do carrinho --}}
  <li class="nav-item">
    <a href="{{ route('cart.index') }}" class="nav-link p-0">
      <span class="badge bg-dark d-flex align-items-center gap-2 px-3 py-2">
        <i class="bi bi-cart3"></i>
        R$ {{ number_format($cartTotal, 2, ',', '.') }}
        @if($cartQty > 0)
          <span class="badge bg-success">{{ $cartQty }}</span>
        @endif
      </span>
    </a>
  </li>

  {{-- Links de auth --}}
  @guest
    <li class="nav-item"><a class="nav-link" href="{{ route('login') }}">Entrar</a></li>
    <li class="nav-item"><a class="btn btn-success btn-sm" href="{{ route('register') }}">Cadastrar</a></li>
  @endguest

  @auth
    <li class="nav-item dropdown">
      <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">
        <i class="bi bi-person-circle me-1"></i> {{ auth()->user()->name }}
      </a>
      <ul class="dropdown-menu dropdown-menu-end">
        {{-- Se você tiver a rota de perfil do Breeze, pode habilitar: --}}
        {{-- <li><a class="dropdown-item" href="{{ route('profile.edit') }}">Perfil</a></li>
        <li><hr class="dropdown-divider"></li> --}}
        <li class="px-3">
          <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="btn btn-link text-danger p-0">
              <i class="bi bi-box-arrow-right me-1"></i> Sair
            </button>
          </form>
        </li>
      </ul>
    </li>
  @endauth
</ul>
  </nav>

  <div class="container-lg">
    @yield('content')
  </div>

  {{-- Toast de feedback --}}
  <div class="position-fixed bottom-0 end-0 p-3" style="z-index: 1055">
    @if(session('ok'))
      <div class="toast align-items-center text-bg-success border-0 show">
        <div class="d-flex">
          <div class="toast-body"><i class="bi bi-check-circle me-2"></i>{{ session('ok') }}</div>
          <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
        </div>
      </div>
    @endif
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  @yield('scripts')
</body>
</html>
