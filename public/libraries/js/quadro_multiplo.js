//arquivo js quadro multiplo - ultima alteração 17/09/2018
//Guilherme Freire

var qtdAcidentesGraves = 0;
var qtdAcidentes = 0;
function validaLatitde() {
	if ($("#Endereco").val() && $("#Bairro").val() && $("#Numero").val() && $("#MunicipioAcidente").val()) {
		getLatitude($("#Endereco").val() + ', ' + $("#Numero").val() + ' - ' + $("#Bairro").val() + ', ' + $("#MunicipioAcidente").val() + ' - ' + $("#EstadoAcidente").val())

	}
}

$('#Filtro_Acidentes').change(function () {
    $('#table').bootstrapTable('removeAll');
    $('#table').bootstrapTable('refresh', {url: './quadro_multiplo.php?Trimestre=' + $('#Trimestre').val() + '&Ano=' + $('#Ano').val() + '&CodCidade=' + $('#CodCidade').val()+ '&FiltroAcidente=' + $('#Filtro_Acidentes').val() });
});

function deleta_vitima($id){
	event.preventDefault();
	alertify.confirm('Tem Certeza?', 'Deseja deletar a vitima', 
		function(){ 
			$.ajax({
				type: "POST",
				data: { acao: 'deleta_vitima', id_vitima: $('#id_vitima'+$id).val() },
				url: 'quadro_multiplo.php',
				success: function(s) {
					$('#Dados_Vitima_FATAL'+$id).remove();
					alertify.success('Deletado com sucesso');
				}
			});
		}, function(){ alertify.error('Cancelado')});
}
function removerAcidente($id){
	event.preventDefault();
	alertify.confirm('Tem Certeza?', 'Deseja deletar o acidente', 
		function(){ 
			$.ajax({
				type: "POST",
				data: { acao: 'deletar_acidente', id_acidente: $id },
				url: 'quadro_multiplo.php',
				success: function(s) {
                    $('#table').bootstrapTable('removeAll');
                    $('#table').bootstrapTable('refresh', {url: './quadro_multiplo.php?Trimestre=' + $('#Trimestre').val() + '&Ano=' + $('#Ano').val() + '&CodCidade=' + $('#CodCidade').val()});
                    alertify.success('Deletado com sucesso');
                }
            });
		}, function(){ alertify.error('Cancelado')});

}

