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
<style>
    thead.table-head {
        background-color: white;
        color: #2184BE;
    }

    .loading {
        background: url({{asset('libraries/img/loading.gif')}}) no-repeat 50% 50%;
        -webkit-transition: background-color 0;
        transition: background-color 0;
        min-height: 250px;
    }

</style>
@endsection
@section('content')

<div class="col-md-12">  
    <div class="page-header">
        <h3>Relatório Geral</h3>
    </div>
    <form id="relatorio_acidentes" method="post">   
        @csrf
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

            </div>{{-- 
            <img class="info" src=" {{ asset('libraries/img/iconinfo.png') }}" alt="Informações"/> --}}

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
            <input type="hidden" id="CodCidade" class='loadData' name="CodCidade" value="{{ Auth::user()->codcidade }}">
            @endif
        </div>  
        <BR>
        <div class="form-group">
            <div class="form-inline">
                <label class="control-label" for="base" style="text-align: left;">Base de Dados :  </label>
                <select name="base" id="base" class="form-control" required="">
                    <option value=""></option>
                    <option value="Acidentes">Acidentes</option>
                    <option value="Vitimas">Vítimas</option>
                    <option value="SIM">Óbitos</option>                               
                    <option value="SIH">Feridos</option>
                    <option value="PARES_SIM">Pares SIM</option>
                    <option value="PARES_SIH">Pares SIH</option>



                </select>
                <label class="control-label" for="linha" style="text-align: left;">Linhas :  </label>
                <select name="linhas" id="linhas" class="form-control" required="">
                    <option value="">Selecione a Base</option>



                </select>
                <label class="control-label" for="colunas" style="text-align: left;">Colunas :  </label>
                <select name="colunas" id="colunas" class="form-control" required="">
                    <option value="">Selecione a Linha</option>



                </select>
                <span>&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp</span> 
                <button id="gerar" class="btn btn-default">Gerar</button>
            </div>  
        </div> 
    </BR>
