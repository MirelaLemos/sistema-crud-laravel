@extends('layout')

@section('title', 'Produtos')

@section('content')
<div class="d-flex justify-content-between mb-3">
    <h1>Produtos</h1>
    <a href="{{ route('products.create') }}" class="btn btn-primary">Novo Produto</a>
</div>

<div class="row">
  @foreach($products as $product)
    <div class="col-md-4 mb-3">
      <div class="card h-100">
        @if($product->photo_path)
          <img src="{{ asset('storage/'.$product->photo_path) }}" class="card-img-top" alt="{{ $product->name }}">
        @endif
        <div class="card-body">
          <h5 class="card-title">{{ $product->name }}</h5>
          <p class="card-text">R$ {{ number_format($product->price, 2, ',', '.') }}</p>

          <form method="POST" action="{{ route('cart.add',$product) }}">
            @csrf
            <button class="btn btn-success btn-sm">Adicionar ao Carrinho</button>
          </form>
        </div>
        <div class="card-footer text-muted d-flex justify-content-between">
          <a href="{{ route('products.edit',$product) }}" class="btn btn-warning btn-sm">Editar</a>
          <form method="POST" action="{{ route('products.destroy',$product) }}">
            @csrf
            @method('DELETE')
            <button class="btn btn-danger btn-sm" onclick="return confirm('Remover?')">Excluir</button>
          </form>
        </div>
      </div>
    </div>
  @endforeach
</div>

{{ $products->links() }}
@endsection
