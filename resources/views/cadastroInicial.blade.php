@extends('layouts.app')
@section('styles')
<style>

	select{     
		text-transform: uppercase;
	}
	body {
		text-transform:capitalize!important;
		text-transform:uppercase!important;
	}
	.loading-steps {
		text-align: center;
	}
	.sub-menu {
		padding-left: 20px;
	}
	.checkbox {
		text-transform: uppercase;
	}
	.wizard > .content > .body {
		height: 100%!important;
		overflow-y: auto;
	}
	.disabled
	{
		cursor: not-allowed;
		pointer-events: none;

		color: #c0c0c0;
		background-color: #ffffff;

	}
</style>
<link rel="stylesheet"  type="text/css" href="//cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.6.1/css/bootstrap-datepicker.min.css">    
<link rel="stylesheet" type="text/css" href="{{asset('libraries/css/jquery.steps.css')}}">
<script src="{{asset('libraries/js/cidades-estados-1-4.js')}}" type="text/javascript"></script> 

@endsection
@section('content')
<div class="content" id="renderPDF"> 

	<div id="pvt">

		<h2>IDENTIFICAÇÃO DO município</h2>
		<section data-step="0">
			<div id="loading-identificacao" style="display:none;">  
				<img src="{{ asset('libraries/img/loading2.gif') }}" class="loading-step" alt="" style="width: 5%;">
			</div>
			<div id="IDENTIFICACAO">
				<!-- Text input-->
				<div class="form-group">
					<label for="Estado">1.1 Estado:</label> 
					@if(Auth::user()->tipo == 1) 
					<select id="Estado" name="Estado" class="form-control loadValues" required=""> 
					</select>    					
					<script language="JavaScript" type="text/javascript" charset="utf-8">
						new dgCidadesEstados({
							cidade: document.getElementById('Cidade'),
							estado: document.getElementById('Estado')
						})
					</script>
					@else
					<select id="Estado" name="Estado" class="form-control loadValues" required="" disabled style="display: none"> 
						<option value="{{ Auth::user()->cidade->uf }}">{{ converterUF(Auth::user()->cidade->uf) }}</option>
					</select>    
					{{ Auth::user()->cidade->uf }}
					@endif
				</div>        
				<!-- Text input-->
				<div class="form-group ">
					<label for="Cidade">1.2 Município:</label>  
					@if(Auth::user()->tipo == 1) 
					<select id="Cidade" name="Cidade" class="form-control loadValues" required=""> 
					</select>
					
					@elseif(Auth::user()->tipo == 2) 
					<input type="hidden" id="Estado" name="Estado" value="{{ Auth::user()->cidade->uf }}"> 
					<input type="hidden" id="CodCidade" name="CodCidade"> 
					<select id="Cidade" name="Cidade" class="form-control loadValues" required=""> 
						@foreach(\App\Models\Cidades::where('uf',Auth::user()->cidade->uf)->get() as $cidade)
						<option value="{{ $cidade->municipio }}">{{ $cidade->municipio }}</option>
						@endforeach
					</select>    

					@else
					<input type="hidden" id="CodCidade" name="CodCidade" value=""> 
					{{ Auth::user()->CodCidade }}
					@endif

				</div>
				<div class="row">
					<div class="form-group col-md-8">
						<label for="Ano">1.3 Ano:</label>  
						<select name="Ano" id="Ano" class="form-control" required="">
							<option value=""></option>
							@for ($i = 2015; $i <= date("Y"); $i++)
							<option value="{{ $i }}">{{ $i }}</option>
							@endfor
						</select>
					</div> 
					<div class="form-group col-md-4">
						<label for="Copiar_dados"></label>  
						<a href="#" class="btn btn-block btn-default btn-flat" id="Copiar_dados" style="display: none;">
							<i class="glyphicon glyphicon-repeat"></i> Copiar dados do ano anterior
						</a>
					</div> 

				</div>
				<div class="form-group ">
					<label for="CodCidade">1.4 Código IBGE:</label>  
					<input id="CodCidade" name="CodCidade" type="int" class="form-control input-md" disabled value="{{ (Auth::user()->tipo > 2 ? Auth::user()->cidade->codigo : '') }}">
				</div>


				<BR>
				<button class="relatorio btn btn-primary" onclick="verRelatorioCompleto()" style="
				display: none;
				">Ver Relátorio</button>
				<button class="exportarDados btn btn-primary" onclick="exportarDados()" style="
				display: none;
				">Exportar Dados</button>
			</div>
		</section>

		<h2>COORDENADORES</h2>
		<section data-step="1">
			<div id="loading-coordenadores" style="display:none;">  
				<img src="{{ asset('libraries/img/loading2.gif') }}" class="loading-step" alt="" style="width: 5%;">
			</div>
			<form id="COORDENADORES" class="form-horizontal" role="form">
				<input type="hidden" name="editado_coordenadores" id="editado_coordenadores" value="false">

				<div class="form-group">
					<label class="control-label col-sm-12" for="coordenaTEM" style="text-align: left;">2.1 O Município tem Coordenação do Programa Vida no Trânsito   </label>
					<div class="radio col-sm-12">
						<label for="coordenaTEM-0">
							<input type="radio" name="coordenaTEM" id="coordenaTEM-0" value="SIM" >
							Sim
						</label>

						<label for="coordenaTEM-1">
							<input type="radio" name="coordenaTEM" id="coordenaTEM-1" value="NAO">
							Não
						</label>
					</div>

				</div>
				<div id="_coordenaTEM">
					<legend>Coordernador 1</legend>                        
					<div class="form-group ">
						<label class="control-label col-sm-2" for="COORDENADOR1">2.2 Nome:</label>  
						<div class="col-sm-10">
							<input id="COORDENADOR1" name="COORDENADOR1" type="text" class="form-control" required="">
						</div>
					</div>


					<div class="form-group">
						<label class="control-label col-sm-2" for="INSTITUICAO1">2.3 Instituição:</label>  
						<div class="col-sm-10">
							<input id="INSTITUICAO1" name="INSTITUICAO1" type="text" class="form-control" required="">                                  
						</div>
					</div>

					<div class="form-group">
						<label class="control-label col-sm-2" for="EMAIL1">2.4 E-mail:</label>  
						<div class="col-sm-10">
							<input id="EMAIL1" name="EMAIL1" type="email" class="form-control" required="">
						</div>
					</div>

					<div class="form-group">
						<label class="control-label col-sm-2" for="TEL1">2.5 Telefone:</label>  
						<div class="col-sm-4">					
							<input id="TEL1" name="TEL1" type="text" class="form-control bfh-phone" data-format="(ddd) dddd dddd" >
						</div>          

						<label class="control-label col-sm-2" for="TEL2">2.6 Celular:</label>  
						<div class="col-sm-4">					
							<input id="TEL1-2" name="TEL1-2" type="text" class="form-control bfh-phone" data-format="(ddd) ddddd dddd" >
						</div>          

					</div>
					<legend>Coordernador 2</legend>
					<div class="form-group ">
						<label class="control-label col-sm-2" for="COORDENADOR2">2.7 Nome:</label>  
						<div class="col-sm-10">
							<input id="COORDENADOR2" name="COORDENADOR2" type="text" class="form-control" >
						</div>
					</div>


					<div class="form-group">
						<label class="control-label col-sm-2" for="INSTITUICAO2">2.8 Instituição:</label>  
						<div class="col-sm-10">
							<input id="INSTITUICAO2" name="INSTITUICAO2" type="text" class="form-control" >                                  
						</div>
					</div>



					<div class="form-group">
						<label class="control-label col-sm-2" for="EMAIL2">2.9 E-mail:</label>  
						<div class="col-sm-10">
							<input id="EMAIL2" name="EMAIL2" type="email" class="form-control" >
						</div>
					</div>

					<div class="form-group">
						<label class="control-label col-sm-2" for="TEL2">2.10 Telefone:</label>  
						<div class="col-sm-4">					
							<input id="TEL2" name="TEL2" type="text" class="form-control bfh-phone" data-format="(ddd) dddd dddd" >
						</div>          

						<label class="control-label col-sm-2" for="TEL2-2">2.11 Celular:</label>  
						<div class="col-sm-4">					
							<input id="TEL2-2" name="TEL2-2" type="text" class="form-control bfh-phone" data-format="(ddd) ddddd dddd" >
						</div>          
					</div>  

					<legend>Coordernador 3</legend>
					<div class="form-group ">
						<label class="control-label col-sm-2" for="COORDENADOR3">2.12 Nome:</label>  
						<div class="col-sm-10">
							<input id="COORDENADOR3" name="COORDENADOR3" type="text" class="form-control" >
						</div>
					</div>


					<div class="form-group">
						<label class="control-label col-sm-2" for="INSTITUICAO3">2.13 Instituição:</label>  
						<div class="col-sm-10">
							<input id="INSTITUICAO3" name="INSTITUICAO3" type="text" class="form-control" >                                  
						</div>
					</div>



					<div class="form-group">
						<label class="control-label col-sm-2" for="EMAIL3">2.14 E-mail:</label>  
						<div class="col-sm-10">
							<input id="EMAIL3" name="EMAIL3" type="email" class="form-control" >
						</div>
					</div>

					<div class="form-group">
						<label class="control-label col-sm-2" for="TEL3">2.15 Telefone:</label>  
						<div class="col-sm-4">					
							<input id="TEL3" name="TEL3" type="text" class="form-control bfh-phone" data-format="(ddd) dddd dddd" >
						</div>          

						<label class="control-label col-sm-2" for="TEL3-2">2.16 Celular:</label>  
						<div class="col-sm-4">					
							<input id="TEL3-2" name="TEL3-2" type="text" class="form-control bfh-phone" data-format="(ddd) ddddd dddd" >
						</div>          

					</div>
				</div>



			</form>  

		</section>

		<h2>IMPLANTAÇÃO</h2>
		<section data-step="2">
			<div id="loading-implantacao" style="display:none;">  
				<img src="{{ asset('libraries/img/loading2.gif') }}" class="loading-step" alt="" style="width: 5%;">
			</div>
			<form id="IMPLANTACAO" class="form-horizontal" enctype="multipart/form-data" method="post">
				<input type="hidden" name="editado_implantacao" id="editado_implantacao" value="false" >
				<!-- decreto Radios -->
				<div class="form-group">
					<label class="control-label col-sm-12" for="COMISSAO" style="text-align: left;">3.1 O PVT tem uma Comissão Intersetorial formalizada   </label>
					<div class="radio col-sm-12">
						<label for="COMISSAO-0">
							<input type="radio" name="COMISSAO" id="COMISSAO-0" value="SIM" >
							Sim
						</label>

						<label for="COMISSAO-1">
							<input type="radio" name="COMISSAO" id="COMISSAO-1" value="NAO">
							Não
						</label>
					</div>

				</div>
				<div class="col-md-12" id='_COMISSAOINTERSETORIAL' style="display: none;">                    
					<div class="form-group" id='COMISSAO'>
						<label class="control-label col-sm-5" for="NOMECOMISSAO" style="text-align: left;">Nome da comissão:</label>  
						<div class="col-sm-4">
							<input id="NOMECOMISSAO" name="NOMECOMISSAO" type="text" placeholder="" class="form-control input-md">
						</div>
					</div>

					<div class="form-group" id='dtdecreto'>
						<label class="control-label col-sm-5" for="DTDECRETO" style="text-align: left;">Data da publicação do documento:</label>  
						<div class="col-sm-4">
							<input id="DTDECRETO" name="DTDECRETO" type="text" placeholder="" class="form-control input-md">
						</div>
					</div>


					<div class="form-group" id='decreto'>
						<label class="control-label col-sm-7" for="DECRETO" style="text-align: left;">Número do documento da Comissão Intersetorial:</label>  
						<div class="col-sm-5">
							<input id="DECRETO" name="DECRETO" type="text" placeholder="" class="form-control input-md">
						</div>
					</div>

					<div class="form-group" id='_UPDECRETO'>
						<label class="control-label col-sm-5" for="UPDECRETO" style="text-align: left;">Upload do documento da Comissão:</label>  
						<div class="col-sm-7" id='InputUPDECRETO'>
							<input id="UPDECRETO" name="UPDECRETO" type="file" class="form-control">
						</div>
					</div>

					<div class="form-group" id='instuicoes' >
						<span class='h4' style="
						margin-bottom: 0px;    
						border-bottom: 1px solid #e5e5e5;   
						">Instituições da Comissão Intersetorial</span>
						<table class="table table-responsive">
							<thead>
								<tr>   
									<th>Nome</th>
									<th>Setor</th>
									<th>Origem</th>
									<th></th>
								</tr> 
							</thead>
							<tbody id="ImplantacaoInstituicoes">
								<tr id="ImplantacaoInstituicoes1">
									<td>
										<input id="instituicao1" type="text" class="form-control" name="instituicao[]">
									</td>
									<td>
										<select id="setor1" name="setor[]" class="form-control" style="padding-right:5px;padding-left:5px;">
											<option value=""></option>
											<option value="SAUDE">Saúde</option>
											<option value="TRANSITO">Trânsito</option>
											<option value="SEGURANCA PUBLICA">Segurança pública</option>
											<option value="INSTITUICOES ACADEMICAS">Instituições acadêmicas</option>
											<option value="EDUCACAO">Educação</option>
											<option value="SOCIEDADE CIVIL">Sociedade civil</option>
											<option value="ORGANIZACOES NAO GOVERNAMENTAIS">Organizações não governamentais</option>
											<option value="EMPRESAS PRIVADAS">Empresas privadas</option>
											<option value="INFRAESTRUTURA URBANA">Infraestrutura Urbana</option>
											<option value="PLANEJAMENTO">Planejamento</option>
											<option value="JUSTICA">JUSTIÇA</option>
											<option value="TRANSPORTE">TRANSPORTE</option>
											<option value="OUTRAS">Outras</option>
										</select>
									</td>
									<td>
										<select id="origem1" name="origem[]" class="form-control" style="padding-right:0px;">
											<option value=""></option>
											<option value="GOVERNAMENTAL">Governamental</option>
											<option value="NAO GOVERNAMENTAL">Não Governamental</option>
										</select>
									</td>
									<td>

									</td>
								</tr>
								<tr id="ImplantacaoInstituicoes2">
									<td>
										<input id="instituicao2" type="text" class="form-control" name="instituicao[]">
									</td>
									<td>
										<select id="setor2" name="setor[]" class="form-control" style="padding-right:5px;padding-left:5px;">
											<option value=""></option>
											<option value="SAUDE">Saúde</option>
											<option value="TRANSITO">Trânsito</option>
											<option value="SEGURANCA PUBLICA">Segurança pública</option>
											<option value="INSTITUICOES ACADEMICAS">Instituições acadêmicas</option>
											<option value="EDUCACAO">Educação</option>
											<option value="SOCIEDADE CIVIL">Sociedade civil</option>
											<option value="ORGANIZACOES NAO GOVERNAMENTAIS">Organizações não governamentais</option>
											<option value="EMPRESAS PRIVADAS">Empresas privadas</option>
											<option value="INFRAESTRUTURA URBANA">Infraestrutura Urbana</option>
											<option value="PLANEJAMENTO">Planejamento</option>
											<option value="JUSTICA">JUSTIÇA</option>
											<option value="TRANSPORTE">TRANSPORTE</option>
											<option value="OUTRAS">Outras</option>
										</select>
									</td>
									<td>
										<select id="origem2" name="origem[]" class="form-control" style="padding-right:0px;">
											<option value=""></option>
											<option value="GOVERNAMENTAL">Governamental</option>
											<option value="NAO GOVERNAMENTAL">Não Governamental</option>
										</select>
									</td>
									<td id="botao_add">
										<button type="button" class="btn btn-default addButton"><i class="glyphicon glyphicon-plus"></i></button>
									</td>
								</tr>
							</tbody>
						</table>  

					</div>

					<div class="form-group">				
						<label class="control-label col-sm-12" style="text-align: left;" for="PERIODIC">Periodicidade das reuniões da Comissão Intersetorial</label>
						<div class="col-sm-12">
							<div class="radio col-sm-9">
								<label class="radio-inline" for="PERIODIC-0">
									<input type="radio" name="PERIODIC" id="PERIODIC-0" value="SEMANAL">
									Semanal
								</label> 
								<label class="radio-inline" for="PERIODIC-1">
									<input type="radio" name="PERIODIC" id="PERIODIC-1" value="QUINZENAL">
									Quinzenal
								</label> 
								<label class="radio-inline" for="PERIODIC-2">
									<input type="radio" name="PERIODIC" id="PERIODIC-2" value="MENSAL">
									Mensal
								</label> 
								<label class="radio-inline" for="PERIODIC-3">
									<input type="radio" name="PERIODIC" id="PERIODIC-3" value="BIMESTRAL">
									Bimestral
								</label> 
								<label class="radio-inline" for="PERIODIC-5">
									<input type="radio" name="PERIODIC" id="PERIODIC-5" value="QUADRIMESTRAL">
									Trimestral
								</label> 
								<label class="radio-inline periodo5" for="PERIODIC-4">
									<input type="radio" name="PERIODIC" id="PERIODIC-4" value="OUTRA">
									Outra
								</label>
							</div>

						</div>             
					</div>
					<div id ="_outradata" class="form-group col-md-3" style="display: none;">
						<input id="outradata" name="outradata" type="text" placeholder="" class="form-control input-md">
					</div>
					<div class="form-group">                                  
						<label class="control-label col-md-12" for="REGREUNIAOCI">Forma de registro das reuniões da comissão intersetorial</label>
						<div class="col-sm-12">
							<div class="radio col-sm-5">
								<label class="radio-inline" for="REGREUNIAOCI-0">
									<input type="radio" name="REGREUNIAOCI" id="REGREUNIAOCI-0" value="ATA">
									Ata 
								</label> 
								<label class="radio-inline" for="REGREUNIAOCI-1">
									<input type="radio" name="REGREUNIAOCI" id="REGREUNIAOCI-1" value="RELATÓRIO">
									Relatório
								</label> 
								<label class="radio-inline" for="REGREUNIAOCI-4">
									<input type="radio" name="REGREUNIAOCI" id="REGREUNIAOCI-4" value="OUTRA">
									Outra
								</label>
							</div>

						</div>
					</div>
					<div id ="_REGREUNIAOCIoutra" class="form-group col-md-3" style="display: none;">
						<input id="REGREUNIAOCIoutra" name="REGREUNIAOCIoutra" type="text" placeholder="" class="form-control input-md">
					</div>

				</div>


				<div id="_REGREUNIAOPVT" style="display: none;">
					<div class="form-group">                                  
						<label class="control-label col-md-12" for="DATAREUNIAOCPVT">Periodicidade de reuniões do PVT</label>
						<div class="col-sm-12">
							<div class="radio col-sm-9">
								<label class="radio-inline" for="DATAREUNIAOCPVT-0">
									<input type="radio" name="DATAREUNIAOCPVT" id="DATAREUNIAOCPVT-0" value="SEMANAL">
									Semanal
								</label> 
								<label class="radio-inline" for="DATAREUNIAOCPVT-1">
									<input type="radio" name="DATAREUNIAOCPVT" id="DATAREUNIAOCPVT-1" value="QUINZENAL">
									Quinzenal
								</label> 
								<label class="radio-inline" for="DATAREUNIAOCPVT-2">
									<input type="radio" name="DATAREUNIAOCPVT" id="DATAREUNIAOCPVT-2" value="MENSAL">
									Mensal
								</label> 
								<label class="radio-inline" for="DATAREUNIAOCPVT-3">
									<input type="radio" name="DATAREUNIAOCPVT" id="DATAREUNIAOCPVT-3" value="BIMESTRAL">
									Bimestral
								</label> 
								<label class="radio-inline" for="DATAREUNIAOCPVT-5">
									<input type="radio" name="DATAREUNIAOCPVT" id="DATAREUNIAOCPVT-5" value="QUADRIMESTRAL">
									Trimestral
								</label> 
								<label class="radio-inline" for="DATAREUNIAOCPVT-4"">
									<input type="radio" name="DATAREUNIAOCPVT" id="DATAREUNIAOCPVT-4" value="OUTRA">
									Outra
								</label>
							</div>                               
						</div>
					</div>
					<div id ="_DATAREUNIAOCPVToutra" class="form-group col-md-3" style="display: none; ">
						<input id="DATAREUNIAOCPVToutra" name="DATAREUNIAOCPVToutra" type="text" placeholder="" class="form-control input-md">
					</div>

					<div class="form-group ">
						<label class="control-label col-md-12" for="REGREUNIAOCPVT">Forma de registro das reuniões do PVT</label>
						<div class="col-sm-12">
							<div class="radio col-sm-5">
								<label class="radio-inline" for="REGREUNIAOCPVT-0">
									<input type="radio" name="REGREUNIAOCPVT" id="REGREUNIAOCPVT-0" value="ATA">
									Ata 
								</label> 
								<label class="radio-inline" for="REGREUNIAOCPVT-1">
									<input type="radio" name="REGREUNIAOCPVT" id="REGREUNIAOCPVT-1" value="RELATÓRIO">
									Relatório
								</label> 
								<label class="radio-inline period3" for="REGREUNIAOCPVT-4">
									<input type="radio" name="REGREUNIAOCPVT" id="REGREUNIAOCPVT-4" value="OUTRA">
									Outra
								</label>
							</div>

						</div>

					</div>
					<div id ="_REGREUNIAOCPVToutra" class="form-group col-md-3" style="display: none;" >
						<input id="REGREUNIAOCPVToutra" name="REGREUNIAOCPVToutra" type="text" placeholder="" class="form-control input-md">
					</div>
				</div>

				<div class="form-group" id="_DTREUNIAOCI" style="display: none;" >
					<label class="control-label col-sm-5" for="DTREUNIAOCI">Data da última reunião:</label>  
					<div class="col-sm-5">
						<input id="DTREUNIAOCI" name="DTREUNIAOCI" type="text" class="form-control">
					</div>  
				</div>
			</form>
		</section>	

		<h2>QUALIDADE E INTEGRAÇÃO DOS DADOS</h2>
		<section data-step="3">
			<div id="loading-qualidade" style="display:none;">  
				<img src="{{ asset('libraries/img/loading2.gif') }}" class="loading-step" alt="" style="width: 5%;">
			</div>
			<form id="QUALIDADE" class="form-horizontal" enctype="multipart/form-data">
				<input type="hidden" name="editado_qualidade" id="editado_qualidade" value="false" >
				<!-- decreto Radios -->
				<div class="form-group">
					<label class="control-label col-sm-12" for="COMISSAOGD" style="text-align: left;">4.1 Foi constituída a Comissão de Gestão de Dados?  </label>
					<div class="radio col-sm-12">
						<label for="COMISSAOGD-0">
							<input type="radio" name="COMISSAOGD" id="COMISSAOGD-0" value="SIM" >
							Sim
						</label>

						<label for="COMISSAOGD-1">
							<input type="radio" name="COMISSAOGD" id="COMISSAOGD-1" value="NAO">
							Não
						</label>
					</div>

				</div>


				<div id="_COMISSAOGD" class="sub-menu" style="display:none;">   

					<div class="form-group">
						<label class="control-label col-sm-12"  for="COMISSAOFORM">A Comissão foi formalizada por ato normativo do prefeito ou Secretário? </label>
						<div class="radio col-sm-12">
							<label for="COMISSAOFORM-0">
								<input type="radio" name="COMISSAOFORM" id="COMISSAOFORM-0" value="SIM" >
								Sim
							</label>

							<label for="COMISSAOFORM-1">
								<input type="radio" name="COMISSAOFORM" id="COMISSAOFORM-1" value="NAO">
								Não
							</label>
						</div>
					</div> 


					<div id='_COMISSAOFORM' class="sub-menu" style="display: none;">                    
						<div class="form-group" id='COMISSAODOC'>
							<label class="control-label col-sm-5" for="COMISSAODOC" style="text-align: left;">Tipo de documento:</label>  
							<div class="col-sm-4">
								<input id="COMISSAODOC" name="COMISSAODOC" type="text" placeholder="" class="form-control input-md">
							</div>
						</div>

						<div class="form-group">
							<label class="control-label col-sm-5" for="DTCOMISSAO" style="text-align: left;">Data de Publicação do Documento:</label>  
							<div class="col-sm-4">
								<input id="DTCOMISSAO" name="DTCOMISSAO" type="text" placeholder="" class="form-control input-md">
							</div>
						</div>



						<div class="form-group">
							<label class="control-label col-sm-5" for="NCOMISSAO" style="text-align: left;">Número do Documento:</label>  
							<div class="col-sm-5">
								<input id="NCOMISSAO" name="NCOMISSAO" type="text" placeholder="" class="form-control input-md">
							</div>
						</div>

						<div class="form-group" id='_UPDECRETOCOMISSAO'>
							<label class="control-label col-sm-5" for="UPDECRETOCOMISSAO" style="text-align: left;">Upload do documento da Comissão:</label>  
							<div class="col-sm-7" id='InputUPDECRETOCOMISSAO'>
								<input id="UPDECRETOCOMISSAO" name="UPDECRETOCOMISSAO" type="file" class="form-control">
							</div>
						</div>

						<div class="form-group sub-menu" id='instuicoes' >
							<label class='control-label'>comissão de gestão de dados</label>
							<table class="table table-responsive">
								<thead>
									<tr>   
										<th>Nome</th>
										<th>Setor</th>
										<th>Origem</th>
										<th></th>
									</tr> 
								</thead>
								<tbody id="QualidadeInstituicoes">
									<tr id="QualidadeInstituicoes1">
										<td>
											<input id="Qualidade_instituicao1" type="text" class="form-control" name="Qualidade_instituicao[]">
										</td>
										<td>
											<select id="Qualidade_setor1" name="Qualidade_setor[]" class="form-control" style="padding-right:5px;padding-left:5px;">
												<option value=""></option>
												<option value="SAUDE">Saúde</option>
												<option value="TRANSITO">Trânsito</option>
												<option value="SEGURANCA PUBLICA">Segurança pública</option>
												<option value="INSTITUICOES ACADEMICAS">Instituições acadêmicas</option>
												<option value="EDUCACAO">Educação</option>
												<option value="SOCIEDADE CIVIL">Sociedade civil</option>
												<option value="ORGANIZACOES NAO GOVERNAMENTAIS">Organizações não governamentais</option>
												<option value="EMPRESAS PRIVADAS">Empresas privadas</option>
												<option value="INFRAESTRUTURA URBANA">Infraestrutura Urbana</option>
												<option value="PLANEJAMENTO">Planejamento</option>
												<option value="JUSTICA">JUSTIÇA</option>
												<option value="TRANSPORTE">TRANSPORTE</option>
												<option value="OUTRAS">Outras</option>
											</select>
										</td>
										<td>
											<select id="Qualidade_origem1" name="Qualidade_origem[]" class="form-control" style="padding-right:0px;">
												<option value=""></option>
												<option value="GOVERNAMENTAL">Governamental</option>
												<option value="NAO GOVERNAMENTAL">Não Governamental</option>
											</select>
										</td>
										<td>

										</td>
									</tr>
									<tr id="QualidadeInstituicoes2">
										<td>
											<input id="Qualidade_instituicao2" type="text" class="form-control" name="Qualidade_instituicao[]">
										</td>
										<td>
											<select id="Qualidade_setor2" name="Qualidade_setor[]" class="form-control" style="padding-right:5px;padding-left:5px;">
												<option value=""></option>
												<option value="SAUDE">Saúde</option>
												<option value="TRANSITO">Trânsito</option>
												<option value="SEGURANCA PUBLICA">Segurança pública</option>
												<option value="INSTITUICOES ACADEMICAS">Instituições acadêmicas</option>
												<option value="EDUCACAO">Educação</option>
												<option value="SOCIEDADE CIVIL">Sociedade civil</option>
												<option value="ORGANIZACOES NAO GOVERNAMENTAIS">Organizações não governamentais</option>
												<option value="EMPRESAS PRIVADAS">Empresas privadas</option>
												<option value="INFRAESTRUTURA URBANA">Infraestrutura Urbana</option>
												<option value="PLANEJAMENTO">Planejamento</option>
												<option value="JUSTICA">JUSTIÇA</option>
												<option value="TRANSPORTE">TRANSPORTE</option>
												<option value="OUTRAS">Outras</option>
											</select>
										</td>
										<td>
											<select id="Qualidade_origem2" name="Qualidade_origem[]" class="form-control" style="padding-right:0px;">
												<option value=""></option>
												<option value="GOVERNAMENTAL">Governamental</option>
												<option value="NO APGOVERNAMENTAL">Não Governamental</option>
											</select>
										</td>
										<td id="botao_add">
											<button type="button" class="btn btn-default addButton"><i class="glyphicon glyphicon-plus"></i></button>
										</td>
									</tr>
								</tbody>
							</table>  

						</div>
					</div>     

				</div> 


				<div class="form-group ">
					<label class="control-label col-md-12" for="BASESAT">4.2 Obtenção dos dados que registram ocorrência de acidentes de trânsito:</label>			   
					<div class="radio col-sm-12">
						<label for="BASESAT-0">
							<input type="radio" name="BASESAT" id="BASESAT-0" value="SIM"  >
							Sim
						</label>

						<label for="BASESAT-1">
							<input type="radio" name="BASESAT" id="BASESAT-1" value="NAO">
							Não
						</label>
					</div>

				</div>
				<div class="form-group sub-menu" id="_BASESAT" style="display: none;">
					<div>
						<label class="col-xs-2 control-label">Base dos acidentes de trânsito:</label>
						<div class="col-xs-6">
							<div class="checkbox">
								<label>
									<input type="checkbox" name="base_dados[]" value="POLÍCIA MILITAR" /> POLÍCIA MILITAR
								</label>
							</div>
							<div class="checkbox">
								<label>
									<input type="checkbox" name="base_dados[]" value="POLÍCIA RODOVIÁRIA ESTADUAL" /> POLÍCIA RODOVIÁRIA ESTADUAL
								</label>
							</div>
							<div class="checkbox">
								<label>
									<input type="checkbox" name="base_dados[]" value="POLÍCIA RODOVIÁRIA FEDERAL" /> POLÍCIA RODOVIÁRIA FEDERAL
								</label>
							</div>
							<div class="checkbox">
								<label>
									<input type="checkbox" name="base_dados[]" value="ÓRGÃO MUNICIPAL DE TRÂNSITO" /> ÓRGÃO MUNICIPAL DE TRÂNSITO
								</label>
							</div>
							<div class="checkbox">
								<label>
									<input type="checkbox" name="base_dados[]" value="ÓRGÃO ESTADUAL DE TRÂNSITO" /> ÓRGÃO ESTADUAL DE TRÂNSITO
								</label>
							</div>
							<div class="checkbox">
								<label>
									<input type="checkbox" name="base_dados[]" value="SAMU" /> SAMU
								</label>
							</div>
							<div class="checkbox">
								<label>
									<input type="checkbox" name="base_dados[]" value="CORPO DE BOMBEIROS" /> CORPO DE BOMBEIROS
								</label>
							</div>
							<div class="checkbox">
								<label>
									<input type="checkbox" name="base_dados[]" value="DELEGACIA DE TRÂNSITO" /> DELEGACIA DE TRÂNSITO
								</label>
							</div>
							<div class="checkbox">
								<label>
									<input type="checkbox" name="base_dados[]" value="IML" /> IML / SEGURANÇA PÚBLICA
								</label>
							</div>
							<div class="checkbox">
								<label>
									<input type="checkbox" name="base_dados[]" value="OUTRAS" /> OUTRAS
								</label>
							</div>
						</div>
					</div> 

					<div class="col-sm-5" id="_BASESAT_Outras" style="display: none;">   
						<label class="control-label">Como foram obtidos os dados?</label>
						<div class="col-sm-10" style="padding-left: 0px;"> 
							<input id="base_dados_outras" name="base_dados_outras" type="text" placeholder="" class="form-control input-md">
						</div>   
					</div>    
				</div>


				<div class="form-group">
					<label class="control-label col-md-12" for="BASESOBITO">4.3 Obtenção dos dados de óbitos causados por Acidentes de Trânsito:</label>

					<div class="radio col-sm-12">
						<label for="BASESOBITO-0">
							<input type="radio" name="BASESOBITO" id="BASESOBITO-0" value="SIM"  >
							Sim
						</label>

						<label for="BASESOBITO-1">
							<input type="radio" name="BASESOBITO" id="BASESOBITO-1" value="NAO">
							Não
						</label>
					</div>

				</div>

				<div class="form-group sub-menu" id="_BASESOBITO" style="display: none;">
					<div>
						<label class="col-xs-2 control-label">Base dos óbitos:</label>
						<div class="col-xs-6">
							<div class="checkbox">
								<label>
									<input type="checkbox" name="base_obitos[]" value="SIM" /> Sistema de informação de Mortalidade (SIM)
								</label>
							</div>
							<div class="checkbox">
								<label>
									<input type="checkbox" name="base_obitos[]" value="IML" /> IML / SEGURANÇA PÚBLICA
								</label>
							</div>
						</div>
					</div>      

				</div>

				<div class="form-group">
					<label class="control-label col-sm-12" for="BASEFERIDO">4.4 Obtenção dos dados de feridos graves (INTERNAÇÃO HOSPITALAR):</label>

					<div class="radio col-sm-12">
						<label for="BASEFERIDO-0">
							<input type="radio" name="BASEFERIDO" id="BASEFERIDO-0" value="SIM"  >
							Sim
						</label>

						<label for="BASEFERIDO-1">
							<input type="radio" name="BASEFERIDO" id="BASEFERIDO-1" value="NAO">
							Não
						</label>
					</div>

				</div>

				<div class="form-group sub-menu" id="_BASEFERIDO" style='display:none'>
					<div>
						<label class="col-xs-3 control-label">Base de dados de feridos graves:</label>
						<div class="col-xs-6">
							<div class="checkbox">
								<label>
									<input type="checkbox" name="base_feridos[]" value="SIH" />Sistema de informação Hospitalar (SIH)
								</label>
							</div>
							<div class="checkbox">
								<label>
									<input type="checkbox" name="base_feridos[]" value="OUTROS REGISTRO DE INTERNAÇÃO DE HOSPITAIS" /> OUTROS REGISTRO DE INTERNAÇÃO DE HOSPITAIS
								</label>
							</div>
						</div>
					</div>    

					<div class="col-sm-4" id="_base_feridos_hospital" style='display:none'>   
						<label class="control-label">Nome do Hospital</label>
						<input id="base-feridos-hospital" name="base_feridos_hospital" type="text" placeholder="" class="form-control input-md">
					</div>
				</div>


				<div class="form-group">
					<label class="control-label col-sm-12" for="MAPEAMENTO">4.5 Mapeamento e documentação dos processos e fluxo de dados de cada fonte de informação:
					</label>

					<div class="radio col-sm-12">
						<label for="MAPEAMENTO-0">
							<input type="radio" name="MAPEAMENTO" id="MAPEAMENTO-0" value="SIM"  >
							Sim
						</label>

						<label for="MAPEAMENTO-1">
							<input type="radio" name="MAPEAMENTO" id="MAPEAMENTO-1" value="NAO">
							Não
						</label>
					</div>


				</div>
				<div class="form-group">
					<label class="control-label col-sm-12" for="LIMPEZA">4.6 Limpeza e processamento das bases para a integração (linkagem) das bases de dados:</label>

					<div class="radio col-sm-12">
						<label for="LIMPEZA-0">
							<input type="radio" name="LIMPEZA" id="LIMPEZA-0" value="SIM"  >
							Sim
						</label>

						<label for="LIMPEZA-1">
							<input type="radio" name="LIMPEZA" id="LIMPEZA-1" value="NAO">
							Não
						</label>
					</div>


				</div>

				<div class="form-group">
					<label class="control-label col-sm-12" for="LISTAUNICA">4.7 Produção de uma lista Única de Vítimas:</label>

					<div class="radio col-sm-12">
						<label for="LISTAUNICA-0">
							<input type="radio" name="LISTAUNICA" id="LISTAUNICA-0" value="SIM"  >
							Sim
						</label>

						<label for="LISTAUNICA-1">
							<input type="radio" name="LISTAUNICA" id="LISTAUNICA-1" value="NAO">
							Não
						</label>
					</div>


				</div>
				<div class="form-group">
					<label class="control-label col-sm-12" for="FATORRISCO">4.8 Análise de fatores de risco:</label>

					<div class="radio col-sm-12">
						<label for="FATORRISCO-0">
							<input type="radio" name="FATORRISCO" id="FATORRISCO-0" value="SIM"  >
							Sim
						</label>

						<label for="CLASSIFICACAO-1">
							<input type="radio" name="FATORRISCO" id="FATORRISCO-1" value="NAO">
							Não
						</label>
					</div>


				</div>
				<div class="form-group">
					<label class="control-label col-sm-12" for="INDICADOROBITO">4.9 Produção dos indicadores finais de segurança no trânsito:</label>

					<div class="radio col-sm-12">
						<label for="INDICADOROBITO-0">
							<input type="radio" name="INDICADOROBITO" id="INDICADOROBITO-0" value="FERIOS_E_OBITOS"  >
							FERIDOS E ÓBITOS
						</label>

						<label for="INDICADOROBITO-1">
							<input type="radio" name="INDICADOROBITO" id="INDICADOROBITO-1" value="FERIDOS">
							APENAS FERIDOS GRAVES
						</label>
						<label for="INDICADOROBITO-0">
							<input type="radio" name="INDICADOROBITO" id="INDICADOROBITO-2" value="OBITOS"  >
							APENAS ÓBITOS
						</label>

						<label for="INDICADOROBITO-1">
							<input type="radio" name="INDICADOROBITO" id="INDICADOROBITO-3" value="NENHUM">
							NENHUM
						</label>
					</div>


				</div>



				<div class="form-group" id="_LINKAGE" style='display:none'>
					<label class="control-label col-sm-12" for="LINKAGE">4.10 Realização do procedimento de relacionamento (linkagem) das bases de dados de ocorrência de acidentes.  </label>

					<div class="radio col-sm-12">
						<label for="LINKAGE-0">
							<input type="radio" name="LINKAGE" id="LINKAGE-0" value="SIM" >
							Sim
						</label>

						<label for="LINKAGE-1">
							<input type="radio" name="LINKAGE" id="LINKAGE-1" value="NAO">
							Não
						</label>
					</div>

				</div>

				<div class="col-sm-12" id="_PRILINKAGE" style='display:none'>  
					<div class="form-group">
						<div class="col-sm-6"> 
							<label class="control-label"  for="PRILINKAGE">Qual o primeiro trimestre realizado?</label>  

							<select id='PRILINKAGE' name="PRILINKAGE" class="form-control">
								<option value=""></option>
								<option value="1">Primeiro</option>
								<option value="2">Segundo</option>
								<option value="3">Terceiro</option>
								<option value="4">Quarto</option>
							</select>
						</div>

						<div class="col-sm-6">
							<label class="control-label" for="PRIMEIROANOLINKAGE">Qual o primeiro ano realizado?</label>  

							<select id='PRIMEIROANOLINKAGE' name="PRIMEIROANOLINKAGE" class="form-control">
								<option value=""></option>
								@for ($i = 2015; $i <= date("Y"); $i++)
								<option value="{{ $i }}">{{ $i }}</option>
								@endfor
							</select>
						</div>
					</div>


					<div class="form-group" >
						<div class="col-sm-6">
							<label class="control-label"  for="ULTLINKAGE">Qual o último trimestre realizado?</label>  
							<select id='ULTLINKAGE' name="ULTLINKAGE" class="form-control">
								<option value=""></option>
								<option value="1">Primeiro</option>
								<option value="2">Segundo</option>
								<option value="3">Terceiro</option>
								<option value="4">Quarto</option>
							</select>
						</div>

						<div class="col-sm-6">
							<label class="control-label" for="ULTLINKAGEANOLINKAGE">Qual o último ano realizado?</label>  
							<select id='ULTLINKAGEANOLINKAGE' name="ULTLINKAGEANOLINKAGE" class="form-control">
								<option value=""></option>
								@for ($i = 2015; $i <= date("Y"); $i++)
								<option value="{{ $i }}">{{ $i }}</option>
								@endfor
							</select>
						</div>
					</div>

					<div class="form-group">
						<label class="control-label" for="BASESULTIMOTRIMESTRE">Quais as bases de dados usadas no último trimestre? </label>  
						<div class="form-group sub-menu" id="bases_utilizadas">
							<div>

								<div class="col-xs-6">
									<div class="checkbox"style='display:none'>
										<label>
											<input type="checkbox" name="bases_utilizadas[]" value="POLÍCIA MILITAR" /> POLÍCIA MILITAR
										</label>
									</div>
									<div class="checkbox"style='display:none'>
										<label>
											<input type="checkbox" name="bases_utilizadas[]" value="POLÍCIA RODOVIÁRIA ESTADUAL" /> POLÍCIA RODOVIÁRIA ESTADUAL
										</label>
									</div>
									<div class="checkbox"style='display:none'>
										<label>
											<input type="checkbox" name="bases_utilizadas[]" value="POLÍCIA RODOVIÁRIA FEDERAL" /> POLÍCIA RODOVIÁRIA FEDERAL
										</label>
									</div>
									<div class="checkbox"style='display:none'>
										<label>
											<input type="checkbox" name="bases_utilizadas[]" value="ÓRGÃO MUNICIPAL DE TRÂNSITO" /> ÓRGÃO MUNICIPAL DE TRÂNSITO
										</label>
									</div>
									<div class="checkbox"style='display:none'>
										<label>
											<input type="checkbox" name="bases_utilizadas[]" value="ÓRGÃO ESTADUAL DE TRÂNSITO" /> ORGÃO ESTADUAL DE TRÂNSITO
										</label>
									</div>
									<div class="checkbox"style='display:none'>
										<label>
											<input type="checkbox" name="bases_utilizadas[]" value="SAMU" /> SAMU
										</label>
									</div>
									<div class="checkbox"style='display:none'>
										<label>
											<input type="checkbox" name="bases_utilizadas[]" value="CORPO DE BOMBEIROS" /> CORPO DE BOMBEIROS
										</label>
									</div>
									<div class="checkbox"style='display:none'>
										<label>
											<input type="checkbox" name="bases_utilizadas[]" value="DELEGACIA DE TRÂNSITO" /> DELEGACIA DE TRÂNSITO
										</label>
									</div>
									<div class="checkbox"style='display:none'>
										<label>
											<input type="checkbox" name="bases_utilizadas[]" value="IML" /> IML / SEGURANÇA PÚBLICA
										</label>
									</div>

									<div class="checkbox"style='display:none'>
										<label>
											<input type="checkbox" name="bases_utilizadas[]" value="SIM" /> Sistema de informação de Mortalidade (SIM)
										</label>
									</div>
									<div class="checkbox"style='display:none'>
										<label>
											<input type="checkbox" name="bases_utilizadas[]" value="REGISTRO DE INTERNAÇÃO DE HOSPITAIS" /> REGISTRO DE INTERNAÇÃO DE HOSPITAIS
										</label>
									</div>
									<div class="checkbox"style='display:none'>
										<label>
											<input type="checkbox" name="bases_utilizadas[]" value="SIH" /> SISTEMA DE INFORMAÇÃO HOSPITALAR (SIH)

										</label>
									</div>
									<div class="checkbox"style='display:none'>
										<label>
											<input type="checkbox" name="bases_utilizadas[]" value="OUTRAS" /> OUTRAS
										</label>
									</div>


								</div>
							</div> 
							<div class="col-sm-4" id="_bases_utilizadas_hospital" style='display:none'>   
								<label class="control-label">Nome do Hospital</label>
								<input id="bases_utilizadas_hospital" name="bases_utilizadas_hospital" type="text" placeholder="" class="form-control input-md">
							</div>
							<div class="col-sm-5" id="_bases_utilizadas_outras" style="display: none;">   
								<label class="control-label">Como foram obtidos os dados?</label>
								<div class="col-sm-10" style="padding-left: 0px;"> 
									<input id="bases_utilizadas_outras" name="bases_utilizadas_outras" type="text" placeholder="" class="form-control input-md">
								</div>   
							</div> 
						</div>

					</div>
				</div>
				<div class="col-sm-12" id="NAO_PRILINKAGE" style='display:none'>  
					<div class="form-group">
						<label class="control-label" for="COMOFOILISTAVITIMAS">Como foi definido a lista de vÍtimas de acidente de trânsito no último trimestre? </label>  
						<input id="COMOFOILISTAVITIMAS" name="COMOFOILISTAVITIMAS" type="text" placeholder="" class="form-control input-md" >
					</div>

					<div class="form-group">
						<label class="control-label" for="NAOLINKOBITO">Caso o município não realize o procedimento de linkagem, como é definido o número final de óbitos?  </label>  
						<input id="NAOLINKOBITO" name="NAOLINKOBITO" type="text" placeholder="" class="form-control input-md" >
					</div>

					<div class="form-group">
						<label class="control-label" for="NAOLINKFER">Caso o município não realize o procedimento de linkagem, como é definido o número final de feridos graves?  </label>  
						<input id="NAOLINKFER" name="NAOLINKFER" type="text" placeholder="" class="form-control input-md" >
					</div>
				</div>





			</form>
		</section>

		<h2>ANÁLISE DE FATORES DE RISCO</h2>
		<section data-step="4">
			<div id="loading-analise" style="display:none;">  
				<img src="{{ asset('libraries/img/loading2.gif') }}" class="loading-step" alt="" style="width: 5%;">
			</div>
			<form id="ANALISE" class="form-horizontal" enctype="multipart/form-data" method="post">
				<input type="hidden" name="editado_analise" id="editado_analise" value="false" >
				<div class="form-group">
					<label class="control-label col-sm-12" for="IDENTIFICACAORISCO">5.1 O município realizou a discussão e identificação dos fatores de risco para cada um dos acidentes fatais? </label>

					<div class="radio col-sm-12">
						<label for="IDENTIFICACAORISCO-0">
							<input type="radio" name="IDENTIFICACAORISCO" id="IDENTIFICACAORISCO-0" value="SIM" >
							Sim
						</label>

						<label for="IDENTIFICACAORISCO-1">
							<input type="radio" name="IDENTIFICACAORISCO" id="IDENTIFICACAORISCO-1" value="NAO">
							Não
						</label>
					</div>

				</div>
				<div class="sub-menu" id="_IDENTIFICACAORISCO" style='display:none'>  
					<div class="form-group" >
						<div class="col-sm-5">
							<label class="control-label"  for="ULTIMOSEMESTRERISCO">Qual o último trimestre realizado?</label>  
							<select id='ULTIMOSEMESTRERISCO' name="ULTIMOSEMESTRERISCO" class="form-control">
								<option value=""></option>
								<option value="1">Primeiro</option>
								<option value="2">Segundo</option>
								<option value="3">Terceiro</option>
								<option value="4">Quarto</option>
							</select>
						</div>

						<div class="col-sm-5">
							<label class="control-label" for="ULTIMORISCO">Qual o último ano realizado?</label>  
							<select id='ULTIMORISCO' name="ULTIMORISCO" class="form-control">
								<option value=""></option>
								@for ($i = 2015; $i <= date("Y"); $i++)
								<option value="{{ $i }}">{{ $i }}</option>
								@endfor
							</select>
						</div>
					</div>

					<div class="form-group">
						<label class="control-label col-sm-12" for="FATORESRISCOACIDENTES">Foram identificados os fatores de risco para o envolvimento em acidentes fatais?</label>
						<div class="radio col-sm-12">
							<label for="FATORESRISCOACIDENTES-0">
								<input type="radio" name="FATORESRISCOACIDENTES" id="FATORESRISCOACIDENTES-0" value="SIM" >
								Sim
							</label>

							<label for="FATORESRISCOACIDENTES-1">
								<input type="radio" name="FATORESRISCOACIDENTES" id="FATORESRISCOACIDENTES-1" value="NAO">
								Não
							</label>
						</div>
						<div class="radio col-sm-12" id='_FATORESRISCOACIDENTES_SIM' style='display:none'>
							<label for="FATORESRISCOACIDENTES_SIM">
								<input type="radio" name="FATORESRISCOACIDENTES_SIM" id="FATORESRISCOACIDENTES_SIM-0" value="TOTAL">
								Total
							</label>

							<label for="FATORESRISCOACIDENTES_SIM-1">
								<input type="radio" name="FATORESRISCOACIDENTES_SIM" id="FATORESRISCOACIDENTES_SIM-1" value="AMOSTRA">
								Amostra
							</label>

						</div>		
						<div id="_amostra" class="col-md-6 input-group" style='display:none'>
							<input id="AMOSTRA" name="AMOSTRA" type="number" min="0" max="100" class="form-control input-md">
							<span class="input-group-addon">%</span>
						</div>  	

						<div class="form-group">
							<label class="control-label col-sm-12" for="CONDUTARISCOACIDENTES">Foram identificadas as condutas de risco relacionadas com o envolvimento em acidente fatal?</label>
							<div class="radio col-sm-12">
								<label for="CONDUTARISCOACIDENTES-0">
									<input type="radio" name="CONDUTARISCOACIDENTES" id="CONDUTARISCOACIDENTES-0" value="SIM" >
									Sim
								</label>

								<label for="CONDUTARISCOACIDENTES-1">
									<input type="radio" name="CONDUTARISCOACIDENTES" id="CONDUTARISCOACIDENTES-1" value="NAO">
									Não
								</label>
							</div>			
						</div>
						<div class="form-group">
							<label class="control-label col-sm-12" for="FATORESGRAVIDADE">Foram identificados os fatores que influenciam na gravidade das lesões?</label>
							<div class="radio col-sm-12">
								<label for="FATORESGRAVIDADE-0">
									<input type="radio" name="FATORESGRAVIDADE" id="FATORESGRAVIDADE-0" value="SIM" >
									Sim
								</label>

								<label for="FATORESGRAVIDADE-1">
									<input type="radio" name="FATORESGRAVIDADE" id="FATORESGRAVIDADE-1" value="NAO">
									Não
								</label>
							</div>			
						</div>
						<div class="form-group">
							<label class="control-label col-sm-12" for="FATORESFATAL">Foram identificados os usuários contributivos para o acidente fatal?</label>
							<div class="radio col-sm-12">
								<label for="FATORESFATAL-0">
									<input type="radio" name="FATORESFATAL" id="FATORESFATAL-0" value="SIM" >
									Sim
								</label>

								<label for="FATORESFATAL-1">
									<input type="radio" name="FATORESFATAL" id="FATORESFATAL-1" value="NAO">
									Não
								</label>
							</div>			
						</div>
					</div>
				</div>
				<div class="form-group">
					<label class="control-label col-sm-12" for="IDENTIFICACAORISCOCADA">5.2 O município realizou a discussão e identificação dos fatores de risco para cada um dos acidentes graves? </label>
					<div class="radio col-sm-12">
						<label for="IDENTIFICACAORISCOCADA-0">
							<input type="radio" name="IDENTIFICACAORISCOCADA" id="IDENTIFICACAORISCOCADA-0" value="SIM" >
							Sim
						</label>

						<label for="IDENTIFICACAORISCOCADA-1">
							<input type="radio" name="IDENTIFICACAORISCOCADA" id="IDENTIFICACAORISCOCADA-1" value="NAO">
							Não
						</label>
					</div>			
				</div>
				<div class="sub-menu" id="_IDENTIFICACAORISCOCADA" style='display:none'>  
					<div class="form-group" >
						<div class="col-sm-5">
							<label class="control-label"  for="ULTIMOSEMESTRERISCOCADA">Qual o último trimestre realizado?</label>  
							<select id='ULTIMOSEMESTRERISCOCADA' name="ULTIMOSEMESTRERISCOCADA" class="form-control">
								<option value=""></option>
								<option value="1">Primeiro</option>
								<option value="2">Segundo</option>
								<option value="3">Terceiro</option>
								<option value="4">Quarto</option>
							</select>
						</div>

						<div class="col-sm-5">
							<label class="control-label" for="ULTIMORISCOCADA">Qual o último ano realizado?</label>  
							<select id='ULTIMORISCOCADA' name="ULTIMORISCOCADA" class="form-control">					
								<option value=""></option>
								@for ($i = 2015; $i <= date("Y"); $i++)
								<option value="{{ $i }}">{{ $i }}</option>
								@endfor
							</select>
						</div>
					</div>
					<div class="form-group">
						<label class="control-label" for="FATORESRISCOACIDENTESCADA">Foram identificados os fatores de risco para o envolvimento em acidentes graves?</label>
						<div class="radio col-sm-12">
							<label for="FATORESRISCOACIDENTESCADA-0">
								<input type="radio" name="FATORESRISCOACIDENTESCADA" id="FATORESRISCOACIDENTESCADA-0" value="SIM" >
								Sim
							</label>

							<label for="FATORESRISCOACIDENTESCADA-1">
								<input type="radio" name="FATORESRISCOACIDENTESCADA" id="FATORESRISCOACIDENTESCADA-1" value="NAO">
								Não
							</label>
						</div>			
					</div>
					<div class="form-group">
						<label class="control-label" for="CONDUTARISCOACIDENTESCADA">Foram identificadas as condutas de risco relacionadas com o envolvimento em acidente grave?</label>
						<div class="radio col-sm-12">
							<label for="CONDUTARISCOACIDENTESCADA-0">
								<input type="radio" name="CONDUTARISCOACIDENTESCADA" id="CONDUTARISCOACIDENTESCADA-0" value="SIM" >
								Sim
							</label>

							<label for="CONDUTARISCOACIDENTESCADA-1">
								<input type="radio" name="CONDUTARISCOACIDENTESCADA" id="CONDUTARISCOACIDENTESCADA-1" value="NAO">
								Não
							</label>
						</div>			
					</div>
					<div class="form-group">
						<label class="control-label" for="FATORESGRAVIDADECADA">Foram identificados os fatores que influenciam na gravidade das lesões ?</label>
						<div class="radio col-sm-12">
							<label for="FATORESGRAVIDADECADA-0">
								<input type="radio" name="FATORESGRAVIDADECADA" id="FATORESGRAVIDADECADA-0" value="SIM" >
								Sim
							</label>

							<label for="FATORESGRAVIDADECADA-1">
								<input type="radio" name="FATORESGRAVIDADECADA" id="FATORESGRAVIDADECADA-1" value="NAO">
								Não
							</label>
						</div>			
					</div>
					<div class="form-group">
						<label class="control-label" for="FATORESFATALCADA">Foram identificados os usuários contributivos para os acidentes graves?</label>
						<div class="radio col-sm-12">
							<label for="FATORESFATALCADA-0">
								<input type="radio" name="FATORESFATALCADA" id="FATORESFATALCADA-0" value="SIM" >
								Sim
							</label>

							<label for="FATORESFATALCADA-1">
								<input type="radio" name="FATORESFATALCADA" id="FATORESFATALCADA-1" value="NAO">
								Não
							</label>
						</div>			
					</div>


				</div>
				<div class="form-group">
					<label class="control-label  col-sm-12" for="CONSTRUCAOQUADROMULTIPLO">5.3 O município realizou o preenchimento do Quadro Múltiplo Integrado?</label>
					<div class="radio col-sm-12">
						<label for="CONSTRUCAOQUADROMULTIPLO-0">
							<input type="radio" name="CONSTRUCAOQUADROMULTIPLO" id="CONSTRUCAOQUADROMULTIPLO-0" value="SIM" >
							Sim
						</label>

						<label for="CONSTRUCAOQUADROMULTIPLO-1">
							<input type="radio" name="CONSTRUCAOQUADROMULTIPLO" id="CONSTRUCAOQUADROMULTIPLO-1" value="NAO">
							Não
						</label>
					</div>			
				</div>
				<div class="sub-menu" id="_CONSTRUCAOQUADROMULTIPLO" style='display:none'>  

					<div class="form-group" >
						<div class="col-sm-5">
							<label class="control-label"  for="ULTIMOSEMESTRECONSTRUCAOQUADROMULTIPLO">Qual o último trimestre realizado?</label>  
							<select id='ULTIMOSEMESTRECONSTRUCAOQUADROMULTIPLO' name="ULTIMOSEMESTRECONSTRUCAOQUADROMULTIPLO" class="form-control">
								<option value=""></option>
								<option value="1">Primeiro</option>
								<option value="2">Segundo</option>
								<option value="3">Terceiro</option>
								<option value="4">Quarto</option>
							</select>
						</div>

						<div class="col-sm-5">
							<label class="control-label" for="ULTIMOCONSTRUCAOQUADROMULTIPLO">Qual o último ano realizado?</label>  
							<select id='ULTIMOCONSTRUCAOQUADROMULTIPLO' name="ULTIMOCONSTRUCAOQUADROMULTIPLO" class="form-control">
								<option value=""></option>
								@for ($i = 2015; $i <= date("Y"); $i++)
								<option value="{{ $i }}">{{ $i }}</option>
								@endfor
							</select>
						</div>
					</div>  
				</div>       
				<div class="form-group">
					<label class="control-label col-sm-12" for="FATORESCHAVE">5.4 O município definiu os fatores-chave de risco local?</label>
					<div class="radio col-sm-12">
						<label for="FATORESCHAVE-0">
							<input type="radio" name="FATORESCHAVE" id="FATORESCHAVE-0" value="SIM" >
							Sim
						</label>

						<label for="FATORESCHAVE-1">
							<input type="radio" name="FATORESCHAVE" id="FATORESCHAVE-1" value="NAO">
							Não
						</label>
					</div>			
				</div>
				<div class="sub-menu" id="_FATORESCHAVE" style='display:none'>  
					<div class="form-group" >


						<div class="col-sm-5">
							<label class="control-label" for="ULTIMOFATORESCHAVE">Qual o último ano realizado?</label>  
							<select id='ULTIMOFATORESCHAVE' name="ULTIMOFATORESCHAVE" class="form-control">
								<option value=""></option>
								@for ($i = 2015; $i <= date("Y"); $i++)
								<option value="{{ $i }}">{{ $i }}</option>
								@endfor
							</select>
						</div>
					</div>  

					<div class="form-group">
						<div>
							<label class="col-xs-4 control-label">Qual ou Quais foram os principais fatores de risco, condutas de risco e FATORES QUE INFLUENCIAM NA GRAVIDADE DAS Lesões no último ano?</label>
							<div class="col-xs-6">
								<div class="checkbox">
									<label>
										<input type="checkbox" name="PRINCIPAISFATORESCHAVE[]" value="VELOCIDADE" /> VELOCIDADE EXCESSIVA OU INADEQUADA
									</label>
								</div>
								<div class="checkbox">
									<label>
										<input type="checkbox" name="PRINCIPAISFATORESCHAVE[]" value="ÁLCOOL" /> DIRIGIR SOB INFLUÊNCIA DE ÁLCOOL
									</label>
								</div>
								<div class="checkbox">
									<label>
										<input type="checkbox" name="PRINCIPAISFATORESCHAVE[]" value="INFRAESTRUTURA" /> INFRAESTRUTURA INADEQUADA
									</label>
								</div>
								<div class="checkbox">
									<label>
										<input type="checkbox" name="PRINCIPAISFATORESCHAVE[]" value="VEÍCULO" />CONDIÇÕES DO VEÍCULO
									</label>
								</div>
								<div class="checkbox">
									<label>
										<input type="checkbox" name="PRINCIPAISFATORESCHAVE[]" value="FADIGA" /> FADIGA
									</label>
								</div>
								<div class="checkbox">
									<label>
										<input type="checkbox" name="PRINCIPAISFATORESCHAVE[]" value="VISIBILIDADE" /> VISIBILIDADE INADEQUADA
									</label>
								</div>

								<div class="checkbox">
									<label>
										<input type="checkbox" name="PRINCIPAISFATORESCHAVE[]" value="DROGAS" />  DIRIGIR SOB INFLUÊNCIA DE DROGAS
									</label>
								</div>
								<div class="checkbox">
									<label>
										<input type="checkbox" name="PRINCIPAISFATORESCHAVE[]" value="OUTROS EQUIPAMENTOS ELETRÔNICOS" /> OUTROS EQUIPAMENTOS ELETRÔNICOS
									</label>
								</div>
								<div class="checkbox">
									<label>
										<input type="checkbox" name="PRINCIPAISFATORESCHAVE[]" value="CELULAR" /> USO DE CELULAR
									</label>
								</div>
								<div class="checkbox">
									<label>
										<input type="checkbox" name="PRINCIPAISFATORESCHAVE[]" value="AVANÇAR SINAL SEMAFÓRICO" /> AVANÇAR SINAL SEMAFÓRICO
									</label>
								</div>
								<div class="checkbox">
									<label>
										<input type="checkbox" name="PRINCIPAISFATORESCHAVE[]" value="CONDUTOR SEM HABILITAÇÃO" /> CONDUTOR SEM HABILITAÇÃO
									</label>
								</div>
								<div class="checkbox">
									<label>
										<input type="checkbox" name="PRINCIPAISFATORESCHAVE[]" value="TRANSITAR EM LOCAL PROIBIDO" /> TRANSITAR EM LOCAL PROIBIDO
									</label>
								</div>
								<div class="checkbox">
									<label>
										<input type="checkbox" name="PRINCIPAISFATORESCHAVE[]" value="TRANSITAR EM LOCAL IMPRÓPRIO" /> TRANSITAR EM LOCAL IMPRÓPRIO
									</label>
								</div>
								<div class="checkbox">
									<label>
										<input type="checkbox" name="PRINCIPAISFATORESCHAVE[]" value="MUDANÇA DE FAIXA / PISTA SEM SINALIZAÇÃO PRÉVIA" /> MUDANÇA DE FAIXA / PISTA SEM SINALIZAÇÃO PRÉVIA
									</label>
								</div>
								<div class="checkbox">
									<label>
										<input type="checkbox" name="PRINCIPAISFATORESCHAVE[]" value="NÃO MANTER DISTÂNCIA MÍNIMA ENTRE VEÍCULOS" /> NÃO MANTER DISTÂNCIA MÍNIMA ENTRE VEÍCULOS
									</label>
								</div>
								<div class="checkbox">
									<label>
										<input type="checkbox" name="PRINCIPAISFATORESCHAVE[]" value="CONVERTER / CRUZAR SEM DAR PREFERÊNCIA" /> CONVERTER / CRUZAR SEM DAR PREFERÊNCIA
									</label>
								</div>
								<div class="checkbox">
									<label>
										<input type="checkbox" name="PRINCIPAISFATORESCHAVE[]" value="NÃO DAR PREFERÊNCIA AO PEDESTRE NA FAIXA A ELE DESTINADA" />NÃO DAR PREFERÊNCIA AO PEDESTRE NA FAIXA A ELE DESTINADA
									</label>
								</div>
								<div class="checkbox">
									<label>
										<input type="checkbox" name="PRINCIPAISFATORESCHAVE[]" value="ATITUDE IMPRUDENTE DO PEDESTRE" />ATITUDE IMPRUDENTE DO PEDESTRE
									</label>
								</div>
								<div class="checkbox">
									<label>
										<input type="checkbox" name="PRINCIPAISFATORESCHAVE[]" value="CINTO DE SEGURANÇA" />CINTO DE SEGURANÇA
									</label>
								</div>
								<div class="checkbox">
									<label>
										<input type="checkbox" name="PRINCIPAISFATORESCHAVE[]" value="EQUIPAMENTO DE RETENÇÃO PARA CRIANÇAS" />EQUIPAMENTO DE RETENÇÃO PARA CRIANÇAS
									</label>
								</div>
								<div class="checkbox">
									<label>
										<input type="checkbox" name="PRINCIPAISFATORESCHAVE[]" value="AIRBAG" />Ausência de “air-bags”
									</label>
								</div>
								<div class="checkbox">
									<label>
										<input type="checkbox" name="PRINCIPAISFATORESCHAVE[]" value="CAPACETE" />CAPACETE
									</label>
								</div>
								<div class="checkbox">
									<label>
										<input type="checkbox" name="PRINCIPAISFATORESCHAVE[]" value="OUTROS" />OUTROS
									</label>
								</div>
							</div>
						</div>                        
					</div>       
				</div>  

				<div class="form-group">
					<label class="control-label col-sm-12" for="GRUPOSVITIMAS">5.5 O município definiu os principais grupos de vítimas?</label>
					<div class="radio col-sm-12">
						<label for="GRUPOSVITIMAS-0">
							<input type="radio" name="GRUPOSVITIMAS" id="GRUPOSVITIMAS-0" value="SIM" >
							Sim
						</label>

						<label for="GRUPOSVITIMAS-1">
							<input type="radio" name="GRUPOSVITIMAS" id="GRUPOSVITIMAS-1" value="NAO">
							Não
						</label>
					</div>			
				</div>
				<div class="sub-menu" id="_GRUPOSVITIMAS" style='display:none'>  

					<div class="form-group" >
