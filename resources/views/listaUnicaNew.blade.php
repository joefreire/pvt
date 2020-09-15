@extends('layouts.app')
@section('styles')

{{-- 
    <link rel="stylesheet" href="//ajax.googleapis.com/ajax/libs/jqueryui/1.8/themes/cupertino/jquery-ui.css"/> --}}
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
            <h3>Lista Única</h3>
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
            <img class="info" src=" {{ asset('libraries/img/iconinfo.png') }}" alt="Informações"/>
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
                <input type="hidden" id="CodCidade" class="loadData" name="CodCidade"> 
            </div>
            @else
            <input type="hidden" id="CodCidade" class="loadData" name="CodCidade" value="{{ Auth::user()->CodCidade }}">
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
        <div id="desabilitar" style="display:none;">  

            <h3 class="control-label" for="arquivos" style="text-align: left;">Selecione o arquivo da lista Única </h3>
            <form name="sendFile" class="form-inline" id="sendFile" method="POST" enctype="multipart/form-data" action="{{route('listaUnicaGrande')}}">
                <p></p>
                <div class="form-group">
                    <label for="file">Arquivo Lista Única:</label>
                    <input type="file" class="form-control" name="arquivo" id="xlf" /> 
                </div>
                @csrf
                <input hidden="true" type="radio" name="format" value="json"><br>
                <input type="hidden" name="CodCidade" id="CodCidadeGrande">
                <input type="hidden" name="Ano" id="AnoGrande">
                <input type="hidden" name="Trimestre" id="TrimestreGrande">
                <button type="submit" class="btn btn-default">Enviar</button>
            </form>

        <!--<pre id="out"></pre>
            <br />-->

        </div> 
        <div id="loading-image" style="display:none;">  
            <img src="{{ asset('libraries/img/loading2.gif') }}" alt="" style="width: 5%;">
            <br><BR>
            <span>CARREGANDO ARQUIVO</span>
            <span><BR>Gravando dados no banco de dados <BR> Este processo pode demorar um pouco<BR> Não Feche o Navegador <BR></span>
            <BR>
            <div id="prog" class="progress-bar" role="progressbar" aria-valuenow="0"
            aria-valuemin="0" aria-valuemax="100" style="width:0%">
            <span class="" id="textProgress"></span>
            <BR>
        </div>
    </div>
    <div id="_delete" style="display:none;">  
        <img src="{{ asset('libraries/img/loading2.gif') }}" alt="" style="width: 5%;">
        <br><BR>
        <span>DELETANDO DADOS</span>
        <span><BR>Limpando os dados no banco de dados <BR> Este processo pode demorar um pouco<BR> Não Feche o Navegador <BR></span>
        <BR><BR>

    </div>

</div> 

<div id="respose"></div>

<style>
    .modal-dialog.erros {
        width: 100%;
    }
