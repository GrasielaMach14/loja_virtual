@extends('layout')
@section('titulo', 'Carrinho de compras - Produtos editar')

@section('conteudo')
	<div class="container">
		<div class="row">
			<h3>Editar produto "{{ $registro->nome }}"</h3>
			<form method="POST" action="{{ route('admin.produtos.atualizar', $registro->id) }}">
				{{ csrf_field() }}
				{{ method_field('PUT') }}

				@include('admin.produtos._form')

				<button type="submit" class="btn blue">Atualizar</button>
			</form>
		</div>
	</div>
	@include('admin.produtos._lib')
@endsection