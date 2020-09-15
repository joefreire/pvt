@extends('layouts.app')
@section('styles')

{{-- <link rel="stylesheet" type="text/css" href="{{ asset('libraries/css/formValidation.min.css') }}">
--}}
<style>
	#FR_EA>td{
		width: 13%;
	}
	#CRL_EA>td{
		width: 10%;
	}
	#FR_PI>td{
		width: 17%;
	}
	.table{
		margin-bottom: 40px;
	}

	th.endereco {

		border-bottom: 0!important;
		text-align: center;
	}
	tr.endereco-dividido>th {
		border-top: 0px!important;
	}
	hr.style3 {
		border-top: 1px dashed #8c8b8b;
	}
	.h3-semlinha{
		padding-bottom: 0.5em;
		margin-bottom: 0em;
		color: #2184BE;
		font-size: 18px;
	}
	.quadroMultiplo{
		outline: none!important;
	}
	td.CEP {
		width: 6%;
		padding-right: 3px;
		padding-left: 3px;
	}
	td.Numero {
		width: 6%;
	}
	td.Municipio {
		width: 18%;
	}
	td.Velocidade_Via {
		width: 10%;
	}
	td.porcento8 {
		width: 9%;
	}
	td.porcento11 {
		width: 12%;
	}
	td.obitos_feridos {
		width: 5%;
	}
	.has-feedback .form-control {
		padding-right: 3px;
		padding-left: 3px;
	}
	td.EstadoAcidente {
		width: 18%;
	}
	td.DataAcidente {
		width: 8%;
	}
	td.Rua {
		width: 25%;
	}
	td.TipoAcidente{
		width: 11%;
	}
	td.IdentificadorAcidente{
		width: 8%;
	}
	td.Bairro {
		width: 13%;
	}
	td.FonteDados {
		width: 10%;
	}
	input {
		text-transform: uppercase;
	}
	select{     
		text-transform: uppercase;
	}
	.form-control {
		font-size: 13px;
	}
	.FatoresPreenchidos{
		background-color: SeaGreen!important;
		color: white!important;
	}
	.FatoresDevemPreenchidos{
		background-color: #B22222!important;
		color: white!important;
	}
	#_DadosVitimas  .form-group{
		padding-right: 5px;
		padding-left: 5px;
	}