</style>
<div class="col-md-12"> 
    <div id="_processo" style="display:none;">   
        <span>Lista Única em processamento</span> <BR>
        <span>Aguarde</span> <BR><BR>
    </div>
    <div id="_sucesso" style="display:none;">   
        <span>Lista Única do periodo foi Carregada</span> <BR><BR>
        <span class="linkSim">Upload SIM <a href="{{route('sim')}}" class="linkSim">Clique Aqui</a></span><BR>
        <span class="linkSim">Upload SIH <a href="{{route('sih')}}" class="linkSim">Clique Aqui</a></span><BR>
        <BR>
        <span class="linkParesSim">Agora você  pode verificar os <a href="{{route('sim.pares')}}" class="linkParesSim"> pares verdadeiros e falsos do SIM</a><BR></span>
        <span class="linkParesSih">Agora você  pode verificar os <a href="{{route('sih.pares')}}" class="linkParesSih"> pares verdadeiros e falsos do SIH</a><BR>
            <BR></span>
            <span>Editar Lista Única no Quadro Multiplo <a class="linkQuadroMultiplo" href="{{ route('quadroMultiplo') }}">Clique Aqui</a><BR></span><BR>
            <span>Fazer novamente o envio da lista única do período <a href="#" onclick="ApagaDados();">Clique Aqui</a><BR></span><BR>
        </span> <BR>
    </div>
    <div id="_tabelaResultados" class="table-responsive" style="display:none;">   
        <span>Fazer novamente o envio da lista única do período <a href="#" onclick="ApagaDados();">Clique Aqui</a></span><BR><BR>

        <table class="table table-bordered table-hover display nowrap dataTable" id="table-2" width="100%">     
            <thead>
                <tr>
                    <th data-field="FonteDados" data-sortable="true" data-cell-style="cellStyle">Fonte</th>
                    <th data-field="Boletim" data-sortable="true" data-cell-style="cellStyle">BO</th>
                    <th data-field="NomeCompleto" data-sortable="true" data-cell-style="cellStyle">Nome</th>
                    <th data-field="NomeMae" data-sortable="true">Mãe</th>
                    <th data-field="Sexo" data-sortable="true" >Sexo</th>
                    <th data-field="DataNascimento" data-sortable="true" data-cell-style="cellStyle">Dt Nascimento</th>
                    <th data-field="DataAcidente" data-sortable="true" data-cell-style="cellStyle">Dt Acidente</th>
                    <th data-field="CondicaoVitima" data-sortable="true" >Condição <BR>da vitima</th>
                    <th data-field="HoraAcidente" data-sortable="true" >Hora do<BR>Acidente</th>
                    <th data-field="GravidadeLesao" data-sortable="true" >Gravidade</th>
                    <th data-field="TipoVeiculo" data-sortable="true" >Tipo Veiculo</th>
                </tr>
            </thead>
        </table>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="myModal" role="dialog">
        <div class="modal-dialog erros" role="document">
            <div class="modal-content">
                <div class="modal-header" style="padding:35px 50px;">

                    <form name="ajaxform" id="ajaxform" method="POST">
                        <input id="id" name="id" type="hidden" >
                        <div class="row">
                            <div class="form-group col-md-2" id="_Fonte_de_dados">
                                <label class="control-label" for="textinput">Fonte de Dados</label>  
                                <select id="Fonte_de_dados" name="FonteDados" class="form-control" required="">
                                    <option></option>
                                    <option value="CORPO DE BOMBEIROS">CORPO DE BOMBEIROS</option>
                                    <option value="POLICIA RODOVIARIA FEDERAL">POLICIA RODOVIARIA FEDERAL</option>
                                    <option value="POLICIA MILITAR">POLICIA MILITAR</option>
                                    <option value="SAMU">SAMU</option>
                                    <option value="DETRAN">DETRAN</option>
                                    <option value="IML">IML</option>
                                    <option value="DELEGACIA DE TRANSITO">DELEGACIA DE TRÂNSITO</option>
                                    <option value="ORGAO MUNICIPAL DE TRANSITO">ORGAO MUNICIPAL DE TRÂNSITO</option>
                                    <option value="OUTRO">OUTRO</option>
                                    <option value="NAO INFORMADO">NAO INFORMADO</option>
                                </select>
                            </div>
                            <div class="form-group col-md-2" id="_Boletim">  
                                <label class="control-label" for="textinput">Boletim de Ocorrência</label>  
                                <input id="Boletim" name="Boletim" type="text"  class="form-control input-md"  required="">
                            </div>
                            <div class="form-group col-md-4" id="_Nome_Completo">  
                                <label class="control-label" for="textinput">Nome Completo</label>  
                                <input id="Nome_Completo" name="NomeCompleto" type="text"  class="form-control input-md" required="">

                            </div>
                            <div class="form-group col-md-4" id="_Nome_da_mae">  
                                <label class="control-label" for="textinput">Nome da Mãe</label>  
                                <input id="Nome_da_mae" name="NomeMae" type="text"  class="form-control input-md" >
                            </div>
                        </div>   
                        <div class="row">
                            <div class="form-group col-md-2" id="_Sexo"> 
                                <label class="control-label" for="selectbasic">Sexo</label>
                                <select id="Sexo" name="Sexo" class="form-control" >
                                    <option></option>
                                    <option value="MASCULINO">MASCULINO</option>
                                    <option value="FEMININO">FEMININO</option>
                                    <option value="IGNORADO">IGNORADO</option>
                                    <option value="NAO INFORMADO">NAO INFORMADO</option>
                                </select>
                            </div>
                            <div class="form-group col-md-2" id="_nascimento">  
                                <label class="control-label" for="textinput">Data de Nascimento</label>  
                                <input id="nascimento" name="DataNascimento" type="text"  class="form-control input-md" required="">
                            </div>


                            <div class="form-group col-md-2" id="_acidente">  
                                <label class="control-label" for="textinput">Data do acidente</label>  
                                <input id="acidente" name="DataAcidente" type="text"  class="form-control input-md" required="">
                            </div>


                            <div class="form-group col-md-2" id="_Hora">  
                                <label class="control-label" for="textinput">Hora do acidente</label>  
                                <input id="Hora" name="Hora" type="number" max="99" min="00" class="form-control input-md">
                            </div>
                            <div class="form-group col-md-2" id="_Gravidade">  
                                <label class="control-label" for="textinput">Gravidade</label>  
                                <select id="Gravidade" name="GravidadeLesao" class="form-control">
                                    <option></option>
                                    <option value="SEM LESOES">SEM LESOES</option>
                                    <option value="COM LESOES">COM LESOES</option>
                                    <option value="LEVE">LEVE</option>
                                    <option value="MODERADA">MODERADA</option>
                                    <option value="GRAVE">GRAVE</option>                                    
                                    <option value="FATAL">FATAL</option>                                    
                                    <option value="FATAL LOCAL">FATAL NO LOCAL</option>                                    
                                    <option value="FATAL POSTERIOR">FATAL POSTERIOR</option>                                    
                                    <option value="LESOES NAO ESPECIFICADAS">LESOES NAO ESPECIFICADAS</option>
                                    <option value="NAO INFORMADO">NAO INFORMADO</option>
                                </select>
                            </div>
                        </div>    
                        <div class="row">
                            <div class="form-group col-md-3" id="_TipoAcidente">  
                                <label class="control-label" for="textinput">Tipo do Acidente</label>
                                <select name="TipoAcidente" id="TipoAcidente" class="form-control" >
                                    <option value=""></option>
                                    @foreach(\App\Models\QuadroMultiplo::getTiposAcidente() as $tipoAcidente)
                                    <option value="{{ $tipoAcidente }}">{{ $tipoAcidente }}</option>
                                    @endforeach 
                                </select>
                            </div>
                            <div class="form-group col-md-2" id="_Condicao_vitima">  
                                <label class="control-label" for="textinput">Condição da vitima</label>  
                                <select id="Condicao_vitima" name="CondicaoVitima" class="form-control" >
                                    <option></option>
                                    <option value="PEDESTRE">PEDESTRE</option>
                                    <option value="CONDUTOR">CONDUTOR</option>
                                    <option value="CONDUTOR AUTOMOVEL">CONDUTOR AUTOMOVEL</option>
                                    <option value="CONDUTOR MOTO">CONDUTOR MOTO</option>                                    
                                    <option value="CONDUTOR VEICULO PESADO">CONDUTOR VEICULO PESADO</option>
                                    <option value="CONDUTOR ONIBUS">CONDUTOR ONIBUS</option>
                                    <option value="CONDUTOR OUTROS">CONDUTOR OUTROS</option>
                                    <option value="CICLISTA">CICLISTA</option>
                                    <option value="PASSAGEIRO">PASSAGEIRO</option>
                                    <option value="NAO INFORMADO">NAO INFORMADO</option>
                                </select>
                            </div>
                            <div class="form-group col-md-2" id="_Tipo_Veiculo">  
                                <label class="control-label" for="textinput">Tipo Veiculo</label>  
                                <select id="Tipo_Veiculo" name="TipoVeiculo" class="form-control" >
                                    <option></option>
                                    <option value="CARRO">CARRO</option>
                                    <option value="MOTOCICLETA">MOTOCICLETA</option>
                                    <option value="CAMINHAO">CAMINHAO</option>
                                    <option value="ONIBUS">ONIBUS</option>                                    
                                    <option value="TRICICLO">TRICICLO</option>
                                    <option value="BICICLETA">BICICLETA</option>
                                    <option value="PEDESTRE">PEDESTRE</option>
                                    <option value="OUTROS">OUTROS</option>
                                    <option value="NAO INFORMADO">NAO INFORMADO</option>
                                </select>
                            </div>
                            <div class="form-group col-md-2" id="_Placa">  
                                <label class="control-label" for="textinput">Placa Veiculo</label>  
                                <input id="PLACA" name="Placa" type="text"  class="form-control input-md" >
                            </div>

                        </div> 
                        <div class="row">
                            <div class="form-group col-md-2" id="_CEP">  
                                <label class="control-label" for="textinput">CEP</label>  
                                <input id="CEP" onChange="buscaCEPEndereco();" name="CepAcidente" maxlength="8" type="text"  class="form-control input-md">
                            </div>
                            <div class="form-group col-md-4" id="_Endereco">  
                                <label class="control-label" for="textinput">Endereco</label>  
                                <input id="Endereco" name="RuaAvenida" type="text" onChange="validaLatitude();" class="form-control input-md">
                            </div>
                            <div class="form-group col-md-2" id="_Bairro">  
                                <label class="control-label" for="textinput">Bairro</label>  
                                <input id="Bairro" name="Bairro" type="text"  onChange="validaLatitude();" class="form-control input-md">
                            </div>
                            <div class="form-group col-md-2" id="_Numero">  
                                <label class="control-label" for="textinput">Número</label>  
                                <input id="Numero" name="Numero" type="text"  onChange="validaLatitude();" class="form-control input-md" >
                            </div>
                            <div class="form-group col-md-2" id="_Complemento">  
                                <label class="control-label" for="textinput">Complemento</label>  
                                <input id="Complemento" name="Complemento" type="text"  class="form-control input-md" >
                            </div>
                        </div>    
                        <div class="row">

                            <div class="form-group col-md-3" id="_Complemento">  
                                <label class="control-label" for="textinput">Estado</label>
                                <select id="EstadoAcidente" class="form-control" name="EstadoAcidente" class="form-control "> 
                                    <option value="">Selecione o Estado do acidente</option>
                                </select>
                            </div>
                            <div class="form-group col-md-3" id="_Complemento">  
                                <label class="control-label" for="textinput">Municipio</label>
                                <select id="MunicipioAcidente" class="form-control" name="MunicipioAcidente" onChange="validaLatitude();" class="form-control "> 
                                    <option value="">Selecione o municipio do acidente</option>
                                </select>                                             


                            </div>       
                            <div class="form-group col-md-3" id="_Complemento">  
                                <label class="control-label" for="textinput">Coordenada X</label>
                                <input type="text" class="form-control" name="CoordenadaY" id="CoordX" >
                            </div>
                            <div class="form-group col-md-3" id="_Complemento"> 
                                <label class="control-label" for="textinput">Coordenada Y</label>
                                <input type="text" class="form-control" name="CoordenadaX" id="CoordY" >
                            </div>
                        </div>
                        <BR> 
                        <div class="pull-right">
                            <button type="submit" class="btn btn-default">Salvar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>   
    </div> 
