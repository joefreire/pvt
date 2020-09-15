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
        <h3>Relatório de indicadores finais</h3>
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
            <button id="gerar" class="btn btn-default">Gerar</button>
        </div>  
        <BR>

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
    <div id ="exportar" >        
        <BR>
        <div id="tabela" class="container row">

            <!-- Text input-->
            <div class="col-md-3 col-xs-12">
                <label class="control-label" for="Populacao">População</label>
                <div class="controls">
                    <input id="Populacao" name="Populacao" value="1000000" type="number"  min='0' placeholder="" class="form-control" required="">
                    <p class="help-block">População IBGE <a target="_blank" href="http://www.ibge.gov.br/home/estatistica/populacao/estimativa2016/estimativa_tcu.shtm"><img class="info" src=" {{ asset('libraries/img/iconinfo.png') }}" alt="Informações"/></p>
                    </a>
                </div>
                <!-- Text input-->
                <label class="control-label" for="Frota">Frota de Veículos</label>
                <div class="controls">
                    <input id="Frota" name="Frota" type="number" value="10000" min='0' placeholder="" class="form-control" required="">
                    <p class="help-block">Frota de Veículos <a target="_blank" href="http://www.denatran.gov.br/index.php/estatistica/237-frota-veiculos"><img class="info" src=" {{ asset('libraries/img/iconinfo.png') }}" alt="Informações"/></p></a>
                </div>



            </div>
            <div class=" col-md-8 col-xs-12">
                <div class="panel panel-default">
                    <div class="panel-heading">Indicadores</div>
                    <div class="panel-body">
                        <div id="sem_dados_linkagem" class="col-md-6" style="display: none;"> <h2> sem dados da linkagem para exibir   </h2></div>
                        <div id="sem_dados" class="col-md-6" style="display: none;"> <h2> sem dados para exibir   </h2></div>
                        <div>
                            <table id="tableLinkagem" class="col-md-6 table table-responsive" style="display: none;  width: 48%; margin-right: 2%;">
                                <thead>
                                    <tr>
                                        <th>Indicadores da Linkagem</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>Óbitos 30 dias no trimestre</td>

                                        <td id="obitos_absoluto_linkagem" align="right"></td>
                                    </tr>
                                    <tr>
                                        <td>&rarr;Residentes no munic&iacute;pio</td>

                                        <td id="obitos_linkagem_residentes" align="right"></td>
                                    </tr>
                                    <tr>
                                        <td>&rarr;N&atilde;o Residentes</td>

                                        <td id="obitos_linkagem_ocorridos" align="right"></td>
                                    </tr>
                                    <tr>
                                        <td>Taxa mortalidade</td>

                                        <td id="taxa_mortalidade_linkagem" align="right"></td>
                                    </tr>                                   
                                    <tr>
                                        <td>&rarr;Taxa mortalidade Residentes</td>
                                        <td id="taxa_mortalidade_linkagem_residentes" align="right"></td>
                                    </tr>
                                    <tr>
                                        <td>Razão mortalidade</td>
                                        <td id="razao_mortalidade_linkagem" align="right"></td>
                                    </tr>

                                    <tr>
                                        <td>&rarr;Razão mortalidade Residentes</td>
                                        <td id="razao_mortalidade_linkagem_residentes" align="right"></td>
                                    </tr>
                                    <tr>
                                        <td>número de Óbitos atribuidos ao fator álcool</td>
                                        <td id="fator_alcool" align="right"></td>
                                    </tr>
                                    <tr>
                                        <td>% de Óbitos atribuidos ao fator álcool</td>
                                        <td id="percent_fator_alcool" align="right"></td>
                                    </tr>
                                    <tr>
                                        <td>número de Óbitos atribuidos ao fator velocidade</td>
                                        <td id="fator_velocidade" align="right"></td>
                                    </tr>
                                    <tr>
                                        <td>% de Óbitos atribuidos ao fator velocidade</td>
                                        <td id="percent_fator_velocidade" align="right"></td>
                                    </tr>
                                </tbody>
                            </table> 
                            <table id="tableIndicadoresGerais" class="col-md-6 table table-responsive" style="display: none;  width: 48%;">
                                <thead>
                                    <tr>
                                        <th>Indicadores Gerais</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>Total de óbitos por ATT</td>                                
                                        <td id="obitos_ocorridos" align="right"></td>
                                    </tr>
                                    <tr>
                                        <td>Total de óbitos de Residentes no munic&iacute;pio</td><td id="obitos_residentes" align="right"></td>
                                    </tr>
                                    <tr>
                                        <td>Taxa de mortalidade</td><td id="taxa_mortalidade" align="right"></td>
                                    </tr>


                                    <tr>
                                        <td>Taxa mortalidade residentes</td>
                                        <td id="taxa_total_obitos" align="right"></td>
                                    </tr>


                                    <tr>
                                        <td>Razão mortalidade</td>
                                        <td id="razao_mortalidade" align="right"></td>
                                    </tr>

                                </tbody>

                            </table>
                        </div>

                    </div>
                    <div class="panel-footer">*Taxa por 100 mil hab. e Razão por 10 mil veículos</div>

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
<script>
    $(document).ready(function () {
        $('[data-toggle="tooltip"]').tooltip();
    });

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


            $.ajax(
            {
                url: '{{ route('resultado.indicadores.data') }}',
                type: "POST",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {ACAO: 'busca', Ano: $('#Ano').val(), Trimestre: $('#Trimestre').val(), CodCidade: $("#CodCidade").val(), Cidade: $("#Cidade").val()},
                success: function (data, textStatus, jqXHR)
                {
                    console.log(data)
                    if(data.obitos_absoluto_linkagem > '0'){

                        $("#obitos_absoluto_linkagem").html(data.obitos_absoluto_linkagem);
                        $("#obitos_linkagem_residentes").html(data.obitos_linkagem_residentes);
                        $("#obitos_linkagem_ocorridos").html(data.obitos_linkagem_ocorridos);
                        $("#taxa_mortalidade_linkagem").html(( (parseFloat(data.obitos_absoluto_linkagem) / parseFloat($("#Populacao").val()) )* 100000).toFixed(2));
                        $("#taxa_mortalidade_linkagem_residentes").html(( (parseFloat(data.obitos_linkagem_residentes) / parseFloat($("#Populacao").val()) )* 100000).toFixed(2));
                        $("#razao_mortalidade_linkagem").html(((parseFloat(data.obitos_absoluto_linkagem) / parseFloat($("#Frota").val())) * 10000).toFixed(2));
                        $("#razao_mortalidade_linkagem_residentes").html(((parseFloat(data.obitos_linkagem_residentes) / parseFloat($("#Frota").val())) * 10000).toFixed(2));
                        $("#fator_alcool").html(data.fator_alcool);
                        $("#percent_fator_alcool").html((parseFloat(data.fator_alcool)*100/parseFloat(data.obitos_absoluto_linkagem)).toFixed(2) );
                        $("#fator_velocidade").html(data.fator_velocidade);
                        $("#percent_fator_velocidade").html((parseFloat(data.fator_velocidade)*100/parseFloat(data.obitos_absoluto_linkagem)).toFixed(2) );


                        $("#obitos_ocorridos").html(data.obitos_ocorridos);
                        $("#obitos_residentes").html(data.obitos_residentes);
                        $("#taxa_mortalidade").html(( (parseFloat(data.obitos_ocorridos) / parseFloat($("#Populacao").val()) )* 100000).toFixed(2));
                        $("#taxa_total_obitos").html(( (parseFloat(data.obitos_residentes) / parseFloat($("#Populacao").val()) )* 100000).toFixed(2));
                        $("#razao_mortalidade").html(((parseFloat(data.obitos_ocorridos) / parseFloat($("#Frota").val())) * 10000).toFixed(2));


                        $("#sem_dados").hide();
                        $("#sem_dados_linkagem").hide(); 
                        $("#tableLinkagem").show();
                        $("#tableIndicadoresGerais").show(); 

                    }else{
                        if(data.obitos_ocorridos == '0'){              
                            $("#sem_dados").show(); 
                            $("#tableLinkagem").hide(); 
                            $("#tableIndicadoresGerais").hide(); 
                        }else{
                            $("#obitos_ocorridos").html(data.obitos_ocorridos);
                            $("#obitos_residentes").html(data.obitos_residentes);
                            $("#taxa_mortalidade").html(( (parseFloat(data.obitos_ocorridos) / parseFloat($("#Populacao").val()) )* 100000).toFixed(2));
                            $("#razao_mortalidade").html(((parseFloat(data.obitos_ocorridos) / parseFloat($("#Frota").val())) * 10000).toFixed(2));
                            $("#sem_dados_linkagem").show(); 
                            $("#tableLinkagem").hide(); 
                            $("#tableIndicadoresGerais").show(); 
                        }
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
function demoPDF() {
    var pdfsize = 'a0';
    var pdf = new jsPDF('l', 'pt', pdfsize);

    var res = pdf.autoTableHtmlToJson(document.getElementById("table_resultados"));
    pdf.autoTable(res.columns, res.data, {
        startY: 60,
        styles: {
            overflow: 'linebreak',
            fontSize: 50,
            rowHeight: 60,
            columnWidth: 'wrap'
        },
        columnStyles: {
            1: {columnWidth: 'auto'}
        }
    });

    pdf.save(pdfsize + ".pdf");
}
;
$('#button').click(function () {
    demoPDF()

});


});

</script>
@endsection