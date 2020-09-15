@extends('layouts.app')
@section('styles')
<link rel="stylesheet" href="//cdn.datatables.net/select/1.3.1/css/select.dataTables.min.css">
<style>
	.erro_celula{
		background-color: #a94442;
	}
	#loading-image {
		text-align: center;
	}
	.ui-progressbar {
		position: relative;
	}
	.progress-label {
		position: absolute;
		left: 46%;
		top: 4px;
		font-weight: bold;
		text-shadow: 1px 1px 0 #fff;
		z-index: 1;
	}
	.ui-progressbar-value {
		position: absolute;
		display: block;
		width: 100%;
	}
	.info {
		max-width: 20px;
	}
</style>
@endsection
@section('content')

<div class="col-md-12">  
	<div class="page-header">
		<h3>Relatório SIH</h3>
	</div>
	<div class="form-inline">        
		<div class="form-group">  
			<label class="control-label " for="Ano" style="text-align: left;">Ano :  </label>
			<input type="number" min="2015" max="9999" step="1" value="{{ request()->get('Ano') }}" class="form-control loadData" id="Ano" name="Ano">      
			<span>&nbsp&nbsp</span> 
		</div>

		<div class="form-group">
			<label class="control-label" for="Trimestre" style="text-align: left;">Trimestre :  </label>
			<select name="Trimestre" id="Trimestre" class="form-control loadData">
				<option value=""></option>
				<option value="1" {{ request()->get('Trimestre') == '1' ? 'selected' : '' }}>Primeiro</option>
				<option value="2" {{ request()->get('Trimestre') == '2' ? 'selected' : '' }}>Segundo</option>
				<option value="3" {{ request()->get('Trimestre') == '3' ? 'selected' : '' }}>Terceiro</option>
				<option value="4" {{ request()->get('Trimestre') == '4' ? 'selected' : '' }}>Quarto</option>
			</select>
			@if (Auth::user()->tipo == 1)
			<div class="form-group">
				<label for="Estado">Estado:</label>  
				<select id="Estado" name="Estado" class="loadData form-control" > 
				</select>
				<span>&nbsp&nbsp</span> 
			</div>
			<div class="form-group">
				<label for="Cidade">Município:</label>  
				<select id="Cidade" name="Cidade" class="form-control loadData"> 
					<option value="">Selecione um Estado</option>
				</select>
				<input type="hidden" id="CodCidade" name="CodCidade"> 
			</div>
			@elseif(Auth::user()->tipo == 2)
			<div class="form-group">
				<label for="Cidade">Município:</label>  
				<select id="Cidade" name="CodCidade" class="form-control loadData"> 
					<option value="">Selecione uma Cidade</option>
					@foreach(\App\Models\Cidades::where('uf',Auth::user()->cidade->uf)->get() as $cidade)
					<option value="{{ $cidade->codigo }}">{{ $cidade->municipio }}</option>
					@endforeach
				</select>
			</div>
			@endif

		</div>


	</div>  
	<BR>
</div> 

<div class="col-md-12">     
	<hr class="separator" style="
	margin-top: 0px;
	margin-bottom: 10px;
	border: 2px solid;
	">
</div> 

<div class="col-md-12">    
	<BR>
	<div id="desabilitar" style="display:none;">  

		<h3 class="control-label" for="arquivos" style="text-align: left;">Selecione o arquivo do SIH </h3>
		<form name="sendFile" class="form-inline" method="POST" enctype="multipart/form-data" action="{{route('gravaSih')}}">
			<p></p>
			<div class="form-group">
				<label for="email">Arquivo Sih:</label>
				<input type="file" class="form-control" name="arquivo" id="arquivo" /> 
			</div>
			@csrf
			<input type="hidden" name="Ano" id="AnoGrande">
			<input type="hidden" name="Trimestre" id="TrimestreGrande">
			<button type="submit" class="btn btn-default">Enviar</button>
		</form>
	</div>
	<div id="_delete" style="display:none;">  
		<img src="{{ asset('libraries/img/loading2.gif') }}" alt="" style="width: 5%;">
		<br><BR>
		<span>DELETANDO DADOS</span>
		<span><BR>Limpando os dados no banco de dados <BR> Este processo pode demorar um pouco<BR> Não Feche o Navegador <BR></span>
		<BR><BR>

	</div>
	
	<div id="SIH" style="display:none;">
		<div class="panel panel-default">
			<div class="panel-heading clearfix"> 
				<span id="titulo-painel" class="h3-semlinha">Dados Importados do SIH</span>
			</div>
			<div class="panel-body">
				<div id="filtro" class="form-group row">
					<label for="Filtro_Acidentes" class="col-sm-2 col-form-label">Mostrar:</label>
					<div class="col-sm-3">
						<select class="form-control" id="Filtro_Acidentes">
							<option value="">Todos</option>
							<option value="ApenasLinkadosSIH">Apenas Linkados</option>
							<option value="ApenasVerdadeiros">Apenas Verdadeiros</option>
							<option value="ApenasTransito">Apenas Trânsito</option>

						</select>
					</div>
				</div>  

				<div class="table-responsive">
					<table class="table table-bordered table-hover display nowrap dataTable" id="table" width="100%">
						<thead>
							<tr>
								<th>id</th>
								@php
								$colunas = (new \App\Models\Sih())->getTableColumns();
								@endphp								
								<th>Linkado</th>
								<th>Verdadeiro</th>
								<th>Trânsito</th>
								@foreach($colunas as $coluna)
								<th>{{ $coluna }}</th>
								@endforeach
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
@if (Auth::user()->tipo == 1)
<script language="JavaScript" type="text/javascript" charset="utf-8">
	new dgCidadesEstados({
		cidade: document.getElementById('Cidade'),
		estado: document.getElementById('Estado')
	})