</div> 



<!-- Modal Informações-->
<div class="modal fade" id="informacoes" tabindex="-1" role="dialog" aria-labelledby="informacoesLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="informacoesLabel">Informações sobre o Arquivo da Lista Única</h4>
            </div>
            <div class="modal-body">
                O Arquivo de Upload da Lista Única deve ser em formato EXCEL (.XLS) <BR>
                E deve conter obrigatóriamente os campos:<BR><BR>
                * <B>FONTE DE DADOS </B>(SAMU , CORPO DE BOMBEIROS, POLÍCIA RODOVIÁRIA FEDERAL, POLÍCIA MILITAR, DETRAN, IML, DELEGACIA DE TRÂNSITO, ÓRGÃO MUNICIPAL DE TRÂNSITO, OUTROS, NÃO INFORMADO)<BR>
                * <B>BOLETIM </B>(CÓDIGO NUMÉRICO DO BOLETIM)<BR>
                <B>TIPO ACIDENTE  </B>(ATROPELAMENTO, COLISÃO, CAPOTAMENTO, OUTROS)<BR>
                * <B>DATA DO ACIDENTE  </B>(FORMATO DD/MM/AAAA)<BR>
                <B>HORA DO ACIDENTE  </B>(FORMATO 24 HORAS)<BR>                
                * <B>NOME COMPLETO </B><BR>
                <B>NOME DA MAE </B><BR>
                * <B>DATA DE NASCIMENTO  </B>(FORMATO DD/MM/AAAA)<BR>
                <B>SEXO  </B>(FEMINIMO, MASCULINO, NÃO INFORMADO) <BR>
                <B>CONDICAO DA VITIMA </B> (CONDUTOR, PASSAGEIRO, PEDESTRE, CONDUTOR AUTOMÓVEL, CONDUTOR MOTO, CONDUTOR VEÍCULO PESADO, CONDUTOR ÔNIBUS, CONDUTOR OUTROS, CICLISTA, NÃO INFORMADO)<BR>
                <B>GRAVIDADE DA LESAO  </B>(LEVE, SEM FERIMENTO, GRAVE, MODERADA, FATAL, FATAL LOCAL, FATAL POSTERIOR, NÃO INFORMADO)<BR>
                <B>TIPO VEICULO </B> (AUTOMÓVEL, CARROÇA, MOTOCICLETA, CAMINHÃO, ÔNIBUS/VAN, TRICICLO, PEDESTRE, BICICLETA, OUTROS, NÃO INFORMADO)<BR>
                <B>PLACA </B><BR>
                <B>VELOCIDADDE VIA </B><BR>
                <B>TIPO LOGRADOURO</B><BR>
                <B>ENDERECO DO ACIDENTE </B><BR>
                <B>NUMERO </B><BR>
                <B>LOTE </B><BR>
                <B>QUADRA </B><BR>
                <B>BAIRRO </B><BR>
                <B>COMPLEMENTO </B><BR>
                <B>CIDADE ACIDENTE </B><BR>
                <B>UF ACIDENTE </B><BR>
                <B>COORDENADA X </B><BR>
                <B>COORDENADA Y </B><BR>
                <B>DESCRICAO </B>(DESCRIÇÃO OU RELATO/HISTORICO DA VITIMA SOBRE O ACIDENTE)<BR>
                <BR>
                * CAMPOS OBRIGATÓRIOS
                <BR>
                O NOME DAS VARIÁVEIS NO MODELO DEVE SER EXATAMENTE IGUAL AOS CAMPOS EM NEGRITO (SEM ACENTUAÇÃO OU SÍMBOLOS) <br><br>
                <a href="ListaUnicaEXEMPLO.xls">BAIXE O MODELO</a>
            </div>
            <div class="modal-footer">
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<!--    Datapick    -->
@if (Auth::user()->tipo == 1)
<script language="JavaScript" type="text/javascript" charset="utf-8">
    new dgCidadesEstados({
        cidade: document.getElementById('Cidade'),
        estado: document.getElementById('Estado')
    })