<!--                         		<div class="col-sm-5">
                        			<label class="control-label"  for="ULTIMOSEMESTREGRUPOSVITIMAS">Qual o último trimestre realizado?</label>  
                        			<select id='ULTIMOSEMESTREGRUPOSVITIMAS' name="ULTIMOSEMESTREGRUPOSVITIMAS" class="form-control">
                        				<option value=""></option>
                        				<option value="1">Primeiro</option>
                        				<option value="2">Segundo</option>
                        				<option value="3">Terceiro</option>
                        				<option value="4">Quarto</option>
                        			</select>
                        		</div> -->

                        		<div class="col-sm-5">
                        			<label class="control-label" for="ULTIMOGRUPOSVITIMAS">Qual o último ano realizado?</label>  
                        			<select id='ULTIMOGRUPOSVITIMAS' name="ULTIMOGRUPOSVITIMAS" class="form-control">
                        				<option value=""></option>
                        				@for ($i = 2015; $i <= date("Y"); $i++)
                        				<option value="{{ $i }}">{{ $i }}</option>
                        				@endfor
                        			</select>
                        		</div>
                        	</div> 


                        	<div class="form-group">
                        		<div>
                        			<label class="col-xs-4 control-label">Qual ou Quais foram os principais grupos de vítimas no último ANO?</label>
                        			<div class="col-xs-6">
                        				<div class="checkbox">
                        					<label>
                        						<input type="checkbox" name="PRINCIPAISGRUPOSVITIMAS[]" value="PEDESTRE" /> PEDESTRE
                        					</label>
                        				</div>
                        				<div class="checkbox">
                        					<label>
                        						<input type="checkbox" name="PRINCIPAISGRUPOSVITIMAS[]" value="CONDUTOR MOTOCICLETA" /> CONDUTOR MOTOCICLETA
                        					</label>
                        				</div>
                        				<div class="checkbox">
                        					<label>
                        						<input type="checkbox" name="PRINCIPAISGRUPOSVITIMAS[]" value="CONDUTOR AUTOMÓVEL" /> CONDUTOR AUTOMÓVEL
                        					</label>
                        				</div>
                        				<div class="checkbox">
                        					<label>
                        						<input type="checkbox" name="PRINCIPAISGRUPOSVITIMAS[]" value="PASSAGEIRO MOTOCICLETA" /> PASSAGEIRO MOTOCICLETA
                        					</label>
                        				</div>
                        				<div class="checkbox">
                        					<label>
                        						<input type="checkbox" name="PRINCIPAISGRUPOSVITIMAS[]" value="PASSAGEIRO AUTOMÓVEL" /> PASSAGEIRO AUTOMÓVEL
                        					</label>
                        				</div>
                        				<div class="checkbox">
                        					<label>
                        						<input type="checkbox" name="PRINCIPAISGRUPOSVITIMAS[]" value="OCUPANTE DE BICICLETA" /> CICLISTA
                        					</label>
                        				</div>
                        				<div class="checkbox">
                        					<label>
                        						<input type="checkbox" name="PRINCIPAISGRUPOSVITIMAS[]" value="OCUPANTE DE VEICULO PESADO" /> OCUPANTE DE VEÍCULO DE PESADO
                        					</label>
                        				</div>
                        				<div class="checkbox">
                        					<label>
                        						<input type="checkbox" name="PRINCIPAISGRUPOSVITIMAS[]" value="OCUPANTE DE EQUIPAMENTO DE MOBILIDADE" /> OCUPANTE DE EQUIPAMENTO DE MOBILIDADE INDIVIDUAL (PATINETE, SKATE E OUTROS)
                        					</label>
                        				</div>
                        				<div class="checkbox">
                        					<label>
                        						<input type="checkbox" name="PRINCIPAISGRUPOSVITIMAS[]" value="OUTRO" />OUTRO
                        					</label>
                        				</div>
                        				<div class="col-sm-10" id="_PRINCIPAISGRUPOSVITIMAS_OUTRO" style="display: none;">   

                        					<div class="col-sm-10" style="padding-left: 0px;"> 
                        						<input id="PRINCIPAISGRUPOSVITIMAS_OUTRO" name="PRINCIPAISGRUPOSVITIMAS_OUTRO" type="text" class="form-control input-md">
                        					</div>   
                        				</div>

                        			</div>
                        		</div>                        
                        	</div> 
                        </div>       
                        <div class="form-group">
                        	<label class="control-label col-sm-12" for="PROGRAMAPRIORITARIOS">5.6 Foram definidos os principais programas de segurança no trânsito do município a partir da análise dos fatores de risco, condutas de risco e FATORES QUE INFLUENCIAM NA GRAVIDADE DAS Lesões no último ano?
                        	</label>
                        	<div class="radio col-sm-12">
                        		<label for="PROGRAMAPRIORITARIOS-0">
                        			<input type="radio" name="PROGRAMAPRIORITARIOS" id="PROGRAMAPRIORITARIOS-0" value="SIM" >
                        			Sim
                        		</label>

                        		<label for="PROGRAMAPRIORITARIOS-1">
                        			<input type="radio" name="PROGRAMAPRIORITARIOS" id="PROGRAMAPRIORITARIOS-1" value="NAO">
                        			Não
                        		</label>
                        	</div>			
                        </div>
                        <div class="sub-menu" id="_PROGRAMAPRIORITARIOS" style='display:none'>  

                        	<div class="form-group" >

                        		<div class="col-sm-4">
                        			<label class="control-label" for="ULTIMOPROGRAMAPRIORITARIOS">Qual o último ano realizado?</label>  
                        			<select id='ULTIMOPROGRAMAPRIORITARIOS' name="ULTIMOPROGRAMAPRIORITARIOS" class="form-control">
                        				<option value=""></option>
                        				@for ($i = 2015; $i <= date("Y"); $i++)
                        				<option value="{{ $i }}">{{ $i }}</option>
                        				@endfor
                        			</select>
                        		</div>
                        	</div> 

                        </div>       
                    </form>
                </section>

                <h2>AÇÕES INTEGRADAS DE SEGURANÇA NO TRÂNSITO</h2>
                <section data-step="5">
                	<div id="loading-acoes" style="display:none;">  
                		<img src="{{ asset('libraries/img/loading2.gif') }}" class="loading-step" alt="" style="width: 5%;">
                	</div>
                	<form id="ACOES" class="form-horizontal" enctype="multipart/form-data">
                		<div class="form-group">
                			<label class="control-label col-sm-12" for="ACOESINTEGRADAS">6.1 O município possui plano de ações integradas de segurança no trânsito?
                			</label>
                			<div class="radio col-sm-12">
                				<label for="ACOESINTEGRADAS-0">
                					<input type="radio" name="ACOESINTEGRADAS" id="ACOESINTEGRADAS-0" value="SIM" >
                					Sim
                				</label>

                				<label for="ACOESINTEGRADAS-1">
                					<input type="radio" name="ACOESINTEGRADAS" id="ACOESINTEGRADAS-1" value="NAO">
                					Não
                				</label>
                			</div>			
                		</div>
                		<div class="sub-menu" id="_ACOESINTEGRADAS" style='display:none'>  
                			<div class="form-group">
                				<div class="col-sm-4">
                					<label class="control-label" for="ULTIMOACOESINTEGRADAS">Qual o último ano?</label>  
                					<select id='ULTIMOACOESINTEGRADAS' name="ULTIMOACOESINTEGRADAS" class="form-control">
                						<option value=""></option>
                						@for ($i = 2015; $i <= date("Y"); $i++)
                						<option value="{{ $i }}">{{ $i }}</option>
                						@endfor
                					</select>
                				</div>
                			</div>


                			<div class="form-group">
                				<div>
                					<label class="col-xs-4 control-label">Qual ou Quais o(s) programa(s) prioritários definidos pelo plano do último ano?</label>
                					<div class="col-xs-6">
                						<div class="checkbox">
                							<label>
                								<input type="checkbox" name="PRINCIPAISACOESINTEGRADAS[]" value="VELOCIDADE" /> VELOCIDADE
                							</label>
                						</div>
                						<div class="checkbox">
                							<label>
                								<input type="checkbox" name="PRINCIPAISACOESINTEGRADAS[]" value="ÁLCOOL" /> ÁLCOOL
                							</label>
                						</div>
                						<div class="checkbox">
                							<label>
                								<input type="checkbox" name="PRINCIPAISACOESINTEGRADAS[]" value="MOTOCICLETA" /> MOTOCICLETA
                							</label>
                						</div>
                						<div class="checkbox">
                							<label>
                								<input type="checkbox" name="PRINCIPAISACOESINTEGRADAS[]" value="PEDESTRE" /> PEDESTRE
                							</label>
                						</div>
                						<div class="checkbox">
                							<label>
                								<input type="checkbox" name="PRINCIPAISACOESINTEGRADAS[]" value="OUTRO" /> OUTRO
                							</label>
                						</div>
                						<div class="col-sm-10" id="_PRINCIPAISACOESINTEGRADAS_OUTRO" style="display: none;">   
                							<div class="col-sm-10" style="padding-left: 0px;"> 
                								<input id="PRINCIPAISACOESINTEGRADAS_OUTRO" name="PRINCIPAISACOESINTEGRADAS_OUTRO" type="text" class="form-control input-md">
                							</div>   
                						</div>

                					</div>
                				</div>                     
                			</div>       
                		</div>  
                	</form>
                </section>

                <h2>MONITORAMENTO DO PLANO DE AÇÕES INTEGRADAS DE SEGURANÇA NO TRÂNSITO</h2>
                <section data-step="6">
                	<div id="loading-monitoramento" style="display:none;">  
                		<img src="{{ asset('libraries/img/loading2.gif') }}" class="loading-step" alt="" style="width: 5%;">
                	</div>
                	<form id="MONITORAMENTO" class="form-horizontal" enctype="multipart/form-data">
                		<div class="form-group">
                			<label class="control-label col-sm-12" for="BEBERDIRIGIR">7.1 Foi CONSTRUÍDO o quadro de monitoramento referente ao fator de risco beber e dirigir ?
                			</label>
                			<div class="radio col-sm-12">
                				<label for="BEBERDIRIGIR-0">
                					<input type="radio" name="BEBERDIRIGIR" id="BEBERDIRIGIR-0" value="SIM" >
                					Sim
                				</label>

                				<label for="BEBERDIRIGIR-1">
                					<input type="radio" name="BEBERDIRIGIR" id="BEBERDIRIGIR-1" value="NAO">
                					Não
                				</label>
                			</div>			
                		</div>
                		<div class="col-sm-12" id="_BEBERDIRIGIR" style='display:none'>  

                			<div class="form-group">
                				<div class="col-sm-4">
                					<label class="control-label" for="ULTIMOBEBERDIRIGIR">Qual o último ano?</label>  
                					<select id='ULTIMOBEBERDIRIGIR' name="ULTIMOBEBERDIRIGIR" class="form-control">
                						<option value=""></option>
                						@for ($i = 2015; $i <= date("Y"); $i++)
                						<option value="{{ $i }}">{{ $i }}</option>
                						@endfor
                					</select>
                				</div>
                			</div>
                			<div class="form-group">
                				<label class="control-label col-sm-12" for="QUADROBEBERDIRIGIR">O quadro apresenta os percentuais de cumprimento das metas de cada projeto?</label>
                				<div class="radio col-sm-12">
                					<label for="QUADROBEBERDIRIGIR-0">
                						<input type="radio" name="QUADROBEBERDIRIGIR" id="QUADROBEBERDIRIGIR-0" value="SIM" >
                						Sim
                					</label>

                					<label for="QUADROBEBERDIRIGIR-1">
                						<input type="radio" name="QUADROBEBERDIRIGIR" id="QUADROBEBERDIRIGIR-1" value="NAO">
                						Não
                					</label>
                				</div>			
                			</div>
                		</div>  
                		<div class="form-group">
                			<label class="control-label col-sm-12" for="VELOCIDADE">7.2 Foi CONSTRUÍDO o quadro de monitoramento referente ao fator de risco Velocidade excessiva ou inadequada?
                			</label>
                			<div class="radio col-sm-12">
                				<label for="VELOCIDADE-0">
                					<input type="radio" name="VELOCIDADE" id="VELOCIDADE-0" value="SIM" >
                					Sim
                				</label>

                				<label for="VELOCIDADE-1">
                					<input type="radio" name="VELOCIDADE" id="VELOCIDADE-1" value="NAO">
                					Não
                				</label>
                			</div>			
                		</div>
                		<div class="sub-menu" id="_VELOCIDADE" style='display:none'>  
                			<div class="form-group">
                				<div class="col-sm-4">
                					<label class="control-label" for="ULTIMOVELOCIDADE">Qual o último ano?</label>  
                					<select id='ULTIMOVELOCIDADE' name="ULTIMOVELOCIDADE" class="form-control">
                						<option value=""></option>
                						@for ($i = 2015; $i <= date("Y"); $i++)
                						<option value="{{ $i }}">{{ $i }}</option>
                						@endfor
                					</select>
                				</div>
                			</div>    

                			<div class="form-group">
                				<label class="control-label col-sm-12" for="QUADROVELOCIDADE">O quadro apresenta os percentuais de cumprimento das metas de cada projeto?</label>
                				<div class="radio col-sm-12">
                					<label for="QUADROVELOCIDADE-0">
                						<input type="radio" name="QUADROVELOCIDADE" id="QUADROVELOCIDADE-0" value="SIM" >
                						Sim
                					</label>

                					<label for="QUADROVELOCIDADE-1">
                						<input type="radio" name="QUADROVELOCIDADE" id="QUADROVELOCIDADE-1" value="NAO">
                						Não
                					</label>
                				</div>			
                			</div>
                		</div>  
                		<div class="form-group">
                			<label class="control-label col-sm-12" for="DEFINIDOMUNICIPIO">7.3 Foi CONSTRUÍDO o quadro de monitoramento referente a outros fatores de risco?
                			</label>
                			<div class="radio col-sm-12">
                				<label for="DEFINIDOMUNICIPIO-0">
                					<input type="radio" name="DEFINIDOMUNICIPIO" id="DEFINIDOMUNICIPIO-0" value="SIM" >
                					Sim
                				</label>

                				<label for="DEFINIDOMUNICIPIO-1">
                					<input type="radio" name="DEFINIDOMUNICIPIO" id="DEFINIDOMUNICIPIO-1" value="NAO">
                					Não
                				</label>
                			</div>			
                		</div>

                		<div class="sub-menu" id="_DEFINIDOMUNICIPIOS" style='display:none'>  


                			<div class="col-sm-12" id="QUADROGRUPOVITIMAS_QUAIS">   

                				<label class="control-label">QUAL / QUAIS?</label>
                				<textarea name="QUADRODEFINIDOMUNICIPIO" class="form-control" id="QUADRODEFINIDOMUNICIPIO" rows="4" cols="50" style="width: 90%;"></textarea>

                			</div>
                			<div class="col-sm-4">
                				<label class="control-label" for="ULTIMODEFINIDOMUNICIPIO">Qual o último ano?</label>  
                				<select id='ULTIMODEFINIDOMUNICIPIO' name="ULTIMODEFINIDOMUNICIPIO" class="form-control">
                					<option value=""></option>
                					@for ($i = 2015; $i <= date("Y"); $i++)
                					<option value="{{ $i }}">{{ $i }}</option>
                					@endfor
                				</select>
                			</div>
                		</div>

                		<div class="form-group">
                			<label class="control-label col-sm-12" for="QUADROGRUPOVITIMAS">7.4 Foi CONSTRUÍDO o quadro de monitoramento referente ao grupo de vítimas?
                			</label>
                			<div class="radio col-sm-12">
                				<label for="QUADROGRUPOVITIMAS-0">
                					<input type="radio" name="QUADROGRUPOVITIMAS" id="QUADROGRUPOVITIMAS-0" value="SIM" >
                					Sim
                				</label>

                				<label for="QUADROGRUPOVITIMAS-1">
                					<input type="radio" name="QUADROGRUPOVITIMAS" id="QUADROGRUPOVITIMAS-1" value="NAO">
                					Não
                				</label>
                			</div>			
                		</div>
                		<div class="sub-menu" id="_QUADROGRUPOVITIMAS" style='display:none'>  


                			<div class="col-sm-12" id="QUADROGRUPOVITIMAS_QUAIS">   

                				<div class="col-xs-6">
                					<label class="control-label">QUAL / QUAIS?</label>
                					<div class="checkbox">
                						<label>
                							<input type="checkbox" name="QUADROGRUPOVITIMAS_QUAIS[]" value="PEDESTRE" /> PEDESTRE
                						</label>
                					</div>
                					<div class="checkbox">
                						<label>
                							<input type="checkbox" name="QUADROGRUPOVITIMAS_QUAIS[]" value="CONDUTOR MOTOCICLETA" /> CONDUTOR MOTOCICLETA
                						</label>
                					</div>
                					<div class="checkbox">
                						<label>
                							<input type="checkbox" name="QUADROGRUPOVITIMAS_QUAIS[]" value="CONDUTOR AUTOMÓVEL" /> CONDUTOR AUTOMÓVEL
                						</label>
                					</div>
                					<div class="checkbox">
                						<label>
                							<input type="checkbox" name="QUADROGRUPOVITIMAS_QUAIS[]" value="PASSAGEIRO MOTOCICLETA" /> PASSAGEIRO MOTOCICLETA
                						</label>
                					</div>
                					<div class="checkbox">
                						<label>
                							<input type="checkbox" name="QUADROGRUPOVITIMAS_QUAIS[]" value="PASSAGEIRO AUTOMÓVEL" /> PASSAGEIRO AUTOMÓVEL
                						</label>
                					</div>
                					<div class="checkbox">
                						<label>
                							<input type="checkbox" name="QUADROGRUPOVITIMAS_QUAIS[]" value="OCUPANTE DE BICICLETA" /> CICLISTA
                						</label>
                					</div>
                					<div class="checkbox">
                						<label>
                							<input type="checkbox" name="QUADROGRUPOVITIMAS_QUAIS[]" value="OCUPANTE DE VEICULO PESADO" /> OCUPANTE DE VEÍCULO DE PESADO
                						</label>
                					</div>
                					<div class="checkbox">
                						<label>
                							<input type="checkbox" name="QUADROGRUPOVITIMAS_QUAIS[]" value="OCUPANTE DE EQUIPAMENTO DE MOBILIDADE" /> OCUPANTE DE EQUIPAMENTO DE MOBILIDADE INDIVIDUAL (PATINETE, SKATE E OUTROS)
                						</label>
                					</div>
                				</div>
                			</div>
                			<div class="col-sm-4">
                				<label class="control-label" for="ULTIMOQUADROGRUPOVITIMAS">Qual o último ano?</label>  
                				<select id='ULTIMOQUADROGRUPOVITIMAS' name="ULTIMOQUADROGRUPOVITIMAS" class="form-control">
                					<option value=""></option>
                					@for ($i = 2015; $i <= date("Y"); $i++)
                					<option value="{{ $i }}">{{ $i }}</option>
                					@endfor
                				</select>
                			</div>
                		</div>

                	</form>
                </section>
                <h2>Validação do Cadastro</h2>
                <section data-step="7">
                	<div id="loading-finaliza" style="display:none;">  
                		<img src="{{ asset('libraries/img/loading2.gif') }}" class="loading-step" alt="" style="width: 5%;">
                	</div>
                	<div id="FINALIZA">
                		Seu cadastro foi concluído com sucesso.
                		<button class="relatorio btn btn-primary" onclick="verRelatorioCompleto()">Ver Relátorio</button>
                		<button class="exportarDados btn btn-primary" onclick="exportarDados()">Exportar Dados</button>
                	</div>
                </section>
            </div>

            <div id="errors"></div>
        </div>

        @endsection
        @section('scripts')
        <script type="text/javascript" src="//cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.6.1/js/bootstrap-datepicker.min.js"></script>
        <script type="text/javascript" src="//cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.6.1/locales/bootstrap-datepicker.pt-BR.min.js"></script>
        <script src="libraries/js/form-bootstrap.min.js" type="text/javascript"></script>
        <script src="{{asset('libraries/js/formValidation.min.js')}}" type="text/javascript"></script> 
        <script src="{{asset('libraries/js/jquery.steps.js')}}" type="text/javascript"></script> 


        <script src="{{asset('libraries/js/form-bootstrap.min.js')}}" type="text/javascript"></script> 


        {{-- <script src="{{asset('libraries/js/validate_form.js')}}" type="text/javascript"></script>  --}}
        <script src="{{asset('libraries/js/pt_BR.js')}}" type="text/javascript"></script> 
        <script src="{{asset('libraries/js/jquery.mask.js')}}" type="text/javascript"></script> 

        <script type="text/javascript">
        	var recuperado_implantacao = false;
        	var recuperado_qualidade = false;
        	var recuperado_analise = false;
        	var recuperado_acoes = false;
        	var recuperado_monitoramento = false;
        	var recuperado = false;


        	var atual = 0;

        	function verRelatorioCompleto(){
        		console.log(atual)

        		$(".editarButton").remove()
        		$(".content").css({"width": "100%"});
        		$("#Estado").prop("disabled", true);
        		$("#Cidade").prop("disabled", true);
        		$("#Ano").prop("disabled", true);

        		$("#btn_editar_acoes").remove()
        		$("#btn_editar_implantacao").remove()
        		$("#btn_editar_qualidade").remove()
        		$("#btn_editar_coordenadores").remove()
        		$("#btn_editar_analise").remove()
        		$("#btn_editar_monitoramento").remove()

        		$("#pvt-p-1").show()
        		$("#pvt-p-2").show()
        		$("#pvt-p-3").show()
        		$("#pvt-p-4").show()
        		$("#pvt-p-5").show()
        		$("#pvt-p-6").show()
        		$("#pvt-p-7").hide()
        		$(".steps").hide()
        		$(".relatorio").hide()
        		$(".actions").hide()

        		$( "button[name='Editar']" ).remove()
        	}
        	function exportarDados(){
        		
        		var newForm = $('<form>', {
        			'action': '{{ route('situacao.exportar') }}',
        			'target': '_top',
        			'method': 'POST'
        		})
        		newForm.append($('<input>', {
        			'name': '_token',
        			'value': '{{ csrf_token() }}',
        			'type': 'hidden'
        		}));
        		newForm.append($('<input>', {
        			'name': 'Ano',
        			'value': $("#Ano").val(),
        			'type': 'hidden'
        		}));
        		newForm.append($('<input>', {
        			'name': 'CodCidade',
        			'value': $("#CodCidade").val(),
        			'type': 'hidden'
        		}));
        		$(document.body).append(newForm);
        		newForm.submit();   
        	}

        	function atualiza(valor){
        		atual = valor;
        	}

        	function recuperar_coordenadores() {
        		$('a[role="menuitem"]').each(function( index, item ) { 
        			$(item).addClass('disabled')
        		})
        		$("#loading-coordenadores").show()
        		$("#COORDENADORES").hide()
        		$.ajax({
        			url: '{{ route('situacao.coordenadores.get') }}',
        			data: {acao: "recuperar", CodCidade: $("#CodCidade").val(), Ano: $("#Ano").val()},
        			type: 'POST',
        			headers: {
        				'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        			},
        			success: function (output) {
        				$('a[role="menuitem"]').each(function( index, item ) { 
        					$(item).removeClass('disabled')
        				})
        				$("#loading-coordenadores").hide()
        				$("#COORDENADORES").show()
        				if (output != 'vazio') {
        					var Vals = JSON.parse(output);
        					console.log(Vals)

        					if (Vals.coordenaTEM == '1'){
        						$('input:radio[name=coordenaTEM][value=SIM]').attr('checked', true);
        						$('input:radio[name=coordenaTEM][value=SIM]').click();
        					} else {
        						$('input:radio[name=coordenaTEM][value=NAO]').attr('checked', true);
        						$('input:radio[name=coordenaTEM][value=NAO]').click();
        					}
        					$('input:radio[name=coordenaTEM]').prop("disabled", true);
        					$("#COORDENADOR1").val(Vals.Nome);
        					$("#COORDENADOR1").prop("disabled", true);
        					$("#INSTITUICAO1").val(Vals.Instiuicao);
        					$("#INSTITUICAO1").prop("disabled", true);
        					$("#EMAIL1").val(Vals.Email);
        					$("#EMAIL1").prop("disabled", true);
        					$("#TEL1").val(Vals.Telefone);
        					$("#TEL1").prop("disabled", true);
        					$("#TEL1-2").val(Vals.Telefone1);
        					$("#TEL1-2").prop("disabled", true);

        					$("#COORDENADOR2").val(Vals.Coordenador2);
        					$("#COORDENADOR2").prop("disabled", true);
        					$("#INSTITUICAO2").val(Vals.Instituicao2);
        					$("#INSTITUICAO2").prop("disabled", true);
        					$("#EMAIL2").val(Vals.Email2);
        					$("#EMAIL2").prop("disabled", true);
        					$("#TEL2").val(Vals.Telefone2);
        					$("#TEL2").prop("disabled", true);
        					$("#TEL2-2").val(Vals.Telefone2_2);
        					$("#TEL2-2").prop("disabled", true);

        					$("#COORDENADOR3").val(Vals.Coordenador3);
        					$("#COORDENADOR3").prop("disabled", true);
        					$("#INSTITUICAO3").val(Vals.Instituicao3);
        					$("#INSTITUICAO3").prop("disabled", true);
        					$("#EMAIL3").val(Vals.Email3);
        					$("#EMAIL3").prop("disabled", true);
        					$("#TEL3").val(Vals.Telefone3);
        					$("#TEL3").prop("disabled", true);
        					$("#TEL3-2").val(Vals.Telefone3_2);
        					$("#TEL3-2").prop("disabled", true);
                //botao editar
                if ($('#btn_editar_coordenadores').length == 0) {
                	let dataFormatada = Vals.created_at;
                	$('#COORDENADORES').prepend('<div id="EDIT_COORDENADORES"><span id="EDIT_Alterado">Alterado Por: ' + Vals.AlteradoPor + ' em ' + dataFormatada +'</span>\n\
                		<button type="button" id="btn_editar_coordenadores" name="Editar" class="btn pull-right btn-default editarButton">Editar</button>\n\n\
                		</div>\n\
                		');
                }
                $("#COORDENADORES").find('.has-success').removeClass('has-success')

                recuperado = true;
                return true;
            } else {
            	recuperado = false;
            	return false;
            }

        },
        error: function () {
        	alertify.error('Erro ao buscar dados de coordenadores');
        	return false;
        }
    });
        	}
        	function recuperar_implantacao() {
        		
        		$('a[role="menuitem"]').each(function( index, item ) { 
        			$(item).addClass('disabled')
        		})
        		$("#loading-implantacao").show()
        		$("#IMPLANTACAO").hide()
        		var periodos = ['SEMANAL', 'QUINZENAL', 'MENSAL', 'BIMESTRAL', 'QUADRIMESTRAL'];
        		var registro = ['ATA', 'RELATÓRIO'];
        		$.ajax({url: '{{route('situacao.implantacao.get')}}',
        			data: {Acao: "recuperar", CodCidade: $("#CodCidade").val(), Ano: $("#Ano").val()},
        			type: 'POST',
        			headers: {
        				'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        			},
        			success: function (output) {
        				$('#link_implantacao').remove()
        				$('a[role="menuitem"]').each(function( index, item ) { 
        					$(item).removeClass('disabled')
        				})
        				$("#loading-implantacao").hide()
        				$("#IMPLANTACAO").show()
        				if (output != 'vazio') {
        					var Vals = JSON.parse(output);
        					if ($('#btn_editar_implantacao').length == 0) {
        						let dataFormatada = Vals.created_at;
        						$('#IMPLANTACAO').prepend('<div id="EDIT_IMPLANTACAO"><span id="EDIT_Alterado">Alterado Por: ' + Vals.AlteradoPor + ' em ' + dataFormatada + '</span>\n\
        							<button type="button" id="btn_editar_implantacao" name="Editar" class="btn pull-right btn-default editarButton">Editar</button>\n\n\
        							</div>\n\
        							');
        					}

        					$('input[name=COMISSAO][value=' + Vals.COMISSAO + ']').click();
        					if (Vals.COMISSAO === 'SIM') {
        						$('input[name=NOMECOMISSAO]').val(Vals.NOMECOMISSAO);
        						$('input[name=NOMECOMISSAO]').prop("disabled", true);

        						$("#DTDECRETO").val(Vals.DTDECRETO);
        						$("#DTDECRETO").prop("disabled", true);

        						$("#DECRETO").val(Vals.DECRETO);
        						$("#DECRETO").prop("disabled", true);

        						if (Vals.UPDECRETO != 'N\u00c3O SE APLICA') {
        							$('#InputUPDECRETO').hide();
        							if ($('#link_implantacao').length == 0) {
        								$('<a/>').attr({
        									id: "link_implantacao",
        									name: "link",
        									href: 'storage/' + Vals.UPDECRETO,
        									text: "ver arquivo",
        									target: "_blank"
        								}).appendTo('#_UPDECRETO').html("Visualizar Arquivo");
        							} else {
        								$("#UPDECRETO").prop("disabled", true);
        							}
        						}
                    //recuperar instituicoes 

                    $.each(Vals.instituicoes, function (key, val) {
                    	console.log(key, val)
                    	$("#instituicao" + (key + 1)).val(Vals.instituicoes[key].NOME);
                    	$("#setor" + (key + 1)).val(Vals.instituicoes[key].SETOR);
                    	$("#origem" + (key + 1)).val(Vals.instituicoes[key].ORIGEM);

                    	if ((key > 1) && ($("#ImplantacaoInstituicoes" + (key + 1)).length == 0) && (Vals.instituicoes[key].nome !== '')) {
                    		$('#ImplantacaoInstituicoes').append('<tr id="ImplantacaoInstituicoes' + (key + 1) + '">\n\n\
                    			<td>\n\
                    			<input id="instituicao' + (key + 1) + '" type="text" class="form-control" name="instituicao[]">\n\
                    			</td>\n\
                    			<td>\n\
                    			<select id="setor' + (key + 1) + '" name="setor[]" class="form-control" style="padding-right:5px;padding-left:5px;">\n\
                    			<option value=""></option>\n\
                    			<option value="Saúde">Saúde</option>\n\
                    			<option value="Trânsito">Trânsito</option>\n\
                    			<option value="Segurança pública">Segurança pública</option>\n\
                    			<option value="Instituições acadêmicas">Instituições acadêmicas</option>\n\
                    			<option value="Educação">Educação</option>\n\
                    			<option value="Sociedade civil">Sociedade civil</option>\n\
                    			<option value="Organizações não governamentais">Organizações não governamentais</option>\n\
                    			<option value="Empresas privadas">Empresas privadas</option>\n\
                    			<option value="Infraestrutura Urbana">Infraestrutura Urbana</option>\n\
                    			<option value="Planejamento">Planejamento</option>\n\
                    			<option value="JUSTIÇA">JUSTIÇA</option>\n\
                    			<option value="TRANSPORTE">TRANSPORTE</option>\n\
                    			<option value="Outras">Outras</option>\n\
                    			</select>\n\
                    			</td>\n\
                    			<td>\n\
                    			<select id="origem' + (key + 1) + '" name="origem[]" class="form-control" style="padding-right:0px;">\n\
                    			<option value=""></option>\n\
                    			<option value="Governamental">Governamental</option>\n\
                    			<option value="Não Governamental">Não Governamental</option>\n\
                    			</select>\n\
                    			</td>\n\
                    			<td>\n\
                    			<button type="button" class="btn btn-default removeButton"><i class="glyphicon glyphicon-minus"></i></button> \n\
                    			</td>\n\
                    			</tr>');
                    		$("#instituicao" + (key + 1)).val(Vals.instituicoes[key].nome);
                    		$("#instituicao" + (key + 1)).prop("disabled", true);
                    		$("#setor" + (key + 1)).val(Vals.instituicoes[key].setor);
                    		$("#setor" + (key + 1)).prop("disabled", true);
                    		$("#origem" + (key + 1)).val(Vals.instituicoes[key].origem);
                    		$("#origem" + (key + 1)).prop("disabled", true);
                    	}
                    });


                    $('#IMPLANTACAO').find('.addButton').hide()
                    $('#IMPLANTACAO').find('.removeButton').hide()
                }

                $('input[name=COMISSAO]').prop("disabled", true);

                //console.log(Vals.PERIODIC)
                if ($.inArray(Vals.PERIODIC, periodos) > -1) {
                	$('input:radio[name=PERIODIC][value="' + Vals.PERIODIC + '"]').click();
                } else {
                    //console.log('aui')
                    $('input:radio[name=PERIODIC][value=OUTRA]').click();
                    //$('input[name=outradata]').prop("disabled", true);
                    $('input[name=outradata]').val([Vals.PERIODIC]);

                }
                if ($.inArray(Vals.REGREUNIAOCI, registro) > -1) {
                	$('input:radio[name=REGREUNIAOCI][value=' + Vals.REGREUNIAOCI + ']').click();
                } else {
                	$('input:radio[name=REGREUNIAOCI][value=OUTRA]').click();
                	//$('input[name=REGREUNIAOCIoutra]').prop("disabled", true);
                	$('input[name=REGREUNIAOCIoutra]').val(Vals.REGREUNIAOCI);
                }
                if ($.inArray(Vals.DTREUNIAOCPVT, periodos) > -1) {
                	$('input:radio[name=DATAREUNIAOCPVT][value=' + Vals.DTREUNIAOCPVT + ']').click();
                } else {
                	$('input:radio[name=DATAREUNIAOCPVT][value=OUTRA]').click();
                	$('input[name=DATAREUNIAOCPVToutra]').prop("disabled", true);
                	$('input[name=DATAREUNIAOCPVToutra]').val(Vals.DTREUNIAOCPVT);
                }

                if ($.inArray(Vals.REGREUNIAOCPVT, registro) > -1) {
                	$('input:radio[name=REGREUNIAOCPVT][value=' + Vals.REGREUNIAOCPVT + ']').click();
                } else {
                	$('input:radio[name=REGREUNIAOCPVT][value=OUTRA]').click();
                	$('input[name=REGREUNIAOCPVToutra]').prop("disabled", true);
                	$('input[name=REGREUNIAOCPVToutra]').val(Vals.REGREUNIAOCPVT);
                }

                $('input[name=DTREUNIAOCPVT]').prop("disabled", true);
                $('input[name=DATAREUNIAOCPVT]').prop("disabled", true);
                $('input[name=PERIODIC]').prop("disabled", true);
                $('input[name=REGREUNIAOCI]').prop("disabled", true);
                $('input[name=REGREUNIAOCPVT]').prop("disabled", true);

                $("#DTREUNIAOCI").val(Vals.DTREUNIAOCI);
                $("#DTREUNIAOCI").prop("disabled", true);
                //Desabilitar os campos
                $("#IMPLANTACAO input").prop("disabled", true);
                $("#IMPLANTACAO select").prop("disabled", true);
                $("#IMPLANTACAO").find('.has-success').removeClass('has-success')
                recuperado_implantacao = true;
                return true;
            } else {
            	$('#link_implantacao').remove()
            	recuperado_implantacao = false;
            	return false;
            }
        },
        error: function () {
        	alertify.error('Erro ao buscar dados de implantação');
        	return false;
        }
    });
}
function recuperar_qualidade() {
	$('a[role="menuitem"]').each(function( index, item ) { 
		$(item).addClass('disabled')
	})
	$("#loading-qualidade").show()
	$("#QUALIDADE").hide()
	$.ajax({url: '{{route('situacao.qualidade.get')}}',
		data: {Acao: "recuperar", CodCidade: $("#CodCidade").val(), Ano: $("#Ano").val()},
		type: 'POST',
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		},
		success: function (output) {
			$('#link_qualidade').remove()
			$('a[role="menuitem"]').each(function( index, item ) { 
				$(item).removeClass('disabled')
			})
			$("#loading-qualidade").hide()
			$("#QUALIDADE").show()
            //console.log('qualidade')
          //  console.log(output);
          if (output != 'vazio') {
          	var Vals = JSON.parse(output);

          	var periodos = ['Semanal', 'Quinzenal', 'Mensal', 'Bimestral'];
          	var registro = ['Ata', 'Relatório'];

                //botao editar
                if ($('#btn_editar_qualidade').length == 0) {
                	let dataFormatada = Vals.created_at;
                	$('#QUALIDADE').prepend('<div id="EDIT_QUALIDADE"><span id="EDIT_Alterado">Alterado Por: ' + Vals.AlteradoPor + ' em ' + dataFormatada + '</span>\n\
                		<button type="button" id="btn_editar_qualidade" name="Editar" class="btn pull-right btn-default editarButton">Editar</button>\n\n\
                		</div>\n\
                		');
                }

                $('input[name=COMISSAOGD][value=' + Vals.COMISSAO + ']').trigger('click');
                if (Vals.COMISSAOGD === 'SIM') {
                	$('input:radio[name=COMISSAOGD][value=SIM]').trigger('click');
                	if (Vals.COMISSAOFORM === 'SIM') {
                		$('input:radio[name=COMISSAOFORM][value=SIM]').trigger('click');
                		$('input[name=COMISSAODOC]').val(Vals.COMISSAODOC);
                		$("#DTCOMISSAO").val(Vals.DTCOMISSAO);
                		$("#NCOMISSAO").val(Vals.NCOMISSAO);

                		if (Vals.UPDECRETOCOMISSAO != 'N\u00c3O SE APLICA') {
                			$('#InputUPDECRETOCOMISSAO').hide();
                			if ($('#link_qualidade').length == 0) {
                				$('<a/>').attr({
                					id: "link_qualidade",
                					name: "link",
                					href: 'storage/' + Vals.UPDECRETOCOMISSAO,
                					text: "ver arquivo",
                					target: "_blank"
                				}).appendTo('#_UPDECRETOCOMISSAO').html("Visualizar Arquivo");
                			}
                		}
                        //recuperar instituicoes                                                  
                        $.each(Vals.instituicoes, function (key, val) {

                        	$("#Qualidade_instituicao" + (key + 1)).val(Vals.instituicoes[key].NOME);
                        	$("#Qualidade_setor" + (key + 1)).val(Vals.instituicoes[key].SETOR);
                        	$("#Qualidade_origem" + (key + 1)).val(Vals.instituicoes[key].ORIGEM);

                        	if ((key > 1) && ($("#QualidadeInstituicoes" + (key + 1)).length == 0)) {
                        		$('#QualidadeInstituicoes').append('<tr id="ImplantacaoInstituicoes' + (key + 1) + '">\n\n\
                        			<td>\n\
                        			<input id="Qualidade_instituicao' + (key + 1) + '" type="text" class="form-control" name="Qualidade_instituicao[]">\n\
                        			</td>\n\
                        			<td>\n\
                        			<select id="Qualidade_setor' + (key + 1) + '" name="Qualidade_setor[]" class="form-control" style="padding-right:5px;padding-left:5px;">\n\
                        			<option value=""></option>\n\
                        			<option value="Saúde">Saúde</option>\n\
                        			<option value="Trânsito">Trânsito</option>\n\
                        			<option value="Segurança pública">Segurança pública</option>\n\
                        			<option value="Instituições acadêmicas">Instituições acadêmicas</option>\n\
                        			<option value="Educação">Educação</option>\n\
                        			<option value="Sociedade civil">Sociedade civil</option>\n\
                        			<option value="Organizações não governamentais">Organizações não governamentais</option>\n\
                        			<option value="Empresas privadas">Empresas privadas</option>\n\
                        			<option value="Infraestrutura Urbana">Infraestrutura Urbana</option>\n\
                        			<option value="Planejamento">Planejamento</option>\n\
                        			<option value="Outras">Outras</option>\n\
                        			<option value="JUSTIÇA">JUSTIÇA</option>\n\
                        			<option value="TRANSPORTE">TRANSPORTE</option>\n\
                        			</select>\n\
                        			</td>\n\
                        			<td>\n\
                        			<select id="Qualidade_origem' + (key + 1) + '" name="Qualidade_origem[]" class="form-control" style="padding-right:0px;">\n\
                        			<option value=""></option>\n\
                        			<option value="Governamental">Governamental</option>\n\
                        			<option value="Não Governamental">Não Governamental</option>\n\
                        			</select>\n\
                        			</td>\n\
                        			<td>\n\
                        			<button type="button" class="btn btn-default removeButton"><i class="glyphicon glyphicon-minus"></i></button> \n\
                        			</td>\n\
                        			</tr>');
                        		$("#Qualidade_instituicao" + (key + 1)).val(Vals.instituicoes[key].nome);
                        		$("#Qualidade_setor" + (key + 1)).val(Vals.instituicoes[key].setor);
                        		$("#Qualidade_origem" + (key + 1)).val(Vals.instituicoes[key].origem);
                        	}
                        });


                        $('#QUALIDADE').find('.addButton').hide()
                        $('#QUALIDADE').find('.removeButton').hide()
                    } else {
                    	$('input:radio[name=COMISSAOFORM][value=NAO]').click();
                    }
                } else {
                	$('input:radio[name=COMISSAOGD][value=NAO]').click();
                }


                var BASESAT = Vals.BASESAT.split(',');
                if (BASESAT[0] === 'NAO') {
                	$('input:radio[name=BASESAT][value=NAO]').click();
                } else {
                	$('input:radio[name=BASESAT][value=SIM]').click();
                	$.each(BASESAT, function (key, val) {
                		if (key > 0) {
                			$('input:checkbox[name="base_dados[]"][value="' + val + '"]').trigger('click');
                			$('input:checkbox[name="base_dados[]"][value="' + val + '"]').prop("checked", true);

                			if (val == 'OUTRAS') {
                				$('#_BASESAT_Outras').show()
                				$('#base_dados_outras').val(BASESAT[(BASESAT.length - 1)])

                			}
                		}
                	})

                }
                if (Vals.BASESOBITO === 'NAO') {
                	$('input:radio[name=BASESOBITO][value=NAO]').click();
                } else {
                	var BASESOBITO = Vals.BASESOBITO.split(',');

                	$('input:radio[name=BASESOBITO][value=SIM]').click();
                	$.each(BASESOBITO, function (key, val) {
                		if (key > 0) {
                			$('input:checkbox[name="base_obitos[]"][value="' + val + '"]').trigger('click');
                			$('input:checkbox[name="base_obitos[]"][value="' + val + '"]').prop("checked", true);

                		}
                	})
                }
                if (Vals.BASEFERIDO === 'NAO') {
                	$('input:radio[name=BASEFERIDO][value=NAO]').click();
                } else {
                	var BASEFERIDO = Vals.BASEFERIDO.split(',');
                	$('input:radio[name=BASEFERIDO][value=SIM]').click();
                	$.each(BASEFERIDO, function (key, val) {
                		if (key > 0) {
                			$('input:checkbox[name="base_feridos[]"][value="' + val + '"]').trigger('click');
                			$('input:checkbox[name="base_feridos[]"][value="' + val + '"]').prop("checked", true);

                			if (val == 'REGISTRO DE INTERNAÇÃO DE HOSPITAIS') {
                				$('#_base_feridos_hospital').show()
                				$('#base-feridos-hospital').val(BASEFERIDO[(BASEFERIDO.length - 1)])

                			}
                		}
                	})
                }
                if (Vals.MAPEAMENTO === 'SIM') {
                	$('input:radio[name=MAPEAMENTO][value=SIM]').click();
                } else {
                	$('input:radio[name=MAPEAMENTO][value=NAO]').click();
                }
                if (Vals.LIMPEZA === 'SIM') {
                	$('input:radio[name=LIMPEZA][value=SIM]').click();
                } else {
                	$('input:radio[name=LIMPEZA][value=NAO]').click();
                }
                if (Vals.LISTAUNICA === 'SIM') {
                	$('input:radio[name=LISTAUNICA][value=SIM]').click();
                } else {
                	$('input:radio[name=LISTAUNICA][value=NAO]').click();
                }
                if (Vals.FATORRISCO === 'SIM') {
                	$('input:radio[name=FATORRISCO][value=SIM]').click();
                } else {
                	$('input:radio[name=FATORRISCO][value=NAO]').click();
                }
                if (Vals.INDICADOROBITO == 'NENHUM') {
                	$('input:radio[name=INDICADOROBITO][value=NENHUM]').click();
                } else if (Vals.INDICADOROBITO == 'FERIDOS'){
                	$('input:radio[name=INDICADOROBITO][value=FERIDOS]').click();
                } else if (Vals.INDICADOROBITO == 'OBITOS'){
                	$('input:radio[name=INDICADOROBITO][value=OBITOS]').click();
                } else if (Vals.INDICADOROBITO == 'FERIOS_E_OBITOS'){
                	$('input:radio[name=INDICADOROBITO][value=FERIOS_E_OBITOS]').click();
                }
                if ((Vals.BASESAT !== 'NAO') && (Vals.BASESOBITO !== 'NAO') && (Vals.BASEFERIDO !== 'NAO')) {
                	$("#_LINKAGE").show();
                	if (Vals.LINKAGE === 'NAO') {
                		$('input:radio[name=LINKAGE][value=NAO]').click();
                		$('#COMOFOILISTAVITIMAS').val(Vals.COMOFOILISTAVITIMAS);
                		$('#NAOLINKOBITO').val(Vals.NAOLINKOBITO);
                		$('#NAOLINKFER').val(Vals.NAOLINKFER);
                	} else {
                		var LINKAGE = Vals.LINKAGE.split(',');
                		var PRILINKAGE = Vals.PRILINKAGE.split('/');
                		var ULTLINKAGE = Vals.ULTLINKAGE.split('/');

                		$('#PRIMEIROANOLINKAGE').val(PRILINKAGE[1]);
                		$('#PRILINKAGE').val(PRILINKAGE[0]);
                		$('#ULTLINKAGE').val(ULTLINKAGE[0]);
                		$('#ULTLINKAGEANOLINKAGE').val(ULTLINKAGE[1]);

                		$('input:radio[name=LINKAGE][value=SIM]').click();
                		$.each(LINKAGE, function (key, val) {
                			if (key > 0) {
                				$('input:checkbox[name="bases_utilizadas[]"][value="' + val + '"]').trigger('click');
                				$('input:checkbox[name="bases_utilizadas[]"][value="' + val + '"]').prop("checked", true);
                				if (val == 'OUTRAS') {
                					$('#_bases_utilizadas_outras').show()
                					$('#bases_utilizadas_outras').val(LINKAGE[(LINKAGE.length - 1)])

                				}
                				if (val == 'REGISTRO DE INTERNAÇÃO DE HOSPITAIS') {
                					$('#_bases_utilizadas_hospital').show()
                					$('#bases_utilizadas_hospital').val(BASEFERIDO[(BASEFERIDO.length - 1)])

                				}
                			}
                		})

                	}

                }

                //Desabilitar os campos
                $("#QUALIDADE input").prop("disabled", true);
                $("#QUALIDADE select").prop("disabled", true);
                $("#QUALIDADE").find('.has-success').removeClass('has-success')
                recuperado_qualidade = true;
                return true;
            } else {
            	$('#link_qualidade').remove()
            	recuperado_qualidade = false;
            	return false;
            }
        },
        error: function () {
        	alertify.error('Erro ao buscar dados de qualidade');
        	return false;
        }
    });
}
function recuperar_analise() {
	$('a[role="menuitem"]').each(function( index, item ) { 
		$(item).addClass('disabled')
	})
	$("#loading-analise").show()
	$("#ANALISE").hide()
	$.ajax({url: '{{ route('situacao.analise.get') }}',
		data: {Acao: "recuperar", CodCidade: $("#CodCidade").val(), Ano: $("#Ano").val()},
		type: 'POST',
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		},
		success: function (output) {
			$('a[role="menuitem"]').each(function( index, item ) { 
				$(item).removeClass('disabled')
			})
			$("#loading-analise").hide()
			$("#ANALISE").show()
            //console.log(output)
            if (output != 'vazio') {
            	var Vals = JSON.parse(output);

                //botao editar
                if ($('#btn_editar_analise').length == 0) {
                	let dataFormatada = Vals.created_at;
                	$('#ANALISE').prepend('<div id="EDIT_ANALISE"><span id="EDIT_Alterado">Alterado Por: ' + Vals.AlteradoPor + ' em ' + dataFormatada + '</span>\n\
                		<button type="button" id="btn_editar_analise" name="Editar" class="btn pull-right btn-default editarButton">Editar</button>\n\n\
                		</div>\n\
                		');
                }
                if (Vals.IDENTIFICACAORISCO === 'SIM') {
                	$('input:radio[name=IDENTIFICACAORISCO][value=SIM]').click();
                	$('input:radio[name=FATORESRISCOACIDENTES][value="' + Vals.FATORESRISCOACIDENTES + '"]').click();
                	$('input:radio[name=CONDUTARISCOACIDENTES][value="' + Vals.CONDUTARISCOACIDENTES + '"]').click();
                	$('input:radio[name=FATORESGRAVIDADE][value="' + Vals.FATORESGRAVIDADE + '"]').click();
                	$('input:radio[name=FATORESFATAL][value="' + Vals.FATORESFATAL + '"]').click();
                	var ULTIMORISCO = Vals.ULTIMORISCO.split('/');
                	$('#ULTIMORISCO').val(ULTIMORISCO[1]);
                	$('#ULTIMOSEMESTRERISCO').val(ULTIMORISCO[0]);
                	if (Vals.FATORESRISCOACIDENTES === 'SIM') {
                		$('input:radio[name=FATORESRISCOACIDENTES_SIM][value="' + Vals.FATORESRISCOACIDENTES_SIM + '"]').click();
                		if(Vals.FATORESRISCOACIDENTES_SIM == 'AMOSTRA'){
                			$('#AMOSTRA').val(Vals.AMOSTRA)
                		}
                	}
                } else {
                	$('input:radio[name=IDENTIFICACAORISCO][value=NAO]').click();
                }
                if (Vals.IDENTIFICACAORISCOCADA === 'SIM') {
                    //console.log(Vals);
                    $('input:radio[name=IDENTIFICACAORISCOCADA][value=SIM]').click();
                    $('input:radio[name="FATORESRISCOACIDENTESCADA"][value="' + Vals.FATORESRISCOACIDENTESCADA + '"]').click();
                    $('input:radio[name=CONDUTARISCOACIDENTESCADA][value="' + Vals.CONDUTARISCOACIDENTESCADA + '"]').click();
                    $('input:radio[name=FATORESGRAVIDADECADA][value="' + Vals.FATORESGRAVIDADECADA + '"]').click();
                    $('input:radio[name=FATORESFATALCADA][value="' + Vals.FATORESFATALCADA + '"]').click();
                    var ULTIMORISCOCADA = Vals.ULTIMORISCOCADA.split('/');
                    $('#ULTIMORISCOCADA').val(ULTIMORISCOCADA[1]);
                    $('#ULTIMOSEMESTRERISCOCADA').val(ULTIMORISCO[0]);
                } else {
                	$('input:radio[name=IDENTIFICACAORISCOCADA][value=NAO]').click();
                }
                if (Vals.CONSTRUCAOQUADROMULTIPLO === 'SIM') {
                	$('input:radio[name=CONSTRUCAOQUADROMULTIPLO][value=SIM]').click();
                	var ULTIMOCONSTRUCAOQUADROMULTIPLO = Vals.ULTIMOCONSTRUCAOQUADROMULTIPLO.split('/');
                	$('#ULTIMOCONSTRUCAOQUADROMULTIPLO').val(ULTIMOCONSTRUCAOQUADROMULTIPLO[1]);
                	$('#ULTIMOSEMESTRECONSTRUCAOQUADROMULTIPLO').val(ULTIMOCONSTRUCAOQUADROMULTIPLO[0]);
                } else {
                	$('input:radio[name=CONSTRUCAOQUADROMULTIPLO][value=NAO]').click();
                }
                if (Vals.PROGRAMAPRIORITARIOS === 'SIM') {
                	$('input:radio[name=PROGRAMAPRIORITARIOS][value=SIM]').click();
                	$('#ULTIMOPROGRAMAPRIORITARIOS').val(Vals.ULTIMOPROGRAMAPRIORITARIOS);
                } else {
                	$('input:radio[name=PROGRAMAPRIORITARIOS][value=NAO]').click();
                }
                if (Vals.FATORESCHAVE === 'SIM') {
                	$('input:radio[name=FATORESCHAVE][value=SIM]').click();
                	var ULTIMOFATORESCHAVE = Vals.ULTIMOFATORESCHAVE.split('/');
                	$('#ULTIMOFATORESCHAVE').val(ULTIMOFATORESCHAVE[1]);
                	$('#ULTIMOSEMESTREFATORESCHAVE').val(ULTIMOFATORESCHAVE[0]);
                	var PRINCIPAISFATORESCHAVE = Vals.PRINCIPAISFATORESCHAVE.split(',');
                	$.each(PRINCIPAISFATORESCHAVE, function (key, val) {
                		$('input:checkbox[name="PRINCIPAISFATORESCHAVE[]"][value="' + val + '"]').click()
                		$('input:checkbox[name="PRINCIPAISFATORESCHAVE[]"][value="' + val + '"]').prop("checked", true);

                	})
                } else {
                	$('input:radio[name=FATORESCHAVE][value=NAO]').click();
                }
                if (Vals.GRUPOSVITIMAS === 'SIM') {
                	$('input:radio[name=GRUPOSVITIMAS][value=SIM]').click();
                	var ULTIMOGRUPOSVITIMAS = Vals.ULTIMOGRUPOSVITIMAS.split('/');
                	$('#ULTIMOGRUPOSVITIMAS').val(ULTIMOGRUPOSVITIMAS[1]);
                	$('#ULTIMOSEMESTREGRUPOSVITIMAS').val(ULTIMOGRUPOSVITIMAS[0]);
                	var PRINCIPAISGRUPOSVITIMAS = Vals.PRINCIPAISGRUPOSVITIMAS.split(',');
                	$.each(PRINCIPAISGRUPOSVITIMAS, function (key, val) {
                		$('input:checkbox[name="PRINCIPAISGRUPOSVITIMAS[]"][value="' + val + '"]').click()
                		$('input:checkbox[name="PRINCIPAISGRUPOSVITIMAS[]"][value="' + val + '"]').prop("checked", true);
                		if (val == 'OUTRO') {
                			$('#_PRINCIPAISGRUPOSVITIMAS_OUTRO').show()
                			$('#PRINCIPAISGRUPOSVITIMAS_OUTRO').val(PRINCIPAISGRUPOSVITIMAS[(PRINCIPAISGRUPOSVITIMAS.length - 1)])

                		}
                	})
                } else {
                	$('input:radio[name=GRUPOSVITIMAS][value=NAO]').click();
                }
                //Desabilitar os campos
                $("#ANALISE input").prop("disabled", true);
                $("#ANALISE select").prop("disabled", true);
                $("#ANALISE").find('.has-success').removeClass('has-success')
                recuperado_analise = true;
                return true;
            } else {
            	recuperado_analise = false;
            	return false;
            }

        },
        error: function () {
        	alertify.error('Erro ao buscar dados de análise');
        	return false;
        }
    });
}
function recuperar_acoes() {
	$('a[role="menuitem"]').each(function( index, item ) { 
		$(item).addClass('disabled')
	})
	$("#loading-acoes").show()
	$("#ACOES").hide()
	$.ajax({url: '{{ route('situacao.acoes.get') }}',
		data: {Acao: "recuperar", CodCidade: $("#CodCidade").val(), Ano: $("#Ano").val()},
		type: 'POST',
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		},
		success: function (output) {
			$('a[role="menuitem"]').each(function( index, item ) { 
				$(item).removeClass('disabled')
			})
			$("#loading-acoes").hide()
			$("#ACOES").show()
            //console.log(output)
            if (output != 'vazio') {
            	var Vals = JSON.parse(output);
                //console.log(output)
                //botao editar
                if ($('#btn_editar_acoes').length == 0) {
                	let dataFormatada = Vals.created_at;
                	$('#ACOES').prepend('<div id="EDIT_ACOES"><span id="EDIT_Alterado">Alterado Por: ' + Vals.AlteradoPor + ' em ' + dataFormatada + '</span>\n\
                		<button type="button" id="btn_editar_acoes" name="Editar" class="btn pull-right btn-default editarButton">Editar</button>\n\n\
                		</div>\n\
                		');
                }
                if (Vals.ACOESINTEGRADAS === 'SIM') {
                	$('input:radio[name=ACOESINTEGRADAS][value=SIM]').click();
                	$('#ULTIMOACOESINTEGRADAS').val(Vals.ULTIMOACOESINTEGRADAS);
                	var PRINCIPAISACOESINTEGRADAS = Vals.PRINCIPAISACOESINTEGRADAS.split(',');
                	$.each(PRINCIPAISACOESINTEGRADAS, function (key, val) {
                		$('input:checkbox[name="PRINCIPAISACOESINTEGRADAS[]"][value="' + val + '"]').click()
                		$('input:checkbox[name="PRINCIPAISACOESINTEGRADAS[]"][value="' + val + '"]').prop("checked", true);
                		if (val == 'OUTRO') {
                			$('#_PRINCIPAISACOESINTEGRADAS_OUTRO').show()
                			$('#PRINCIPAISACOESINTEGRADAS_OUTRO').val(PRINCIPAISACOESINTEGRADAS[(PRINCIPAISACOESINTEGRADAS.length - 1)])
                		}
                	})
                } else {
                	$('input:radio[name=ACOESINTEGRADAS][value=NAO]').click();
                }
                //Desabilitar os campos
                $("#ACOES input").prop("disabled", true);
                $("#ACOES select").prop("disabled", true);
                $("#ACOES").find('.has-success').removeClass('has-success')
                recuperado_acoes = true;
                return true;
            } else {
            	recuperado_acoes = false;
            	return false;
            }
        },
        error: function () {
        	alertify.error('Erro ao buscar dados de acoes');
        	return false;
        }
    });
}
function recuperar_monitoramento() {
	$('a[role="menuitem"]').each(function( index, item ) { 
		$(item).addClass('disabled')
	})
	$("#loading-monitoramento").show()
	$("#MONITORAMENTO").hide()
	$.ajax({url: '{{ route('situacao.monitoramento.get') }}',
		data: {Acao: "recuperar", CodCidade: $("#CodCidade").val(), Ano: $("#Ano").val()},
		type: 'POST',
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		},
		success: function (output) {
			$('a[role="menuitem"]').each(function( index, item ) { 
				$(item).removeClass('disabled')
			})
			$("#loading-monitoramento").hide()
			$("#MONITORAMENTO").show()
            //console.log(output)
            if (output != 'vazio') {
            	var Vals = JSON.parse(output);
                //botao editar
                if ($('#btn_editar_monitoramento').length == 0) {
                	let dataFormatada = Vals.created_at;
                	$('#MONITORAMENTO').prepend('<div id="EDIT_MONITORAMENTO"><span id="EDIT_Alterado">Alterado Por: ' + Vals.AlteradoPor + ' em ' + dataFormatada + '</span>\n\
                		<button type="button" id="btn_editar_monitoramento" name="Editar" class="btn pull-right btn-default editarButton">Editar</button>\n\n\
                		</div>\n\
                		');
                }
                if (Vals.BEBERDIRIGIR === 'SIM') {
                	$('input:radio[name=BEBERDIRIGIR][value=SIM]').click();
                	$('#ULTIMOBEBERDIRIGIR').val(Vals.ULTIMOBEBERDIRIGIR)
                	$('input:radio[name=QUADROBEBERDIRIGIR][value="' + Vals.QUADROBEBERDIRIGIR + '"]').click();
                } else {
                	$('input:radio[name=BEBERDIRIGIR][value=NAO]').click();
                }
                if (Vals.VELOCIDADE === 'SIM') {
                	$('input:radio[name=VELOCIDADE][value=SIM]').click();
                	$('#ULTIMOVELOCIDADE').val(Vals.ULTIMOVELOCIDADE)
                	$('input:radio[name=QUADROVELOCIDADE][value="' + Vals.QUADROVELOCIDADE + '"]').click();
                } else {
                	$('input:radio[name=VELOCIDADE][value=NAO]').click();
                }
                if (Vals.DEFINIDOMUNICIPIO === 'SIM') {
                	$('input:radio[name=DEFINIDOMUNICIPIO][value=SIM]').click();
                	$('#ULTIMODEFINIDOMUNICIPIO').val(Vals.ULTIMODEFINIDOMUNICIPIO)
                	$('#QUADRODEFINIDOMUNICIPIO').val(Vals.QUADRODEFINIDOMUNICIPIO)

                    //$('input:radio[name=QUADRODEFINIDOMUNICIPIO][value="' + Vals.QUADRODEFINIDOMUNICIPIO + '"]').click();
                } else {
                	$('input:radio[name=DEFINIDOMUNICIPIO][value=NAO]').click();
                }
                if (Vals.QUADROGRUPOVITIMAS === 'SIM') {
                	$('input:radio[name=QUADROGRUPOVITIMAS][value=SIM]').click();
                	$('#ULTIMOQUADROGRUPOVITIMAS').val(Vals.ULTIMOQUADROGRUPOVITIMAS)
                	var QUADROGRUPOVITIMAS_QUAIS = Vals.QUADROGRUPOVITIMAS_QUAIS.split(',');
                	$.each(QUADROGRUPOVITIMAS_QUAIS, function (key, val) {
                		$('input:checkbox[name="QUADROGRUPOVITIMAS_QUAIS[]"][value="' + val + '"]').click()
                		$('input:checkbox[name="QUADROGRUPOVITIMAS_QUAIS[]"][value="' + val + '"]').prop("checked", true);
                	})

                } else {
                	$('input:radio[name=QUADROGRUPOVITIMAS][value=NAO]').click();
                }
                //Desabilitar os campos
                $("#MONITORAMENTO input").prop("disabled", true);
                $("#MONITORAMENTO select").prop("disabled", true);
                $("#MONITORAMENTO textarea").prop("disabled", true);
                $("#MONITORAMENTO").find('.has-success').removeClass('has-success')
                recuperado_monitoramento = true;
                return true;
            } else {
            	recuperado_monitoramento = false;
            	return false;
            }
        },
        error: function () {
        	alertify.error('Erro ao buscar dados de monitoramento');
        	return false;
        }
    });
}

