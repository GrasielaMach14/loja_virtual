@extends('layout')
@section('titulo', 'HOME')

@section('conteudo')

<div class="container">
	<div class="row">
	@foreach($registros as $registro)
		<div class="col s10 m4 l2">
			<div class="card small">
				<div class="card-image">
					<img src="{{ $registro->imagem }}">
				</div>
				<div class="card-content">
					<h6>
						<span class="grey-text" title="{{ $registro->nome }}">{{ $registro->nome }}</span>						
					</h6>
					<h6 class="card">R$ {{ number_format($registro->valor, 2, ',', '.') }}</h6>
				</div>
				<div class="card-action">
					<a class="blue-text" href="{{ route('produto', $registro->id) }}">Veja mais informações</a>
				</div>
			</div>
		</div>
	@endforeach
	</div>
</div>

@endsection