</script>
@endif

<script defer src="//maps.googleapis.com/maps/api/js?key=AIzaSyAZeAE3pKDikUrhlhYm_0g20a-9YkXi4Ic"></script>

<script src="{{ asset('libraries/js/jquery.form.min.js')}}"></script> 
<script language="JavaScript" type="text/javascript" charset="utf-8">
    new dgCidadesEstados({
        cidade: document.getElementById('MunicipioAcidente'),
        estado: document.getElementById('EstadoAcidente')
    })
</script>
<script type="text/javascript">
    //arquivo js lista unica - ultima alteração 22/09/2016
//Guilherme Freire

function limpa_formulário_cep() {
    // Limpa valores do formulário de cep.
    $("#Endereco").val("");
    $("#Bairro").val("");
    //$("#ibge").val("");
}


$('.info').click(function () {
    $('#informacoes').modal('show');
});
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
                alertify.error('Erro ao buscar cep');
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
                console.log("erro get latitude", jqXHR, textStatus, errorThrown);
                $.isLoading('hide');
                alertify.error('Erro ao buscar dados de latitude');
            }
        });
        

    }
}
function cellStyle(value, row, index) {
    //console.log(row)
    if (value === '' || value === null) {
        return {
            classes: 'erro_celula'
        };
    } else if (row.DATA_DO_ACIDENTE != '99/99/9999') {
        return {
            classes: 'erro_data'
        };
    }
    return {};
}
function ApagaDados() {
    alertify.confirm('Você quer apagar a lista única de ' + $('#Cidade').val(), 'Você irá deletar toda a lista única/Quadro multiplo, os arquivos do SIM/SIH e as Linkagens de ' + $('#Cidade').val() + ' \n\
        no Periodo de ' + $('#Trimestre').val() + '/' + $('#Ano').val() + '<BR>Você tem certeza?', function () {
            $("#xlf").val('');
            $('#_sucesso').hide();
            $('#_tabelaResultados').hide();
            $('#_delete').show();

            $.ajax(
            {
                url: '{{ route('deleteDados') }}',
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
                    $("#xlf").val('');
                    $('#_delete').hide();
                    alertify.success('Registros Apagados com Sucesso');
                    console.log(data)
                    $('#desabilitar').show();


                },
                error: function (jqXHR, textStatus, errorThrown)
                {
                    if(jqXHR.status == 419){
                        location.reload();
                    }
                    console.log("erro para apagar");
                    alertify.error('Erro ao apagar');
                }
            });
        }, function () {
            alertify.error('Cancelado')
        }
        );

}

