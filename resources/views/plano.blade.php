@extends('layouts.app')
@section('styles')
<style>
	.fixInlineForm {
		margin-top: 6px;
	}
	.red {
		background-color: red;
		color: white;
	}


</style>
<link href="{{asset('libraries/css/formValidation.min.css')}}" rel="stylesheet" type="text/css"/>
<script src="{{asset('libraries/js/formValidation.min.js')}}" type="text/javascript"></script>
<script src="{{asset('libraries/js/form-bootstrap.min.js')}}" type="text/javascript"></script>
<link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/bootstrap-table/1.11.0/bootstrap-table.min.css">

<link rel="stylesheet" type="text/css" href="{{asset('libraries/css/alertify.css')}}">
<link rel="stylesheet" type="text/css" href="{{asset('libraries/css/bootstrap_alertify.css')}}">
@endsection
@section('content')

<div class="col-md-12">  
	<div class="page-header">
		<h3>Plano das Ações Integradas</h3>
	</div>
	<div class="form-inline">        
		<div class="form-group">  
			<label class="control-label " for="Ano" style="text-align: left;">Ano :  </label>
			<input type="number" min="2015" max="9999" step="1" value="{{ request()->get('Ano') }}" class="form-control loadData" id="Ano" name="Ano">      
			<span>&nbsp&nbsp</span> 
		</div>

		<div class="form-group">
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
			@else
			<input type="hidden" id="CodCidade" name="CodCidade" value="{{ Auth::user()->CodCidade }}">
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
	
	<div id="_tabelaResults" style="display:none;">
		<div class="panel panel-default">
			<div class="panel-heading clearfix"> 
				<span id="titulo-painel" class="h3-semlinha">Plano de ações</span>
			</div>
			<div class="panel-body">
				<div class="table-responsive">
					<h2>Programas</h2>
					<button type="button" id="adicionarPlano" class="btn btn-primary" style="display:none;"><span class="glyphicon glyphicon-plus"></span>Novo Programa</button>    
					<table id="table" data-toggle="table"
					data-search="true"
					data-show-refresh="true"
					data-show-toggle="false"
					data-show-columns="false"
					data-advanced-search="false"
					data-detail-view="true"
					data-detail-formatter="detailFormatter"
					data-show-pagination-switch="false"
					data-pagination="false"
					data-query-params="queryParams"
					data-page-list="[10, 25, 50, 100, ALL]">
					<thead>
						<tr>
							<th data-field="id" data-sortable="true">ID:</th>
							<th data-field="NomePrograma" data-sortable="true">Nome do Programa:</th>
							<th data-field="PesoPrograma" data-sortable="true">Peso do Programa:</th>
							<th data-field="ObjetivoPrograma" data-sortable="true">Objetivo do Programa:</th>
							<th data-field="CoordenadorPrograma" data-sortable="true">Coordenador do Programa:</th>
							<th data-field="id" data-formatter="operateFormatter" data-events="operateEvents">Opções</th>

						</tr>
					</thead>
				</table> 
				<h2>Intervenções (Ações)</h2>
				<button type="button" class="btn btn-small btn-primary" onClick="projeto();"><span class="glyphicon glyphicon-plus"></span> Nova intervenção (ação)</button>
				<table id="tableProjetos" data-toggle="table"
				data-search="true"
				data-show-refresh="false"
				data-show-toggle="false"
				data-show-columns="true"
				data-advanced-search="false"
				data-detail-view="false"
				data-show-pagination-switch="false"
				data-pagination="false"
				data-query-params="queryParams"
				data-show-footer="false"
				>
				<thead>
					<tr>
						<th data-field="id" data-formatter="editProjeto" data-events="editProjeto">Opções</th>
						<th data-field="NomesProgramas" data-footer-formatter="idFormatter" data-sortable="true">Programas:</th>
						<th data-field="TipoProjeto" data-footer-formatter="idFormatter" data-sortable="true">Projeto:</th>
						<th data-field="NomeProjeto" data-footer-formatter="nameFormatter" data-sortable="true">Nome da intervenção (ação):</th>

						<th data-field="DescricaoProjeto"  data-footer-formatter="nameFormatter" data-sortable="true">Descricao:</th>
						<th data-field="ResponsavelProjeto" data-footer-formatter="nameFormatter" data-sortable="true">Responsável da Ação:</th>
						{{-- 						<th data-field="Programas" data-footer-formatter="nameFormatter" data-sortable="false">Programas:</th> --}}
						<th data-field="UnidadeProjeto" data-footer-formatter="nameFormatter" data-sortable="true">Unidade:</th>
						<th data-field="Janeiro" data-footer-formatter="priceFormatter" data-sortable="true">Janeiro:</th>
						<th data-field="Fevereiro" data-footer-formatter="priceFormatter" data-sortable="true">Fevereiro:</th>
						<th data-field="Marco"  data-footer-formatter="priceFormatter" data-sortable="true">Marco:</th>
						<th data-field="Abril" data-footer-formatter="priceFormatter"  data-sortable="true">Abril:</th>
						<th data-field="Maio" data-footer-formatter="priceFormatter"  data-sortable="true">Maio:</th>
						<th data-field="Junho" data-footer-formatter="priceFormatter"  data-sortable="true">Junho:</th>
						<th data-field="Julho"  data-footer-formatter="priceFormatter" data-sortable="true">Julho:</th>
						<th data-field="Agosto"  data-footer-formatter="priceFormatter" data-sortable="true">Agosto:</th>
						<th data-field="Setembro"  data-footer-formatter="priceFormatter" data-sortable="true">Setembro:</th>
						<th data-field="Outubro"  data-footer-formatter="priceFormatter" data-sortable="true">Outubro:</th>
						<th data-field="Novembro"  data-footer-formatter="priceFormatter" data-sortable="true">Novembro:</th>
						<th data-field="Dezembro"  data-footer-formatter="priceFormatter" data-sortable="true">Dezembro:</th>
						<th data-field="total"  
						data-footer-formatter="priceFormatter"
						data-sortable="true">Total:</th>
						<th data-field="ObjetivoProjeto"  data-footer-formatter="priceFormatter" data-footer-formatter="priceFormatter" data-sortable="true">Meta:</th>
						<th 
						data-field="realizadoPerCent"
						data-footer-formatter="footerRealizado" data-sortable="true">Realizado:</th>
						{{-- <th data-field="PesoProjeto" data-footer-formatter="priceFormatter" data-sortable="true">PESO <BR>DA INTERVENÇÃO(AÇÃO)<BR>DENTRO DO<BR> PROJETO:</th> --}}

					</tr>
				</thead>
			</table> 
		</div>
	</div>
