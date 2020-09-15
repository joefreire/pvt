//arquivo js lista unica - ultima alteração 22/09/2016
//Guilherme Freire
function IsJsonString(str) {
    try {
        JSON.parse(str);
    } catch (e) {
        return false;
    }
    return true;
}
function replaceSpecialChars(str) {
    var conversions = new Object();
    conversions['ae'] = 'ä|æ|ǽ';
    conversions['oe'] = 'ö|œ';
    conversions['º'] = '';
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

function isJson(str) {
    try {
        JSON.parse(str);
    } catch (e) {
        return false;
    }

    return true;
}
function limpa_formulário_cep() {
    // Limpa valores do formulário de cep.
    $("#Endereco").val("");
    $("#Bairro").val("");
    //$("#ibge").val("");
}


$('.info').click(function () {
    $('#informacoes').modal('show');
});

//Quando o campo cep perde o foco.
$("#CEP").blur(function () {

    //Nova variável "cep" somente com dígitos.
    var cep = $(this).val().replace(/\D/g, '');

    //Verifica se campo cep possui valor informado.
    if (cep != "") {

        //Expressão regular para validar o CEP.
        var validacep = /^[0-9]{8}$/;

        //Valida o formato do CEP.
        if (validacep.test(cep)) {

            //Preenche os campos com "..." enquanto consulta webservice.
            $("#Endereco").val("...")
            $("#Bairro").val("...")
            //$("#MunicipioAcidente").val("...")
            //$("#EstadoAcidente").val("...")
            //$("#ibge").val("...")

            //Consulta o webservice viacep.com.br/
            $.getJSON("//viacep.com.br/ws/" + cep + "/json/?callback=?", function (dados) {
                console.log(dados)
                if (!("erro" in dados)) {
                    //Atualiza os campos com os valores da consulta.
                    $("#Endereco").val(dados.logradouro);
                    $("#Bairro").val(dados.bairro);
                    $("#EstadoAcidente").val(dados.uf);
                    $("#EstadoAcidente").trigger('change');
                    $("#MunicipioAcidente").val(dados.localidade);

                } else {
                    //CEP pesquisado não foi encontrado.
                    limpa_formulário_cep();
                    alert("CEP não encontrado.");
                    $("#CEP").val('');
                }
            });
        } //end if.
        else {
            //cep é inválido.
            limpa_formulário_cep();
            $("#CEP").val('');
        }
    }
});
function validaLatitde() {
    if ($("#Endereco").val() && $("#Bairro").val() && $("#Numero").val() && $("#MunicipioAcidente").val()) {
        getLatitude($("#Endereco").val() + ', ' + $("#Numero").val() + ' - ' + $("#Bairro").val() + ', ' + $("#MunicipioAcidente").val() + ' - ' + $("#EstadoAcidente").val())

    }
}

function getLatitude(endereco) {
    console.log(endereco)
    var geocoder;
    geocoder = new google.maps.Geocoder();
    geocoder.geocode({'address': endereco + ', Brasil', 'region': 'BR'}, function (results, status) {
        console.log(results)
        if (status == google.maps.GeocoderStatus.OK) {
            if (results[0]) {

                var latitude = results[0].geometry.location.lat();
                var longitude = results[0].geometry.location.lng();
                $("#CoordX").val(latitude);
                $("#CoordY").val(longitude);
                $.each(results[0].address_components, function (index, value) {
                    if (value.types[0] == 'postal_code') {
                        var cep = value.long_name
                        if (cep) {
                            $("#CEP").val(cep.replace(/-/g, ""));
                        }
                    }
                });

            }
        }
    })



}
function cellStyle(value, row, index) {
    console.log(row)
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
    alertify.confirm('Você quer apagar a lista única de ' + $('#Cidade').val(), 'Você irá deletar toda a lista única e os arquivos do SIM/SIH e as Linkagens de ' + $('#Cidade').val() + ' \n\
        no Periodo de ' + $('#Trimestre').val() + '/' + $('#Ano').val() + '<BR>Você tem certeza?', function () {
            $("#xlf").val('');
            $('#_sucesso').hide();
            $('#_tabelaResultados').hide();
            $('#_delete').show();

            $.ajax(
            {
                url: 'lista_unica.php?Apaga=SIM',
                type: "POST",
                data: {Ano: $('#Ano').val(), Trimestre: $('#Trimestre').val(), CodCidade: $("#CodCidade").val()},
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
                    console.log("erro para apagar");
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

$("#nascimento").mask("99/99/9999");
$("#acidente").mask("99/99/9999");

$('#table').bootstrapTable({
    onClickRow: function (value, row, index) {
        console.log(value)
        $('#id').val(value["id"]);
        if (value["FONTE_DE_DADOS"] === "" || value["FONTE_DE_DADOS"] === null) {
            $('#_Fonte_de_dados').addClass("has-error");
        } else {
            $('#Fonte_de_dados').val(value["FONTE_DE_DADOS"]);
        }
        if (value["DATA_DE_NASCIMENTO"] === null || value["DATA_DE_NASCIMENTO"] === '') {
            $('#_nascimento').addClass("has-error");
        } else {
            $('#nascimento').val(value["DATA_DE_NASCIMENTO"]);
        }
        if (value["DATA_DO_ACIDENTE"] === null || value["DATA_DO_ACIDENTE"] === '') {
            $('#_acidente').addClass("has-error");
        } else {
            $('#acidente').val(value["DATA_DO_ACIDENTE"]);
        }
        if (value["SEXO"] === "") {
            //$('#_Sexo').addClass("has-error");
        } else {
            $('#Sexo').val(value["SEXO"]);
        }
        if (value["BOLETIM"] === "" || value["BOLETIM"] === null) {
            $('#_Boletim').addClass("has-error");
        } else {
            $('#Boletim').val(value["BOLETIM"]);
        }
        if (value["NOME_COMPLETO"] === "" || value["NOME_COMPLETO"] === null) {
            $('#_Nome_Completo').addClass("has-error");
        } else {
            $('#Nome_Completo').val(value["NOME_COMPLETO"]);
        }
        if (value["CONDICAO_DA_VITIMA"] === "" || value["CONDICAO_DA_VITIMA"] === null) {
            //$('#_Condicao_vitima').addClass("has-error");
        } else {
            $('#Condicao_vitima').val(value["CONDICAO_DA_VITIMA"]);
        }
        if (value["HORA_DO_ACIDENTE"] === "") {
            //$('#_Hora').addClass("has-error");
        } else {
            $('#Hora').val(value["HORA_DO_ACIDENTE"]);
        }
        if (value["GRAVIDADE_DA_LESAO"] === "") {
            $('#_Gravidade').addClass("has-error");
        } else {
            $('#Gravidade').val(value["GRAVIDADE_DA_LESAO"]);
        }
        if (value["TIPO_VEICULO"] === "") {
            //$('#_Tipo_Veiculo').addClass("has-error");
        } else {
            $('#Tipo_Veiculo').val(value["TIPO_VEICULO"]);
        }
        if (value["NOME_DA_MAE"] === "") {
            //$('#_Nome_da_mae').addClass("has-error");
        } else {
            $('#Nome_da_mae').val(value["NOME_DA_MAE"]);
        }
        if (value["ENDERECO_DO_ACIDENTE"] === "") {
            //$('#_Endereco').addClass("has-error");
        } else {
            $('#Endereco').val(value["ENDERECO_DO_ACIDENTE"]);
        }


        $('#Placa').val(value["PLACA"]);
        $('#Bairro').val(value["BAIRRO_DO_ACIDENTE"]);
        $('#Numero').val(value["NUMERO_DO_ACIDENTE"]);
        $('#Complemento').val(value["COMPLEMENTO_DO_ACIDENTE"]);


        $("#myModal").modal();
        $("#myModal").on('hidden.bs.modal', function (e) {
            //console.log(e)
            $("#ajaxform").find('.has-error').removeClass('has-error');
            $("#ajaxform").trigger('reset');
        });


    }

});

$("#ajaxform").submit(function (e)
{
    $('input[type=text]').val(function () {
        return replaceSpecialChars(this.value.toUpperCase());
    })
    var postData = $(this).serialize();
    //console.log(postData)
    $.ajax(
    {
        url: 'lista_unica.php?fix=lista_unica&Trimestre=' + $('#Trimestre').val() + '&Ano=' + $('#Ano').val() + '&CodCidade=' + $('#CodCidade').val(),
        type: "POST",
        data: postData,
        success: function (data, textStatus, jqXHR)
        {
            console.log(data);
            if (data == 'sem_mais_registros') {
               $('#table').bootstrapTable('removeAll');
               $('#table').bootstrapTable('refresh', {url: './lista_unica.php?Trimestre=' + $('#Trimestre').val() + '&Ano=' + $('#Ano').val() + '&CodCidade=' + $('#CodCidade').val()});
               $('#desabilitar').hide();
               $('#_sucesso').show();
               $('#_tabelaResultados').hide();

           }
           $('#table').bootstrapTable('removeAll');
           $('#table').bootstrapTable('refresh', {url: './lista_unica.php?Trimestre=' + $('#Trimestre').val() + '&Ano=' + $('#Ano').val() + '&CodCidade=' + $('#CodCidade').val()});
           $('#myModal').modal('toggle');
       },
       error: function (jqXHR, textStatus, errorThrown)
       {
        console.log("erro");
    }
});
    e.preventDefault();	//STOP default action
});

$('#Ano').change(function () {
    if ($('#Ano').val() < 2015) {
        $('#Ano').val('');
        $('#Ano').focus();
    } else {
        if ($('#Ano').val() !== '' && $('#Trimestre').val() !== '' && $('#CodCidade').val() !== '') {
            $.ajax(
            {
                url: 'lista_unica.php',
                type: "POST",
                data: {Ano: $('#Ano').val(), Trimestre: $('#Trimestre').val(), CodCidade: $("#CodCidade").val()},
                success: function (data, textStatus, jqXHR)
                {
                    $("#xlf").val('');
                    console.log(data)
                    if (IsJsonString(data) != false) {
                        $('#_sucesso').hide();
                        $('#desabilitar').hide();
                        $('#table').bootstrapTable('removeAll');
                        $('#table').bootstrapTable('refresh', {url: './lista_unica.php?Trimestre=' + $('#Trimestre').val() + '&Ano=' + $('#Ano').val() + '&CodCidade=' + $('#CodCidade').val()});
                                // $('#table').bootstrapTable('refresh', {url: './uploads/errados_' + $('#Ano').val() + '-' + $('#Trimestre').val() + '-' + $("#CodCidade").val() + '.json'});
                                $('#_tabelaResultados').show();
                            } else if (data === 'PENDENCIAS') {
                                $('#_sucesso').hide();
                                $('#desabilitar').hide();
                                $('#table').bootstrapTable('refresh', {url: './lista_unica.php?Trimestre=' + $('#Trimestre').val() + '&Ano=' + $('#Ano').val() + '&CodCidade=' + $('#CodCidade').val()});
                                // $('#table').bootstrapTable('refresh', {url: './uploads/errados_' + $('#Ano').val() + '-' + $('#Trimestre').val() + '-' + $("#CodCidade").val() + '.json'});
                                $('#_tabelaResultados').show();
                            } else if (data === 'VAZIO') {
                                $('#_sucesso').hide();
                                $('#desabilitar').show();
                                $('#_tabelaResultados').hide();
                            } else {
                                $('#desabilitar').hide();
                                $('#_sucesso').show();
                            }
                        },
                        error: function (jqXHR, textStatus, errorThrown)
                        {
                            console.log("erro cidade");
                        }
                    });

        }
    }

});
$('#Trimestre').change(function () {
    if ($('#Ano').val() !== '' && $('#Trimestre').val() !== '' && $('#CodCidade').val() !== '') {
        $.ajax(
        {
            url: 'lista_unica.php',
            type: "POST",
            data: {Ano: $('#Ano').val(), Trimestre: $('#Trimestre').val(), CodCidade: $("#CodCidade").val()},
            success: function (data, textStatus, jqXHR)
            {
                $("#xlf").val('');
                console.log(data)
                if (IsJsonString(data) != false) {
                    $('#_sucesso').hide();
                    $('#desabilitar').hide();
                    $('#table').bootstrapTable('removeAll');
                    $('#table').bootstrapTable('refresh', {url: './lista_unica.php?Trimestre=' + $('#Trimestre').val() + '&Ano=' + $('#Ano').val() + '&CodCidade=' + $('#CodCidade').val()});
                            // $('#table').bootstrapTable('refresh', {url: './uploads/errados_' + $('#Ano').val() + '-' + $('#Trimestre').val() + '-' + $("#CodCidade").val() + '.json'});
                            $('#_tabelaResultados').show();
                        } else if (data === 'PENDENCIAS') {
                            $('#_sucesso').hide();
                            $('#desabilitar').hide();
                            $('#table').bootstrapTable('refresh', {url: './lista_unica.php?Trimestre=' + $('#Trimestre').val() + '&Ano=' + $('#Ano').val() + '&CodCidade=' + $('#CodCidade').val()});
                            // $('#table').bootstrapTable('refresh', {url: './uploads/errados_' + $('#Ano').val() + '-' + $('#Trimestre').val() + '-' + $("#CodCidade").val() + '.json'});
                            $('#_tabelaResultados').show();
                        } else if (data === 'VAZIO') {
                            $('#_sucesso').hide();
                            $('#desabilitar').show();
                            $('#_tabelaResultados').hide();
                        } else {
                            $('#desabilitar').hide();
                            $('#_sucesso').show();
                        }
                    },
                    error: function (jqXHR, textStatus, errorThrown)
                    {
                        console.log("erro cidade");
                    }
                });
    }
});
$('#Cidade').change(function () {
    $.ajax(
    {
        url: 'mun_ibge.php',
        type: "POST",
        data: {Cidade: this.value, Estado: $('#Estado').val()},
        success: function (data, textStatus, jqXHR)
        {
            $("#CodCidade").val(data);
            if ($('#Ano').val() !== '' && $('#Trimestre').val() !== '' && $('#CodCidade').val() !== '') {
                $.ajax(
                {
                    url: 'lista_unica.php',
                    type: "POST",
                    data: {Ano: $('#Ano').val(), Trimestre: $('#Trimestre').val(), CodCidade: $("#CodCidade").val()},
                    success: function (data, textStatus, jqXHR)
                    {
                        console.log(data)

                        $("#xlf").val('');
                        if (IsJsonString(data) != false) {
                            $('#_sucesso').hide();
                            $('#desabilitar').hide();
                            $('#table').bootstrapTable('refresh', {url: './lista_unica.php?Trimestre=' + $('#Trimestre').val() + '&Ano=' + $('#Ano').val() + '&CodCidade=' + $('#CodCidade').val()});
                                            // $('#table').bootstrapTable('refresh', {url: './uploads/errados_' + $('#Ano').val() + '-' + $('#Trimestre').val() + '-' + $("#CodCidade").val() + '.json'});
                                            $('#_tabelaResultados').show();
                                        } else if (data === 'PENDENCIAS') {
                                            $('#_sucesso').hide();
                                            $('#desabilitar').hide();
                                            $('#table').bootstrapTable('refresh', {url: './lista_unica.php?Trimestre=' + $('#Trimestre').val() + '&Ano=' + $('#Ano').val() + '&CodCidade=' + $('#CodCidade').val()});
                                            // $('#table').bootstrapTable('refresh', {url: './uploads/errados_' + $('#Ano').val() + '-' + $('#Trimestre').val() + '-' + $("#CodCidade").val() + '.json'});
                                            $('#_tabelaResultados').show();
                                        } else if (data === 'VAZIO') {
                                            $('#_sucesso').hide();
                                            $('#desabilitar').show();
                                            $('#_tabelaResultados').hide();
                                        } else {
                                            $('#desabilitar').hide();
                                            $('#_sucesso').show();
                                        }
                                    },
                                    error: function (jqXHR, textStatus, errorThrown)
                                    {
                                        console.log("erro cidade");
                                    }
                                });
            }
        },
        error: function (jqXHR, textStatus, errorThrown)
        {
            console.log("erro cidade");
        }
    });

});
var X = XLSX;
var XW = {
    /* worker message */
    msg: 'xlsx',
};
var schema = {
    type: 'object',
    properties: {
        'LISTA UNICA': {
            'type': 'array',
            'minItems': 1,
            'items': {
                type: 'object',
                'properties': {
                    "NOME COMPLETO": {"type": "string"},
                    "SEXO": {"type": "string"},
                    "DATA DO ACIDENTE": {"type": "string"},
                    "DATA DE NASCIMENTO": {"type": "string"},
                    "FONTE DE DADOS": {"type": "string"},
                    "CONDICAO DA VITIMA": {"type": "string"},
                    "TIPO ACIDENTE": {"type": "string"},
                    "COORDENADA X": {"type": "string"},
                    "COORDENADA Y": {"type": "string"},
                    "GRAVIDADE DA LESAO": {"type": "string"},
                    "TIPO VEICULO": {"type": "string"},
                    "PLACA": {"type": "string"},
                    "HORA DO ACIDENTE": {"type": "string"},
                    "ENDERECO DO ACIDENTE": {"type": "string"},
                    "NOME DA MAE": {"type": "string"},
                    "BAIRRO": {"type": "string"},
                    "NUMERO": {"type": "string"},
                    "COMPLEMENTO": {"type": "string"},
                    "TIPO LOGRADOURO": {"type": "string"},
                    "IDADE": {"type": "string"},
                    "BOLETIM": {"type": "string"},
                    "DESCRICAO": {"type": "string"}
                },
                'additionalProperties': false,
                'required': [
                'BOLETIM',
                           /* 'DATA DO ACIDENTE',
                            'HORA DO ACIDENTE',
                            'DATA DE NASCIMENTO',
                            'SEXO', 
                            'FONTE DE DADOS',
                            'IDADE',
                            'CONDICAO DA VITIMA',
                            'TIPO ACIDENTE', 'COORD X', 'COORD Y',
                            'GRAVIDADE DA LESAO',
                            'TIPO VEICULO','ENDERECO DO ACIDENTE',
                            'BAIRRO','TIPO LOGRADOURO',
                            'NUMERO',
                            'NOME COMPLETO'*/
                            ]
                        }
                    }
                }
//  ,
//  required: ['LISTA UNICA']
};
var ajv = new Ajv({allErrors: true});
var validate = ajv.compile(schema);
function fixdata(data) {
    var o = "", l = 0, w = 10240;
    for (; l < data.byteLength / w; ++l)
        o += String.fromCharCode.apply(null, new Uint8Array(data.slice(l * w, l * w + w)));
    o += String.fromCharCode.apply(null, new Uint8Array(data.slice(l * w)));
    return o;
}
function get_radio_value(radioName) {
    var radios = document.getElementsByName(radioName);
    for (var i = 0; i < radios.length; i++) {
        if (radios[i].checked || radios.length === 1) {
            return radios[i].value;
        }
    }
}
function to_json(workbook) {
    var result = {};
    workbook.SheetNames.forEach(function (sheetName) {
        var roa = X.utils.sheet_to_row_object_array(workbook.Sheets[sheetName]);
        if (roa.length > 0) {
            result[sheetName] = roa;
        }
    });
    return result;
}
function trimObj(obj) {
    if (!Array.isArray(obj) && typeof obj != 'object')
        return obj;
    return Object.keys(obj).reduce(function (acc, key) {
        acc[key.trim()] = typeof obj[key] == 'string' ? obj[key].trim() : trimObj(obj[key]);
        return acc;
    }, Array.isArray(obj) ? [] : {});
}
function process_wb(wb) {
    var output = "";
    switch (get_radio_value("format")) {
        case "json":
        output = JSON.stringify(to_json(wb), 2, 2);
        break;
        case "form":
        output = to_formulae(wb);
        break;
        default:
        output = to_csv(wb);
    }

    //console.log(output)

    var obj = JSON.parse(output.replace(/\\r/g, ' ').replace(/\\n/g, ' ').replace(/\r\n/g, ' '));
    obj = trimObj(obj)
    console.log(validate(obj))
    console.log(obj)
    if (validate(obj)) {
        // console.log("json valido!");
        var request = {
            checkStatus: function () {
                $.ajax({
                    method: 'GET',
                    url: 'status.php?user_id=LISTA' + $("#user_id").val(),
                    dataType: 'json',
                    success: function (data) {
                        if (data)
                            request.setStatus(data);
                    }
                });
            },
            setStatus: function (status) {

                $(".progress-label").html(status.toFixed(2) + '%');

                $('#prog')
                .progressbar('option', 'value', status)
                .children('.ui-progressbar-value')
                        //.html(status.toPrecision(3) + '%')
                        .css('display', 'block');
                    },
                    _interval: null,
                    clearInterval: function () {
                        clearInterval(request._interval);
                    }
                };
                $(function () {
                    $('#prog').progressbar({value: 0});
                    request._interval = setInterval(request.checkStatus, 1000);

                    $.ajax({
                        method: 'POST',
                        url: 'lista_unica.php',
                        data: {json: output, Ano: $('#Ano').val(), Trimestre: $('#Trimestre').val(), CodCidade: $("#CodCidade").val()},
                        success: function (data) {
                            console.log(validate.errors);
                            request.clearInterval();
                            request.setStatus(100);
                            console.log(data);
                            if (data == "Salvo") {
                                $('#loading-image').hide();
                                $('#_sucesso').show();
                                alertify.success('Salvo com Sucesso');
                                alertify.alert('Salvo com Sucesso', 'Agora você pode fazer o Upload SIM e do SIH para realizar as linkagens').set({onshow: null, onclose: function () {
                                    window.location.replace('./envio_sim');
                                }});
                            } else if (data == "Contem Errados") {
                                $('#loading-image').hide();
                                $('#table').bootstrapTable('refresh', {url: './lista_unica.php?Trimestre=' + $('#Trimestre').val() + '&Ano=' + $('#Ano').val() + '&CodCidade=' + $('#CodCidade').val()});
                                $('#_tabelaResultados').show();
                                alertify.warning('Você deve corrigir os registros com pendências');
                            } else if (data == "Erro") {
                                alertify.alert('Erro', 'Não foi possivel fazer o upload<BR> Tente novamente').set({onshow: null, onclose: function () {
                                    window.location.reload(true);
                                }});

                            } else if (data == "Campos invalidos") {
                                alertify.alert('Erro', 'ERRO NO ARQUIVO DA LISTA UNICA - CAMPOS INVALIDOS').set({onshow: null, onclose: function () {
                                    window.location.reload(true);
                                }});
                            }else if (data == "ERRO_ARQUIVO_FONTE") {
                                alertify.alert('Erro', 'ERRO NO ARQUIVO DA LISTA UNICA - FONTE DE DADOS').set({onshow: null, onclose: function () {
                                    window.location.reload(true);
                                }});
                            } else if (data == "ERRO_ARQUIVO_BOLETIM") {
                                alertify.alert('Erro', 'ERRO NO ARQUIVO DA LISTA UNICA - FALTANDO BOLETIM').set({onshow: null, onclose: function () {
                                    window.location.reload(true);
                                }});
                            } else if (data == "ERRO_PROCESSANDO_LISTA") {
                                alertify.alert('Erro', 'Já esta sendo feito o envio, por favor aguarde').set({onshow: null, onclose: function () {
                                    window.location.reload(true);
                                }});
                            } else {
                                alertify.alert('Erro', 'Problema com banco de dados').set({onshow: null, onclose: function () {
                                    window.location.reload(true);
                                }});
                            }

                        },
                        error: function(xmlhttprequest, textstatus, message) {
                            request.setStatus(0);
                            request.clearInterval();
                            if(textstatus==="timeout") {
                                alertify.alert('Erro', 'Conexão interrompida, por favor verifique sua Conexão').set({onshow: null, onclose: function () {
                                    window.location.reload(true);
                                }});
                            } else {
                                alertify.alert('Erro', 'texto : '+textstatus+'<BR> message : '+message).set({onshow: null, onclose: function () {
                                    window.location.reload(true);
                                }});
                                alert(textstatus);
                            }
                        }
                    });
});

} else {
    console.log(obj);
    console.log(validate.errors);
    if (validate.errors[0].params.missingProperty === null || typeof validate.errors[0].params.missingProperty === 'undefined') {
        alertify.alert('Erro', 'Insira uma Lista Única válida<br> Verifique se todos os campos da Lista Única estão corretos<BR> ERRO NO CAMPO: ' + validate.errors[0].params.additionalProperty).set({onshow: null, onclose: function () {
            window.location.reload(true);
        }});
    } else if (validate.errors[0].params.additionalProperty === null || typeof validate.errors[0].params.additionalProperty === 'undefined') {
        alertify.alert('Erro', 'Insira uma Lista Única válida<br> Verifique se todos os campos da Lista Única estão corretos<BR> ERRO NO CAMPO: ' + validate.errors[0].params.missingProperty).set({onshow: null, onclose: function () {
            window.location.reload(true);
        }});
    }else{
       alertify.alert('Erro', 'Insira uma Lista Única válida<br> Verifique se todos os campos da Lista Única estão corretos<BR> ').set({onshow: null, onclose: function () {
        window.location.reload(true);
    }});
   }
}
;



}
var xlf = document.getElementById('xlf');
function handleFile(e) {
    var sFileName = $('#xlf')[0].files[0].name;
    var sFileExtension = sFileName.split('.')[sFileName.split('.').length - 1].toLowerCase();
    if (!(sFileExtension === "xls" ||
        sFileExtension === "xlsx")) {
        $("#msg").html("Por Favor insira um arquivo EXCEL <BR>(XLX / XLSX)");
    $("#xlfile").val('');
    $('#ModalDialogo').modal('show')
} else {
    $('#loading-image').show();
    $('#desabilitar').hide();
    var files = e.target.files;
    var f = files[0];
    {
        var reader = new FileReader();
        var name = f.name;
        reader.onload = function (e) {
            var data = e.target.result;
            var wb;
            var arr = fixdata(data);
            wb = X.read(btoa(arr), {type: 'base64'});
            process_wb(wb);
        };
        reader.readAsArrayBuffer(f);
    }
}
}


if (xlf.addEventListener)
    xlf.addEventListener('change', handleFile, false);