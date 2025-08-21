@extends('layout')

@section('title','Novo Produto')

@section('content')
<h1>Novo Produto</h1>

{{-- Exibe mensagens de erro/sucesso --}}
@if ($errors->any())
  <div class="alert alert-danger">
    <ul class="mb-0">
      @foreach ($errors->all() as $e)
        <li>{{ $e }}</li>
      @endforeach
    </ul>
  </div>
@endif
@if (session('ok'))
  <div class="alert alert-success">{{ session('ok') }}</div>
@endif


<form method="POST" action="{{ route('products.store') }}" enctype="multipart/form-data">
  @csrf

  <div class="mb-3">
    <label class="form-label">Nome</label>
    <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
  </div>

  <div class="mb-3">
    <label class="form-label">Descrição</label>
    <textarea name="description" class="form-control">{{ old('description') }}</textarea>
  </div>

  <div class="mb-3">
    <label class="form-label">Preço</label>
    <input type="number" step="0.01" name="price" class="form-control" value="{{ old('price') }}" required>
  </div>

  <div class="mb-3">
    <label class="form-label">Estoque</label>
    <input type="number" name="stock" class="form-control" value="{{ old('stock', 0) }}" min="0" required>
  </div>

  <div class="mb-3">
    <label class="form-label">Foto</label>
    <input type="file" name="photo" class="form-control" accept="image/*">
  </div>

  <button type="submit" class="btn btn-primary">Salvar</button>
</form>
@endsection