</div>
<div id="_Plano" class="col-md-12 row panel-body" style="display:none;">   
	<form id="Plano" method="post">  
		<hr class="separator" style="
		margin-top: 0px;
		margin-bottom: 8px;
		">
		<div class="row">
			<br>
		</div>
		<div class="row">

			<div class="col-md-4">
				<div class="form-group">
					<label class="control-label" for="NomePrograma" style="text-align: left;">Nome do programa:  </label>
					<input type="text" class="form-control" name="NomePrograma" >
					<input type="hidden" name="idPlano" id="idPlano" >
				</div> 
			</div>

			<div class="col-md-3">
				<div class="form-group">
					<label class="control-label" for="PesoPrograma" style="text-align: left;">Peso do programa:  </label>
					<div class="input-group" style="width: 38%;">
						<input type="number" class="form-control" name="PesoPrograma" value="0" max="100" min="0">

					</div>
				</div> 

				<div class="col-md-5">
				</div> 
			</div> 
		</div> 

		<div class="row">
			<div class="form-group">
				<div class="col-md-10">
					<label class="control-label" for="ObjetivoPrograma" style="text-align: left;">Objetivo do programa:  </label>
					<textarea class="form-control" rows="2" name="ObjetivoPrograma" id="ObjetivoPrograma"></textarea>
				</div> 
			</div> 
			<div class="col-md-2">
			</div> 
		</div> 
		<div class="row">
			<div class="form-group">
				<div class="col-md-10">
					<label class="control-label" for="Publico" style="text-align: left;">Público Alvo:  </label>
					<textarea class="form-control" rows="2" name="Publico" id="Publico"></textarea>
				</div> 
			</div> 
			<div class="col-md-2">
			</div> 
		</div> 

		<div class="row">

			<div class="col-md-6">
				<div class="form-group">
					<label class="control-label" for="IndicadorIntermediarioPrograma" style="text-align: left;">Indicador Intermediário:  </label>
					<input type="text" class="form-control" name="IndicadorIntermediarioPrograma" >
				</div> 
			</div> 

			<div class="col-md-2">
				<div class="form-group">
					<label class="control-label" for="MetaIntermediaria" style="text-align: left;">Meta:  </label>
					<div class="input-group" style="width: 55%;">
						<input type="text"  class="form-control" id="MetaIntermediaria" name="MetaIntermediaria">

					</div> 
				</div> 
			</div> 

			<div class="col-md-2">
				<div class="form-group">
					<label class="control-label" for="MetaIntermediariaDescritiva" style="text-align: left;">Descrição do indicador:  </label>
					<input type="text" class="form-control" id="MetaIntermediariaDescritiva" name="MetaIntermediariaDescritiva">
				</div> 
				<div class="col-md-2">
				</div> 
			</div> 
		</div> 
		<div class="row">

			<div class="col-md-6">
				<div class="form-group">
					<label class="control-label" for="IndicadorFinalPrograma" style="text-align: left;">Indicador Final:  </label>
					<input type="text" class="form-control" name="IndicadorFinalPrograma" >
				</div> 
			</div> 

			<div class="col-md-2">
				<div class="form-group">
					<label class="control-label" for="MetaFinal" style="text-align: left;">Meta:  </label>
					<div class="input-group" style="width: 55%;">
						<input type="text"  class="form-control" id="MetaFinal" name="MetaFinal">

					</div> 
				</div> 
			</div> 

			<div class="col-md-2">
				<div class="form-group">
					<label class="control-label" for="MetaFinalDescritiva" style="text-align: left;">Descrição do indicador:  </label>
					<input type="text" class="form-control" id="MetaFinalDescritiva" required="" name="MetaFinalDescritiva">
				</div> 
				<div class="col-md-2">
				</div> 
			</div> 
		</div> 
		<div class="row">

			<div class="col-md-8">
				<div class="form-group">
					<label class="control-label" for="CoordenadorPrograma" style="text-align: left;">Coordenador / Responsável:  </label>
					<input type="text" class="form-control" id="CoordenadorPrograma" name="CoordenadorPrograma">
				</div> 
				<div class="col-md-4">
				</div> 
			</div> 
		</div> 

		<div class="row">
			<div class="col-md-5">
				<div class="form-inline">

					<div class="col-md-12 grupo-form">
						<legend class="the-legend">Parcerias</legend>
						<div class="col-md-12">
							<div class="form-group">
								<label class="control-label" for="ParceriasPublicas" style="text-align: left;">Setor Público  :  </label>
								<input type="text"  class="form-control fixInlineForm" id="ParceriasPublicas" name="ParceriasPublicas">
							</div>
						</div>
						<div class="col-md-12">
							<div class="form-group">
								<label class="control-label" for="ParceriasPrivadas" style="text-align: left;">Setor Privado  :  </label>
								<input type="text"  class="form-control fixInlineForm" id="ParceriasPrivadas" name="ParceriasPrivadas">
							</div>
						</div>
						<div class="col-md-12">       
							<div class="form-group">
								<label class="control-label" for="ParceriasCivil" style="text-align: left;">Sociedade Civil:  </label>
								<input type="text"  class="form-control fixInlineForm" id="ParceriasCivil" name="ParceriasCivil">
							</div>
						</div>
					</div>

				</div> 
			</div> 

			<div class="col-md-7">
				<div class="form-group">
					<legend class="the-legend">Secretarias Envolvidas: </legend>
					<textarea class="form-control fixInlineForm" rows="5" name="SecretariasEnvolvidas" id="SecretariasEnvolvidas"></textarea>
				</div> 
			</div> 
		</div> 


		<button type='submit' id='salvar' class="btn btn-default pull-right">Salvar</button>
	</form>
	<br>
