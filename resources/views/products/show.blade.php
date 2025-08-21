{{-- resources/views/products/show.blade.php --}}

@extends('layout')

@section('title', $product->name)

@section('content')
<div class="mb-3">
  @role('admin')
    <a href="{{ route('products.edit',$product) }}" class="btn btn-warning">Editar</a>
    <form action="{{ route('products.destroy',$product) }}" method="POST" style="display:inline">
      @csrf @method('DELETE')
      <button class="btn btn-danger">Excluir</button>
    </form>
  @endrole
</div>

<div class="row">
  <div class="col-md-6">
    @php
      use Illuminate\Support\Str;
      use Illuminate\Support\Facades\Storage;

      $img = null;
      if (!empty($product->photo_url)) {
          // caso já tenha URL completa salva
          $img = $product->photo_url;
      } elseif (!empty($product->photo_path)) {
          // se tem só o path salvo
          $img = Str::startsWith($product->photo_path, ['http://','https://'])
              ? $product->photo_path
              : Storage::disk('s3')->url($product->photo_path);
      }
    @endphp

    <img
      src="{{ $img ?: 'https://via.placeholder.com/800x600?text=Produto' }}"
      alt="{{ $product->name }}"
      class="img-fluid rounded shadow-sm"
      loading="lazy">
  </div>

  <div class="col-md-6">
    <h1>{{ $product->name }}</h1>
    <p class="text-muted fs-4">R$ {{ number_format($product->price, 2, ',', '.') }}</p>
    <p>{{ $product->description }}</p>

    @if (Route::has('cart.add'))
      <form method="POST" action="{{ route('cart.add',$product) }}">
        @csrf
        <button class="btn btn-success btn-lg">
          <i class="bi bi-cart-plus"></i> Adicionar ao Carrinho
        </button>
      </form>
    @endif

    <hr>
    <a href="{{ route('products.index') }}" class="btn btn-secondary mt-2">
      <i class="bi bi-arrow-left"></i> Voltar
    </a>
  </div>
</div>
@endsection
