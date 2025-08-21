@extends('layout')

@section('title', 'Produtos')

@section('content')
  @php($produtos = $produtos ?? collect())
  @php
    use Illuminate\Support\Str;
    use Illuminate\Support\Facades\Storage;
  @endphp

  <div class="d-flex justify-content-between align-items-center mb-3">
    <h1>Produtos</h1>

    <div class="d-flex gap-2">
      <a href="{{ route('cart.index') }}" class="btn btn-outline-primary">
        <i class="bi bi-bag"></i> Ver carrinho
      </a>
      @role('admin')
        <a href="{{ route('products.create') }}" class="btn btn-dark">
          <i class="bi bi-plus-circle"></i> Novo Produto
        </a>
      @endrole
    </div>
  </div>

  <div class="row g-3">
    @forelse($produtos as $product)
      <div class="col-12 col-sm-6 col-md-4 col-lg-3">
        <div class="card h-100">
          @php
            $img = null;
            if (!empty($product->photo_url)) {
                // se já salva a URL completa no banco
                $img = $product->photo_url;
            } elseif (!empty($product->photo_path)) {
                // se salva só o caminho (ex: "products/abc.png")
                $img = Str::startsWith($product->photo_path, ['http://','https://'])
                    ? $product->photo_path
                    : Storage::url($product->photo_path);
            }
          @endphp

          <img
            src="{{ $img ?: 'https://via.placeholder.com/600x400?text=Produto' }}"
            class="thumb"
            alt="{{ $product->name }}"
            loading="lazy">

          <div class="card-body d-flex flex-column">
            <h6 class="text-muted mb-1">{{ Str::limit($product->name, 40) }}</h6>

            <div class="mb-3">
              <span class="badge bg-success-subtle text-success">
                R$ {{ number_format($product->price,2,',','.') }}
              </span>
            </div>

            <div class="mt-auto d-grid gap-2">
              {{-- Detalhes (GET) --}}
              <a href="{{ route('products.show',$product) }}" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-eye"></i> Detalhes
              </a>

              {{-- Adicionar no carrinho (POST) --}}
              @if (Route::has('cart.add'))
                <form method="POST" action="{{ route('cart.add',$product) }}" class="d-inline">
                  @csrf
                  <button type="submit" class="btn btn-dark btn-sm">
                    <i class="bi bi-cart-plus"></i> Adicionar
                  </button>
                </form>
              @endif
            </div>
          </div>

          @role('admin')
            <div class="card-footer bg-transparent d-flex gap-2">
              <a href="{{ route('products.edit',$product) }}" class="btn btn-warning btn-sm flex-fill">
                <i class="bi bi-pencil"></i> Editar
              </a>
              <form method="POST" action="{{ route('products.destroy',$product) }}" class="flex-fill">
                @csrf @method('DELETE')
                <button type="submit" class="btn btn-danger btn-sm w-100" onclick="return confirm('Remover?')">
                  <i class="bi bi-trash"></i> Excluir
                </button>
              </form>
            </div>
          @endrole
        </div>
      </div>
    @empty
      <div class="col-12"><p>Nenhum produto encontrado.</p></div>
    @endforelse
  </div>

  <div class="mt-3">
    {{ $produtos->links() }}
  </div>
@endsection