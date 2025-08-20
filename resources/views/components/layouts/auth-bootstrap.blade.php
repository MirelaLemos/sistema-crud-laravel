<!doctype html>
<html lang="pt-br" data-bs-theme="dark">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>{{ $title ?? 'Entrar' }} · {{ config('app.name') }}</title>

  {{-- Bootstrap 5 via CDN --}}
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  {{-- Opcional: ícones Bootstrap --}}
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

  {{-- Dica: NÃO incluir @vite('resources/css/app.css') aqui para evitar conflito com Tailwind --}}
</head>
<body class="bg-body-tertiary">

  <main class="d-flex align-items-center min-vh-100 py-5">
    <div class="container">
      <div class="row justify-content-center">
        <div class="col-12 col-sm-10 col-md-8 col-lg-6 col-xl-5">
          <div class="card shadow-lg border-0">
            <div class="card-body p-4 p-sm-5">

              {{-- Branding --}}
              <div class="d-flex align-items-center gap-2 mb-3">
                <i class="bi bi-bag-check-fill fs-3 text-primary"></i>
                <h1 class="h4 mb-0">{{ config('app.name', 'Loja') }}</h1>
              </div>

              @isset($title)
                <h2 class="h4 fw-bold mb-3">{{ $title }}</h2>
              @endisset
              @isset($subtitle)
                <p class="text-secondary mb-4">{{ $subtitle }}</p>
              @endisset

              {{-- Alerts padrão Laravel --}}
              @if (session('status'))
                <div class="alert alert-success">{{ session('status') }}</div>
              @endif

              @if ($errors->any())
                <div class="alert alert-danger">
                  <div class="fw-semibold mb-1">Ops! Verifique os campos:</div>
                  <ul class="mb-0 ps-3">
                    @foreach ($errors->all() as $e)
                      <li>{{ $e }}</li>
                    @endforeach
                  </ul>
                </div>
              @endif

              {{-- Conteúdo da página (form) --}}
              {{ $slot }}

            </div>
          </div>

          {{-- Footer curtinho --}}
          <p class="text-center text-secondary mt-3 mb-0 small">
            &copy; {{ date('Y') }} {{ config('app.name') }}
          </p>
        </div>
      </div>
    </div>
  </main>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
