<x-layouts.auth-bootstrap
  :title="'Entrar'"
  :subtitle="'Acesse sua conta para continuar.'"
>
  <form method="POST" action="{{ route('login') }}" novalidate>
    @csrf

    <div class="mb-3">
      <label for="email" class="form-label">E-mail</label>
      <input type="email" class="form-control" id="email" name="email"
             value="{{ old('email') }}" required autofocus autocomplete="email"
             placeholder="voce@email.com">
    </div>

    <div class="mb-2">
      <label for="password" class="form-label">Senha</label>
      <input type="password" class="form-control" id="password" name="password"
             required autocomplete="current-password" placeholder="••••••••">
    </div>

    <div class="d-flex justify-content-between align-items-center mb-4">
      <div class="form-check">
        <input class="form-check-input" type="checkbox" value="1" id="remember" name="remember">
        <label class="form-check-label" for="remember">Manter conectado</label>
      </div>

      @if (Route::has('password.request'))
        <a class="small text-decoration-none" href="{{ route('password.request') }}">
          Esqueci minha senha
        </a>
      @endif
    </div>

    <button type="submit" class="btn btn-primary w-100">
      <i class="bi bi-box-arrow-in-right me-1"></i> Entrar
    </button>

    @if (Route::has('register'))
      <p class="text-center mt-3 mb-0 small">
        Não tem conta?
        <a href="{{ route('register') }}" class="text-decoration-none">Cadastre-se</a>
      </p>
    @endif
  </form>
</x-layouts.auth-bootstrap>