function detailFormatter(index, row) {
    var html = [];
    $.each(row, function (key, value) {
        html.push('<p><b>' + key + ':</b> ' + value + '</p>');
    });
    return html.join('');
}
function isValidDate(s) {
  var bits = s.split('/');
  var d = new Date(bits[2] + '/' + bits[1] + '/' + bits[0]);
  return !!(d && (d.getMonth() + 1) == bits[1] && d.getDate() == Number(bits[0]));
}

$("#nascimento").mask("99/99/9999");
$("#acidente").mask("99/99/9999");

$('#table-2').on('click', 'tr', function () {
    var table = $('#table-2').DataTable();
    var value = table.row( this ).data()

    $('#id').val(value["id"]);
    if (value["FonteDados"] === "" || value["FonteDados"] === null) {
        $('#_Fonte_de_dados').addClass("has-error");
    } else {
        $('#Fonte_de_dados').val(value["FonteDados"]);
    }
    if (value["DataNascimento"] === null || value["DataNascimento"] === '') {
        $('#_nascimento').addClass("has-error");
    } else {
        $('#nascimento').val(value["DataNascimento"]);
    }
    if (value["DataAcidente"] === null || value["DataAcidente"] === '' || value["DataAcidente"] === '99/99/9999') {
        $('#_acidente').addClass("has-error");
    } else {
        $('#acidente').val(value["DataAcidente"]);
    }
    if (value["Sexo"] === "") {
            //$('#_Sexo').addClass("has-error");
        } else {
            $('#Sexo').val(value["Sexo"]);
        }
        if (value["Boletim"] === "" || value["Boletim"] === null) {
            $('#_Boletim').addClass("has-error");
        } else {
            $('#Boletim').val(value["Boletim"]);
        }
        if (value["NomeCompleto"] === "" || value["NomeCompleto"] === null) {
            $('#_Nome_Completo').addClass("has-error");
        } else {
            $('#Nome_Completo').val(value["NomeCompleto"]);
        }
        if (value["CondicaoVitima"] === "" || value["CondicaoVitima"] === null) {
            //$('#_Condicao_vitima').addClass("has-error");
        } else {
            $('#Condicao_vitima').val(value["CondicaoVitima"]);
        }
        if (value["HoraAcidente"] === "") {
            //$('#_Hora').addClass("has-error");
        } else {
            $('#Hora').val(value["HoraAcidente"]);
        }
        if (value["GravidadeLesao"] === "") {
            $('#_Gravidade').addClass("has-error");
        } else {
            $('#Gravidade').val(value["GravidadeLesao"]);
        }
        if (value["TipoVeiculo"] === "") {
            //$('#_Tipo_Veiculo').addClass("has-error");
        } else {
            $('#Tipo_Veiculo').val(value["TipoVeiculo"]);
        }
        if (value["NomeMae"] === "") {
            //$('#_Nome_da_mae').addClass("has-error");
        } else {
            $('#Nome_da_mae').val(value["NomeMae"]);
        }
        if (value["RuaAvenida"] === "") {
            //$('#_Endereco').addClass("has-error");
        } else {
            $('#Endereco').val(value["RuaAvenida"]).trigger('change');
        }


        $('#CEP').val(value["CepAcidente"]);
        $('#Placa').val(value["Placa"]);
        $('#Bairro').val(value["Bairro"]);
        $('#Numero').val(value["Numero"]);
        $('#Complemento').val(value["Complemento"]);
        $('#EstadoAcidente').val(value["EstadoAcidente"]).trigger('change');
        $('#MunicipioAcidente').val(value["CidadeAcidente"]);

        $('#acidente').datepicker({
            language: "pt-BR",
            autoclose: true,
            format: 'dd/mm/yyyy',
            todayHighlight: true,
            endDate:  new Date()
        })

        $("#myModal").modal();
        $("#myModal").on('hidden.bs.modal', function (e) {
            //console.log(e)
            $("#ajaxform").find('.has-error').removeClass('has-error');
            $("#ajaxform").trigger('reset');
        });

    } );
