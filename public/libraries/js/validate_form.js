var recuperado_implantacao = false;
var recuperado_qualidade = false;
var recuperado_analise = false;
var recuperado_acoes = false;
var recuperado_monitoramento = false;
var recuperado = false;
var atual = 0;

function verRelatorioCompleto(){
    console.log(atual)
    if(atual < 6){
        alert('Dados ainda não preenchidos')
    }else{
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

}

function atualiza(valor){
    atual = valor;
}
function recuperar_coordenadores() {
    $.ajax({
        url: 'coordenadores.php',
        data: {acao: "recuperar", Cidade: $("#CodCidade").val(), Ano: $("#Ano").val()},
        type: 'POST',
        success: function (output) {

            if (output != 'vazio') {
                var Vals = JSON.parse(output);

                if (Vals[0].coordenaTEM == '1'){
                    $('input:radio[name=coordenaTEM][value=SIM]').attr('checked', true);
                    $('input:radio[name=coordenaTEM][value=SIM]').click();
                } else {
                    $('input:radio[name=coordenaTEM][value=NAO]').attr('checked', true);
                    $('input:radio[name=coordenaTEM][value=NAO]').click();
                }
                $('input:radio[name=coordenaTEM]').prop("disabled", true);
                $("#COORDENADOR1").val(Vals[0].Nome);
                $("#COORDENADOR1").prop("disabled", true);
                $("#INSTITUICAO1").val(Vals[0].Instiuicao);
                $("#INSTITUICAO1").prop("disabled", true);
                $("#EMAIL1").val(Vals[0].Email);
                $("#EMAIL1").prop("disabled", true);
                $("#TEL1").val(Vals[0].Telefone);
                $("#TEL1").prop("disabled", true);
                $("#TEL1-2").val(Vals[0].Telefone1);
                $("#TEL1-2").prop("disabled", true);

                $("#COORDENADOR2").val(Vals[0].Coordenador2);
                $("#COORDENADOR2").prop("disabled", true);
                $("#INSTITUICAO2").val(Vals[0].Instituicao2);
                $("#INSTITUICAO2").prop("disabled", true);
                $("#EMAIL2").val(Vals[0].Email2);
                $("#EMAIL2").prop("disabled", true);
                $("#TEL2").val(Vals[0].Telefone2);
                $("#TEL2").prop("disabled", true);
                $("#TEL2-2").val(Vals[0].Telefone2_2);
                $("#TEL2-2").prop("disabled", true);

                $("#COORDENADOR3").val(Vals[0].Coordenador3);
                $("#COORDENADOR3").prop("disabled", true);
                $("#INSTITUICAO3").val(Vals[0].Instituicao3);
                $("#INSTITUICAO3").prop("disabled", true);
                $("#EMAIL3").val(Vals[0].Email3);
                $("#EMAIL3").prop("disabled", true);
                $("#TEL3").val(Vals[0].Telefone3);
                $("#TEL3").prop("disabled", true);
                $("#TEL3-2").val(Vals[0].Telefone3_2);
                $("#TEL3-2").prop("disabled", true);
                //botao editar
                if ($('#btn_editar_coordenadores').length == 0) {
                    var d = Vals[0].data_save,
                    DataHora = d.split(' '),
                    dArr = DataHora[0].split('-'),
                    dataFormatada = dArr[2] + "/" + dArr[1] + "/" + dArr[0];
                    $('#COORDENADORES').prepend('<div id="EDIT_COORDENADORES"><span id="EDIT_Alterado">Alterado Por: ' + Vals[0].AlteradoPor + ' em ' + dataFormatada + ' ' + DataHora[1] + '</span>\n\
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
            return false;
        }
    });
}
function recuperar_implantacao() {
    var periodos = ['SEMANAL', 'QUINZENAL', 'MENSAL', 'BIMESTRAL', 'QUADRIMESTRAL'];
    var registro = ['ATA', 'RELATÓRIO'];
    $.ajax({url: 'implantacao.php',
        data: {Acao: "recuperar", Cidade: $("#CodCidade").val(), Ano: $("#Ano").val()},
        type: 'POST',
        success: function (output) {
           // console.log(output);
           if (output != 'vazio') {
            var Vals = JSON.parse(output);
                //botao editar
                if ($('#btn_editar_implantacao').length == 0) {

                    var d = Vals[0].data_save,
                    DataHora = d.split(' '),
                    dArr = DataHora[0].split('-'),
                    dataFormatada = dArr[2] + "/" + dArr[1] + "/" + dArr[0];
                    $('#IMPLANTACAO').prepend('<div id="EDIT_IMPLANTACAO"><span id="EDIT_Alterado">Alterado Por: ' + Vals[0].AlteradoPor + ' em ' + dataFormatada + ' ' + DataHora[1] + '</span>\n\
                        <button type="button" id="btn_editar_implantacao" name="Editar" class="btn pull-right btn-default editarButton">Editar</button>\n\n\
                        </div>\n\
                        ');
                }

                $('input[name=COMISSAO][value=' + Vals[0].COMISSAO + ']').click();
                if (Vals[0].COMISSAO === 'SIM') {
                    $('input[name=NOMECOMISSAO]').val(Vals[0].NOMECOMISSAO);
                    $('input[name=NOMECOMISSAO]').prop("disabled", true);

                    $("#DTDECRETO").val(Vals[0].DTDECRETO);
                    $("#DTDECRETO").prop("disabled", true);

                    $("#DECRETO").val(Vals[0].DECRETO);
                    $("#DECRETO").prop("disabled", true);

                    if (Vals[0].UPDECRETO != 'N\u00c3O SE APLICA') {
                        $('#InputUPDECRETO').hide();
                        if ($('#link_implantacao').length == 0) {
                            $('<a/>').attr({
                                id: "link_implantacao",
                                name: "link",
                                href: 'uploads/' + Vals[0].UPDECRETO,
                                text: "ver arquivo",
                                target: "_blank"
                            }).appendTo('#_UPDECRETO').html("Visualizar Arquivo");
                        } else {
                            $("#UPDECRETO").prop("disabled", true);
                        }
                    }
                    //recuperar instituicoes 

                    $.each(Vals[0].instituicoes, function (key, val) {

                        $("#instituicao" + (key + 1)).val(Vals[0].instituicoes[key].nome);
                        $("#setor" + (key + 1)).val(Vals[0].instituicoes[key].setor);
                        $("#origem" + (key + 1)).val(Vals[0].instituicoes[key].origem);

                        if ((key > 1) && ($("#ImplantacaoInstituicoes" + (key + 1)).length == 0) && (Vals[0].instituicoes[key].nome !== '')) {
                            $('#ImplantacaoInstituicoes').append('<tr id="ImplantacaoInstituicoes' + (key + 1) + '">\n\n\
                                <td>\n\
                                <input id="instituicao' + (key + 1) + '" type="text" class="form-control" name="instituicao[]">\n\
                                </td>\n\
                                <td>\n\
                                <select id="setor' + (key + 1) + '" name="setor[]" class="form-control" style="padding-right:5px;padding-left:5px;">\n\
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
                                <select id="origem' + (key + 1) + '" name="origem[]" class="form-control" style="padding-right:0px;">\n\
                                <option value=""></option>\n\
                                <option value="GOVERNAMENTAL">Governamental</option>\n\
                                <option value="NAO GOVERNAMENTAL">Não Governamental</option>\n\
                                </select>\n\
                                </td>\n\
                                <td>\n\
                                <button type="button" class="btn btn-default removeButton"><i class="glyphicon glyphicon-minus"></i></button> \n\
                                </td>\n\
                                </tr>');
                            $("#instituicao" + (key + 1)).val(Vals[0].instituicoes[key].nome);
                            $("#instituicao" + (key + 1)).prop("disabled", true);
                            $("#setor" + (key + 1)).val(Vals[0].instituicoes[key].setor);
                            $("#setor" + (key + 1)).prop("disabled", true);
                            $("#origem" + (key + 1)).val(Vals[0].instituicoes[key].origem);
                            $("#origem" + (key + 1)).prop("disabled", true);
                        }
                    });


                    $('#IMPLANTACAO').find('.addButton').hide()
                    $('#IMPLANTACAO').find('.removeButton').hide()
                }

                $('input[name=COMISSAO]').prop("disabled", true);

                //console.log(Vals[0].PERIODIC)
                if ($.inArray(Vals[0].PERIODIC, periodos) > -1) {
                    $('input:radio[name=PERIODIC][value="' + Vals[0].PERIODIC + '"]').click();
                } else {
                    //console.log('aui')
                    $('input:radio[name=PERIODIC][value=OUTRA]').click();
                    $('input[name=outradata]').prop("disabled", true);
                    $('input[name=outradata]').val([Vals[0].PERIODIC]);

                }
                if ($.inArray(Vals[0].REGREUNIAOCI, registro) > -1) {
                    $('input:radio[name=REGREUNIAOCI][value=' + Vals[0].REGREUNIAOCI + ']').click();
                } else {
                    $('input:radio[name=REGREUNIAOCI][value=OUTRA]').click();
                    $('input[name=REGREUNIAOCIoutra]').prop("disabled", true);
                    $('input[name=REGREUNIAOCIoutra]').val(Vals[0].REGREUNIAOCI);
                }
                if ($.inArray(Vals[0].DTREUNIAOCPVT, periodos) > -1) {
                    $('input:radio[name=DATAREUNIAOCPVT][value=' + Vals[0].DTREUNIAOCPVT + ']').click();
                } else {
                    $('input:radio[name=DATAREUNIAOCPVT][value=OUTRA]').click();
                    $('input[name=DATAREUNIAOCPVToutra]').prop("disabled", true);
                    $('input[name=DATAREUNIAOCPVToutra]').val(Vals[0].DTREUNIAOCPVT);
                }

                if ($.inArray(Vals[0].REGREUNIAOCPVT, registro) > -1) {
                    $('input:radio[name=REGREUNIAOCPVT][value=' + Vals[0].REGREUNIAOCPVT + ']').click();
                } else {
                    $('input:radio[name=REGREUNIAOCPVT][value=OUTRA]').click();
                    $('input[name=REGREUNIAOCPVToutra]').prop("disabled", true);
                    $('input[name=REGREUNIAOCPVToutra]').val(Vals[0].REGREUNIAOCPVT);
                }

                $('input[name=DTREUNIAOCPVT]').prop("disabled", true);
                $('input[name=DATAREUNIAOCPVT]').prop("disabled", true);
                $('input[name=PERIODIC]').prop("disabled", true);
                $('input[name=REGREUNIAOCI]').prop("disabled", true);
                $('input[name=REGREUNIAOCPVT]').prop("disabled", true);

                $("#DTREUNIAOCI").val(Vals[0].DTREUNIAOCI);
                $("#DTREUNIAOCI").prop("disabled", true);
                //Desabilitar os campos
                $("#IMPLANTACAO input").prop("disabled", true);
                $("#IMPLANTACAO select").prop("disabled", true);
                $("#IMPLANTACAO").find('.has-success').removeClass('has-success')
                recuperado_implantacao = true;
                return true;
            } else {
                recuperado_implantacao = false;
                return false;
            }
        }
    });
}
function recuperar_qualidade() {
    $.ajax({url: 'qualidade.php',
        data: {Acao: "recuperar", Cidade: $("#CodCidade").val(), Ano: $("#Ano").val()},
        type: 'POST',
        success: function (output) {
            //console.log('qualidade')
          //  console.log(output);
          if (output != 'vazio') {
            var Vals = JSON.parse(output);

            var periodos = ['Semanal', 'Quinzenal', 'Mensal', 'Bimestral'];
            var registro = ['Ata', 'Relatório'];

                //botao editar
                if ($('#btn_editar_qualidade').length == 0) {

                    var d = Vals[0].data_save,
                    DataHora = d.split(' '),
                    dArr = DataHora[0].split('-'),
                    dataFormatada = dArr[2] + "/" + dArr[1] + "/" + dArr[0];
                    $('#QUALIDADE').prepend('<div id="EDIT_QUALIDADE"><span id="EDIT_Alterado">Alterado Por: ' + Vals[0].AlteradoPor + ' em ' + dataFormatada + ' ' + DataHora[1] + '</span>\n\
                        <button type="button" id="btn_editar_qualidade" name="Editar" class="btn pull-right btn-default editarButton">Editar</button>\n\n\
                        </div>\n\
                        ');
                }

                $('input[name=COMISSAOGD][value=' + Vals[0].COMISSAO + ']').trigger('click');
                if (Vals[0].COMISSAOGD === 'SIM') {
                    $('input:radio[name=COMISSAOGD][value=SIM]').trigger('click');
                    if (Vals[0].COMISSAOFORM === 'SIM') {
                        $('input:radio[name=COMISSAOFORM][value=SIM]').trigger('click');
                        $('input[name=COMISSAODOC]').val(Vals[0].COMISSAODOC);
                        $("#DTCOMISSAO").val(Vals[0].DTCOMISSAO);
                        $("#NCOMISSAO").val(Vals[0].NCOMISSAO);

                        if (Vals[0].UPDECRETOCOMISSAO != 'N\u00c3O SE APLICA') {
                            $('#InputUPDECRETOCOMISSAO').hide();
                            if ($('#link_qualidade').length == 0) {
                                $('<a/>').attr({
                                    id: "link_qualidade",
                                    name: "link",
                                    href: 'uploads/' + Vals[0].UPDECRETOCOMISSAO,
                                    text: "ver arquivo",
                                    target: "_blank"
                                }).appendTo('#_UPDECRETOCOMISSAO').html("Visualizar Arquivo");
                            }
                        }
                        //recuperar instituicoes                                                  
                        $.each(Vals[0].instituicoes, function (key, val) {

                            $("#Qualidade_instituicao" + (key + 1)).val(Vals[0].instituicoes[key].nome);
                            $("#Qualidade_setor" + (key + 1)).val(Vals[0].instituicoes[key].setor);
                            $("#Qualidade_origem" + (key + 1)).val(Vals[0].instituicoes[key].origem);

                            if ((key > 1) && ($("#QualidadeInstituicoes" + (key + 1)).length == 0)) {
                                $('#QualidadeInstituicoes').append('<tr id="ImplantacaoInstituicoes' + (key + 1) + '">\n\n\
                                    <td>\n\
                                    <input id="Qualidade_instituicao' + (key + 1) + '" type="text" class="form-control" name="Qualidade_instituicao[]">\n\
                                    </td>\n\
                                    <td>\n\
                                    <select id="Qualidade_setor' + (key + 1) + '" name="Qualidade_setor[]" class="form-control" style="padding-right:5px;padding-left:5px;">\n\
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
                                    <select id="Qualidade_origem' + (key + 1) + '" name="Qualidade_origem[]" class="form-control" style="padding-right:0px;">\n\
                                    <option value=""></option>\n\
                                    <option value="GOVERNAMENTAL">Governamental</option>\n\
                                    <option value="NAO GOVERNAMENTAL">Não Governamental</option>\n\
                                    </select>\n\
                                    </td>\n\
                                    <td>\n\
                                    <button type="button" class="btn btn-default removeButton"><i class="glyphicon glyphicon-minus"></i></button> \n\
                                    </td>\n\
                                    </tr>');
                                $("#Qualidade_instituicao" + (key + 1)).val(Vals[0].instituicoes[key].nome);
                                $("#Qualidade_setor" + (key + 1)).val(Vals[0].instituicoes[key].setor);
                                $("#Qualidade_origem" + (key + 1)).val(Vals[0].instituicoes[key].origem);
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


                var BASESAT = Vals[0].BASESAT.split(',');
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
                if (Vals[0].BASESOBITO === 'NAO') {
                    $('input:radio[name=BASESOBITO][value=NAO]').click();
                } else {
                    var BASESOBITO = Vals[0].BASESOBITO.split(',');

                    $('input:radio[name=BASESOBITO][value=SIM]').click();
                    $.each(BASESOBITO, function (key, val) {
                        if (key > 0) {
                            $('input:checkbox[name="base_obitos[]"][value="' + val + '"]').trigger('click');
                            $('input:checkbox[name="base_obitos[]"][value="' + val + '"]').prop("checked", true);

                        }
                    })
                }
                if (Vals[0].BASEFERIDO === 'NAO') {
                    $('input:radio[name=BASEFERIDO][value=NAO]').click();
                } else {
                    var BASEFERIDO = Vals[0].BASEFERIDO.split(',');
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
                if (Vals[0].MAPEAMENTO === 'SIM') {
                    $('input:radio[name=MAPEAMENTO][value=SIM]').click();
                } else {
                    $('input:radio[name=MAPEAMENTO][value=NAO]').click();
                }
                if (Vals[0].LIMPEZA === 'SIM') {
                    $('input:radio[name=LIMPEZA][value=SIM]').click();
                } else {
                    $('input:radio[name=LIMPEZA][value=NAO]').click();
                }
                if (Vals[0].LISTAUNICA === 'SIM') {
                    $('input:radio[name=LISTAUNICA][value=SIM]').click();
                } else {
                    $('input:radio[name=LISTAUNICA][value=NAO]').click();
                }
                if (Vals[0].FATORRISCO === 'SIM') {
                    $('input:radio[name=FATORRISCO][value=SIM]').click();
                } else {
                    $('input:radio[name=FATORRISCO][value=NAO]').click();
                }
                if (Vals[0].INDICADOROBITO === 'FERIOS_E_OBITOS') {
                    $('input:radio[name=INDICADOROBITO][value=FERIOS_E_OBITOS]').click();
                } else if (Vals[0].INDICADOROBITO === 'FERIDOS') {
                    $('input:radio[name=INDICADOROBITO][value=FERIDOS]').click();
                } else if (Vals[0].INDICADOROBITO === 'OBITOS') {
                    $('input:radio[name=INDICADOROBITO][value=OBITOS]').click();
                } else if (Vals[0].INDICADOROBITO === 'NENHUM') {
                    $('input:radio[name=INDICADOROBITO][value=NENHUM]').click();
                }
                if ((Vals[0].BASESAT !== 'NAO') && (Vals[0].BASESOBITO !== 'NAO') && (Vals[0].BASEFERIDO !== 'NAO')) {
                    $("#_LINKAGE").show();
                    if (Vals[0].LINKAGE === 'NAO') {
                        $('input:radio[name=LINKAGE][value=NAO]').click();
                        $('#COMOFOILISTAVITIMAS').val(Vals[0].COMOFOILISTAVITIMAS);
                        $('#NAOLINKOBITO').val(Vals[0].NAOLINKOBITO);
                        $('#NAOLINKFER').val(Vals[0].NAOLINKFER);
                    } else {
                        var LINKAGE = Vals[0].LINKAGE.split(',');
                        var PRILINKAGE = Vals[0].PRILINKAGE.split('/');
                        var ULTLINKAGE = Vals[0].ULTLINKAGE.split('/');

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
                recuperado_qualidade = false;
                return false;
            }
        }
    });
}
function recuperar_analise() {
    $.ajax({url: 'analise.php',
        data: {Acao: "recuperar", Cidade: $("#CodCidade").val(), Ano: $("#Ano").val()},
        type: 'POST',
        success: function (output) {
            //console.log(output)
            if (output != 'vazio') {
                var Vals = JSON.parse(output);

                //botao editar
                if ($('#btn_editar_analise').length == 0) {

                    var d = Vals[0].data_save,
                    DataHora = d.split(' '),
                    dArr = DataHora[0].split('-'),
                    dataFormatada = dArr[2] + "/" + dArr[1] + "/" + dArr[0];
                    $('#ANALISE').prepend('<div id="EDIT_ANALISE"><span id="EDIT_Alterado">Alterado Por: ' + Vals[0].AlteradoPor + ' em ' + dataFormatada + ' ' + DataHora[1] + '</span>\n\
                        <button type="button" id="btn_editar_analise" name="Editar" class="btn pull-right btn-default editarButton">Editar</button>\n\n\
                        </div>\n\
                        ');
                }
                if (Vals[0].IDENTIFICACAORISCO === 'SIM') {
                    $('input:radio[name=IDENTIFICACAORISCO][value=SIM]').click();
                    $('input:radio[name=FATORESRISCOACIDENTES][value="' + Vals[0].FATORESRISCOACIDENTES + '"]').click();
                    $('input:radio[name=CONDUTARISCOACIDENTES][value="' + Vals[0].CONDUTARISCOACIDENTES + '"]').click();
                    $('input:radio[name=FATORESGRAVIDADE][value="' + Vals[0].FATORESGRAVIDADE + '"]').click();
                    $('input:radio[name=FATORESFATAL][value="' + Vals[0].FATORESFATAL + '"]').click();
                    var ULTIMORISCO = Vals[0].ULTIMORISCO.split('/');
                    $('#ULTIMORISCO').val(ULTIMORISCO[1]);
                    $('#ULTIMOSEMESTRERISCO').val(ULTIMORISCO[0]);
                    if (Vals[0].FATORESRISCOACIDENTES === 'SIM') {
                        $('input:radio[name=FATORESRISCOACIDENTES_SIM][value="' + Vals[0].FATORESRISCOACIDENTES_SIM + '"]').click();
                        if(Vals[0].FATORESRISCOACIDENTES_SIM == 'AMOSTRA'){
                            $('#AMOSTRA').val(Vals[0].AMOSTRA)
                        }
                    }
                } else {
                    $('input:radio[name=IDENTIFICACAORISCO][value=NAO]').click();
                }
                if (Vals[0].IDENTIFICACAORISCOCADA === 'SIM') {
                    //console.log(Vals);
                    $('input:radio[name=IDENTIFICACAORISCOCADA][value=SIM]').click();
                    $('input:radio[name="FATORESRISCOACIDENTESCADA"][value="' + Vals[0].FATORESRISCOACIDENTESCADA + '"]').click();
                    $('input:radio[name=CONDUTARISCOACIDENTESCADA][value="' + Vals[0].CONDUTARISCOACIDENTESCADA + '"]').click();
                    $('input:radio[name=FATORESGRAVIDADECADA][value="' + Vals[0].FATORESGRAVIDADECADA + '"]').click();
                    $('input:radio[name=FATORESFATALCADA][value="' + Vals[0].FATORESFATALCADA + '"]').click();
                    var ULTIMORISCOCADA = Vals[0].ULTIMORISCOCADA.split('/');
                    $('#ULTIMORISCOCADA').val(ULTIMORISCOCADA[1]);
                    $('#ULTIMOSEMESTRERISCOCADA').val(ULTIMORISCO[0]);
                } else {
                    $('input:radio[name=IDENTIFICACAORISCOCADA][value=NAO]').click();
                }
                if (Vals[0].CONSTRUCAOQUADROMULTIPLO === 'SIM') {
                    $('input:radio[name=CONSTRUCAOQUADROMULTIPLO][value=SIM]').click();
                    var ULTIMOCONSTRUCAOQUADROMULTIPLO = Vals[0].ULTIMOCONSTRUCAOQUADROMULTIPLO.split('/');
                    $('#ULTIMOCONSTRUCAOQUADROMULTIPLO').val(ULTIMOCONSTRUCAOQUADROMULTIPLO[1]);
                    $('#ULTIMOSEMESTRECONSTRUCAOQUADROMULTIPLO').val(ULTIMOCONSTRUCAOQUADROMULTIPLO[0]);
                } else {
                    $('input:radio[name=CONSTRUCAOQUADROMULTIPLO][value=NAO]').click();
                }
                if (Vals[0].PROGRAMAPRIORITARIOS === 'SIM') {
                    $('input:radio[name=PROGRAMAPRIORITARIOS][value=SIM]').click();
                    $('#ULTIMOPROGRAMAPRIORITARIOS').val(Vals[0].ULTIMOPROGRAMAPRIORITARIOS);
                } else {
                    $('input:radio[name=PROGRAMAPRIORITARIOS][value=NAO]').click();
                }
                if (Vals[0].FATORESCHAVE === 'SIM') {
                    $('input:radio[name=FATORESCHAVE][value=SIM]').click();
                    var ULTIMOFATORESCHAVE = Vals[0].ULTIMOFATORESCHAVE.split('/');
                    $('#ULTIMOFATORESCHAVE').val(ULTIMOFATORESCHAVE[1]);
                    /*$('#ULTIMOSEMESTREFATORESCHAVE').val(ULTIMOFATORESCHAVE[0]);*/
                    var PRINCIPAISFATORESCHAVE = Vals[0].PRINCIPAISFATORESCHAVE.split(',');
                    $.each(PRINCIPAISFATORESCHAVE, function (key, val) {
                        $('input:checkbox[name="PRINCIPAISFATORESCHAVE[]"][value="' + val + '"]').click()
                        $('input:checkbox[name="PRINCIPAISFATORESCHAVE[]"][value="' + val + '"]').prop("checked", true);

                    })
                } else {
                    $('input:radio[name=FATORESCHAVE][value=NAO]').click();
                }
                if (Vals[0].GRUPOSVITIMAS === 'SIM') {
                    $('input:radio[name=GRUPOSVITIMAS][value=SIM]').click();
                    var ULTIMOGRUPOSVITIMAS = Vals[0].ULTIMOGRUPOSVITIMAS.split('/');
                    $('#ULTIMOGRUPOSVITIMAS').val(ULTIMOGRUPOSVITIMAS[1]);
                    /*      $('#ULTIMOSEMESTREGRUPOSVITIMAS').val(ULTIMOGRUPOSVITIMAS[0]);*/
                    var PRINCIPAISGRUPOSVITIMAS = Vals[0].PRINCIPAISGRUPOSVITIMAS.split(',');
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
        }
    });
}
function recuperar_acoes() {
    $.ajax({url: 'acoes.php',
        data: {Acao: "recuperar", Cidade: $("#CodCidade").val(), Ano: $("#Ano").val()},
        type: 'POST',
        success: function (output) {
            //console.log(output)
            if (output != 'vazio') {
                var Vals = JSON.parse(output);
                //console.log(output)
                //botao editar
                if ($('#btn_editar_acoes').length == 0) {

                    var d = Vals[0].data_save,
                    DataHora = d.split(' '),
                    dArr = DataHora[0].split('-'),
                    dataFormatada = dArr[2] + "/" + dArr[1] + "/" + dArr[0];
                    $('#ACOES').prepend('<div id="EDIT_ACOES"><span id="EDIT_Alterado">Alterado Por: ' + Vals[0].AlteradoPor + ' em ' + dataFormatada + ' ' + DataHora[1] + '</span>\n\
                        <button type="button" id="btn_editar_acoes" name="Editar" class="btn pull-right btn-default editarButton">Editar</button>\n\n\
                        </div>\n\
                        ');
                }
                if (Vals[0].ACOESINTEGRADAS === 'SIM') {
                    $('input:radio[name=ACOESINTEGRADAS][value=SIM]').click();
                    $('#ULTIMOACOESINTEGRADAS').val(Vals[0].ULTIMOACOESINTEGRADAS);
                    var PRINCIPAISACOESINTEGRADAS = Vals[0].PRINCIPAISACOESINTEGRADAS.split(',');
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
        }
    });
}
function recuperar_monitoramento() {
    $.ajax({url: 'monitoramento.php',
        data: {Acao: "recuperar", Cidade: $("#CodCidade").val(), Ano: $("#Ano").val()},
        type: 'POST',
        success: function (output) {
            //console.log('monitoramento')
            //console.log(output)
            if (output != 'vazio') {
                var Vals = JSON.parse(output);
                //botao editar
                if ($('#btn_editar_monitoramento').length == 0) {

                    var d = Vals[0].data_save,
                    DataHora = d.split(' '),
                    dArr = DataHora[0].split('-'),
                    dataFormatada = dArr[2] + "/" + dArr[1] + "/" + dArr[0];
                    $('#MONITORAMENTO').prepend('<div id="EDIT_MONITORAMENTO"><span id="EDIT_Alterado">Alterado Por: ' + Vals[0].AlteradoPor + ' em ' + dataFormatada + ' ' + DataHora[1] + '</span>\n\
                        <button type="button" id="btn_editar_monitoramento" name="Editar" class="btn pull-right btn-default editarButton">Editar</button>\n\n\
                        </div>\n\
                        ');
                }
                if (Vals[0].BEBERDIRIGIR === 'SIM') {
                    $('input:radio[name=BEBERDIRIGIR][value=SIM]').click();
                    $('#ULTIMOBEBERDIRIGIR').val(Vals[0].ULTIMOBEBERDIRIGIR)
                    $('input:radio[name=QUADROBEBERDIRIGIR][value="' + Vals[0].QUADROBEBERDIRIGIR + '"]').click();
                } else {
                    $('input:radio[name=BEBERDIRIGIR][value=NAO]').click();
                }
                if (Vals[0].VELOCIDADE === 'SIM') {
                    $('input:radio[name=VELOCIDADE][value=SIM]').click();
                    $('#ULTIMOVELOCIDADE').val(Vals[0].ULTIMOVELOCIDADE)
                    $('input:radio[name=QUADROVELOCIDADE][value="' + Vals[0].QUADROVELOCIDADE + '"]').click();
                } else {
                    $('input:radio[name=VELOCIDADE][value=NAO]').click();
                }
                if (Vals[0].DEFINIDOMUNICIPIO === 'SIM') {
                    $('input:radio[name=DEFINIDOMUNICIPIO][value=SIM]').click();
                    $('#ULTIMODEFINIDOMUNICIPIO').val(Vals[0].ULTIMODEFINIDOMUNICIPIO)
                    $('#QUADRODEFINIDOMUNICIPIO').val(Vals[0].QUADRODEFINIDOMUNICIPIO)

                    //$('input:radio[name=QUADRODEFINIDOMUNICIPIO][value="' + Vals[0].QUADRODEFINIDOMUNICIPIO + '"]').click();
                } else {
                    $('input:radio[name=DEFINIDOMUNICIPIO][value=NAO]').click();
                }
                if (Vals[0].QUADROGRUPOVITIMAS === 'SIM') {
                    $('input:radio[name=QUADROGRUPOVITIMAS][value=SIM]').click();
                    $('#ULTIMOQUADROGRUPOVITIMAS').val(Vals[0].ULTIMOQUADROGRUPOVITIMAS)
                    var QUADROGRUPOVITIMAS_QUAIS = Vals[0].QUADROGRUPOVITIMAS_QUAIS.split(',');
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
        }
    });
}

function cidade() {
    recuperado = false;
    recuperado_implantacao = false;
    recuperado_qualidade = false;
    recuperado_analise = false;
    recuperado_monitoramento = false;
    recuperado_acoes = false;
    recuperado_implantacao = false;
    recuperado = false;

    $('#COORDENADORES').trigger("reset");
    $("#COORDENADORES input").prop("disabled", false);
    $("#btn_editar_coordenadores").remove();
    $('input:radio[name=coordenaTEM][value=SIM]').attr('checked', false);
    $('input:radio[name=coordenaTEM][value=NAO]').attr('checked', false);
    $("#EDIT_COORDENADORES").remove();

    $('#IMPLANTACAO').trigger("reset");
    $('#IMPLANTACAO input').prop("disabled", false);
    $('#IMPLANTACAO select').prop("disabled", false);
    $("#btn_editar_implantacao").remove();
    $("#EDIT_IMPLANTACAO").remove();

    $('#QUALIDADE').trigger("reset");
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

    if ($("#CidadeUser").length == 0) {

        $.ajax(
        {
            url: 'mun_ibge.php',
            type: "POST",
            data: {Cidade: $('#Cidade').val(),Estado:$('#Estado').val()},
            success: function (data, textStatus, jqXHR)
            {
                       //console.log(data);
                       $("#CodCidade").val(data);
//                        if ($("#Populacao").length == 1) {
//                            $.ajax(
//                                    {
//                                        url: 'situacao.php',
//                                        type: "POST",
//                                        data: {Cidade: $("#CodCidade").val(), acao: "DadosCidade", },
//                                        success: function (data, textStatus, jqXHR)
//                                        {
//                                            console.log(data)
//                                            if (data != 'vazio') {
//                                                var parsed = JSON.parse(data);
//                                                //console.log(parsed)
//                                                $("#Populacao").val(parsed[0].Populacao)
//                                                $("#FrotaVeiculos").val(parsed[0].FrotaVeiculos)
//                                            } else if (data == 'vazio') {
//                                                $("#Populacao").val('')
//                                                $("#FrotaVeiculos").val('')
//                                            }
//                                        },
//                                        error: function (jqXHR, textStatus, errorThrown)
//                                        {
//                                            console.log("erro");
//                                        }
//                                    });
//                        }

$('.steps.clearfix ul > li').removeClass('done').addClass('disabled');
$('.steps.clearfix ul > li').eq(0).removeClass('disabled');
$.ajax(
{
    url: 'situacao.php',
    type: "POST",
    data: {Cidade: $("#CodCidade").val(), acao: "etapas", Ano: $("#Ano").val()},
    success: function (data, textStatus, jqXHR)
    {
        //console.log('situacao')
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
                                            $(".relatorio").show()

                                        }else{
                                            $("#Copiar_dados").show()
                                        }
                                    },
                                    error: function (jqXHR, textStatus, errorThrown)
                                    {
                                        console.log("erro");
                                    }
                                });
},
error: function (jqXHR, textStatus, errorThrown)
{
    console.log("erro");
}
});
} else {
    $('.steps.clearfix ul > li').removeClass('done').addClass('disabled');
    $('.steps.clearfix ul > li').eq(0).removeClass('disabled');
    $.ajax(
    {
        url: 'situacao.php',
        type: "POST",
        data: {Cidade: $("#CodCidade").val(), acao: "etapas", Ano: $("#Ano").val() },
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
                    }
                });

}

}

$(document).on('change', '#Cidade', function () {
    $('#Ano').val('')
})

$(document).on('change', '#Ano', function () {
    cidade();
})
$(document).on('click', '#Copiar_dados', function () {
    $.ajax(
    {
        url: 'situacao.php',
        type: "POST",
        data: {Cidade: $("#CodCidade").val(), acao: "CopiaDados", Ano: $("#Ano").val() },
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
        }
    });
})
$(document).ready(function () {
    for (i = new Date().getFullYear(); i > 2014; i--)
    {
        $('#Ano').append($('<option />').val(i).html(i));
    }
    var pvt = $("#pvt");
    var fv = pvt.data('formValidation')
    var contador = 0;
    var caminho = '../uploads/';
    var Implantacao = 2;
    var MAX_OPTIONS_Implantacao = 30;
    var Qualidade = 2;
    var MAX_OPTIONS_Qualidade = 15;
    var atual = 0;
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

                if(currentIndex == 6){
                    atual = 6;
                }


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
            //console.log(atual)
            if (priorIndex < currentIndex) {
              if (priorIndex === 1) {
                    //cordenadores
               //     console.log(recuperado)
               if (recuperado === false) {
                var values = {};
                $.each($('#COORDENADORES input'), function (i, field) {
                    values[field.name] = field.value.toUpperCase();
                });
                values['coordenaTEM'] = $("input[name='coordenaTEM']").val().toUpperCase();

                $.ajax({url: 'coordenadores.php',
                    data: {acao: "gravar", valores: values, Cidade: $("#CodCidade").val(), Ano: $("#Ano").val()},
                    type: 'POST',
                    async: false,
                    success: function (output) {
                        $('#COORDENADORES').trigger("reset");
                        if(output == 'gravado'){
                            recuperar_coordenadores();
                            recuperado = true;
                        }else{
                                    //implementar loading
                                }

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
                    $.ajax({url: 'situacao.php',
                        data: {acao: "gravar", valores: values, Ano: $("#Ano").val()},
                        type: 'POST',
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
                        formData.append('Cidade', $("#CodCidade").val());
                        formData.append('Acao', 'gravar');
                        formData.append('Ano', $("#Ano").val());

                        $.ajax({url: 'implantacao.php',
                            data: formData,
                            type: 'POST',
                            processData: false,
                            contentType: false,
                            async: false,
                            cache: false
                        }).done(function (data) {
                        //console.log(data);
                        //recuperado_implantacao == false;
                        $('#IMPLANTACAO').trigger("reset");
                        recuperar_implantacao();

                    }).fail(function () {
                        console.log("error");
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
    /*                    $("#QUALIDADE select").each(function(){
                         this.value = this.value.toUpperCase();          
                     });*/
                     var formData = new FormData($("#QUALIDADE")[0]);
                     formData.append('Cidade', $("#CodCidade").val());
                     formData.append('Acao', 'gravar');
                     formData.append('Ano', $("#Ano").val());
                   // console.log('grava qualidade')
                   $.ajax({url: 'qualidade.php',
                    data: formData,
                    type: 'POST',
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

        }).fail(function () {
            console.log("error");
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
        formData.append('Cidade', $("#CodCidade").val());
        formData.append('Acao', 'gravar');
        formData.append('Ano', $("#Ano").val());

        $.ajax({url: 'analise.php',
            data: formData,
            type: 'POST',
            processData: false,
            contentType: false,
            cache: false
        }).done(function (data) {
                                        //console.log(data)
                                        $('#ANALISE').trigger("reset");
                                        recuperar_analise();
                                        //recuperado_analise = true;
                                    }).fail(function () {
                                        console.log("error");
                                    })

                                }
                            }
                            if (priorIndex === 5) {

                                if (recuperado_acoes === false) {
                       // console.log('teste')
                       $("#ACOES input").each(function () {
                        if ($(this).attr('type') != 'file') {
                            this.value = this.value.toUpperCase();
                        }
                       });
                       var formData = new FormData($("#ACOES")[0]);
                       formData.append('Cidade', $("#CodCidade").val());
                       formData.append('Acao', 'gravar');
                       formData.append('Ano', $("#Ano").val());

                       $.ajax({url: 'acoes.php',
                        data: formData,
                        type: 'POST',
                        processData: false,
                        contentType: false,
                        cache: false
                       }).done(function (data) {
                       //     console.log(data)
                       $('#ACOES').trigger("reset");
                       recuperar_acoes();
                            //recuperado_acoes = true;
//                            console.log(data)
//                            recuperado_acoes == false;
//                            $('#ACOES').trigger("reset");
}).fail(function () {
    console.log("error");
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
        formData.append('Cidade', $("#CodCidade").val());
        formData.append('Acao', 'gravar');
        formData.append('Ano', $("#Ano").val());

        $.ajax({url: 'monitoramento.php',
            data: formData,
            type: 'POST',
            processData: false,
            contentType: false,
            cache: false
        }).done(function (data) {
          //  console.log(data)
          $('#MONITORAMENTO').trigger("reset");
          recuperar_monitoramento();
          atual = 6;
                            //recuperado_monitoramento = true;
//                            console.log(data);
//                            recuperado_monitoramento == false;
//                            $('#MONITORAMENTO').trigger("reset");
}).fail(function () {
    console.log("error");
})

}
}
}
},
//                            onFinishing: function (event, currentIndex)
//                            {
//                                
//                                return form.valid();
//                            },
onFinished: function (event, currentIndex)
{
    window.location.replace($(location).attr('href').replace('/situacao.php', ''));
},
labels: {
//                                cancel: "Cancelar",
//                                current: "current step:",
//                                pagination: "Pagination",
finish: "Finalizar",
next: "Próximo",
previous: "Anterior",
loading: "Carregando ..."
}

})
.formValidation({
    locale: 'pt_BR',
    framework: 'bootstrap',
//                  TEM Que COMENTAR
//                err: {
//                    container: '#errors'
//                },
icon: {
//                        valid: 'glyphicon glyphicon-ok',
//                        invalid: 'glyphicon glyphicon-remove',
//                        validating: 'glyphicon glyphicon-refresh'
}, excluded: ':disabled',
message: 'Campo Obrigatório ',
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
                message: 'Campo Obrigatório '
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
                message: 'Informe a data da reunião '
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
                message: 'Campo Obrigatório '
            }
        }
    },
    DATAREUNIAOCPVT: {
        enabled: false,
        icon: false,
        validators: {
            notEmpty: {
                message: 'Campo Obrigatório '
            }
        }
    },
    DATAREUNIAOCPVToutra: {
        enabled: false,
        validators: {
            notEmpty: {
                message: 'Campo Obrigatório '
            }
        }
    },
    REGREUNIAOCIoutra: {
        enabled: false,
        validators: {
            notEmpty: {
                message: 'Campo Obrigatório '
            }
        }
    },
    REGREUNIAOCPVT: {
        enabled: false,
        icon: false,
        validators: {
            notEmpty: {
                message: 'Campo Obrigatório '
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
                message: 'Campo Obrigatório '
            }
        }
    },
    COMISSAOFORM: {
        validators: {
            notEmpty: {
                message: 'Campo Obrigatório '
            },
        }
    },
    COMISSAODOC: {
        enabled: false,
        validators: {
            notEmpty: {
                message: 'Campo Obrigatório '
            },
        }
    },
    NCOMISSAO: {
        enabled: false,
        validators: {
            notEmpty: {
                message: 'Campo Obrigatório '
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
                message: 'Campo Obrigatório '
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
                message: 'Campo Obrigatório '
            },
        }
    },
    BASESOBITO: {
        validators: {
            notEmpty: {
                message: 'Campo Obrigatório '
            },
        }
    },
    BASEFERIDO: {
        validators: {
            notEmpty: {
                message: 'Campo Obrigatório '
            },
        }
    },
    BASEFATORRISCO: {
        validators: {
            notEmpty: {
                message: 'Campo Obrigatório '
            },
        }
    },
    MAPEAMENTO: {
        validators: {
            notEmpty: {
                message: 'Campo Obrigatório '
            },
        }
    },
    LIMPEZA: {
        validators: {
            notEmpty: {
                message: 'Campo Obrigatório '
            },
        }
    },
    LISTAUNICA: {
        validators: {
            notEmpty: {
                message: 'Campo Obrigatório '
            },
        }
    },
    FATORRISCO: {
        validators: {
            notEmpty: {
                message: 'Campo Obrigatório '
            },
        }
    },
    INDICADOROBITO: {
        validators: {
            notEmpty: {
                message: 'Campo Obrigatório '
            },
        }
    },
    INDICADORFERIDO: {
        validators: {
            notEmpty: {
                message: 'Campo Obrigatório '
            },
        }
    },
    QUADROMULTIPLO: {
        validators: {
            notEmpty: {
                message: 'Campo Obrigatório '
            },
        }
    },
    LOCAISCHAVE: {
        validators: {
            notEmpty: {
                message: 'Campo Obrigatório '
            },
        }
    },
    RELATORIOS: {
        validators: {
            notEmpty: {
                message: 'Campo Obrigatório '
            },
        }
    },
    LINKAGE: {
        enabled: false,
        validators: {
            notEmpty: {
                message: 'Campo Obrigatório '
            },
        }
    },
    NAOLINKOBITO: {
        enabled: false,
        validators: {
            notEmpty: {
                message: 'Campo Obrigatório '
            },
        }
    },
    ULTBASEOBITO: {
        enabled: false,
        validators: {
            notEmpty: {
                message: 'Campo Obrigatório '
            },
        }
    },
    NAOLINKFER: {
        enabled: false,
        validators: {
            notEmpty: {
                message: 'Campo Obrigatório '
            },
        }
    },
    ULTBASEVITIMAS: {
        enabled: false,
        validators: {
            notEmpty: {
                message: 'Campo Obrigatório '
            },
        }
    },
    PRILINKAGE: {
        enabled: false,
        validators: {
            notEmpty: {
                message: 'Campo Obrigatório '
            },
        }
    },
    PRIMEIROANOLINKAGE: {
        enabled: false,
        validators: {
            notEmpty: {
                message: 'Campo Obrigatório '
            },
        }
    },
    ULTLINKAGEANOLINKAGE: {
        enabled: false,
        validators: {
            notEmpty: {
                message: 'Campo Obrigatório '
            },
        }
    },
    ULTLINKAGE: {
        enabled: false,
        validators: {
            notEmpty: {
                message: 'Campo Obrigatório '
            },
        }
    },
    COMOFOILISTAVITIMAS: {
        enabled: false,
        validators: {
            notEmpty: {
                message: 'Campo Obrigatório '
            },
        }
    },
    PLANOACAO: {
        validators: {
            notEmpty: {
                message: 'Campo Obrigatório '
            },
        }
    },
    QUADRODEFINIDOMUNICIPIO: {
        enabled: false,
        validators: {
            notEmpty: {
                message: 'Campo Obrigatório '
            },
        }
    },
    QUADROVELOCIDADE: {
        enabled: false,
        validators: {
            notEmpty: {
                message: 'Campo Obrigatório '
            },
        }
    },
    ULTIMOVELOCIDADE: {
        enabled: false,
        validators: {
            notEmpty: {
                message: 'Campo Obrigatório '
            },
        }
    },
    VELOCIDADE: {
        validators: {
            notEmpty: {
                message: 'Campo Obrigatório '
            },
        }
    },
    BEBERDIRIGIR: {
        validators: {
            notEmpty: {
                message: 'Campo Obrigatório '
            },
        }
    },
    QUADROBEBERDIRIGIR: {
        enabled: false,
        validators: {
            notEmpty: {
                message: 'Campo Obrigatório '
            },
        }
    },
    PRINCIPAISACOESINTEGRADAS_OUTRO: {
        enabled: false,
        validators: {
            notEmpty: {
                message: 'Campo Obrigatório '
            },
        }
    },
    ULTIMOBEBERDIRIGIR: {
        enabled: false,
        validators: {
            notEmpty: {
                message: 'Campo Obrigatório '
            },
        }
    },
    DEFINIDOMUNICIPIO: {
        validators: {
            notEmpty: {
                message: 'Campo Obrigatório '
            },
        }
    },
    ULTIMODEFINIDOMUNICIPIO: {
        enabled: false,
        validators: {
            notEmpty: {
                message: 'Campo Obrigatório '
            },
        }
    },
    NOMECOMISSAO: {
        validators: {
            notEmpty: {
                message: 'Insira o Nome da Comissão '
            },
        }
    },
    PERCPROG: {
        validators: {
            notEmpty: {
                message: 'Campo Obrigatório '
            },
        }
    },
    IDENTIFICACAORISCO: {
        validators: {
            notEmpty: {
                message: 'Campo Obrigatório '
            },
        }
    },
    ULTIMOSEMESTRERISCO: {
        enabled: false,
        validators: {
            notEmpty: {
                message: 'Campo Obrigatório '
            },
        }
    },
    ULTIMORISCO: {
        enabled: false,
        validators: {
            notEmpty: {
                message: 'Campo Obrigatório '
            },
        }
    },
    FATORESRISCOACIDENTES: {
        enabled: false,
        validators: {
            notEmpty: {
                message: 'Campo Obrigatório '
            },
        }
    },
    CONDUTARISCOACIDENTES: {
        enabled: false,
        validators: {
            notEmpty: {
                message: 'Campo Obrigatório '
            },
        }
    },
    FATORESGRAVIDADE: {
        enabled: false,
        validators: {
            notEmpty: {
                message: 'Campo Obrigatório '
            },
        }
    },
    FATORESFATAL: {
        enabled: false,
        validators: {
            notEmpty: {
                message: 'Campo Obrigatório '
            },
        }
    },
    CONDUTARISCOACIDENTESCADA: {
        enabled: false,
        validators: {
            notEmpty: {
                message: 'Campo Obrigatório '
            },
        }
    },
    ULTIMOSEMESTRERISCOCADA: {
        enabled: false,
        validators: {
            notEmpty: {
                message: 'Campo Obrigatório '
            },
        }
    },
    FATORESGRAVIDADECADA: {
        enabled: false,
        validators: {
            notEmpty: {
                message: 'Campo Obrigatório '
            },
        }
    },
    FATORESFATALCADA: {
        enabled: false,
        validators: {
            notEmpty: {
                message: 'Campo Obrigatório '
            },
        }
    },
    CONSTRUCAOQUADROMULTIPLO: {
        validators: {
            notEmpty: {
                message: 'Campo Obrigatório '
            },
        }
    },
    ULTIMORISCOCADA: {
        enabled: false,
        validators: {
            notEmpty: {
                message: 'Campo Obrigatório '
            },
        }
    },
    FATORESRISCOACIDENTESCADA: {
        enabled: false,
        validators: {
            notEmpty: {
                message: 'Campo Obrigatório '
            },
        }
    },
    ULTIMOSEMESTRECONSTRUCAOQUADROMULTIPLO: {
        enabled: false,
        validators: {
            notEmpty: {
                message: 'Campo Obrigatório '
            },
        }
    },
    ULTIMOCONSTRUCAOQUADROMULTIPLO: {
        enabled: false,
        validators: {
            notEmpty: {
                message: 'Campo Obrigatório '
            },
        }
    },
    FATORESCHAVE: {
        validators: {
            notEmpty: {
                message: 'Campo Obrigatório '
            },
        }
    },
/*  ULTIMOSEMESTREFATORESCHAVE: {
        enabled: false,
        validators: {
            notEmpty: {
                message: 'Campo Obrigatório '
            },
        }
    },*/
    ULTIMOGRUPOSVITIMAS: {
        enabled: false,
        validators: {
            notEmpty: {
                message: 'Campo Obrigatório '
            },
        }
    },
    ULTIMOFATORESCHAVE: {
        enabled: false,
        validators: {
            notEmpty: {
                message: 'Campo Obrigatório '
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
                message: 'Campo Obrigatório '
            },
        }
    },
    ACOESINTEGRADAS: {
        validators: {
            notEmpty: {
                message: 'Campo Obrigatório '
            },
        }
    },
/*  ULTIMOSEMESTREGRUPOSVITIMAS: {
        enabled: false,
        validators: {
            notEmpty: {
                message: 'Campo Obrigatório '
            },
        }
    },*/
    ULTIMOACOESINTEGRADAS: {
        enabled: false,
        validators: {
            notEmpty: {
                message: 'Campo Obrigatório '
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
                message: 'Informe o outro grupo '
            },
        }
    },
    PROGRAMAPRIORITARIOS: {
        validators: {
            notEmpty: {
                message: 'Campo Obrigatório '
            },
        }
    },
    ULTIMOPROGRAMAPRIORITARIOS: {
        enabled: false,
        validators: {
            notEmpty: {
                message: 'Campo Obrigatório '
            },
        }
    },
    IDENTIFICACAORISCOCADA: {
        validators: {
            notEmpty: {
                message: 'Campo Obrigatório '
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
                message: 'Campo Obrigatório '
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
                min: 1,
                message: 'Por favor selecione pelo menos duas opções'
            }
        }
    },
    base_feridos_hospital: {
        enabled: false,
        validators: {
            notEmpty: {
                message: 'Campo Obrigatório '
            },
        }
    },
    base_dados_outras: {
        enabled: false,
        validators: {
            notEmpty: {
                message: 'Campo Obrigatório '
            },
        }
    },
    bases_utilizadas_hospital: {
        enabled: false,
        validators: {
            notEmpty: {
                message: 'Campo Obrigatório '
            },
        }
    },
    bases_utilizadas_outras: {
        enabled: false,
        validators: {
            notEmpty: {
                message: 'Campo Obrigatório '
            },
        }
    },
    'base_dados[]': {
        enabled: false,
        validators: {
            choice: {
                min: 1,
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
                message: 'Informe a origem da instituicao   '
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
                    $("#pvt").formValidation('enableFieldValidators', 'DATAREUNIAOCPVToutra', true)
                } else {
                    $("#pvt").formValidation('enableFieldValidators', 'DATAREUNIAOCPVToutra', false)
                    $('#_DATAREUNIAOCPVToutra').hide();
                }
            })
            .on('click', '[name="PERIODIC"]', function (e) {
                if ($(this).val() === 'OUTRA') {
                    $('#_outradata').show();
                    $("#pvt").formValidation('enableFieldValidators', 'outradata', true)
                } else if ($.inArray($(this).val(), periodos) !== -1) {
                    $("#pvt").formValidation('enableFieldValidators', 'outradata', false)
                    $('#_outradata').hide();
                } else {
                    $('#_outradata').show();
                    $("#pvt").formValidation('enableFieldValidators', 'outradata', true)
                }
            })
            .on('change', '[name="REGREUNIAOCI"]', function (e) {
                if ($(this).val() === 'OUTRA') {
                    $('#_REGREUNIAOCIoutra').show();
                    $("#pvt").formValidation('enableFieldValidators', 'REGREUNIAOCIoutra', true)
                } else {
                    $("#pvt").formValidation('enableFieldValidators', 'REGREUNIAOCIoutra', false)
                    $('#_REGREUNIAOCIoutra').hide();
                }
            })
            .on('change', '[name="REGREUNIAOCPVT"]', function (e) {
                if ($(this).val() === 'OUTRA') {
                    $('#_REGREUNIAOCPVToutra').show();
                    $("#pvt").formValidation('enableFieldValidators', 'REGREUNIAOCPVToutra', true)
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
                    /*$('#pvt').formValidation('enableFieldValidators', 'ULTIMOSEMESTREFATORESCHAVE', true);*/
                    $('#pvt').formValidation('enableFieldValidators', 'ULTIMOFATORESCHAVE', true);
                    $('#pvt').formValidation('enableFieldValidators', 'PRINCIPAISFATORESCHAVE[]', true);

                } else {
                    /*$('#pvt').formValidation('enableFieldValidators', 'ULTIMOSEMESTREFATORESCHAVE', false);*/
                    $('#pvt').formValidation('enableFieldValidators', 'ULTIMOFATORESCHAVE', false);
                    $('#pvt').formValidation('enableFieldValidators', 'PRINCIPAISFATORESCHAVE[]', false);
                }
            })
            .on('change', '[name="GRUPOSVITIMAS"]', function (e) {
                if ($(this).val() === 'SIM') {
                 /* $('#pvt').formValidation('enableFieldValidators', 'ULTIMOSEMESTREGRUPOSVITIMAS', true);*/
                 $('#pvt').formValidation('enableFieldValidators', 'ULTIMOGRUPOSVITIMAS', true);
                 $('#pvt').formValidation('enableFieldValidators', 'PRINCIPAISGRUPOSVITIMAS[]', true);

             } else {
                 /* $('#pvt').formValidation('enableFieldValidators', 'ULTIMOSEMESTREGRUPOSVITIMAS', false);*/
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
                maxDate: new Date(),
                endDate: new Date(),
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
                maxDate: new Date(),
                endDate: new Date(),
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
                maxDate: new Date(),
                endDate: new Date(),
                todayHighlight: true
            }).on('changeDate', function (e) {
        // Revalidate the date field
        $('#pvt').formValidation('revalidateField', 'DTDECRETO');
    });


        });



