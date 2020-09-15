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
		<h3>Pares SIM</h3>
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

		</div>
		@if (Auth::user()->tipo == 1)
		<div class="form-group">
			<label for="Estado">Estado:</label>  
			<select id="Estado" name="Estado" class="form-control" > 
			</select>
			<span>&nbsp&nbsp</span> 
		</div>
		<div class="form-group">
			<label for="Cidade">Município:</label>  
			<select id="Cidade" name="Cidade" class="form-control"> 
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
			<input type="hidden" id="CodCidade" class="form-control loadData" name="CodCidade"> 
		</div>
		@else
		<input type="hidden" id="CodCidade" name="CodCidade" value="{{ Auth::user()->CodCidade }}">
		@endif
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
	<div id="_processo" style="display:none;">   
		<span>Sim em processamento</span> <BR>
		<span>Aguarde</span> <BR><BR>
	</div>
	<div id="_ListaUnica" style="display:none;">
		<span>Primeiro você deve fazer o upload da  <a href="{{route('listaUnica')}}" class="linkListaUnica">Lista Única</a></span>
	</div>
	<div id="_SIM" style="display:none;">
		<span>Primeiro você deve fazer o upload do  <a href="{{route('sim')}}" class="linkSim">SIM</a></span>
	</div>
	<div id="Linkagem" style="display:none;">
		<div class="panel panel-default">
			<div class="panel-heading clearfix"> 
				<span id="titulo-painel" class="h3-semlinha"> Possíveis pares ainda não marcados na Linkagem entre o SIM e a Lista Única</span>
			</div>
			<div class="panel-body">
				<p id="QtdListaUnica"></p>
				<p id="QtdSIM"></p>
				<p id="QtdLinkagem"></p>
				
				<div class="form-group acaoPar" style="display:none;margin-top: 5px">
					<button id="ParFalso" class="btn btn-default">Par Falso</button>
					<button id="ParVerdadeiro" class="btn btn-default">Par Verdadeiro</button>
				</div>
				<div class="table-responsive">
					<table class="table table-bordered table-hover display nowrap dataTable" id="table" width="100%">
						<thead>
							<tr>
								<th id="selecionar_lista" title="Selecionar valores monstrados"><input type="checkbox" id="input_selecionar_lista" class="form-check-input"></th>
								<th></th>
								<th></th>
								<th>Nome <BR> Lista Única</th>
								<th>Nome <BR> SIM</th>
								<th>Data Nascimento <BR> Lista Única</th>
								<th>Data Nascimento <BR> SIM</th>
								<th>Data Acidente <BR> Lista Única</th>
								<th>Data Óbito <BR> SIM</th>
								<th>Sexo<BR>Lista Única</th>
								<th>Sexo<BR>SIM</th>
								<th>CAUSABAS<BR>SIM</th>
								<th>NOME DA MÃE<BR>Lista Única</th>
								<th>NOME DA MÃE <BR>SIM</th>
								<th>Município<BR>Ocorrência</th>
								<th>Endereço Acidente(LISTA ÚNICA)</th>
								<th>Identificador do Acidente</th>
							</tr>
						</thead>
					</table>
				</div>
			</div>
		</div>
	</div>
	<div id="Pendencia" style="display:none;">
		<span>Primeiro você deve primeiro resolver as pendências na <a href="{{route('listaUnica')}}" class="linkListaUnica"> Lista única </a></span>
	</div>
</div>

<!-- Modal Informações-->
<div class="modal fade" id="informacoes" tabindex="-1" role="dialog" aria-labelledby="informacoesLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title" id="informacoesLabel">Pares Verdadeiros Lista Única / SIM</h4>
			</div>
			<form method="POST" action="{{ route('sim.salvaPares') }}">
				@csrf
				<div class="modal-body">
					<div id='ids_pares' name="ids_pares"></div>
					<div name="Trimestre" value=""></div>
					<div id='pares'></div>
				</div>
				<div class="modal-footer">
					<span> Deseja confirmar? </span>
					<button type="submit" class="btn btn-success">Sim</button>
					<button type="button" class="btn btn-danger" data-dismiss="modal">Não</button>
				</div>
			</form>
		</div>
	</div>
</div>



@endsection

@section('scripts')
<script src="//cdn.datatables.net/select/1.3.1/js/dataTables.select.min.js"></script>
<script src="//cdn.datatables.net/buttons/1.6.1/js/dataTables.buttons.min.js"></script>