</style>
@endsection
@section('content')
<div class="col-md-12 col-sm-12"> 
	<div id="loading" style="display: none;"></div>
	<div class="page-header">
		<h3>Quadro Múltiplo</h3>
	</div>

	<form id="QuadroMultiplo" method="post">   
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
				<input type="hidden" id="CodCidade" class="loadData" name="CodCidade"> 
			</div>
			@else
			<input type="hidden" id="CodCidade" class='loadData' name="CodCidade" value="{{ Auth::user()->codcidade }}">
			@endif
		</div>  

		<!-- Text input-->
		<div class="form-inline">

		</div>        
		<!-- Text input-->
		<div class="form-group ">

		</div>

		<div id="_tabelaResults" class="panel panel-default" style="display: none;">
			<div class="panel-heading clearfix"> 
				<div class="loading"></div>
				<span id="titulo-painel" class="h3-semlinha"> </span>
				<div class="btn-group pull-right">
					<button type="button" id="adicionarAcidente" class="btn btn-primary"><span class="glyphicon glyphicon-plus"></span>Adicionar Acidente</button>             
				</div>
			</div>
			<div id="loading-image" style="text-align: center;display: none;">   
				<img src="{{ asset('libraries/img/loading2.gif') }}" alt="" style="width: 5%;">
			</div>
			<div class="panel-body" id="resultadosQM">
				<div class="col-md-12"> 
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
					</div>      
					<div class="row table-responsive">    
						<table class="table display table-striped table-bordered nowrap table" cellspacing="0" id="table" width="100%"> 
							<thead>
								<tr>
									<th></th>
									<th data-priority="1">Identificador <BR>do acidente:</th>
									<th>Tipo de Acidente:</th>
									<th>Óbitos:</th>
									<th>Feridos:</th>
									<th>Data <BR>do Acidente:</th>
									<th>Hora <BR>do Acidente:</th>
									<th>Rua/Avenida:</th>
									<th>Bairro:</th>
									<th>Município:</th>
									<th>Vitimas:</th>
									<th data-priority="2">Ações:</th>
								</tr>
							</thead>

						</table> 

					</div>
				</div>
			</div>

		</div>

		<div id="_QuadroMultiplo" class='quadroMultiplo' style="display: none;">
			<div class="alert alert-danger print-error-msg" style="display:none">
				<ul></ul>
			</div>
			<hr class="separator">
			<div class="table-responsive">    
				<table class="table table-condensed">
					<h4>Dados do Acidente</h4>
					<thead>
						<tr>
							<th>Fonte de Dados:</th>
							<th>Boletim / Identificador:</th>
							<th>Data do acidente:</th>
							<th>Hora do acidente:</th>
							<th>Tipo de Acidente</th>            
						</tr> 
					</thead>
					<tbody>
						<tr>
							<td data-title="FonteDados" class='FonteDados'>
								<select name="FonteDados" id="FonteDados" class="form-control" style="padding-right:5px;padding-left:5px;">
									<OPTION VALUE=""></OPTION>
									<OPTION VALUE="CORPO DE BOMBEIROS">CORPO DE BOMBEIROS</OPTION>
									<OPTION VALUE="POLICIA RODOVIARIA FEDERAL">POLÍCIA RODOVIÁRIA FEDERAL</OPTION>
									<OPTION VALUE="POLICIA MILITAR">POLÍCIA MILITAR</OPTION>
									<OPTION VALUE="SAMU">SAMU</OPTION>
									<OPTION VALUE="DETRAN">DETRAN</OPTION>
									<OPTION VALUE="IML">IML</OPTION>
									<OPTION VALUE="DELEGACIA DE TRANSITO">DELEGACIA DE TRÂNSITO</OPTION>
									<OPTION VALUE="ORGAO MUNICIPAL DE TRANSITO">ÓRGÃO MUNICIPAL DE TRÂNSITO</OPTION>
									<OPTION VALUE="OUTRO">OUTRO</OPTION>
								</select>
							</td>        
							<td data-title="IdentificadorAcidente" class='IdentificadorAcidente'>
								<input type="text" class="form-control" id="IdentificadorAcidente" name="IdentificadorAcidente" >
							</td>        
							<td data-title="DataAcidente" class='DataAcidente'>
								<input type="text" class="form-control" id="DataAcidente" name="DataAcidente" maxlength="10" >
							</td>        
							<td data-title="HoraAcidente" class='DataAcidente'>
								<input type="number" min="0" max="23" class="form-control" id="HoraAcidente" name="HoraAcidente" >
							</td>        
							<td class='TipoAcidente'>
								<select name="TipoAcidente" id="TipoAcidente" class="form-control" style="padding-right:5px;padding-left:5px;">
									<OPTION VALUE=""></OPTION>
									<OPTION VALUE="ABALROAMENTO">ABALROAMENTO</OPTION>
									<OPTION VALUE="ABALROAMENTO LATERAL NO MESMO SENTIDO">ABALROAMENTO LATERAL NO MESMO SENTIDO</OPTION>                          
									<OPTION VALUE="ABALROAMENTO TRANSVERSAL">ABALROAMENTO TRANSVERSAL</OPTION>
									<OPTION VALUE="ATROPELAMENTO">ATROPELAMENTO</OPTION>
									<OPTION VALUE="ATROPELAMENTO DE ANIMAL">ATROPELAMENTO DE ANIMAL</OPTION>
									<OPTION VALUE="CAPOTAGEM">CAPOTAGEM</OPTION>
									<OPTION VALUE="CHOQUE">CHOQUE</OPTION>
									<OPTION VALUE="CHOQUE COM OBJETOS NA LATERAL DA VIA">CHOQUE COM OBJETOS NA LATERAL DA VIA</OPTION>
									<OPTION VALUE="CHOQUE COM VEICULO ESTACIONADO">CHOQUE COM VEICULO ESTACIONADO</OPTION>
									<OPTION VALUE="COLISÃO">COLISÃO</OPTION>
									<OPTION VALUE="COLISÃO TRASEIRA">COLISÃO TRASEIRA</OPTION>
									<OPTION VALUE="COLISÃO FRONTAL">COLISÃO FRONTAL</OPTION>
									<OPTION VALUE="SAÍDA DE PISTA">SAÍDA DE PISTA</OPTION>              
									<OPTION VALUE="TOMBAMENTO">TOMBAMENTO</OPTION>
									<OPTION VALUE="OUTRO">OUTRO</OPTION>
									<OPTION VALUE="NAO INFORMADO">NÃO INFORMADO</OPTION>
								</select>
							</td>        
						</tr>
						<tr>
							<td data-title="FonteDados" class='FonteDados'>
								<select name="FonteDados2" id="FonteDados2" class="form-control" style="padding-right:5px;padding-left:5px;">
									<OPTION VALUE=""></OPTION>
									<OPTION VALUE="CORPO DE BOMBEIROS">CORPO DE BOMBEIROS</OPTION>
									<OPTION VALUE="POLICIA RODOVIARIA FEDERAL">POLICIA RODOVIARIA FEDERAL</OPTION>
									<OPTION VALUE="POLICIA MILITAR">POLICIA MILITAR</OPTION>
									<OPTION VALUE="SAMU">SAMU</OPTION>
									<OPTION VALUE="DETRAN">DETRAN</OPTION>
									<OPTION VALUE="IML">IML</OPTION>
									<OPTION VALUE="DELEGACIA DE TRANSITO">DELEGACIA DE TRANSITO</OPTION>
									<OPTION VALUE="ORGAO MUNICIPAL DE TRANSITO">ORGAO MUNICIPAL DE TRANSITO</OPTION>
									<OPTION VALUE="OUTRO">OUTRO</OPTION>
								</select>
							</td>        
							<td data-title="IdentificadorAcidente" class='IdentificadorAcidente'>
								<input type="text" class="form-control" id="IdentificadorAcidente2" name="IdentificadorAcidente2" >
							</td>        
						</tr>
						<tr>
							<td data-title="FonteDados" class='FonteDados'>
								<select name="FonteDados3" id="FonteDados3" class="form-control" style="padding-right:5px;padding-left:5px;">
									<OPTION VALUE=""></OPTION>
									<OPTION VALUE="CORPO DE BOMBEIROS">CORPO DE BOMBEIROS</OPTION>
									<OPTION VALUE="POLICIA RODOVIARIA FEDERAL">POLICIA RODOVIARIA FEDERAL</OPTION>
									<OPTION VALUE="POLICIA MILITAR">POLICIA MILITAR</OPTION>
									<OPTION VALUE="SAMU">SAMU</OPTION>
									<OPTION VALUE="DETRAN">DETRAN</OPTION>
									<OPTION VALUE="IML">IML</OPTION>
									<OPTION VALUE="DELEGACIA DE TRANSITO">DELEGACIA DE TRANSITO</OPTION>
									<OPTION VALUE="ORGAO MUNICIPAL DE TRANSITO">ORGAO MUNICIPAL DE TRANSITO</OPTION>
									<OPTION VALUE="OUTRO">OUTRO</OPTION>
								</select>

							</td>        
							<td data-title="IdentificadorAcidente" class='IdentificadorAcidente'>
								<input type="text" class="form-control" id="IdentificadorAcidente3" name="IdentificadorAcidente3" >
							</td>        
						</tr>
					</tbody>
				</table> 
				<input type="hidden" id="idQuadroMultiplo" name="idQuadroMultiplo">
				<input type="hidden" id="qtdVitimas" name="qtdVitimas" value="0">
				<table class="table table-condensed" style="margin-bottom: 0px!important;">
					<h4>Endereço do Acidente</h4>
					<thead>
						<tr>
							<th>CEP:</th>
							<th>Rua/Avenida:</th>
							<th>Número:</th>
							<th>Complemento:</th>
							<th>Quadra:</th>
							<th>Lote:</th>
							<th>Bairro:</th>
						</tr>
					</thead>
					<tbody>
						<tr>

							<td class='CEP'>
								<input id="CEP" type="text" min='0' maxlength="8" class="form-control" name="CEP" onChange="buscaCEPEndereco();">
							</td>
							<td class='Rua'>
								<input id="Endereco" type="text" class="form-control" name="Endereco" onChange="validaLatitude();">
							</td>
							<td class='Numero'>
								<input type="text" class="form-control" name="Numero" id="Numero" onChange="validaLatitude();">
							</td>
							<td class='Complemento' style=" width: 5%;">
								<input type="text" class="form-control" name="Complemento" id="Complemento">
							</td>
							<td class='Complemento' style=" width: 5%;">
								<input type="text" class="form-control" name="Quadra" id="Quadra">
							</td>
							<td class='Complemento' style=" width: 5%;">
								<input type="text" class="form-control" name="Lote" id="Lote">
							</td>
							<td class='Bairro'>
								<input type="text" class="form-control" name="Bairro" id="Bairro" onChange="validaLatitude();">
							</td>

						</tr>
					</tbody>
				</table> 
				<table class="table table-condensed">
					<thead>
						<tr>
							<th>Estado:</th>
							<th>Município:</th>                            
							<th>Velocidade Via</th>
							<th>Coodernada X:</th>
							<th>Coodernada Y:</th>
						</tr>
					</thead>
					<tbody>
						<tr>
							<td class='EstadoAcidente'>
								<select id="EstadoAcidente" class="form-control" name="EstadoAcidente" class="form-control " onChange="validaLatitude();"> 
									<option value="">Selecione o Estado do acidente</option>
								</select>
							</td>
							<td class='Municipio'>

								<select id="MunicipioAcidente" class="form-control" name="MunicipioAcidente" class="form-control " onChange="validaLatitude();"> 
									<option value="">Selecione o município do acidente</option>
								</select>

							</td>
							<td class='Velocidade_Via'>
								<input type="text" class="form-control" name="velocidade_via" id="velocidade_via" >
							</td>
							<td class=''>
								<input type="text" class="form-control" name="CoordX" id="CoordX" >
							</td>
							<td class=''>
								<input type="text" class="form-control" name="CoordY" id="CoordY" >
							</td>
						</tr>
					</tbody>
				</table> 

			</div>
			<div class="table-responsive">  
				<table class="table table-condensed">
					<h4>Fatores de Risco</h4>
					<thead>
						<tr>

							<th>Velocidade</th>        
							<th>Álcool</th>       
							<th>Infraestrutura</th>        
							<th>Condição <BR>do Veículo</th>        
							<th>Fadiga</th>        
							<th>Visibilidade</th>       
							<th>Drogas</th>       
							<th>Equipamentos <BR>de distração</th>

						</tr>
					</thead>
					<tbody id='FR_EA'>
						<tr id="FR_EA">
							<td>

								<select name="Velocidade" id="Velocidade" class="form-control num11">
									<option value="0">0</option>
									<option class='2' value="2">2</option>
									<option class='4' value="4">4</option>
									<option class='6' value="6">6</option>
									<option class='8' value="8">8</option>
									<option class='10' value="10">10</option>
								</select>

								<div id="_TipoVelocidade" >
									<label>Tipo: </label><BR>
									<select name="TipoVelocidade"  id="TipoVelocidade" class="form-control"  style="padding-right:0px;padding-left:5px;">
										<OPTION VALUE=""></OPTION>
										<OPTION VALUE="EXCESSIVA">EXCESSIVA</OPTION>
										<OPTION VALUE="INADEQUADA">INADEQUADA</OPTION>
									</select>
								</div> 
								<div id="_UsuarioContributivo_Velocidade">
									<label>Usuário Contributivo: </label><BR>
									<select name="UsuarioContributivo_Velocidade" id="UsuarioContributivo_Velocidade" class="form-control"  style="padding-right:0px;padding-left:5px;">
										<option value=""></option>
										<OPTION VALUE="CONDUTOR OU PASSAGEIRO DE VEÍCULO LEVE">CONDUTOR OU PASSAGEIRO DE VEÍCULO LEVE</OPTION>
										<OPTION VALUE="CONDUTOR OU PASSAGEIRO DE VEÍCULO PESADO">CONDUTOR OU PASSAGEIRO DE VEÍCULO PESADO</OPTION>
										<OPTION VALUE="CONDUTOR OU PASSAGEIRO DE MOTO">CONDUTOR OU PASSAGEIRO DE MOTO</OPTION>
										<OPTION VALUE="CONDUTOR OU PASSAGEIRO DE BICICLETA">CONDUTOR OU PASSAGEIRO DE BICICLETA</OPTION>
										<OPTION VALUE="CONDUTOR OU PASSAGEIRO DE ÔNIBUS/VAN ">CONDUTOR OU PASSAGEIRO DE ÔNIBUS/VAN </OPTION>
										<OPTION VALUE="PEDESTRE">PEDESTRE</OPTION>
									</select>
								</div> 
							</td>
							<td>   
								<select name="Alcool" id="Alcool" class="form-control num11">
									<option value="0">0</option>
									<option class='2' value="2">2</option>
									<option class='4' value="4">4</option>
									<option class='6' value="6">6</option>
									<option class='8' value="8">8</option>
									<option class='10' value="10">10</option>
								</select>
								<div id="_UsuarioContributivo_Alcool">
									<label>Usuário Contributivo: </label><BR>
									<select name="UsuarioContributivo_Alcool" id="UsuarioContributivo_Alcool" class="form-control"  style="padding-right:0px;padding-left:5px;">
										<OPTION VALUE=""></OPTION>
										<OPTION VALUE="CONDUTOR OU PASSAGEIRO DE VEÍCULO LEVE">CONDUTOR OU PASSAGEIRO DE VEÍCULO LEVE</OPTION>
										<OPTION VALUE="CONDUTOR OU PASSAGEIRO DE VEÍCULO PESADO">CONDUTOR OU PASSAGEIRO DE VEÍCULO PESADO</OPTION>
										<OPTION VALUE="CONDUTOR OU PASSAGEIRO DE MOTO">CONDUTOR OU PASSAGEIRO DE MOTO</OPTION>
										<OPTION VALUE="CONDUTOR OU PASSAGEIRO DE BICICLETA">CONDUTOR OU PASSAGEIRO DE BICICLETA</OPTION>
										<OPTION VALUE="CONDUTOR OU PASSAGEIRO DE ÔNIBUS/VAN ">CONDUTOR OU PASSAGEIRO DE ÔNIBUS/VAN </OPTION>
										<OPTION VALUE="PEDESTRE">PEDESTRE</OPTION>
									</select>
								</div> 
							</td>
							<td>   
								<select name="Infraestrutura"  id="Infraestrutura" class="form-control num11">
									<option value="0">0</option>
									<option class='2' value="2">2</option>
									<option class='4' value="4">4</option>
									<option class='6' value="6">6</option>
									<option class='8' value="8">8</option>
									<option class='10' value="10">10</option>
								</select>
								<div id="_TipoInfraestrutura" >
									<label>Tipo: </label><BR>
									<select name="TipoInfraestrutura" id="TipoInfraestrutura" class="form-control"  style="padding-right:0px;padding-left:5px;    font-size: 11px;">
										<OPTION VALUE=""></OPTION>
										<OPTION VALUE="ILUMINAÇÃO">ILUMINAÇÃO</OPTION>
										<OPTION VALUE="AUSÊNCIA TRAVESSIA SEGURA PARA O PEDESTRE">AUSÊNCIA TRAVESSIA SEGURA PARA O PEDESTRE</OPTION>
										<OPTION VALUE="AUSÊNCIA DE CALÇADA">AUSÊNCIA DE CALÇADA</OPTION>
										<OPTION VALUE="ENGENHARIA INDUZ A ERRO">ENGENHARIA INDUZ A ERRO</OPTION>
										<OPTION VALUE="ANTEPARO">ANTEPARO</OPTION>
										<OPTION VALUE="CONSERVAÇÃO DA PISTA DE ROLAMENTO">CONSERVAÇÃO DA PISTA DE ROLAMENTO</OPTION>
										<OPTION VALUE="FALTA DE SINALIZAÇÃO">FALTA DE SINALIZAÇÃO</OPTION>
										<OPTION VALUE="FALTA DE DEFENSA">FALTA DE DEFENSA</OPTION>
										<OPTION VALUE="FALTA DE SINALIZAÇÃO PARA CICLISTA">FALTA DE SINALIZAÇÃO PARA CICLISTA</OPTION>
										<OPTION VALUE="FALTA DE MURETA DE PROTEÇÃO">FALTA DE MURETA DE PROTEÇÃO</OPTION>
										<OPTION VALUE="AUSÊNCIA DE LOCAL ADEQUADO PARA RETORNO">AUSÊNCIA DE LOCAL ADEQUADO PARA RETORNO</OPTION>
									</select>
								</div>
							</td>
							<td>   
								<select name="Veiculo" id="Veiculo" class="form-control num11">
									<option value="0">0</option>
									<option class='2' value="2">2</option>
									<option class='4' value="4">4</option>
									<option class='6' value="6">6</option>
									<option class='8' value="8">8</option>
									<option class='10' value="10">10</option>
								</select>
								<div id="_UsuarioContributivo_Veiculo">
									<label>Usuário Contributivo: </label><BR>
									<select name="UsuarioContributivo_Veiculo" id="UsuarioContributivo_Veiculo" class="form-control"  style="padding-right:0px;padding-left:5px;">
										<OPTION VALUE=""></OPTION>
										<OPTION VALUE="CONDUTOR OU PASSAGEIRO DE VEÍCULO LEVE">CONDUTOR OU PASSAGEIRO DE VEÍCULO LEVE</OPTION>
										<OPTION VALUE="CONDUTOR OU PASSAGEIRO DE VEÍCULO PESADO">CONDUTOR OU PASSAGEIRO DE VEÍCULO PESADO</OPTION>
										<OPTION VALUE="CONDUTOR OU PASSAGEIRO DE MOTO">CONDUTOR OU PASSAGEIRO DE MOTO</OPTION>
										<OPTION VALUE="CONDUTOR OU PASSAGEIRO DE BICICLETA">CONDUTOR OU PASSAGEIRO DE BICICLETA</OPTION>
										<OPTION VALUE="CONDUTOR OU PASSAGEIRO DE ÔNIBUS/VAN ">CONDUTOR OU PASSAGEIRO DE ÔNIBUS/VAN </OPTION>
										<OPTION VALUE="PEDESTRE">PEDESTRE</OPTION>
									</select>
								</div> 
							</td>
							<td>   
								<select name="Fadiga" id="Fadiga" class="form-control num11">
									<option value="0">0</option>
									<option class='2' value="2">2</option>
									<option class='4' value="4">4</option>
									<option class='6' value="6">6</option>
									<option class='8' value="8">8</option>
									<option class='10' value="10">10</option>
								</select>
								<div id="_UsuarioContributivo_Fadiga" >
									<label>Usuário Contributivo: </label><BR>
									<select name="UsuarioContributivo_Fadiga" id="UsuarioContributivo_Fadiga" class="form-control"  style="padding-right:0px;padding-left:5px;">
										<OPTION VALUE=""></OPTION>
										<OPTION VALUE="CONDUTOR OU PASSAGEIRO DE VEÍCULO LEVE">CONDUTOR OU PASSAGEIRO DE VEÍCULO LEVE</OPTION>
										<OPTION VALUE="CONDUTOR OU PASSAGEIRO DE VEÍCULO PESADO">CONDUTOR OU PASSAGEIRO DE VEÍCULO PESADO</OPTION>
										<OPTION VALUE="CONDUTOR OU PASSAGEIRO DE MOTO">CONDUTOR OU PASSAGEIRO DE MOTO</OPTION>
										<OPTION VALUE="CONDUTOR OU PASSAGEIRO DE BICICLETA">CONDUTOR OU PASSAGEIRO DE BICICLETA</OPTION>
										<OPTION VALUE="CONDUTOR OU PASSAGEIRO DE ÔNIBUS/VAN ">CONDUTOR OU PASSAGEIRO DE ÔNIBUS/VAN </OPTION>
										<OPTION VALUE="PEDESTRE">PEDESTRE</OPTION>
									</select>
								</div> 
							</td>
							<td>   
								<select name="Visibilidade" id="Visibilidade" class="form-control num11">
									<option value="0">0</option>
									<option class='2' value="2">2</option>
									<option class='4' value="4">4</option>
									<option class='6' value="6">6</option>
									<option class='8' value="8">8</option>
									<option class='10' value="10">10</option>
								</select>
							</td>
							<td>   
								<select name="Drogas" id="Drogas" class="form-control num11">
									<option value="0">0</option>
									<option class='2' value="2">2</option>
									<option class='4' value="4">4</option>
									<option class='6' value="6">6</option>
									<option class='8' value="8">8</option>
									<option class='10' value="10">10</option>
								</select>
								<div id="_UsuarioContributivo_Drogas" >
									<label>Usuário Contributivo: </label><BR>
									<select name="UsuarioContributivo_Drogas" id="UsuarioContributivo_Drogas" class="form-control"  style="padding-right:0px;padding-left:5px;">
										<OPTION VALUE=""></OPTION>
										<OPTION VALUE="CONDUTOR OU PASSAGEIRO DE VEÍCULO LEVE">CONDUTOR OU PASSAGEIRO DE VEÍCULO LEVE</OPTION>
										<OPTION VALUE="CONDUTOR OU PASSAGEIRO DE VEÍCULO PESADO">CONDUTOR OU PASSAGEIRO DE VEÍCULO PESADO</OPTION>
										<OPTION VALUE="CONDUTOR OU PASSAGEIRO DE MOTO">CONDUTOR OU PASSAGEIRO DE MOTO</OPTION>
										<OPTION VALUE="CONDUTOR OU PASSAGEIRO DE BICICLETA">CONDUTOR OU PASSAGEIRO DE BICICLETA</OPTION>
										<OPTION VALUE="CONDUTOR OU PASSAGEIRO DE ÔNIBUS/VAN ">CONDUTOR OU PASSAGEIRO DE ÔNIBUS/VAN </OPTION>
										<OPTION VALUE="PEDESTRE">PEDESTRE</OPTION>
									</select>
									<label>Tipo de Droga: </label><BR>
									<select name="TipoDroga" id="TipoDroga" class="form-control"  style="padding-right:0px;padding-left:5px;">
										<OPTION VALUE=""></OPTION>
										<OPTION VALUE="MEDICAMENTOS">MEDICAMENTOS</OPTION>
										<OPTION VALUE="MACONHA">MACONHA</OPTION>
										<OPTION VALUE="COCAÍNA">COCAÍNA</OPTION>
										<OPTION VALUE="CRACK">CRACK</OPTION>
										<OPTION VALUE="ANFETAMINAS">ANFETAMINAS</OPTION>
										<OPTION VALUE="OPIOÁCEOS">OPIOÁCEOS</OPTION>

									</select>
								</div> 
							</td>
							<td>   
								<select name="Distacao" id="Distacao" class="form-control num11">
									<option value="0">0</option>
									<option class='2' value="2">2</option>
									<option class='4' value="4">4</option>
									<option class='6' value="6">6</option>
									<option class='8' value="8">8</option>
									<option class='10' value="10">10</option>
								</select>
								<div id="_UsuarioContributivo_Distacao" >
									<label>Usuário Contributivo: </label><BR>
									<select name="UsuarioContributivo_Distacao" id="UsuarioContributivo_Distacao" class="form-control"  style="padding-right:0px;padding-left:5px;">
										<OPTION VALUE=""></OPTION>
										<OPTION VALUE="CONDUTOR OU PASSAGEIRO DE VEÍCULO LEVE">CONDUTOR OU PASSAGEIRO DE VEÍCULO LEVE</OPTION>
										<OPTION VALUE="CONDUTOR OU PASSAGEIRO DE VEÍCULO PESADO">CONDUTOR OU PASSAGEIRO DE VEÍCULO PESADO</OPTION>
										<OPTION VALUE="CONDUTOR OU PASSAGEIRO DE MOTO">CONDUTOR OU PASSAGEIRO DE MOTO</OPTION>
										<OPTION VALUE="CONDUTOR OU PASSAGEIRO DE BICICLETA">CONDUTOR OU PASSAGEIRO DE BICICLETA</OPTION>
										<OPTION VALUE="CONDUTOR OU PASSAGEIRO DE ÔNIBUS/VAN ">CONDUTOR OU PASSAGEIRO DE ÔNIBUS/VAN </OPTION>
										<OPTION VALUE="PEDESTRE">PEDESTRE</OPTION>
									</select>
									<label>Tipo de distração: </label><BR>
									<select name="TipoDistracao" id="TipoDistracao" class="form-control"  style="padding-right:0px;padding-left:5px;">
										<OPTION VALUE=""></OPTION>
										<OPTION VALUE="CELULAR">CELULAR</OPTION>
										<OPTION VALUE="GPS">GPS</OPTION>
										<OPTION VALUE="OUTROS">OUTROS</OPTION>
									</select>
								</div>
							</td>
							<tr>
							</tbody>
						</table>
					</div>
					<div class="table-responsive">   
						<table class="table table-condensed">
							<h4>Condutas locais de Risco </h4>
							<thead>
								<tr>
									<th>Avançar Sinal</th>
									<th>Condutor sem Habilitação</th>
									<th>Transitar em local proibido</th>
									<th>Transitar em local impróprio</th>
									<th>Mudança de Faixa sem sinalização</th>
									<th>Não Manter a distância mínima</th>
									<th>Converter/Cruzar sem dar a preferência</th>
									<th>Não dar a preferência ao pedestre</th>
									<th>Atitude imprudente do pedestre</th>
									<tr>                
									</thead>
									<tbody id="CRL_EA">
										<tr id="CRL_EA">
											<td>
												<select name="AvancarSinal" id="AvancarSinal" class="form-control num12">
													<option value="0">0</option>
													<option class='2' value="2">2</option>
													<option class='4' value="4">4</option>
													<option class='6' value="6">6</option>
													<option class='8' value="8">8</option>
													<option class='10' value="10">10</option>
												</select>
												<div id="_UsuarioContributivo_AvancarSinal" >
													<label>Usuário Contributivo: </label><BR>
													<select name="UsuarioContributivo_AvancarSinal" id="UsuarioContributivo_AvancarSinal" class="form-control"  style="padding-right:0px;padding-left:5px;">
														<OPTION VALUE=""></OPTION>
														<OPTION VALUE="CONDUTOR OU PASSAGEIRO DE VEÍCULO LEVE">CONDUTOR OU PASSAGEIRO DE VEÍCULO LEVE</OPTION>
														<OPTION VALUE="CONDUTOR OU PASSAGEIRO DE VEÍCULO PESADO">CONDUTOR OU PASSAGEIRO DE VEÍCULO PESADO</OPTION>
														<OPTION VALUE="CONDUTOR OU PASSAGEIRO DE MOTO">CONDUTOR OU PASSAGEIRO DE MOTO</OPTION>
														<OPTION VALUE="CONDUTOR OU PASSAGEIRO DE BICICLETA">CONDUTOR OU PASSAGEIRO DE BICICLETA</OPTION>
														<OPTION VALUE="CONDUTOR OU PASSAGEIRO DE ÔNIBUS/VAN ">CONDUTOR OU PASSAGEIRO DE ÔNIBUS/VAN </OPTION>
														<OPTION VALUE="PEDESTRE">PEDESTRE</OPTION>
													</select>
												</div> 
											</td>
											<td>   
												<select name="CondutorSemHabilitacao" id="CondutorSemHabilitacao" class="form-control num12">
													<option value="0">0</option>
													<option class='2' value="2">2</option>
													<option class='4' value="4">4</option>
													<option class='6' value="6">6</option>
													<option class='8' value="8">8</option>
													<option class='10' value="10">10</option>
												</select>
												<div id="_UsuarioContributivo_CondutorSemHabilitacao" >
													<label>Usuário Contributivo: </label><BR>
													<select name="UsuarioContributivo_CondutorSemHabilitacao" id="UsuarioContributivo_CondutorSemHabilitacao" class="form-control"  style="padding-right:0px;padding-left:5px;">
														<OPTION VALUE=""></OPTION>
														<OPTION VALUE="CONDUTOR OU PASSAGEIRO DE VEÍCULO LEVE">CONDUTOR OU PASSAGEIRO DE VEÍCULO LEVE</OPTION>
														<OPTION VALUE="CONDUTOR OU PASSAGEIRO DE VEÍCULO PESADO">CONDUTOR OU PASSAGEIRO DE VEÍCULO PESADO</OPTION>
														<OPTION VALUE="CONDUTOR OU PASSAGEIRO DE MOTO">CONDUTOR OU PASSAGEIRO DE MOTO</OPTION>
														<OPTION VALUE="CONDUTOR OU PASSAGEIRO DE BICICLETA">CONDUTOR OU PASSAGEIRO DE BICICLETA</OPTION>
														<OPTION VALUE="CONDUTOR OU PASSAGEIRO DE ÔNIBUS/VAN ">CONDUTOR OU PASSAGEIRO DE ÔNIBUS/VAN </OPTION>
														<OPTION VALUE="PEDESTRE">PEDESTRE</OPTION>
													</select>
												</div> 
											</td>
											<td>   
												<select name="LocalProibido" id="LocalProibido" class="form-control num12">
													<option value="0">0</option>
													<option class='2' value="2">2</option>
													<option class='4' value="4">4</option>
													<option class='6' value="6">6</option>
													<option class='8' value="8">8</option>
													<option class='10' value="10">10</option>
												</select>
												<div id="_UsuarioContributivo_LocalProibido" >
													<label>Usuário Contributivo: </label><BR>
													<select name="UsuarioContributivo_LocalProibido" id="UsuarioContributivo_LocalProibido" class="form-control"  style="padding-right:0px;padding-left:5px;">
														<OPTION VALUE=""></OPTION>
														<OPTION VALUE="CONDUTOR OU PASSAGEIRO DE VEÍCULO LEVE">CONDUTOR OU PASSAGEIRO DE VEÍCULO LEVE</OPTION>
														<OPTION VALUE="CONDUTOR OU PASSAGEIRO DE VEÍCULO PESADO">CONDUTOR OU PASSAGEIRO DE VEÍCULO PESADO</OPTION>
														<OPTION VALUE="CONDUTOR OU PASSAGEIRO DE MOTO">CONDUTOR OU PASSAGEIRO DE MOTO</OPTION>
														<OPTION VALUE="CONDUTOR OU PASSAGEIRO DE BICICLETA">CONDUTOR OU PASSAGEIRO DE BICICLETA</OPTION>
														<OPTION VALUE="CONDUTOR OU PASSAGEIRO DE ÔNIBUS/VAN ">CONDUTOR OU PASSAGEIRO DE ÔNIBUS/VAN </OPTION>
														<OPTION VALUE="PEDESTRE">PEDESTRE</OPTION>
													</select>
												</div> 
											</td>
											<td>   
												<select name="LocalImproprio" id="LocalImproprio" class="form-control num12">
													<option value="0">0</option>
													<option class='2' value="2">2</option>
													<option class='4' value="4">4</option>
													<option class='6' value="6">6</option>
													<option class='8' value="8">8</option>
													<option class='10' value="10">10</option>
												</select>
												<div id="_UsuarioContributivo_LocalImproprio" >
													<label>Usuário Contributivo: </label><BR>
													<select name="UsuarioContributivo_LocalImproprio" id="UsuarioContributivo_LocalImproprio" class="form-control"  style="padding-right:0px;padding-left:5px;">
														<OPTION VALUE=""></OPTION>
														<OPTION VALUE="CONDUTOR OU PASSAGEIRO DE VEÍCULO LEVE">CONDUTOR OU PASSAGEIRO DE VEÍCULO LEVE</OPTION>
														<OPTION VALUE="CONDUTOR OU PASSAGEIRO DE VEÍCULO PESADO">CONDUTOR OU PASSAGEIRO DE VEÍCULO PESADO</OPTION>
														<OPTION VALUE="CONDUTOR OU PASSAGEIRO DE MOTO">CONDUTOR OU PASSAGEIRO DE MOTO</OPTION>
														<OPTION VALUE="CONDUTOR OU PASSAGEIRO DE BICICLETA">CONDUTOR OU PASSAGEIRO DE BICICLETA</OPTION>
														<OPTION VALUE="CONDUTOR OU PASSAGEIRO DE ÔNIBUS/VAN ">CONDUTOR OU PASSAGEIRO DE ÔNIBUS/VAN </OPTION>
														<OPTION VALUE="PEDESTRE">PEDESTRE</OPTION>
													</select>
												</div> 
											</td> 
											<td>   
												<select name="MudancaFaixa" id="MudancaFaixa" class="form-control num12">
													<option value="0">0</option>
													<option class='2' value="2">2</option>
													<option class='4' value="4">4</option>
													<option class='6' value="6">6</option>
													<option class='8' value="8">8</option>
													<option class='10' value="10">10</option>
												</select>
												<div id="_UsuarioContributivo_MudancaFaixa" >
													<label>Usuário Contributivo: </label><BR>
													<select name="UsuarioContributivo_MudancaFaixa" id="UsuarioContributivo_MudancaFaixa" class="form-control"  style="padding-right:0px;padding-left:5px;">
														<OPTION VALUE=""></OPTION>
														<OPTION VALUE="CONDUTOR OU PASSAGEIRO DE VEÍCULO LEVE">CONDUTOR OU PASSAGEIRO DE VEÍCULO LEVE</OPTION>
														<OPTION VALUE="CONDUTOR OU PASSAGEIRO DE VEÍCULO PESADO">CONDUTOR OU PASSAGEIRO DE VEÍCULO PESADO</OPTION>
														<OPTION VALUE="CONDUTOR OU PASSAGEIRO DE MOTO">CONDUTOR OU PASSAGEIRO DE MOTO</OPTION>
														<OPTION VALUE="CONDUTOR OU PASSAGEIRO DE BICICLETA">CONDUTOR OU PASSAGEIRO DE BICICLETA</OPTION>
														<OPTION VALUE="CONDUTOR OU PASSAGEIRO DE ÔNIBUS/VAN ">CONDUTOR OU PASSAGEIRO DE ÔNIBUS/VAN </OPTION>
														<OPTION VALUE="PEDESTRE">PEDESTRE</OPTION>
													</select>
												</div> 
											</td>
											<td>   
												<select name="DistanciaMinima" id="DistanciaMinima" class="form-control num12">
													<option value="0">0</option>
													<option class='2' value="2">2</option>
													<option class='4' value="4">4</option>
													<option class='6' value="6">6</option>
													<option class='8' value="8">8</option>
													<option class='10' value="10">10</option>
												</select>
												<div id="_UsuarioContributivo_DistanciaMinima" >
													<label>Usuário Contributivo: </label><BR>
													<select name="UsuarioContributivo_DistanciaMinima" id="UsuarioContributivo_DistanciaMinima" class="form-control"  style="padding-right:0px;padding-left:5px;">
														<OPTION VALUE=""></OPTION>
														<OPTION VALUE="CONDUTOR OU PASSAGEIRO DE VEÍCULO LEVE">CONDUTOR OU PASSAGEIRO DE VEÍCULO LEVE</OPTION>
														<OPTION VALUE="CONDUTOR OU PASSAGEIRO DE VEÍCULO PESADO">CONDUTOR OU PASSAGEIRO DE VEÍCULO PESADO</OPTION>
														<OPTION VALUE="CONDUTOR OU PASSAGEIRO DE MOTO">CONDUTOR OU PASSAGEIRO DE MOTO</OPTION>
														<OPTION VALUE="CONDUTOR OU PASSAGEIRO DE BICICLETA">CONDUTOR OU PASSAGEIRO DE BICICLETA</OPTION>
														<OPTION VALUE="CONDUTOR OU PASSAGEIRO DE ÔNIBUS/VAN ">CONDUTOR OU PASSAGEIRO DE ÔNIBUS/VAN </OPTION>
														<OPTION VALUE="PEDESTRE">PEDESTRE</OPTION>
													</select>
												</div> 
											</td>
											<td>   
												<select name="Preferencia" id="Preferencia" class="form-control num12">
													<option value="0">0</option>
													<option class='2' value="2">2</option>
													<option class='4' value="4">4</option>
													<option class='6' value="6">6</option>
													<option class='8' value="8">8</option>
													<option class='10' value="10">10</option>
												</select>
												<div id="_UsuarioContributivo_Preferencia" >
													<label>Usuário Contributivo: </label><BR>
													<select name="UsuarioContributivo_Preferencia" id="UsuarioContributivo_Preferencia" class="form-control"  style="padding-right:0px;padding-left:5px;">
														<OPTION VALUE=""></OPTION>
														<OPTION VALUE="CONDUTOR OU PASSAGEIRO DE VEÍCULO LEVE">CONDUTOR OU PASSAGEIRO DE VEÍCULO LEVE</OPTION>
														<OPTION VALUE="CONDUTOR OU PASSAGEIRO DE VEÍCULO PESADO">CONDUTOR OU PASSAGEIRO DE VEÍCULO PESADO</OPTION>
														<OPTION VALUE="CONDUTOR OU PASSAGEIRO DE MOTO">CONDUTOR OU PASSAGEIRO DE MOTO</OPTION>
														<OPTION VALUE="CONDUTOR OU PASSAGEIRO DE BICICLETA">CONDUTOR OU PASSAGEIRO DE BICICLETA</OPTION>
														<OPTION VALUE="CONDUTOR OU PASSAGEIRO DE ÔNIBUS/VAN ">CONDUTOR OU PASSAGEIRO DE ÔNIBUS/VAN </OPTION>
														<OPTION VALUE="PEDESTRE">PEDESTRE</OPTION>
													</select>
												</div> 
											</td>
											<td>   
												<select name="PreferenciaPedestre" id="PreferenciaPedestre" class="form-control num12">
													<option value="0">0</option>
													<option class='2' value="2">2</option>
													<option class='4' value="4">4</option>
													<option class='6' value="6">6</option>
													<option class='8' value="8">8</option>
													<option class='10' value="10">10</option>
												</select>
												<div id="_UsuarioContributivo_PreferenciaPedestre" >
													<label>Usuário Contributivo: </label><BR>
													<select name="UsuarioContributivo_PreferenciaPedestre" id="UsuarioContributivo_PreferenciaPedestre" class="form-control"  style="padding-right:0px;padding-left:5px;">
														<OPTION VALUE=""></OPTION>
														<OPTION VALUE="CONDUTOR OU PASSAGEIRO DE VEÍCULO LEVE">CONDUTOR OU PASSAGEIRO DE VEÍCULO LEVE</OPTION>
														<OPTION VALUE="CONDUTOR OU PASSAGEIRO DE VEÍCULO PESADO">CONDUTOR OU PASSAGEIRO DE VEÍCULO PESADO</OPTION>
														<OPTION VALUE="CONDUTOR OU PASSAGEIRO DE MOTO">CONDUTOR OU PASSAGEIRO DE MOTO</OPTION>
														<OPTION VALUE="CONDUTOR OU PASSAGEIRO DE BICICLETA">CONDUTOR OU PASSAGEIRO DE BICICLETA</OPTION>
														<OPTION VALUE="CONDUTOR OU PASSAGEIRO DE ÔNIBUS/VAN ">CONDUTOR OU PASSAGEIRO DE ÔNIBUS/VAN </OPTION>
														<OPTION VALUE="PEDESTRE">PEDESTRE</OPTION>
													</select>
												</div> 
											</td>
											<td>   
												<select name="ImprudenciaPedestre" id="ImprudenciaPedestre" class="form-control num12">
													<option value="0">0</option>
													<option class='2' value="2">2</option>
													<option class='4' value="4">4</option>
													<option class='6' value="6">6</option>
													<option class='8' value="8">8</option>
													<option class='10' value="10">10</option>
												</select>
												<div id="_UsuarioContributivo_ImprudenciaPedestre" >
													<label>Usuário Contributivo: </label><BR>
													<select name="UsuarioContributivo_ImprudenciaPedestre" id="UsuarioContributivo_ImprudenciaPedestre" class="form-control"  style="padding-right:0px;padding-left:5px;">
														<OPTION VALUE=""></OPTION>
														<OPTION VALUE="CONDUTOR OU PASSAGEIRO DE VEÍCULO LEVE">CONDUTOR OU PASSAGEIRO DE VEÍCULO LEVE</OPTION>
														<OPTION VALUE="CONDUTOR OU PASSAGEIRO DE VEÍCULO PESADO">CONDUTOR OU PASSAGEIRO DE VEÍCULO PESADO</OPTION>
														<OPTION VALUE="CONDUTOR OU PASSAGEIRO DE MOTO">CONDUTOR OU PASSAGEIRO DE MOTO</OPTION>
														<OPTION VALUE="CONDUTOR OU PASSAGEIRO DE BICICLETA">CONDUTOR OU PASSAGEIRO DE BICICLETA</OPTION>
														<OPTION VALUE="CONDUTOR OU PASSAGEIRO DE ÔNIBUS/VAN ">CONDUTOR OU PASSAGEIRO DE ÔNIBUS/VAN </OPTION>
														<OPTION VALUE="PEDESTRE">PEDESTRE</OPTION>
													</select>
												</div> 
											</td>
											<tr>
											</tbody>
										</table>
									</div>
									<div class="table-responsive">   
										<table class="table table-condensed table-responsive">
											<h4>Proteção Inadequada</h4>
											<thead>
												<tr>   

													<th>Cinto de segurança/<br>Dispositivo de contenção de criança</th>
													<th>Veículo <BR>sem equipamento<BR> de proteção</th>
													<th>Gerenciamento de trauma</th>
													<th>Objetos laterais na via</th>
													<th>Capacete</th>
													<th>Outros</th>
													<tr> 
													</thead>
													<tbody  id="FR_PI">
														<tr id="FR_PI">
															<td>
																<select name="CintoSeguranca" id="CintoSeguranca" class="form-control num10">
																	<option class="0" value="0">0</option>
																	<option class="1" value="1">1</option>
																	<option class="3" value="3">3</option>
																	<option class="5" value="5">5</option>

																</select>
																<div id="_UsuarioContributivo_CintoSeguranca">
																	<label>Usuário Contributivo: </label><BR>
																	<select name="UsuarioContributivo_CintoSeguranca" id="UsuarioContributivo_CintoSeguranca" class="form-control"  style="padding-right:0px;padding-left:5px;">
																		<OPTION VALUE=""></OPTION>
																		<OPTION VALUE="CONDUTOR OU PASSAGEIRO DE VEÍCULO LEVE">CONDUTOR OU PASSAGEIRO DE VEÍCULO LEVE</OPTION>
																		<OPTION VALUE="CONDUTOR OU PASSAGEIRO DE VEÍCULO PESADO">CONDUTOR OU PASSAGEIRO DE VEÍCULO PESADO</OPTION>
																		<OPTION VALUE="CONDUTOR OU PASSAGEIRO DE MOTO">CONDUTOR OU PASSAGEIRO DE MOTO</OPTION>
																		<OPTION VALUE="CONDUTOR OU PASSAGEIRO DE BICICLETA">CONDUTOR OU PASSAGEIRO DE BICICLETA</OPTION>
																		<OPTION VALUE="CONDUTOR OU PASSAGEIRO DE ÔNIBUS/VAN ">CONDUTOR OU PASSAGEIRO DE ÔNIBUS/VAN </OPTION>
																		<OPTION VALUE="PEDESTRE">PEDESTRE</OPTION>
																	</select>
																</div> 
															</td>
															<td>   
																<select name="EquipamentoProtecao" id="EquipamentoProtecao" class="form-control num10">
																	<option class="0" value="0">0</option>
																	<option class="1" value="1">1</option>
																	<option class="3" value="3">3</option>
																	<option class="5" value="5">5</option>
																</select>
															</td>
															<td>   
																<select name="GerenciamentoTrauma" id="GerenciamentoTrauma" class="form-control num10">
																	<option class="0" value="0">0</option>
																	<option class="1" value="1">1</option>
																	<option class="3" value="3">3</option>
																	<option class="5" value="5">5</option>
																</select>
															</td>
															<td>   
																<select name="ObjetosLateraisVia" id="ObjetosLateraisVia" class="form-control num10">
																	<option class="0" value="0">0</option>
																	<option class="1" value="1">1</option>
																	<option class="3" value="3">3</option>
																	<option class="5" value="5">5</option>
																</select>
															</td> 
															<td>   
																<select name="Capacete" id="Capacete" class="form-control num10">
																	<option class="0" value="0">0</option>
																	<option class="1" value="1">1</option>
																	<option class="3" value="3">3</option>
																	<option class="5" value="5">5</option>
																</select>
																<div id="_UsuarioContributivo_Capacete">
																	<label>Usuário Contributivo: </label><BR>
																	<select name="UsuarioContributivo_Capacete" id="UsuarioContributivo_Capacete" class="form-control"  style="padding-right:0px;padding-left:5px;">
																		<OPTION VALUE=""></OPTION>
																		<OPTION VALUE="CONDUTOR OU PASSAGEIRO DE VEÍCULO LEVE">CONDUTOR OU PASSAGEIRO DE VEÍCULO LEVE</OPTION>
																		<OPTION VALUE="CONDUTOR OU PASSAGEIRO DE VEÍCULO PESADO">CONDUTOR OU PASSAGEIRO DE VEÍCULO PESADO</OPTION>
																		<OPTION VALUE="CONDUTOR OU PASSAGEIRO DE MOTO">CONDUTOR OU PASSAGEIRO DE MOTO</OPTION>
																		<OPTION VALUE="CONDUTOR OU PASSAGEIRO DE BICICLETA">CONDUTOR OU PASSAGEIRO DE BICICLETA</OPTION>
																		<OPTION VALUE="CONDUTOR OU PASSAGEIRO DE ÔNIBUS/VAN ">CONDUTOR OU PASSAGEIRO DE ÔNIBUS/VAN </OPTION>
																		<OPTION VALUE="PEDESTRE">PEDESTRE</OPTION>
																	</select>
																</div> 
															</td> 
															<td>   
																<select name="outra_protecao" id="outra_protecao" class="form-control num10">
																	<option class="0" value="0">0</option>
																	<option class="1" value="1">1</option>
																	<option class="3" value="3">3</option>
																	<option class="5" value="5">5</option>
																</select>
																<div id="_definicao_outra_protecao">
																	<label>Definição: </label><BR>
																	<input type="text" class="form-control" id="definicao_outra_protecao" name="definicao_outra_protecao">
																</div> 
															</td>
															<tr>
															</tbody>
														</table>      
													</div>
													<div id="_DadosVitimas">   
														<h4>Dados das Vítimas 
															<button type="button" onClick="addVitima()" class="btn btn-primary"><span class="glyphicon glyphicon-plus"></span>Adicionar Vitima</button>    </h4>
															<div id='Dados_Vitima'>
															</div>
														</table>    
													</div>


													<button type='submit' id='salvar' class="btn btn-default pull-right">Salvar</button>
												</div></form>
											</div>

											@endsection
											{{-- Scripts Javascript --}}
											@section('scripts')

											<script src="{{ asset('libraries/js/bootstrap-table-toolbar.js') }}" type="text/javascript"></script>

											<script src="//oss.maxcdn.com/momentjs/2.8.2/moment.min.js"></script>
											@if (Auth::user()->tipo == 1)
											<script language="JavaScript" type="text/javascript" charset="utf-8">
												new dgCidadesEstados({
													cidade: document.getElementById('Cidade'),
													estado: document.getElementById('Estado')
												})
											</script>
											@endif
											<script language="JavaScript" type="text/javascript" charset="utf-8">
												new dgCidadesEstados({
													cidade: document.getElementById('MunicipioAcidente'),
													estado: document.getElementById('EstadoAcidente')
												})
											</script>

											<script type="text/javascript">
												$(document).ready(function(){

													@if( request()->get('Trimestre') && request()->get('Ano') )
													$("#Trimestre").trigger('change');
													@endif
													@if( \Session::has('Ano') && \Session::has('CodCidade') && \Session::has('Trimestre') )
													$("#CodCidade").val('{{\Session::get("CodCidade")}}');
													$("#Trimestre").trigger('change');
													@endif


												});

												var table = $('#table').DataTable({
													processing: true,
													serverSide: true,
													responsive: false,
													deferLoading: 0,
													ajax: {
														type: 'POST',
														dataType: "json",
														url: "{{ route('dataQuadroMultiplo') }}",
														headers: {
															'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
														},
														data: function (d) {
															d.Filtro = $('#Filtro_Acidentes').val();
															d.Trimestre = $('#Trimestre').val();
															d.CodCidade = $('#CodCidade').val();
															d.Ano = $('#Ano').val();
														}
													},
													language: {
														url: "{{ asset('libs/Datatables/Portuguese-Brasil.json') }}"
													},
													rowId: 'id',
													columns: [
													{
														"className":      'details-control',
														"orderable":      false,
														"data":           null,
														"searchable":           false,
														"defaultContent": ''
													},
													{ data: 'IdentificadorAcidente' },
													{ data: 'TipoAcidente' },
													{ data: 'qtdfatais', "searchable": false, 
													render: function ( data, type, row, meta ) {
														return (data != null?data:'0'); 
													},
												}, 
												{ data: 'qtdleves', "searchable": false,
												render: function ( data, type, row, meta ) {
													return (data != null?data:'0'); 
												},
											},
											{ data: 'DataAcidente' },    
											{ data: 'HoraAcidente' },
											{ data: 'RuaAvenida' },
											{ data: 'Bairro' },
											{ data: 'CidadeAcidente', 
											render: function ( data, type, row, meta ) 
											{
												return (row.CidadeAcidente != null?row.CidadeAcidente:'') + ' '+ (row.EstadoAcidente != null?row.EstadoAcidente:'')     } 
											},
											{ data: 'NomeVitimas', name:'vitimas.NomeCompleto', "visible": false, "orderable": false  },
											{ data: 'acoes', render: function( data, type, row, meta ) {
												return '<div class="btn-group">'+
												'<button data-toggle="dropdown" class="btn btn-primary btn-xs btn-flat dropdown-toggle"><i class="fa fa-gear"></i> <span class="caret"></span>'+
												'</button>'+
												'<ul class="dropdown-menu pull-right">'+
												'<li>'+
												'<a onClick="editar('+meta.row+')">Editar</a>'+
												'</li>'+
												'<li>'+
												'<a onClick="deletar('+row.id+')">Deletar</a>'+
												'</li>'+
												'</ul></div>';
											}},
											],
											rowCallback: function ( row, data, index ) {

												if( typeof(data.qtdfatais) != 'undefined'){
													if (data.TotalFatores == 'Preenchidos'){
														$(row).addClass('FatoresPreenchidos');
													}
													if( data.qtdfatais >=  1 && data.TotalFatores == 'Não Preenchido'){
														$(row).addClass('FatoresDevemPreenchidos');
													}
												}
											},
											drawCallback: function( settings){
												var api = this.api();
												var data =  api.rows().data();


											}

										});

									</script>
									<script type="text/javascript">
										function resetAllValues() {
											$('#_QuadroMultiplo div[id^="_"]').hide();
											$('#_QuadroMultiplo').hide();
											$('#_QuadroMultiplo').find('input').val('');
											$('#_QuadroMultiplo').find(".num10").val(0).change();
											$('#_QuadroMultiplo').find(".num11").val(0).change();
											$('#_QuadroMultiplo').find(".num12").val(0).change();
											$('#Dados_Vitima').html('');


											$('#qtdVitimas').val(0);
											$('#adicionarAcidente').show();

											$('#_QuadroMultiplo').find('.has-success').removeClass('has-success')
											$('#_QuadroMultiplo').find('.has-error').removeClass('has-error')
											$('#_QuadroMultiplo').find('.help-block').hide()
											$('#_QuadroMultiplo').find('.fv-icon-no-label').hide()

											$(".print-error-msg").find("ul").html('');
											$(".print-error-msg").css('display','none');

										}
										@if(Auth::user()->tipo == 1)
										$('#Cidade').change(function () {
											resetAllValues()
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


   // Add event listener for opening and closing details
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
   function format ( d ) {
    // `d` is the original data object for the row
    return '<table cellpadding="5" cellspacing="0" border="0" style="padding-left:50px;">'+
    '<tr>'+
    '<td>Vitimas:</td>'+
    '<td>'+d.NomeVitimas+'</td>'+
    '</tr>'+
    '</table>';
}

   	//fatores de risco
   	$('#Velocidade').change(function () {
   		if ($('#Velocidade').val() != '0') {
   			$('#_TipoVelocidade').show();
   			$('#_UsuarioContributivo_Velocidade').show();
   			$( "#_TipoVelocidade" ).focus();
   		} else {
   			$('#_UsuarioContributivo_Velocidade').hide();
   			$('#_TipoVelocidade').hide();
   		}
   	});
   	$('#Drogas').change(function () {
   		if ($('#Drogas').val() != '0') {
   			$('#_UsuarioContributivo_Drogas').show();
   			$( "#_UsuarioContributivo_Drogas" ).focus();
   		} else {
   			$('#_UsuarioContributivo_Drogas').hide();   	
   		}
   	});
   	$('#Distacao').change(function () {
   		if ($('#Distacao').val() != '0') {
   			$('#_UsuarioContributivo_Distacao').show();
   			$( "#_UsuarioContributivo_Distacao" ).focus();
   		} else {
   			$('#_UsuarioContributivo_Distacao').hide();   	
   		}
   	});
   	$('#Fadiga').change(function () {
   		if ($('#Fadiga').val() != '0') {
   			$('#_UsuarioContributivo_Fadiga').show();
   			$( "#_UsuarioContributivo_Fadiga" ).focus();
   		} else {
   			$('#_UsuarioContributivo_Fadiga').hide();   	
   		}
   	});
   	$('#Veiculo').change(function () {
   		if ($('#Veiculo').val() != '0') {
   			$('#_UsuarioContributivo_Veiculo').show();
   			$( "#_UsuarioContributivo_Veiculo" ).focus();
   		} else {
   			$('#_UsuarioContributivo_Veiculo').hide();   	
   		}
   	});
   	$('#Infraestrutura').change(function () {
   		if ($('#Infraestrutura').val() != '0') {
   			$('#_TipoInfraestrutura').show();
   			$( "#_TipoInfraestrutura" ).focus();
   		} else {
   			$('#_TipoInfraestrutura').hide();   	
   		}
   	});
   	$('#Alcool').change(function () {
   		if ($('#Alcool').val() != '0') {
   			$('#_UsuarioContributivo_Alcool').show();
   			$( "#_UsuarioContributivo_Alcool" ).focus();
   		} else {
   			$('#_UsuarioContributivo_Alcool').hide();   	
   		}
   	});
   	//Condutas locais
   	$('#AvancarSinal').change(function () {
   		if ($('#AvancarSinal').val() != '0') {
   			$('#_UsuarioContributivo_AvancarSinal').show();
   			$( "#_UsuarioContributivo_AvancarSinal" ).focus();
   		} else {
   			$('#_UsuarioContributivo_AvancarSinal').hide();
   		}
   	});
   	$('#LocalProibido').change(function () {
   		if ($('#LocalProibido').val() != '0') {
   			$('#_UsuarioContributivo_LocalProibido').show();
   			$( "#_UsuarioContributivo_LocalProibido" ).focus();
   		} else {
   			$('#_UsuarioContributivo_LocalProibido').hide();
   		}
   	});
   	$('#CondutorSemHabilitacao').change(function () {
   		if ($('#CondutorSemHabilitacao').val() != '0') {
   			$('#_UsuarioContributivo_CondutorSemHabilitacao').show();
   			$( "#_UsuarioContributivo_CondutorSemHabilitacao" ).focus();
   		} else {
   			$('#_UsuarioContributivo_CondutorSemHabilitacao').hide();
   		}
   	});
   	$('#LocalImproprio').change(function () {
   		if ($('#LocalImproprio').val() != '0') {
   			$('#_UsuarioContributivo_LocalImproprio').show();
   			$( "#_UsuarioContributivo_LocalImproprio" ).focus();
   		} else {
   			$('#_UsuarioContributivo_LocalImproprio').hide();
   		}
   	});
   	$('#MudancaFaixa').change(function () {
   		if ($('#MudancaFaixa').val() != '0') {
   			$('#_UsuarioContributivo_MudancaFaixa').show();
   			$( "#_UsuarioContributivo_MudancaFaixa").focus();
   		} else {
   			$('#_UsuarioContributivo_MudancaFaixa').hide();
   		}
   	});
   	$('#DistanciaMinima').change(function () {
   		if ($('#DistanciaMinima').val() != '0') {
   			$('#_UsuarioContributivo_DistanciaMinima').show();
   			$( "#_UsuarioContributivo_DistanciaMinima").focus();
   		} else {
   			$('#_UsuarioContributivo_DistanciaMinima').hide();
   		}
   	});
   	$('#Preferencia').change(function () {
   		if ($('#Preferencia').val() != '0') {
   			$('#_UsuarioContributivo_Preferencia').show();
   			$( "#_UsuarioContributivo_Preferencia").focus();
   		} else {
   			$('#_UsuarioContributivo_Preferencia').hide();
   		}
   	});
   	$('#PreferenciaPedestre').change(function () {
   		if ($('#PreferenciaPedestre').val() != '0') {
   			$('#_UsuarioContributivo_PreferenciaPedestre').show();
   			$( "#_UsuarioContributivo_PreferenciaPedestre").focus();
   		} else {
   			$('#_UsuarioContributivo_PreferenciaPedestre').hide();
   		}
   	});
   	$('#ImprudenciaPedestre').on('change',function () {
   		if ($('#ImprudenciaPedestre').val() != '0') {
   			$('#_UsuarioContributivo_ImprudenciaPedestre').show();
   			$( "#_UsuarioContributivo_ImprudenciaPedestre").focus();
   		} else {
   			$('#_UsuarioContributivo_ImprudenciaPedestre').hide();
   		}
   	});
    //Proteção Inadequada
    $('#CintoSeguranca').on('change',function () {
    	if ($('#CintoSeguranca').val() != '0') {
    		$('#_UsuarioContributivo_CintoSeguranca').show();
    		$( "#_UsuarioContributivo_CintoSeguranca").focus();
    	} else {
    		$('#_UsuarioContributivo_CintoSeguranca').hide();
    	}
    });
    $('#Capacete').on('change', function () {
    	if ($('#Capacete').val() != '0') {
    		$('#_UsuarioContributivo_Capacete').show();
    		$( "#_UsuarioContributivo_Capacete").focus();
    	} else {
    		$('#_UsuarioContributivo_Capacete').hide();
    	}
    });
    $('#outra_protecao').on('change', function () {
    	if ($('#outra_protecao').val() != '0') {
    		$('#_definicao_outra_protecao').show();
    		$( "#_definicao_outra_protecao").focus();
    	} else {
    		$('#_definicao_outra_protecao').hide();
    	}
    });

    $('#QuadroMultiplo').on('submit', function(e) {
    	e.preventDefault();
    	$.isLoading({ text: "Carregando ... "}); 
    	var retorno = 1;
    	$(".print-error-msg").find("ul").html('');
    	$(".print-error-msg").css('display','none');
    	$('#_QuadroMultiplo').find('.has-success').removeClass('has-success')
    	$('#_QuadroMultiplo').find('.has-error').removeClass('has-error')
   		//validações
   		//valida proteçoes inadequadas
   		var inputs1 = new Array();
   		$('.num10').each(function () {
   			if ((this.value != undefined) && (this.value != null) && (this.value != '') && (this.value != '0')) {
   				if ($.inArray(this.value, inputs1) != -1)
   				{
   					$('.num10').focus()
   					alertify.alert('Verifique os pesos das proteções inadequadas', 'os valores dos pesos não podem ser repetidos, com exceção do valor 0');
   					retorno = 0;
   				}
   				inputs1.push(this.value);
   			}
   		});
   		//valida fatores de risco
   		var inputs = new Array();
   		$('.num11').each(function () {
   			if ((this.value != undefined) && (this.value != null) && (this.value != '0') && (this.value != '')) {
   				if ($.inArray(this.value, inputs) != -1)
   				{
   					$('.num11').focus()
   					alertify.alert('Verifique os pesos dos fatores de risco', 'os valores dos pesos não podem ser repetidos, com exceção do valor 0');
   					retorno = 0;
   				}
   				inputs.push(this.value);
   			}
   		});   
   		//valida locais de risco             
   		var inputs12 = new Array();
   		$('.num12').each(function () {
   			if ((this.value != undefined) && (this.value != null) && (this.value != '0') && (this.value != '')) {
   				if ($.inArray(this.value, inputs12) != -1)
   				{
   					$('.num12').focus()
   					alertify.alert('Verifique os pesos das Condutas locais de risco', 'os valores dos pesos não podem ser repetidos, com exceção do valor 0');
   					retorno = 0;
   				}
   				inputs12.push(this.value);                    }
   			});
   		//valida valores 
   		if ($('#FonteDados').val() == '') {
   			$('#FonteDados').closest('td').addClass('has-error');
   			retorno = 0;
   		}
   		if ($('#IdentificadorAcidente').val() == '') {
   			$('#IdentificadorAcidente').closest('td').addClass('has-error');
   			retorno = 0;
   		}
   		if (parseInt($('#HoraAcidente').val()) > 23 || parseInt($('#HoraAcidente').val()) < 0) {
   			$('#HoraAcidente').closest('td').addClass('has-error');
   			retorno = 0;
   		}
   		if ($('#TipoAcidente').val() == '') {
   			$('#TipoAcidente').closest('td').addClass('has-error');
   			retorno = 0;
   		}
   		if ($('#AvancarSinal').val() != '0'  && $('#UsuarioContributivo_AvancarSinal').val() == '') {
   			$('#AvancarSinal').closest('td').addClass('has-error');
   			retorno = 0;
   		}
   		if ($('#CondutorSemHabilitacao').val() != '0'  && $('#UsuarioContributivo_CondutorSemHabilitacao').val() == '') {
   			$('#CondutorSemHabilitacao').closest('td').addClass('has-error');
   			retorno = 0;
   		}
   		if ($('#LocalProibido').val() != '0'  && $('#UsuarioContributivo_LocalProibido').val() == '') {
   			$('#LocalProibido').closest('td').addClass('has-error');
   			retorno = 0;
   		}
   		if ($('#LocalImproprio').val() != '0'  && $('#UsuarioContributivo_LocalImproprio').val() == '') {
   			$('#LocalImproprio').closest('td').addClass('has-error');
   			retorno = 0;
   		}
   		if ($('#MudancaFaixa').val() != '0'  && $('#UsuarioContributivo_MudancaFaixa').val() == '') {
   			$('#MudancaFaixa').closest('td').addClass('has-error');
   			retorno = 0;
   		}
   		if ($('#DistanciaMinima').val() != '0'  && $('#UsuarioContributivo_DistanciaMinima').val() == '') {
   			$('#DistanciaMinima').closest('td').addClass('has-error');
   			retorno = 0;
   		}
   		if ($('#Preferencia').val() != '0'  && $('#UsuarioContributivo_Preferencia').val() == '') {
   			$('#Preferencia').closest('td').addClass('has-error');
   			retorno = 0;
   		}
   		if ($('#PreferenciaPedestre').val() != '0'  && $('#UsuarioContributivo_PreferenciaPedestre').val() == '') {
   			$('#PreferenciaPedestre').closest('td').addClass('has-error');
   			retorno = 0;
   		}
   		if ($('#ImprudenciaPedestre').val() != '0'  && $('#UsuarioContributivo_ImprudenciaPedestre').val() == '') {
   			$('#ImprudenciaPedestre').closest('td').addClass('has-error');
   			retorno = 0;
   		}
   		if ($('#CintoSeguranca').val() != '0'  && $('#UsuarioContributivo_CintoSeguranca').val() == '') {
   			$('#CintoSeguranca').closest('td').addClass('has-error');
   			retorno = 0;
   		}
   		if ($('#Capacete').val() != '0'  && $('#UsuarioContributivo_Capacete').val() == '') {
   			$('#Capacete').closest('td').addClass('has-error');
   			retorno = 0;
   		}
   		if ($('#outra_protecao').val() != '0'  && $('#definicao_outra_protecao').val() == '') {
   			$('#outra_protecao').closest('td').addClass('has-error');
   			retorno = 0;
   		}
   		if ($('#Alcool').val() != '0'  && $('#UsuarioContributivo_Alcool').val() == '') {
   			$('#Alcool').closest('td').addClass('has-error');
   			retorno = 0;
   		}
   		if ($('#Infraestrutura').val() != '0'  && $('#TipoInfraestrutura').val() == '') {
   			$('#Infraestrutura').closest('td').addClass('has-error');
   			retorno = 0;
   		}
   		if ($('#Veiculo').val() != '0'  && $('#UsuarioContributivo_Veiculo').val() == '') {
   			$('#Veiculo').closest('td').addClass('has-error');
   			retorno = 0;
   		}
   		if ($('#Fadiga').val() != '0'  && $('#UsuarioContributivo_Fadiga').val() == '') {
   			$('#Fadiga').closest('td').addClass('has-error');
   			retorno = 0;
   		}
   		if($('#Drogas').val() != '0'  
   			&& ($('#UsuarioContributivo_Drogas').val() == ''
   				|| $('#TipoDroga').val() == '')) 
   		{
   			$('#Drogas').closest('td').addClass('has-error');
   			retorno = 0;
   		}
   		if($('#Distacao').val() != '0'  
   			&& ($('#UsuarioContributivo_Distacao').val() == ''
   				|| $('#TipoDistracao').val() == '')) 
   		{
   			$('#Distacao').closest('td').addClass('has-error');
   			retorno = 0;
   		}
   		if ($('#Velocidade').val() != '0'  
   			&& ($('#TipoVelocidade').val() == ''
   				|| $('#UsuarioContributivo_Velocidade').val() == '')) 
   		{
   			$('#Velocidade').closest('td').addClass('has-error');
   			retorno = 0;
   		}
   		let i;
   		var qtdVitimas = parseInt($('#qtdVitimas').val());
   		for (i = 1; i <= qtdVitimas; i++) {
   			if($('#VitimaNome'+i).val() == '' ){
   				$('#VitimaNome'+i).closest('div').addClass('has-error');
   				retorno = 0;
   			}
   			if($('#VitimaDataNascimento'+i).val() == '' ){
   				$('#VitimaDataNascimento'+i).closest('div').addClass('has-error');
   				retorno = 0;
   			}
   		} 
		//envia form se estiver tudo certo
		if(retorno == 1){
   			//envia form
   			var form = $(this);
   			$.ajax({
   				url: '{{ route('store.quadro')}}',
   				type: "POST",
   				async: false,
   				headers: {
   					'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
   				},
   				data: form.serialize(),
   				success: function (data, textStatus, jqXHR)
   				{
   					if($.isEmptyObject(data.error)){
   					//sucesso
   					resetAllValues();
   					$('#table').DataTable().ajax.reload();
   					alertify.success('Salvo com sucesso');
   				}else{
   					printErrorMsg(data.error);
   					$( "#_QuadroMultiplo" ).focus();
   				}
   				//console.log('teste',data, textStatus, jqXHR)
   			},
   			error: function (jqXHR, textStatus, errorThrown)
   			{
   				if(jqXHR.status == 419){
   					location.reload();
   				}
   				console.log(jqXHR, textStatus, errorThrown);
   				alertify.alert('Erro ao gravar', jqXHR.responseText);
   			}
   		});
   		}else{
   			$( "#_QuadroMultiplo" ).focus();
   			alertify.success('Verifique as pendências');
   		}
   		$.isLoading('hide');

   	});


function deletar(id){
	event.preventDefault();
	alertify.confirm('Tem Certeza?', 'Deseja deletar esse acidente? todas as vitimas serão deletadas', function(){ 
		$.ajax({
			url: "{{ route('delete.acidente') }}",
			type: "POST",
			headers: {
				'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
			},
			data: { id_acidente: id },
			success: function(s) {
				$('#table').DataTable().ajax.reload();
				alertify.success('Deletado com sucesso');
			},
			error(){
				alertify.success('Erro ao deletar vitima');
			}
		});
	}, function(){ alertify.error('Cancelado')});
}
function deleta_vitima(id){
	var nova = $('#nova'+id).val()
	var div = $('#Dados_Vitima'+id)
	event.preventDefault();
	alertify.confirm('Tem Certeza?', 'Deseja deletar a vitima', 
		function(){ 
			if(nova == true){
				div.remove()
			}else{
				$.ajax({
					url: "{{ route('delete.vitima') }}",
					type: "POST",
					headers: {
						'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
					},
					data: { id_vitima: $('#id_vitima'+id).val() },
					success: function(s) {
						div.remove()
						alertify.success('Deletado com sucesso');
					},
					error(){
						alertify.success('Erro ao deletar vitima');
					}
				});
			}

		}, function(){ alertify.error('Cancelado')});
}

function editar(id) {
	$.isLoading({ text: "Carregando ... "}); 
	$('#resultadosQM').hide();
	$('#loading-image').show();
	event.preventDefault();
	resetAllValues();

	var table = $('#table').DataTable();
	$( "#_QuadroMultiplo" ).focus();
	var data =  table.rows().data()[id]
	console.log(data)
	$('#FonteDados').val(data.FonteDados);
	$('#IdentificadorAcidente').val(data.Boletim);
	if(data.IdentificadorAcidente2 != null){
		var id2 = data.IdentificadorAcidente2.split('/');
		$('#FonteDados2').val(id2[0]);
		$('#IdentificadorAcidente2').val(id2[1]);
	}  
	if(data.IdentificadorAcidente3 != null){
		var id3 = data.IdentificadorAcidente3.split('/');
		$('#FonteDados3').val(id3[0]);
		$('#IdentificadorAcidente3').val(id3[1]);
	}
	$('#DataAcidente').val(data.DataAcidente);
	$('#HoraAcidente').val(data.HoraAcidente);

	if(data.TipoAcidente == '' || data.TipoAcidente == null){
		$('#TipoAcidente').val('NAO INFORMADO');
	}else{                            
		$('#TipoAcidente').val(data.TipoAcidente);
	}
	$('#idQuadroMultiplo').val(data.id);
	$('#CoordX').val(data.CoordX);
	$('#CoordY').val(data.CoordY);
	$('#Endereco').val(data.RuaAvenida);
	$('#Numero').val(data.Numero);
	$('#Complemento').val(data.Complemento);
	$('#velocidade_via').val(data.velocidade_via);
	$('#Bairro').val(data.Bairro);
	$('#EstadoAcidente').val(data.EstadoAcidente);
	$('#EstadoAcidente').trigger('change');
	$('#MunicipioAcidente option').each(function () {
		$(this).val(replaceSpecialChars($(this).val().toUpperCase()));
	});
	$('#MunicipioAcidente').val(data.CidadeAcidente);
	$('#MunicipioAcidente').trigger('change');
	$('#CEP').val(data.CepAcidente);
   	//Preenche fatores de risco
   	if (data.fatores_risco != null) {
   		$('#Velocidade').val(data.fatores_risco.Velocidade);
   		$('#Velocidade').trigger("change");
   		$('#TipoVelocidade').val(data.fatores_risco.TipoVelocidade);
   		$('#UsuarioContributivo_Velocidade').val(data.fatores_risco.UsuarioContributivo_Velocidade);
   		$('#Alcool').val(data.fatores_risco.Alcool);
   		$('#Alcool').trigger("change");
   		$('#UsuarioContributivo_Alcool').val(data.fatores_risco.UsuarioContributivo_Alcool);
   		$('#Veiculo').val(data.fatores_risco.Veiculo);
   		$('#Veiculo').trigger("change");
   		$('#UsuarioContributivo_Veiculo').val(data.fatores_risco.UsuarioContributivo_Veiculo);
   		$('#Infraestrutura').val(data.fatores_risco.Infraestrutura);
   		$('#Infraestrutura').trigger("change");
   		$('#TipoInfraestrutura').val(data.fatores_risco.TipoInfraestrutura);
   		$('#Fadiga').val(data.fatores_risco.Fadiga);
   		$('#Fadiga').trigger("change");
   		$('#UsuarioContributivo_Fadiga').val(data.fatores_risco.UsuarioContributivo_Fadiga);

   		$('#Visibilidade').val(data.fatores_risco.Visibilidade);
   		$('#Drogas').val(data.fatores_risco.Drogas);
   		$('#Drogas').trigger("change");
   		$('#UsuarioContributivo_Drogas').val(data.fatores_risco.UsuarioContributivo_Drogas);
   		$('#TipoDroga').val(data.fatores_risco.TipoDroga);

   		$('#Distacao').val(data.fatores_risco.Distacao);
   		$('#Distacao').trigger("change");
   		$('#UsuarioContributivo_Distacao').val(data.fatores_risco.UsuarioContributivo_Distacao);

   		$('#AvancarSinal').val(data.fatores_risco.AvancarSinal);
   		$('#AvancarSinal').trigger("change");
   		$('#UsuarioContributivo_AvancarSinal').val(data.fatores_risco.UsuarioContributivo_AvancarSinal);

   		$('#CondutorSemHabilitacao').val(data.fatores_risco.CondutorSemHabilitacao);
   		$('#CondutorSemHabilitacao').trigger("change");
   		$('#UsuarioContributivo_CondutorSemHabilitacao').val(data.fatores_risco.UsuarioContributivo_CondutorSemHabilitacao);

   		$('#LocalProibido').val(data.fatores_risco.LocalProibido);
   		$('#LocalProibido').trigger("change");
   		$('#UsuarioContributivo_LocalProibido').val(data.fatores_risco.UsuarioContributivo_LocalProibido);

   		$('#LocalImproprio').val(data.fatores_risco.LocalImproprio);
   		$('#LocalImproprio').trigger("change");
   		$('#UsuarioContributivo_LocalImproprio').val(data.fatores_risco.UsuarioContributivo_LocalImproprio);

   		$('#MudancaFaixa').val(data.fatores_risco.MudancaFaixa);
   		$('#MudancaFaixa').trigger("change");
   		$('#UsuarioContributivo_MudancaFaixa').val(data.fatores_risco.UsuarioContributivo_MudancaFaixa);

   		$('#DistanciaMinima').val(data.fatores_risco.DistanciaMinima);
   		$('#DistanciaMinima').trigger("change");
   		$('#UsuarioContributivo_DistanciaMinima').val(data.fatores_risco.UsuarioContributivo_DistanciaMinima);

   		$('#Preferencia').val(data.fatores_risco.Preferencia);
   		$('#Preferencia').trigger("change");
   		$('#UsuarioContributivo_Preferencia').val(data.fatores_risco.UsuarioContributivo_Preferencia);

   		$('#PreferenciaPedestre').val(data.fatores_risco.PreferenciaPedestre);
   		$('#PreferenciaPedestre').trigger("change");
   		$('#UsuarioContributivo_PreferenciaPedestre').val(data.fatores_risco.UsuarioContributivo_PreferenciaPedestre);

   		$('#ImprudenciaPedestre').val(data.fatores_risco.ImprudenciaPedestre);
   		$('#ImprudenciaPedestre').trigger("change");
   		$('#UsuarioContributivo_ImprudenciaPedestre').val(data.fatores_risco.UsuarioContributivo_ImprudenciaPedestre);

   		$('#CintoSeguranca').val(data.fatores_risco.CintoSeguranca);
   		$('#CintoSeguranca').trigger("change");
   		$('#UsuarioContributivo_CintoSeguranca').val(data.fatores_risco.UsuarioContributivo_CintoSeguranca);

   		$('#EquipamentoProtecao').val(data.fatores_risco.EquipamentoProtecao);
   		$('#GerenciamentoTrauma').val(data.fatores_risco.GerenciamentoTrauma);
   		$('#ObjetosLateraisVia').val(data.fatores_risco.ObjetosLateraisVia);

   		$('#Capacete').val(data.fatores_risco.Capacete);
   		$('#Capacete').trigger("change");
   		$('#UsuarioContributivo_Capacete').val(data.fatores_risco.UsuarioContributivo_Capacete);
   	}else{
   		$(".num10").val(0).change();
   		$(".num11").val(0).change();
   		$(".num12").val(0).change();
   	}

   	$.each(data.vitimas, function (key, val) {
   		addVitima(val.id)
   		//console.log(val, key)

   		$('#VitimaInfluencia' + val.id).val(val.InfluenciaAlcool);
   		$('#VitimaInfluencia' + val.id).trigger('change');
   		$('#ComprovaAlcool' + val.id).val(val.ComprovaAlcoolemia);
   		$('#ComprovaAlcool' + val.id).trigger('change');
   		$('#ValorAlcoolemia' + val.id).val(val.ValorAlcoolemia);
   		$('#ComprovaBafometro' + val.id).val(val.ComprovaBafometro);
   		$('#ComprovaBafometro' + val.id).trigger('change');
   		$('#ValorBafometro' + val.id).val(val.ValorBafometro);

   		$('#CoordVitimaX' + val.id).val(val.CoordVitimaX);
   		$('#CoordVitimaY' + val.id).val(val.CoordVitimaY);
   		$('#EnderecoVitima' + val.id).val(val.EnderecoVitima);
   		$('#CEPVitima' + val.id).val(val.CEPVitima);
   		$('#BairroVitima' + val.id).val(val.BairroVitima);
   		$('#NumeroVitima' + val.id).val(val.NumeroVitima);
   		$('#EstadoVitima' + val.id).val(val.EstadoVitima).trigger('change');
   		$('#MunicipioVitima' + val.id).val(val.MunicipioVitima).trigger('change');


   		$('#TipoVitima' + val.id).val(val.GravidadeLesao);
   		$('#id_vitima' + val.id).val(val.id);
   		$('#VitimaNome' + val.id).val(val.NomeCompleto);
   		$('#VitimaNomeMae' + val.id).val(val.NomeMae);
   		$('#VitimaDataNascimento' + val.id).val(val.DataNascimento);
   		$('#SexoVitima' + val.id).val(val.Sexo);
   		$('#MeioVitima' + val.id).val(val.MeioTransporte);

   		if(typeof(val.linkagem_sim) != 'undefined' && val.linkagem_sim != null){
   			var valorLinkagemSIM = '';
   			var linkagemSIM = val.linkagem_sim;
   			if(linkagemSIM.ParVerdadeiro == 1){
   				$('#PAR_VITIMA_' + val.id).html("Verdadeiro");
   				$('#SIM_CAUSABAS_' + val.id).html('<label> CAUSABASE: </label> '+ linkagemSIM.sim.CAUSABAS);
   				$('#SIM_DO_' + val.id).html('<label> DO: </label> '+ linkagemSIM.sim.NUMERODO);
   			}else if (linkagemSIM.ParVerdadeiro == 0){
   				$('#PAR_VITIMA_' + val.id).html("Falso ");
   				//$('#PAR_VITIMA_' + val.id).html("Falso "+(linkagemSIM.TipoFalso != null ? linkagemSIM.TipoFalso : ''));
   			}else{
   				$('#PAR_VITIMA_' + val.id).html("Par Linkado mas não verificado");
   			}
   		}   		
   		if(typeof(val.linkagem_sih) != 'undefined' && val.linkagem_sih != null){
   			var linkagemSIH = val.linkagem_sih;
   			if(linkagemSIH.ParVerdadeiro == 1){
   				$('#PAR_VITIMA_SIH_' + val.id).html("Verdadeiro");
   				$('#DIAGPRINCIPAL' + val.id).html('<label> DIAG_PRI: </label> '+ linkagemSIH.sih.DIAG_PRI);
   				$('#AIH' + val.id).html('<label> AIH: </label> '+ linkagemSIH.sih.NUM_AIH);
   			}else if (linkagemSIH.ParVerdadeiro == 0){
   				$('#PAR_VITIMA_SIH_' + val.id).html("Falso ");
   			}else{
   				$('#PAR_VITIMA_SIH_' + val.id).html("Par Linkado mas não verificado");
   			}
   		}


   		$('#descricao_' + val.id).val(val.Descricao);
   		$('#CBO' + val.id).val(val.CBO);
   		$('#Placa' + val.id).val(val.Placa);
   		$('#NUMSUS' + val.id).val(val.NUMSUS);
   		$('#resultadosQM').show();
   		$('#loading-image').hide();
   	});
   	$('#DataAcidente').datepicker({
   		language: "pt-BR",
   		autoclose: true,
   		format: 'dd/mm/yyyy',
   		endDate:  new Date(),
   		todayHighlight: true
   	});
   	$('#_QuadroMultiplo').show();
   	$('#_DadosVitimas').show();
   	$.isLoading('hide');
   	$(window).scrollTop($('#_QuadroMultiplo').offset().top);
   	$('#_QuadroMultiplo').focus();
   	$('#adicionarAcidente').hide();
   }
   $('#adicionarAcidente').click(function () {
   	resetAllValues();
   	$('#_QuadroMultiplo').show();
   	$('#_DadosVitimas').show();
   	$('#_QuadroMultiplo').focus();
   	$('#adicionarAcidente').hide();
   	$('#DataAcidente').datepicker({
   		language: "pt-BR",
   		autoclose: true,
   		format: 'dd/mm/yyyy',
   		endDate:  new Date(),
   		todayHighlight: true
   	});
   });
   function addVitima(idVitima = null){
   	var nova 
   	if(idVitima == null){
   		nova = true;
   		idVitima = $('#idQuadroMultiplo').val()+$('#qtdVitimas').val();
   		if(typeof($("#id_vitima"+idVitima).val()) != 'undefined'){
   			idVitima = $("#id_vitima"+idVitima).val() * randomInt( 10, 20);
   		}
   	}else{
   		nova = false;
   	}
   	$('#qtdVitimas').val($('#qtdVitimas').val()+1);
   	$('#Dados_Vitima').append('<div id="Dados_Vitima' + idVitima + '">\n\
   		<input type="hidden" name="nova[]" id="nova'+idVitima+'" value="'+nova+'"">\n\
   		<input type="hidden" name="id_vitima[]" class="form-control" id="id_vitima'+idVitima+'" value="'+(nova == true ? null : idVitima)+'"">\n\
   		<div class="row">\n\
   		<div class="col-md-2 form-group">\n\
   		<label for="usr">Tipo Vítima:</label>\n\
   		<select name="TipoVitima[]" id="TipoVitima' + idVitima + '" class="form-control" >\n\
   		<OPTION VALUE=""></OPTION>\N\\n\
   		<OPTION VALUE="SEM LESOES">SEM LESOES</OPTION>\N\\n\
   		<OPTION VALUE="COM LESOES">COM LESOES</OPTION>\N\\n\
   		<OPTION VALUE="LEVE">LESOES LEVES</OPTION>\N\
   		<OPTION VALUE="MODERADA">LESOES MODERADAS</OPTION>\N\
   		<OPTION VALUE="GRAVE">LESOES GRAVE</OPTION>\N\
   		<OPTION VALUE="FATAL">FATAL</OPTION>\N\
   		<OPTION VALUE="FATAL LOCAL">FATAL LOCAL</OPTION>\N\
   		<OPTION VALUE="FATAL POSTERIOR">FATAL POSTERIOR</OPTION>\N\
   		<OPTION VALUE="LESOES NAO ESPECIFICADAS">LESOES NAO ESPECIFICADAS</OPTION>\N\
   		<OPTION VALUE="NAO INFORMADO">NÃO INFORMADO</OPTION>\N\
   		</select>\n\
   		</div>\n\
   		<div class="col-md-4 form-group">\n\
   		<label for="usr">Nome Completo:</label>\n\
   		<input type="text" class="form-control" id="VitimaNome' + idVitima + '" name="VitimaNome[]">\n\
   		</div>\n\
   		<div class="col-md-4 form-group">\n\
   		<label for="usr">Nome Mãe:</label>\n\
   		<input type="text" class="form-control" id="VitimaNomeMae' + idVitima + '" name="VitimaNomeMae[]">\n\
   		</div>\n\
   		<div class="col-md-2 form-group">\n\
   		<label for="usr">Data Nascimento:</label>\n\
   		<input type="text" class="form-control" id="VitimaDataNascimento' + idVitima + '" name="VitimaDataNascimento[]">\n\
   		</div>\n\
   		</div>\n\
   		<div class="row">\n\
   		<div class="col-md-2 form-group">\n\
   		<label for="usr">Sexo:</label>\n\
   		<select name="SexoVitima[]" id="SexoVitima' + idVitima + '" class="form-control" >\n\
   		<OPTION VALUE=""></OPTION>\N\
   		<OPTION VALUE="MASCULINO">MASCULINO</OPTION>\N\
   		<OPTION VALUE="FEMININO">FEMININO</OPTION>\N\
   		<OPTION VALUE="IGNORADO">IGNORADO</OPTION>\N\
   		<OPTION VALUE="NAO INFORMADO">NÃO INFORMADO</OPTION>\N\
   		</select>\n\
   		</div>\n\
   		<div class="col-md-2 form-group">\n\
   		<label for="usr">Meio de Transporte:</label>\n\
   		<select name="MeioVitima[]" id="MeioVitima' + idVitima + '" class="form-control">\n\
   		<OPTION VALUE=""></OPTION>\N\
   		<OPTION VALUE="PEDESTRE">PEDESTRE</OPTION>\N\
   		<OPTION VALUE="MOTOCICLETA">MOTOCICLETA</OPTION>\N\
   		<OPTION VALUE="AUTOMOVEL">AUTOMÓVEL</OPTION>\N\
   		<OPTION VALUE="BICICLETA">BICICLETA</OPTION>\N\
   		<OPTION VALUE="CAMINHAO">CAMINHÃO</OPTION>\N\
   		<OPTION VALUE="CARROCA">CARROÇA</OPTION>\N\
   		<OPTION VALUE="VEICULO PESADO">VEÍCULO PESADO</OPTION>\N\
   		<OPTION VALUE="ONIBUS/VAN">ÔNIBUS/VAN</OPTION>\N\
   		<OPTION VALUE="TRICICLO">TRICICLO</OPTION>\N\
   		<OPTION VALUE="OUTROS">OUTROS</OPTION>\N\
   		<OPTION VALUE="NAO INFORMADO">NÃO INFORMADO</OPTION>\N\
   		</select>\n\
   		</div>\n\
   		<div class="col-md-2 form-group">\n\
   		<label for="usr">Condição da Vítima:</label>\n\
   		<select name="CondVitima[]" id="CondVitima' + idVitima + '" class="form-control" >\n\
   		<OPTION VALUE=""></OPTION>\n\
   		<OPTION VALUE="CONDUTOR">CONDUTOR</OPTION>\n\
   		<OPTION VALUE="PASSAGEIRO">PASSAGEIRO</OPTION>\n\
   		<OPTION VALUE="PEDESTRE">PEDESTRE</OPTION>\n\
   		<OPTION VALUE="NAO INFORMADO">NÃO INFORMADO</OPTION>\n\
   		</select>\n\
   		</div>\n\
   		\n\ <div class="col-md-2 form-group" >\n\
   		<label for="usr">Influência do Álcool?</label>\n\
   		<select name="VitimaInfluencia[]" id="VitimaInfluencia' + idVitima + '" onchange="exibeAlcoolemia(' + idVitima + ');" class="form-control" >\n\
   		<OPTION VALUE=""></OPTION>\N\
   		<OPTION VALUE="SIM">SIM</OPTION>\N\
   		<OPTION VALUE="NAO">NÃO</OPTION>\N\
   		<OPTION VALUE="NAO INFORMADO">NÃO INFORMADO</OPTION>\N\
   		</select>\n\
   		</div>\n\
   		<div class="col-md-2 form-group" id="_classAlcoolemia' + idVitima + '" style="display: none;">\n\
   		<label for="usr">Alcoolemia?</label>\n\
   		<select name="ComprovaAlcool[]" id="ComprovaAlcool' + idVitima + '" class="form-control" onChange="exibeValorAlcolemia(' + idVitima + ');" >\n\
   		<OPTION VALUE=""> </OPTION>\N\
   		<OPTION VALUE="SIM">SIM</OPTION>\N\
   		<OPTION VALUE="NAO">NAO</OPTION>\N\
   		<OPTION VALUE="NAO INFORMADO">NÃO INFORMADO</OPTION>\N\
   		</select>\n\
   		</div>\n\
   		<div class="col-md-2 form-group" id="_valorAlcolemia' + idVitima + '" style="display: none;">\n\
   		<Label>Valor Alcoolemia: </Label>\n\
   		<input type="text" class="form-control" id="ValorAlcoolemia' + idVitima + '" name="ValorAlcoolemia[]" >\n\
   		</div>\n\
   		<div class="col-md-2 form-group" id="_classBafometro' + idVitima + '" style="display: none;">\n\
   		<label for="usr">Bafômetro?</label>\n\
   		<select name="ComprovaBafometro[]" id="ComprovaBafometro' + idVitima + '" onChange="exibeValorBafometro(' + idVitima + ');" class="form-control">\n\
   		<OPTION VALUE=""> </OPTION>\N\
   		<OPTION VALUE="SIM">SIM</OPTION>\N\
   		<OPTION VALUE="NAO">NAO</OPTION>\N\
   		<OPTION VALUE="NAO INFORMADO">NÃO INFORMADO</OPTION>\N\
   		</select>\n\
   		</div>\n\
   		<div class="col-md-2 form-group" id="_valorBafometro' + idVitima + '" style="display: none;">\n\
   		<Label>Valor Bafômetro:</Label>\n\
   		<input type="text" class="form-control" id="ValorBafometro' + idVitima + '" name="ValorBafometro[]"> \n\
   		</div>\n\
   		<div class="col-md-2 form-group" id="Placa' + idVitima + '">\n\
   		<Label>PLACA :</Label>\n\
   		<input type="text" class="form-control" id="Placa' + idVitima + '" name="Placa[]"> \n\
   		</div>\n\
   		\n\</div>\n\
   		<div class="row">\n\
   		<div class="col-md-2 form-group">\n\
   		<label for="usr">CEP</label>\n\
   		<input id="CEPVitima' + idVitima + '" type="text" min="0" maxlength="8" class="form-control" name="CEPVitima[]" onChange="buscaEnderecoCEPVitima(' + idVitima + ');">\n\
   		</div>\n\
   		<div class="col-md-3 form-group">\n\
   		<label for="usr">Endereço</label>\n\
   		<input id="EnderecoVitima' + idVitima + '" type="text" class="form-control" name="EnderecoVitima[]" onChange="validaLatitudeVitima(' + idVitima + ');">\n\
   		</div>\n\
   		<div class="col-md-2 form-group">\n\
   		<label for="usr">Número</label>\n\
   		<input type="text" class="form-control" name="NumeroVitima[]" id="NumeroVitima' + idVitima + '" onChange="validaLatitudeVitima(' + idVitima + ');">\n\
   		</div>\n\
   		\n\    <div class="col-md-2 form-group">\n\
   		<label for="usr">Bairro</label>\n\
   		<input type="text" class="form-control" name="BairroVitima[]" id="BairroVitima' + idVitima + '" onChange="validaLatitudeVitima(' + idVitima + ');">\n\
   		</div>\n\
   		<div class="col-md-2 form-group">\n\
   		<label for="usr">Estado</label>\n\
   		<select id="EstadoVitima' + idVitima + '" class="form-control" name="EstadoVitima[]" class="form-control " onChange="validaLatitudeVitima(' + idVitima + ');"> \n\
   		<option value="">Selecione o Estado do acidente</option>\n\
   		</select>\n\
   		</div>\n\
   		<div class="col-md-2 form-group">\n\
   		<label for="usr">Município</label>\n\
   		<select id="MunicipioVitima' + idVitima + '" class="form-control" name="MunicipioVitima[]" class="form-control " onChange="validaLatitudeVitima(' + idVitima + ');"> \n\
   		<option value="">Selecione o municipio do acidente</option>\n\
   		</select>\n\
   		</div>\n\
   		<div class="col-md-2 form-group">\n\
   		<label for="usr">Coord X</label>\n\
   		<input type="text" class="form-control" name="CoordVitimaX[]" id="CoordVitimaX' + idVitima + '" >\n\
   		</div>\n\
   		<div class="col-md-2 form-group">\n\
   		<label for="usr">Coord Y</label>\n\
   		<input type="text" class="form-control" name="CoordVitimaY[]" id="CoordVitimaY' + idVitima + '" >\n\
   		</div>\n\
   		<div class="col-md-2 form-group" id="CBO' + idVitima + '">\n\
   		<Label>CBO :</Label>\n\
   		<input type="text" class="form-control" id="CBO' + idVitima + '" name="CBO[]"> \n\
   		</div>\n\
   		<div class="col-md-2 form-group" id="NUMSUS' + idVitima + '">\n\
   		<Label>NUMSUS :</Label>\n\
   		<input type="text" class="form-control" id="NUMSUS' + idVitima + '" name="NUMSUS[]"> \n\
   		</div>\n\
   		</div>\n\
   		<div class="row">\n\
   		<div class="col-md-4 form-group" >\n\
   		<Label>PAR SIM:</Label>\n\
   		<span id="PAR_VITIMA_'+ idVitima + '"> </span> <br>\n\
   		<span id="SIM_CAUSABAS_'+ idVitima + '"> </span> <br>\n\
   		<span id="SIM_DO_'+ idVitima + '"> </span> <br>\n\
   		</div>\n\
   		<div class="col-md-4 form-group" >\n\
   		<Label>PAR SIH:</Label>\n\
   		<span id="PAR_VITIMA_SIH_'+ idVitima + '"> </span> <br>\n\
   		<span id="DIAGPRINCIPAL'+ idVitima + '"> </span> <br>\n\
   		<span id="AIH'+ idVitima + '"> </span> <br>\n\
   		</div>\n\
   		<div class="col-md-3 form-group" >\n\
   		<a href="#" class="btn btn-default pull-right" onclick="deleta_vitima('+ idVitima + ')">Deletar Vítima</a>\n\
   		</div>\n\
   		<div class="col-md-12 form-group">\n\
   		<label for="comment">DESCRIÇÃO VITIMA:</label>\n\
   		<textarea class="form-control" rows="5" name="Descricao[]" id="descricao_'+ idVitima + '"></textarea>\n\
   		</div>\n\
   		</div>\n\
   		<hr class="style3">');
new dgCidadesEstados({
	cidade: document.getElementById(('MunicipioVitima' + idVitima != 'null'?'MunicipioVitima' + idVitima : '')),
	estado: document.getElementById(('EstadoVitima' + idVitima != 'null'?'EstadoVitima' + idVitima : ''))
});
$('#VitimaDataNascimento' + idVitima).datepicker({
	language: "pt-BR",
	autoclose: true,
	format: 'dd/mm/yyyy',
	todayHighlight: true,
	endDate:  new Date(),
	constrainInput: false
}).on('changeDate', function (e) {
	if ($(this).val() == "99/99/9999")
		return true;
        // Revalidate the date field
        //$('#QuadroMultiplo').formValidation('revalidateField', 'VitimaDataNascimento[]');
    });
}

function printErrorMsg (msg) {
	$(".print-error-msg").find("ul").html('');
	$(".print-error-msg").css('display','block');
	$.each( msg, function( key, value ) {
		$(".print-error-msg").find("ul").append('<li>'+value+'</li>');
	});
}

function validaLatitude() {
	buscaEnderecoCEP();
	if (($("#Endereco").val() != '' && $("#Bairro").val() != '' && $("#Numero").val() != '' && $("#MunicipioAcidente").val() != '') && ($("#CoordX").val() == '' && $("#CoordY").val() == '')) {
		var endereco = $("#Endereco").val() + ', ' + $("#Numero").val() + ' - ' + $("#Bairro").val() + ', ' + $("#MunicipioAcidente").val() + ' - ' + $("#EstadoAcidente").val()+', Brazil';
		$.isLoading({ text: "Carregando ... "});
		$.ajax({
			url: '{{ route('getCoordenada') }}',
			type: "POST",
			async: false,
			headers: {
				'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
			},
			data: {endereco: endereco},
			success: function (data, textStatus, jqXHR){
				var latitude = data.lat;
				var longitude = data.lon;
				$("#CoordX").val(latitude);
				$("#CoordY").val(longitude);
				$.isLoading('hide');
			},
			error: function (jqXHR, textStatus, errorThrown)
			{
				if(jqXHR.status == 419){
					location.reload();
				}
				console.log("erro get latitude", jqXHR, textStatus, errorThrown);
				$.isLoading('hide');
			}
		});
		

	}
}
function validaLatitudeVitima(id) {
	buscaCEPVitima(id)
	if ($("#EnderecoVitima" + id).val() != '' && $("#BairroVitima" + id).val() != '' && $("#NumeroVitima" + id).val() != '' && $("#MunicipioVitima" + id).val() != '' && ($("#CoordVitimaX"+ id).val() == '' && $("#CoordVitimaY"+ id).val() == '')) {

		var endereco = ($("#EnderecoVitima").val() + ', ' + $("#NumeroVitima" + id).val() + ' - ' + $("#BairroVitima" + id).val() + ', ' + $("#MunicipioVitima" + id).val() + ' - ' + $("#EstadoVitima" + id).val() + ', Brazil');
		$.isLoading({ text: "Carregando ... "});
		$.ajax({
			url: '{{ route('getCoordenada') }}',
			type: "POST",
			async: false,
			headers: {
				'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
			},
			data: {endereco: endereco},
			success: function (data, textStatus, jqXHR){
				var latitude = data.lat;
				var longitude = data.lon;
				$("#CoordVitimaX" + id).val(latitude);
				$("#CoordVitimaY" + id).val(longitude);
				$.isLoading('hide');
			},
			error: function (jqXHR, textStatus, errorThrown)
			{
				if(jqXHR.status == 419){
					location.reload();
				}
				console.log("erro get latitude", jqXHR, textStatus, errorThrown);
				$.isLoading('hide');
			}
		});
		

	}
}
function buscaEnderecoCEP() {
	if ($('#Endereco').val() !== '' 
		&& $('#CEP').val() == '' 
		&& $('#EstadoAcidente').val() !== '' 
		&& $('#MunicipioAcidente').val() !== null 
		&& $('#MunicipioAcidente').val() !== '' 
		&& $('#Bairro').val() !== '') {
		var EstadoCep = replaceSpecialChars($('#EstadoAcidente').val().toUpperCase());
	var RuaCep = replaceSpecialChars($('#Endereco').val().toUpperCase().replace(/QUILOMETRO |KM |ESTACAO |EST |VILA |PRAIA |PR |PRQ |LARGO |LGO |LADEIRA |LD |RUA |PRACA |AV |PC |VIADUTO |VD |AV |ALAMEDA |AL |ALM |BECO |BC |R |AVENIDA /g, ''));
	var Bairro = replaceSpecialChars($('#Bairro').val().toUpperCase().toUpperCase());
	var MunicipioCep = $('#MunicipioAcidente').val().toUpperCase();
	var URL = encodeURI("https://viacep.com.br/ws/" + EstadoCep + "/" + MunicipioCep + "/" + RuaCep + "/json/");
        // console.log(URL);
        $.isLoading({ text: "Carregando ... "});
        $.ajax({
            // url para o arquivo json.php
            url: URL,
            // dataType json
            dataType: "json",
            // função para de sucesso
            success: function (data) {
                //console.log(data)
                $.each(data, function (i, item) {
                	if (Bairro == replaceSpecialChars(data[i].bairro.toUpperCase())) {
                		var CEP = data[i].cep;
                		$('#CEP').val('');
                		$('#CEP').val(CEP.replace('-', ''));
                	}
                })
                $.isLoading('hide');
            },
            error: function (data, xhr, ajaxOptions, thrownError) {
            	console.log(xhr.statusText);
            	console.log(thrownError);
            	console.log(data);
            	$.isLoading('hide');
            }
        });
    }
}
function buscaCEPEndereco() {
	if ($('#CEP').val() !== ''){
		var Cep = $('#CEP').val();
		var cep = Cep.replace(/\D/g, '');
		var validacep = /^[0-9]{8}$/;
		if (validacep.test(cep)) {
			$("#Endereco").val("...")
			$("#Bairro").val("...")
			$.isLoading({ text: "Carregando ... "});
			$.getJSON("//viacep.com.br/ws/" + cep + "/json/?callback=?", function (dados) {
                //console.log(dados)
                $.isLoading('hide');
                if (!("erro" in dados)) {
                    //Atualiza os campos com os valores da consulta.
                    $("#Endereco" ).val(dados.logradouro);
                    $("#Bairro").val(dados.bairro);
                    $("#EstadoAcidente").val(dados.uf);
                    $("#EstadoAcidente").trigger('change');
                    $("#MunicipioAcidente").val(dados.localidade.toUpperCase());
                }
                else {
                    //CEP pesquisado não foi encontrado.
                    $("#Endereco").val("");
                    $("#Bairro").val("");
                    $("#EstadoAcidente").val("");
                    $("#EstadoAcidente").trigger('change');
                    $("#MunicipioAcidente").val("");
                    alert("CEP não encontrado.");
                    $("#CEP").val('');
                }
            });
		}else{
			$("#Endereco").val("");
			$("#Bairro").val("");
			$("#EstadoAcidente").val("");
			$("#EstadoAcidente").trigger('change');
			$("#MunicipioAcidente").val("");
			alert("Formato de CEP inválido.");
			$("#CEP").val('');
		}
	}

}
function buscaEnderecoCEPVitima(id) {
	if ($('#CEPVitima' + id).val() != '' && $('#CEPVitima' + id).val().length == 8 
		&& $('#EnderecoVitima'+ id).val() == '' 
		&& $('#EstadoVitima'+ id).val() == '' 
		&& $('#MunicipioVitima'+ id).val() == '' 
		&& $('#BairroVitima'+ id).val() == '' ) {
		var CepVitima = $('#CEPVitima' + id).val();
	var cep = CepVitima.replace(/\D/g, '');
	var validacep = /^[0-9]{8}$/;
	if (validacep.test(cep)) {
		$("#EnderecoVitima" + id).val("...")
		$("#BairroVitima" + id).val("...")
		$.getJSON("//viacep.com.br/ws/" + cep + "/json/?callback=?", function (dados) {
                //console.log(dados)
                if (!("erro" in dados)) {
                    //Atualiza os campos com os valores da consulta.
                    $("#EnderecoVitima" + id).val(dados.logradouro);
                    $("#BairroVitima" + id).val(dados.bairro);
                    $("#EstadoVitima" + id).val(dados.uf);
                    $("#EstadoVitima" + id).trigger('change');
                    $("#MunicipioVitima" + id).val(dados.localidade.toUpperCase());
                    //$("#ibge").val(dados.ibge);
                } //end if.
                else {
                    //CEP pesquisado não foi encontrado.
                    $("#EnderecoVitima" + id).val("");
                    $("#BairroVitima" + id).val("");
                    $("#EstadoVitima" + id).val("");
                    $("#EstadoVitima" + id).trigger('change');
                    $("#MunicipioVitima" + id).val("");
                    alert("CEP não encontrado.");
                    $("#CEPVitima" + id).val('');
                }
            });
	}else{
		$("#EnderecoVitima" + id).val("");
		$("#BairroVitima" + id).val("");
		$("#EstadoVitima").val("");
		$("#EstadoVitima" + id).trigger('change');
		$("#MunicipioVitima" + id).val("");
		alert("Formato de CEP inválido.");
		$("#CEPVitima" + id).val('');
	}
}

}
function buscaCEPVitima(id) {
	if ($('#CEPVitima'+ id).val() == '' 
		&& $('#EnderecoVitima'+ id).val() != '' 
		&& $('#EstadoVitima'+ id).val() != '' 
		&& $('#MunicipioVitima'+ id).val() != '' 
		&& $('#BairroVitima'+ id).val() != ''){
		var EstadoCep = replaceSpecialChars($('#EstadoVitima'+ id).val().toUpperCase());
	var RuaCep = replaceSpecialChars($('#EnderecoVitima'+ id).val().toUpperCase().replace(/QUILOMETRO |KM |ESTACAO |EST |VILA |PRAIA |PR |PRQ |LARGO |LGO |LADEIRA |LD |RUA |PRACA |AV |PC |VIADUTO |VD |AV |ALAMEDA |AL |ALM |BECO |BC |R |AVENIDA /g, ''));
	var Bairro = replaceSpecialChars($('#BairroVitima'+ id).val().toUpperCase().toUpperCase());
	var MunicipioCep = $('#MunicipioVitima'+ id).val().toUpperCase();
	var URL = encodeURI("https://viacep.com.br/ws/" + EstadoCep + "/" + MunicipioCep + "/" + RuaCep + "/json/");
        // console.log(URL);
        $.isLoading({ text: "Carregando ... "});
        $.ajax({
            // url para o arquivo json.php
            url: URL,
            // dataType json
            dataType: "json",
            // função para de sucesso
            success: function (data) {
                //console.log(data)
                $.each(data, function (i, item) {
                	if (Bairro == replaceSpecialChars(data[i].bairro.toUpperCase())) {
                		var CEP = data[i].cep;
                		$('#CEPVitima'+ id).val('');
                		$('#CEPVitima'+ id).val(CEP.replace('-', ''));
                	}
                })
                $.isLoading('hide');
            },
            error: function (data, xhr, ajaxOptions, thrownError) {
            	console.log(xhr.statusText);
            	console.log(thrownError);
            	console.log(data);
            	$.isLoading('hide');
            }
        });
    }
}

function exibeValorBafometro(id) {
	if ($('#ComprovaBafometro' + id).val() == 'SIM') {
		$('#_valorBafometro' + id).show();
	} else {
		$('#_valorBafometro' + id).hide();
	}
}

function exibeAlcoolemia(id) {
	if ($('#VitimaInfluencia' + id).val() == 'SIM') {
		$('#_classAlcoolemia' + id).show();
		$('#_classBafometro' + id).show();
	} else {
		$('#_classAlcoolemia' + id).hide();
		$('#_classBafometro' + id).hide();
	}
}
function exibeValorAlcolemia(id) {
	if ($('#ComprovaAlcool' + id).val() == 'SIM') {
		$('#_valorAlcolemia' + id).show();
	} else {
		$('#_valorAlcolemia' + id).hide();
	}
}
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

$('.loadData').change(function () {

	resetAllValues()
	if ($('#Ano').val() != '' && $('#Trimestre').val() != '' && $('#CodCidade').val() != '') {
		var pendencia = '';
		$.ajax({
			url: '{{ route('checkPendencias') }}',
			type: "POST",
			async: false,
			headers: {
				'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
			},
			data: {
				Ano: $('#Ano').val(), 
				Trimestre: $('#Trimestre').val(), 
				CodCidade: $("#CodCidade").val()
			},
			success: function (data, textStatus, jqXHR)
			{
				//console.log(data, data.success)
				if (data.success == true) {
                        //implementar aviso
                        console.log("pendencia")
                        pendencia = "- Pendência na Lista: Clique <a href='{{ route('listaUnica') }}?Ano="+$('#Ano').val()+"&Trimestre="+$('#Trimestre').val()+"'>aqui</a> para resolver";
                    }
                },
                error: function (jqXHR, textStatus, errorThrown)
                {
                	console.log("erro pendencia");
                }
            });
		//console.log(pendencia)
		$('#titulo-painel').html('Acidentes do período ' + $('#Ano').val() + ' / ' + $('#Trimestre').val()+' '+pendencia);
		$('#adicionarAcidente').show();
		$('#table').DataTable().ajax.reload();
		$('#_tabelaResults').show();
	} else {
		//console.log('vazim')
		$('#_tabelaResults').hide();
	}
});
$(document).on('change', '#HoraAcidente', function() {
	if($(this).val() > 23){
		$(this).val(23);
		alertify.alert('A Hora do acidente não pode ser maior que 23');
	}
});

$('#Filtro_Acidentes').on('change', function () {
	$('#table').DataTable().ajax.reload();
	resetAllValues();
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
	$("#CodCidade").val('{{Auth::user()->CodCidade}}').trigger('change');
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
				alertify.error('Erro ao buscar cidade');
			}
		});

	});
	@endif

});
</script>


@endsection