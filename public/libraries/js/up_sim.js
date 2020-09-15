
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
function ApagaDados() {
    alertify.confirm('Você quer apagar o SIM de ' + $('#Cidade').val(), 'Você irá deletar todos os Registros do SIM de ' + $('#Cidade').val() + ' \n\
        no Período de ' + $('#Trimestre').val() + '/' + $('#Ano').val() + '<BR>Você tem certeza?', function () {
            $('#Linkagem').hide();
            $('#imageInput').val('');
            $('#_ListaUnica').hide();
            $('#_delete').show();
            $.ajax(
            {
                url: 'envio_sim.php?Apaga=SIM',
                type: "POST",
                data: {local: 'SIM', Ano: $('#Ano').val(), Trimestre: $('#Trimestre').val(), CodCidade: $("#CodCidade").val()},
                success: function (data, textStatus, jqXHR)
                {
                    alertify.success('Salvo com Sucesso<BR>Foram apagados todos os registros<BR> Agora você pode enviar novamente');
                    console.log(data)
                    $('#_delete').hide();
                    $('#upload-wrapper').show();

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
                    url: 'envio_sim.php?verifica=upload_listaUnica',
                    type: "POST",
                    data: {Ano: $('#Ano').val(), Trimestre: $('#Trimestre').val(), CodCidade: $("#CodCidade").val()},
                    success: function (data, textStatus, jqXHR)
                    {
                        console.log(data)
                        if (data == 'VAZIO') {
                            console.log('d')
                            $('#upload-wrapper').hide();
                            $('#Linkagem').hide();
                            $('#_ListaUnica').show();
                        } else {

                            $.ajax(
                            {
                                url: 'envio_sim.php?verifica=upload_sim',
                                type: "POST",
                                data: {Ano: $('#Ano').val(), Trimestre: $('#Trimestre').val(), CodCidade: $("#CodCidade").val()},
                                success: function (data, textStatus, jqXHR)
                                {
                                    console.log(data)
                                    if (data == 'VAZIO') {
                                        $('#upload-wrapper').show();
                                        $('#inputEnvio').show();
                                        $('#_ListaUnica').hide();
                                        $('#Linkagem').hide();
                                        $('#Pendencia').hide();
                                    } else if (data == 'PENDENCIA') {
                                        $('#upload-wrapper').hide();
                                        $('#Linkagem').hide();
                                        $('#_ListaUnica').hide();
                                        $('#Pendencia').show();
                                    } else {
                                        $('#upload-wrapper').hide();
                                        $('#_ListaUnica').hide();
                                        $('#Linkagem').show();
                                        $('#Pendencia').hide();
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

            }
        },
        error: function (jqXHR, textStatus, errorThrown)
        {
            console.log("erro cidade");
        }
    });
});
$('#Ano').change(function () {
    if ($('#Ano').val() < 2015) {
        $('#Ano').val('');
        $('#Ano').focus();
    } else {
        if ($('#Ano').val() !== '' && $('#Trimestre').val() !== '' && $('#CodCidade').val() !== '') {
            $.ajax(
            {
                url: 'envio_sim.php?verifica=upload_listaUnica',
                type: "POST",
                data: {Ano: $('#Ano').val(), Trimestre: $('#Trimestre').val(), CodCidade: $("#CodCidade").val()},
                success: function (data, textStatus, jqXHR)
                {
                    console.log(data)
                    if (data == 'VAZIO') {
                        $('#upload-wrapper').hide();
                        $('#Linkagem').hide();
                        $('#_ListaUnica').show();
                    } else {
                        $.ajax(
                        {
                            url: 'envio_sim.php?verifica=upload_sim',
                            type: "POST",
                            data: {Ano: $('#Ano').val(), Trimestre: $('#Trimestre').val(), CodCidade: $("#CodCidade").val()},
                            success: function (data, textStatus, jqXHR)
                            {
                                console.log(data)
                                if (data == 'VAZIO') {
                                    $('#upload-wrapper').show();
                                    $('#inputEnvio').show();
                                    $('#_ListaUnica').hide();
                                    $('#Linkagem').hide();
                                    $('#Pendencia').hide();
                                } else if (data == 'PENDENCIA') {
                                    $('#upload-wrapper').hide();
                                    $('#Linkagem').hide();
                                    $('#_ListaUnica').hide();
                                    $('#Pendencia').show();
                                } else {
                                    $('#upload-wrapper').hide();
                                    $('#_ListaUnica').hide();
                                    $('#Linkagem').show();
                                    $('#Pendencia').hide();
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

        }

    }

});


$('.info').click(function () {
    $('#informacoes').modal('show');
});
$('#Trimestre').change(function () {
    if ($('#Ano').val() !== '' && $('#Trimestre').val() !== '' && $('#CodCidade').val() !== '') {
        $.ajax(
        {
            url: 'envio_sim.php?verifica=upload_listaUnica',
            type: "POST",
            data: {Ano: $('#Ano').val(), Trimestre: $('#Trimestre').val(), CodCidade: $("#CodCidade").val()},
            success: function (data, textStatus, jqXHR)
            {
                console.log(data)
                if (data == 'VAZIO') {
                    $('#upload-wrapper').hide();
                    $('#Linkagem').hide();
                    $('#_ListaUnica').show();
                } else {
                    $.ajax(
                    {
                        url: 'envio_sim.php?verifica=upload_sim',
                        type: "POST",
                        data: {Ano: $('#Ano').val(), Trimestre: $('#Trimestre').val(), CodCidade: $("#CodCidade").val()},
                        success: function (data, textStatus, jqXHR)
                        {
                            console.log(data)
                            if (data == 'VAZIO') {
                                $('#upload-wrapper').show();
                                $('#inputEnvio').show();
                                $('#_ListaUnica').hide();
                                $('#Linkagem').hide();
                                $('#Pendencia').hide();
                            } else if (data == 'PENDENCIA') {
                                $('#upload-wrapper').hide();
                                $('#Linkagem').hide();
                                $('#_ListaUnica').hide();
                                $('#Pendencia').show();
                            } else {
                                $('#upload-wrapper').hide();
                                $('#_ListaUnica').hide();
                                $('#Linkagem').show();
                                $('#Pendencia').hide();
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

    }

});
document.getElementById('imageInput').addEventListener('change', checkFile, false);
var file;
var sFileName;
var sFileExtension;
function checkFile(e) {
    file = e.target.files;
    sFileName = $('#imageInput')[0].files[0].name
    sFileExtension = sFileName.split('.')[sFileName.split('.').length - 1].toLowerCase();
    if (!(sFileExtension === "rar" ||
        sFileExtension === "zip" ||
        sFileExtension === "dbf"
            //sFileExtension === "xls"
            //sFileExtension === "xlsx" ||
            //sFileExtension === "dbf"
            )) {
        $("#msg").html("Por Favor insira um arquivo dbf ou Compactado <BR>( DBF / RAR / ZIP )");
    $('#myModal').modal('show')
        // $("#output").html("Por Favor insira um arquivo DBF ou Comprimido ZIP/RAR!");
    }

}
$(document).ready(function () {

    var progressbox = $('#progressbox');
    var progressbar = $('#progressbar');
    var statustxt = $('#statustxt');
    var completed = '0%';
    var output;
    var options = {
        target: output, // target element(s) to be updated with server response 
        beforeSubmit: beforeSubmit, // pre-submit callback 
        uploadProgress: OnProgress,
        success: afterSuccess, // post-submit callback 
        resetForm: false        // reset the form after successful submit 
    };
    $('#MyUploadForm').submit(function () {
        $(this).ajaxSubmit(options);
        // return false to prevent standard browser submit and page navigation 
        return false;
    });
//when upload progresses	
function OnProgress(event, position, total, percentComplete)
{
        //Progress bar
        progressbar.width(percentComplete + '%') //update progressbar percent complete
        statustxt.html(percentComplete + '%'); //update status text

//        if (percentComplete > 50)
//        {
//            statustxt.css('color', '#fff'); //change status text to white after 50%
//        }
if (percentComplete == 100) {
            //Progress bar
            progressbox.show(); //show progressbar
            statustxt.css('left', '46%'); //set status text
            statustxt.css('color', '#000'); //initial color of status text
        }
    }

//after succesful upload
function afterSuccess(output)
{
    console.log(output)
    if (output != 'arquivo invalido') {
            $('#submit-btn').hide(); //hide submit button
            $('#inputEnvio').hide();
            $('#progressbox').hide();
            $('#titulo').html('Gravando dados no banco de dados');
            $('#mensagem').html('<BR>Gravando dados no banco de dados <BR> Este processo pode demorar um pouco<BR> Não Feche o Navegador <BR>');
            $('#loading-image').show(); //hide submit button
            var request = {
                checkStatus: function () {
                    $.ajax({
                        method: 'GET',
                        url: 'status.php?user_id=SIM' + $("#user_id").val(),
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
                            url: 'excel.php',
                            data: {Arquivo: output, Ano: $('#Ano').val(), Trimestre: $('#Trimestre').val(), CodCidade: $("#CodCidade").val()},
                            success: function (data) {
                                request.clearInterval();
                                request.setStatus(0);
                                console.log(data);
                                if (data == 'gravado excel') {
                                    $('#titulo').html('Fazendo Linkagem');
                                    $('#mensagem').html('<BR>Fazendo a Linkagem dos dados <BR> Este processo pode demorar um pouco <BR> Não Feche o Navegador <BR>');
                                    $(function () {
                                        $('#prog').progressbar({value: 0});
                                        $(".progress-label").html('Carregando...');
                                        request._interval = setInterval(request.checkStatus, 1000);
                                        $.ajax({
                                            method: 'POST',
                                            url: 'progress.php',
                                            data: {Arquivo: output, Ano: $('#Ano').val(), Trimestre: $('#Trimestre').val(), CodCidade: $("#CodCidade").val()},
                                            success: function (data) {
                                                request.clearInterval();
                                                request.setStatus(100);
                                                console.log(data)
                                                if (data != 'vazio') {
                                                    $('#upload-wrapper').hide();
                                                    $('#_ListaUnica').hide();
                                                    $('#Linkagem').show();
                                                    alertify.success('Efetuado a Linkagem com Sucesso ' + data + ' registros');
                                                } else if(data =='vazio'){
                                                 alertify.alert('Linkagem Vazia', 'Linkagem do SIM não retornou nenhuma combinação').set({onshow: null, onclose: function () {
                                                    window.location.reload(true);
                                                }});
                                             }else {
                                                alertify.alert('Erro', 'Não foi efetuado a Linkagem').set({onshow: null, onclose: function () {
                                                    window.location.reload(true);
                                                }});
                                            }
                                        },error: function(xmlhttprequest, textstatus, message) {
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
                                    if (data == 'SEM DADOS') {
                                //erro campos invalidos sim
                                alertify.alert('Erro', 'Não foi possivel fazer o upload do SIM<BR>O Arquivo do SIM deve ter pelo menos os campos: NOME, DO, DTOBITO, DTNASC <BR> Tente novamente').set({onshow: null, onclose: function () {
                                    window.location.reload(true);
                                }});
                            } else {
                                //erro refaça a operacao
                                alertify.alert('Erro', 'Não foi possivel fazer a Linkagem<BR> Tente novamente').set({onshow: null, onclose: function () {
                                    window.location.reload(true);
                                }});
                            }

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
            //arquivo upload invalido -- refaça a operacao
            alertify.alert('Erro', 'Arquivo do SIM inválido<BR> Tente novamente').set({onshow: null, onclose: function () {
                window.location.reload(true);
            }});

        }

//        $.ajax(
//                {
//                    url: 'excel.php',
//                    type: "POST",
//                    data: {CodCidade: $('#CodCidade').val(), Ano: $('#Ano').val(), Trimestre: $('#Trimestre').val(), File: output},
//                    success: function (data, textStatus, jqXHR)
//                    {
//                        $('#myModal').on('hide.bs.modal', function (e) {
//                                window.location.reload();
//                            });
//                            console.log(data)
//                        if (data == 'Enviado') {
//                            
//                            $('#loading-image').hide();
//                            $('.modal-title').html('Salvo com Sucesso')
//                            $("#msg").html("Arquivo Enviado com Sucesso.");
//                            $('#myModal').modal('show');
//                        } else {
//                            $('.modal-title').html('ERRO')
//                            $("#msg").html("Arquivo com erro <BR> Entre em contato com o suporte.");
//                            $('#myModal').modal('show');
//                            console.log('erro');
//                        }
//
//                    },
//                    error: function (jqXHR, textStatus, errorThrown)
//                    {
//                        console.log("erro");
//                    },
//                    complete: function (data) {
//                        //console.log(data);
//
//                    }
//                });
}

//function to check file size before uploading.
function beforeSubmit(e) {

        //check whether browser fully supports all File API
        if (window.File && window.FileReader && window.FileList && window.Blob)
        {

            if (!$('#imageInput').val()) //check empty input filed
            {
                //$("#output").html("Por Favor Insira um Arquivo");
                $("#msg").html("Por favor insira um arquivo");
                $('#myModal').modal('show')
                return false
            }

            var fsize = $('#imageInput')[0].files[0].size; //get file size
            var ftype = $('#imageInput')[0].files[0].name; // get file type

            //Allowed file size is less than 1 MB (1048576) //arquvio phpinfo 20mb
            if (fsize > 20971520)
            {
                //$("#output").html("<b>" + bytesToSize(fsize) + "</b> Arquivo muito grande! <br />Tente compacta-lo com o winrar ou winzip");
                $("#msg").html("<b> Arquivo maior que 20MB </b> <br />Tente compacta-lo com o winrar ou winzip");
                $('#myModal').modal('show')
                return false
            }


            //Progress bar
            progressbox.show(); //show progressbar
            progressbar.width(completed); //initial value 0% of progressbar
            statustxt.html(completed); //set status text
            statustxt.css('color', '#000'); //initial color of status text


            $('#submit-btn').hide(); //hide submit button
            $('#loading-img').show(); //hide submit button
            $("#output").html("");
        } else
        {
            //Output error to older unsupported browsers that doesn't support HTML5 File API
            $("#output").html("Please upgrade your browser, because your current browser lacks some new features we need!");
            return false;
        }
    }

//function to format bites bit.ly/19yoIPO
function bytesToSize(bytes) {
    var sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB'];
    if (bytes == 0)
        return '0 Bytes';
    var i = parseInt(Math.floor(Math.log(bytes) / Math.log(1024)));
    return Math.round(bytes / Math.pow(1024, i), 2) + ' ' + sizes[i];
}

});