</div> 
<div id="_Projeto" class="col-md-12 row panel-body" style="display:none;">   
	<form id="Projeto" method="post">  
		<hr class="separator" style="
		margin-top: 0px;
		margin-bottom: 8px;
		">
		<div class="row">
			<br>
		</div>
		<div class="row">

			<div class="col-md-4">
				<div class="form-group">
					<label class="control-label" for="NomeProjeto" style="text-align: left;">Nome da intervenção (ação):  </label>
					<input type="text" class="form-control" name="NomeProjeto" >
					<input type="hidden" name="idProjeto" id="idProjeto" >
				</div> 
			</div>            
			<div class="col-md-4">
				<div class="form-group">
					<label class="control-label" for="ResponsavelProjeto" style="text-align: left;">Responsável:  </label>
					<input type="text" class="form-control" name="ResponsavelProjeto" >
				</div> 
			</div>         
			<div class="col-md-4">
				<div class="form-group">
					<label class="control-label" for="TipoProjeto" style="text-align: left;">Projeto:  </label>
					<select class="form-control" name="TipoProjeto" id="TipoProjeto">
						<option selected></option>
						<option value="EDUCAÇÃO">EDUCAÇÃO</option>
						<option value="ENGENHARIA">ENGENHARIA</option>
						<option value="FISCALIZAÇÃO">FISCALIZAÇÃO</option>
						<option value="PROJETOS ESPECIAIS">PROJETOS ESPECIAIS</option>
						<option value="SDMC">SDMC</option>
					</select>
				</div> 
			</div>
		</div>
		<div class="row">
			<div class="col-md-4">
				<div class="form-group">
					<label class="control-label" for="UnidadeProjeto" style="text-align: left;">Unidade de Medida da Meta:  </label>

					<input type="text" class="form-control" name="UnidadeProjeto">

				</div> 


			</div> 
			<div class="col-md-4">
				<div class="form-group">
					<label class="control-label" for="ObjetivoProjeto" style="text-align: left;">Quantificação da Meta:  </label>
					<input type="number" class="form-control" name="ObjetivoProjeto" min="0" step="0.1">
				</div> 

			</div>
			<div class="col-md-4" style="display: none">
				{{-- alteração pedida pelo professor otaliba --}}
				<div class="form-group">
					<label class="control-label" for="PesoProjeto" style="text-align: left;">Peso da intervenção(ação) dentro do Projeto:  </label>

					<input type="number" value="0" class="form-control" name="PesoProjeto"  id="PesoProjeto" value="0" max="100" min="0">

				</div> 

			</div> 
			<div class="col-md-4">
				<div class="form-group">
					<label class="control-label" for="CustoProjeto" style="text-align: left;">Custo Estimado (R$):</label>

					<input type="number" class="form-control" name="CustoProjeto" min="0" step="0.1">

				</div> 

			</div> 
		</div> 

		<div class="row">
			<div class="form-group">
				<div class="col-md-8">
					<label class="control-label" for="DescricaoProjeto" style="text-align: left;">Descrição da intervenção (ação):  </label>
					<textarea class="form-control" rows="2" name="DescricaoProjeto" id="DescricaoProjeto"></textarea>
				</div> 
			</div> 

		</div> 
		<div class="row panel-body">
			<div class="form-group">
				<div class="col-md-2">
					<label class="control-label" for="Janeiro" style="text-align: left;">Janeiro:  </label>
					<input type="number" class="form-control" name="Janeiro" min="0" step="0.1">
				</div> 
				<div class="col-md-2">
					<label class="control-label" for="Fevereiro" style="text-align: left;">Fevereiro:  </label>
					<input type="number" class="form-control" name="Fevereiro" min="0" step="0.1">
				</div>
				<div class="col-md-2">
					<label class="control-label" for="Marco" style="text-align: left;">Março:  </label>
					<input type="number" class="form-control" name="Marco" min="0" step="0.1">
				</div> 
				<div class="col-md-2">
					<label class="control-label" for="Abril" style="text-align: left;">Abril:  </label>
					<input type="number" class="form-control" name="Abril" min="0" step="0.1">
				</div> 
				<div class="col-md-2">
					<label class="control-label" for="Maio" style="text-align: left;">Maio:  </label>
					<input type="number" class="form-control" name="Maio" min="0" step="0.1">
				</div> 
				<div class="col-md-2">
					<label class="control-label" for="Junho" style="text-align: left;">Junho:  </label>
					<input type="number" class="form-control" name="Junho" min="0" step="0.1">
				</div> 
				<div class="col-md-2">
					<label class="control-label" for="Julho" style="text-align: left;">Julho:  </label>
					<input type="number" class="form-control" name="Julho" min="0" step="0.1">
				</div> 
				<div class="col-md-2">
					<label class="control-label" for="Agosto" style="text-align: left;">Agosto:  </label>
					<input type="number" class="form-control" name="Agosto" min="0" step="0.1">
				</div> 
				<div class="col-md-2">
					<label class="control-label" for="Setembro" style="text-align: left;">Setembro:  </label>
					<input type="number" class="form-control" name="Setembro" min="0" step="0.1">
				</div> 
				<div class="col-md-2">
					<label class="control-label" for="Outubro" style="text-align: left;">Outubro:  </label>
					<input type="number" class="form-control" name="Outubro" min="0" step="0.1">
				</div> 
				<div class="col-md-2">
					<label class="control-label" for="Novembro" style="text-align: left;">Novembro:  </label>
					<input type="number" class="form-control" name="Novembro" min="0" step="0.1">
				</div> 
				<div class="col-md-2">
					<label class="control-label" for="Dezembro" style="text-align: left;">Dezembro:  </label>
					<input type="number" class="form-control" name="Dezembro" min="0" step="0.1">
				</div> 
			</div> 
		</div> 
		<div class="row ProjetosPrograma">
			<div class="col-md-4" id="_ProjetoPrograma">
				<div class="form-group">
					<label class="control-label" for="ProjetoPrograma" style="text-align: left;">Programa:  </label>
					<select class="form-control ProjetoPrograma_" name="ProjetoPrograma[]" id="ProjetoPrograma">
					</select>
				</div> 
			</div>
			<div class="col-md-3" id="_PesoPrograma">
				<div class="form-group">
					<label class="control-label" for="PesoPrograma" style="text-align: left;">Peso da interveção(ação) no programa:  </label>
					<input type="number" class="form-control PesoPrograma_" name="PesoPrograma[]" value="0" max="100" min="0">
				</div> 
			</div>
			<div class="col-md-2 form-group">
				<a href="#" class="btn btn-default addPrograma" style="
				margin-top: 23px;
				">Adicionar Outro Programa</a>
			</div>
		</div>
		<button type='submit' id='salvarProjeto' class="btn btn-default pull-right">Salvar intervenção(ação)</button>
	</form>
	<br>
</div> 

{{-- resultados --}}
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
<script src="{{asset('libraries/js/formValidation.min.js')}}" type="text/javascript"></script>
<script src="{{asset('libraries/js/form-bootstrap.min.js')}}" type="text/javascript"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/bootstrap-table/1.11.0/bootstrap-table.min.js"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/bootstrap-table/1.11.0/locale/bootstrap-table-pt-BR.min.js"></script>
<script src="{{asset('libraries/js/bootstrap-table-fixed-columns.js')}}" type="text/javascript"></script> 
<script src="{{asset('libraries/js/alertify.min.js')}}"></script> 
<script src="{{asset('libraries/js/bootstrap-table-fixed-columns.js')}}" type="text/javascript"></script> 
<script type="text/javascript">
//override defaults
alertify.defaults.transition = "slide";
alertify.defaults.theme.ok = "btn btn-primary";
alertify.defaults.theme.cancel = "btn btn-danger";
alertify.defaults.theme.input = "form-control";
</script>
<script type="text/javascript">
	
//arquivo js plano de acao - ultima alteração 30/09/2019
//Guilherme Freire
var pesoTotal = 0;
function replaceSpecialChars(str) {
	var conversions = new Object();
	conversions['ae'] = 'ä|æ|ǽ';
	conversions['oe'] = 'ö|œ';
	conversions['ue'] = 'ü';
	conversions['Ae'] = 'Ä';
	conversions['Ue'] = 'Ü';
	conversions['Oe'] = 'Ö';
	conversions['A'] = 'À|Á|Â|Ã|Ä|Å|Ǻ|Ā|Ă|Ą|Ǎ';
	conversions['a'] = 'à|á|â|ã|å|ǻ|ā|ă|ą|ǎ|ª';
	conversions['C'] = 'Ç|Ć|Ĉ|Ċ|Č';
	conversions['c'] = 'ç|ć|ĉ|ċ|č';
	conversions['D'] = 'Ð|Ď|Đ';
	conversions['d'] = 'ð|ď|đ';
	conversions['E'] = 'È|É|Ê|Ë|Ē|Ĕ|Ė|Ę|Ě';
	conversions['e'] = 'è|é|ê|ë|ē|ĕ|ė|ę|ě';
	conversions['G'] = 'Ĝ|Ğ|Ġ|Ģ';
	conversions['g'] = 'ĝ|ğ|ġ|ģ';
	conversions['H'] = 'Ĥ|Ħ';
	conversions['h'] = 'ĥ|ħ';
	conversions['I'] = 'Ì|Í|Î|Ï|Ĩ|Ī|Ĭ|Ǐ|Į|İ';
	conversions['i'] = 'ì|í|î|ï|ĩ|ī|ĭ|ǐ|į|ı';
	conversions['J'] = 'Ĵ';
	conversions['j'] = 'ĵ';
	conversions['K'] = 'Ķ';
	conversions['k'] = 'ķ';
	conversions['L'] = 'Ĺ|Ļ|Ľ|Ŀ|Ł';
	conversions['l'] = 'ĺ|ļ|ľ|ŀ|ł';
	conversions['N'] = 'Ñ|Ń|Ņ|Ň';
	conversions['n'] = 'ñ|ń|ņ|ň|ŉ';
	conversions['O'] = 'Ò|Ó|Ô|Õ|Ō|Ŏ|Ǒ|Ő|Ơ|Ø|Ǿ';
	conversions['o'] = 'ò|ó|ô|õ|ō|ŏ|ǒ|ő|ơ|ø|ǿ|º';
	conversions['R'] = 'Ŕ|Ŗ|Ř';
	conversions['r'] = 'ŕ|ŗ|ř';
	conversions['S'] = 'Ś|Ŝ|Ş|Š';
	conversions['s'] = 'ś|ŝ|ş|š|ſ';
	conversions['T'] = 'Ţ|Ť|Ŧ';
	conversions['t'] = 'ţ|ť|ŧ';
	conversions['U'] = 'Ù|Ú|Û|Ũ|Ū|Ŭ|Ů|Ű|Ų|Ư|Ǔ|Ǖ|Ǘ|Ǚ|Ǜ';
	conversions['u'] = 'ù|ú|û|ũ|ū|ŭ|ů|ű|ų|ư|ǔ|ǖ|ǘ|ǚ|ǜ';
	conversions['Y'] = 'Ý|Ÿ|Ŷ';
	conversions['y'] = 'ý|ÿ|ŷ';
	conversions['W'] = 'Ŵ';
	conversions['w'] = 'ŵ';
	conversions['Z'] = 'Ź|Ż|Ž';
	conversions['z'] = 'ź|ż|ž';
	conversions['AE'] = 'Æ|Ǽ';
	conversions['ss'] = 'ß';
	conversions['IJ'] = 'Ĳ';
	conversions['ij'] = 'ĳ';
	conversions['OE'] = 'Œ';
	conversions['f'] = 'ƒ';
	for (var i in conversions) {
		var re = new RegExp(conversions[i], "g");
		str = str.replace(re, i);
	}
	return str;
}

