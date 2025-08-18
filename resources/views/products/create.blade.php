@extends('layout')

@section('title','Novo Produto')

@section('content')
<h1>Novo Produto</h1>

<form method="POST" action="{{ route('products.store') }}" enctype="multipart/form-data">
  @csrf
  <div class="mb-3">
    <label class="form-label">Nome</label>
    <input type="text" name="name" class="form-control" required>
  </div>

  <div class="mb-3">
    <label class="form-label">Descrição</label>
    <textarea name="description" class="form-control"></textarea>
  </div>

  <div class="mb-3">
    <label class="form-label">Preço</label>
    <input type="number" step="0.01" name="price" class="form-control" required>
  </div>

  <div class="mb-3">
    <label class="form-label">Foto</label>
    <input type="file" name="photo" class="form-control">
  </div>

  <button type="submit" class="btn btn-primary">Salvar</button>
</form>
@endsection
