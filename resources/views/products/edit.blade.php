@extends('layout')

@section('title','Editar Produto')

@section('content')
<h1>Editar Produto</h1>

<form method="POST" action="{{ route('products.update',$product) }}" enctype="multipart/form-data">
  @csrf
  @method('PUT')

  <div class="mb-3">
    <label class="form-label">Nome</label>
    <input type="text" name="name" class="form-control" value="{{ $product->name }}" required>
  </div>

  <div class="mb-3">
    <label class="form-label">Descrição</label>
    <textarea name="description" class="form-control">{{ $product->description }}</textarea>
  </div>

  <div class="mb-3">
    <label class="form-label">Preço</label>
    <input type="number" step="0.01" name="price" class="form-control" value="{{ $product->price }}" required>
  </div>

  <div class="mb-3">
    <label class="form-label">Foto</label>
    <input type="file" name="photo" class="form-control">
    @if($product->photo_path)
      <p class="mt-2">Atual: <img src="{{ asset('storage/'.$product->photo_path) }}" width="100"></p>
    @endif
  </div>

  <button type="submit" class="btn btn-primary">Atualizar</button>
</form>
@endsection
