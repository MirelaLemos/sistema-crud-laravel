@extends('layout')

@section('title','Sucesso!')

@section('content')
  <h1>Compra finalizada 🎉</h1>
  <p>Obrigado pela sua compra. Seu pedido foi processado com sucesso.</p>
  <a href="{{ route('products.index') }}" class="btn btn-primary">Voltar aos produtos</a>
@endsection