function projeto(projeto = null){

	var qtdProgramas = $('#table').bootstrapTable('getData').length;

	if(qtdProgramas > 0){
		resetAllValues();
		$.ajax({
			type: "GET",
			url: '{{ route('plano.BuscaProgramas') }}?CodCidade='+$('#CodCidade').val()+'&Ano='+$('#Ano').val(),
			headers: {
				'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
			},
			dataType: 'json',
			contentType: "application/json; charset=utf-8",
			success: function (obj) {
				if (obj != null) {
					var selectbox = $("#ProjetoPrograma");
					selectbox.find('option').remove();
					$.each(obj, function (i, d) {
						$('<option>').val(d.id).text(d.NomePrograma).appendTo(selectbox);
					});

				}
			}
		});
		$('#_Projeto').slideDown();
		$("html, body").animate({ scrollTop: $(document).height()-$(window).height() });
	}else{
		alertify.alert('Alerta','Você deve inserir um programa primeiro'); 
	}


};


function editar(id) {
	$('#HiddenEdit').remove();
	$('#adicionarPlano').show();
	resetAllValues();
	$('#idPlano').val(id);
	$.ajax(
	{
		url: '{{ route('plano.BuscaProgramas') }}',
		type: "POST",
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		},
		data: {id: id, ajax: 'BuscaEditar', CodCidade: $('#CodCidade').val(), Ano: $('#Ano').val()},
		success: function (data, textStatus, jqXHR)
		{
			console.log(data)
			if(typeof(data.error) == 'undefined'){
				var valores = data;
				$("input[name=NomePrograma]").val(valores.NomePrograma);
				$("input[name=PesoPrograma]").val(valores.PesoPrograma);
				$("textarea[name=ObjetivoPrograma]").val(valores.ObjetivoPrograma);
				$("textarea[name=Publico]").val(valores.Publico);
				$("input[name=IndicadorIntermediarioPrograma]").val(valores.IndicadorIntermediarioPrograma);
				$("input[name=MetaIntermediaria]").val(valores.MetaIntermediaria);
				$("input[name=MetaIntermediariaDescritiva]").val(valores.MetaIntermediariaDescritiva);
				$("input[name=IndicadorFinalPrograma]").val(valores.IndicadorFinalPrograma);
				$("input[name=MetaFinal]").val(valores.MetaFinal);
				$("input[name=MetaFinalDescritiva]").val(valores.MetaFinalDescritiva);
				$("input[name=CoordenadorPrograma]").val(valores.CoordenadorPrograma);
				$("input[name=ParceriasPublicas]").val(valores.ParceriasPublicas);
				$("input[name=ParceriasPrivadas]").val(valores.ParceriasPrivadas);
				$("input[name=ParceriasCivil]").val(valores.ParceriasCivil);
				$("textarea[name=SecretariasEnvolvidas]").val(valores.SecretariasEnvolvidas);
				$("#_Plano").show();
				$("html, body").animate({ scrollTop: $(document).height()-$(window).height() });


			}else{
				alertify.success('Erro ao buscar dados' + data.error);
			}
		},
		error: function (jqXHR, textStatus, errorThrown)
		{
			if(jqXHR.status == 419){
				location.reload();
			}
			alertify.success('Erro ao buscar dados');
		}
	});

}
function editarProjeto(id) {
	resetAllValues();
	$('#idProjeto').val(id);
	$.ajax(
	{
		url: '{{ route('plano.BuscaProjetos') }}',
		data: {
			idProjeto: id,
			CodCidade: $('#CodCidade').val(), 
			Ano: $('#Ano').val()
		},
		type: "POST",
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		},
		dataType: 'json',
		success: function (data, textStatus, jqXHR)
		{
			$.ajax({
				type: "GET",
				url: '{{ route('plano.BuscaProgramas') }}?CodCidade='+$('#CodCidade').val()+'&Ano='+$('#Ano').val(),
				dataType: 'json',
				headers: {
					'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
				},
				async: false,
				global: false,
				contentType: "application/json; charset=utf-8",
				success: function (obj) {
					if (obj != null) {
						var selectbox = $("#ProjetoPrograma");
						selectbox.find('option').remove();
						$('<option>').val('').text('').appendTo(selectbox);
						$.each(obj, function (i, d) {
							$('<option>').val(d.id).text(d.NomePrograma).appendTo(selectbox);
						});

					}
				}
			});
			console.log(data)
			var valores = data
			$("input[name=NomeProjeto]").val(valores.NomeProjeto);
			$("input[name=ResponsavelProjeto]").val(valores.ResponsavelProjeto);
			$("input[name=CustoProjeto]").val(valores.CustoProjeto);
			$("input[name=UnidadeProjeto]").val(valores.UnidadeProjeto);
			$("input[name=ObjetivoProjeto]").val(valores.ObjetivoProjeto);
			$("input[name=PesoProjeto]").val(valores.PesoProjeto);
			$("#TipoProjeto").val(valores.TipoProjeto);
			console.log(valores.TipoProjeto);
			$("textarea[name=DescricaoProjeto]").val(valores.DescricaoProjeto);

			$("input[name=Janeiro]").val(valores.Janeiro);
			$("input[name=Fevereiro]").val(valores.Fevereiro);
			$("input[name=Marco]").val(valores.Marco);
			$("input[name=Abril]").val(valores.Abril);
			$("input[name=Maio]").val(valores.Maio);
			$("input[name=Junho]").val(valores.Junho);
			$("input[name=Julho]").val(valores.Julho);
			$("input[name=Agosto]").val(valores.Agosto);
			$("input[name=Setembro]").val(valores.Setembro);
			$("input[name=Outubro]").val(valores.Outubro);
			$("input[name=Novembro]").val(valores.Novembro);
			$("input[name=Dezembro]").val(valores.Dezembro);

			if(valores.planos != null){
				var programas = valores.planos;
				if(programas.length == 1){
					$("select[name='ProjetoPrograma[]']").last().val(programas[0].id);
					$("input[name='PesoPrograma[]']").last().val(programas[0].pivot.PesoPlano);
				}else{
					console.log(programas)
					$.each(programas, function(i, item) {
						console.log(i,item)
						if(i == 0){
							$("select[name='ProjetoPrograma[]']").last().val(item.id);
							$("input[name='PesoPrograma[]']").last().val(programas[i].PesoPlano);
						}else{
							$('.addPrograma').trigger('click')
							$("select[name='ProjetoPrograma[]']").last().val(item.id);
							$("input[name='PesoPrograma[]']").last().val(programas[i].PesoPlano);
						}
					}) 
				}

			}


			$("#_Projeto").show();
			$("html, body").animate({ scrollTop: $(document).height()-$(window).height() });

		},
		error: function (jqXHR, textStatus, errorThrown)
		{
			if(jqXHR.status == 419){
				location.reload();
			}
			alertify.success('Erro ao buscar dados');
		}
	});

}