</form>
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
    <button id="button" class="btn btn-default" type="button" style="display: none;"  onClick ="$('#table_resultados').tableExport({type: 'excel', escape: 'false'});" >Exportar Dados</button>
    <div id ="exportar" >        
        <BR>
        <div id="tableResults" class="container table-responsive">


        </div>
        <BR><BR>
        <div id="chart" style="min-width: 310px; max-width: 800px; height: 400px; margin: 0 auto">

        </div>
    </div>
    <div id="sem_dados" style="display: none;">
     <h2> sem dados para exibir   </h2>
 </div>
 <BR><BR>
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
<script src="https://code.highcharts.com/highcharts.js"></script>
<script src="https://code.highcharts.com/modules/exporting.js"></script>
<script src="https://code.highcharts.com/modules/data.js"></script>
<script src="https://code.highcharts.com/modules/drilldown.js"></script>
<script src="https://cdn.datatables.net/1.10.15/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.10.15/js/dataTables.bootstrap.min.js"></script>
<script type="text/javascript" src="{{asset('libraries/js/tableExport_HTML.j')}}s"></script> 
<script type="text/javascript" src="{{asset('libraries/js/jquery.base64.js')}}"></script> 
<script src="{{asset('libraries/js/FileSaver.min.js')}}"></script> 
<script src="{{asset('libraries/js/html2canvas.min.js')}}"></script>
<script src="{{asset('libraries/js/jspdf.debug.js')}}"></script>
<script src="{{asset('libraries/js/jspdf.plugin.autotable.js')}}"></script>
<script>
    var chart;

    $('#base').change(function () {
        if ($('#base').val() == 'Vitimas') {
            $('#linhas').html('<option value="Gravidade">Gravidade</option>\n\
                <option value="Sexo">Sexo</option>\n\
                <option value="FaixaEtaria">Faixa Etária</option>\n\
                <option value="MeioTransporte">Meio de Transporte</option>\n\
                <option value="Condicao">Condição da Vítima</option>\n\
                <option value="MunicipioRes">Múnicipio de Residência</option>\n\
            ');
            $('#colunas').html('<option value="Frequencia">[...]</option>\n\
                <option value="Gravidade">Gravidade</option>\n\
                <option value="Sexo">Sexo</option>\n\
                <option value="FaixaEtaria">Faixa Etária</option>\n\
                <option value="Dia">Dia do Acidente</option>\n\
                <option value="MeioTransporte">Meio de Transporte</option>\n\
                <option value="Condicao">Condição da Vítima</option>\n\
                ');

        } else if (($('#base').val() == 'SIM')) {
            $('#linhas').html('<option value="Sexo">Sexo</option>\n\
               <option value="FaixaEtaria">Faixa Etária</option>\n\
               ');
            $('#colunas').html('<option value="Frequencia">[...]</option>\n\
               ');
        } else if (($('#base').val() == 'SIH')) {
            $('#linhas').html('<option value="Sexo">Sexo</option>\n\
               <option value="FaixaEtaria">Faixa Etária</option>\n\
               ');
            $('#colunas').html('<option value="Frequencia">[...]</option>\n\
               ');
        } else if (($('#base').val() == 'PARES_SIM') || ($('#base').val() == 'PARES_SIH')) {
            $('#linhas').html('<option value="Gravidade">Gravidade</option>\n\
                <option value="Sexo">Sexo</option>\n\
                <option value="TipoAcidente">Tipo de Acidente</option>\n\
                <option value="FaixaEtaria">Faixa Etária</option>\n\
                <option value="MeioTransporte">Meio de Transporte</option>\n\
                <option value="Condicao">Condição da Vítima</option>\n\
                <option value="FonteDados">Fonte de Dados</option>\n\
                <option value="MunicipioRes">Múnicipio de Residência</option>\n\
                <option value="CausaBase">Causa Base</option>\n\
                <option value="QtdVitimas">Qtd Vítimas</option>\n\
                <option value="DiaSemana">Dia da Semana</option>\n\
                <option value="Horario">Horário do Acidente</option>\n\
                ');
            $('#colunas').html('<option value="Frequencia">[...]</option>\n\
                <option value="Gravidade">Gravidade</option>\n\
                <option value="Sexo">Sexo</option>\n\
                <option value="TipoAcidente">Tipo de Acidente</option>\n\
                <option value="FaixaEtaria">Faixa Etária</option>\n\
                <option value="FonteDados">Fonte dos Dados</option>\n\
                <option value="Horario">Horário do Acidente</option>\n\
                <option value="Dia">Dia do Acidente</option>\n\
                <option value="MeioTransporte">Meio de Transporte</option>\n\
                <option value="Condicao">Condição da Vítima</option>\n\
                <option value="FatorRisco">Soma dos Pesos dos Fatores de Risco do Acidente</option>\n\
                <option value="FrequenciaFatorRisco">Frequência dos Fatores de Risco do Acidente</option>\n\
                <option value="CondutaRisco">Soma dos Pesos das Condutas de Risco dos Acidente</option>\n\
                <option value="FrequenciaCondutaRisco">Frequência das Condutas de Risco do Acidente</option>\n\
                <option value="ProtecaoInadequada">Soma dos Pesos dos Fatores de Risco - Proteção Inadequada</option>\n\
                <option value="FrequenciaProtecaoInadequada">Frequência dos Fatores de Risco - Proteção Inadequada</option>\n\
                <option value="UsuarioContributivo">Usuário Contributivo</option>\n\
                ');
        } else if (($('#base').val() == 'Acidentes')) {
            $('#linhas').html('<option value="TipoAcidente">Tipo de Acidente</option>\n\
                <option value="FonteDados">Fonte de Dados</option>\n\
                <option value="QtdVitimas">Qtd Vítimas</option>\n\
                <option value="DiaSemana">Dia da Semana</option>\n\
                <option value="Horario">Horário do Acidente</option>\n\
                ');
            $('#colunas').html('<option value="Frequencia">[...]</option><option value="TipoAcidente">Tipo de Acidente</option>\n\
                <option value="FonteDados">Fonte de Dados</option>\n\
                <option value="FatorRisco">Soma dos Pesos Fatores de Risco do Acidente</option>\n\
                <option value="FrequenciaFatorRisco">Frequência dos Fatores de Risco do Acidente</option>\n\
                <option value="CondutaRisco">Soma dos Pesos das Condutas de Risco dos Acidente</option>\n\
                <option value="FrequenciaCondutaRisco">Frequência das Condutas de Risco do Acidente</option>\n\
                <option value="ProtecaoInadequada">Soma dos Pesos dos Fatores de Risco - Proteção Inadequada</option>\n\
                <option value="FrequenciaProtecaoInadequada">Frequência dos Fatores de Risco - Proteção Inadequada</option>\n\
                <option value="UsuarioContributivo">Usuário Contributivo</option>\n\
                ');
        }
    });