function abreVitimas(qtdAcidentes) {
    //console.log('entrou aqui ' + $("#NumObitos").val() + ' qtdAcidentes ' + qtdAcidentes)
    qtdAcidentes = $('#Dados_Vitima').children().length;
    if (($("#NumObitos").val() !== 0)) {
    	var adicionar = ($("#NumObitos").val() - qtdAcidentes);
    	if ($("#NumObitos").val() > qtdAcidentes) {

    		for (var i = 0; i < adicionar; i++) {
    			$('#Dados_Vitima').append('<div id="Dados_Vitima_FATAL' + qtdAcidentes + '">\n\
    				<input type="hidden" class="form-control" id="id_vitima'+qtdAcidentes+'" value="" ">\n\
    				<div class="row">\n\
    				<div class="col-md-2 form-group">\n\
    				<label for="usr">Tipo Vítima:</label>\n\
    				<select name="TipoVitima_FATAL[]" id="TipoVitima_FATAL' + qtdAcidentes + '" class="form-control" >\n\
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
    				<input type="text" class="form-control" id="VitimaNome_FATAL' + qtdAcidentes + '" name="VitimaNome_FATAL[]">\n\
    				</div>\n\
    				<div class="col-md-4 form-group">\n\
    				<label for="usr">Nome Mãe:</label>\n\
    				<input type="text" class="form-control" id="VitimaNomeMae_FATAL' + qtdAcidentes + '" name="VitimaNomeMae_FATAL[]">\n\
    				</div>\n\
    				<div class="col-md-2 form-group">\n\
    				<label for="usr">Data Nascimento:</label>\n\
    				<input type="text" class="form-control" id="VitimaDataNascimento_FATAL' + qtdAcidentes + '" name="VitimaDataNascimento_FATAL[]">\n\
    				</div>\n\
    				</div>\n\
    				<div class="row">\n\
    				<div class="col-md-2 form-group">\n\
    				<label for="usr">Sexo:</label>\n\
    				<select name="SexoVitima_FATAL[]" id="SexoVitima_FATAL' + qtdAcidentes + '" class="form-control" >\n\
    				<OPTION VALUE=""></OPTION>\N\
    				<OPTION VALUE="MASCULINO">MASCULINO</OPTION>\N\
    				<OPTION VALUE="FEMININO">FEMININO</OPTION>\N\
    				<OPTION VALUE="IGNORADO">IGNORADO</OPTION>\N\
    				<OPTION VALUE="NAO INFORMADO">NÃO INFORMADO</OPTION>\N\
    				</select>\n\
    				</div>\n\
    				<div class="col-md-2 form-group">\n\
    				<label for="usr">Meio de Transporte:</label>\n\
    				<select name="MeioVitima_FATAL[]" id="MeioVitima_FATAL' + qtdAcidentes + '" onchange="MeioVitima_FATAL(event, ' + qtdAcidentes + ');" class="form-control">\n\
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
    				<select name="CondVitima_FATAL[]" id="CondVitima_FATAL' + qtdAcidentes + '" class="form-control" >\n\
    				<OPTION VALUE=""></OPTION>\n\
    				<OPTION VALUE="CONDUTOR">CONDUTOR</OPTION>\n\
    				<OPTION VALUE="PASSAGEIRO">PASSAGEIRO</OPTION>\n\
    				<OPTION VALUE="PEDESTRE">PEDESTRE</OPTION>\n\
    				<OPTION VALUE="NAO INFORMADO">NÃO INFORMADO</OPTION>\n\
    				</select>\n\
    				</div>\n\
    				\n\ <div class="col-md-2 form-group" id="_classAlcool' + qtdAcidentes + '" style="display: none;">\n\
    				<label for="usr">Influência do Álcool?</label>\n\
    				<select name="VitimaInfluencia[]" id="VitimaInfluencia' + qtdAcidentes + '" class="form-control" >\n\
    				<OPTION VALUE=""></OPTION>\N\
    				<OPTION VALUE="SIM">SIM</OPTION>\N\
    				<OPTION VALUE="NAO">NÃO</OPTION>\N\
    				<OPTION VALUE="NAO INFORMADO">NÃO INFORMADO</OPTION>\N\
    				</select>\n\
    				</div>\n\
    				<div class="col-md-2 form-group" id="_classAlcoolemia' + qtdAcidentes + '" style="display: none;">\n\
    				<label for="usr">Alcoolemia?</label>\n\
    				<select name="ComprovaAlcool[]" id="ComprovaAlcool' + qtdAcidentes + '" class="form-control" onChange="exibeValorAlcolemia(' + qtdAcidentes + ');" >\n\
    				<OPTION VALUE=""> </OPTION>\N\
    				<OPTION VALUE="SIM">SIM</OPTION>\N\
    				<OPTION VALUE="NAO">NAO</OPTION>\N\
    				<OPTION VALUE="NAO INFORMADO">NÃO INFORMADO</OPTION>\N\
    				</select>\n\
    				</div>\n\
    				<div class="col-md-2 form-group" id="_valorAlcolemia' + qtdAcidentes + '" style="display: none;">\n\
    				<Label>Valor: </Label>\n\
    				<input type="text" class="form-control" id="ValorAlcoolemia' + qtdAcidentes + '" name="ValorAlcoolemia[]" >\n\
    				</div>\n\
    				<div class="col-md-2 form-group" id="_classBafometro' + qtdAcidentes + '" style="display: none;">\n\
    				<label for="usr">Bafômetro?</label>\n\
    				<select name="ComprovaBafometro[]" id="ComprovaBafometro' + qtdAcidentes + '" onChange="exibeValorBafometro(' + qtdAcidentes + ');" class="form-control">\n\
    				<OPTION VALUE=""> </OPTION>\N\
    				<OPTION VALUE="SIM">SIM</OPTION>\N\
    				<OPTION VALUE="NAO">NAO</OPTION>\N\
    				<OPTION VALUE="NAO INFORMADO">NÃO INFORMADO</OPTION>\N\
    				</select>\n\
    				</div>\n\
                    <div class="col-md-2 form-group" id="_valorBafometro' + qtdAcidentes + '" style="display: none;">\n\
                    <Label>Valor :</Label>\n\
                    <input type="text" class="form-control" id="ValorBafometro' + qtdAcidentes + '" name="ValorBafometro[]"> \n\
                    </div>\n\
    				<div class="col-md-2 form-group" id="CBO' + qtdAcidentes + '">\n\
    				<Label>CBO :</Label>\n\
    				<input type="text" class="form-control" id="CBO' + qtdAcidentes + '" name="CBO[]"> \n\
    				</div>\n\
    				\n\</div>\n\
    				<div class="row">\n\
    				<div class="col-md-2 form-group">\n\
    				<label for="usr">CEP</label>\n\
    				<input id="CEPVitima' + qtdAcidentes + '" type="text" min="0" maxlength="8" class="form-control" name="CEPVitima[]" onChange="buscaVitimaCEP(' + qtdAcidentes + ');">\n\
    				</div>\n\
    				<div class="col-md-3 form-group">\n\
    				<label for="usr">Endereço</label>\n\
    				<input id="EnderecoVitima' + qtdAcidentes + '" type="text" class="form-control" name="EnderecoVitima[]" onChange="validaLatitudeVitima(' + qtdAcidentes + ');">\n\
    				</div>\n\
    				<div class="col-md-2 form-group">\n\
    				<label for="usr">Número</label>\n\
    				<input type="text" class="form-control" name="NumeroVitima[]" id="NumeroVitima' + qtdAcidentes + '" onChange="validaLatitudeVitima(' + qtdAcidentes + ');">\n\
    				</div>\n\
    				\n\    <div class="col-md-2 form-group">\n\
    				<label for="usr">Bairro</label>\n\
    				<input type="text" class="form-control" name="BairroVitima[]" id="BairroVitima' + qtdAcidentes + '" onChange="validaLatitudeVitima(' + qtdAcidentes + ');">\n\
    				</div>\n\
    				<div class="col-md-2 form-group">\n\
    				<label for="usr">Estado</label>\n\
    				<select id="EstadoVitima' + qtdAcidentes + '" class="form-control" name="EstadoVitima[]" class="form-control " onChange="validaLatitudeVitima(' + qtdAcidentes + ');"> \n\
    				<option value="">Selecione o Estado do acidente</option>\n\
    				</select>\n\
    				</div>\n\
    				<div class="col-md-2 form-group">\n\
    				<label for="usr">Município</label>\n\
    				<select id="MunicipioVitima' + qtdAcidentes + '" class="form-control" name="MunicipioVitima[]" class="form-control " onChange="validaLatitudeVitima(' + qtdAcidentes + ');"> \n\
    				<option value="">Selecione o municipio do acidente</option>\n\
    				</select>\n\
    				</div>\n\
    				<div class="col-md-2 form-group">\n\
    				<label for="usr">Coord X</label>\n\
    				<input type="text" class="form-control" name="CoordVitimaX[]" id="CoordVitimaX' + qtdAcidentes + '" >\n\
    				</div>\n\
    				<div class="col-md-2 form-group">\n\
    				<label for="usr">Coord Y</label>\n\
    				<input type="text" class="form-control" name="CoordVitimaY[]" id="CoordVitimaY' + qtdAcidentes + '" >\n\
    				</div>\n\
    				</div>\n\
    				<div class="row">\n\
    				<div class="col-md-4 form-group" >\n\
    				<Label>PAR SIM:</Label>\n\
                    <span id="PAR_VITIMA_'+ qtdAcidentes + '"> </span> <br>\n\
                    <span id="SIM_CAUSABAS_'+ qtdAcidentes + '"> </span> <br>\n\
    				<span id="SIM_DO_'+ qtdAcidentes + '"> </span> <br>\n\
    				</div>\n\
    				<div class="col-md-4 form-group" >\n\
    				<Label>PAR SIH:</Label>\n\
    				<span id="PAR_VITIMA_SIH_'+ qtdAcidentes + '"> </span> <br>\n\
    				<span id="DIAGPRINCIPAL'+ qtdAcidentes + '"> </span> <br>\n\
    				<span id="AIH'+ qtdAcidentes + '"> </span> <br>\n\
    				</div>\n\
    				<div class="col-md-3 form-group" >\n\
    				<a href="#" class="btn btn-default pull-right" onclick="deleta_vitima('+ qtdAcidentes + ')">Deletar Vítima</a>\n\
    				</div>\n\
    				<div class="col-md-12 form-group">\n\
    				<label for="comment">DESCRIÇÃO VITIMA:</label>\n\
    				<textarea class="form-control" rows="5" name="Descricao[]" id="descricao_'+ qtdAcidentes + '"></textarea>\n\
    				</div>\n\
    				</div>\n\
    				<hr class="style3">\n\
    				');


new dgCidadesEstados({
	cidade: document.getElementById(('MunicipioVitima' + qtdAcidentes != 'null'?'MunicipioVitima' + qtdAcidentes : '')),
	estado: document.getElementById(('EstadoVitima' + qtdAcidentes != 'null'?'EstadoVitima' + qtdAcidentes : ''))
});


$('#QuadroMultiplo').formValidation('addField', 'MeioVitima_FATAL[]');
$('#QuadroMultiplo').formValidation('addField', 'TipoVitima_FATAL[]');
$('#QuadroMultiplo').formValidation('addField', 'CondVitima_FATAL[]');
$('#QuadroMultiplo').formValidation('addField', 'VitimaNome_FATAL[]');
$('#QuadroMultiplo').formValidation('addField', 'VitimaNomeMae_FATAL[]');
$('#QuadroMultiplo').formValidation('addField', 'VitimaDataNascimento_FATAL[]');
$('#QuadroMultiplo').formValidation('addField', 'SexoVitima_FATAL[]');
                //datapick
                $('#VitimaDataNascimento_FATAL' + qtdAcidentes).datepicker({
                	language: "pt-BR",
                	autoclose: true,
                	format: 'dd/mm/yyyy',
                	todayHighlight: true,
                	constrainInput: false
                }).on('changeDate', function (e) {
                	if ($(this).val() == "99/99/9999")
                		return true;

                    // Revalidate the date field
                    $('#QuadroMultiplo').formValidation('revalidateField', 'VitimaDataNascimento_FATAL[]');
                });

                qtdAcidentes++;
            }

        } else {
        	var remover = (qtdAcidentes - $("#NumObitos").val());
        	for (var i = 0; i < remover; i++) {
        		$("#Dados_Vitima_FATAL" + (qtdAcidentes - 1)).remove();
        		$("#_VitimaAlcool" + (qtdAcidentes - 1)).remove();
        		qtdAcidentes--;
        	}
        }

    } else {
    	$("#NumObitos").val(0);
    }
    if ($("#NumObitos").val() > 0) {
    	$("#_DadosVitimas").show();
    } else {
    	$("#_DadosVitimas").hide();
    }
    if ($('#Alcool').val() > 0) {
    	for (i = 0; i < $("#NumObitos").val(); i++) {
    		console.log(i)
    		$('#_classAlcool' + i).show();
    		$('#_classAlcoolemia' + i).show();
    		$('#_classBafometro' + i).show();
    	}
    } else {
    	for (i = 0; i < $("#NumObitos").val(); i++) {
    		$('#_classAlcool' + i).hide();
    		$('#_classAlcoolemia' + i).hide();
    		$('#_classBafometro' + i).hide();
    	}
    }
    //qtdAcidentes = $("#NumObitos").val();
}
function buscaVitimaCEP(id) {
	console.log('cep vitima')
    //$('#CEP').val('');
    if ($('#CEPVitima' + id).val() != '') {

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
                    //$("#CEP").val('');
                }
            });
        } //end if.
        else {
            //cep é inválido.
            $("#EnderecoVitima" + id).val("");
            $("#BairroVitima" + id).val("");

            $("#EstadoVitima").val("");
            $("#EstadoVitima" + id).trigger('change');
            $("#MunicipioVitima" + id).val("");
            alert("Formato de CEP inválido.");
            $("#CEPVitima" + id).val('');
        }
    } //end if.
    else {
        //cep sem valor, limpa formulário.
        //limpa_formulário_cep();
    }

}
function validaLatitudeVitima(id) {


	if ($("#EnderecoVitima" + id).val() && $("#BairroVitima" + id).val() && $("#NumeroVitima" + id).val() && $("#MunicipioVitima" + id).val()) {

		var endereco = ($("#Endereco").val() + ', ' + $("#NumeroVitima" + id).val() + ' - ' + $("#BairroVitima" + id).val() + ', ' + $("#MunicipioVitima" + id).val() + ' - ' + $("#EstadoVitima" + id).val());
		var geocoder;
		geocoder = new google.maps.Geocoder();
		geocoder.geocode({'address': endereco + ', Brasil', 'region': 'BR'}, function (results, status) {
			console.log(results)
			if (status == google.maps.GeocoderStatus.OK) {
				if (results[0]) {

					var latitude = results[0].geometry.location.lat();
					var longitude = results[0].geometry.location.lng();
					$("#CoordVitimaX" + id).val(latitude);
					$("#CoordVitimaY" + id).val(longitude);
					$.each(results[0].address_components, function (index, value) {
						if (value.types[0] == 'postal_code') {
							var cep = value.long_name
							if (cep) {
								$("#CEPVitima" + id).val(cep.replace(/-/g, ""));
							}
						}
					});

				}
			}
		})
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
function exibeValorBafometro(id) {
	if ($('#ComprovaBafometro' + id).val() == 'SIM') {
		$('#_valorBafometro' + id).show();
	} else {
		$('#_valorBafometro' + id).hide();
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

function operateFormatter(value, row, index) {

	return '<button type="button" class="btn btn-sm btn-primary" onClick="editar(' + value + ');"><span class="glyphicon glyphicon-edit"></span> Editar</button><button type="button" class="btn btn-sm btn-primary" onClick="removerAcidente(' + value + ');" style="margin-left: 5px;"><span class="glyphicon glyphicon-remove"></span></button>';
}

function cellStyle(value, row, index) {

	if (row.TotalFatores !== null && row.TotalFatores != 0) {
		return {
			classes: 'FatoresPreenchidos'
		};
	}
	if ((row.TotalFatores == null || row.TotalFatores == 0) && row.TotalObitos >= 1) {
		return {
			classes: 'FatoresDevemPreenchidos'
		};
	}
	return {};
}
function queryParams(params) {
	return {
		limit: params.limit,
		Trimestre: $('#Trimestre').val(),
		Ano: $('#Ano').val(),
		CodCidade: $('#CodCidade').val(),
		offset: params.offset,
		search: params.search,
		sort: params.sort,
		order: params.order
	};
}
function resetAllValues() {
    //var qtdAcidentes = 0;
    $("#NumObitos").val(0);
    $('#_QuadroMultiplo div[id^="_"]').hide();
    $('#_QuadroMultiplo').hide();
    $('#_QuadroMultiplo').find('input').val('');
    $('#_QuadroMultiplo').find(".num10").val('');
    $('#_QuadroMultiplo').find("select:not(.num10)").val('');
    $('#_QuadroMultiplo').find(".num11").val('');
    $('#_QuadroMultiplo').find("select:not(.num11)").val('');
    $('#_QuadroMultiplo').find(".num12").val('');
    $('#_QuadroMultiplo').find("select:not(.num12)").val('');
    $('#Dados_Vitima').html('');

    //qtdAcidentesGraves = 0;

    $('#_QuadroMultiplo').find('.has-success').removeClass('has-success')
    $('#_QuadroMultiplo').find('.has-error').removeClass('has-error')
    $('#_QuadroMultiplo').find('.help-block').hide()
    $('#_QuadroMultiplo').find('.fv-icon-no-label').hide()

}
function editar(id) {

    $('#loading').show();
	$('#adicionarAcidente').show();
	$('#HiddenEdit').remove();
	resetAllValues();
	$.ajax(
	{
		url: 'quadro_multiplo.php',
		type: "POST",
		data: {id: id, ajax: 'BuscaEditar', CodCidade: $('#CodCidade').val(), Trimestre: $('#Trimestre').val(), Ano: $('#Ano').val()},
		success: function (data, textStatus, jqXHR)
		{
                    //console.log(data)
                    if (data != 'vazio') {
                        var valores = JSON.parse(data)
                        $('#_QuadroMultiplo').prepend('<input id="HiddenEdit" type="hidden" class="form-control" name="HiddenEdit" value="' + id + '">');
                        if (valores[0]["IdentificadorAcidente"].split('/').length != valores[0]["IdentificadorAcidente"].length && valores[0]["IdentificadorAcidente"].length > 0) {
                           var identificadorAcidente = valores[0]["IdentificadorAcidente"].split(/\/(.+)/);
                           $('input[name=IdentificadorAcidente]').val(identificadorAcidente[1]);
                           $('#FonteDados').val(identificadorAcidente[0]);
                       }
                       if (valores[0]["IdentificadorAcidente2"] != null && valores[0]["IdentificadorAcidente2"].split('/').length != valores[0]["IdentificadorAcidente2"].length && valores[0]["IdentificadorAcidente2"].length > 0) {
                           var identificadorAcidente2 = valores[0]["IdentificadorAcidente2"].split(/\/(.+)/);
                           $('input[name=IdentificadorAcidente2]').val(identificadorAcidente2[1]);
                           $('#FonteDados2').val(identificadorAcidente2[0]);

                       }
                       if (valores[0]["IdentificadorAcidente3"] != null && valores[0]["IdentificadorAcidente3"].split('/').length != valores[0]["IdentificadorAcidente3"].length && valores[0]["IdentificadorAcidente3"].length > 0) {
                         var identificadorAcidente3 = valores[0]["IdentificadorAcidente3"].split(/\/(.+)/);
                         $('input[name=IdentificadorAcidente3]').val(identificadorAcidente3[1]);
                         $('#FonteDados3').val(identificadorAcidente3[0]);

                     }
                     if (valores[0].HoraAcidente == '99') {
                       $('#QuadroMultiplo').formValidation('enableFieldValidators', 'HoraAcidente', false);
                   } else {
                       $('#QuadroMultiplo').formValidation('enableFieldValidators', 'HoraAcidente', true);
                   }
                   $('#DataAcidente').val(valores[0].DataAcidente);
                   $('#HoraAcidente').val(valores[0].HoraAcidente);
                   if(valores[0].TipoAcidente == ''){
                       $('#TipoAcidente').val('NAO INFORMADO');
                   }else{                            
                       $('#TipoAcidente').val(valores[0].TipoAcidente);
                   }
                   $('#NumObitos').val((parseInt(valores[0].TotalObitos) + parseInt(valores[0].TotalGraves)));
                   abreVitimas(0);
                   $('#Endereco').val(valores[0].RuaAvenida);
                   $('#Numero').val(valores[0].Numero);
                   $('#Complemento').val(valores[0].Complemento);
                   $('#velocidade_via').val(valores[0].velocidade_via);
                   $('#Bairro').val(valores[0].Bairro);
                   $('#EstadoAcidente').val(valores[0].EstadoAcidente);
                   $('#EstadoAcidente').trigger('change');
                   $('#MunicipioAcidente option').each(function () {
                       $(this).val(replaceSpecialChars($(this).val().toUpperCase()));
                   });
                   $('#MunicipioAcidente').val(valores[0].CidadeAcidente);
                   $('#MunicipioAcidente').trigger('change');
                   $('#CEP').val(valores[0].CepAcidente);

                        //recupera fatores de risco
                        if (valores[0].FatoresDeRisco !== null) {
                        	$.ajax(
                        	{
                        		url: 'quadro_multiplo.php',
                        		type: "POST",
                        		data: {id: valores[0].FatoresDeRisco, ajax: 'BuscarFatores', idAcidente: identificadorAcidente, CodCidade: $('#CodCidade').val(), Trimestre: $('#Trimestre').val(), Ano: $('#Ano').val()},
                        		success: function (data, textStatus, jqXHR)
                        		{
                                    //console.log(data)
                                    if (data != 'vazio') {
                                     console.log(data)
                                     var fatores = JSON.parse(data);
                                     $('#Velocidade').val(fatores.Velocidade);
                                     $('#Velocidade').trigger("change");
                                     $('#TipoVelocidade').val(fatores.TipoVelocidade);
                                     $('#UsuarioContributivo_Velocidade').val(fatores.UsuarioContributivo_Velocidade);
                                     $('#Alcool').val(fatores.Alcool);
                                     $('#Alcool').trigger("change");
                                     $('#UsuarioContributivo_Alcool').val(fatores.UsuarioContributivo_Alcool);
                                     $('#Veiculo').val(fatores.Veiculo);
                                     $('#Veiculo').trigger("change");
                                     $('#UsuarioContributivo_Veiculo').val(fatores.UsuarioContributivo_Veiculo);
                                     $('#Infraestrutura').val(fatores.Infraestrutura);
                                     $('#Infraestrutura').trigger("change");
                                     $('#TipoInfraestrutura').val(fatores.TipoInfraestrutura);
                                     $('#Fadiga').val(fatores.Fadiga);
                                     $('#Fadiga').trigger("change");
                                     $('#UsuarioContributivo_Fadiga').val(fatores.UsuarioContributivo_Fadiga);

                                     $('#Visibilidade').val(fatores.Visibilidade);
                                     $('#Drogas').val(fatores.Drogas);
                                     $('#Drogas').trigger("change");
                                     $('#UsuarioContributivo_Drogas').val(fatores.UsuarioContributivo_Drogas);
                                     $('#TipoDroga').val(fatores.TipoDroga);

                                     $('#Distacao').val(fatores.Distacao);
                                     $('#Distacao').trigger("change");
                                     $('#UsuarioContributivo_Distacao').val(fatores.UsuarioContributivo_Distacao);

                                     $('#AvancarSinal').val(fatores.AvancarSinal);
                                     $('#AvancarSinal').trigger("change");
                                     $('#UsuarioContributivo_AvancarSinal').val(fatores.UsuarioContributivo_AvancarSinal);

                                     $('#CondutorSemHabilitacao').val(fatores.CondutorSemHabilitacao);
                                     $('#CondutorSemHabilitacao').trigger("change");
                                     $('#UsuarioContributivo_CondutorSemHabilitacao').val(fatores.UsuarioContributivo_CondutorSemHabilitacao);

                                     $('#LocalProibido').val(fatores.LocalProibido);
                                     $('#LocalProibido').trigger("change");
                                     $('#UsuarioContributivo_LocalProibido').val(fatores.UsuarioContributivo_LocalProibido);

                                     $('#LocalImproprio').val(fatores.LocalImproprio);
                                     $('#LocalImproprio').trigger("change");
                                     $('#UsuarioContributivo_LocalImproprio').val(fatores.UsuarioContributivo_LocalImproprio);

                                     $('#MudancaFaixa').val(fatores.MudancaFaixa);
                                     $('#MudancaFaixa').trigger("change");
                                     $('#UsuarioContributivo_MudancaFaixa').val(fatores.UsuarioContributivo_MudancaFaixa);

                                     $('#DistanciaMinima').val(fatores.DistanciaMinima);
                                     $('#DistanciaMinima').trigger("change");
                                     $('#UsuarioContributivo_DistanciaMinima').val(fatores.UsuarioContributivo_DistanciaMinima);

                                     $('#Preferencia').val(fatores.Preferencia);
                                     $('#Preferencia').trigger("change");
                                     $('#UsuarioContributivo_Preferencia').val(fatores.UsuarioContributivo_Preferencia);

                                     $('#PreferenciaPedestre').val(fatores.PreferenciaPedestre);
                                     $('#PreferenciaPedestre').trigger("change");
                                     $('#UsuarioContributivo_PreferenciaPedestre').val(fatores.UsuarioContributivo_PreferenciaPedestre);

                                     $('#ImprudenciaPedestre').val(fatores.ImprudenciaPedestre);
                                     $('#ImprudenciaPedestre').trigger("change");
                                     $('#UsuarioContributivo_ImprudenciaPedestre').val(fatores.UsuarioContributivo_ImprudenciaPedestre);

                                     $('#CintoSeguranca').val(fatores.CintoSeguranca);
                                     $('#CintoSeguranca').trigger("change");
                                     $('#UsuarioContributivo_CintoSeguranca').val(fatores.UsuarioContributivo_CintoSeguranca);

                                     $('#EquipamentoProtecao').val(fatores.EquipamentoProtecao);
                                     $('#GerenciamentoTrauma').val(fatores.GerenciamentoTrauma);
                                     $('#ObjetosLateraisVia').val(fatores.ObjetosLateraisVia);

                                     $('#Capacete').val(fatores.Capacete);
                                     $('#Capacete').trigger("change");
                                     $('#UsuarioContributivo_Capacete').val(fatores.UsuarioContributivo_Capacete);
                                 }
                             },
                             error: function (jqXHR, textStatus, errorThrown)
                             {
                               console.log("erro BuscaEditar");
                           }
                       });

}
var MaeVitimas = '';
var ids_vitimas = '';
var TipoVitimas = '';
var NomesVitimas = '';
var NomesVitimasBusca = '';
var DtNascimentoVitimas = '';
var SexoVitimas = '';
var MeioVitimas = '';
var CondicaoVitimas = '';
var EndVitimas = '';
var BairroVitima = '';
var NumeroVitima = '';
var CEPVitima = '';
var MunicipioVitima = '';
var EstadoVitima = '';
var CoordVitimaX = '';
var CoordVitimaY = '';
var Influencia = '';
var Alcolemia = '';
var Valor_Alcolemia = '';
var Valor_Bafometro = '';
var Bafometro = '';
var LinkagemSIM = '';
var LinkagemSIH = '';
var CAUSABAS_SIM = '';
var DO_SIM = '';
var DIAGPRINCIPAL = '';
var AIH = '';
var Descricao = '';
var Valor_Bafometro = valores[0]["VALOR BAFOMETRO DAS VITIMAS"] + '';
var Valor_Alcolemia = valores[0]["VALOR ALCOLEMIA DAS VITIMAS"] + '';

if (valores[0]["INFLUENCIA DAS VITIMAS"] != null && valores[0]["INFLUENCIA DAS VITIMAS"].split(', ').length != valores[0]["INFLUENCIA DAS VITIMAS"].length && valores[0]["INFLUENCIA DAS VITIMAS"].length > 0) {
   var Influencia = valores[0]["INFLUENCIA DAS VITIMAS"].split(', ');
}
if (valores[0]["ALCOLEMIA DAS VITIMAS"] != null && valores[0]["ALCOLEMIA DAS VITIMAS"].split(', ').length != valores[0]["ALCOLEMIA DAS VITIMAS"].length && valores[0]["ALCOLEMIA DAS VITIMAS"].length > 0) {
   var Alcolemia = valores[0]["ALCOLEMIA DAS VITIMAS"].split(', ');
}
if (valores[0]["VALOR ALCOLEMIA DAS VITIMAS"] != null && valores[0]["VALOR ALCOLEMIA DAS VITIMAS"].split(', ').length != valores[0]["VALOR ALCOLEMIA DAS VITIMAS"].length && valores[0]["VALOR ALCOLEMIA DAS VITIMAS"].length > 0) {

   Valor_Alcolemia = Valor_Alcolemia.split(', ');
} else {
   Valor_Alcolemia = '';
}
if (valores[0]["BAFOMETRO DAS VITIMAS"] != null && valores[0]["BAFOMETRO DAS VITIMAS"].split(', ').length != valores[0]["BAFOMETRO DAS VITIMAS"].length && valores[0]["BAFOMETRO DAS VITIMAS"].length > 0) {
   var Bafometro = valores[0]["BAFOMETRO DAS VITIMAS"].split(', ');
}
if (valores[0]["VALOR BAFOMETRO DAS VITIMAS"] != null && valores[0]["VALOR BAFOMETRO DAS VITIMAS"].split(', ').length != valores[0]["VALOR BAFOMETRO DAS VITIMAS"].length && valores[0]["VALOR BAFOMETRO DAS VITIMAS"].length > 0) {

   Valor_Bafometro = Valor_Bafometro.split(', ');
} else {
   Valor_Bafometro = '';
}
if (valores[0]["ENDERECO DAS VITIMAS"] != null && valores[0]["ENDERECO DAS VITIMAS"].split(', ').length != valores[0]["ENDERECO DAS VITIMAS"].length && valores[0]["ENDERECO DAS VITIMAS"].length > 0) {
   var EndVitimas = valores[0]["ENDERECO DAS VITIMAS"].split(', ');
}
if (valores[0]["BAIRRO DAS VITIMAS"] != null && valores[0]["BAIRRO DAS VITIMAS"].split(', ').length != valores[0]["BAIRRO DAS VITIMAS"].length && valores[0]["BAIRRO DAS VITIMAS"].length > 0) {
   var BairroVitima = valores[0]["BAIRRO DAS VITIMAS"].split(', ');
}
if (valores[0]["NUMERO DAS VITIMAS"] != null && valores[0]["NUMERO DAS VITIMAS"].split(', ').length != valores[0]["NUMERO DAS VITIMAS"].length && valores[0]["NUMERO DAS VITIMAS"].length > 0) {
   var NumeroVitima = valores[0]["NUMERO DAS VITIMAS"].split(', ');
}
if (valores[0]["CEP DAS VITIMAS"] != null && valores[0]["CEP DAS VITIMAS"].split(', ').length != valores[0]["CEP DAS VITIMAS"].length && valores[0]["CEP DAS VITIMAS"].length > 0) {
   var CEPVitima = valores[0]["CEP DAS VITIMAS"].split(', ');
}
if (valores[0]["MUNICIPIO DAS VITIMAS"] != null && valores[0]["MUNICIPIO DAS VITIMAS"].split(', ').length != valores[0]["MUNICIPIO DAS VITIMAS"].length && valores[0]["MUNICIPIO DAS VITIMAS"].length > 0) {
   var MunicipioVitima = valores[0]["MUNICIPIO DAS VITIMAS"].split(', ');
}
if (valores[0]["ESTADO DAS VITIMAS"] != null && valores[0]["ESTADO DAS VITIMAS"].split(', ').length != valores[0]["ESTADO DAS VITIMAS"].length && valores[0]["ESTADO DAS VITIMAS"].length > 0) {
   var EstadoVitima = valores[0]["ESTADO DAS VITIMAS"].split(', ');
}
if (valores[0]["COORDX DAS VITIMAS"] != null && valores[0]["COORDX DAS VITIMAS"].split(', ').length != valores[0]["COORDX DAS VITIMAS"].length && valores[0]["COORDX DAS VITIMAS"].length > 0) {
   var CoordVitimaX = valores[0]["COORDX DAS VITIMAS"].split(', ');
}
if (valores[0]["COORDY DAS VITIMAS"] != null && valores[0]["COORDY DAS VITIMAS"].split(', ').length != valores[0]["COORDY DAS VITIMAS"].length && valores[0]["COORDY DAS VITIMAS"].length > 0) {
   var CoordVitimaY = valores[0]["COORDY DAS VITIMAS"].split(', ');
}

if (valores[0]["TIPO DAS VITIMAS"] != null && valores[0]["TIPO DAS VITIMAS"].split(', ').length != valores[0]["TIPO DAS VITIMAS"].length && valores[0]["TIPO DAS VITIMAS"].length > 0) {
   var TipoVitimas = valores[0]["TIPO DAS VITIMAS"].split(', ');
}
if (valores[0]["NOME DAS VITIMAS"] != null && valores[0]["NOME DAS VITIMAS"].split(', ').length != valores[0]["NOME DAS VITIMAS"].length && valores[0]["NOME DAS VITIMAS"].length > 0) {
   var NomesVitimas = valores[0]["NOME DAS VITIMAS"].split(', ');
}
if (valores[0]["NOME DA MAE DAS VITIMAS"] != null && valores[0]["NOME DA MAE DAS VITIMAS"].split(', ').length != valores[0]["NOME DA MAE DAS VITIMAS"].length && valores[0]["NOME DA MAE DAS VITIMAS"].length > 0) {
   var MaeVitimas = valores[0]["NOME DA MAE DAS VITIMAS"].split(', ');
}
if (valores[0]["DT NASCIMENTO DAS VITIMAS"] != null && valores[0]["DT NASCIMENTO DAS VITIMAS"].split(', ').length != valores[0]["DT NASCIMENTO DAS VITIMAS"].length && valores[0]["DT NASCIMENTO DAS VITIMAS"].length > 0) {
   var DtNascimentoVitimas = valores[0]["DT NASCIMENTO DAS VITIMAS"].split(', ');
}
if (valores[0]["SEXO VITIMAS"] != null && valores[0]["SEXO VITIMAS"].split(', ').length != valores[0]["SEXO VITIMAS"].length && valores[0]["SEXO VITIMAS"].length > 0) {
   var SexoVitimas = valores[0]["SEXO VITIMAS"].split(', ');
}
if (valores[0]["MEIO DE TRANSPORTE DAS VITIMAS"] != null && valores[0]["MEIO DE TRANSPORTE DAS VITIMAS"].split(', ').length != valores[0]["MEIO DE TRANSPORTE DAS VITIMAS"].length && valores[0]["MEIO DE TRANSPORTE DAS VITIMAS"].length > 0) {
   var MeioVitimas = valores[0]["MEIO DE TRANSPORTE DAS VITIMAS"].split(', ');
}
if (valores[0]["CODICAO DAS VITIMAS"] != null && valores[0]["CODICAO DAS VITIMAS"].split(', ').length != valores[0]["CODICAO DAS VITIMAS"].length && valores[0]["CODICAO DAS VITIMAS"].length > 0) {
   var CondicaoVitimas = valores[0]["CODICAO DAS VITIMAS"].split(', ');
}
if (valores[0]["LinkagemSIM"] != null && valores[0]["LinkagemSIM"].split(', ').length != valores[0]["LinkagemSIM"].length && valores[0]["LinkagemSIM"].length > 0) {
   var LinkagemSIM = valores[0]["LinkagemSIM"].split(', ');
}
if (valores[0]["CAUSABAS_SIM"] != null && valores[0]["CAUSABAS_SIM"].split(', ').length != valores[0]["CAUSABAS_SIM"].length && valores[0]["CAUSABAS_SIM"].length > 0) {
   var CAUSABAS_SIM = valores[0]["CAUSABAS_SIM"].split(', ');
}
if (valores[0]["DO_SIM"] != null && valores[0]["DO_SIM"].split(', ').length != valores[0]["DO_SIM"].length && valores[0]["DO_SIM"].length > 0) {
   var DO_SIM = valores[0]["DO_SIM"].split(', ');
}
if (valores[0]["DIAGPRINCIPAL"] != null && valores[0]["DIAGPRINCIPAL"].split(', ').length != valores[0]["DIAGPRINCIPAL"].length && valores[0]["DIAGPRINCIPAL"].length > 0) {
   var DIAGPRINCIPAL = valores[0]["DIAGPRINCIPAL"].split(', ');
}
if (valores[0]["AIH"] != null && valores[0]["AIH"].split(', ').length != valores[0]["AIH"].length && valores[0]["AIH"].length > 0) {
   var AIH = valores[0]["AIH"].split(', ');
}
if (valores[0]["LinkagemSIH"] != null && valores[0]["LinkagemSIH"].split(', ').length != valores[0]["LinkagemSIH"].length && valores[0]["LinkagemSIH"].length > 0) {
   var LinkagemSIH = valores[0]["LinkagemSIH"].split(', ');
}
if(valores[0]["Descricao"] != null){
   if (valores[0]["Descricao"].split('**--!!!--** ').length != valores[0]["Descricao"].length && valores[0]["Descricao"].length > 0) {
      var Descricao = valores[0]["Descricao"].split('**--!!!--** ');
  }
}

if (valores[0]["ids_vitimas"] != null && valores[0]["ids_vitimas"].split(', ').length != valores[0]["ids_vitimas"].length && valores[0]["ids_vitimas"].length > 0) {
   var ids_vitimas = valores[0]["ids_vitimas"].split(', ');
}
                        //console.log(Valor_Bafometro)
                        $.each(TipoVitimas, function (key, val) {

                        	qtdAcidentes++;
                        	if (Influencia[key] != 'undefined') {
                        		$('#VitimaInfluencia' + key).val(Influencia[key]);
                        		$('#VitimaInfluencia' + key).trigger('change');
                        	}
                        	if (Alcolemia[key] != 'undefined') {
                        		$('#ComprovaAlcool' + key).val(Alcolemia[key]);
                        		$('#ComprovaAlcool' + key).trigger('change');
                        	}
                        	if (Valor_Alcolemia[key] != 'undefined') {
                        		$('#ValorAlcoolemia' + key).val(Valor_Alcolemia[key]);
                        	}
                        	if (Bafometro[key] != 'undefined') {
                        		$('#ComprovaBafometro' + key).val(Bafometro[key]);
                        		$('#ComprovaBafometro' + key).trigger('change');
                        	}
                        	if (Valor_Bafometro[key] != 'undefined') {
                        		$('#ValorBafometro' + key).val(Valor_Bafometro[key]);
                        	}
                        	if (EndVitimas[key] != 'undefined') {
                        		$('#EnderecoVitima' + key).val(EndVitimas[key]);
                        	}
                        	if (CEPVitima[key] != 'undefined') {
                        		$('#CEPVitima' + key).val(CEPVitima[key]);
                        	}
                        	if (BairroVitima[key] != 'undefined') {
                        		$('#BairroVitima' + key).val(BairroVitima[key]);
                        	}
                        	if (NumeroVitima[key] != 'undefined') {
                        		$('#NumeroVitima' + key).val(NumeroVitima[key]);
                        	}

                        	if (EstadoVitima[key] != 'undefined' && EstadoVitima[key] != 'null') {
                        		$('#EstadoVitima' + key).val(EstadoVitima[key]).trigger('change');
                        		$('#MunicipioVitima' + key).val(MunicipioVitima[key]).trigger('change');

                        	}
                        	if (MunicipioVitima[key] != 'undefined' && MunicipioVitima[key] != 'null') {
                        		$('#MunicipioVitima' + key).val(MunicipioVitima[key]).trigger('change');
                                // $('#MunicipioVitima' + key).trigger('change');
                            }
                            if (CoordVitimaX[key] != 'undefined') {
                            	$('#CoordVitimaX' + key).val(CoordVitimaX[key]);
                            }
                            if (CoordVitimaY[key] != 'undefined') {
                            	$('#CoordVitimaY' + key).val(CoordVitimaY[key]);
                            }

                            if (TipoVitimas[key] != 'undefined') {
                            	$('#TipoVitima_FATAL' + key).val(TipoVitimas[key]);
                            }
                            if (ids_vitimas[key] != 'undefined') {
                            	$('#id_vitima' + key).val(ids_vitimas[key]);
                            }
                            if (NomesVitimas[key] != 'undefined') {
                            	$('#VitimaNome_FATAL' + key).val(NomesVitimas[key]);
                            	NomesVitimasBusca += '"' + NomesVitimas[key] + '",';
                            }
                            if (MaeVitimas[key] != 'undefined') {
                            	$('#VitimaNomeMae_FATAL' + key).val(MaeVitimas[key]);

                            }
                            if (DtNascimentoVitimas[key] != 'undefined') {
                            	$('#VitimaDataNascimento_FATAL' + key).val(DtNascimentoVitimas[key]);
                            }
                            if (SexoVitimas[key] != 'undefined') {
                            	$('#SexoVitima_FATAL' + key).val(SexoVitimas[key]);
                            }
                            if (MeioVitimas[key] != 'undefined') {
                            	$('#MeioVitima_FATAL' + key).val(MeioVitimas[key]);
                            }
                            if (CondicaoVitimas[key] != 'undefined') {
                            	$('#CondVitima_FATAL' + key).val(CondicaoVitimas[key]);
                            }
                            if (LinkagemSIM[key] != 'undefined') {
                                $('#PAR_VITIMA_' + key).html(LinkagemSIM[key]);
                            }
                            if (CAUSABAS_SIM[key] != 'undefined') {
                                $('#SIM_CAUSABAS_' + key).html('<label> CAUSABASE: </label> '+ CAUSABAS_SIM[key]);
                            }
                            if (DO_SIM[key] != 'undefined') {
                                $('#SIM_DO_' + key).html('<label> DO: </label> '+ DO_SIM[key]);
                            }
                            if (DIAGPRINCIPAL[key] != 'undefined') {
                                $('#DIAGPRINCIPAL' + key).html('<label> CAUSABASE: </label> '+ DIAGPRINCIPAL[key]);
                            }
                            if (AIH[key].toLowerCase() != 'undefined') {
                                $('#AIH' + key).html('<label> AIH: </label> '+ AIH[key]);
                            }
                            if (LinkagemSIH[key] != 'undefined') {
                            	$('#PAR_VITIMA_SIH_' + key).html(LinkagemSIH[key]);
                            }
                            if (Descricao[key] != 'undefined') {
                            	$('#descricao_' + key).val(Descricao[key]);
                            }

                        })

$('#_QuadroMultiplo').show();
$('#_QuadroMultiplo').focus();
$('#loading').hide();

qtdAcidentes = 0;
} else {
	console.log('registro sem dados');

}
},
error: function (jqXHR, textStatus, errorThrown)
{
	console.log("erro BuscaEditar");
}
});

}

function detailFormatter(index, row) {
	var html = [];
	$.each(row, function (key, value) {
		html.push('<p><b>' + key + ':</b> ' + value + '</p>');
	});
	return html.join('');
}
function MeioVitima_FATAL(event, quantidade) {

	if ($('#MeioVitima_FATAL' + quantidade).val() === 'PEDESTRE') {
		$('#CondVitima_FATAL' + quantidade).val('PEDESTRE');
		$('#CondVitima_FATAL' + quantidade).parent().append('<input id="HiddenCondVitima_FATAL' + quantidade + '" type="hidden" class="form-control" name="CondVitima_FATAL[]" value="PEDESTRE">')
		$('#CondVitima_FATAL' + quantidade).attr('disabled', true);
		$('#CondVitima_FATAL' + quantidade).parent().removeClass('has-feedback has-error fv-has-tooltip');
		$('#CondVitima_FATAL' + quantidade).next().remove();
	} else {
		$('#CondVitima_FATAL' + quantidade).val('');
		$('#CondVitima_FATAL' + quantidade).attr('disabled', false);
		$('#QuadroMultiplo').formValidation('addField', 'CondVitima_FATAL[]');
	}
    //$('#QuadroMultiplo').data('formValidation').resetForm();
}
function CondVitima_FATAL(event, quantidade) {
	if ($('#CondVitima_FATAL' + quantidade).val() == 'PEDESTRE') {
		$('#MeioVitima_FATAL' + quantidade).val('PEDESTRE');
		$('#MeioVitima_FATAL' + quantidade).parent().append('<input id="HiddenCondVitima_GRAVE' + quantidade + '" type="hidden" class="form-control" name="CondVitima_GRAVE[]" value="PEDESTRE">')
		$('#MeioVitima_FATAL' + quantidade).attr('disabled', true);
		$('#MeioVitima_FATAL' + quantidade).parent().removeClass('has-feedback has-error fv-has-tooltip');
		$('#MeioVitima_FATAL' + quantidade).next().remove();
	}
    //$('#QuadroMultiplo').data('formValidation').resetForm();
}
$('#Ano').change(function () {
	resetAllValues()
	if ($('#Ano').val() < 2015) {
		$('#Ano').val('');
		$('#Ano').focus();
	} else {
		if ($('#Ano').val() !== '' && $('#Trimestre').val() !== '' && $('#CodCidade').val() !== '') {
			resetAllValues();
			$('#titulo-painel').html('Acidentes do período ' + $('#Ano').val() + ' / ' + $('#Trimestre').val());
			$('#adicionarAcidente').show();
			$('#table').bootstrapTable('removeAll');
            //$('#table').bootstrapTable('refresh', {url: './quadro_multiplo.php'});
            $('#table').bootstrapTable('refresh', {url: './quadro_multiplo.php?Trimestre=' + $('#Trimestre').val() + '&Ano=' + $('#Ano').val() + '&CodCidade=' + $('#CodCidade').val()});
            $('#_tabelaResults').show();
        } else {
        	$('#_tabelaResults').hide();
        }
    }

});

function buscaCEP(id) {

    //$('#CEP').val('');
    if (($('#CEPVitima').val() === '') && ($('#Endereco').val() !== '') && ($('#EstadoAcidente').val() !== '') && ($('#MunicipioAcidente').val() !== null) && ($('#MunicipioAcidente').val() !== '') && ($('#Bairro').val() !== '')) {
    	var EstadoCep = replaceSpecialChars($('#EstadoAcidente').val().toUpperCase());
    	var RuaCep = replaceSpecialChars($('#Endereco').val().toUpperCase().replace(/QUILOMETRO |KM |ESTACAO |EST |VILA |PRAIA |PR |PRQ |LARGO |LGO |LADEIRA |LD |RUA |PRACA |AV |PC |VIADUTO |VD |AV |ALAMEDA |AL |ALM |BECO |BC |R |AVENIDA /g, ''));
    	var Bairro = replaceSpecialChars($('#Bairro').val().toUpperCase().toUpperCase());
    	var MunicipioCep = $('#MunicipioAcidente').val().toUpperCase();
    	var URL = encodeURI("https://viacep.com.br/ws/" + EstadoCep + "/" + MunicipioCep + "/" + RuaCep + "/json/");
        // console.log(URL);
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
            },
            error: function (data, xhr, ajaxOptions, thrownError) {
            	console.log(xhr.statusText);
            	console.log(thrownError);
            	console.log(data);
            }
        });
    }


}
$('#Endereco').change(function () {
    //$('#CEP').val('');
    if (($('#CEP').val() === '') && ($('#Endereco').val() !== '') && ($('#EstadoAcidente').val() !== '') && ($('#MunicipioAcidente').val() !== null) && ($('#MunicipioAcidente').val() !== '') && ($('#Bairro').val() !== '')) {
    	var EstadoCep = replaceSpecialChars($('#EstadoAcidente').val().toUpperCase());
    	var RuaCep = replaceSpecialChars($('#Endereco').val().toUpperCase().replace(/QUILOMETRO |KM |ESTACAO |EST |VILA |PRAIA |PR |PRQ |LARGO |LGO |LADEIRA |LD |RUA |PRACA |AV |PC |VIADUTO |VD |AV |ALAMEDA |AL |ALM |BECO |BC |R |AVENIDA /g, ''));
    	var Bairro = replaceSpecialChars($('#Bairro').val().toUpperCase().toUpperCase());
    	var MunicipioCep = $('#MunicipioAcidente').val().toUpperCase();
    	var URL = encodeURI("https://viacep.com.br/ws/" + EstadoCep + "/" + MunicipioCep + "/" + RuaCep + "/json/");
        // console.log(URL);
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
            },
            error: function (data, xhr, ajaxOptions, thrownError) {
            	console.log(xhr.statusText);
            	console.log(thrownError);
            	console.log(data);
            }
        });
    }

});




