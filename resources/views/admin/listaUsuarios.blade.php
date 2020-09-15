@extends('layouts.app')
@section('styles')
<link rel="stylesheet" href="{{ asset('libs/datatables/datatables.bootstrap4.min.css') }}">
@endsection
@section('content')

<div class="container">
	<div class="panel panel-default">
		<div class="panel-body">
			<a href="{{route('user.novo')}}" class="btn btn-success btn-sm" style="color: #FFF; margin-bottom: 10px;"><i class="fa fa-plus" style="color: #FFF; "></i> Novo</a>
			<BR>
			<table class="table table-bordered" id="table" width="100%">
				<thead>
					<th>Nome</th>
					<th>Email</th>
					<th>Cidade</th>
					<th>Estado</th>
					<th>Instituição</th>
					<th>Telefone</th>
					<th>Tipo</th>
					<th>Data Cadastro</th>
					<th>Ação</th>
				</tr>
			</thead>
		</table>

	</div>
</div>
</div>
@endsection
@section('scripts')
<script src="{{ asset('libs/datatables/datatables.min.js') }}"></script>
<script>
	$(function() {
		$('#table').DataTable({
			"language": {
				"url": "{{ asset('plugins/datatables.net/js/Portuguese-Brasil.json')}}"
			},
			processing: true,
			serverSide: true,
			responsive: true,
			ajax: {
				type: 'POST',
				dataType: "json",
				url: '{!! route('getUsuarios') !!}',
				headers: {
					'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
				},
			},
			columns: [
			{ data: 'nome'},
			{ data: 'email'},
			{ data: 'cidade.municipio', name: 'cidade.municipio'},
			{ data: 'cidade.uf', name: 'cidade.uf'},
			{ data: 'instituicao'},
			{ data: 'telefone'},
			{ data: 'tipo', render: function ( data, type, row, meta ){
				if(data == 1){
					return 'Administrador';
				}else if(data == 2){
					return 'Administrador Estado';
				}else if(data == 3){
					return 'Administrador Municipio';
				}else{
					return 'Usuário';
				}				
			}},
			{ data: 'created_at'},
			{ data: 'action'}
			]
		});
	});
</script>
@endsection