</script>
@endif
<script src="//cdn.datatables.net/select/1.3.1/js/dataTables.select.min.js"></script>
<script src="//cdn.datatables.net/buttons/1.6.1/js/dataTables.buttons.min.js"></script>

<script src="//cdn.datatables.net/buttons/1.6.1/js/buttons.bootstrap.min.js"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>

<script src="//cdn.datatables.net/buttons/1.5.6/js/buttons.html5.min.js"></script>
<script src="//cdn.datatables.net/buttons/1.5.6/js/buttons.print.min.js"></script>

<script type="text/javascript">
	$(document).ready(function() {
		@if (Auth::user()->tipo == 1)
		$('#Cidade').change(function () {
			$.ajax(
			{
				url: '{{ route('getCidades') }}',
				type: "POST",
				headers: {
					'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
				},
				data: {Cidade: this.value, Estado: $('#Estado').val()},
				success: function (data, textStatus, jqXHR)
				{
					$("#CodCidade").val(data);
					$('#Ano').change()
					console.log(data);
				},
				error: function (jqXHR, textStatus, errorThrown)
				{
					if(jqXHR.status == 419){
						location.reload();
					}
					console.log("erro cidade");
				}
			});

		});
		@endif

		$('#CodCidade').change(function () {
			$('#Trimestre').trigger('change');
		});
		var table = $('#table').DataTable({
			processing: true,
			serverSide: true,
			responsive: false,
			lengthChange: true,
			lengthMenu: [ [10, 25, 100, -1], [10, 25, 100, "Todos"] ],
			deferLoading: 0,
			dom: '<"float-left"B<"toolbar">><"float-right"f>t<"bottom"<"float-left"lri><"float-right"p>>',
			buttons: [ 
			{
				extend: 'excelHtml5',
				text: '<i class="fa fa-table"></i> Excel',
				autoFilter: true,

			}, 
			// {
			// 	extend: 'pdfHtml5',
			// 	text: '<i class="fa fa-file"></i> PDF',
			// 	autoFilter: true,
			// 	orientation: 'landscape',
			// 	pageSize: 'LEGAL',
			// 	customize: function(doc) {
			// 		doc.defaultStyle.fontSize = 7; 
			// 	}  
			// }
			],
			ajax: {
				type: 'POST',
				dataType: "json",
				url: "{{ route('sih.relatorio.data') }}",
				headers: {
					'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
				},
				data: function (d) {
					d.Ano = $('#Ano').val();
					d.Trimestre = $('#Trimestre').val();
					d.CodCidade = $('#CodCidade').val();
					d.Filtro = $('#Filtro_Acidentes').val();
				}
			},
			language: {
				url: "{{ asset('libs/Datatables/Portuguese-Brasil.json') }}"
			},
			columns: [
			{ data: 'id', searchable: false, orderable: false},
			{ data: 'linkado', searchable: false, orderable: false},
			{ data: 'ParVerdadeiro',searchable: false, orderable: false},
			{ data: 'acidente_transito', orderable: false},
			@foreach($colunas as $coluna)
			{ data: '{{$coluna}}' },
			@endforeach
			]
		});
	});

	$('.loadData').change(function () {
		if ($('#Ano').val() < 2015) {
			$('#Ano').val('');
			$('#Ano').focus();
		} else {
			{
				if ($('#Ano').val() !== '' && $('#Trimestre').val() !== '' ){
					$('#table').DataTable().ajax.reload();
					$('#SIH').show();

				}
			}
		}

	});
	$('#Filtro_Acidentes').on('change', function () {
		$('#table').DataTable().ajax.reload();
	});

	$(document).ready(function(){
		@if( request()->get('Trimestre') && request()->get('Ano') )
		$("#Trimestre").trigger('change');
		@endif
		@if( \Session::has('Ano') && \Session::has('CodCidade') && \Session::has('Trimestre') )
		$("#CodCidade").val('{{\Session::has("CodCidade")}}');
		$("#Trimestre").trigger('change');
		@endif
		@if(Auth::user()->tipo == 2)
		$('#Cidade').change(function () {
			$("#CodCidade").val($(this).val()).trigger('change');
			$("#CodCidadeGrande").val($(this).val());
		});
		@endif
		@if(Auth::user()->tipo >= 3)
		$("#CodCidade").val('{{Auth::user()->CodCidade}}');
		@endif

	});
</script>

@endsection