<script src="//cdn.datatables.net/buttons/1.6.1/js/buttons.bootstrap.min.js"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>

<script src="//cdn.datatables.net/buttons/1.5.6/js/buttons.html5.min.js"></script>
<script src="//cdn.datatables.net/buttons/1.5.6/js/buttons.print.min.js"></script>

<script type="text/javascript">
	$(document).on('change', '#input_selecionar_lista', function() {
		$('.select-checkbox').each(function (index, value){
			$(this).click()
		})
	});

	$(document).ready(function() {

		function format ( d ) {
			var tableQuadro= '<h5> Dados do Acidente</h5><table class="table table-bordered" width="100%"><thead><tr>';

			$.each(d.dados_acidente, function(key, value) {
				tableQuadro= tableQuadro+ '<th>'+key+'</th>';
			});
			tableQuadro= tableQuadro+ '</tr></thead><tbody><tr>';
			$.each(d.dados_acidente, function(key, value) {
				tableQuadro= tableQuadro+ '<td>'+String(value)+'</td>';
			});
			tableQuadro= tableQuadro+ '</tr></tbody><table>';

			var tableVitima = '<h5> Dados Originais Lista única </h5><table class="table table-bordered" width="100%"><thead><tr>';

			$.each(d.dados_vitima, function(key, value) {
				tableVitima = tableVitima + '<th>'+key+'</th>';
			});
			tableVitima = tableVitima + '</tr></thead><tbody><tr>';
			$.each(d.dados_vitima, function(key, value) {
				tableVitima = tableVitima + '<td>'+String(value)+'</td>';
			});
			tableVitima = tableVitima + '</tr></tbody><table>';

			var tableSim = '<h5> Dados Originais SIM </h5><table class="table table-bordered" width="100%"><thead><tr>';

			$.each(d.dados_sim, function(key, value) {
				tableSim = tableSim + '<th>'+key+'</th>';
			});
			tableSim = tableSim + '</tr></thead><tbody><tr>';
			$.each(d.dados_sim, function(key, value) {
				tableSim = tableSim + '<td>'+String(value)+'</td>';
			});
			tableSim = tableSim + '</tr></tbody><table>';
			
			return tableQuadro+tableVitima+tableSim;
		}
		var table = $('#table').DataTable({
			processing: true,
			serverSide: true,
			responsive: false,
			lengthChange: true,
			lengthMenu: [ [10, 25, 100, -1], [10, 25, 100, "Todos"] ],
			deferLoading: 0,
			dom: '<"float-left"B<"toolbar">><"float-right"f>t<"bottom"<"float-left"lri><"float-right"p>>',
			buttons: [ 
			// {
			// 	text: 'Marcar todos os pares como Falsos',
			// 	action: function ( e, dt, node, config ) {
			// 		dt.ajax.reload();
			// 	}
			// },
			// { extend: 'selectAll', text: 'Selecionar Todos Registros' },
			// { extend: 'selectNone', text: 'Remover Todos' },
			{
				extend: 'excelHtml5',
				text: '<i class="fa fa-table"></i> Excel',
				autoFilter: true,

			}, 
			{
				extend: 'pdfHtml5',
				text: '<i class="fa fa-file"></i> PDF',
				autoFilter: true,
				orientation: 'landscape',
				pageSize: 'LEGAL',
				customize: function(doc) {
					doc.defaultStyle.fontSize = 7; 
				}  
			}
			],
			ajax: {
				type: 'POST',
				dataType: "json",
				url: "{{ route('sim.data') }}",
				headers: {
					'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
				},
				data: function (d) {
					d.Ano = $('#Ano').val();
					d.Trimestre = $('#Trimestre').val();
					@if(Auth::user()->tipo < 3)
					d.CodCidade = $('#CodCidade').val();
					@endif
				}
			},
			language: {
				url: "{{ asset('libs/Datatables/Portuguese-Brasil.json') }}"
			},
			columns: [
			{
				orderable: false,
				searchable: false,
				className: 'select-checkbox',
				targets:   0,
				defaultContent: '',
			},
			{
				className:      'details-control',
				orderable:      false,
				data:           null,
				searchable:           false,
				defaultContent: ''
			},
			{ data: 'Score', name: 'Score', searchable: false},
			{ data: 'vitima.NomeCompleto', name: 'vitima.NomeCompleto' },
			{ data: 'sim.NOME', name: 'sim.NOME' },
			{ data: 'vitima.DataNascimento', name: 'vitima.DataNascimento' },
			{ data: 'sim.DTNASC', name: 'sim.DTNASC' },
			{ data: 'vitima.DataAcidente', name: 'vitima.DataAcidente' },
			{ data: 'sim.DTOBITO', name: 'sim.DTOBITO' },
			{ data: 'vitima.Sexo', name: 'vitima.Sexo' },
			{ data: 'SexoSIM'},
			{ data: 'sim.CAUSABAS', name: 'sim.CAUSABAS' },
			{ data: 'vitima.NomeMae', name: 'vitima.NomeMae' },
			{ data: 'sim.NOMEMAE', name: 'sim.NOMEMAE' },
			{ data: 'quadro_multiplo.CidadeAcidente', name: 'quadro_multiplo.CidadeAcidente' },
			{ data: 'quadro_multiplo.RuaAvenida', name: 'quadro_multiplo.RuaAvenida' },
			{ data: 'quadro_multiplo.IdentificadorAcidente', name: 'quadro_multiplo.IdentificadorAcidente' },
			],
			select: {
				style: 'multi',
				selector: 'td:first-child:not(.details-control)'
			},
			order: [[ 1, "desc" ]]
		});

		table.on( 'select', function ( e, dt, type, indexes ) {      
			if ( type === 'row' ) {  
				data = table.rows( { selected: true } ).data()
				if(data.length > 0){
					$(".acaoPar").show()
				}else{
					$(".acaoPar").hide()
				}
			}
		});
		table.on( 'deselect', function ( e, dt, type, indexes ) {   

			if ( type === 'row' ) {        
				data = table.rows( { selected: true } ).data()
				console.log()
				if(data.length > 0){
					$(".acaoPar").show()
				}else{
					$(".acaoPar").hide()
				}
			}
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
		$('#ParFalso').click(function () {

			$('#ids_pares').html('');
			$('#pares').html('');
			$('#ids_pares').append('<input id="tipo" name="tipo" type="hidden" value="0">');
			data = [];
			var data = $('#table').DataTable().rows({selected: true}).data();
			
			$.each(data, function(key, value) {
				$('#pares').append('Nome Lista Única: ' + value.vitima.NomeCompleto + '<BR> Nome SIM :' + value.sim.NOME + '<BR>');
				$('#ids_pares').append('<input id="ids' + value.id + '" name="ids_pares[]" type="hidden" value="' + value.id + '">');
				$('#informacoesLabel').html('Pares Falsos Lista Única / SIM');
				$('#informacoes').modal('show');
			});
		});
		$('#ParVerdadeiro').click(function () {

			$('#ids_pares').html('');
			$('#pares').html('');
			$('#ids_pares').append('<input id="tipo" name="tipo" type="hidden" value="1">');
			data = [];
			var data = $('#table').DataTable().rows({selected: true}).data();
			console.log(data)

			$.each(data, function(key, value) {
				//console.log(key, value)
				if ((value.vitima.DataAcidente == value.sim.DTOBITO) && (value.vitima.NomeCompleto == value.sim.NOME) && (value.vitima.Sexo == value.SexoSIM) && (value.vitima.DataNascimento == value.sim.DTNASC)) {
					$('#pares').append('<div class="alert alert-success"> \n\
						<button type="button" onclick="removeID(' + value.id + ');" class="close" data-dismiss="alert" aria-label="Close"> <span aria-hidden="true">&times;</span></button> \n\
						Nome Lista Única: ' + value.vitima.NomeCompleto + '<br> Nome SIM :' + value.sim.NOME + '\n\
						<BR>Data Nascimento Lista Única: ' + value.vitima.DataNascimento + ' <BR> Data Nascimento SIM :' + value.sim.DTNASC + '\n\
						<BR>Sexo Lista Única: ' + value.vitima.Sexo + ' <BR> Sexo SIM :' + value.SexoSIM + '\n\
						<BR>Data Acidente Lista Única: ' + value.vitima.DataAcidente + ' <BR> Data Óbito :' + value.sim.DTOBITO + '\n\
						<BR>Mãe Lista Única: ' + value.vitima.NomeMae + ' <BR> Mãe SIM :' + value.sim.NOMEMAE + '\n\
						<BR>Causa Base: ' + value.sim.CAUSABAS + '\n\
						</div>');
				} else {
					var data_lista = new Date(value.vitima.DataAcidente.replace(/(\d{2})[-/](\d{2})[-/](\d{4})/, "$2/$1/$3"));
					var data_obito = new Date(value.sim.DTOBITO.replace(/(\d{2})[-/](\d{2})[-/](\d{4})/, "$2/$1/$3"));
					var trinta_dias = new Date(data_lista.getFullYear(), data_lista.getMonth(), data_lista.getDate() + 31)

					if (data_obito > trinta_dias) {
						$('#pares').append('<div class="alert alert-danger"> \n\
							<button type="button" onclick="removeID(' + value.id + ');" class="close" data-dismiss="alert" aria-label="Close"> <span aria-hidden="true">&times;</span></button> \n\
							<strong>ATENÇÃO: VERIFIQUE TODOS OS DADOS ANTES DE APROVAR</STRONG><BR><BR>\n\
							\n\                                                     Nome Lista Única: ' + value.vitima.NomeCompleto + '<br> Nome SIM :' + value.sim.NOME + '\n\
							<BR>Data Nascimento Lista Única: ' + value.vitima.DataNascimento + ' <BR> Data Nascimento SIM :' + value.sim.DTNASC + '\n\
							<BR>Sexo Lista Única: ' + value.vitima.Sexo + ' <BR> Sexo SIM :' + value.SexoSIM + '\n\
							<BR><strong>ATENÇÃO: DATA DO ACIDENTE MAIOR QUE 31 DIAS DA DATA DO ÓBITO\n\
							<BR>Data Acidente Lista Única: ' + value.vitima.DataAcidente + ' <BR> Data Óbito :' + value.sim.DTOBITO + '</STRONG>\n\
							<BR>Causa Base: ' + value.sim.CAUSABAS + '<BR>IDENTIFICADOR ACIDENTE: ' + value.quadro_multiplo.IdentificadorAcidente + '\n\
							</div>');
					} else {
						if (data_lista <= data_obito) {
							$('#pares').append('<div class="alert alert-info"> \n\
								<button type="button" onclick="removeID(' + value.id + ');" class="close" data-dismiss="alert" aria-label="Close"> <span aria-hidden="true">&times;</span></button> \n\
								<strong>ATENÇÃO: VERIFIQUE TODOS OS DADOS ANTES DE APROVAR</STRONG><BR><BR>\n\
								' + ((value.vitima.NomeCompleto != value.sim.NOME) ? '<STRONG>' : '') + '\n\
								Nome Lista Única: ' + value.vitima.NomeCompleto + '<br> Nome SIM :' + value.sim.NOME + '\n\  \n\
								' + ((value.vitima.NomeCompleto != value.sim.NOME) ? '</STRONG>' : '') + '\n\
								' + ((value.vitima.DataNascimento != value.sim.DTNASC) ? '<STRONG>' : '') + '\n\
								<BR>Data Nascimento Lista Única: ' + value.vitima.DataNascimento + ' <BR> Data Nascimento SIM :' + value.sim.DTNASC + '\n\
								' + ((value.vitima.DataNascimento != value.sim.DTNASC) ? '</STRONG>' : '') + '\n\
								' + ((value.vitima.Sexo != value.SexoSIM) ? '<STRONG>' : '') + '\n\
								<BR>Sexo Lista Única: ' + value.vitima.Sexo + ' <BR> Sexo SIM :' + value.SexoSIM + '\n\
								' + ((value.vitima.Sexo != value.SexoSIM) ? '</STRONG>' : '') + '\n\
								' + ((value.vitima.DataAcidente != value.sim.DTOBITO) ? '<STRONG>' : '') + '\n\
								<BR>Data Acidente Lista Única: ' + value.vitima.DataAcidente + ' <BR> Data Óbito :' + value.sim.DTOBITO + '\n\
								' + ((value.vitima.DataAcidente != value.sim.DTOBITO) ? '</STRONG>' : '') + '\n\
								' + ((value.vitima.NomeMae != value.sim.NOMEMAE) ? '<STRONG>' : '') + '\n\
								<BR>Mãe Lista Única: ' + value.vitima.NomeMae + ' <BR> Mãe SIM :' + value.sim.NOMEMAE + '\n\
								' + ((value.vitima.NomeMae != value.sim.NOMEMAE) ? '</STRONG>' : '') + '\n\
								<BR>Causa Base: ' + value.sim.CAUSABAS + '<BR>IDENTIFICADOR ACIDENTE: ' + value.quadro_multiplo.IdentificadorAcidente + '\n\
								</div>');
						} else {
							$('#pares').append('<div class="alert alert-danger"> \n\
								<button type="button" onclick="removeID(' + value.id + ');" class="close" data-dismiss="alert" aria-label="Close"> <span aria-hidden="true">&times;</span></button> \n\
								<strong>ATENÇÃO: VERIFIQUE TODOS OS DADOS ANTES DE APROVAR</STRONG><BR><BR>\n\
								\n\                                                     Nome Lista Única: ' + value.vitima.NomeCompleto + '<br> Nome SIM :' + value.sim.NOME + '\n\
								<BR>Data Nascimento Lista Única: ' + value.vitima.DataNascimento + ' <BR> Data Nascimento SIM :' + value.sim.DTNASC + '\n\
								<BR>Sexo Lista Única: ' + value.vitima.Sexo + ' <BR> Sexo SIM :' + value.SexoSIM + '\n\
								<BR><strong>ATENÇÃO: DATA DO ACIDENTE MAIOR QUE A DATA DO ÓBITO</STRONG>\n\
								<BR>Data Acidente Lista Única: ' + value.vitima.DataAcidente + ' <BR> Data Óbito :' + value.sim.DTOBITO + '\n\
								<BR>Causa Base: ' + value.sim.CAUSABAS + '<BR>IDENTIFICADOR ACIDENTE: ' + value.quadro_multiplo.IdentificadorAcidente + '\n\
								</div>');
						}
					}
				}
				$('#ids_pares').append('<input id="ids' + value.id + '" name="ids_pares[]" type="hidden" value="' + value.id + '">');

			});
$('#informacoesLabel').html('Pares Verdadeiros Lista Única / SIM');
$('#informacoes').modal('show');

});




});

function removeID(id) {
	console.log(id);
	$('#ids' + id).remove();
	$('#SEXO' + id).remove();
	$('#mae' + id).remove();
	$('#NASCIMENTO' + id).remove();
	$('#endereco_res' + id).remove();
	$('#numsus' + id).remove();
	$('#vitimas' + id).remove();
}

$('.loadData').change(function () {
	if ($('#Ano').val() < 2015) {
		$('#Ano').val('');
		$('#Ano').focus();
	} else {
		{
			if ($('#Ano').val() !== '' && $('#Trimestre').val() !== '' ){
				$('#loading-image').hide();
				$('.linkListaUnica').attr("href", "{{ route('listaUnica') }}?Ano="+$('#Ano').val()+"&Trimestre="+$('#Trimestre').val());
				$('.linkSim').attr("href", "{{ route('sim') }}?Ano="+$('#Ano').val()+"&Trimestre="+$('#Trimestre').val());
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
						
						$('#QtdListaUnica').html('Nesse período temos '+data.lista+' vítimas na Lista Única');
						$('#QtdSIM').html('Nesse período temos '+data.sim+' vítimas no SIM');
						$('#QtdLinkagem').html('Nesse período foram encontrados '+data.linkagem+' possíveis pares');
						if (data.processo > 0 ){
							$('#desabilitar').hide();
							$('#_delete').hide();
							$('#_processo').show();
							$('#_SIM').hide();
							$('#_ListaUnica').hide();
							$('#Linkagem').hide();
							$('#Pendencia').hide();
						}else if (data.processo == 0 
							&& data.pendencias == 0 
							&& data.sim == 0 
							&& data.lista > 0){
							$('#desabilitar').show();
							$('#_delete').hide();
							$('#_processo').hide();
							$('#_SIM').show();
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
							$('#_SIM').hide();
							$('#_processo').hide();
							$('#_ListaUnica').hide();
							$('#Linkagem').show();
							$('#Pendencia').hide();
						}else if (data.lista > 0 && data.sim == 0) {
							$('#desabilitar').hide();
							$('#_delete').hide();
							$('#_processo').hide();
							$('#_ListaUnica').hide();
							$('#_SIM').hide();
							$('#Linkagem').show();
							$('#Pendencia').hide();
						}
					},
					error: function (jqXHR, textStatus, errorThrown)
					{
						if(jqXHR.status == 419){
							location.reload();
						}
						console.log(jqXHR, textStatus, errorThrown)
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
@if (Auth::user()->tipo == 1)
<script language="JavaScript" type="text/javascript" charset="utf-8">
	new dgCidadesEstados({
		cidade: document.getElementById('Cidade'),
		estado: document.getElementById('Estado')
	})
</script>
@endif
@endsection