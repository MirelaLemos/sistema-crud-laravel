@extends('layout')
@php
  use Illuminate\Support\Facades\Storage;
  use Illuminate\Support\Str;
@endphp

@section('title','Editar Produto')

@section('content')
<h1>Editar Produto</h1>

<form method="POST" action="{{ route('products.update',$product) }}" enctype="multipart/form-data">
  @csrf
  @method('PUT')

  <div class="mb-3">
    <label class="form-label">Nome</label>
    <input type="text" name="name" class="form-control" value="{{ old('name',$product->name) }}" required>
  </div>

  <div class="mb-3">
    <label class="form-label">Descrição</label>
    <textarea name="description" class="form-control">{{ old('description',$product->description) }}</textarea>
  </div>

  <div class="mb-3">
    <label class="form-label">Preço</label>
    <input type="number" step="0.01" name="price" class="form-control" value="{{ old('price',$product->price) }}" required>
  </div>

  <div class="mb-3">
    <label class="form-label">Foto</label>
    <input type="file" name="photo" class="form-control">

    @php
      $img = null;
      if (!empty($product->photo_url)) {
          $img = $product->photo_url;
      } elseif (!empty($product->photo_path)) {
          $img = Str::startsWith($product->photo_path, ['http://','https://'])
              ? $product->photo_path
              : Storage::url($product->photo_path);
      }
    @endphp

    @if($img)
      <p class="mt-2">
        <strong>Atual:</strong><br>
        <img src="{{ $img }}" alt="{{ $product->name }}" class="img-thumbnail" style="max-width: 200px">
      </p>
    @endif
  </div>

  <button type="submit" class="btn btn-primary">Atualizar</button>
  <a href="{{ route('products.index') }}" class="btn btn-secondary">Cancelar</a>
</form>
@endsection
