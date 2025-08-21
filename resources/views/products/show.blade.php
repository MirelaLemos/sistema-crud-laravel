{{-- resources/views/products/show.blade.php --}}

@extends('layout')

@section('title', $product->name)



@section('content')
@role('admin')
  <a href="{{ route('products.edit',$product) }}" class="btn btn-warning">Editar</a>
  <form action="{{ route('products.destroy',$product) }}" method="POST" style="display:inline">
    @csrf @method('DELETE')
    <button class="btn btn-danger">Excluir</button>
  </form>
@endrole
<div class="row">
  <div class="col-md-6">
    @if(!empty($product->photo_path))
      <img
        src="{{ Storage::url($product->photo_path) }}"
        alt="{{ $product->name }}"
        style="max-width:100%; height:auto"
        loading="lazy">
    @else
      <img
        src="https://via.placeholder.com/800x600?text=Produto"
        alt="{{ $product->name }}"
        style="max-width:100%; height:auto"
        loading="lazy">
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