$('#Bairro').change(function () {
    //$('#CEP').val('');
    if (($('#CEP').val() === '') && ($('#Endereco').val() !== '') && ($('#EstadoAcidente').val() !== '') && ($('#MunicipioAcidente').val() !== null) && ($('#MunicipioAcidente').val() !== '') && ($('#Bairro').val() !== '')) {
    	var EstadoCep = replaceSpecialChars($('#EstadoAcidente').val().toUpperCase());
    	var RuaCep = replaceSpecialChars($('#Endereco').val().toUpperCase().replace(/QUILOMETRO |KM |ESTACAO |EST |VILA |PRAIA |PR |PRQ |LARGO |LGO |LADEIRA |LD |RUA |PRACA |AV |PC |VIADUTO |VD |AV |ALAMEDA |AL |ALM |BECO |BC |R |AVENIDA /g, ''));
    	var Bairro = replaceSpecialChars($('#Bairro').val().toUpperCase().toUpperCase());
    	var MunicipioCep = $('#MunicipioAcidente').val().toUpperCase();
    	var URL = encodeURI("https://viacep.com.br/ws/" + EstadoCep + "/" + MunicipioCep + "/" + RuaCep + "/json/");
        //console.log(URL);
        $.ajax({
            // url para o arquivo json.php
            url: URL,
            // dataType json
            dataType: "json",
            // função para de sucesso
            success: function (data) {
            	console.log(data)
            	$.each(data, function (i, item) {
            		if (Bairro == replaceSpecialChars(data[i].bairro.toUpperCase())) {
            			var CEP = data[i].cep;
                        //$('#CEP').val('');
                        $('#CEP').val(CEP.replace('-', ''));
                    }
                })
            },
            error: function (data, xhr, ajaxOptions, thrownError) {
            	console.log(xhr.statusText);
            	console.log(thrownError);
            	console.log(data);
            }
        });
    }

});
$('#EstadoAcidente').change(function () {
    //$('#CEP').val('');
    if (($('#CEP').val() === '') && ($('#Endereco').val() !== '') && ($('#EstadoAcidente').val() !== '') && ($('#MunicipioAcidente').val() !== null) && ($('#MunicipioAcidente').val() !== '') && ($('#Bairro').val() !== '')) {
    	var EstadoCep = replaceSpecialChars($('#EstadoAcidente').val().toUpperCase());
    	var RuaCep = replaceSpecialChars($('#Endereco').val().toUpperCase().replace(/QUILOMETRO |KM |ESTACAO |EST |VILA |PRAIA |PR |PRQ |LARGO |LGO |LADEIRA |LD |RUA |PRACA |AV |PC |VIADUTO |VD |AV |ALAMEDA |AL |ALM |BECO |BC |R |AVENIDA /g, ''));
    	var Bairro = replaceSpecialChars($('#Bairro').val().toUpperCase().toUpperCase());
    	var MunicipioCep = $('#MunicipioAcidente').val().toUpperCase();
    	var URL = encodeURI("https://viacep.com.br/ws/" + EstadoCep + "/" + MunicipioCep + "/" + RuaCep + "/json/");
        // console.log(URL);
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
                        //$('#CEP').val('');
                        $('#CEP').val(CEP.replace('-', ''));
                    }
                })
            },
            error: function (data, xhr, ajaxOptions, thrownError) {
            	console.log(xhr.statusText);
            	console.log(thrownError);
            	console.log(data);
            }
        });
    }

});
$('#MunicipioAcidente').change(function () {
	$('#CEP').val('');
	if (($('#CEP').val() === '') && ($('#Endereco').val() !== '') && ($('#EstadoAcidente').val() !== '') && ($('#MunicipioAcidente').val() !== null) && ($('#MunicipioAcidente').val() !== '') && ($('#Bairro').val() !== '')) {
		var EstadoCep = replaceSpecialChars($('#EstadoAcidente').val().toUpperCase());
		var RuaCep = replaceSpecialChars($('#Endereco').val().toUpperCase().replace(/QUILOMETRO |KM |ESTACAO |EST |VILA |PRAIA |PR |PRQ |LARGO |LGO |LADEIRA |LD |RUA |PRACA |AV |PC |VIADUTO |VD |AV |ALAMEDA |AL |ALM |BECO |BC |R |AVENIDA /g, ''));
		var Bairro = replaceSpecialChars($('#Bairro').val().toUpperCase().toUpperCase());
		var MunicipioCep = $('#MunicipioAcidente').val().toUpperCase();
		var URL = encodeURI("https://viacep.com.br/ws/" + EstadoCep + "/" + MunicipioCep + "/" + RuaCep + "/json/");
        //console.log(URL);
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
            },
            error: function (data, xhr, ajaxOptions, thrownError) {
            	console.log(xhr.statusText);
            	console.log(thrownError);
            	console.log(data);
            }
        });
    }
});