$('#table').bootstrapTable({
    onClickRow: function (value, row, index) {


    }

});

$('#table-2').DataTable({
    processing: true,
    serverSide: true,
    responsive: false,
    ajax: {
        type: 'POST',
        dataType: "json",
        url: "{{ route('dataPendencias') }}",
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
{ data: 'FonteDados', name: 'FonteDados' },
{ data: 'Boletim', name: 'Boletim' },
{ data: 'NomeCompleto', name: 'NomeCompleto' },
{ data: 'NomeMae', name: 'NomeMae' },
{ data: 'Sexo' },
{ data: 'DataNascimento' },
{ data: 'DataAcidente' },
{ data: 'CondicaoVitima' },
{ data: 'HoraAcidente' },
{ data: 'GravidadeLesao' },
{ data: 'TipoVeiculo' },
]
});

$("#ajaxform").submit(function (e)
{
    if($("#Nome_Completo").val() == '' || $("#acidente").val() == '' || $("#acidente").val() == '99/99/9999' || !isValidDate($("#acidente").val())){
       alertify.alert('Erro', 'Insira os valores obrigatórios<br>');
       e.preventDefault();
       return null;
   }
   var postData = $(this).serialize();
   $.ajax(
   {
    url: '{{route('gravaLista')}}',
    type: "POST",
    data: postData,
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    },
    success: function (data, textStatus, jqXHR)
    {
        var table = $('#table-2').DataTable();
        table.ajax.reload();
        if(data.success){
            console.log(table.data().length, $('#table-2').DataTable().data().length )
            if($('#table-2').DataTable().data().length == 1){
                $('#desabilitar').hide();
                $('#_tabelaResultados').hide();
                $('#_delete').hide();
                $('#_sucesso').show();
            }
            $('#myModal').modal('toggle');
        }else{
            alertify.alert('Error', data.mensagem);
        }

    },
    error: function (jqXHR, textStatus, errorThrown)
    {
        if(jqXHR.status == 419){
            location.reload();
        }
        console.log("erro");
        alertify.alert('Error', "Erro ao salvar");
    }
});
    e.preventDefault(); //STOP default action
});


