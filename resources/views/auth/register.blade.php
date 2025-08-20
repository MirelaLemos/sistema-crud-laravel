<x-layouts.auth-bootstrap
  :title="'Criar conta'"
  :subtitle="'Preencha seus dados para começar.'"
>
  <form method="POST" action="{{ route('register') }}" novalidate>
    @csrf

    <div class="mb-3">
      <label for="name" class="form-label">Nome</label>
      <input type="text" class="form-control" id="name" name="name"
             value="{{ old('name') }}" required autocomplete="name" placeholder="Seu nome">
    </div>

    <div class="mb-3">
      <label for="email" class="form-label">E-mail</label>
      <input type="email" class="form-control" id="email" name="email"
             value="{{ old('email') }}" required autocomplete="email" placeholder="voce@email.com">
    </div>

    <div class="mb-3">
      <label for="password" class="form-label">Senha</label>
      <input type="password" class="form-control" id="password" name="password"
             required autocomplete="new-password" placeholder="Mínimo 8 caracteres">
    </div>

    <div class="mb-4">
      <label for="password_confirmation" class="form-label">Confirmar senha</label>
      <input type="password" class="form-control" id="password_confirmation" name="password_confirmation"
             required autocomplete="new-password" placeholder="Repita a senha">
    </div>

    <button type="submit" class="btn btn-primary w-100">
      <i class="bi bi-person-plus me-1"></i> Criar conta
    </button>

    @if (Route::has('login'))
      <p class="text-center mt-3 mb-0 small">
        Já tem conta?
        <a href="{{ route('login') }}" class="text-decoration-none">Entrar</a>
      </p>
    @endif
  </form>
</x-layouts.auth-bootstrap>