$('#Trimestre').change(function () {
	resetAllValues()
	if ($('#Ano').val() !== '' && $('#Trimestre').val() !== '' && $('#CodCidade').val() !== '') {
		resetAllValues();
		$('#titulo-painel').html('Acidentes do período ' + $('#Ano').val() + ' / ' + $('#Trimestre').val());
		$('#adicionarAcidente').show();
		$('#table').bootstrapTable('removeAll');
        //$('#table').bootstrapTable('refresh', {url: './quadro_multiplo.php'});
        $('#table').bootstrapTable('refresh', {url: './quadro_multiplo.php?Trimestre=' + $('#Trimestre').val() + '&Ano=' + $('#Ano').val() + '&CodCidade=' + $('#CodCidade').val()});
        $('#_tabelaResults').show();
    } else {
    	$('#_tabelaResults').hide();
    }
});
$('#DataAcidente').datepicker({
	language: "pt-BR",
	autoclose: true,
	format: 'dd/mm/yyyy',
	todayHighlight: true
}).on('changeDate', function (e) {
    // Revalidate the date field
    $('#QuadroMultiplo').formValidation('revalidateField', 'DataAcidente');
});
$('#Cidade').change(function () {
	resetAllValues()
	$.ajax(
	{
		url: 'mun_ibge.php',
		type: "POST",
		data: {Cidade: this.value, Estado: $('#Estado').val()},
		success: function (data, textStatus, jqXHR)
		{

			$("#CodCidade").val(data);
			if ($('#Ano').val() !== '' && $('#Trimestre').val() !== '' && $('#CodCidade').val() !== '') {
				resetAllValues();
				$('#titulo-painel').html('Acidentes do período ' + $('#Ano').val() + ' / ' + $('#Trimestre').val());
				$('#adicionarAcidente').show();
				$('#table').bootstrapTable('removeAll');
                //$('#table').bootstrapTable('refresh', {url: './quadro_multiplo.php'});
                $('#table').bootstrapTable('refresh', {url: './quadro_multiplo.php?Trimestre=' + $('#Trimestre').val() + '&Ano=' + $('#Ano').val() + '&CodCidade=' + $('#CodCidade').val()});
                $('#_tabelaResults').show();
            } else {
            	$('#_tabelaResults').hide();
            }
        },
        error: function (jqXHR, textStatus, errorThrown)
        {
        	console.log("erro cidade");
        }
    });

});