function cidade() {
	@if(Auth::user()->tipo == 1)
	if($('#Cidade').val() == ''){
		return false;
	}
	@endif
	atualiza(0);
	$('a[role="menuitem"]').each(function( index, item ) { 
		$(item).addClass('disabled')
	})
	$("#loading-identificacao").show()
	$("#IDENTIFICACAO").hide()
	recuperado = false;
	recuperado_implantacao = false;
	recuperado_qualidade = false;
	recuperado_analise = false;
	recuperado_monitoramento = false;
	recuperado_acoes = false;
	recuperado_implantacao = false;


	$('#COORDENADORES').trigger("reset");
	$("#COORDENADORES input").prop("disabled", false);
	$("#btn_editar_coordenadores").remove();
	$('input:radio[name=coordenaTEM][value=SIM]').attr('checked', false);
	$('input:radio[name=coordenaTEM][value=NAO]').attr('checked', false);
	$("#EDIT_COORDENADORES").remove();

	$('#IMPLANTACAO').trigger("reset");
	$('#link_implantacao').remove()
	$('#IMPLANTACAO input').prop("disabled", false);
	$('#IMPLANTACAO select').prop("disabled", false);
	$("#btn_editar_implantacao").remove();
	$("#EDIT_IMPLANTACAO").remove();

	$('#QUALIDADE').trigger("reset");
	$('#link_qualidade').remove()
	$('#QUALIDADE input').prop("disabled", false);
	$('#QUALIDADE select').prop("disabled", false);
	$("#btn_editar_qualidade").remove();
	$("#EDIT_QUALIDADE").remove();

	$('#ANALISE').trigger("reset");
	$('#ANALISE input').prop("disabled", false);
	$('#ANALISE select').prop("disabled", false);
	$("#btn_editar_analise").remove();
	$("#EDIT_ANALISE").remove();

	$('#ACOES').trigger("reset");
	$('#ACOES input').prop("disabled", false);
	$('#ACOES select').prop("disabled", false);
	$("#btn_editar_acoes").remove();
	$("#EDIT_ACOES").remove();

	$('#MONITORAMENTO').trigger("reset");
	$('#MONITORAMENTO input').prop("disabled", false);
	$('#MONITORAMENTO textarea').prop("disabled", false);
	$('#MONITORAMENTO select').prop("disabled", false);
	$("#btn_editar_monitoramento").remove();
	$("#EDIT_MONITORAMENTO").remove();

	$('div[id^="_"]').hide();

	if (($('#CodCidade').val() != '' || $('#Cidade').val() != '') && $('#Estado').val() != '') {
		console.log('dsa')
		$.ajax(
		{
			url: 'mun_ibge',
			type: "POST",
			headers: {
				'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
			},
			data: {Cidade: $('#Cidade').val(),Estado:$('#Estado').val()},
			success: function (data, textStatus, jqXHR)
			{
				console.log(data);
				$("#CodCidade").val(data);
				$("input[name='CodCidade']").val(data);


				$('.steps.clearfix ul > li').removeClass('done').addClass('disabled');
				$('.steps.clearfix ul > li').eq(0).removeClass('disabled');
				$.ajax(
				{
					url: '{{route('situacao.status')}}',
					type: "POST",
					headers: {
						'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
					},
					data: {CodCidade: $("#CodCidade").val(), acao: "etapas", Ano: $("#Ano").val()},
					success: function (data, textStatus, jqXHR)
					{
        //console.log('situacao')
        
        console.log(data)
        if (data === 'Implantacao') {
        	atual = 2;
        	$('.steps.clearfix ul > li').eq(1).removeClass('disabled').addClass('done');
        	$('.steps.clearfix ul > li').eq(2).removeClass('disabled').addClass('done');
        	$('.steps.clearfix ul > li').eq(3).removeClass('disabled').addClass('done');
        	recuperar_implantacao();
                                            //recuperado_implantacao = true;
                                            recuperar_coordenadores();
                                            //recuperado = true;
                                            $("#Copiar_dados").hide()

                                        } else if (data === 'Coordenadores') {
                                        	atual = 1;
                                        	$('.steps.clearfix ul > li').eq(1).removeClass('disabled').addClass('done');
                                        	$('.steps.clearfix ul > li').eq(2).removeClass('disabled').addClass('done');
                                        	recuperar_coordenadores();
                                            //recuperado = true;
                                            $("#Copiar_dados").hide()

                                        } else if (data === 'Qualidade') {
                                        	atual = 3;
                                        	$('.steps.clearfix ul > li').eq(1).removeClass('disabled').addClass('done');
                                        	$('.steps.clearfix ul > li').eq(2).removeClass('disabled').addClass('done');
                                        	$('.steps.clearfix ul > li').eq(3).removeClass('disabled').addClass('done');
                                        	$('.steps.clearfix ul > li').eq(4).removeClass('disabled').addClass('done');
                                        	recuperar_qualidade();
                                            //recuperado_qualidade = true;
                                            recuperar_implantacao();
                                            //recuperado_implantacao = true;
                                            recuperar_coordenadores();
                                            //recuperado = true;
                                            $("#Copiar_dados").hide()

                                        } else if (data === 'Analise') {
                                        	atual = 4;
                                        	$('.steps.clearfix ul > li').eq(1).removeClass('disabled').addClass('done');
                                        	$('.steps.clearfix ul > li').eq(2).removeClass('disabled').addClass('done');
                                        	$('.steps.clearfix ul > li').eq(3).removeClass('disabled').addClass('done');
                                        	$('.steps.clearfix ul > li').eq(4).removeClass('disabled').addClass('done');
                                        	$('.steps.clearfix ul > li').eq(5).removeClass('disabled').addClass('done');
                                        	recuperar_analise();
                                            //recuperado_analise = true;
                                            recuperar_qualidade();
                                            //recuperado_qualidade = true;
                                            recuperar_implantacao();
                                            //recuperado_implantacao = true;
                                            recuperar_coordenadores();
                                            //recuperado = true;
                                            $("#Copiar_dados").hide()

                                        } else if (data === 'Acoes') {
                                        	atual = 5;
                                        	$('.steps.clearfix ul > li').eq(1).removeClass('disabled').addClass('done');
                                        	$('.steps.clearfix ul > li').eq(2).removeClass('disabled').addClass('done');
                                        	$('.steps.clearfix ul > li').eq(3).removeClass('disabled').addClass('done');
                                        	$('.steps.clearfix ul > li').eq(4).removeClass('disabled').addClass('done');
                                        	$('.steps.clearfix ul > li').eq(5).removeClass('disabled').addClass('done');
                                        	$('.steps.clearfix ul > li').eq(6).removeClass('disabled').addClass('done');
                                        	recuperar_acoes();
                                            //recuperado_acoes = true;
                                            recuperar_analise();
                                            //recuperado_analise = true;
                                            recuperar_qualidade();
                                            //recuperado_qualidade = true;
                                            //recuperar_implantacao();
                                            //recuperado_implantacao = true;
                                            recuperar_coordenadores();
                                            //recuperado = true;
                                            recuperar_implantacao();
                                            $("#Copiar_dados").hide()

                                        } else if (data === 'Monitoramento') {
                                        	atual = 6;
                                        	$('.steps.clearfix ul > li').eq(1).removeClass('disabled').addClass('done');
                                        	$('.steps.clearfix ul > li').eq(2).removeClass('disabled').addClass('done');
                                        	$('.steps.clearfix ul > li').eq(3).removeClass('disabled').addClass('done');
                                        	$('.steps.clearfix ul > li').eq(4).removeClass('disabled').addClass('done');
                                        	$('.steps.clearfix ul > li').eq(5).removeClass('disabled').addClass('done');
                                        	$('.steps.clearfix ul > li').eq(6).removeClass('disabled').addClass('done');
                                        	$('.steps.clearfix ul > li').eq(7).removeClass('disabled').addClass('done');
                                        	recuperar_monitoramento();
                                            //recuperado_monitoramento = true;
                                            recuperar_acoes();
                                            //recuperado_acoes = true;
                                            recuperar_analise();
                                            //recuperado_analise = true;
                                            recuperar_qualidade();
                                            //recuperado_qualidade = true;
                                            recuperar_implantacao();
                                            //recuperado_implantacao = true;
                                            recuperar_coordenadores();
                                            //recuperado = true;
                                            $("#Copiar_dados").hide()

                                        }else{
                                        	$("#Copiar_dados").show()
                                        }
                                    },
                                    error: function (jqXHR, textStatus, errorThrown)
                                    {
                                    	console.log("erro");
                                    	alertify.error('Erro ao buscar dados');
                                    }
                                });
$('a[role="menuitem"]').each(function( index, item ) { 
	$(item).removeClass('disabled')
})
$("#loading-identificacao").hide()
$("#IDENTIFICACAO").show()
},
error: function (jqXHR, textStatus, errorThrown)
{
	alertify.error('Erro ao buscar dados');
	console.log("erro");
}
});
} else {
	$('.steps.clearfix ul > li').removeClass('done').addClass('disabled');
	$('.steps.clearfix ul > li').eq(0).removeClass('disabled');
	$.ajax(
	{
		url: '{{route('situacao.status')}}',
		type: "POST",
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		},
		data: {CodCidade: $("#CodCidade").val(), acao: "etapas", Ano: $("#Ano").val() },
		success: function (data, textStatus, jqXHR)
		{
                        //console.log(data)
                        if (data === 'Implantacao') {
                        	atual = 2;
                        	$('.steps.clearfix ul > li').eq(1).removeClass('disabled').addClass('done');
                        	$('.steps.clearfix ul > li').eq(2).removeClass('disabled').addClass('done');
                        	$('.steps.clearfix ul > li').eq(3).removeClass('disabled').addClass('done');
                        	recuperar_implantacao();
                            //recuperado_implantacao = true;
                            recuperar_coordenadores();
                            //recuperado = true;
                            $("#Copiar_dados").hide()

                        } else if (data === 'Coordenadores') {
                        	atual = 1;
                        	$('.steps.clearfix ul > li').eq(1).removeClass('disabled').addClass('done');
                        	$('.steps.clearfix ul > li').eq(2).removeClass('disabled').addClass('done');
                        	recuperar_coordenadores();
                            //recuperado = true;
                            $("#Copiar_dados").hide()

                        } else if (data === 'Qualidade') {
                        	atual = 3;
                        	$('.steps.clearfix ul > li').eq(1).removeClass('disabled').addClass('done');
                        	$('.steps.clearfix ul > li').eq(2).removeClass('disabled').addClass('done');
                        	$('.steps.clearfix ul > li').eq(3).removeClass('disabled').addClass('done');
                        	$('.steps.clearfix ul > li').eq(4).removeClass('disabled').addClass('done');
                        	recuperar_qualidade();
                            //recuperado_qualidade = true;
                            recuperar_implantacao();
                            //recuperado_implantacao = true;
                            recuperar_coordenadores();
                            //recuperado = true;
                            $("#Copiar_dados").hide()

                        } else if (data === 'Analise') {
                        	atual = 4;
                        	$('.steps.clearfix ul > li').eq(1).removeClass('disabled').addClass('done');
                        	$('.steps.clearfix ul > li').eq(2).removeClass('disabled').addClass('done');
                        	$('.steps.clearfix ul > li').eq(3).removeClass('disabled').addClass('done');
                        	$('.steps.clearfix ul > li').eq(4).removeClass('disabled').addClass('done');
                        	$('.steps.clearfix ul > li').eq(5).removeClass('disabled').addClass('done');
                        	recuperar_analise();
                            //recuperado_analise = true;
                            recuperar_qualidade();
                            //recuperado_qualidade = true;
                            recuperar_implantacao();
                            //recuperado_implantacao = true;
                            recuperar_coordenadores();
                            //recuperado = true;
                            $("#Copiar_dados").hide()

                        } else if (data === 'Acoes') {
                        	atual = 5;
                        	$('.steps.clearfix ul > li').eq(1).removeClass('disabled').addClass('done');
                        	$('.steps.clearfix ul > li').eq(2).removeClass('disabled').addClass('done');
                        	$('.steps.clearfix ul > li').eq(3).removeClass('disabled').addClass('done');
                        	$('.steps.clearfix ul > li').eq(4).removeClass('disabled').addClass('done');
                        	$('.steps.clearfix ul > li').eq(5).removeClass('disabled').addClass('done');
                        	$('.steps.clearfix ul > li').eq(6).removeClass('disabled').addClass('done');
                        	recuperar_acoes();
                            //recuperado_acoes = true;
                            recuperar_analise();
                            //recuperado_analise = true;
                            recuperar_qualidade();
                            //recuperado_qualidade = true;
                            recuperar_implantacao();
                            //recuperado_implantacao = true;
                            recuperar_coordenadores();
                            //recuperado = true;
                            $("#Copiar_dados").hide()


                        } else if (data === 'Monitoramento') {
                        	atual = 6;
                        	$('.steps.clearfix ul > li').eq(1).removeClass('disabled').addClass('done');
                        	$('.steps.clearfix ul > li').eq(2).removeClass('disabled').addClass('done');
                        	$('.steps.clearfix ul > li').eq(3).removeClass('disabled').addClass('done');
                        	$('.steps.clearfix ul > li').eq(4).removeClass('disabled').addClass('done');
                        	$('.steps.clearfix ul > li').eq(5).removeClass('disabled').addClass('done');
                        	$('.steps.clearfix ul > li').eq(6).removeClass('disabled').addClass('done');
                        	$('.steps.clearfix ul > li').eq(7).removeClass('disabled').addClass('done');
                        	recuperar_monitoramento();
                            //recuperado_monitoramento = true;
                            recuperar_acoes();
                            //recuperado_acoes = true;
                            recuperar_analise();
                            //recuperado_analise = true;
                            recuperar_qualidade();
                            //recuperado_qualidade = true;
                            recuperar_implantacao();
                            //recuperado_implantacao = true;
                            recuperar_coordenadores();
                            //recuperado = true;
                            $("#Copiar_dados").hide()

                        }else{
                        	$("#Copiar_dados").show()
                        }
                    },
                    error: function (jqXHR, textStatus, errorThrown)
                    {
                    	console.log("erro");
                    	alertify.error('Erro ao buscar dados');
                    }
                });

}

}