function removePrograma(id) {
	$.ajax(
	{
		url: '{{ route('plano.remover') }}',
		type: "POST",
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		},
		data:{
			idPrograma: id, 
			CodCidade: $('#CodCidade').val(), 
			Ano: $('#Ano').val()
		},
		success: function (data, textStatus, jqXHR)
		{
			resetAllValues();
			$('#table').bootstrapTable('removeAll');
			$('#table').bootstrapTable('refresh', {url: '{{ route('plano.BuscaProgramas') }}?CodCidade='+$('#CodCidade').val()+'&Ano='+$('#Ano').val()});
			$('#tableProjetos').bootstrapTable('removeAll');
			$('#tableProjetos').bootstrapTable('refresh', {url: '{{ route('plano.BuscaProjetos') }}?CodCidade='+$('#CodCidade').val()+'&Ano='+$('#Ano').val()});

		},
		error: function (jqXHR, textStatus, errorThrown)
		{
			if(jqXHR.status == 419){
				location.reload();
			}
			console.log(jqXHR, textStatus, errorThrown)
			alertify.success('Erro ao buscar dados');
		}
	});

}


function removeProjeto(id) {
	$.ajax(
	{
		url: '{{ route('projeto.remover') }}',
		type: "POST",
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		},
		data:{
			idProjeto: id, 
			CodCidade: $('#CodCidade').val(), 
			Ano: $('#Ano').val()
		},
		success: function (data, textStatus, jqXHR)
		{
			resetAllValues();
			$('#table').bootstrapTable('removeAll');
			$('#table').bootstrapTable('refresh', {url: '{{ route('plano.BuscaProgramas') }}?CodCidade='+$('#CodCidade').val()+'&Ano='+$('#Ano').val()});
			$('#tableProjetos').bootstrapTable('removeAll');
			$('#tableProjetos').bootstrapTable('refresh', {url: '{{ route('plano.BuscaProjetos') }}?CodCidade='+$('#CodCidade').val()+'&Ano='+$('#Ano').val()});

		},
		error: function (jqXHR, textStatus, errorThrown)
		{
			if(jqXHR.status == 419){
				location.reload();
			}
			console.log(jqXHR, textStatus, errorThrown)
			alertify.success('Erro ao buscar dados');
		}
	});

}



function operateFormatter(value, row, index) {
	return '<button type="button" class="btn btn-small btn-primary" onClick="editar(' + value + ');"><span class="glyphicon glyphicon-edit"></span> Editar</button><button type="button" data-toggle="tooltip" title="Remover Intervenção" class="btn btn-small btn-primary" onClick="removePrograma(' + value + ');"><span class="glyphicon glyphicon-minus"></span> Remover</button>';
}
function editProjeto(value, row, index) {
	return '<button type="button" class="btn btn-small btn-primary" data-toggle="tooltip" title="Editar Intervenção" onClick="editarProjeto(' + value + ');"><span class="glyphicon glyphicon-edit"></span></button><button type="button" data-toggle="tooltip" title="Remover Projeto" class="btn btn-small btn-primary" onClick="removeProjeto(' + value + ');"><span class="glyphicon glyphicon-minus"></span></button>';
}
function queryParams(params) {
    //console.log(this);
    return {
    	Ano: $('#Ano').val(),
    	CodCidade: $('#CodCidade').val(),

    };
}
function priceFormatter(data) {
	var field = this.field
	//console.log(data,field)

	var total = '' + data.map(function (row) {
		//console.log(row)
		return +row[field]
	}).reduce(function (sum, i) {
		return sum + i
	}, 0)
	if(field = 'PesoProjeto'){
		pesoTotal = total
	}
	return total
}
function idFormatter() {
	return 'Total'
}
function footerSomaMeses(data){
	//console.log(data)
	var TotalGeral = 0;
	$.each(data, function (index, value) {
		console.log(value)
		TotalGeral = parseFloat(TotalGeral) 
		+parseFloat(value.Janeiro)
		+parseFloat(value.Fevereiro)
		+parseFloat(value.Marco)
		+parseFloat(value.Abril)
		+parseFloat(value.Maio)
		+parseFloat(value.Junho)
		+parseFloat(value.Julho)
		+parseFloat(value.Agosto)
		+parseFloat(value.Setembro)
		+parseFloat(value.Outubro)
		+parseFloat(value.Novembro)
		+parseFloat(value.Dezembro);
	});
	return parseFloat(TotalGeral).toFixed(2);
}
function somaMeses(value, row, index){
	//console.log(value, row, index)
	var item = row;
	return item.Janeiro+item.Fevereiro+item.Marco+item.Abril+item.Maio+item.Junho+item.Julho+item.Agosto+item.Setembro+item.Outubro+item.Novembro+item.Dezembro
}
function realiz(value, row, index){
	console.log(value, row, index)
	var item = row;
	return item.relizado+'%'
}
function footerRealizado(data){
	var realizadoTotal = 0.0;
	$.each(data, function (index, value) {
		var item = value
		var total = item.total;
		var realizado = item.realizado
		realizadoTotal = parseFloat(realizadoTotal) + parseFloat(realizado)

	});
	return realizadoTotal.toFixed(2)+'%';
}
function nameFormatter(data) {
	return ''
}
function detailFormatter(index, row) {
	var html = [];
	$.each(row, function (key, value) {
		html.push('<p><b>' + key + ':</b> ' + value + '</p>');
	});
	return html.join('');
}
$('#Ano').change(function () {
	resetAllValues()
	if ($('#Ano').val() < 2015) {
		$('#Ano').val('');
		$('#Ano').focus();
	} else {
		if ($('#Ano').val() !== '' && $('#CodCidade').val() !== '') {
			$('#titulo-painel').html('Planos de Ações do período ' + $('#Ano').val() );
			$('#adicionarPlano').show();
			$('#table').bootstrapTable('removeAll');
			$('#table').bootstrapTable('refresh', {url: '{{ route('plano.BuscaProgramas') }}?CodCidade='+$('#CodCidade').val()+'&Ano='+$('#Ano').val()});
			$('#tableProjetos').bootstrapTable('removeAll');
			$('#tableProjetos').bootstrapTable('refresh', {url: '{{ route('plano.BuscaProjetos') }}?CodCidade='+$('#CodCidade').val()+'&Ano='+$('#Ano').val()});
			$('#_tabelaResults').show();
		} else {
			$('#_tabelaResults').hide();
		}
	}

});


