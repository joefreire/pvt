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
		<h3>Relatório Quadro Multiplo</h3>
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
				<select id="Cidade" name="Cidade" class="form-control loadData" onClick="$(this).trigger('change');"> 
					<option value="">Selecione uma Cidade</option>
					@foreach(\App\Models\Cidades::where('uf',Auth::user()->cidade->uf)->get() as $cidade)
					<option value="{{ $cidade->codigo }}">{{ $cidade->municipio }}</option>
					@endforeach
				</select>
				<input type="hidden" id="CodCidade" class="form-control loadData"  name="CodCidade"> 
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
	
	<div id="Linkagem" style="display:none;">
		<div class="panel panel-default">
			<div class="panel-heading clearfix"> 
				<span id="titulo-painel" class="h3-semlinha">Dados do Quadro Multiplo (VÍTIMAS)</span>
			</div>
			<div class="panel-body">
				<div id="filtro" class="form-group row">
					<label for="Filtro_Acidentes" class="col-sm-2 col-form-label">Mostrar Acidentes:</label>
					<div class="col-sm-3">
						<select class="form-control" id="Filtro_Acidentes">
							<option value="">Todos</option>
							<option value="ApenasFatais">Apenas Fatais</option>
							<option value="ApenasFeridos">Apenas Feridos</option>
							<option value="ApenasLinkadosSIM">Apenas Linkados SIM</option>
							<option value="ApenasLinkadosSIH">Apenas Linkados SIH</option>
							<option value="ComFatores">Apenas Com Fatores Preenchidos</option>
							<option value="SemFatores">Sem Fatores Preenchidos</option>
						</select>
					</div>
					<label for="Filtro_Acidentes" class="col-sm-2 col-form-label">Buscar Vitima Por Nome:</label>
					<div class="col-sm-3">
						<input type="text" id="vitima" name="vitima" class="form-control">
					</div>
				</div> 
				<div class="table-responsive">
					<table class="table table-bordered table-hover display nowrap dataTable" id="table" width="100%">
						<thead>
							<tr>
								@php
								$colunas = (new \App\Models\QuadroMultiplo())->getTableColumns();
								$colunasFatores = (new \App\Models\FatoresRisco())->getTableColumns();
								@endphp
								<th>ID</th>
								<th>Vitimas Fatais</th>
								<th>Vitimas Graves</th>
								<th>Nome Vitimas</th>
								@foreach($colunas as $coluna)
								<th>{{ $coluna }}</th>
								@endforeach
								@foreach($colunasFatores as $colunaFatores)
								<th>{{ $colunaFatores }}</th>
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
				url: "{{ route('quadro.relatorio.data') }}",
				headers: {
					'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
				},
				data: function (d) {
					d.Ano = $('#Ano').val();
					d.Trimestre = $('#Trimestre').val();
					d.CodCidade = $('#CodCidade').val();
					d.Filtro = $('#Filtro_Acidentes').val();
					d.vitima = $('#vitima').val();
				}
			},
			language: {
				url: "{{ asset('libs/Datatables/Portuguese-Brasil.json') }}"
			},
			columns: [
			{data: 'id'},
			{ data: 'qtdfatais', orderable: false,searchable: false, render: function ( data, type, row, meta ) {
				return (data != null?data:'0'); 
			},
		},
		{ data: 'qtdleves', orderable: false,searchable: false, render: function ( data, type, row, meta ) {
			return (data != null?data:'0'); 
		},
	},
	{ data: 'NomeVitimas',searchable: false, orderable: false, name: 'vitimas_quadro_multiplo.NomeCompleto'},
	@foreach($colunas as $coluna)			
	{ data: '{{$coluna}}' },
	@endforeach
	@foreach($colunasFatores as $colunaFatores)
	{ name:'{{$colunaFatores}}', data: 'fatores_risco.{{$colunaFatores}}' , "defaultContent": "", orderable: false,searchable: false},
	@endforeach
	]
});
	});
	$('#Filtro_Acidentes').on('change', function () {
		$('#table').DataTable().ajax.reload();
	});
	$('#vitima').on('keyup', function () {
			$('#table').DataTable().ajax.reload();		
	});

	$('.loadData').change(function () {
		if ($('#Ano').val() < 2015) {
			$('#Ano').val('');
			$('#Ano').focus();
		} else {
			{
				if ($('#Ano').val() !== '' && $('#Trimestre').val() !== '' && $('#CodCidade').val() !== '' ){
					$('#loading-image').hide();
					$('.linkListaUnica').attr("href", "{{ route('listaUnica') }}?Ano="+$('#Ano').val()+"&Trimestre="+$('#Trimestre').val());
					$('.linkQuadroMultiplo').attr("href", "{{ route('quadroMultiplo') }}?Ano="+$('#Ano').val()+"&Trimestre="+$('#Trimestre').val());
					$('.linkParesSim').attr("href", "{{ route('sim.pares') }}?Ano="+$('#Ano').val()+"&Trimestre="+$('#Trimestre').val());
					$('.linkParesSih').attr("href", "{{ route('sih.pares') }}?Ano="+$('#Ano').val()+"&Trimestre="+$('#Trimestre').val());
					$.ajax(
					{
						url: '{{route('checkSim')}}',
						type: "POST",
						data: {
							Ano: $('#Ano').val(), 
							Trimestre: $('#Trimestre').val()
							@if(Auth::user()->tipo < 3)
							,CodCidade: $('#CodCidade').val()
							@endif
						},
						headers: {
							'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
						},
						success: function (data, textStatus, jqXHR)
						{
							$("#arquivo").val('');
							$("#AnoGrande").val($("#Ano").val());
							$("#TrimestreGrande").val($("#Trimestre").val());
							console.log(data)

							if (data.processo == 0 
								&& data.pendencias == 0 
								&& data.sim == 0 
								&& data.lista > 0) {
								console.log('dsa')
							$('#desabilitar').show();
							$('#_delete').hide();
							$('#_processo').hide();
							$('#_ListaUnica').hide();
							$('#Linkagem').hide();
							$('#Pendencia').hide();
						}else if (data.processo > 0 ) {
							$('#desabilitar').hide();
							$('#_delete').hide();
							$('#_processo').show();
							$('#_ListaUnica').hide();
							$('#Linkagem').hide();
							$('#Pendencia').hide();
						}else if (data.lista == 0 ) {
							$('#desabilitar').hide();
							$('#_delete').hide();
							$('#_processo').hide();
							$('#_ListaUnica').show();
							$('#Linkagem').hide();
							$('#Pendencia').hide();
						}else if (data.pendencias > 0 ) {
							$('#desabilitar').hide();
							$('#_delete').hide();
							$('#_processo').hide();
							$('#_ListaUnica').hide();
							$('#Linkagem').hide();
							$('#Pendencia').show();
						}else if (data.sim > 0 ) {
							$('#table').DataTable().ajax.reload();
							$('#desabilitar').hide();
							$('#_delete').hide();
							$('#_processo').hide();
							$('#_ListaUnica').hide();
							$('#Linkagem').show();
							$('#Pendencia').hide();
						}
					},
					error: function (jqXHR, textStatus, errorThrown)
					{
						if(jqXHR.status == 419){
							location.reload();
						}
						console.log("erro cidade");
					}
				});

				}
			}
		}

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
		@if(Auth::user()->tipo == 1)
		$('#Cidade').change(function () {
			$.ajax({
				url: '{{ route('getCidades') }}',
				type: "POST",
				data: {
					Cidade: $('#Cidade').val(), Estado: $('#Estado').val()
				},
				success: function (data, textStatus, jqXHR) {
					$("#CodCidade").val(data);
					$("#CodCidadeGrande").val(data);
					$('#Trimestre').trigger('change')
				},
				error: function (jqXHR, textStatus, errorThrown) {
					if(jqXHR.status == 419){
						location.reload();
					}
					alertify.error('Erro ao buscar cidade');
				}
			});
		});
		@endif
		@if(Auth::user()->tipo >= 3)
		$("#CodCidade").val('{{Auth::user()->CodCidade}}');
		@endif

	});
</script>

@endsection