$(document).on('change', '.loadValues', function () {
	$('#Ano').val('')

})

$(document).on('change', '#Ano', function () {
	cidade();
})
$(document).on('click', '#Copiar_dados', function () {
	$.ajax(
	{
		url: '{{route('situacao.copia')}}',
		type: "POST",
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		},
		data: {CodCidade: $("#CodCidade").val(), acao: "CopiaDados", Ano: $("#Ano").val() },
		success: function (data, textStatus, jqXHR)
		{
			if(data == 'OK'){
				cidade()
			}else{
				alert(data)
			}
		},
		error: function (jqXHR, textStatus, errorThrown)
		{
			console.log("erro");
			alertify.error('Erro ao buscar dados');
		}
	});
})
$(document).ready(function () {

	var pvt = $("#pvt");
	var fv = pvt.data('formValidation')
	var contador = 0;
	var caminho = '../uploads/';
	var Implantacao = 2;
	var MAX_OPTIONS_Implantacao = 30;
	var Qualidade = 2;
	var MAX_OPTIONS_Qualidade = 15;
	var periodos = ['SEMANAL', 'QUINZENAL', 'MENSAL', 'BIMESTRAL', 'QUADRIMESTRAL'];
	var registro = ['ATA', 'RELATÓRIO'];
	var dt = new Date();


	var SPMaskBehavior = function (val) {
		return val.replace(/\D/g, '').length === 11 ? '(00) 00000-0000' : '(00) 0000-00009';
	},
	spOptions = {
		onKeyPress: function (val, e, field, options) {
			field.mask(SPMaskBehavior.apply({}, arguments), options);
		}
	};

	$('.bfh-phone').mask(SPMaskBehavior, spOptions);
	pvt.steps({
		headerTag: "h2",
		bodyTag: "section",
		transitionEffect: "none",
		stepsOrientation: "vertical",
		saveState: false,
		enableAllSteps: false,
		onStepChanging: function (e, currentIndex, newIndex) {

            //console.log(currentIndex,newIndex)                            
            var fv = pvt.data('formValidation'), // FormValidation instance
                    // The current step container
                    $container = $('#pvt').find('section[data-step="' + currentIndex + '"]');
                    if (newIndex === 0) {


                    }



                    if (newIndex < currentIndex) {
                    	return true;
                    } else {
                    	fv.validateContainer($container);

                    	var isValidStep = fv.isValidContainer($container);
                //debug
                console.log(fv.validateContainer($container))
                console.log(fv.getInvalidFields())


                if (isValidStep === false || fv.getInvalidFields().length > 0) {
                    // Do not jump to the next step
                    return false;
                } else {
                	return true;
                }
            }
        },
        onStepChanged: function (event, currentIndex, priorIndex) {
        	atualiza(priorIndex);
        	console.log(priorIndex, recuperado)
        	if (priorIndex < currentIndex) {
        		if (priorIndex === 1) {

        			if (recuperado === false) {
        				var values = {};
        				$.each($('#COORDENADORES input'), function (i, field) {
        					values[field.name] = field.value.toUpperCase();
        				});
        				values['coordenaTEM'] = $("input[name='coordenaTEM']:checked").val().toUpperCase();

        				$.ajax({url: '{{route('situacao.coordenadores.grava')}}',
        					data: {acao: "gravar", valores: values, CodCidade: $("#CodCidade").val(), Ano: $("#Ano").val()},
        					type: 'POST',
        					headers: {
        						'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        					},
        					async: false,
        					success: function (output) {
        						$('#COORDENADORES').trigger("reset");
        						if(output == 'gravado'){
        							recuperar_coordenadores();
        							recuperado = true;
        						}

        					},
        					error: function (jqXHR, textStatus, errorThrown)
        					{
        						console.log('erro',jqXHR, textStatus, errorThrown);
        						alertify.error('Erro ao gravar dados');
        					}
        				});
        			}
        		}
        		if (priorIndex === 0) {
                    //Cidade

                    var values = {};
                    $.each($('#pvt-p-0 input'), function (i, field) {
                    	values[field.name] = field.value.toUpperCase();
                    });
                    $.ajax({url: '{{route('situacao.status')}}',
                    	data: {acao: "gravar", valores: values, Ano: $("#Ano").val()},
                    	type: 'POST',
                    	headers: {
                    		'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    	},
                    	success: function (output) {
               //             console.log(output)

           }
       });
                }
                if (priorIndex === 2) {
                	if (recuperado_implantacao === false) {                		
                		$("#IMPLANTACAO input").each(function () {

                			if ($(this).attr('type') != 'file') {
                				this.value = this.value.toUpperCase();
                			}
                		});
                		var formData = new FormData($("#IMPLANTACAO")[0]);
                		formData.append('CodCidade', $("#CodCidade").val());
                		formData.append('Acao', 'gravar');
                		formData.append('Ano', $("#Ano").val());

                		$.ajax({url: '{{route('situacao.implantacao.grava')}}',
                			data: formData,
                			type: 'POST',
                			headers: {
                				'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                			},
                			processData: false,
                			contentType: false,
                			async: false,
                			cache: false
                		}).done(function (data) {
                        //console.log(data);
                        //recuperado_implantacao == false;
                        $('#IMPLANTACAO').trigger("reset");
                        recuperar_implantacao();

                    }).fail(function (data) {
                    	console.log(data)
                    	alertify.error('Erro ao gravar dados: '+data.responseText);
                    	$("#pvt-t-"+priorIndex).click();
                    })

                }
            }
            if (priorIndex === 3) {
                    //console.log(recuperado_implantacao)
                    if (recuperado_qualidade === false) {
                    	$("#QUALIDADE input").each(function () {
                    		if ($(this).attr('type') != 'file') {
                    			this.value = this.value.toUpperCase();
                    		}
                    	});

                    	var formData = new FormData($("#QUALIDADE")[0]);
                    	formData.append('CodCidade', $("#CodCidade").val());
                    	formData.append('Acao', 'gravar');
                    	formData.append('Ano', $("#Ano").val());
                   // console.log('grava qualidade')
                   $.ajax({url: '{{route('situacao.qualidade.grava')}}',
                   	data: formData,
                   	type: 'POST',
                   	headers: {
                   		'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                   	},
                   	processData: false,
                   	async: false,
                   	contentType: false,
                   	cache: false
                   }).done(function (data) {
               // console.log(data)
               $('#QUALIDADE').trigger("reset");
               recuperar_qualidade();
                                        //recuperado_qualidade = true;

            //
            //                            recuperado_qualidade == false;

        }).fail(function (data) {
        	console.log("error");
        	alertify.error('Erro ao gravar dados: '+data.responseText);
        	$("#pvt-t-"+priorIndex).click();
        })

    }
}
if (priorIndex === 4) {
	if (recuperado_analise === false) {
		$("#ANALISE input").each(function () {
			if ($(this).attr('type') != 'file') {
				this.value = this.value.toUpperCase();
			}
		});
		var formData = new FormData($("#ANALISE")[0]);
		formData.append('CodCidade', $("#CodCidade").val());
		formData.append('Acao', 'gravar');
		formData.append('Ano', $("#Ano").val());

		$.ajax({url: '{{ route('situacao.analise.grava') }}',
			data: formData,
			type: 'POST',
			headers: {
				'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
			},
			processData: false,
			contentType: false,
			cache: false
		}).done(function (data) {
			$('#ANALISE').trigger("reset");
			recuperar_analise();
		}).fail(function () {
			console.log("error",event, currentIndex, priorIndex);
			alertify.error('Erro ao gravar dados');
			$("#pvt-t-"+priorIndex).click();

		})

	}
}
if (priorIndex === 5) {

	if (recuperado_acoes === false) {
		$("#ACOES input").each(function () {
			if ($(this).attr('type') != 'file') {
				this.value = this.value.toUpperCase();
			}
		});
		var formData = new FormData($("#ACOES")[0]);
		formData.append('CodCidade', $("#CodCidade").val());
		formData.append('Acao', 'gravar');
		formData.append('Ano', $("#Ano").val());

		$.ajax({url: '{{ route('situacao.acoes.grava') }}',
			data: formData,
			type: 'POST',
			headers: {
				'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
			},
			processData: false,
			contentType: false,
			cache: false
		}).done(function (data) {
                       //     console.log(data)
                       $('#ACOES').trigger("reset");
                       recuperar_acoes();
                   }).fail(function () {
                   	console.log("error");
                   	alertify.error('Erro ao gravar dados');
                   	$("#pvt-t-"+priorIndex).click();
                   })

               }
           }
           if (priorIndex === 6) {
           	if (recuperado_monitoramento === false) {
           		$("#MONITORAMENTO input").each(function () {
           			if ($(this).attr('type') != 'file') {
           				this.value = this.value.toUpperCase();
           			}
           		});
           		var formData = new FormData($("#MONITORAMENTO")[0]);
           		formData.append('CodCidade', $("#CodCidade").val());
           		formData.append('Acao', 'gravar');
           		formData.append('Ano', $("#Ano").val());

           		$.ajax({url: '{{ route('situacao.monitoramento.grava') }}',
           			data: formData,
           			type: 'POST',
           			headers: {
           				'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
           			},
           			processData: false,
           			contentType: false,
           			cache: false
           		}).done(function (data) {
          //  console.log(data)
          $('#MONITORAMENTO').trigger("reset");
          recuperar_monitoramento();

      }).fail(function () {
      	console.log("error");
      	alertify.error('Erro ao gravar dados');
      	$("#pvt-t-"+priorIndex).click();
      })

  }
}
}
},

onFinished: function (event, currentIndex)
{
	window.location.replace($(location).attr('href').replace('/{{route('situacao.status')}}', ''));
},
labels: {

	finish: "Finalizar",
	next: "Próximo",
	previous: "Anterior",
	loading: "Carregando ..."
}

})
.formValidation({
	locale: 'pt_BR',
	framework: 'bootstrap',

	icon: {

	}, excluded: ':disabled',
	message: 'Campo Obrigatório	',
	fields: {
		EMAIL1: {
			enabled: false,
			validators: {
				notEmpty: {
					message: 'Campo Obrigatório'
				},
				emailAddress: {
					message: 'Favor verificar o formato do email'
				}
			}
		},
		COORDENADOR1: {
			enabled: false,
			validators: {
				notEmpty: {
					message: 'Campo Obrigatório'
				}
			}
		},
		Estado: {
			validators: {
				notEmpty: {
					message: 'Por Favor preencha o Estado'
				}
			}
		},
		Cidade: {
			validators: {
				notEmpty: {
					message: 'Por Favor preencha o Municipio'
				}
			}
		},
		INSTITUICAO1: {
			enabled: false,
			validators: {
				notEmpty: {
					message: 'Campo Obrigatório'
				}
			}
		},
		TEL1: {
			enabled: false,
			validators: {
				notEmpty: {
					message: 'Campo Obrigatório'
				}
			}
		},
		COMISSAO: {
			icon: false,
			validators: {
				notEmpty: {
					message: 'Campo Obrigatório	'
				}
			}
		},
		DTDECRETO: {
			validators: {
				date: {
					max: dt.toLocaleDateString(),
					min: '01/01/2010',
					format: 'DD/MM/YYYY',
					message: 'Formato da data invalido',
				}
			}
		},
		DTREUNIAOCI: {
			validators: {
				notEmpty: {
					message: 'Informe a Data da última reunião',
				}, date: {
					max: dt.toLocaleDateString(),
					min: '01/01/2010',
					format: 'DD/MM/YYYY',
					message: 'Formato da data invalido',
				}
			}
		},
		DECRETO: {
			enabled: false,
			validators: {
				notEmpty: {
					message: 'Informe o número do decreto',
				}
			}
		},
		PERIODIC: {
			icon: false,
			enabled: false,
			validators: {
				notEmpty: {
					message: 'Campo Obrigatório '
				}
			}
		},
		outradata: {
			enabled: false,
			validators: {
				notEmpty: {
					message: 'Informe a Periodicidade das reuniões ',
				}
			}
		},
		DTREUNIAOCPVT: {
			icon: false,
			validators: {
				notEmpty: {
					message: 'Informe a data da reunião	'
				}
			}
		},
		CPVToutradata: {
			enabled: false,
			validators: {
				notEmpty: {
					message: 'Informe a Periodicidade de reuniões da Coordenação do PVT',
				}
			}
		},
		REGREUNIAOCI: {
			enabled: false,
			icon: false,
			validators: {
				notEmpty: {
					message: 'Campo Obrigatório	'
				}
			}
		},
		DATAREUNIAOCPVT: {
			enabled: false,
			icon: false,
			validators: {
				notEmpty: {
					message: 'Campo Obrigatório	'
				}
			}
		},
		DATAREUNIAOCPVToutra: {
			enabled: false,
			validators: {
				notEmpty: {
					message: 'Campo Obrigatório	'
				}
			}
		},
		REGREUNIAOCIoutra: {
			enabled: false,
			validators: {
				notEmpty: {
					message: 'Campo Obrigatório	'
				}
			}
		},
		REGREUNIAOCPVT: {
			enabled: false,
			icon: false,
			validators: {
				notEmpty: {
					message: 'Campo Obrigatório	'
				}
			}
		},
		REGREUNIAOCPVToutra: {
			enabled: false,
			validators: {
				notEmpty: {
					message: 'Informe a Forma de registro das reuniões',
				}
			}
		},
		COMISSAOGD: {
			validators: {
				notEmpty: {
					message: 'Campo Obrigatório	'
				}
			}
		},
		COMISSAOFORM: {
			validators: {
				notEmpty: {
					message: 'Campo Obrigatório	'
				},
			}
		},
		COMISSAODOC: {
			enabled: false,
			validators: {
				notEmpty: {
					message: 'Campo Obrigatório	'
				},
			}
		},
		NCOMISSAO: {
			enabled: false,
			validators: {
				notEmpty: {
					message: 'Campo Obrigatório	'
				},
			}
		},
		DTCOMISSAO: {
			enabled: false,
			validators: {
				date: {
					min: '01/01/2010',
					format: 'DD/MM/YYYY',
					max: dt.toLocaleDateString(),
					message: 'Data Inválida'
				},
				notEmpty: {
					message: 'Campo Obrigatório	'
				},
			}
		},
		UPDECRETOCOMISSAO: {
			enabled: false,
			validators: {
				file: {
					extension: 'jpeg,jpg,png,pdf,doc,docx',
					message: 'Arquivo Inválido, por favor insira uma imagem, pdf ou word'
				}
			}
		},
		BASESAT: {
			validators: {
				notEmpty: {
					message: 'Campo Obrigatório	'
				},
			}
		},
		BASESOBITO: {
			validators: {
				notEmpty: {
					message: 'Campo Obrigatório	'
				},
			}
		},
		BASEFERIDO: {
			validators: {
				notEmpty: {
					message: 'Campo Obrigatório	'
				},
			}
		},
		BASEFATORRISCO: {
			validators: {
				notEmpty: {
					message: 'Campo Obrigatório	'
				},
			}
		},
		MAPEAMENTO: {
			validators: {
				notEmpty: {
					message: 'Campo Obrigatório	'
				},
			}
		},
		LIMPEZA: {
			validators: {
				notEmpty: {
					message: 'Campo Obrigatório	'
				},
			}
		},
		LISTAUNICA: {
			validators: {
				notEmpty: {
					message: 'Campo Obrigatório	'
				},
			}
		},
		FATORRISCO: {
			validators: {
				notEmpty: {
					message: 'Campo Obrigatório	'
				},
			}
		},
		INDICADOROBITO: {
			validators: {
				notEmpty: {
					message: 'Campo Obrigatório	'
				},
			}
		},
		INDICADORFERIDO: {
			validators: {
				notEmpty: {
					message: 'Campo Obrigatório	'
				},
			}
		},
		QUADROMULTIPLO: {
			validators: {
				notEmpty: {
					message: 'Campo Obrigatório	'
				},
			}
		},
		LOCAISCHAVE: {
			validators: {
				notEmpty: {
					message: 'Campo Obrigatório	'
				},
			}
		},
		RELATORIOS: {
			validators: {
				notEmpty: {
					message: 'Campo Obrigatório	'
				},
			}
		},
		LINKAGE: {
			enabled: false,
			validators: {
				notEmpty: {
					message: 'Campo Obrigatório	'
				},
			}
		},
		NAOLINKOBITO: {
			enabled: false,
			validators: {
				notEmpty: {
					message: 'Campo Obrigatório	'
				},
			}
		},
		ULTBASEOBITO: {
			enabled: false,
			validators: {
				notEmpty: {
					message: 'Campo Obrigatório	'
				},
			}
		},
		NAOLINKFER: {
			enabled: false,
			validators: {
				notEmpty: {
					message: 'Campo Obrigatório	'
				},
			}
		},
		ULTBASEVITIMAS: {
			enabled: false,
			validators: {
				notEmpty: {
					message: 'Campo Obrigatório	'
				},
			}
		},
		PRILINKAGE: {
			enabled: false,
			validators: {
				notEmpty: {
					message: 'Campo Obrigatório	'
				},
			}
		},
		PRIMEIROANOLINKAGE: {
			enabled: false,
			validators: {
				notEmpty: {
					message: 'Campo Obrigatório	'
				},
			}
		},
		ULTLINKAGEANOLINKAGE: {
			enabled: false,
			validators: {
				notEmpty: {
					message: 'Campo Obrigatório	'
				},
			}
		},
		ULTLINKAGE: {
			enabled: false,
			validators: {
				notEmpty: {
					message: 'Campo Obrigatório	'
				},
			}
		},
		COMOFOILISTAVITIMAS: {
			enabled: false,
			validators: {
				notEmpty: {
					message: 'Campo Obrigatório	'
				},
			}
		},
		PLANOACAO: {
			validators: {
				notEmpty: {
					message: 'Campo Obrigatório	'
				},
			}
		},
		QUADRODEFINIDOMUNICIPIO: {
			enabled: false,
			validators: {
				notEmpty: {
					message: 'Campo Obrigatório	'
				},
			}
		},
		QUADROVELOCIDADE: {
			enabled: false,
			validators: {
				notEmpty: {
					message: 'Campo Obrigatório	'
				},
			}
		},
		ULTIMOVELOCIDADE: {
			enabled: false,
			validators: {
				notEmpty: {
					message: 'Campo Obrigatório	'
				},
			}
		},
		VELOCIDADE: {
			validators: {
				notEmpty: {
					message: 'Campo Obrigatório	'
				},
			}
		},
		BEBERDIRIGIR: {
			validators: {
				notEmpty: {
					message: 'Campo Obrigatório	'
				},
			}
		},
		QUADROBEBERDIRIGIR: {
			enabled: false,
			validators: {
				notEmpty: {
					message: 'Campo Obrigatório	'
				},
			}
		},
		PRINCIPAISACOESINTEGRADAS_OUTRO: {
			enabled: false,
			validators: {
				notEmpty: {
					message: 'Campo Obrigatório	'
				},
			}
		},
		ULTIMOBEBERDIRIGIR: {
			enabled: false,
			validators: {
				notEmpty: {
					message: 'Campo Obrigatório	'
				},
			}
		},
		DEFINIDOMUNICIPIO: {
			validators: {
				notEmpty: {
					message: 'Campo Obrigatório	'
				},
			}
		},
		ULTIMODEFINIDOMUNICIPIO: {
			enabled: false,
			validators: {
				notEmpty: {
					message: 'Campo Obrigatório	'
				},
			}
		},
		NOMECOMISSAO: {
			validators: {
				notEmpty: {
					message: 'Insira o Nome da Comissão	'
				},
			}
		},
		PERCPROG: {
			validators: {
				notEmpty: {
					message: 'Campo Obrigatório	'
				},
			}
		},
		IDENTIFICACAORISCO: {
			validators: {
				notEmpty: {
					message: 'Campo Obrigatório	'
				},
			}
		},
		ULTIMOSEMESTRERISCO: {
			enabled: false,
			validators: {
				notEmpty: {
					message: 'Campo Obrigatório	'
				},
			}
		},
		ULTIMORISCO: {
			enabled: false,
			validators: {
				notEmpty: {
					message: 'Campo Obrigatório	'
				},
			}
		},
		FATORESRISCOACIDENTES: {
			enabled: false,
			validators: {
				notEmpty: {
					message: 'Campo Obrigatório	'
				},
			}
		},
		CONDUTARISCOACIDENTES: {
			enabled: false,
			validators: {
				notEmpty: {
					message: 'Campo Obrigatório	'
				},
			}
		},
		FATORESGRAVIDADE: {
			enabled: false,
			validators: {
				notEmpty: {
					message: 'Campo Obrigatório	'
				},
			}
		},
		FATORESFATAL: {
			enabled: false,
			validators: {
				notEmpty: {
					message: 'Campo Obrigatório	'
				},
			}
		},
		CONDUTARISCOACIDENTESCADA: {
			enabled: false,
			validators: {
				notEmpty: {
					message: 'Campo Obrigatório	'
				},
			}
		},
		ULTIMOSEMESTRERISCOCADA: {
			enabled: false,
			validators: {
				notEmpty: {
					message: 'Campo Obrigatório	'
				},
			}
		},
		FATORESGRAVIDADECADA: {
			enabled: false,
			validators: {
				notEmpty: {
					message: 'Campo Obrigatório	'
				},
			}
		},
		FATORESFATALCADA: {
			enabled: false,
			validators: {
				notEmpty: {
					message: 'Campo Obrigatório	'
				},
			}
		},
		CONSTRUCAOQUADROMULTIPLO: {
			validators: {
				notEmpty: {
					message: 'Campo Obrigatório	'
				},
			}
		},
		ULTIMORISCOCADA: {
			enabled: false,
			validators: {
				notEmpty: {
					message: 'Campo Obrigatório	'
				},
			}
		},
		FATORESRISCOACIDENTESCADA: {
			enabled: false,
			validators: {
				notEmpty: {
					message: 'Campo Obrigatório	'
				},
			}
		},
		ULTIMOSEMESTRECONSTRUCAOQUADROMULTIPLO: {
			enabled: false,
			validators: {
				notEmpty: {
					message: 'Campo Obrigatório	'
				},
			}
		},
		ULTIMOCONSTRUCAOQUADROMULTIPLO: {
			enabled: false,
			validators: {
				notEmpty: {
					message: 'Campo Obrigatório	'
				},
			}
		},
		FATORESCHAVE: {
			validators: {
				notEmpty: {
					message: 'Campo Obrigatório	'
				},
			}
		},
		ULTIMOSEMESTREFATORESCHAVE: {
			enabled: false,
			validators: {
				notEmpty: {
					message: 'Campo Obrigatório	'
				},
			}
		},
		ULTIMOGRUPOSVITIMAS: {
			enabled: false,
			validators: {
				notEmpty: {
					message: 'Campo Obrigatório	'
				},
			}
		},
		ULTIMOFATORESCHAVE: {
			enabled: false,
			validators: {
				notEmpty: {
					message: 'Campo Obrigatório	'
				},
			}
		},
		'PRINCIPAISFATORESCHAVE[]': {
			enabled: false,
			validators: {
				choice: {
					min: 1,
					message: 'Por favor selecione pelo menos uma opção'
				},
			}
		},
		GRUPOSVITIMAS: {
			validators: {
				notEmpty: {
					message: 'Campo Obrigatório	'
				},
			}
		},
		ACOESINTEGRADAS: {
			validators: {
				notEmpty: {
					message: 'Campo Obrigatório	'
				},
			}
		},
		ULTIMOSEMESTREGRUPOSVITIMAS: {
			enabled: false,
			validators: {
				notEmpty: {
					message: 'Campo Obrigatório	'
				},
			}
		},
		ULTIMOACOESINTEGRADAS: {
			enabled: false,
			validators: {
				notEmpty: {
					message: 'Campo Obrigatório	'
				},
			}
		},
		'PRINCIPAISGRUPOSVITIMAS[]': {
			enabled: false,
			validators: {
				choice: {
					min: 1,
					message: 'Por favor selecione pelo menos uma opção'
				},
			}
		},
		'PRINCIPAISACOESINTEGRADAS[]': {
			enabled: false,
			validators: {
				choice: {
					min: 1,
					message: 'Por favor selecione pelo menos uma opção'
				},
			}
		},
		PRINCIPAISGRUPOSVITIMAS_OUTRO: {
			enabled: false,
			validators: {
				notEmpty: {
					message: 'Informe o outro grupo	'
				},
			}
		},
		PROGRAMAPRIORITARIOS: {
			validators: {
				notEmpty: {
					message: 'Campo Obrigatório	'
				},
			}
		},
		ULTIMOPROGRAMAPRIORITARIOS: {
			enabled: false,
			validators: {
				notEmpty: {
					message: 'Campo Obrigatório	'
				},
			}
		},
		IDENTIFICACAORISCOCADA: {
			validators: {
				notEmpty: {
					message: 'Campo Obrigatório	'
				},
			}
		},
		UPDECRETO: {
			validators: {
				file: {
					extension: 'jpeg,jpg,png,pdf,doc,docx',
					message: 'Arquivo Inválido, por favor insira uma imagem, pdf ou word'
				}
			}
		},
		QUADROPLANOACAO: {
			validators: {
				notEmpty: {
					message: 'Campo Obrigatório	'
				},
				file: {
					extension: 'xlsx,xls,zip,rar',
					message: 'Arquivo Inválido'
				}
			}
		},
		'base_feridos[]': {
			enabled: false,
			validators: {
				choice: {
					min: 1,
					message: 'Por favor selecione pelo menos uma opção'
				}
			}
		},
		'bases_utilizadas[]': {
			enabled: false,
			validators: {
				choice: {
					min: 2,
					message: 'Por favor selecione pelo menos duas opções'
				}
			}
		},
		base_feridos_hospital: {
			enabled: false,
			validators: {
				notEmpty: {
					message: 'Campo Obrigatório	'
				},
			}
		},
		base_dados_outras: {
			enabled: false,
			validators: {
				notEmpty: {
					message: 'Campo Obrigatório	'
				},
			}
		},
		bases_utilizadas_hospital: {
			enabled: false,
			validators: {
				notEmpty: {
					message: 'Campo Obrigatório	'
				},
			}
		},
		bases_utilizadas_outras: {
			enabled: false,
			validators: {
				notEmpty: {
					message: 'Campo Obrigatório	'
				},
			}
		},
		'base_dados[]': {
			enabled: false,
			validators: {
				choice: {
					min: 2,
					message: 'Por favor selecione pelo menos duas opções'
				}
			}
		},
		'base_obitos[]': {
			enabled: false,
			validators: {
				choice: {
					min: 1,
					message: 'Por favor selecione pelo menos uma opção'
				}
			}
		},
		'instituicao[]': {
			enabled: false,
			icon: false,
			row: 'td',
			validators: {
				notEmpty: {
					message: 'Informe o nome da instituicao '
				}
			}
		},
		'setor[]': {
			enabled: false,
			icon: false,
			row: 'td',
			validators: {
				notEmpty: {
					message: 'Informe o setor da instituicao'
				}
			}
		},
		'Qualidade_instituicao[]': {
			enabled: false,
			icon: false,
			row: 'td',
			validators: {
				notEmpty: {
					message: 'Informe o setor da instituicao'
				}
			}
		},
		'Qualidade_origem[]': {
			enabled: false,
			icon: false,
			row: 'td',
			validators: {
				notEmpty: {
					message: 'Informe o setor da instituicao'
				}
			}
		},
		'Qualidade_setor[]': {
			enabled: false,
			icon: false,
			row: 'td',
			validators: {
				notEmpty: {
					message: 'Informe o setor da instituicao'
				}
			}
		},
		'origem[]': {
			enabled: false,
			icon: false,
			row: 'td',
			validators: {
				notEmpty: {
					message: 'Informe a origem da instituicao	'
				}
			}
		}
//                                'instituicao[0].instituicao': instituicaoValidators,
//                                'setor[0].setor': setorValidators,
//                                'origem[0].origem': origemValidators

}
})

            //inputs
            .on('change', '[name="DATAREUNIAOCPVT"]', function (e) {
            	if ($(this).val() === 'OUTRA') {
            		$('#_DATAREUNIAOCPVToutra').show();
            		//$("#pvt").formValidation('enableFieldValidators', 'DATAREUNIAOCPVToutra', true)
            	} else {
            		$("#pvt").formValidation('enableFieldValidators', 'DATAREUNIAOCPVToutra', false)
            		$('#_DATAREUNIAOCPVToutra').hide();
            	}
            })
            .on('click', '[name="PERIODIC"]', function (e) {
            	if ($(this).val() === 'OUTRA') {
            		$('#_outradata').show();
            		//$("#pvt").formValidation('enableFieldValidators', 'outradata', true)
            	} else if ($.inArray($(this).val(), periodos) !== -1) {
            		$("#pvt").formValidation('enableFieldValidators', 'outradata', false)
            		$('#_outradata').hide();
            	} else {
            		$('#_outradata').show();
            		//$("#pvt").formValidation('enableFieldValidators', 'outradata', true)
            	}
            })
            .on('change', '[name="REGREUNIAOCI"]', function (e) {
            	if ($(this).val() === 'OUTRA') {
            		$('#_REGREUNIAOCIoutra').show();
            		//$("#pvt").formValidation('enableFieldValidators', 'REGREUNIAOCIoutra', true)
            	} else {
            		$("#pvt").formValidation('enableFieldValidators', 'REGREUNIAOCIoutra', false)
            		$('#_REGREUNIAOCIoutra').hide();
            	}
            })
            .on('change', '[name="REGREUNIAOCPVT"]', function (e) {
            	if ($(this).val() === 'OUTRA') {
            		$('#_REGREUNIAOCPVToutra').show();
            		//$("#pvt").formValidation('enableFieldValidators', 'REGREUNIAOCPVToutra', true)
            	} else {
            		$("#pvt").formValidation('enableFieldValidators', 'REGREUNIAOCPVToutra', false)
            		$('#_REGREUNIAOCPVToutra').hide();
            	}
            })

            .on('click', '#btn_editar_coordenadores', function () {
                //console.log('dsa')
                $("#COORDENADORES input").prop("disabled", false);
                $("#EDIT_Alterado").remove();
                $("#EDIT_COORDENADORES").remove();
                $("#btn_editar_coordenadores").remove();
                recuperado = false;
            })
            .on('click', '#btn_editar_implantacao', function () {
            	$('#IMPLANTACAO').find('.addButton').show()
            	$('#IMPLANTACAO').find('.removeButton').show()
            	$("#IMPLANTACAO input").prop("disabled", false);
            	$("#IMPLANTACAO select").prop("disabled", false);
            	$('#InputUPDECRETO').show();
            	$("#EDIT_IMPLANTACAO").remove();
            	$("#editado_implantacao").val("true");
            	recuperado_implantacao = false;


            })
            .on('click', '#btn_editar_qualidade', function () {
            	$('#QUALIDADE').find('.addButton').show()
            	$('#QUALIDADE').find('.removeButton').show()
            	$('#InputUPDECRETOCOMISSAO').show();
            	$("#QUALIDADE input").prop("disabled", false);
            	$("#QUALIDADE select").prop("disabled", false);
            	$("#EDIT_QUALIDADE").remove();
            	$("#editado_qualidade").val("true");

            	recuperado_qualidade = false;
            })
            .on('click', '#btn_editar_analise', function () {
            	$("#ANALISE input").prop("disabled", false);
            	$("#ANALISE select").prop("disabled", false);
            	$("#EDIT_ANALISE").remove();
            	$("#editado_analise").val("true");

            	recuperado_analise = false;
            })
            .on('click', '#btn_editar_acoes', function () {
            	$("#ACOES input").prop("disabled", false);
            	$("#ACOES select").prop("disabled", false);
            	$("#EDIT_ACOES").remove();
            	$("#editado_acoes").val("true");

            	recuperado_acoes = false;
            })
            .on('click', '#btn_editar_monitoramento', function () {
                //console.log('monitoramento editar')
                $("#MONITORAMENTO input").prop("disabled", false);
                $("#MONITORAMENTO select").prop("disabled", false);
                $("#MONITORAMENTO textarea").prop("disabled", false);
                $("#EDIT_MONITORAMENTO").remove();
                $("#editado_monitoramento").val("true");

                recuperado_monitoramento = false;
            })
            // Add button click handler --> instituicoes
            .on('click', '.addButton', function () {
            	if ($(this).closest('tbody').attr('id') == 'ImplantacaoInstituicoes') {
            		Implantacao++;
            		$('#ImplantacaoInstituicoes').append('<tr id="ImplantacaoInstituicoes' + Implantacao + '">\n\n\
            			<td>\n\
            			<input id="instituicao' + Implantacao + '" type="text" class="form-control" name="instituicao[]">\n\
            			</td>\n\
            			<td>\n\
            			<select id="setor' + Implantacao + '" name="setor[]" class="form-control" style="padding-right:5px;padding-left:5px;">\n\
            			<option value="SAUDE">Saúde</option>\n\
            			<option value="TRANSITO">Trânsito</option>\n\
            			<option value="SEGURANCA PUBLICA">Segurança pública</option>\n\
            			<option value="INSTITUICOES ACADEMICAS">Instituições acadêmicas</option>\n\
            			<option value="EDUCACAO">Educação</option>\n\
            			<option value="SOCIEDADE CIVIL">Sociedade civil</option>\n\
            			<option value="ORGANIZACOES NAO GOVERNAMENTAIS">Organizações não governamentais</option>\n\
            			<option value="EMPRESAS PRIVADAS">Empresas privadas</option>\n\
            			<option value="INFRAESTRUTURA URBANA">Infraestrutura Urbana</option>\n\
            			<option value="PLANEJAMENTO">Planejamento</option>\n\
            			<option value="JUSTICA">JUSTIÇA</option>\n\
            			<option value="TRANSPORTE">TRANSPORTE</option>\n\
            			<option value="OUTRAS">Outras</option>\n\
            			</select>\n\
            			</td>\n\
            			<td>\n\
            			<select id="origem' + Implantacao + '" name="origem[]" class="form-control" style="padding-right:0px;">\n\
            			<option value=""></option>\n\
            			<option value="GOVERNAMENTAL">Governamental</option>\n\
            			<option value="NAO GOVERNAMENTAL">Não Governamental</option>\n\
            			</select>\n\
            			</td>\n\
            			<td>\n\
            			<button type="button" class="btn btn-default removeButton"><i class="glyphicon glyphicon-minus"></i></button> \n\
            			</td>\n\
            			</tr>');
            		$('#pvt').formValidation('addField', 'instituicao[]');
            		$('#pvt').formValidation('addField', 'setor[]');
            		$('#pvt').formValidation('addField', 'origem[]');


            		if (Implantacao >= MAX_OPTIONS_Implantacao) {
            			$('#IMPLATANCAO').find('.addButton').attr('disabled', 'disabled');
            		}
            	} else {
            		Qualidade++;
            		$('#QualidadeInstituicoes').append('<tr id="QualidadeInstituicoes' + Qualidade + '">\n\n\
            			<td>\n\
            			<input id="Qualidade_instituicao' + Qualidade + '" type="text" class="form-control" name="Qualidade_instituicao[]">\n\
            			</td>\n\
            			<td>\n\
            			<select id="Qualidade_setor' + Qualidade + '" name="Qualidade_setor[]" class="form-control" style="padding-right:5px;padding-left:5px;">\n\
            			<option value=""></option>\n\
            			<option value="SAUDE">Saúde</option>\n\
            			<option value="TRANSITO">Trânsito</option>\n\
            			<option value="SEGURANCA PUBLICA">Segurança pública</option>\n\
            			<option value="INSTITUICOES ACADEMICAS">Instituições acadêmicas</option>\n\
            			<option value="EDUCACAO">Educação</option>\n\
            			<option value="SOCIEDADE CIVIL">Sociedade civil</option>\n\
            			<option value="ORGANIZACOES NAO GOVERNAMENTAIS">Organizações não governamentais</option>\n\
            			<option value="EMPRESAS PRIVADAS">Empresas privadas</option>\n\
            			<option value="INFRAESTRUTURA URBANA">Infraestrutura Urbana</option>\n\
            			<option value="PLANEJAMENTO">Planejamento</option>\n\
            			<option value="JUSTICA">JUSTIÇA</option>\n\
            			<option value="TRANSPORTE">TRANSPORTE</option>\n\
            			<option value="OUTRAS">Outras</option>\n\
            			</select>\n\
            			</td>\n\
            			<td>\n\
            			<select id="Qualidade_origem' + Qualidade + '" name="Qualidade_origem[]" class="form-control" style="padding-right:0px;">\n\
            			<option value=""></option>\n\
            			<option value="GOVERNAMENTAL">Governamental</option>\n\
            			<option value="NAO GOVERNAMENTAL">Não Governamental</option>\n\
            			</select>\n\
            			</td>\n\
            			<td>\n\
            			<button type="button" class="btn btn-default removeButton"><i class="glyphicon glyphicon-minus"></i></button> \n\
            			</td>\n\
            			</tr>');
            		$('#pvt').formValidation('addField', 'Qualidade_instituicao[]');
            		$('#pvt').formValidation('addField', 'Qualidade_setor[]');
            		$('#pvt').formValidation('addField', 'Qualidade_origem[]');


            		if (Qualidade >= MAX_OPTIONS_Qualidade) {
            			$('#QUALIDADE').find('.addButton').attr('disabled', 'disabled');
            		}
            	}

            })

            // Remove button click handler --> instituicoes
            .on('click', '.removeButton', function () {
            	if ($(this).closest('tbody').attr('id') == 'ImplantacaoInstituicoes') {
            		Implantacao--;
            		var $row = $(this).closest('tr');
            		$row.remove();
            		var $option = $row.find('[name="instituicao[]"]');
            		$('#pvt').formValidation('removeField', $option);
            		$option = $row.find('[name="setor[]"]');
            		$('#pvt').formValidation('removeField', $option);
            		$option = $row.find('[name="origem[]"]');
            		$('#pvt').formValidation('removeField', $option);
            		if (Implantacao < MAX_OPTIONS_Implantacao) {
            			$('#IMPLATANCAO').find('.addButton').removeAttr('disabled');
            		}
            	} else {
            		Qualidade--;
            		var $row = $(this).closest('tr');
            		$row.remove();
            		var $option = $row.find('[name="Qualidade_instituicao[]"]');
            		$('#pvt').formValidation('removeField', $option);
            		$option = $row.find('[name="Qualidade_setor[]"]');
            		$('#pvt').formValidation('removeField', $option);
            		$option = $row.find('[name="Qualidade_origem[]"]');
            		$('#pvt').formValidation('removeField', $option);
            		if (Qualidade < MAX_OPTIONS_Qualidade) {
            			$('#QUALIDADE').find('.addButton').removeAttr('disabled');
            		}
            	}

            })



            .on('change', '[name="COMISSAOGD"]', function (e) {
            	if ($(this).val() === 'SIM') {
//                $('#pvt').formValidation('enableFieldValidators', 'BASESOBITO', true);
$('#pvt').formValidation('enableFieldValidators', 'COMISSAOFORM', true);
//                $('#pvt').formValidation('enableFieldValidators', 'BASESAT', true);
//                $('#pvt').formValidation('enableFieldValidators', 'BASEFERIDO', true);
//                $('#pvt').formValidation('enableFieldValidators', 'BASEFATORRISCO', true);
//                $('#pvt').formValidation('enableFieldValidators', 'MAPEAMENTO', true);
//                $('#pvt').formValidation('enableFieldValidators', 'LIMPEZA', true);
//                $('#pvt').formValidation('enableFieldValidators', 'LISTAUNICA', true);
//                $('#pvt').formValidation('enableFieldValidators', 'FATORRISCO', true);
//                $('#pvt').formValidation('enableFieldValidators', 'INDICADOROBITO', true);
//                $('#pvt').formValidation('enableFieldValidators', 'INDICADORFERIDO', true);
//                $('#pvt').formValidation('enableFieldValidators', 'QUADROMULTIPLO', true);
//                $('#pvt').formValidation('enableFieldValidators', 'LOCAISCHAVE', true);
//                $('#pvt').formValidation('enableFieldValidators', 'RELATORIOS', true);
//                $('#pvt').formValidation('enableFieldValidators', 'LINKAGE', true);
} else {
//                $('#pvt').formValidation('enableFieldValidators', 'BASESOBITO', false);
$('#pvt').formValidation('enableFieldValidators', 'COMISSAOFORM', false);
//                $('#pvt').formValidation('enableFieldValidators', 'BASESAT', false);
//                $('#pvt').formValidation('enableFieldValidators', 'BASEFERIDO', false);
//                $('#pvt').formValidation('enableFieldValidators', 'BASEFATORRISCO', false);
//                $('#pvt').formValidation('enableFieldValidators', 'MAPEAMENTO', false);
//                $('#pvt').formValidation('enableFieldValidators', 'LIMPEZA', false);
//                $('#pvt').formValidation('enableFieldValidators', 'LISTAUNICA', false);
//                $('#pvt').formValidation('enableFieldValidators', 'FATORRISCO', false);
//                $('#pvt').formValidation('enableFieldValidators', 'INDICADOROBITO', false);
//                $('#pvt').formValidation('enableFieldValidators', 'INDICADORFERIDO', false);
//                $('#pvt').formValidation('enableFieldValidators', 'QUADROMULTIPLO', false);
//                $('#pvt').formValidation('enableFieldValidators', 'LOCAISCHAVE', false);
//                $('#pvt').formValidation('enableFieldValidators', 'RELATORIOS', false);
//                $('#pvt').formValidation('enableFieldValidators', 'LINKAGE', false);
//                $('#pvt').formValidation('enableFieldValidators', 'PRILINKAGE', false);
//                $('#pvt').formValidation('enableFieldValidators', 'ULTLINKAGE', false);
//                $('#pvt').formValidation('enableFieldValidators', 'ULTBASEVITIMAS', false);
//                $('#pvt').formValidation('enableFieldValidators', 'ULTBASEOBITO', false);
//                $('#pvt').formValidation('enableFieldValidators', 'NAOLINKOBITO', false);
//                $('#pvt').formValidation('enableFieldValidators', 'NAOLINKFER', false);
$('#pvt').formValidation('enableFieldValidators', 'UPDECRETOCOMISSAO', false);
$('#pvt').formValidation('enableFieldValidators', 'Qualidade_instituicao[]', false);
$('#pvt').formValidation('enableFieldValidators', 'Qualidade_setor[]', false);
$('#pvt').formValidation('enableFieldValidators', 'Qualidade_origem[]', false);

}
})
            .on('change', '[name="COMISSAOFORM"]', function (e) {
            	if ($(this).val() === 'SIM') {
            		$('#pvt').formValidation('enableFieldValidators', 'Qualidade_instituicao[]', true);
            		$('#pvt').formValidation('enableFieldValidators', 'Qualidade_setor[]', true);
            		$('#pvt').formValidation('enableFieldValidators', 'Qualidade_origem[]', true);
            	} else {
            		$('#pvt').formValidation('enableFieldValidators', 'Qualidade_instituicao[]', false);
            		$('#pvt').formValidation('enableFieldValidators', 'Qualidade_setor[]', false);
            		$('#pvt').formValidation('enableFieldValidators', 'Qualidade_origem[]', false);
            	}
            })
            .on('change', '[name="IDENTIFICACAORISCO"]', function (e) {
            	if ($(this).val() === 'SIM') {
            		$('#pvt').formValidation('enableFieldValidators', 'ULTIMOSEMESTRERISCO', true);
            		$('#pvt').formValidation('enableFieldValidators', 'ULTIMORISCO', true);
            		$('#pvt').formValidation('enableFieldValidators', 'FATORESRISCOACIDENTES', true);
            		$('#pvt').formValidation('enableFieldValidators', 'CONDUTARISCOACIDENTES', true);
            		$('#pvt').formValidation('enableFieldValidators', 'FATORESGRAVIDADE', true);
            		$('#pvt').formValidation('enableFieldValidators', 'FATORESFATAL', true);
            	} else {
            		$('#pvt').formValidation('enableFieldValidators', 'ULTIMOSEMESTRERISCO', false);
            		$('#pvt').formValidation('enableFieldValidators', 'ULTIMORISCO', false);
            		$('#pvt').formValidation('enableFieldValidators', 'FATORESRISCOACIDENTES', false);
            		$('#pvt').formValidation('enableFieldValidators', 'CONDUTARISCOACIDENTES', false);
            		$('#pvt').formValidation('enableFieldValidators', 'FATORESGRAVIDADE', false);
            		$('#pvt').formValidation('enableFieldValidators', 'FATORESFATAL', false);
            	}
            })
            .on('change', '[name="IDENTIFICACAORISCOCADA"]', function (e) {
            	if ($(this).val() === 'SIM') {
            		$('#pvt').formValidation('enableFieldValidators', 'ULTIMOSEMESTRERISCOCADA', true);
            		$('#pvt').formValidation('enableFieldValidators', 'ULTIMORISCOCADA', true);
            		$('#pvt').formValidation('enableFieldValidators', 'FATORESRISCOACIDENTESCADA', true);
            		$('#pvt').formValidation('enableFieldValidators', 'CONDUTARISCOACIDENTESCADA', true);
            		$('#pvt').formValidation('enableFieldValidators', 'FATORESGRAVIDADECADA', true);
            		$('#pvt').formValidation('enableFieldValidators', 'FATORESFATALCADA', true);
            	} else {
            		$('#pvt').formValidation('enableFieldValidators', 'ULTIMOSEMESTRERISCOCADA', false);
            		$('#pvt').formValidation('enableFieldValidators', 'ULTIMORISCOCADA', false);
            		$('#pvt').formValidation('enableFieldValidators', 'FATORESRISCOACIDENTESCADA', false);
            		$('#pvt').formValidation('enableFieldValidators', 'CONDUTARISCOACIDENTESCADA', false);
            		$('#pvt').formValidation('enableFieldValidators', 'FATORESGRAVIDADECADA', false);
            		$('#pvt').formValidation('enableFieldValidators', 'FATORESFATALCADA', false);
            	}
            })
            .on('change', '[name="FATORESRISCOACIDENTES_SIM"]', function (e) {
            	if ($('#FATORESRISCOACIDENTES_SIM-1').is(':checked')) {
            		$('#_amostra').show()
            	} else {
            		$('#_amostra').hide()
            		$('#AMOSTRA').val('')
            	}
            })
            .on('change', '[name="FATORESRISCOACIDENTES"]', function (e) {
            	if ($(this).val() == 'SIM') {
            		$('#_FATORESRISCOACIDENTES_SIM').show()
            	} else {
            		$('#_FATORESRISCOACIDENTES_SIM').hide()
            		$('#AMOSTRA').val('')
            	}
            })
            .on('change', '[name="QUADROGRUPOVITIMAS"]', function (e) {

            	if ($(this).val() == 'SIM') {

            		$('#_QUADROGRUPOVITIMAS').show();
            	} else {
            		$('#_QUADROGRUPOVITIMAS').hide();
            	}
            })
            .on('change', '[name="FATORESCHAVE"]', function (e) {
            	if ($(this).val() === 'SIM') {
            		$('#pvt').formValidation('enableFieldValidators', 'ULTIMOSEMESTREFATORESCHAVE', true);
            		$('#pvt').formValidation('enableFieldValidators', 'ULTIMOFATORESCHAVE', true);
            		$('#pvt').formValidation('enableFieldValidators', 'PRINCIPAISFATORESCHAVE[]', true);

            	} else {
            		$('#pvt').formValidation('enableFieldValidators', 'ULTIMOSEMESTREFATORESCHAVE', false);
            		$('#pvt').formValidation('enableFieldValidators', 'ULTIMOFATORESCHAVE', false);
            		$('#pvt').formValidation('enableFieldValidators', 'PRINCIPAISFATORESCHAVE[]', false);
            	}
            })
            .on('change', '[name="GRUPOSVITIMAS"]', function (e) {
            	if ($(this).val() === 'SIM') {
            		$('#pvt').formValidation('enableFieldValidators', 'ULTIMOSEMESTREGRUPOSVITIMAS', true);
            		$('#pvt').formValidation('enableFieldValidators', 'ULTIMOGRUPOSVITIMAS', true);
            		$('#pvt').formValidation('enableFieldValidators', 'PRINCIPAISGRUPOSVITIMAS[]', true);

            	} else {
            		$('#pvt').formValidation('enableFieldValidators', 'ULTIMOSEMESTREGRUPOSVITIMAS', false);
            		$('#pvt').formValidation('enableFieldValidators', 'ULTIMOGRUPOSVITIMAS', false);
            		$('#pvt').formValidation('enableFieldValidators', 'PRINCIPAISGRUPOSVITIMAS[]', false);
            		$('#pvt').formValidation('enableFieldValidators', 'PRINCIPAISGRUPOSVITIMAS_OUTRO', false);
            	}
            })
            .on('change', '[name="CONSTRUCAOQUADROMULTIPLO"]', function (e) {
            	if ($(this).val() === 'SIM') {
            		$('#pvt').formValidation('enableFieldValidators', 'ULTIMOSEMESTRECONSTRUCAOQUADROMULTIPLO', true);
            		$('#pvt').formValidation('enableFieldValidators', 'ULTIMOCONSTRUCAOQUADROMULTIPLO', true);

            	} else {
            		$('#pvt').formValidation('enableFieldValidators', 'ULTIMOSEMESTRECONSTRUCAOQUADROMULTIPLO', false);
            		$('#pvt').formValidation('enableFieldValidators', 'ULTIMOCONSTRUCAOQUADROMULTIPLO', false);
            	}
            })
            .on('change', '[name="PROGRAMAPRIORITARIOS"]', function (e) {
            	if ($(this).val() === 'SIM') {
            		$('#pvt').formValidation('enableFieldValidators', 'ULTIMOPROGRAMAPRIORITARIOS', true);

            	} else {
            		$('#pvt').formValidation('enableFieldValidators', 'ULTIMOPROGRAMAPRIORITARIOS', false);
            	}
            })
            .on('change', '[name="ACOESINTEGRADAS"]', function (e) {
            	if ($(this).val() === 'SIM') {
            		$('#pvt').formValidation('enableFieldValidators', 'ULTIMOACOESINTEGRADAS', true);
            		$('#pvt').formValidation('enableFieldValidators', 'PRINCIPAISACOESINTEGRADAS[]', true);

            	} else {
            		$('#pvt').formValidation('enableFieldValidators', 'ULTIMOACOESINTEGRADAS', false);
            		$('#pvt').formValidation('enableFieldValidators', 'PRINCIPAISACOESINTEGRADAS[]', false);
            		$('#pvt').formValidation('enableFieldValidators', 'PRINCIPAISACOESINTEGRADAS_OUTRO', false);
            	}
            })
            .on('change', '[name="LINKAGE"]', function (e) {
            	($(this).val() === 'SIM') ? ($('#pvt').formValidation('enableFieldValidators', 'PRILINKAGE', true)) : ($('#pvt').formValidation('enableFieldValidators', 'PRILINKAGE', false));
            	($(this).val() === 'SIM') ? ($('#pvt').formValidation('enableFieldValidators', 'ULTLINKAGE', true)) : ($('#pvt').formValidation('enableFieldValidators', 'ULTLINKAGE', false));
            	($(this).val() === 'SIM') ? ($('#pvt').formValidation('enableFieldValidators', 'bases_utilizadas[]', true)) : ($('#pvt').formValidation('enableFieldValidators', 'bases_utilizadas[]', false));
            	($(this).val() === 'SIM') ? ($('#pvt').formValidation('enableFieldValidators', 'ULTBASEVITIMAS', true)) : ($('#pvt').formValidation('enableFieldValidators', 'ULTBASEVITIMAS', false));
            	($(this).val() === 'SIM') ? ($('#pvt').formValidation('enableFieldValidators', 'NAOLINKFER', false)) : ($('#pvt').formValidation('enableFieldValidators', 'NAOLINKFER', true));
            	($(this).val() === 'SIM') ? ($('#pvt').formValidation('enableFieldValidators', 'ULTBASEOBITO', true)) : ($('#pvt').formValidation('enableFieldValidators', 'ULTBASEOBITO', false));
            	($(this).val() === 'SIM') ? ($('#pvt').formValidation('enableFieldValidators', 'NAOLINKOBITO', false)) : ($('#pvt').formValidation('enableFieldValidators', 'NAOLINKOBITO', true));
            	($(this).val() === 'SIM') ? ($('#pvt').formValidation('enableFieldValidators', 'COMOFOILISTAVITIMAS', false)) : ($('#pvt').formValidation('enableFieldValidators', 'COMOFOILISTAVITIMAS', true));
            })
            .on('change', '[name="COMISSAO"]', function (e) {
            	($(this).val() === 'SIM') ? ($('#pvt').formValidation('enableFieldValidators', 'NOMECOMISSAO', true)) : ($('#pvt').formValidation('enableFieldValidators', 'NOMECOMISSAO', false));
            	($(this).val() === 'SIM') ? ($('#pvt').formValidation('enableFieldValidators', 'instituicao[]', true)) : ($('#pvt').formValidation('enableFieldValidators', 'instituicao[]', false));
            	($(this).val() === 'SIM') ? ($('#pvt').formValidation('enableFieldValidators', 'setor[]', true)) : ($('#pvt').formValidation('enableFieldValidators', 'setor[]', false));
            	($(this).val() === 'SIM') ? ($('#pvt').formValidation('enableFieldValidators', 'origem[]', true)) : ($('#pvt').formValidation('enableFieldValidators', 'origem[]', false));
            	$("#_DTREUNIAOCI").show();
            })
            $("input[name='COMISSAO']").click(function () {
            	$('#_COMISSAOINTERSETORIAL').css('display', ($(this).val() === 'SIM') ? 'block' : 'none');
            	$('#_REGREUNIAOPVT').css('display', ($(this).val() === 'NAO') ? 'block' : 'none');
            	$('#_UPDECRETO').css('display', ($(this).val() === 'SIM') ? 'block' : 'none');
            })



            .on('change', '[name="PLANOACAO"]', function (e) {
            	($(this).val() === 'SIM') ? ($('#pvt').formValidation('enableFieldValidators', 'QUADROPLANOACAO', true)) : ($('#pvt').formValidation('enableFieldValidators', 'QUADROPLANOACAO', false));
            })


            $("input[name='ALCOOL']").click(function () {
            	$('#METAALC').css('display', ($(this).val() === 'SIM') ? 'block' : 'none');
            	$('#PERCALC').css('display', ($(this).val() === 'SIM') ? 'block' : 'none');
            });
            $("input[name='VELOCIDADE']").click(function () {
            	$('#_VELOCIDADE').css('display', ($(this).val() === 'SIM') ? 'block' : 'none');
            	($(this).val() === 'SIM') ? ($('#pvt').formValidation('enableFieldValidators', 'ULTIMOVELOCIDADE', true)) : ($('#pvt').formValidation('enableFieldValidators', 'ULTIMOVELOCIDADE', false));
            	($(this).val() === 'SIM') ? ($('#pvt').formValidation('enableFieldValidators', 'QUADROVELOCIDADE', true)) : ($('#pvt').formValidation('enableFieldValidators', 'QUADROVELOCIDADE', false));

            });
            $("input[name='DEFINIDOMUNICIPIO']").click(function () {
            	$('#_DEFINIDOMUNICIPIOS').css('display', ($(this).val() === 'SIM') ? 'block' : 'none');
            	($(this).val() === 'SIM') ? ($('#pvt').formValidation('enableFieldValidators', 'ULTIMODEFINIDOMUNICIPIO', true)) : ($('#pvt').formValidation('enableFieldValidators', 'ULTIMODEFINIDOMUNICIPIO', false));
            	($(this).val() === 'SIM') ? ($('#pvt').formValidation('enableFieldValidators', 'QUADRODEFINIDOMUNICIPIO', true)) : ($('#pvt').formValidation('enableFieldValidators', 'QUADRODEFINIDOMUNICIPIO', false));

            });
            $("input[name='OUTROPROGRAMA']").click(function () {
            	$('#METAPROG').css('display', ($(this).val() === 'SIM') ? 'block' : 'none');
            	$('#PERCPROG').css('display', ($(this).val() === 'SIM') ? 'block' : 'none');
            });

            $("input[name='COMISSAOGD']").click(function () {
            	$('#_COMISSAOGD').css('display', ($(this).val() === 'SIM') ? 'block' : 'none');
            });
            $("input[name='PLANOACAO']").click(function () {
            	$('#QUADROPLANOACAO').css('display', ($(this).val() === 'SIM') ? 'block' : 'none');
            });

            $("input[name='COMISSAOFORM']").click(function () {
            	$('#_COMISSAOFORM').css('display', ($(this).val() === 'SIM') ? 'block' : 'none');
            	$('#_UPDECRETOCOMISSAO').css('display', ($(this).val() === 'SIM') ? 'block' : 'none');
            });

            $("input[name='LINKAGE']").click(function () {

            	if ($("[name='LINKAGE']:checked").val() === 'SIM') {
            		$('#pvt').formValidation('enableFieldValidators', 'PRILINKAGE', true)
            		$('#pvt').formValidation('enableFieldValidators', 'PRIMEIROANOLINKAGE', true)
            		$('#pvt').formValidation('enableFieldValidators', 'bases_utilizadas[]', true)
            		$('#pvt').formValidation('enableFieldValidators', 'ULTLINKAGEANOLINKAGE', true)
            		$('#pvt').formValidation('enableFieldValidators', 'ULTLINKAGE', true)

            		$('#pvt').formValidation('enableFieldValidators', 'COMOFOILISTAVITIMAS', false)
            		$('#pvt').formValidation('enableFieldValidators', 'NAOLINKOBITO', false)
            		$('#pvt').formValidation('enableFieldValidators', 'NAOLINKFER', false)
            	} else {
            //console.log('asdq')
            $('#pvt').formValidation('enableFieldValidators', 'COMOFOILISTAVITIMAS', true)
            $('#pvt').formValidation('enableFieldValidators', 'NAOLINKOBITO', true)
            $('#pvt').formValidation('enableFieldValidators', 'NAOLINKFER', true)

            $('#pvt').formValidation('enableFieldValidators', 'PRILINKAGE', false)
            $('#pvt').formValidation('enableFieldValidators', 'PRIMEIROANOLINKAGE', false)
            $('#pvt').formValidation('enableFieldValidators', 'bases_utilizadas[]', false)
            $('#pvt').formValidation('enableFieldValidators', 'ULTLINKAGEANOLINKAGE', false)
            $('#pvt').formValidation('enableFieldValidators', 'ULTLINKAGE', false)
        }
        $('#_PRILINKAGE').css('display', ($(this).val() === 'SIM') ? 'block' : 'none');
        $('#NAO_PRILINKAGE').css('display', ($(this).val() === 'NAO') ? 'block' : 'none');
    });

            $("input[name='IDENTIFICACAORISCO']").click(function () {
            	$('#_IDENTIFICACAORISCO').css('display', ($(this).val() === 'SIM') ? 'block' : 'none');
            });
            $("input[name='IDENTIFICACAORISCOCADA']").click(function () {
            	$('#_IDENTIFICACAORISCOCADA').css('display', ($(this).val() === 'SIM') ? 'block' : 'none');
            });
            $("input[name='BASEFERIDO']").click(function () {
            	$('#_BASEFERIDO').css('display', ($(this).val() === 'SIM') ? 'block' : 'none');
            });
            $("input[name='BEBERDIRIGIR']").click(function () {
            	$('#_BEBERDIRIGIR').css('display', ($(this).val() === 'SIM') ? 'block' : 'none');
            	($(this).val() === 'SIM') ? ($('#pvt').formValidation('enableFieldValidators', 'ULTIMOBEBERDIRIGIR', true)) : ($('#pvt').formValidation('enableFieldValidators', 'ULTIMOBEBERDIRIGIR', false));
            	($(this).val() === 'SIM') ? ($('#pvt').formValidation('enableFieldValidators', 'QUADROBEBERDIRIGIR', true)) : ($('#pvt').formValidation('enableFieldValidators', 'QUADROBEBERDIRIGIR', false));


            });

            $('#base-feridos').change(function () {
            	$('#_base-feridos-hospital').css('display', ($(this).val() == 'REGISTRO DE INTERNAÇÃO DE HOSPITAIS') ? 'block' : 'none');

            });
            $("input[name='coordenaTEM']").change(function () {
        //console.log('coordenatem')
        if ($(this).val()=='SIM'){
        	$('#_coordenaTEM').show(); 
        	$('#pvt').formValidation('enableFieldValidators', 'COORDENADOR1', true)
        	$('#pvt').formValidation('enableFieldValidators', 'INSTITUICAO1', true)
        	$('#pvt').formValidation('enableFieldValidators', 'EMAIL1', true)
        	$('#pvt').formValidation('enableFieldValidators', 'TEL1', true)

        }else{
        	$('#_coordenaTEM').hide();
        	$('#pvt').formValidation('enableFieldValidators', 'COORDENADOR1', false)
        	$('#pvt').formValidation('enableFieldValidators', 'INSTITUICAO1', false)
        	$('#pvt').formValidation('enableFieldValidators', 'EMAIL1', false)
        	$('#pvt').formValidation('enableFieldValidators', 'TEL1', false)


        }

    });
            $("input[name='CONSTRUCAOQUADROMULTIPLO']").click(function () {
            	$('#_CONSTRUCAOQUADROMULTIPLO').css('display', ($(this).val() === 'SIM') ? 'block' : 'none');
            });
            $("input[name='FATORESCHAVE']").click(function () {
            	$('#_FATORESCHAVE').css('display', ($(this).val() === 'SIM') ? 'block' : 'none');
            });
            $("input[name='GRUPOSVITIMAS']").click(function () {
            	$('#_GRUPOSVITIMAS').css('display', ($(this).val() === 'SIM') ? 'block' : 'none');
            });
            $("input[name='PROGRAMAPRIORITARIOS']").click(function () {
            	$('#_PROGRAMAPRIORITARIOS').css('display', ($(this).val() === 'SIM') ? 'block' : 'none');
            });
            $("input[name='ACOESINTEGRADAS']").click(function () {
            	$('#_ACOESINTEGRADAS').css('display', ($(this).val() === 'SIM') ? 'block' : 'none');
            });


            $("input[name='BASESOBITO']").click(function () {
            	$('#_BASESOBITO').css('display', ($(this).val() === 'SIM') ? 'block' : 'none');
            	($(this).val() === 'SIM') ? ($('#pvt').formValidation('enableFieldValidators', 'base_obitos[]', true)) : ($('#pvt').formValidation('enableFieldValidators', 'base_obitos[]', false));
            	if (($("[name='BASESAT']:checked").val() === 'SIM') && ($("[name='BASEFERIDO']:checked").val() === 'SIM') && ($("[name='BASESOBITO']:checked").val() === 'SIM')) {
            		$('#_LINKAGE').show();
            		$('#pvt').formValidation('enableFieldValidators', 'LINKAGE', true)
            	} else {

            		$("input[name='LINKAGE']").prop('checked', false);
            		$('#_LINKAGE').hide();
            		$('#_PRILINKAGE').hide();
            		$('#NAO_PRILINKAGE').hide();
            		$('#pvt').formValidation('enableFieldValidators', 'LINKAGE', false)
            		$('#pvt').formValidation('enableFieldValidators', 'PRILINKAGE', false)
            		$('#pvt').formValidation('enableFieldValidators', 'PRIMEIROANOLINKAGE', false)
            		$('#pvt').formValidation('enableFieldValidators', 'ULTLINKAGE', false)
            		$('#pvt').formValidation('enableFieldValidators', 'ULTLINKAGEANOLINKAGE', false)
            		$('#pvt').formValidation('enableFieldValidators', 'bases_utilizadas[]', false)
            		$('#pvt').formValidation('enableFieldValidators', 'COMOFOILISTAVITIMAS', false)
            		$('#pvt').formValidation('enableFieldValidators', 'NAOLINKOBITO', false)
            		$('#pvt').formValidation('enableFieldValidators', 'NAOLINKFER', false)
            		$('#pvt').formValidation('enableFieldValidators', 'bases_utilizadas_hospital', false)
            		$('#pvt').formValidation('enableFieldValidators', 'bases_utilizadas_outras', false)

            	}
            });
            $("input[name='BASESAT']").click(function () {
            	$('#_BASESAT').css('display', ($(this).val() === 'SIM') ? 'block' : 'none');
            	($(this).val() === 'SIM') ? ($('#pvt').formValidation('enableFieldValidators', 'base_dados[]', true)) : ($('#pvt').formValidation('enableFieldValidators', 'base_dados[]', false));
            	if (($("[name='BASESAT']:checked").val() === 'SIM') && ($("[name='BASEFERIDO']:checked").val() === 'SIM') && ($("[name='BASESOBITO']:checked").val() === 'SIM')) {
            		$('#_LINKAGE').show();
            		$('#pvt').formValidation('enableFieldValidators', 'LINKAGE', true)
            	} else {
            		$("input[name='LINKAGE']").prop('checked', false);
            		$('#_LINKAGE').hide();
            		$('#_PRILINKAGE').hide();
            		$('#NAO_PRILINKAGE').hide();
            		$('#pvt').formValidation('enableFieldValidators', 'LINKAGE', false);
            		$('#pvt').formValidation('enableFieldValidators', 'PRILINKAGE', false);
            		$('#pvt').formValidation('enableFieldValidators', 'PRIMEIROANOLINKAGE', false);
            		$('#pvt').formValidation('enableFieldValidators', 'ULTLINKAGE', false)
            		$('#pvt').formValidation('enableFieldValidators', 'ULTLINKAGEANOLINKAGE', false)
            		$('#pvt').formValidation('enableFieldValidators', 'bases_utilizadas[]', false)
            		$('#pvt').formValidation('enableFieldValidators', 'COMOFOILISTAVITIMAS', false)
            		$('#pvt').formValidation('enableFieldValidators', 'NAOLINKOBITO', false)
            		$('#pvt').formValidation('enableFieldValidators', 'NAOLINKFER', false)
            		$('#pvt').formValidation('enableFieldValidators', 'bases_utilizadas_hospital', false)
            		$('#pvt').formValidation('enableFieldValidators', 'bases_utilizadas_outras', false)
            	}
            });
            $("input[name='BASEFERIDO']").click(function () {
            	$('#_BASEFERIDO').css('display', ($(this).val() === 'SIM') ? 'block' : 'none');
            	($(this).val() === 'SIM') ? ($('#pvt').formValidation('enableFieldValidators', 'base_feridos[]', true)) : ($('#pvt').formValidation('enableFieldValidators', 'base_feridos[]', false));
            	if (($("[name='BASESAT']:checked").val() === 'SIM') && ($("[name='BASEFERIDO']:checked").val() === 'SIM') && ($("[name='BASESOBITO']:checked").val() === 'SIM')) {
            		$('#_LINKAGE').show();
            		$('#pvt').formValidation('enableFieldValidators', 'LINKAGE', true)
            	} else {
            		$("input[name='LINKAGE']").prop('checked', false);
            		$('#_LINKAGE').hide();
            		$('#_PRILINKAGE').hide();
            		$('#NAO_PRILINKAGE').hide();
            		$('#pvt').formValidation('enableFieldValidators', 'LINKAGE', false)
            		$('#pvt').formValidation('enableFieldValidators', 'PRILINKAGE', false)
            		$('#pvt').formValidation('enableFieldValidators', 'PRIMEIROANOLINKAGE', false)
            		$('#pvt').formValidation('enableFieldValidators', 'ULTLINKAGE', false)
            		$('#pvt').formValidation('enableFieldValidators', 'ULTLINKAGEANOLINKAGE', false)
            		$('#pvt').formValidation('enableFieldValidators', 'bases_utilizadas[]', false)
            		$('#pvt').formValidation('enableFieldValidators', 'COMOFOILISTAVITIMAS', false)
            		$('#pvt').formValidation('enableFieldValidators', 'NAOLINKOBITO', false)
            		$('#pvt').formValidation('enableFieldValidators', 'NAOLINKFER', false)
            		$('#pvt').formValidation('enableFieldValidators', 'bases_utilizadas_hospital', false)
            		$('#pvt').formValidation('enableFieldValidators', 'bases_utilizadas_outras', false)
            	}

            });

            $("input[name='base_dados[]']").click(function () {
            	if ($(this).is(':checked')) {
            		$("input[name='bases_utilizadas[]'][value='" + $(this).val() + "']").show()
            		$("input[name='bases_utilizadas[]'][value='" + $(this).val() + "']").closest('.checkbox').show()
            		if ($(this).val() == 'OUTRAS') {
            			$('#_BASESAT_Outras').show();
            			$('#pvt').data('formValidation').enableFieldValidators('base_dados_outras', true);
            		}
            	} else {
            		if ($(this).val() == 'OUTRAS') {
            			$('#_BASESAT_Outras').hide();
            			$('#pvt').data('formValidation').enableFieldValidators('base_dados_outras', false);
            		}
            		$("input[name='bases_utilizadas[]'][value='" + $(this).val() + "']").hide()
            		$("input[name='bases_utilizadas[]'][value='" + $(this).val() + "']").closest('.checkbox').hide()
            	}
            });
            $("input[name='PRINCIPAISGRUPOSVITIMAS[]']").click(function () {
            	if ($(this).is(':checked')) {
            		if ($(this).val() == 'OUTRO') {
            			$('#_PRINCIPAISGRUPOSVITIMAS_OUTRO').show();
            			$('#pvt').data('formValidation').enableFieldValidators('PRINCIPAISGRUPOSVITIMAS_OUTRO', true);
            		}
            	} else {
            		if ($(this).val() == 'OUTRO') {
            			$('#_PRINCIPAISGRUPOSVITIMAS_OUTRO').hide();
            			$('#pvt').data('formValidation').enableFieldValidators('PRINCIPAISGRUPOSVITIMAS_OUTRO', false);
            		}
            	}
            });
            $("input[name='PRINCIPAISACOESINTEGRADAS[]']").click(function () {
            	if ($(this).is(':checked')) {
            		if ($(this).val() == 'OUTRO') {
            			$('#_PRINCIPAISACOESINTEGRADAS_OUTRO').show();
            			$('#pvt').data('formValidation').enableFieldValidators('PRINCIPAISACOESINTEGRADAS_OUTRO', true);
            		}
            	} else {
            		if ($(this).val() == 'OUTRO') {
            			$('#_PRINCIPAISACOESINTEGRADAS_OUTRO').hide();
            			$('#pvt').data('formValidation').enableFieldValidators('PRINCIPAISACOESINTEGRADAS_OUTRO', false);
            		}
            	}
            });
            $("input[name='base_feridos[]']").click(function () {
            	if ($(this).is(':checked')) {
            		$("input[name='bases_utilizadas[]'][value='" + $(this).val() + "']").show()
            		$("input[name='bases_utilizadas[]'][value='" + $(this).val() + "']").closest('.checkbox').show()
            		if ($(this).val() == 'REGISTRO DE INTERNAÇÃO DE HOSPITAIS') {
            			$('#_base_feridos_hospital').show();
            			$('#pvt').data('formValidation').enableFieldValidators('base_feridos_hospital', true);
            		}
            	} else {
            		if ($(this).val() == 'REGISTRO DE INTERNAÇÃO DE HOSPITAIS') {
            			$('#_base_feridos_hospital').hide();
            			$('#pvt').data('formValidation').enableFieldValidators('base_feridos_hospital', false);
            		}
            		$("input[name='bases_utilizadas[]'][value='" + $(this).val() + "']").hide()
            		$("input[name='bases_utilizadas[]'][value='" + $(this).val() + "']").closest('.checkbox').hide()
            	}
            });
            $("input[name='bases_utilizadas[]']").click(function () {
            	if ($(this).is(':checked')) {
            		if ($(this).val() == 'REGISTRO DE INTERNAÇÃO DE HOSPITAIS') {
            			$('#_bases_utilizadas_hospital').show();
            			$('#pvt').data('formValidation').enableFieldValidators('bases_utilizadas_hospital', true);
            		} else if ($(this).val() == 'OUTRAS') {
            			$('#_bases_utilizadas_outras').show();
            			$('#pvt').data('formValidation').enableFieldValidators('bases_utilizadas_outras', true);
            		}
            	} else {
            		if ($(this).val() == 'REGISTRO DE INTERNAÇÃO DE HOSPITAIS') {
            			$('#_bases_utilizadas_hospital').hide();
            			$('#pvt').data('formValidation').enableFieldValidators('bases_utilizadas_hospital', false);
            		} else if ($(this).val() == 'OUTRAS') {
            			$('#_bases_utilizadas_outras').hide();
            			$('#pvt').data('formValidation').enableFieldValidators('bases_utilizadas_outras', false);
            		}
            	}
            });
            $("input[name='base_obitos[]']").click(function () {
            	if ($(this).is(':checked')) {
            		$("input[name='bases_utilizadas[]'][value='" + $(this).val() + "']").show()
            		$("input[name='bases_utilizadas[]'][value='" + $(this).val() + "']").closest('.checkbox').show()
            	} else {
            		$("input[name='bases_utilizadas[]'][value='" + $(this).val() + "']").hide()
            		$("input[name='bases_utilizadas[]'][value='" + $(this).val() + "']").closest('.checkbox').hide()
            	}
            });

            $('#DTREUNIAOCI').datepicker({
            	language: "pt-BR",
            	autoclose: true,
            	format: 'dd/mm/yyyy',
            	startDate: "01/01/2010",
            	endDate:  new Date(),
            	todayHighlight: true
            }).on('changeDate', function (e) {
        // Revalidate the date field
        $('#pvt').formValidation('revalidateField', 'DTREUNIAOCI');
    });
            $('#DTCOMISSAO').datepicker({
            	language: "pt-BR",
            	autoclose: true,
            	format: 'dd/mm/yyyy',
            	startDate: "01/01/2010",
            	endDate:  new Date(),
            	todayHighlight: true
            }).on('changeDate', function (e) {
        // Revalidate the date field
        $('#pvt').formValidation('revalidateField', 'DTCOMISSAO');
    });
            $('#DTDECRETO').datepicker({
            	language: "pt-BR",
            	autoclose: true,
            	format: 'dd/mm/yyyy',
            	startDate: "01/01/2010",
            	endDate:  new Date(),
            	todayHighlight: true
            }).on('changeDate', function (e) {
        // Revalidate the date field
        $('#pvt').formValidation('revalidateField', 'DTDECRETO');
    });


        });




    </script>
    @endsection