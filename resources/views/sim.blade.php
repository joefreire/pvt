@extends('layouts.app')
@section('styles')

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
        <h3>Envio do SIM</h3>
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

        <h3 class="control-label" for="arquivos" style="text-align: left;">Selecione o arquivo do SIM </h3>
        <form name="sendFile" class="form-inline" method="POST" enctype="multipart/form-data" action="{{route('gravaSim')}}">
            <p></p>
            <div class="form-group">
                <label for="email">Arquivo Sim:</label>
                <input type="file" class="form-control" name="arquivo" id="arquivo" /> 
            </div>
            @csrf
            <input type="hidden" name="Ano" id="AnoGrande">
            <input type="hidden" name="Trimestre" id="TrimestreGrande">
            <input type="hidden" name="CodCidade" id="CodCidadeGrande">
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
    <div id="_processo" style="display:none;">   
        <span>Sim em processamento</span> <BR>
        <span>Aguarde</span> <BR><BR>
    </div>
    <div id="_ListaUnica" style="display:none;">
        <span>Primeiro você deve fazer o upload da  <a href="{{route('sim')}}" class="linkListaUnica">Lista Única</a></span>
    </div>
    <div id="Linkagem" style="display:none;">
        <span>Agora você  pode verificar os <a href="{{route('sim.pares')}}" class="linkParesSim"> pares verdadeiros e falsos </a></span><BR><BR>
        <span>Para fazer novamente o envio do SIM e Linkagem do período <a href="#" onclick="ApagaDados();">Clique Aqui</a></span>
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
                <h4 class="modal-title" id="informacoesLabel">Informações sobre o Arquivo do SIM</h4>
            </div>
            <div class="modal-body">
                O Arquivo de Upload do SIM deve ser o DBF <BR><BR>

                *NUMERODO<BR>
                NUMERODV<BR>
                CODESTCART<BR>
                CODMUNCART<BR>
                CODCART<BR>
                NUMREGCART<BR>
                DTREGCART<BR>
                TIPOBITO<BR>
                *DTOBITO<BR>
                HORAOBITO<BR>
                NUMSUS<BR>
                NATURAL<BR>
                CODMUNNATU<BR>
                *NOME<BR>
                NOMEPAI<BR>
                NOMEMAE<BR>
                *DTNASC<BR>
                IDADE<BR>
                SEXO<BR>
                RACACOR<BR>
                ESTCIV<BR>
                ESC<BR>
                OCUP<BR>
                CODESTRES<BR>
                CODMUNRES<BR>
                BAIRES<BR>
                CODBAIRES<BR>
                CODENDRES<BR>
                ENDRES<BR>
                CODREGRES<BR>
                NUMRES<BR>
                COMPLRES<BR>
                CEPRES<BR>
                CODDISRES<BR>
                CODPAISRES<BR>
                LOCOCOR<BR>
                CODMUNOCOR<BR>
                BAIOCOR<BR>                
                ENDOCOR<BR>
                NUMENDOCOR<BR>
                COMPLOCOR<BR>
                CEPOCOR<BR>
                CODDISOCOR<BR>
                NUMERODN<BR>
                ASSISTMED<BR>
                EXAME<BR>
                CIRURGIA<BR>
                NECROPSIA<BR>
                LINHAA<BR>
                LINHAB<BR>
                LINHAC<BR>
                LINHAD<BR>
                LINHAII<BR>
                DSTEMPO<BR>
                CAUSABAS<BR>
                DSEXPLICA<BR>
                MEDICO<BR>
                CRM<BR>
                CONTATO<BR>
                DTATESTADO<BR>
                CIRCOBITO<BR>
                ACIDTRAB<BR>
                FONTE<BR>
                DSEVENTO<BR>
                ENDACID<BR>
                NUMEROLOTE<BR>
                TPPOS<BR>
                DTINVESTIG<BR>
                LINHAA_O<BR>
                LINHAB_O<BR>
                LINHAC_O<BR>
                LINHAD_O<BR>
                LINHAII_O<BR>
                CAUSABAS_O<BR>
                DTCADASTRO<BR>
                ATESTANTE<BR>
                
                CRITICA<BR>
                <BR>
                <BR>
                * CAMPOS OBRIGATÓRIOS<BR>
                <!--                <a href="libraries/exemplo_sim.csv">Baixe o Modelo se Necessário</a>-->
            </div>
            <div class="modal-footer">
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
<script type="text/javascript">

    $('.info').click(function () {
        $('#informacoes').modal('show');
    });

    function ApagaDados() {
        alertify.confirm('Você quer apagar o sim', 'Você irá deletar toda o SIM e a Linkagem de ' + ' \n\
            no Periodo de ' + $('#Trimestre').val() + '/' + $('#Ano').val() + '<BR>Você tem certeza?', function () {
                $("#xlf").val('');
                $('#_sucesso').hide();
                $('#_tabelaResultados').hide();
                $('#_delete').show();

                $.ajax(
                {
                    url: '{{ route('sim.deleteDados') }}',
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
                        $('#Linkagem').hide();
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
                    data: {Ano: $('#Ano').val(), Trimestre: $('#Trimestre').val(), CodCidade: $('#CodCidade').val()},
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