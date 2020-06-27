@extends('layout')
@section('titulo', 'Carrinho de compras - Produtos adicionar')

@section('conteudo')
	<div class="container">
		<div class="row">
			<h3>Adicionar produto</h3>
			<form method="POST" action="{{ route('admin.produtos.salvar') }}">
				{{ csrf_field() }}
				@include('admin.produtos._form')

				<button type="submit" class="btn blue">Salvar</button>
			</form>
		</div>
	</div>
	@include('admin.produtos._lib')
@endsection