$('#adicionarAcidente').click(function () {
	resetAllValues();
	$('#_QuadroMultiplo').show();
	$('#_QuadroMultiplo').focus();
	$('#adicionarAcidente').hide();
});




$(document).ready(function () {
	var qtdAcidentes = 0;
	var cinto_seguranca = 0;
	var veiculoSemEquip = 0;
	var GerenciamentoTrauma = 0;
	var ObjLateral = 0;
	var Capacete = 0;
	var $table = $('#table'),
	$tableExport = $("#Export_table"),
	$Export = $("#Export"),
	$button = $('.btn-export'),
	$PDF = $('#PDF'),
	$CSV = $('#CSV'),
	$EXCEL = $('#EXCEL');
	$IMAGEM = $('#IMAGEM');
	$(function () {
		$button.click(function (e) {
			var tdbody = $("#Export")
			$('.loading-spinner').addClass('active');
            //console.log($(this).hasClass('PDF'))
            e.preventDefault();
            var dataExportTable = $table.bootstrapTable('getData', false);
            //var dataExport = $tableExport.bootstrapTable('getData', false);
//            if (_.isEqual(dataExport, dataExportTable) == false) {
//                tdbody.html('');
//                $.each(dataExportTable, function (i, item) {
//                    tdbody.append('<tr>\n\
//                        <td>' + item.Vitima + '</td>\n\
//                        <td>' + item.NomeCompleto + '</td>\n\
//                        <td>' + item.NomeMae + '</td>\n\
//                        <td>' + item.DataNascimento + '</td>\n\
//                        <td>' + item.DataAcidente + '</td>\n\
//                        <td>' + item.Sexo + '</td>\n\
//                        <td>' + item.MeioTransporte + '</td>\n\
//                        <td>' + item.CondicaoVitima + '</td>\n\
//                        <td>' + item.EnderecoVitima + ' ' + item.NumeroVitima + ' - ' + item.BairroVitima + ' ' + item.MunicipioVitima + '/' + item.EstadoVitima + '</td>\n\
//                        <td>' + item.NUMSUS + '</td>\n\
//                    </tr>');
//                })
//
//            }
if ($(this).hasClass('PDF')) {
	var columns = [
	{title: "Acidente", dataKey: "IdentificadorAcidente"},
	{title: "Vitima", dataKey: "Vitima"},
	{title: "Nome Completo", dataKey: "NomeCompleto"},
	{title: "Data Nascimento", dataKey: "DataNascimento"},
	{title: "Data Acidente", dataKey: "DataAcidente"},
	{title: "Sexo", dataKey: "Sexo"},
	{title: "MeioTransporte", dataKey: "MeioTransporte"},
	{title: "CondicaoVitima", dataKey: "CondicaoVitima"},
                    //{title: "EnderecoVitima", dataKey: "EnderecoVitima" + ' ' + "NumeroVitima"}
                    ];
                    var rows = dataExportTable;
                    var doc = new jsPDF('l', 'pt');
                    doc.autoTable(columns, rows, {
                    	startY: 20,
                    	margin: {horizontal: 7},
                    	styles: {columnWidth: 'wrap'},
                    	columnStyles: {text: {columnWidth: 'auto'}}
                    });
                    doc.save('ListaUnica.pdf');
                }
                if ($(this).hasClass('CSV')) {
                	$('#Export_table').tableExport({type: 'csv',
                		displayTableName: true,
                		tableName: 'RelatórioVítimas' + $('#Ano').val() + '-' + $('#Trimestre').val(),
                		fileName: 'RelatórioVítimas' + $('#Ano').val() + '-' + $('#Trimestre').val()
                	});
                }
                if ($(this).hasClass('EXCEL')) {
                	testJson = dataExportTable;
//                console.log(dataExportTable)
//                console.log(Object.keys(dataExportTable[0]))
testTypes = {
	"IdentificadorAcidente": "String",
	"DataAcidente": "String",
	"TipoAcidente": "String",
	"HoraAcidente": "String",
	"TotalGraves": "String",
	"TotalObitos": "String",
	"RuaAvenida": "String",
	"Numero": "String",
	"Bairro": "String",
	"CidadeAcidente": "String",
	"EstadoAcidente": "String",
	"TIPO DAS VITIMAS": "String",
	"NOME DAS VITIMAS": "String",
	"NOME DA MAE DAS VITIMAS": "String",
	"DT NASCIMENTO DAS VITIMAS": "String",
	"SEXO VITIMAS": "String",
	"MEIO DE TRANSPORTE DAS VITIMAS": "String",
	"CODICAO DAS VITIMAS": "String",
	"CEP DAS VITIMAS": "String",
	"ENDERECO DAS VITIMAS": "String",
	"NUMERO DAS VITIMAS": "String",
	"BAIRRO DAS VITIMAS": "String",
	"MUNICIPIO DAS VITIMAS": "String",
	"ESTADO DAS VITIMAS": "String",
	"COORDX DAS VITIMAS": "String"
};
emitXmlHeader = function () {
	var headerRow = '<ss:Row>\n';
	$.each(Object.keys(dataExportTable[0]), function (index, value) {
		headerRow += '  <ss:Cell>\n';
		headerRow += '    <ss:Data ss:Type="String">';
		headerRow += value + '</ss:Data>\n';
		headerRow += '  </ss:Cell>\n';
	})

	headerRow += '</ss:Row>\n';
	return '<?xml version="1.0"?>\n' +
	'<ss:Workbook xmlns:ss="urn:schemas-microsoft-com:office:spreadsheet">\n' +
	'<ss:Worksheet ss:Name="ListaUnica">\n' +
	'<ss:Table>\n\n' + headerRow;
};
emitXmlFooter = function () {
	return '\n</ss:Table>\n' +
	'</ss:Worksheet>\n' +
	'</ss:Workbook>\n';
};
jsonToSsXml = function (jsonObject) {
	var row;
	var col;
	var xml;
	var data = typeof jsonObject != "object" ? JSON.parse(jsonObject) : jsonObject;
	xml = emitXmlHeader();
	for (row = 0; row < data.length; row++) {
		xml += '<ss:Row>\n';
		for (col in data[row]) {
			xml += '  <ss:Cell>\n';
			xml += '    <ss:Data ss:Type="String">';
			xml += data[row][col] + '</ss:Data>\n';
			xml += '  </ss:Cell>\n';
		}

		xml += '</ss:Row>\n';
	}

	xml += emitXmlFooter();
	return xml;
};
console.log(jsonToSsXml(testJson));
download = function (content, filename, contentType) {
	if (!contentType)
		contentType = 'application/octet-stream';
	var a = document.getElementById('btnEXCEL');
	var blob = new Blob([content], {
		'type': contentType
	});
	a.href = window.URL.createObjectURL(blob);
	a.download = filename;
};
var saveData = (function () {
	var a = document.createElement("a");
	document.body.appendChild(a);
	a.style = "display: none";
	return function (data, fileName) {
		var blob = new Blob([data], {type: "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet"}),
		url = window.URL.createObjectURL(blob);
		a.href = url;
		a.download = fileName;
		a.click();
		window.URL.revokeObjectURL(url);
	};
}());

saveData(jsonToSsXml(testJson), 'QUADROMULTIPLO_'+ $('#Ano').val() + '-' + $('#Trimestre').val()+'.xls');
                //download(jsonToSsXml(testJson), 'test.xls', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
                //$('#btnEXCEL').click();
            }
            if ($(this).hasClass('IMAGEM')) {
            	$('#Export_table').tableExport({type: 'png',
            		fileName: 'RelatórioQUADRO' + $('#Ano').val() + '-' + $('#Trimestre').val()
            	});
            }

            $('.loading-spinner').removeClass('active');
        });
});
function validaLatitde() {
	if ($("#Endereco").val() && $("#Bairro").val() && $("#Numero").val() && $("#MunicipioAcidente").val()) {
		getLatitude($("#Endereco").val() + ', ' + $("#Numero").val() + ' - ' + $("#Bairro").val() + ', ' + $("#MunicipioAcidente").val() + ' - ' + $("#EstadoAcidente").val())

	}
}
    //var qtdAcidentes = 0;
    var dt = new Date();
    $('#QuadroMultiplo').formValidation({
    	framework: 'bootstrap',
    	icon: {
    		valid: 'glyphicon glyphicon-ok',
    		invalid: 'glyphicon glyphicon-remove',
    		validating: 'glyphicon glyphicon-refresh'
    	},
    	err: {
    		container: 'tooltip'
    	},
    	row: {
    		selector: 'td'
    	},
    	fields: {
    		'IdentificadorAcidente': {
    			validators: {
    				notEmpty: {
    					message: 'Campo Obrigatório'
    				}
    			}
    		},
    		'TipoAcidente': {
    			validators: {
    				notEmpty: {
    					message: 'Campo Obrigatório'
    				}
    			}
    		},
    		'UsuarioContributivo_Distacao': {
    			enabled: false,
    			icon: false,
    			validators: {
    				notEmpty: {
    					message: 'Campo Obrigatório'
    				}
    			}
    		},
    		'UsuarioContributivo_Fadiga': {
    			enabled: false,
    			icon: false,
    			validators: {
    				notEmpty: {
    					message: 'Campo Obrigatório'
    				}
    			}
    		},
    		'Ano': {
    			icon: false,
    			validators: {
    				notEmpty: {
    					message: 'Campo Obrigatório'
    				}
    			}
    		},
    		'UsuarioContributivo_Drogas': {
    			enabled: false,
    			icon: false,
    			validators: {
    				notEmpty: {
    					message: 'Campo Obrigatório'
    				}
    			}
    		},
    		'UsuarioContributivo_Veiculo': {
    			enabled: false,
    			icon: false,
    			validators: {
    				notEmpty: {
    					message: 'Campo Obrigatório'
    				}
    			}
    		},
    		'UsuarioContributivo_Alcool': {
    			enabled: false,
    			icon: false,
    			validators: {
    				notEmpty: {
    					message: 'Campo Obrigatório'
    				}
    			}
    		},
    		'UsuarioContributivo_Velocidade': {
    			enabled: false,
    			icon: false,
    			validators: {
    				notEmpty: {
    					message: 'Campo Obrigatório'
    				}
    			}
    		},
    		'UsuarioContributivo_AvancarSinal': {
    			enabled: false,
    			icon: false,
    			validators: {
    				notEmpty: {
    					message: 'Campo Obrigatório'
    				}
    			}
    		},
    		'UsuarioContributivo_CondutorSemHabilitacao': {
    			enabled: false,
    			icon: false,
    			validators: {
    				notEmpty: {
    					message: 'Campo Obrigatório'
    				}
    			}
    		},
    		'UsuarioContributivo_LocalProibido': {
    			enabled: false,
    			icon: false,
    			validators: {
    				notEmpty: {
    					message: 'Campo Obrigatório'
    				}
    			}
    		},
    		'UsuarioContributivo_LocalImproprio': {
    			enabled: false,
    			icon: false,
    			validators: {
    				notEmpty: {
    					message: 'Campo Obrigatório'
    				}
    			}
    		},
    		'UsuarioContributivo_MudancaFaixa': {
    			enabled: false,
    			icon: false,
    			validators: {
    				notEmpty: {
    					message: 'Campo Obrigatório'
    				}
    			}
    		},
    		'UsuarioContributivo_DistanciaMinima': {
    			enabled: false,
    			icon: false,
    			validators: {
    				notEmpty: {
    					message: 'Campo Obrigatório'
    				}
    			}
    		},
    		'UsuarioContributivo_Preferencia': {
    			enabled: false,
    			icon: false,
    			validators: {
    				notEmpty: {
    					message: 'Campo Obrigatório'
    				}
    			}
    		},
    		'UsuarioContributivo_PreferenciaPedestre': {
    			enabled: false,
    			icon: false,
    			validators: {
    				notEmpty: {
    					message: 'Campo Obrigatório'
    				}
    			}
    		},
    		'UsuarioContributivo_ImprudenciaPedestre': {
    			enabled: false,
    			icon: false,
    			validators: {
    				notEmpty: {
    					message: 'Campo Obrigatório'
    				}
    			}
    		},
    		'UsuarioContributivo_CintoSeguranca': {
    			enabled: false,
    			icon: false,
    			validators: {
    				notEmpty: {
    					message: 'Campo Obrigatório'
    				}
    			}
    		},
    		'UsuarioContributivo_Capacete': {
    			enabled: false,
    			icon: false,
    			validators: {
    				notEmpty: {
    					message: 'Campo Obrigatório'
    				}
    			}
    		},
    		'VitimaNome_FATAL[]': {
    			validators: {
    				notEmpty: {
    					message: 'Campo Obrigatório'
    				}
    			}
    		},
    		'VitimaNomeMae_FATAL[]': {
    			enabled: false,
    			validators: {
    				notEmpty: {
    					message: 'Campo Obrigatório'
    				}
    			}
    		},
    		'Endereco': {
    			enabled: false,
    			validators: {
    				notEmpty: {
    					message: 'Campo Obrigatório'
    				}
    			}
    		},
    		'FonteDados': {
    			validators: {
    				notEmpty: {
    					message: 'Campo Obrigatório'
    				}
    			}
    		},
    		'CEP': {
    			enabled: false,
    			validators: {
    				notEmpty: {
    					message: 'Campo Obrigatório'
    				}
    			}
    		},
    		'VitimaDataNascimento_FATAL[]': {
    			validators: {
//                    date: {
//                        format: 'DD/MM/YYYY',
//                        min: '01/01/1900',
//                        max: dt.toLocaleDateString(),
//                        message: 'Data de Nascimento Inválida'
//                    },
//                    notEmpty: {
//                        message: 'Campo Obrigatório'
//                    },
callback: {
	message: 'Data de Nascimento Inválida',
	callback: function (value, validator, $field) {
		if (value === '99/99/9999') {
			return {valid: true,
				message: 'Data de Nascimento Inválida'}
			} else {
				return moment(value, 'DD/MM/YYYY', true).isValid()
			}
		}
	}
}
},
HoraAcidente: {
	verbose: false,
	validators: {
		message: 'Hora Inválida',
		between: {
			min: 0,
			max: 23,
			message: 'A Hora deve ser entre 0 e 23'
		}
	}
},
'DataAcidente': {
	validators: {
		date: {
			format: 'DD/MM/YYYY',
                        //max: dt.toLocaleDateString(),
                        //min: '01/01/2000',
                        message: 'Data do Acidente Inválida'
                    },
                    notEmpty: {
                    	message: 'Campo Obrigatório'
                    }
                }
            },
            'SexoVitima_FATAL[]': {
            	validators: {
            		notEmpty: {
            			message: 'Campo Obrigatório'
            		}
            	}
            },
            'SexoVitima_GRAVE[]': {
            	validators: {
            		notEmpty: {
            			message: 'Campo Obrigatório'
            		}
            	}
            },
            'NumVitimas': {
            	validators: {
            		notEmpty: {
            			message: 'Campo Obrigatório'
            		},
            		between: {
            			min: 1,
            			max: 20,
            			message: 'Insira um Óbito ou um Ferido Grave'
            		}
            	}
            },
            'TipoVitima_FATAL[]': {
            	validators: {
            		notEmpty: {
            			message: 'Campo Obrigatório'
            		},
            	}
            },
            'MeioVitima_FATAL[]': {
            	validators: {
            		notEmpty: {
            			message: 'Campo Obrigatório'
            		},
            	}
            },
            'CondVitima_FATAL[]': {
            	validators: {
            		notEmpty: {
            			message: 'Campo Obrigatório'
            		},
            	}
            },
            'MeioVitima_GRAVE[]': {
            	validators: {
            		notEmpty: {
            			message: 'Campo Obrigatório'
            		},
            	}
            },
            'CondVitima_GRAVE[]': {
            	validators: {
            		notEmpty: {
            			message: 'Campo Obrigatório'
            		},
            	}
            },
            'TipoVelocidade': {
            	enabled: false,
            	icon: false,
            	validators: {
            		notEmpty: {
            			message: 'Campo Obrigatório'
            		},
            	}
            },
            'Cidade': {
            	enabled: false,
            	validators: {
            		notEmpty: {
            			message: 'Campo Obrigatório'
            		},
            	}
            },
            'TipoInfraestrutura': {
            	enabled: false,
            	icon: false,
            	validators: {
            		notEmpty: {
            			message: 'Campo Obrigatório'
            		},
            	}
            },
            'NumObitos': {
            	validators: {
            		notEmpty: {
            			message: 'Campo Obrigatório'
            		},
            		between: {
            			min: 1,
            			max: 300,
            			message: 'Você deve adicionar pelo menos uma vítima'
            		}
            	}

            },
        }

    })
.on('success.form.fv', function (e) {

                // Prevent form submission
                e.preventDefault();
                var $form = $(e.target),
                fv = $form.data('formValidation');

                $('input[type=text]').val(function () {
                	return replaceSpecialChars(this.value);
                })/*
                $('.1').prop('disabled', false);
                $('.2').prop('disabled', false);
                $('.3').prop('disabled', false);
                $('.4').prop('disabled', false);
                $('.5').prop('disabled', false);
                $('.6').prop('disabled', false);
                $('.8').prop('disabled', false);
                $('.10').prop('disabled', false);*/
                var retorno = 1;
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
                //console.log(inputs1)
                var inputs = new Array();
                $('.num11').each(function () {
                    //console.log($(this).val())
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
                //console.log(inputs1)
                var inputs12 = new Array();
                $('.num12').each(function () {
                    //console.log($(this).val())
                    if ((this.value != undefined) && (this.value != null) && (this.value != '0') && (this.value != '')) {
                    	if ($.inArray(this.value, inputs12) != -1)
                    	{
                    		$('.num12').focus()
                    		alertify.alert('Verifique os pesos das Condutas locais de risco', 'os valores dos pesos não podem ser repetidos, com exceção do valor 0');
                    		retorno = 0;
                    	}
                    	inputs12.push(this.value);
                    }

                });
                //console.log(inputs)




                if (retorno == 1) {
                    // Use Ajax to submit form data
                    $.ajax({
                    	url: 'quadro_multiplo.php',
                    	type: 'POST',
                    	data: $form.serialize(),
                    	success: function (result) {
                            console.log(result)
                            if (result.indexOf("gravado") >= 0 ) {
                            	resetAllValues();
                            	qtdAcidentes = 0;
                            	$form.data('formValidation').resetForm();

                            	$('#adicionarAcidente').show();
                                $('#table').bootstrapTable('removeAll');
                                $('#table').bootstrapTable('refresh', {url: './quadro_multiplo.php'});
                                $('#_tabelaResults').show();
                                $('#HiddenEdit').remove();
                                alertify.success('Gravado com sucesso');
                            } else {
                              alertify.error('erro ao gravar');
                              console.log(result);
                          }
                      }
                  });
                } else {
                	$('#QuadroMultiplo').data('formValidation').resetForm();
                }
            })


//    $('#NumObitos').on('change', function () {
//        // Revalidate the fields
//        $('#QuadroMultiplo')
//                .formValidation('revalidateField', 'NumGraves');
//    });


$("#NumObitos").on('change', function () {
	abreVitimas(qtdAcidentes);
});
$("#HoraAcidente").on('change', function () {
	if ($("#HoraAcidente").val() == '99') {
		$('#QuadroMultiplo').formValidation('enableFieldValidators', 'HoraAcidente', false);
	} else {
		$('#QuadroMultiplo').formValidation('enableFieldValidators', 'HoraAcidente', true);
	}
});
var infra = 0;
$('#Infraestrutura').change(function () {
/*        $("." + infra).attr("disabled", false);
        if ($('#Infraestrutura').val() != '0' && $('#Infraestrutura').val() != null) {
            if (infra != $('#Infraestrutura').val()) {
                $("." + $('#Infraestrutura').val()).attr("disabled", true);
            }
        }*/
        infra = $("#Infraestrutura option:selected").text();
        if ($('#Infraestrutura').val() != '0' && $('#Infraestrutura').val() != null) {
        	$('#_TipoInfraestrutura').show();
        	$('#QuadroMultiplo').formValidation('enableFieldValidators', 'TipoInfraestrutura', true);
        } else {
        	$('#_TipoInfraestrutura').hide();
        	$('#QuadroMultiplo').formValidation('enableFieldValidators', 'TipoInfraestrutura', false);

        }
    });
var velo = 0;
$('#Velocidade').change(function () {

	if ($('#Velocidade').val() != '0' && $('#Velocidade').val() != null) {
		$('#_TipoVelocidade').show();
		$('#_UsuarioContributivo_Velocidade').show();
		$('#QuadroMultiplo').formValidation('enableFieldValidators', 'TipoVelocidade', true);
		$('#QuadroMultiplo').formValidation('enableFieldValidators', 'UsuarioContributivo_Velocidade', true);
	} else {
		$('#_TipoVelocidade').hide();
		$('#_UsuarioContributivo_Velocidade').hide();
		$('#QuadroMultiplo').formValidation('enableFieldValidators', 'UsuarioContributivo_Velocidade', false);
		$('#QuadroMultiplo').formValidation('enableFieldValidators', 'TipoVelocidade', false);

	}
});

var alcoo = 0;
$('#Alcool').change(function () {
/*        $("." + alcoo).attr("disabled", false);
        if ($('#Alcool').val() != '0') {
            if (alcoo != $('#Alcool').val()) {
                $("." + $('#Alcool').val()).attr("disabled", true);
            }
        }*/
        alcoo = $("#Alcool option:selected").text();
        if ($('#Alcool').val() != '0' && $('#Alcool').val() != null) {
        	$('#_UsuarioContributivo_Alcool').show();
        	var i = 0;
        	var total = $('#NumObitos').val();
        	for (i = 0; i < total; i++) {
        		$('#_classAlcool' + i).show();
        		$('#_classAlcoolemia' + i).show();
        		$('#_classBafometro' + i).show();
        	}
        	$('#QuadroMultiplo').formValidation('enableFieldValidators', 'UsuarioContributivo_Alcool', true);

        } else {
        	var i = 0;
        	var total = $('#NumObitos').val();
        	for (i = 0; i < total; i++) {
        		$('#_classAlcool' + i).hide();
        		$('#_classAlcoolemia' + i).hide();
        		$('#_classBafometro' + i).hide();
        	}
        	$('#_UsuarioContributivo_Alcool').hide();
        	$('#QuadroMultiplo').formValidation('enableFieldValidators', 'UsuarioContributivo_Alcool', false);
        }
    });
var veicul = 0;
$('#Veiculo').change(function () {
/*        $("." + veicul).attr("disabled", false);
        if ($('#Veiculo').val() != '0') {
            if (veicul != $('#Veiculo').val()) {
                $("." + $('#Veiculo').val()).attr("disabled", true);
            }
        }*/
        veicul = $("#Veiculo option:selected").text();
        if ($('#Veiculo').val() != '0' && $('#Veiculo').val() != null) {
        	$('#_UsuarioContributivo_Veiculo').show();
        	$('#QuadroMultiplo').formValidation('enableFieldValidators', 'UsuarioContributivo_Veiculo', true);
        } else {
        	$('#_UsuarioContributivo_Veiculo').hide();
        	$('#QuadroMultiplo').formValidation('enableFieldValidators', 'UsuarioContributivo_Veiculo', false);
        }
    });
var fadiga = 0;
$('#Fadiga').change(function () {
/*        $("." + fadiga).attr("disabled", false);
        if ($('#Fadiga').val() != '0') {
            if (fadiga != $('#Fadiga').val()) {
                $("." + $('#Fadiga').val()).attr("disabled", true);
            }
        }*/
        fadiga = $("#Fadiga option:selected").text();
        if ($('#Fadiga').val() != '0'  && $('#Fadiga').val() != null) {
        	$('#_UsuarioContributivo_Fadiga').show();
        	$('#QuadroMultiplo').formValidation('enableFieldValidators', 'UsuarioContributivo_Fadiga', true);
        } else {
        	$('#_UsuarioContributivo_Fadiga').hide();
        	$('#QuadroMultiplo').formValidation('enableFieldValidators', 'UsuarioContributivo_Fadiga', false);
        }
    });
var visibilidade = 0;
$('#Visibilidade').change(function () {
/*        $("." + visibilidade).attr("disabled", false);
        if ($('#Visibilidade').val() != '0') {
            if (visibilidade != $('#Visibilidade').val()) {
                $("." + $('#Visibilidade').val()).attr("disabled", true);
            }
        }*/
        visibilidade = $("#Visibilidade option:selected").text();
//    if ($('#Visibilidade').val() != '0') {
//        $('#_UsuarioContributivo_Visibilidade').show();
//        $('#QuadroMultiplo').formValidation('enableFieldValidators', 'UsuarioContributivo_Visibilidade', true);
//    } else {
//        $('#_UsuarioContributivo_Visibilidade').hide();
//        $('#QuadroMultiplo').formValidation('enableFieldValidators', 'UsuarioContributivo_Visibilidade', false);
//    }
});
var drogas = 0;
$('#Drogas').change(function () {
/*        $("." + drogas).attr("disabled", false);
        if ($('#Drogas').val() != '0') {
            if (drogas != $('#Drogas').val()) {
                $("." + $('#Drogas').val()).attr("disabled", true);
            }
        }*/
        drogas = $("#Drogas option:selected").text();
        if ($('#Drogas').val() != '0'  && $('#Drogas').val() != null) {
        	$('#_UsuarioContributivo_Drogas').show();
        	$('#QuadroMultiplo').formValidation('enableFieldValidators', 'UsuarioContributivo_Drogas', true);
        } else {
        	$('#_UsuarioContributivo_Drogas').hide();
        	$('#QuadroMultiplo').formValidation('enableFieldValidators', 'UsuarioContributivo_Drogas', false);
        }
    });
var distracao = 0;
$('#Distacao').change(function () {
/*        $("." + distracao).attr("disabled", false);
        if ($('#Distacao').val() != '0') {
            if (distracao != $('#Distacao').val()) {
                $("." + $('#Distacao').val()).attr("disabled", true);
            }
        }*/
        distracao = $("#Distacao option:selected").text();
        if ($('#Distacao').val() != '0'  && $('#Distacao').val() != null) {
        	$('#_UsuarioContributivo_Distacao').show();
        	$('#QuadroMultiplo').formValidation('enableFieldValidators', 'UsuarioContributivo_Distacao', true);
        } else {
        	$('#_UsuarioContributivo_Distacao').hide();
        	$('#QuadroMultiplo').formValidation('enableFieldValidators', 'UsuarioContributivo_Distacao', false);
        }
    });
var avanca = 0;
$('#AvancarSinal').change(function () {
/*        $("." + avanca).attr("disabled", false);
        if ($('#AvancarSinal').val() != '0') {
            if (avanca != $('#AvancarSinal').val()) {
                $("." + $('#AvancarSinal').val()).attr("disabled", true);
            }
        }*/
        avanca = $("#AvancarSinal option:selected").text();
        if ($('#AvancarSinal').val() != '0'  && $('#AvancarSinal').val() != null) {
        	$('#_UsuarioContributivo_AvancarSinal').show();
        	$('#QuadroMultiplo').formValidation('enableFieldValidators', 'UsuarioContributivo_AvancarSinal', true);
        } else {
        	$('#_UsuarioContributivo_AvancarSinal').hide();
        	$('#QuadroMultiplo').formValidation('enableFieldValidators', 'UsuarioContributivo_AvancarSinal', false);
        }
    });
var condutorSem = 0;
$('#CondutorSemHabilitacao').change(function () {
/*        $("." + condutorSem).attr("disabled", false);
        if ($('#CondutorSemHabilitacao').val() != '0') {
            if (condutorSem != $('#CondutorSemHabilitacao').val()) {
                $("." + $('#CondutorSemHabilitacao').val()).attr("disabled", true);
            }
        }*/
        condutorSem = $("#CondutorSemHabilitacao option:selected").text();
        if ($('#CondutorSemHabilitacao').val() != '0' && $('#CondutorSemHabilitacao').val() != null) {
        	$('#_UsuarioContributivo_CondutorSemHabilitacao').show();
        	$('#QuadroMultiplo').formValidation('enableFieldValidators', 'UsuarioContributivo_CondutorSemHabilitacao', true);
        } else {
        	$('#_UsuarioContributivo_CondutorSemHabilitacao').hide();
        	$('#QuadroMultiplo').formValidation('enableFieldValidators', 'UsuarioContributivo_CondutorSemHabilitacao', false);
        }
    });
var localProibido = 0;
$('#LocalProibido').change(function () {
/*        $("." + localProibido).attr("disabled", false);
        if ($('#LocalProibido').val() != '0') {
            if (localProibido != $('#LocalProibido').val()) {
                $("." + $('#LocalProibido').val()).attr("disabled", true);
            }
        }*/
        localProibido = $("#LocalProibido option:selected").text();
        if ($('#LocalProibido').val() != '0'  && $('#LocalProibido').val() != null) {
        	$('#_UsuarioContributivo_LocalProibido').show();
        	$('#QuadroMultiplo').formValidation('enableFieldValidators', 'UsuarioContributivo_LocalProibido', true);
        } else {
        	$('#_UsuarioContributivo_LocalProibido').hide();
        	$('#QuadroMultiplo').formValidation('enableFieldValidators', 'UsuarioContributivo_LocalProibido', false);
        }
    });
var LocalImproprio = 0;
$('#LocalImproprio').change(function () {
/*        $("." + LocalImproprio).attr("disabled", false);
        if ($('#LocalImproprio').val() != '0') {
            if (LocalImproprio != $('#LocalImproprio').val()) {
                $("." + $('#LocalImproprio').val()).attr("disabled", true);
            }
        }*/
        LocalImproprio = $("#LocalImproprio option:selected").text();
        if ($('#LocalImproprio').val() != '0' && $('#LocalImproprio').val() != null) {
        	$('#_UsuarioContributivo_LocalImproprio').show();
        	$('#QuadroMultiplo').formValidation('enableFieldValidators', 'UsuarioContributivo_LocalImproprio', true);
        } else {
        	$('#_UsuarioContributivo_LocalImproprio').hide();
        	$('#QuadroMultiplo').formValidation('enableFieldValidators', 'UsuarioContributivo_LocalImproprio', false);
        }
    });
var faixa = 0;
$('#MudancaFaixa').change(function () {
/*        $("." + faixa).attr("disabled", false);
        if ($('#MudancaFaixa').val() != '0') {
            if (faixa != $('#MudancaFaixa').val()) {
                $("." + $('#MudancaFaixa').val()).attr("disabled", true);
            }
        }*/
        faixa = $("#MudancaFaixa option:selected").text();
        if ($('#MudancaFaixa').val() != '0' && $('#MudancaFaixa').val() != null) {
        	$('#_UsuarioContributivo_MudancaFaixa').show();
        	$('#QuadroMultiplo').formValidation('enableFieldValidators', 'UsuarioContributivo_MudancaFaixa', true);
        } else {
        	$('#_UsuarioContributivo_MudancaFaixa').hide();
        	$('#QuadroMultiplo').formValidation('enableFieldValidators', 'UsuarioContributivo_MudancaFaixa', false);
        }
    });
var distancia = 0;
$('#DistanciaMinima').change(function () {
/*        $("." + distancia).attr("disabled", false);
        if ($('#DistanciaMinima').val() != '0') {
            if (distancia != $('#DistanciaMinima').val()) {
                $("." + $('#DistanciaMinima').val()).attr("disabled", true);
            }
        }*/
        distancia = $("#DistanciaMinima option:selected").text();
        if ($('#DistanciaMinima').val() != '0' && $('#DistanciaMinima').val() != null) {
        	$('#_UsuarioContributivo_DistanciaMinima').show();
        	$('#QuadroMultiplo').formValidation('enableFieldValidators', 'UsuarioContributivo_DistanciaMinima', true);
        } else {
        	$('#_UsuarioContributivo_DistanciaMinima').hide();
        	$('#QuadroMultiplo').formValidation('enableFieldValidators', 'UsuarioContributivo_DistanciaMinima', false);
        }
    });
var Preferencia = 0;
$('#Preferencia').change(function () {
/*        $("." + Preferencia).attr("disabled", false);
        if ($('#Preferencia').val() != '0') {
            if (Preferencia != $('#Preferencia').val()) {
                $("." + $('#Preferencia').val()).attr("disabled", true);
            }
        }*/
        Preferencia = $("#Preferencia option:selected").text();
        if ($('#Preferencia').val() != '0' && $('#Preferencia').val() != null) {
        	$('#_UsuarioContributivo_Preferencia').show();
        	$('#QuadroMultiplo').formValidation('enableFieldValidators', 'UsuarioContributivo_Preferencia', true);
        } else {
        	$('#_UsuarioContributivo_Preferencia').hide();
        	$('#QuadroMultiplo').formValidation('enableFieldValidators', 'UsuarioContributivo_Preferencia', false);
        }
    });
var PreferenciaPedestre = 0;
$('#PreferenciaPedestre').change(function () {
/*        $("." + PreferenciaPedestre).attr("disabled", false);
        if ($('#PreferenciaPedestre').val() != '0') {
            if (PreferenciaPedestre != $('#PreferenciaPedestre').val()) {
                $("." + $('#PreferenciaPedestre').val()).attr("disabled", true);
            }
        }*/
        if ($('#PreferenciaPedestre').val() != '0' && $('#PreferenciaPedestre').val() != null) {
        	$('#_UsuarioContributivo_PreferenciaPedestre').show();
        	$('#QuadroMultiplo').formValidation('enableFieldValidators', 'UsuarioContributivo_PreferenciaPedestre', true);
        } else {
        	$('#_UsuarioContributivo_PreferenciaPedestre').hide();
        	$('#QuadroMultiplo').formValidation('enableFieldValidators', 'UsuarioContributivo_PreferenciaPedestre', false);
        }
        PreferenciaPedestre = $("#PreferenciaPedestre option:selected").text();
    });
var imprudenciaPedestre = 0;
$('#ImprudenciaPedestre').change(function () {
/*        $("." + imprudenciaPedestre).attr("disabled", false);
        if ($('#ImprudenciaPedestre').val() != '0') {
            if (imprudenciaPedestre != $('#ImprudenciaPedestre').val()) {
                $("." + $('#ImprudenciaPedestre').val()).attr("disabled", true);
            }
        }*/
        if ($('#ImprudenciaPedestre').val() != '0' && $('#ImprudenciaPedestre').val() != null) {
        	$('#_UsuarioContributivo_ImprudenciaPedestre').show();
        	$('#QuadroMultiplo').formValidation('enableFieldValidators', 'UsuarioContributivo_ImprudenciaPedestre', true);
        } else {
        	$('#_UsuarioContributivo_ImprudenciaPedestre').hide();
        	$('#QuadroMultiplo').formValidation('enableFieldValidators', 'UsuarioContributivo_ImprudenciaPedestre', false);
        }
        imprudenciaPedestre = $("#ImprudenciaPedestre option:selected").text();
    });


$('#GerenciamentoTrauma').change(function () {
/*        $("." + GerenciamentoTrauma).attr("disabled", false);
        if ($('#GerenciamentoTrauma').val() != '0') {
            if (GerenciamentoTrauma != $('#GerenciamentoTrauma').val()) {
                $("." + $('#GerenciamentoTrauma').val()).attr("disabled", true);
            }
        }*/
        GerenciamentoTrauma = $("#GerenciamentoTrauma option:selected").text();
    });

$('#ObjetosLateraisVia').change(function () {
/*        $("." + ObjLateral).attr("disabled", false);
        if ($('#ObjetosLateraisVia').val() != '0') {
            if (ObjLateral != $('#ObjetosLateraisVia').val()) {
                $("." + $('#ObjetosLateraisVia').val()).attr("disabled", true);
            }
        }*/
        ObjLateral = $("#ObjetosLateraisVia option:selected").text();
    });
$('#Capacete').change(function () {
	/*        $("." + Capacete).attr("disabled", false);*/
	if ($('#Capacete').val() != '0' && $('#Capacete').val() != null) {
/*            if (Capacete != $('#Capacete').val()) {
                $("." + $('#Capacete').val()).attr("disabled", true);
            }*/
            $('#_UsuarioContributivo_Capacete').show();
            $('#QuadroMultiplo').formValidation('enableFieldValidators', 'UsuarioContributivo_Capacete', true);
        } else {
        	$('#_UsuarioContributivo_Capacete').hide();
        	$('#QuadroMultiplo').formValidation('enableFieldValidators', 'UsuarioContributivo_Capacete', false);
        }
        Capacete = $("#Capacete option:selected").text();
    });
$('#outra_protecao').change(function () {
	/*        $("." + Capacete).attr("disabled", false);*/
	if ($('#outra_protecao').val() != '0' && $('#outra_protecao').val() != null) {
/*            if (Capacete != $('#Capacete').val()) {
                $("." + $('#Capacete').val()).attr("disabled", true);
            }*/
            $('#_definicao_outra_protecao').show();
        } else {
        	$('#_definicao_outra_protecao').hide();
        }
        outra_protecao = $("#outra_protecao option:selected").text();
    });
$('#EquipamentoProtecao').change(function () {
	/*        $("." + veiculoSemEquip).attr("disabled", false);*/
/*        if ($('#EquipamentoProtecao').val() != '0') {
            if (veiculoSemEquip != $('#EquipamentoProtecao').val()) {
                $("." + $('#EquipamentoProtecao').val()).attr("disabled", true);
            }
        }*/
        veiculoSemEquip = $("#EquipamentoProtecao option:selected").text();
    });
$('#CintoSeguranca').change(function () {
/*
$("." + cinto_seguranca).attr("disabled", false);*/
if ($('#CintoSeguranca').val() != '0' && $('#CintoSeguranca').val() != null) {
/*            if (cinto_seguranca != $('#CintoSeguranca').val()) {
                $("." + $('#CintoSeguranca').val()).attr("disabled", true);
            }*/
            $('#_UsuarioContributivo_CintoSeguranca').show();
            $('#QuadroMultiplo').formValidation('enableFieldValidators', 'UsuarioContributivo_CintoSeguranca', true);
        } else {
        	$('#_UsuarioContributivo_CintoSeguranca').hide();
        	$('#QuadroMultiplo').formValidation('enableFieldValidators', 'UsuarioContributivo_CintoSeguranca', false);
        }
        cinto_seguranca = $("#CintoSeguranca option:selected").text();
    });

function limpa_formulário_cep() {
        // Limpa valores do formulário de cep.
        $("#Endereco").val("");
        $("#Bairro").val("");
        $("#EstadoAcidente").trigger('change');
        $("#EstadoAcidente").val("");
        $("#MunicipioAcidente").val("");
        $("#CEP").val("");
        //$("#ibge").val("");
    }

    //Quando o campo cep perde o foco.
    $("#CEP").change(function () {

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


                //Consulta o webservice viacep.com.br/
                $.getJSON("//viacep.com.br/ws/" + cep + "/json/?callback=?", function (dados) {
                    //console.log(dados)
                    if (!("erro" in dados)) {
                        //Atualiza os campos com os valores da consulta.
                        $("#Endereco").val(dados.logradouro);
                        $("#Bairro").val(dados.bairro);
                        $("#EstadoAcidente").val(dados.uf);
                        $("#EstadoAcidente").trigger('change');
                        $("#MunicipioAcidente").val(dados.localidade.toUpperCase());
                        //$("#ibge").val(dados.ibge);
                    } //end if.
                    else {
                        //CEP pesquisado não foi encontrado.
                        limpa_formulário_cep();
                        alert("CEP não encontrado.");
                        //$("#CEP").val('');
                    }
                });
            } //end if.
            else {
                //cep é inválido.
                limpa_formulário_cep();
                alert("Formato de CEP inválido.");
                //$("#CEP").val('');
            }
        } //end if.
        else {
            //cep sem valor, limpa formulário.
            //limpa_formulário_cep();
        }
    });




});

