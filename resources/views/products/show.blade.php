@extends('layout')

@section('title', $product->name)

@role('admin')
  <a href="{{ route('products.edit',$product) }}" class="btn btn-warning">Editar</a>
  <form action="{{ route('products.destroy',$product) }}" method="POST" style="display:inline">
    @csrf @method('DELETE')
    <button class="btn btn-danger">Excluir</button>
  </form>
@endrole

@section('content')
<div class="row">
  <div class="col-md-6">
    @if($product->photo_path)
      <img src="{{ asset('storage/'.$product->photo_path) }}" class="img-fluid mb-3" alt="{{ $product->name }}">
    @else
      <img src="https://via.placeholder.com/400x300?text=Sem+Foto" class="img-fluid mb-3" alt="{{ $product->name }}">
    @endif
  </div>
  <div class="col-md-6">
    <h1>{{ $product->name }}</h1>
    <p class="text-muted">R$ {{ number_format($product->price, 2, ',', '.') }}</p>
    <p>{{ $product->description }}</p>

    <form method="POST" action="{{ route('cart.add',$product) }}">
      @csrf
      <button class="btn btn-success">Adicionar ao Carrinho</button>
    </form>

    <hr>
    <a href="{{ route('products.index') }}" class="btn btn-secondary mt-2">Voltar</a>
  </div>
</div>
@endsection
