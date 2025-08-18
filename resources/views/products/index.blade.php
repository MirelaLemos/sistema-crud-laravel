@extends('layout')

@section('title','Produtos')

@section('content')


<div class="d-flex justify-content-between align-items-center mb-3">
    <h1>Produtos</h1>
    <a href="{{ route('products.create') }}" class="btn btn-dark">
        <i class="bi bi-plus-circle"></i> Novo Produto
    </a>
</div>


<div class="row g-3">
  @foreach($products as $product)
    <div class="col-12 col-sm-6 col-md-4 col-lg-3">
      <div class="card h-100 card-hover">
        @if($product->photo_path)
          <img src="{{ asset('storage/'.$product->photo_path) }}" class="thumb" alt="{{ $product->name }}">
        @else
          <img src="https://via.placeholder.com/600x400?text=Produto" class="thumb" alt="{{ $product->name }}">
        @endif
        <div class="card-body d-flex flex-column">
          <h6 class="text-muted mb-1">{{ Str::limit($product->name, 40) }}</h6>
          <div class="mb-3">
            <span class="badge bg-success-subtle text-success price-badge">
              R$ {{ number_format($product->price,2,',','.') }}
            </span>
          </div>
          <div class="mt-auto d-grid gap-2">
            <a href="{{ route('products.show',$product) }}" class="btn btn-outline-secondary btn-sm">
              <i class="bi bi-eye"></i> Detalhes
            </a>
            <form method="POST" action="{{ route('cart.add',$product) }}">
              @csrf
              <button class="btn btn-dark btn-sm"><i class="bi bi-cart-plus"></i> Adicionar</button>
            </form>
          </div>
        </div>
        <div class="card-footer bg-transparent d-flex gap-2">
          <a href="{{ route('products.edit',$product) }}" class="btn btn-warning btn-sm flex-fill">
            <i class="bi bi-pencil"></i> Editar
          </a>
          <form method="POST" action="{{ route('products.destroy',$product) }}" class="flex-fill">
            @csrf @method('DELETE')
            <button class="btn btn-danger btn-sm w-100" onclick="return confirm('Remover?')">
              <i class="bi bi-trash"></i> Excluir
            </button>
          </form>
        </div>
      </div>
    </div>
  @endforeach
</div>

<div class="mt-3">
  {{ $products->links() }}
</div>
@endsection