$('.loadData').change(function () {
    @if(Auth::user()->tipo < 3)
    if($('#CodCidade').val() == ''){
        return false;
    }
    @endif
    if ($('#Ano').val() < 2015) {
        $('#Ano').val('');
        $('#Ano').focus();
    } else {
        if ($('#Ano').val() !== '' && $('#Trimestre').val() !== '' ){
            $('#loading-image').hide();
            $('.linkListaUnica').attr("href", "{{ route('listaUnica') }}?Ano="+$('#Ano').val()+"&Trimestre="+$('#Trimestre').val());
            $('.linkQuadroMultiplo').attr("href", "{{ route('quadroMultiplo') }}?Ano="+$('#Ano').val()+"&Trimestre="+$('#Trimestre').val());
            $('.linkSim').attr("href", "{{route('sim')}}?Ano="+$('#Ano').val()+"&Trimestre="+$('#Trimestre').val());
            $('.linkSih').attr("href", "{{route('sih')}}?Ano="+$('#Ano').val()+"&Trimestre="+$('#Trimestre').val());
            $('.linkParesSim').attr("href", "{{ route('sim.pares') }}?Ano="+$('#Ano').val()+"&Trimestre="+$('#Trimestre').val());
            $('.linkParesSih').attr("href", "{{ route('sih.pares') }}?Ano="+$('#Ano').val()+"&Trimestre="+$('#Trimestre').val());
            $.ajax(
            {
                url: '{{route('checkListaUnica')}}',
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
                    $("#CodCidadeGrande").val($("#CodCidade").val());
                    $("#TrimestreGrande").val($("#Trimestre").val());

                    if (data.sih > 0 ) {
                        $('.linkSih').hide();
                    }else{
                        $('.linkSih').show();
                    }
                    if (data.sim > 0 ) {
                        $('.linkSim').hide();
                    }else{
                        $('.linkSim').show();
                    }
                    if (data.linkagem_sih > 0 ) {
                        $('.linkParesSih').show();
                    }else{
                        $('.linkParesSih').hide();
                    }
                    if (data.linkagem_sim > 0 ) {
                        $('.linkParesSim').show();
                    }else{
                        $('.linkParesSim').hide();
                    }
                    if (data.processo > 0 ) {
                        $('#_sucesso').hide();
                        $('#desabilitar').hide();
                        $('#_delete').hide();
                        $('#_tabelaResultados').hide();
                        $('#_processo').show();
                    }
                    else if (data.pendencias > 0 ) {
                        $('#_sucesso').hide();
                        $('#_processo').hide();
                        $('#desabilitar').hide();
                        $('#_delete').hide();
                        $('#table-2').DataTable().ajax.reload();
                        $('#_tabelaResultados').show();
                    } else if (data.lista == 0 ) {
                        $('#_sucesso').hide();
                        $('#desabilitar').show();
                        $('#_processo').hide();
                        $('#_delete').hide();
                        $('#_tabelaResultados').hide();
                    } else {
                        $('#desabilitar').hide();
                        $('#_processo').hide();
                        $('#_delete').hide();
                        $('#_sucesso').show();
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

});


function enviaGrande(){
    //arquivo grande, eviando o excel
    alertify.alert('Aviso', 'Sua Lista Única será processada pelo servidor em segundo plano, quando terminada será enviado uma notificação')
    .set({onshow: null, onclose: function () {
        $('#AnoGrande').val($('#Ano').val());
        $("#CodCidadeGrande").val($("#CodCidade").val());
        $('#TrimestreGrande').val($('#Trimestre').val());
        var progressbox = $('#prog');
        var progressbar = $('#textProgress');
        var statustxt = $('#statustxt');
        var completed = '0%';
        var output;
        var options = {
            target: output, 
            beforeSubmit: beforeSubmit,
            uploadProgress: OnProgress,
            success: afterSuccess, 
            resetForm: false      
        };
        $("#sendFile").ajaxSubmit(options);
        function OnProgress(event, position, total, percentComplete)
        {
            $("#prog").css('width',percentComplete.toFixed(2) + '%')
            $("#textProgress").html(percentComplete.toFixed(2) + '%');
            statustxt.html(percentComplete + '%'); 
            if (percentComplete == 100) {
                progressbox.show();
                statustxt.css('left', '46%'); 
                statustxt.css('color', '#000'); 
            }
        }

        function afterSuccess(output)
        {
            console.log(output)
        }
        function bytesToSize(bytes) {
            var sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB'];
            if (bytes == 0)
                return '0 Bytes';
            var i = parseInt(Math.floor(Math.log(bytes) / Math.log(1024)));
            return Math.round(bytes / Math.pow(1024, i), 2) + ' ' + sizes[i];
        }

        function beforeSubmit(e) {

            //check whether browser fully supports all File API
            if (window.File && window.FileReader && window.FileList && window.Blob)
            {

            if (!$('#xlf').val()) //check empty input filed
            {
                //$("#output").html("Por Favor Insira um Arquivo");
                $("#msg").html("Por favor insira um arquivo");
                $('#myModal').modal('show')
                return false
            }

            var fsize = $('#xlf')[0].files[0].size; //get file size
            var ftype = $('#xlf')[0].files[0].name; // get file type

            //Allowed file size is less than 20 MB (2048576) //arquvio phpinfo 20mb
            if (fsize > 20971520)
            {
                //$("#output").html("<b>" + bytesToSize(fsize) + "</b> Arquivo muito grande! <br />Tente compacta-lo com o winrar ou winzip");
                $("#msg").html("<b> Arquivo maior que 20MB </b>");
                $('#myModal').modal('show')
                return false
            }


            //Progress bar
            progressbox.show(); //show progressbar
            progressbar.width(completed); //initial value 0% of progressbar
            statustxt.html(completed); //set status text
            statustxt.css('color', '#000'); //initial color of status text


            $("#output").html("");
        }else{
            //Output error to older unsupported browsers that doesn't support HTML5 File API
            $("#output").html("Please upgrade your browser, because your current browser lacks some new features we need!");
            return false;
        }
    }


}});

}


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
        $("#CodCidade").val($(this).val());
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
                $('#Ano').trigger('change')
                $('#CodCidade').trigger('change')
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

});
</script>

@endsection