$('#Cidade').change(function () {
	resetAllValues()
	$.ajax(
	{
		url: '{{ route('getCidades') }}',
		type: "POST",
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		},
		data: {Cidade: this.value , Estado: $('#Estado').val()},
		success: function (data, textStatus, jqXHR)
		{
			$("#CodCidade").val(data);
			if ($('#Ano').val() !== '' && $('#CodCidade').val() !== '') {
				$('#titulo-painel').html('Planos de Ações do período ' + $('#Ano').val());
				$('#adicionarPlano').show();
				$('#table').bootstrapTable('removeAll');
				$('#table').bootstrapTable('refresh', {url: '{{ route('plano.BuscaProgramas') }}?CodCidade='+$('#CodCidade').val()+'&Ano='+$('#Ano').val()});
				$('#tableProjetos').bootstrapTable('removeAll');
				$('#tableProjetos').bootstrapTable('refresh', {url: '{{ route('plano.BuscaProjetos') }}?CodCidade='+$('#CodCidade').val()+'&Ano='+$('#Ano').val()});
				$('#_tabelaResults').show();
			} else {
				$('#_tabelaResults').hide();
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

});
function resetAllValues() {
	$('#_Plano div[id^="_"]').hide();
	$('#_Plano').hide();
	$('#_Projeto').hide();
	$('#ObjetivoPrograma').val('');
	$('#Publico').val('');
	$('#SecretariasEnvolvidas').val('');

	$('#_Plano input').val('');
	$('#_Plano').find('input').val('');
	$('#_Projeto input').val('');
	$('#_Projeto textarea').val('');
	$('#_Projeto').find('input').val('');


	$('#_Plano').find('.has-success').removeClass('has-success')
	$('#_Plano').find('.has-error').removeClass('has-error')
	$('#_Plano').find('.help-block').hide()
	$('#_Plano').find('.fv-icon-no-label').hide()

	$('#_Projeto').find('.has-success').removeClass('has-success')
	$('#_Projeto').find('.has-error').removeClass('has-error')
	$('#_Projeto').find('.help-block').hide()
	$('#_Projeto').find('.fv-icon-no-label').hide()

	$('#table').bootstrapTable('collapseAllRows');

	$('#tableProjetos').bootstrapTable('collapseAllRows');
	$('#Projeto').data('formValidation').resetForm($('#Projeto'));
	$('#Plano').data('formValidation').resetForm($('#Plano'));


}
$('#adicionarPlano').click(function () {
	resetAllValues();
	$('#_Plano').slideDown();
	$("html, body").animate({ scrollTop: $(document).height()-$(window).height() });
});



$('#table').on('load-success.bs.table', function (data) {
	var data = $('#table').bootstrapTable('getData');
	var totalPesoPrograma = 0;
	$.each(data, function (i, d) {
		totalPesoPrograma = totalPesoPrograma + parseInt(d.PesoPrograma);
	});
	if(totalPesoPrograma < 100){
		$("#table tbody tr td:nth-child(4)").addClass('red')
	}else{
		$("#table tbody tr td:nth-child(4)").removeClass('red')
	}

});
$('#table').on('expand-row.bs.table', function (e, index, row, $detail) {
	//console.log(e, index, row, $detail)
	$detail.html('Carregando dados...');
	$.ajax({
		url: '{{ route('plano.BuscaProjetos') }}',
		type: 'POST',
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		},
		data: {
			idPlano: row.id,
			CodCidade: $('#CodCidade').val(), 
			Ano: $('#Ano').val()
		},
		dataType: 'json',
		success: function (data) {
			console.log(data)
			var count = Object.keys(data).length;
			if (count > 0){
				$detail.html("<table class='table table-responsive' id='records_table_"+row.id+"' border='1'>"+
					"<tr class='titlerow'>"+
					"<th>Projeto</th>"+
					"<th>Interveção<BR>(Ação)</th>"+
					"<th>Responsavel</th>"+
					"<th>Unidade</th>"+
					"<th>Jan</th>"+
					"<th>Fev</th>"+
					"<th>Mar</th>"+
					"<th>Abr</th>"+
					"<th>Mai</th>"+
					"<th>Jun</th>"+
					"<th>Jul</th>"+
					"<th>Ago</th>"+
					"<th>Set</th>"+
					"<th>Out</th>"+
					"<th>Nov</th>"+
					"<th>Dez</th>"+
					"<th>Total</th>"+
					"<th>Meta</th>"+
					"<th>Realizado</th>"+
					"<th>Peso da interveção(ação)<BR>no Programa</th>"+
					"<th>% Realizado<BR>Ponderado</th>"+
					"</tr>"+
					"</table>");
				$.each(data, function(i, item) {

					var $tr = $('<tr>').append(
						$('<td>').text(item.TipoProjeto),
						$('<td>').text(item.NomeProjeto),
						$('<td>').text(item.ResponsavelProjeto),
						$('<td>').text(item.UnidadeProjeto),
						$('<td class="sum">').text(item.Janeiro),
						$('<td class="sum">').text(item.Fevereiro),
						$('<td class="sum">').text(item.Marco),
						$('<td class="sum">').text(item.Abril),
						$('<td class="sum">').text(item.Maio),
						$('<td class="sum">').text(item.Junho),
						$('<td class="sum">').text(item.Julho),
						$('<td class="sum">').text(item.Agosto),
						$('<td class="sum">').text(item.Setembro),
						$('<td class="sum">').text(item.Outubro),
						$('<td class="sum">').text(item.Novembro),
						$('<td class="sum">').text(item.Dezembro),
						$('<td class="sum">').text(item.total),
						$('<td class="sum">').text(item.ObjetivoProjeto),
						$('<td>').text(item.realizado+'%'),
						$('<td class="sum">').text(item.PesoPrograma),
						$('<td class="sum">').text(((item.PesoPrograma*item.realizado)/100).toFixed(2)),
						$('</tr>')
						).appendTo('#records_table_'+row.id);
				});
				var $tr = $('<tr class="totalColumn">').append(
					$('<td  colspan="4">').text(''),
					$('<td class="totalCol">').text(''),
					$('<td class="totalCol">').text(''),
					$('<td class="totalCol">').text(''),
					$('<td class="totalCol">').text(''),
					$('<td class="totalCol">').text(''),
					$('<td class="totalCol">').text(''),
					$('<td class="totalCol">').text(''),
					$('<td class="totalCol">').text(''),
					$('<td class="totalCol">').text(''),
					$('<td class="totalCol">').text(''),
					$('<td class="totalCol">').text(''),
					$('<td class="totalCol">').text(''),
					$('<td class="totalCol">').text(''),
					$('<td class="totalCol">').text(''),
					$('<td>').text(''),
					$('<td class="totalCol" id="PesosIntervencao_'+row.id+'">').text(''),
					$('<td>').text(''),
					$('</tr>')
					).appendTo('#records_table_'+row.id);
				var totals=[0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0];
				var $dataRows=$("#records_table_"+row.id+" tr:not('.totalColumn, .titlerow')");
				$dataRows.each(function() {
					$(this).find('.sum').each(function(i){        
						totals[i]+=parseInt( $(this).html());
					});
				});
				$("#records_table_"+row.id+" td.totalCol").each(function(i){  
					$(this).html("total:"+parseFloat(totals[i]).toFixed(2));
				});
				var totalPesos = $('#PesosIntervencao_'+row.id).html().replace("total:",'');
				if(parseInt(!s) < 100){
					//console.log('sda', totalPesos)
					$("#records_table_"+row.id+" tbody tr td:nth-last-child(2)").addClass('red')
				}else{
					$("#records_table_"+row.id+" tbody tr td:nth-last-child(2)").removeClass('red')
				}
			}else{
				$detail.html('Sem Dados para exibir...');
			}
		},error: function(jqXHR, textStatus, errorThrown) {
			if(jqXHR.status == 419){
				location.reload();
			}
			alertify.success('Erro ao buscar dados');
		}
	});
});


function deleteRow(id) {
    //console.log(id)
    $("#" + id).remove();
}
function calculaCumprimento() {

}

$(document).ready(function () {
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
				},
				error: function (jqXHR, textStatus, errorThrown) {
					if(jqXHR.status == 419){
						location.reload();
					}
					console.log("erro");
				}
			});
		});
		@endif
		@if(Auth::user()->tipo >= 3)
		$("#CodCidade").val('{{Auth::user()->CodCidade}}');
		@endif

	});
	$('.fixed-table-body').scroll(function(){
		$('.fixed-table-footer').scrollLeft($(this).scrollLeft());
	});
	$("#AnoPrograma").val((new Date).getFullYear() - 1);
	var dt = new Date();
	$('#Plano').formValidation({
		framework: 'bootstrap',
		err: {
			container: 'tooltip'
		},
		excluded: [':disabled'],
		fields: {
			'NomePrograma': {
				validators: {
					notEmpty: {
						message: 'Campo Obrigatório'
					}
				}
			},
			'PesoPrograma': {
				validators: {
					notEmpty: {
						message: 'Campo Obrigatório'
					}
				}
			},
			'ObjetivoPrograma': {
				validators: {
					notEmpty: {
						message: 'Campo Obrigatório'
					}
				}
			},
			'IndicadorIntermediarioPrograma': {
				validators: {
					notEmpty: {
						message: 'Campo Obrigatório'
					}
				}
			},
			'MetaIntermediariaDescritiva': {
				validators: {
					notEmpty: {
						message: 'Campo Obrigatório'
					}
				}
			},
			'CoordenadorPrograma': {
				validators: {
					notEmpty: {
						message: 'Campo Obrigatório'
					}
				}
			},
			'MetaIntermediaria': {
				validators: {
					notEmpty: {
						message: 'Campo Obrigatório'
					},
					between: {
						min: 0,
						max: 100,
						message: 'Insira um valor válido para meta'
					}
				}
			},
			'IndicadorFinalPrograma': {
				validators: {
					notEmpty: {
						message: 'Campo Obrigatório'
					}
				}
			},
			'MetaFinalDescritiva': {
				validators: {
					notEmpty: {
						message: 'Campo Obrigatório'
					}
				}
			},
			'MetaFinal': {
				validators: {
					notEmpty: {
						message: 'Campo Obrigatório'
					},
					between: {
						min: 0,
						max: 100,
						message: 'Insira um valor válido para meta'
					}
				}
			},
			'ParceriasPublicas': {
				enabled: false,
				validators: {
					notEmpty: {
						message: 'Campo Obrigatório'
					}
				}
			},
			'ParceriasPrivadas': {
				enabled: false,
				validators: {
					notEmpty: {
						message: 'Campo Obrigatório'
					}
				}
			},
			'Ano': {
				enabled: false,
				validators: {
					notEmpty: {
						message: 'Campo Obrigatório'
					}
				}
			},
			'ParceriasCivil': {
				enabled: false,
				validators: {
					notEmpty: {
						message: 'Campo Obrigatório'
					}
				}
			},
			'SecretariasEnvolvidas': {
				enabled: false,
				validators: {
					notEmpty: {
						message: 'Campo Obrigatório'
					}
				}
			},

		}


	})
	.on('success.field.fv', function (e, data) {
                // data.fv      --> The FormValidation instance
                // data.element --> The field element
                // Check if all fields in tab are valid

            })
	.on('success.form.fv', function (e, data) {

                // Prevent form submission programa
                e.preventDefault();
                var validacao = true;
                var data = $('#table').bootstrapTable('getData');
                var totalPesoPrograma = 0;
                if($("input[name='PesoPrograma']").val() == ''){
                	alertify.alert('Você deve inserir o peso do programa'); 
                	return false;
                }
                if($('#idPlano').val() == ''){
                	$.each(data, function (i, d) {
                		totalPesoPrograma = totalPesoPrograma + parseInt(d.PesoPrograma);
                	});
                	totalPesoPrograma = totalPesoPrograma + parseInt($("input[name='PesoPrograma']").val());
                }else{
                	$.each(data, function (i, d) {
                		console.log('dsa',d.PesoPrograma)
                		if($('#idPlano').val() == d.id){
                			totalPesoPrograma = totalPesoPrograma + parseInt($("input[name='PesoPrograma[]']").val());
                		}else{
                			totalPesoPrograma = totalPesoPrograma + parseInt(d.PesoPrograma);
                		}
                		
                	});
                }
                if($("input[name='PesoPrograma']").val() != '' ){
                	$.ajax({
                		url: '{{ route('plano.PesoTotal') }}',
                		type: "POST",
                		headers: {
                			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                		},
                		data:{
                			pesoPrograma: $("input[name='PesoPrograma']").val(), 
                			Ano: $("#Ano").val(), 
                			CodCidade: $("#CodCidade").val(), 
                			idPlano: $("#idPlano").val(), 
                		},
                		async: false,
                		global: false,
                		success: function (result) {
                			console.log(result, 'aqui')
                			totalPesoPrograma = result;
                		}
                	});
                }
                console.log(totalPesoPrograma)
                if(totalPesoPrograma > 100){
                	var validacao = false;
                	alertify.alert('Total de pesos do programa não pode ser maior que 100'); 
                	return false;
                }


                var $form = $(e.target),
                fv = $form.data('formValidation');

                $('input[type=text]').val(function () {
                	return replaceSpecialChars(this.value);
                })

                $('input:disabled, select:disabled').each(function () {
                	$(this).removeAttr('disabled');
                });

                if (validacao == true){
                // Use Ajax to submit form data
                $.ajax({
                	url: '{{ route('plano.gravar') }}',
                	type: 'POST',
                	data: $form.serialize()+ '&CodCidade=' +$("#CodCidade").val()+ '&Ano=' +$("#Ano").val(),
                	headers: {
                		'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                	},
                	success: function (result) {
                		console.log(result)
                		if (typeof(result.success) != 'undefined') {
                			resetAllValues();
                			$('#table').bootstrapTable('removeAll');
                			$('#table').bootstrapTable('refresh', {url: '{{ route('plano.BuscaProgramas') }}?CodCidade='+$('#CodCidade').val()+'&Ano='+$('#Ano').val()});
                			$('#tableProjetos').bootstrapTable('removeAll');
                			$('#tableProjetos').bootstrapTable('refresh', {url: '{{ route('plano.BuscaProjetos') }}?CodCidade='+$('#CodCidade').val()+'&Ano='+$('#Ano').val()});
                			$('#_tabelaResults').show();
                			alertify.success('Gravado com sucesso'); 
                			//window.location.replace("{{ route('plano') }}&Ano="+$('#Ano').val());
                			//$('#Plano').data('formValidation').resetForm($('#Plano'));
                		}else{
                			if(typeof(result.error) != 'undefined'){
                				alertify.error('Erro '+result.error); 
                			}
                			//$('#Plano').data('formValidation').resetForm($('#Plano'));
                		}
                	}
                });
            }
        })

.on('err.field.fv', function (e, data) {
	console.log(e)
	console.log(data)
	var $invalidFields = data.fv.getInvalidFields().eq(0);

});


    //Form Projeto
    $('#Projeto').formValidation({
    	framework: 'bootstrap',
    	err: {
    		container: 'tooltip'
    	},
    	excluded: [':disabled'],
    	fields: {
    		'NomeProjeto': {
    			validators: {
    				notEmpty: {
    					message: 'Campo Obrigatório'
    				}
    			}
    		},
    		'ResponsavelProjeto': {
    			validators: {
    				notEmpty: {
    					message: 'Campo Obrigatório'
    				}
    			}
    		},
    		'TipoProjeto': {
    			validators: {
    				notEmpty: {
    					message: 'Campo Obrigatório'
    				}
    			}
    		},
    		// 'PesoProjeto': {
    		// 	validators: {
    		// 		notEmpty: {
    		// 			message: 'Campo Obrigatório'
    		// 		}
    		// 	}
    		// },
    		'PesoPrograma[]': {
    			selector: '.PesoPrograma_',
    			validators: {
    				notEmpty: {
    					message: 'Campo Obrigatório'
    				}
    			}
    		},
    		'ProjetoPrograma[]': {
    			selector: '.ProjetoPrograma_',
    			validators: {
    				notEmpty: {
    					message: 'Campo Obrigatório'
    				}
    			}
    		},


    	}


    })
    .on('success.field.fv', function (e, data) {
                // data.fv      --> The FormValidation instance
                // data.element --> The field element
            })
    .on('success.form.fv', function (e, data) {

                // Prevent form submission
                var arr = [];
                var validacao = true;
                e.preventDefault();

                if($("#TipoProjeto").val()==''){
                	alertify.success('Você deve preencher o Projeto');
                	validacao = false;
                	return false;
                }
                // if($("#PesoProjeto").val()==''){
                // 	alertify.success('Você deve preencher o Peso dentro do Projeto');
                // 	validacao = false;
                // 	return false;
                // }
                $.ajax(
                {
                	url: '{{ route('projeto.PesoPorProjeto') }}',
                	type: "POST",
                	headers: {
                		'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                	},
                	data:{
                		idProjeto: $("#idProjeto").val(),  
                		PesoProjeto: $("#PesoProjeto").val(), 
                		Ano: $("#Ano").val(), 
                		CodCidade: $("#CodCidade").val(), 
                	},
                	async: false,
                	global: false,
                	success: function (data, textStatus, jqXHR)
                	{
                		if(parseInt(data) > 100){
                			alertify.alert('A soma dos pesos das intervenções(Ações) dentro do projeto '+$("#TipoProjeto").val()+' não pode ser maior que 100'); 
                			$('select[name*="ProjetoPrograma[]"]').focus()
                			validacao = false;
                			return false;
                		}

                	},
                	error: function (jqXHR, textStatus, errorThrown)
                	{
                		if(jqXHR.status == 419){
                			location.reload();
                		}
                		alertify.success('Erro ao validar peso no projeto');
                		validacao = false;
                		return false;
                	}
                });

                $('select[name*="ProjetoPrograma[]"]').each(function(){
                	var value = $(this).val();
                	var pesoPrograma = $('select[name*="ProjetoPrograma[]"]').parent().parent().parent().find('.PesoPrograma_').val();
                	$.ajax(
                	{
                		url: '{{ route('projeto.PesoTotal') }}',
                		type: "POST",
                		headers: {
                			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                		},
                		data:{
                			idProjeto: $("#idProjeto").val(), 
                			idPrograma: value, 
                			projeto: $("#TipoProjeto").val(), 
                			pesoPrograma: pesoPrograma, 
                			Ano: $("#Ano").val(), 
                			CodCidade: $("#CodCidade").val(), 
                		},
                		async: false,
                		global: false,
                		success: function (data, textStatus, jqXHR)
                		{
                			console.log(data, pesoPrograma)
                			if(parseInt(data) > 100){
                				alertify.alert('A soma dos pesos das intervenções(Ações) no programa não pode ser maior que 100'); 
                				$('select[name*="ProjetoPrograma[]"]').focus()
                				validacao = false;
                				return false;
                			}

                		},
                		error: function (jqXHR, textStatus, errorThrown)
                		{
                			if(jqXHR.status == 419){
                				location.reload();
                			}
                			alertify.success('Erro ao validar peso no programa');
                			validacao = false;
                			return false;
                		}
                	});
                	
                	if (arr.indexOf(value) == -1){
                		arr.push(value);
                	}else{
                		alertify.alert('Você deve selecionar programas diferentes'); 
                		$('select[name*="ProjetoPrograma[]"]').focus()
                		validacao = false;
                		return false;
                	}
                });
                $('input[name*="PesoPrograma[]"]').each(function(){
                	if($(this).val() == ''){
                		validacao = false;
                		alertify.alert('Peso no programa não pode ser vazio'); 
                		return false;
                	}

                });
                if (validacao == true){

                	var $form = $(e.target),
                	fv = $form.data('formValidation');

                	$('input[type=text]').val(function () {
                		return replaceSpecialChars(this.value);
                	})

                	$('input:disabled, select:disabled').each(function () {
                		$(this).removeAttr('disabled');
                	});

                // Use Ajax to submit form data
                $.ajax({
                	url: '{{ route('projeto.gravar') }}',
                	type: 'POST',
                	headers: {
                		'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                	},
                	data: $form.serialize()+ '&CodCidade=' +$("#CodCidade").val()+ '&Ano=' +$("#Ano").val(),
                	success: function (result) {
                		//console.log(result)
                		if(typeof(result.error) == 'undefined'){
                			resetAllValues();
                			$('#table').bootstrapTable('removeAll');
                			$('#table').bootstrapTable('refresh', {url: '{{ route('plano.BuscaProgramas') }}?CodCidade='+$('#CodCidade').val()+'&Ano='+$('#Ano').val()});
                			$('#tableProjetos').bootstrapTable('removeAll');
                			$('#tableProjetos').bootstrapTable('refresh', {url: '{{ route('plano.BuscaProjetos') }}?CodCidade='+$('#CodCidade').val()+'&Ano='+$('#Ano').val()});
                			$('#_tabelaResults').show();
                			alertify.success('Gravado com sucesso'); 
                			//reload page
                			//window.location.replace("{{ route('plano') }}&Ano="+$('#Ano').val());
                			//$('#Projeto').data('formValidation').resetForm($('#Projeto'));


                		}else{
                			alertify.alert('Erro ao gravar '+result.error); 
                		}

                	},
                	error: function (jqXHR, textStatus, errorThrown)
                	{
                		if(jqXHR.status == 419){
                			location.reload();
                		}
                		alertify.success('Erro ao gravar');
                		return false;
                		//$('#Projeto').data('formValidation').resetForm($('#Projeto'));
                	}
                });
            }

        })

.on('err.field.fv', function (e, data) {
	console.log(e)
	console.log(data)
	var $invalidFields = data.fv.getInvalidFields().eq(0);

})
.on('click', '.addPrograma', function(e) {
	e.preventDefault()

	if($('select[name*="ProjetoPrograma[]"]').length == $('#ProjetoPrograma').children('option').length){
		alertify.alert('Você precisa criar um novo programa para vincular nesse projeto'); 
	}else{
		var ProjetoPrograma = $('.ProjetosPrograma').clone();
		ProjetoPrograma.append('<div class="col-md-2 form-group">'+
			'<a href="#" class="btn btn-default pull-right removeProjetoPrograma" '+
			'style="margin-top: 23px;">Remover Programa</a></div>')
		ProjetoPrograma.insertAfter($('.ProjetosPrograma'))
		$("input[name='PesoPrograma[]']").last().val('');
		$('#Projeto').formValidation('addField', 'ProjetoPrograma[]');
		$('#Projeto').formValidation('addField', 'PesoPrograma[]');

	}
})
.on('click', '.removeProjetoPrograma', function(e) {
	e.preventDefault();
	$projeto = $(this).parent().parent().find('[name="ProjetoPrograma[]"]');
	$peso = $(this).parent().parent().find('[name="PesoPrograma[]"]');
	console.log($peso, $projeto)
	var idPlano = $(this).parent().parent().find('[name="ProjetoPrograma[]"]').val();
	console.log(idPlano)
	if($("#idProjeto").val() != ''){
		$.ajax({
			url: 'plano.php?Acao=removeProjetoPrograma',
			type: 'POST',
			headers: {
				'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
			},
			data: $form.serialize()+ '&idProjeto=' +$("#idProjeto").val()+ '&idPlano=' +idPlano,
			success: function (result) {
				console.log(result)
				if(result == 'ok'){
					$('#Projeto').formValidation('removeField', $projeto);
					$('#Projeto').formValidation('removeField', $peso);
					$(this).parent().parent().remove()


				}else{
					alertify.alert('Erro ao remover'); 
				}

			}
		});

	}else{
		$('#Projeto').formValidation('removeField', $projeto);
		$('#Projeto').formValidation('removeField', $peso);
		$(this).parent().parent().remove()

	}

});

});

@if( request()->get('Ano') )
$("#Ano").val('{{ request()->get('Ano')}}').trigger('change');
@endif
</script>

@endsection