function frequencia() {
    if ($("#colunas").val() != 'Frequencia') {
        var indexs = new Array();
        $('table#table_resultados thead tr th.nomes').each(function () {
            if ($(this).html() != 'Total') {
                        var coluna = $(this); // Salvo a coluna atual
                        indexs.push(coluna.index()); //Salvo o Index
                        coluna.after('<th>% do Total</th>'); // Insiro a nova coluna
                    }
                });
                //console.log(indexs)
                $('table#table_resultados tbody tr').each(function () {
                    //console.log(this)
                    var coluna = $(this); //Salvo a coluna
                    var total = parseFloat(coluna.find('td').last().html()); // Salvo o valor total da linha
                    $(indexs).each(function (key, value) { // Percorro os index's salvos
                        var row = coluna.find('td').eq(value); // Salvo a linha 
                        var valor = parseFloat(row.html()); // Salvo o valor total da linha
                        if (valor != 0 || valor != '') {
                            var procentagem = (valor / total) * 100; // Verifico a porgentagem
                        } else {
                            var procentagem = 0;
                        }
                        row.after('<td>' + procentagem.toFixed(2) + '</td>'); // Adiciono o valor na linha e na coluna porcentagem
                    });
                });
            //} else if ($("#base").val() == 'SIM' || $("#base").val() == 'SIH') {//frequencia SIM - SIH
            //     var indexs = new Array();
            //     $('table#table_resultados thead tr th.nomes').each(function () {
            //         if ($(this).html() != 'Total') {
            //             var coluna = $(this); // Salvo a coluna atual
            //             indexs.push(coluna.index()); //Salvo o Index
            //             coluna.after('<th>% do Total</th>'); // Insiro a nova coluna
            //         }
            //     });
            //     //console.log(indexs)
            //     $('table#table_resultados tbody tr').each(function () {
            //         //console.log(this)
            //         var coluna = $(this); //Salvo a coluna
            //         var total = parseFloat($("#FREQUÊNCIA").html()); // Salvo o valor total da linha
            //         $(indexs).each(function (key, value) { // Percorro os index's salvos
            //             var row = coluna.find('td').eq(value); // Salvo a linha 
            //             var valor = parseFloat(row.html()); // Salvo o valor total da linha
            //             if (valor != 0 || valor != '') {
            //                 var procentagem = (valor / total) * 100; // Verifico a porgentagem
            //             } else {
            //                 var procentagem = 0;
            //             }
            //             row.after('<td>' + procentagem.toFixed(2) + '</td>'); // Adiciono o valor na linha e na coluna porcentagem
            //         });
            //     });
            } else {//frequencia % do Total
                $('.nomes').after('<th>% do Total</th>'); // Insiro a nova coluna
                var total = parseFloat($('#FREQUENCIA').html());
                $('.FREQUENCIA').each(function () {
                    $(this).after('<td>' + ((parseFloat($(this).html()) / total) * 100).toFixed(2) + '</td>'); // Insiro a nova coluna       
                });
                $('#FREQUENCIA').after('<td>100.00</td>');
            }
        }

        function colSum() {

            var ids = new Array();
            $('#table_resultados .nomes').each(function () {
                console.log(this)

                ids.push($(this).html().replace(/\s/g, ''));
                $('#Totais').append('<td id="' + $(this).html().replace(/\s/g, '') + '"></td>')

            });

            //console.log(ids)
            $.each(ids, function (index, value) {
                var sum = 0;
                $('.' + value.replace(/\s/g, '')).each(function () {
                    console.log($(this).html());
                    sum += parseInt($(this).html());

                });
                $('#' + value.replace(/\s/g, '')).html(sum);
                //console.log('aq'+sum)
                //$('.Totais').after('<td>'+sum+'</td>')

            });

        }
        $('#Cidade').change(function () {
            $.ajax({
                url: '{{ route('getCidades') }}',
                type: "POST",
                data: {
                    Cidade: $('#Cidade').val(), Estado: $('#Estado').val()
                },
                success: function (data, textStatus, jqXHR) {
                    $("#CodCidade").val(data);
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    if(jqXHR.status == 419){
                        location.reload();
                    }
                    console.log("erro");
                }
            });
        });

        $(document).ready(function () {
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
            $("#relatorio_acidentes").submit(function (e) {
                e.preventDefault();
                $("#tableResults").hide();
                $("#chart").hide();
                $("#exportar").addClass('loading');
                $.ajax({
                    url: '{{ route('resultado.geral.data') }}',
                    type: "POST",
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: {
                        ACAO: 'busca',
                        Ano: $('#Ano').val(),
                        base: $('#base').val(),
                        linhas: $('#linhas').val(),
                        colunas: $('#colunas').val(),
                        Trimestre: $('#Trimestre').val(),
                        CodCidade: $("#CodCidade").val()
                    },
                    success: function (data, textStatus, jqXHR) {
                        console.log(data)
                        if(typeof(data.error) != 'undefined'){
                            alertify.error('Erro ao buscar dados')
                            return false;
                        }
                        if(data == 'sem_dados'){
                            $("#sem_dados").show();
                            $("#exportar").removeClass('loading');
                        }else{
                            $("#sem_dados").hide();
                            $("#button").show();
                            $("#tableResults").html(data);
                            colSum();

                            chart = Highcharts.chart('chart', {
                                data: {
                                    table: 'table_resultados'
                                },
                                chart: {
                                    type: 'column',
                                    events: {
                                        load: function () {
                                            this.originalData = this.options.series.map(s => {
                                                return {
                                                    name: s.name,
                                                    data: s.data.slice()
                                                };
                                            });

                                        //console.log(this.originalData);
                                    }
                                }
                            },
                            title: {
                                text: $( "#colunas option:selected" ).text()
                            },
                            yAxis: {
                                allowDecimals: false,
                                title: {
                                    text: ''
                                }
                            },
                            tooltip: {
                                formatter: function () {
                                    return '<b>' + this.series.name + '</b><br/>' +
                                    this.point.y + ' ' + this.point.name;
                                }
                            },
                            credits: {
                                enabled: false
                            }
                        });
                            frequencia();

                            $('#chart').append('<button id="btnMostra" style="display: none;">Mostrar todos os dados</button>');

                            $("#exportar").removeClass('loading');
                            $("#tableResults").show();
                            $("#chart").show();
                        //$('#table_resultados').tablesorter(); 
                        //tableToJson(#tableResults);    
                    }

                },
                error: function (jqXHR, textStatus, errorThrown) {
                    if(jqXHR.status == 419){
                        location.reload();
                    }
                    console.log("erro cidade");
                    alertify.error('Erro ao buscar dados')
                }

            });



            });



});
$(document).on('click', '.GRUPOS', function () {
    $('#btnMostra').show()
    chart.update({
        series: chart.originalData.map(s => {
            return {
                name: s.name,
                data: s.data.filter(p => p[0] === $(this).html())
            }
        })
    });
    $('#btnMostra').on('click', function () {
        $('#btnMostra').hide()
        chart.update({
            series: chart.originalData.map(s => {
                return {
                    name: s.name,
                    data: s.data.slice()
                }
            })
        });

    });
});


</script>
@endsection