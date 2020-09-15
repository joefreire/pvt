@extends('layouts.app')
@section('styles')
<link rel="stylesheet" href="{{ asset('libs/datatables/datatables.bootstrap4.min.css') }}">
@endsection
@section('content')

<div class="container">
	<div class="panel panel-default">
		<div class="panel-heading clearfix"> 
			<span id="titulo-painel" class="h3-semlinha"> Logs </span>
		</div>
		<div class="panel-body">
			<div class="row table-responsive">  
				<div class="col-md-12"> 
					<table class="table table-bordered" id="table" width="100%">
						<thead>
							<tr>
								<th></th>
								<th>Model</th>
								<th>Tipo</th>
								<th>User</th>
								<th>id_Editado</th>
								<th>Ip</th>
								<th>Data</th>
							</tr>
						</thead>
					</table>
				</div>
			</div>
		</div>
	</div>
</div>
@endsection
@section('scripts')
<script src="{{ asset('libs/datatables/datatables.min.js') }}"></script>
<script>
	$(function() {
		function format ( d ) {
			return '<table cellpadding="5" cellspacing="0" border="0" style="padding-left:50px;">'+
			'<tr>'+
			'<td>Dados Antigos:</td>'+
			'<td>'+d.old_values+'</td>'+
			'</tr>'+
			'<tr>'+
			'<td>Dados Novos:</td>'+
			'<td>'+d.new_values+'</td>'+
			'</tr>'+
			'<tr>'+
			'<td>URL:</td>'+
			'<td>'+d.url+'</td>'+
			'</tr>'+
			'<tr>'+
			'<td>User Agent:</td>'+
			'<td>'+d.user_agent+'</td>'+
			'</tr>'+
			'</table>';
		}
		var table =  $('#table').DataTable({
			"language": {
				"url": "{{ asset('plugins/datatables.net/js/Portuguese-Brasil.json')}}"
			},
			processing: true,
			serverSide: true,
			ajax: {
				type: 'POST',
				dataType: "json",
				url: '{!! route('auditoria') !!}',
				headers: {
					'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
				},
			},
			columns: [
			{
				"className":      'details-control',
				"orderable":      false,
				"data":           null,
				"searchable":           false,
				"defaultContent": ''
			},
			{ data: 'auditable_type', name: 'audits.auditable_type'},
			{ data: 'event', name: 'audits.event'},
			{ data: 'user.nome', name: 'user.nome', "defaultContent": ''},
			{ data: 'auditable_id', name: 'audits.auditable_id'},
			{ data: 'ip_address', name: 'audits.ip_address'},
			{ data: 'created_at', name: 'audits.created_at'},
			],
			 "order": [[ 6, "desc" ]]
		});
		$('#table tbody').on('click', 'td.details-control', function () {
			var tr = $(this).closest('tr');
			var row = table.row( tr );

			if ( row.child.isShown() ) {
            // This row is already open - close it
            row.child.hide();
            tr.removeClass('shown');
        }
        else {
            // Open this row
            row.child( format(row.data()) ).show();
            tr.addClass('shown');
        }
    });
	});
</script>
@endsection