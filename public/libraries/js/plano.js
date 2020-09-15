//arquivo js plano de acao - ultima alteração 08/08/2016
//Guilherme Freire
var educacao = 1;
var fiscalizacao = 1;
var engenharia = 1;
var SDMC = 1;
var especiais = 1;
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
function editar(id) {
	$('#adicionarAcidente').show();
	$('#HiddenEdit').remove();
	$('#salvar').hide();
	$('#adicionarPlano').show();
	resetAllValues();
	$('#idPlano').val(id);
	$.ajax(
	{
		url: 'plano.php',
		type: "POST",
		data: {id: id, ajax: 'BuscaEditar', CodCidade: $('#CodCidade').val(), Trimestre: $('#Trimestre').val(), Ano: $('#Ano').val()},
		success: function (data, textStatus, jqXHR)
		{
                    //console.log(data)
                    if (data != 'vazio') {
                    	console.log(data)
                    	var valores = JSON.parse(data)
                    	$("input[name=NomePrograma]").val(valores[0].NomePrograma);
                    	$("input[name=PesoPrograma]").val(valores[0].PesoPrograma);
                    	$("textarea[name=ObjetivoPrograma]").val(valores[0].ObjetivoPrograma);
                    	$("input[name=IndicadorIntermediarioPrograma]").val(valores[0].IndicadorIntermediarioPrograma);
                    	$("input[name=MetaIntermediaria]").val(valores[0].MetaIntermediaria);
                    	$("input[name=MetaIntermediariaDescritiva]").val(valores[0].MetaIntermediariaDescritiva);
                    	$("input[name=IndicadorFinalPrograma]").val(valores[0].IndicadorFinalPrograma);
                    	$("input[name=MetaFinal]").val(valores[0].MetaFinal);
                    	$("input[name=MetaFinalDescritiva]").val(valores[0].MetaFinalDescritiva);
                    	$("input[name=CoordenadorPrograma]").val(valores[0].CoordenadorPrograma);
                    	$("input[name=ParceriasPublicas]").val(valores[0].ParceriasPublicas);
                    	$("input[name=ParceriasPrivadas]").val(valores[0].ParceriasPrivadas);
                    	$("input[name=ParceriasCivil]").val(valores[0].ParceriasCivil);
                    	$("textarea[name=SecretariasEnvolvidas]").val(valores[0].SecretariasEnvolvidas);
                    	$("#_Plano").show();



                    }
                },
                error: function (jqXHR, textStatus, errorThrown)
                {
                	console.log("erro BuscaEditar");
                }
            });

}


function operateFormatter(value, row, index) {
	return '<button type="button" class="btn btn-sm btn-primary" onClick="editar(' + value + ');"><span class="glyphicon glyphicon-edit"></span> Editar</button>';
}
function queryParams(params) {
    //console.log(this);
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
function detailFormatter(index, row) {
	var html = [];
	$.each(row, function (key, value) {
		html.push('<p><b>' + key + ':</b> ' + value + '</p>');
	});
	return html.join('');
}
$('#Ano').change(function () {
	if ($('#Ano').val() < 2015) {
		$('#Ano').val('');
		$('#Ano').focus();
	} else {
		if ($('#Ano').val() !== '' && $('#CodCidade').val() !== '') {
			$('#titulo-painel').html('Planos de Ações do período ' + $('#Ano').val() + ' / ' + $('#Trimestre').val());
			$('#adicionarPlano').show();
			$('#table').bootstrapTable('removeAll');
			$('#table').bootstrapTable('refresh', {url: './plano.php'});
            //$('#table').bootstrapTable('refresh', {url: './quadro_multiplo.php?Trimestre=' + $('#Trimestre').val() + '&Ano=' + $('#Ano').val() + '&CodCidade=' + $('#CodCidade').val()});
            $('#_tabelaResults').show();
        } else {
        	$('#_tabelaResults').hide();
        }
    }

});
$('#Trimestre').change(function () {
	$("#pesoeducacao").val('0');
	$("#educacaoResults").html('');
	$("#pesoengenharia").val('0');
	$("#engenhariaResults").html('');
	$("#pesofiscalizacao").val('0');
	$("#fiscalizacaoResults").html('');
	$("#pesoespeciais").val('0');
	$("#especiaisResults").html('');
	$("#pesosdmc").val('0');
	$("#SDMCResults").html('');
	educacao = 0;
	fiscalizacao = 0;
	engenharia = 0;
	SDMC = 0;
	especiais = 0;
	addEducacao();
	addEngenharia();
	addFiscalizacao();
	addSDMC();
	addEspeciais();

	if ($('#Ano').val() !== '' && $('#Trimestre').val() !== '' && $('#CodCidade').val() !== '') {
		if ($('#idPlano').val() != '') {

			$.ajax(
			{
				url: 'plano.php',
				type: "POST",
				data: {id: $('#idPlano').val(), ajax_projetos: 'BuscaEditar', CodCidade: $('#CodCidade').val(), Trimestre: $('#Trimestre').val(), Ano: $('#Ano').val()},
				success: function (data, textStatus, jqXHR)
				{
					console.log(data)
					if (data != 'vazio') {
						var valores = JSON.parse(data)
						$.each(valores, function (key, val) {
                                    //console.log(val.TipoProjeto)
                                    if (val.TipoProjeto == 'EDUCAÇÃO') {
                                        //console.log(val)
                                        if (educacao == 1) {
                                        	$('#pesoeducacao').val(val.PesoProjeto)
                                        	$('#idEducacao1').val(val.id)
                                        	$('#NomeAtividadeEducacao1').val(val.NomeAtividade)
                                        	$('#PesoAtividadeEducacao1').val(val.PesoAtividade)
                                        	$('#MetaAtividadeEducacao1').val(val.MetaAtividade)
                                        	$('#MetaDescritivaAtividadeEducacao1').val(val.MetaDescritivaAtividade)
                                        	$('#ResponsavelAtividadeEducacao1').val(val.ResponsavelAtividade)
                                        	$('#ParceiroAtividadeEducacao1').val(val.ParceiroAtividade)
                                        	$('#CumprimentoMetaAtividadeEducacao1').val(val.CumprimentoMeta)
                                        	educacao++;
                                        } else {
                                        	addEducacao();
                                        	$('#idEducacao' + educacao).val(val.id)
                                        	$('#NomeAtividadeEducacao' + educacao).val(val.NomeAtividade)
                                        	$('#PesoAtividadeEducacao' + educacao).val(val.PesoAtividade)
                                        	$('#MetaAtividadeEducacao' + educacao).val(val.MetaAtividade)
                                        	$('#MetaDescritivaAtividadeEducacao' + educacao).val(val.MetaDescritivaAtividade)
                                        	$('#ResponsavelAtividadeEducacao' + educacao).val(val.ResponsavelAtividade)
                                        	$('#ParceiroAtividadeEducacao' + educacao).val(val.ParceiroAtividade)
                                        	$('#CumprimentoMetaAtividadeEducacao' + educacao).val(val.CumprimentoMeta)
                                        	educacao++;
                                        }

                                    } else if (val.TipoProjeto == 'ENGENHARIA') {
                                    	if (engenharia == 1) {
                                    		$('#pesoengenharia').val(val.PesoProjeto)
                                    		$('#idEngenharia1').val(val.id)
                                    		$('#NomeAtividadeEngenharia1').val(val.NomeAtividade)
                                    		$('#PesoAtividadeEngenharia1').val(val.PesoAtividade)
                                    		$('#MetaAtividadeEngenharia1').val(val.MetaAtividade)
                                    		$('#MetaDescritivaAtividadeEngenharia1').val(val.MetaDescritivaAtividade)
                                    		$('#ResponsavelAtividadeEngenharia1').val(val.ResponsavelAtividade)
                                    		$('#ParceiroAtividadeEngenharia1').val(val.ParceiroAtividade)
                                    		$('#CumprimentoMetaAtividadeEngenharia1').val(val.CumprimentoMeta)
                                    		engenharia++;
                                    	} else {
                                    		addEngenharia();
                                    		$('#idEngenharia' + engenharia).val(val.id)
                                    		$('#NomeAtividadeEngenharia' + engenharia).val(val.NomeAtividade)
                                    		$('#PesoAtividadeEngenharia' + engenharia).val(val.PesoAtividade)
                                    		$('#MetaAtividadeEngenharia' + engenharia).val(val.MetaAtividade)
                                    		$('#MetaDescritivaAtividadeEngenharia' + engenharia).val(val.MetaDescritivaAtividade)
                                    		$('#ResponsavelAtividadeEngenharia' + engenharia).val(val.ResponsavelAtividade)
                                    		$('#ParceiroAtividadeEngenharia' + engenharia).val(val.ParceiroAtividade)
                                    		$('#CumprimentoMetaAtividadeEngenharia' + engenharia).val(val.CumprimentoMeta)
                                    		engenharia++;
                                    	}

                                    } else if (val.TipoProjeto == 'FISCALIZAÇÃO') {
                                    	if (fiscalizacao == 1) {
                                    		$('#pesofiscalizacao').val(val.PesoProjeto)
                                    		$('#idFiscalizacao1').val(val.id)
                                    		$('#NomeAtividadeFiscalizacao1').val(val.NomeAtividade)
                                    		$('#PesoAtividadeFiscalizacao1').val(val.PesoAtividade)
                                    		$('#MetaAtividadeFiscalizacao1').val(val.MetaAtividade)
                                    		$('#MetaDescritivaAtividadeFiscalizacao1').val(val.MetaDescritivaAtividade)
                                    		$('#ResponsavelAtividadeFiscalizacao1').val(val.ResponsavelAtividade)
                                    		$('#ParceiroAtividadeFiscalizacao1').val(val.ParceiroAtividade)
                                    		$('#CumprimentoMetaAtividadeFiscalizacao1').val(val.CumprimentoMeta)
                                    		fiscalizacao++;
                                    	} else {
                                    		addFiscalizacao();
                                    		$('#idFiscalizacao' + fiscalizacao).val(val.id)
                                    		$('#NomeAtividadeFiscalizacao' + fiscalizacao).val(val.NomeAtividade)
                                    		$('#PesoAtividadeFiscalizacao' + fiscalizacao).val(val.PesoAtividade)
                                    		$('#MetaAtividadeFiscalizacao' + fiscalizacao).val(val.MetaAtividade)
                                    		$('#MetaDescritivaAtividadeFiscalizacao' + fiscalizacao).val(val.MetaDescritivaAtividade)
                                    		$('#ResponsavelAtividadeFiscalizacao' + fiscalizacao).val(val.ResponsavelAtividade)
                                    		$('#ParceiroAtividadeFiscalizacao' + fiscalizacao).val(val.ParceiroAtividade)
                                    		$('#CumprimentoMetaAtividadeFiscalizacao' + fiscalizacao).val(val.CumprimentoMeta)
                                    		fiscalizacao++;
                                    	}

                                    } else if (val.TipoProjeto == 'ESPECIAIS') {
                                    	if (especiais == 1) {
                                    		$('#pesoespeciais').val(val.PesoProjeto)
                                    		$('#idEspeciais1').val(val.id)
                                    		$('#NomeAtividadeEspeciais1').val(val.NomeAtividade)
                                    		$('#PesoAtividadeEspeciais1').val(val.PesoAtividade)
                                    		$('#MetaAtividadeEspeciais1').val(val.MetaAtividade)
                                    		$('#MetaDescritivaAtividadeEspeciais1').val(val.MetaDescritivaAtividade)
                                    		$('#ResponsavelAtividadeEspeciais1').val(val.ResponsavelAtividade)
                                    		$('#ParceiroAtividadeEspeciais1').val(val.ParceiroAtividade)
                                    		$('#CumprimentoMetaAtividadeEspeciais1').val(val.CumprimentoMeta)
                                    		especiais++;
                                    	} else {
                                    		addEspeciais();
                                    		$('#idEspeciais' + especiais).val(val.id)
                                    		$('#NomeAtividadeEspeciais' + especiais).val(val.NomeAtividade)
                                    		$('#PesoAtividadeEspeciais' + especiais).val(val.PesoAtividade)
                                    		$('#MetaAtividadeEspeciais' + especiais).val(val.MetaAtividade)
                                    		$('#MetaDescritivaAtividadeEspeciais' + especiais).val(val.MetaDescritivaAtividade)
                                    		$('#ResponsavelAtividadeEspeciais' + especiais).val(val.ResponsavelAtividade)
                                    		$('#ParceiroAtividadeEspeciais' + especiais).val(val.ParceiroAtividade)
                                    		$('#CumprimentoMetaAtividadeEspeciais' + especiais).val(val.CumprimentoMeta)
                                    		especiais++;
                                    	}

                                    } else if (val.TipoProjeto == 'SDMC') {
                                    	if (SDMC == 1) {
                                    		$('#pesosdmc').val(val.PesoProjeto)
                                    		$('#idSDMC1').val(val.id)
                                    		$('#NomeAtividadeSDMC1').val(val.NomeAtividade)
                                    		$('#PesoAtividadeSDMC1').val(val.PesoAtividade)
                                    		$('#MetaAtividadeSDMC1').val(val.MetaAtividade)
                                    		$('#MetaDescritivaAtividadeSDMC1').val(val.MetaDescritivaAtividade)
                                    		$('#ResponsavelAtividadeSDMC1').val(val.ResponsavelAtividade)
                                    		$('#ParceiroAtividadeSDMC1').val(val.ParceiroAtividade)
                                    		$('#CumprimentoMetaAtividadeSDMC1').val(val.CumprimentoMeta)
                                    		SDMC++;
                                    	} else {
                                    		addSDMC();
                                    		$('#idSDMC' + SDMC).val(val.id)
                                    		$('#NomeAtividadeSDMC' + SDMC).val(val.NomeAtividade)
                                    		$('#PesoAtividadeSDMC' + SDMC).val(val.PesoAtividade)
                                    		$('#MetaAtividadeSDMC' + SDMC).val(val.MetaAtividade)
                                    		$('#MetaDescritivaAtividadeSDMC' + SDMC).val(val.MetaDescritivaAtividade)
                                    		$('#ResponsavelAtividadeSDMC' + SDMC).val(val.ResponsavelAtividade)
                                    		$('#ParceiroAtividadeSDMC' + SDMC).val(val.ParceiroAtividade)
                                    		$('#CumprimentoMetaAtividadeSDMC' + SDMC).val(val.CumprimentoMeta)
                                    		SDMC++;
                                    	}
                                    }
                                });
$("#_Projetos").show();

}else{
	$("#_Projetos").show();

}
},
error: function (jqXHR, textStatus, errorThrown)
{
	console.log("erro Buscaprojetos");
}
});
} else {
	$("#_Projetos").show();
}
} else {
	console.log('erro dados trimestre')
}
$('#salvar').show();
});
$('#Cidade').change(function () {
	$.ajax(
	{
		url: 'mun_ibge.php',
		type: "POST",
		data: {Cidade: this.value , Estado: $('#Estado').val()},
		success: function (data, textStatus, jqXHR)
		{
			$("#CodCidade").val(data);
			if ($('#Ano').val() !== '' && $('#CodCidade').val() !== '') {
				$('#titulo-painel').html('Planos de Ações do período ' + $('#Ano').val());
				$('#adicionarPlano').show();
				$('#table').bootstrapTable('removeAll');
				$('#table').bootstrapTable('refresh', {url: './plano.php'});
                        //$('#table').bootstrapTable('refresh', {url: './quadro_multiplo.php?Trimestre=' + $('#Trimestre').val() + '&Ano=' + $('#Ano').val() + '&CodCidade=' + $('#CodCidade').val()});
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
function resetAllValues() {
	educacao = 0;
	fiscalizacao = 0;
	engenharia = 0;
	SDMC = 0;
	especiais = 0;

	$("#educacaoResults").html('');

	$("#engenhariaResults").html('');

	$("#fiscalizacaoResults").html('');

	$("#especiaisResults").html('');

	$("#SDMCResults").html('');
	addEducacao();
	addEngenharia();
	addFiscalizacao();
	addSDMC();
	addEspeciais();
	$('#_Plano div[id^="_"]').hide();
	$('#_Plano').hide();
	$('#Trimestre').val('');
	$('#ObjetivoPrograma').val('');
	$('#SecretariasEnvolvidas').val('');
	$('#_Plano input').val('');
	$('#_Plano').find('input').val('');


	$('#_Plano').find('.has-success').removeClass('has-success')
	$('#_Plano').find('.has-error').removeClass('has-error')
	$('#_Plano').find('.help-block').hide()
	$('#_Plano').find('.fv-icon-no-label').hide()
}
$('#adicionarPlano').click(function () {
	resetAllValues();
	$('#_Plano').show();
	$('#_Plano').focus();
	$('#adicionarPlano').hide();
	$('#salvar').hide();
});
function addEducacao() {
	$("#btnEducacao" + educacao).removeAttr("onclick");
	$("#btnEducacao" + educacao).attr('onclick', 'deleteRow("educacao' + educacao + '")');
	$("#btnEducacao" + educacao).find('i').removeClass('glyphicon-plus');
	$("#btnEducacao" + educacao).find('i').addClass('glyphicon-minus');
	$("#btnEducacao" + educacao).addClass('btnDelete');
	educacao++;
	$('#educacaoResults').append('<tr id="educacao' + educacao + '">\n\
		<input type="hidden" id="idEducacao' + educacao + '" name="idEducacao[]">\n\
		<td style="width: 20%;"><input type="text" class="form-control" id="NomeAtividadeEducacao' + educacao + '" name="NomeAtividadeEducacao[]" required=""></td> \n\
		<td style="width: 10%;"><div class="input-group"><input type="number" class="form-control metaProposta" id="PesoAtividadeEducacao' + educacao + '" name="PesoAtividadeEducacao[]" required=""></div></td>\n\
		<td style="width: 10%;"><div class="input-group"><input type="number" class="form-control metaProposta" id="MetaAtividadeEducacao' + educacao + '" name="MetaAtividadeEducacao[]"></div></td>\n\
		<td style="width: 20%;"><input type="text" class="form-control" id="MetaDescritivaAtividadeEducacao' + educacao + '" name="MetaDescritivaAtividadeEducacao[]"></td>    \n\
		<td style="width: 20%;"><input type="text" class="form-control" id="ResponsavelAtividadeEducacao' + educacao + '" name="ResponsavelAtividadeEducacao[]"></td>\n\
		<td style="width: 20%;"><input type="text" class="form-control" id="ParceiroAtividadeEducacao' + educacao + '" name="ParceiroAtividadeEducacao[]"> </td>\n\
		<td style="width: 20%;"><input type="number" disabled class="form-control" id="CumprimentoMetaAtividadeEducacao' + educacao + '" name="CumprimentoMetaAtividadeEducacao[]"> </td>\n\
		<td> <button type="button" id="btnEducacao' + educacao + '" onClick="addEducacao();" class="btn btn-default btnEducacao"><i class="glyphicon glyphicon-plus"></i></button></td>\n\
		</tr>');
}

function addFiscalizacao() {
	$("#btnFiscalizacao" + fiscalizacao).removeAttr("onclick");
	$("#btnFiscalizacao" + fiscalizacao).attr('onclick', 'deleteRow("fiscalizacao' + fiscalizacao + '")');
	$("#btnFiscalizacao" + fiscalizacao).find('i').removeClass('glyphicon-plus');
	$("#btnFiscalizacao" + fiscalizacao).find('i').addClass('glyphicon-minus');
	$("#btnFiscalizacao" + fiscalizacao).addClass('btnDelete');
	fiscalizacao++;
	$('#fiscalizacaoResults').append('<tr id="fiscalizacao' + fiscalizacao + '">\n\
		<input type="hidden" id="idFiscalizacao' + fiscalizacao + '" name="idFiscalizacao[]">\n\
		<td style="width: 20%;"><input type="text" class="form-control" id="NomeAtividadeFiscalizacao' + fiscalizacao + '" name="NomeAtividadeFiscalizacao[]" required=""></td> \n\
		<td style="width: 10%;"><div class="input-group"><input type="number" class="form-control metaProposta" id="PesoAtividadeFiscalizacao' + fiscalizacao + '" name="PesoAtividadeFiscalizacao[]" required=""></div></td>\n\
		<td style="width: 10%;"><div class="input-group"><input type="number" class="form-control metaProposta" id="MetaAtividadeFiscalizacao' + fiscalizacao + '" name="MetaAtividadeFiscalizacao[]"></div></td>\n\
		<td style="width: 20%;"><input type="text" class="form-control" id="MetaDescritivaAtividadeFiscalizacao' + fiscalizacao + '" name="MetaDescritivaAtividadeFiscalizacao[]"></td>    \n\
		<td style="width: 20%;"> <input type="text" class="form-control" id="ResponsavelAtividadeFiscalizacao' + fiscalizacao + '" name="ResponsavelAtividadeFiscalizacao[]"></td>\n\
		<td style="width: 20%;"> <input type="text" class="form-control" id="ParceiroAtividadeFiscalizacao' + fiscalizacao + '" name="ParceiroAtividadeFiscalizacao[]"> </td>\n\
		<td style="width: 20%;"><input type="number" disabled class="form-control" id="CumprimentoMetaAtividadeFiscalizacao' + fiscalizacao + '" name="CumprimentoMetaAtividadeFiscalizacao[]"> </td>\n\
		\n\             <td> <button type="button" id="btnFiscalizacao' + fiscalizacao + '" onClick="addFiscalizacao();" class="btn btn-default btnFiscalizacao"><i class="glyphicon glyphicon-plus"></i></button></td>\n\
		</tr>');
}

function addEngenharia() {
	$("#btnEngenharia" + engenharia).removeAttr("onclick");
	$("#btnEngenharia" + engenharia).attr('onclick', 'deleteRow("engenharia' + engenharia + '")');
	$("#btnEngenharia" + engenharia).find('i').removeClass('glyphicon-plus');
	$("#btnEngenharia" + engenharia).find('i').addClass('glyphicon-minus');
	$("#btnEngenharia" + engenharia).addClass('btnDelete');
	engenharia++;
	$('#engenhariaResults').append('<tr id="engenharia' + engenharia + '">\n\
		<input type="hidden" id="idEngenharia' + engenharia + '" name="idEngenharia[]">\n\
		<td style="width: 20%;"><input type="text" class="form-control" id="NomeAtividadeEngenharia' + engenharia + '" name="NomeAtividadeEngenharia[]" required=""></td> \n\
		<td style="width: 10%;"><div class="input-group"><input type="number" class="form-control metaProposta" id="PesoAtividadeEngenharia' + engenharia + '" name="PesoAtividadeEngenharia[]" required=""></div></td>\n\
		<td style="width: 10%;"><div class="input-group"><input type="number"  class="form-control metaProposta" id="MetaAtividadeEngenharia' + engenharia + '" name="MetaAtividadeEngenharia[]"></div></td>\n\
		<td style="width: 20%;"><input type="text" class="form-control" id="MetaDescritivaAtividadeEngenharia' + engenharia + '" name="MetaDescritivaAtividadeEngenharia[]"></td>    \n\
		<td style="width: 20%;"> <input type="text" class="form-control" id="ResponsavelAtividadeEngenharia' + engenharia + '" name="ResponsavelAtividadeEngenharia[]"></td>\n\
		<td style="width: 20%;"> <input type="text" class="form-control" id="ParceiroAtividadeEngenharia' + engenharia + '" name="ParceiroAtividadeEngenharia[]"> </td>\n\
		\n\             <td style="width: 20%;"><input type="number" disabled class="form-control" id="CumprimentoMetaAtividadeEngenharia' + engenharia + '" name="CumprimentoMetaAtividadeEngenharia[]"> </td>\n\
		<td> <button type="button" id="btnEngenharia' + engenharia + '" onClick="addEngenharia();" class="btn btn-default btnEngenharia"><i class="glyphicon glyphicon-plus"></i></button></td>\n\
		</tr>');
}

function addEspeciais() {
	$("#btnEspeciais" + especiais).removeAttr("onclick");
	$("#btnEspeciais" + especiais).attr('onclick', 'deleteRow("especiais' + especiais + '")');
	$("#btnEspeciais" + especiais).find('i').removeClass('glyphicon-plus');
	$("#btnEspeciais" + especiais).find('i').addClass('glyphicon-minus');
	$("#btnEspeciais" + especiais).addClass('btnDelete');
	especiais++;
	$('#especiaisResults').append('<tr id="especiais' + especiais + '">\n\
		<input type="hidden" id="idEspeciais' + especiais + '" name="idEspeciais[]">\n\
		<td style="width: 20%;"><input type="text" class="form-control" id="NomeAtividadeEspeciais' + especiais + '" name="NomeAtividadeEspeciais[]" required=""></td> \n\
		<td style="width: 10%;"><div class="input-group"><input type="number" class="form-control metaProposta" id="PesoAtividadeEspeciais' + especiais + '" name="PesoAtividadeEspeciais[]" required=""></div></td>\n\
		<td style="width: 10%;"><div class="input-group"><input type="number"  class="form-control metaProposta" id="MetaAtividadeEspeciais' + especiais + '" name="MetaAtividadeEspeciais[]"></div></td>\n\
		<td style="width: 20%;"><input type="text" class="form-control" id="MetaDescritivaAtividadeEspeciais' + especiais + '" name="MetaDescritivaAtividadeEspeciais[]"></td>    \n\
		<td style="width: 20%;"> <input type="text" class="form-control" id="ResponsavelAtividadeEspeciais' + especiais + '" name="ResponsavelAtividadeEspeciais[]"></td>\n\
		<td style="width: 20%;"> <input type="text" class="form-control" id="ParceiroAtividadeEspeciais' + especiais + '" name="ParceiroAtividadeEspeciais[]"> </td>\n\
		\n\             <td style="width: 20%;"><input type="number" disabled class="form-control" id="CumprimentoMetaAtividadeEspeciais' + especiais + '" name="CumprimentoMetaAtividadeEspeciais[]"> </td>\n\
		<td> <button type="button" id="btnEspeciais' + especiais + '" onClick="addEspeciais();" class="btn btn-default btnEspeciais"><i class="glyphicon glyphicon-plus"></i></button></td>\n\
		</tr>');
}

function addSDMC() {
	$("#btnSDMC" + SDMC).removeAttr("onclick");
	$("#btnSDMC" + SDMC).attr('onclick', 'deleteRow("SDMC' + SDMC + '")');
	$("#btnSDMC" + SDMC).find('i').removeClass('glyphicon-plus');
	$("#btnSDMC" + SDMC).find('i').addClass('glyphicon-minus');
	$("#btnSDMC" + SDMC).addClass('btnDelete');
	SDMC++;
	$('#SDMCResults').append('<tr id="SDMC' + SDMC + '">\n\
		<input type="hidden" id="idSDMC' + SDMC + '" name="idSDMC[]">\n\
		<td style="width: 20%;"><input type="text" class="form-control" id="NomeAtividadeSDMC' + SDMC + '" name="NomeAtividadeSDMC[]" required=""></td> \n\
		<td style="width: 10%;"><div class="input-group"><input type="number" class="form-control metaProposta" id="PesoAtividadeSDMC' + SDMC + '" name="PesoAtividadeSDMC[]" required=""></div></td>\n\
		<td style="width: 10%;"><div class="input-group"><input type="number"  class="form-control metaProposta" id="MetaAtividadeSDMC' + SDMC + '" name="MetaAtividadeSDMC[]"></div></td>\n\
		<td style="width: 20%;"><input type="text" class="form-control" id="MetaDescritivaAtividadeSDMC' + SDMC + '" name="MetaDescritivaAtividadeSDMC[]"></td>    \n\
		<td style="width: 20%;"> <input type="text" class="form-control" id="ResponsavelAtividadeSDMC' + SDMC + '" name="ResponsavelAtividadeSDMC[]"></td>\n\
		<td style="width: 20%;"> <input type="text" class="form-control" id="ParceiroAtividadeSDMC' + SDMC + '" name="ParceiroAtividadeSDMC[]"> </td>\n\
		\n\             <td style="width: 20%;"><input type="number" disabled class="form-control" id="CumprimentoMetaAtividadeSDMC' + SDMC + '" name="CumprimentoMetaAtividadeSDMC[]"> </td>\n\
		<td> <button type="button" id="btnSDMC' + SDMC + '" onClick="addSDMC();" class="btn btn-default btnSDMC"><i class="glyphicon glyphicon-plus"></i></button></td>\n\
		</tr>');
}
function deleteRow(id) {
    //console.log(id)
    $("#" + id).remove();
}
function calculaCumprimento() {

}

$(document).ready(function () {
	$("#pesoeducacao").val('0');
	$("#pesoengenharia").val('0');
	$("#pesoespeciais").val('0');
	$("#pesofiscalizacao").val('0');
	$("#pesosdmc").val('0');
	$("#AnoPrograma").val((new Date).getFullYear() - 1);
	var dt = new Date();
	$('#Plano').formValidation({
		framework: 'bootstrap',
		err: {
			container: 'tooltip'
		},
		excluded: [':disabled'],
		fields: {
			'NomePrograma': {
				validators: {
					notEmpty: {
						message: 'Campo Obrigatório'
					}
				}
			},
			'PesoPrograma': {
				validators: {
					notEmpty: {
						message: 'Campo Obrigatório'
					}
				}
			},
			'ObjetivoPrograma': {
				validators: {
					notEmpty: {
						message: 'Campo Obrigatório'
					}
				}
			},
			'IndicadorIntermediarioPrograma': {
				validators: {
					notEmpty: {
						message: 'Campo Obrigatório'
					}
				}
			},
			'MetaIntermediariaDescritiva': {
				validators: {
					notEmpty: {
						message: 'Campo Obrigatório'
					}
				}
			},
			'CoordenadorPrograma': {
				validators: {
					notEmpty: {
						message: 'Campo Obrigatório'
					}
				}
			},
			'MetaIntermediaria': {
				validators: {
					notEmpty: {
						message: 'Campo Obrigatório'
					},
					between: {
						min: 0,
						max: 100,
						message: 'Insira um valor válido para meta'
					}
				}
			},
			'IndicadorFinalPrograma': {
				validators: {
					notEmpty: {
						message: 'Campo Obrigatório'
					}
				}
			},
			'MetaFinalDescritiva': {
				validators: {
					notEmpty: {
						message: 'Campo Obrigatório'
					}
				}
			},
			'MetaFinal': {
				validators: {
					notEmpty: {
						message: 'Campo Obrigatório'
					},
					between: {
						min: 0,
						max: 100,
						message: 'Insira um valor válido para meta'
					}
				}
			},
			'ParceriasPublicas': {
				enabled: false,
				validators: {
					notEmpty: {
						message: 'Campo Obrigatório'
					}
				}
			},
			'ParceriasPrivadas': {
				enabled: false,
				validators: {
					notEmpty: {
						message: 'Campo Obrigatório'
					}
				}
			},
			'Ano': {
				enabled: false,
				validators: {
					notEmpty: {
						message: 'Campo Obrigatório'
					}
				}
			},
			'ParceriasCivil': {
				enabled: false,
				validators: {
					notEmpty: {
						message: 'Campo Obrigatório'
					}
				}
			},
			'SecretariasEnvolvidas': {
				enabled: false,
				validators: {
					notEmpty: {
						message: 'Campo Obrigatório'
					}
				}
			},
			'NomeAtividadeEducacao[]': {
				enabled: false,
				validators: {
					notEmpty: {
						message: 'Campo Obrigatório'
					}
				}
			},
			'MetaDescritivaAtividadeEducacao[]': {
				enabled: false,
				validators: {
					notEmpty: {
						message: 'Campo Obrigatório'
					}
				}
			},
			'ResponsavelAtividadeEducacao[]': {
				enabled: false,
				validators: {
					notEmpty: {
						message: 'Campo Obrigatório'
					}
				}
			},
			'ParceiroAtividadeEducacao[]': {
				enabled: false,
				validators: {
					notEmpty: {
						message: 'Campo Obrigatório'
					}
				}
			},
			'MetaAtividadeEducacao[]': {
				enabled: false,
				validators: {
					notEmpty: {
						message: 'Campo Obrigatório'
					},
					between: {
						min: 0,
						max: 100,
						message: 'Insira um valor válido para meta'
					}
				}
			},
			'PesoAtividadeEducacao[]': {
				enabled: false,
				validators: {
					notEmpty: {
						message: 'Campo Obrigatório'
					},
					between: {
						min: 0,
						max: 100,
						message: 'Insira um valor válido para meta'
					}
				}
			},
			'NomeAtividadeEngenharia[]': {
				enabled: false,
				validators: {
					notEmpty: {
						message: 'Campo Obrigatório'
					}
				}
			},
			'MetaDescritivaAtividadeEngenharia[]': {
				enabled: false,
				validators: {
					notEmpty: {
						message: 'Campo Obrigatório'
					}
				}
			},
			'ResponsavelAtividadeEngenharia[]': {
				enabled: false,
				validators: {
					notEmpty: {
						message: 'Campo Obrigatório'
					}
				}
			},
			'ParceiroAtividadeEngenharia[]': {
				enabled: false,
				validators: {
					notEmpty: {
						message: 'Campo Obrigatório'
					}
				}
			},
			'MetaAtividadeEngenharia[]': {
				enabled: false,
				validators: {
					notEmpty: {
						message: 'Campo Obrigatório'
					},
					between: {
						min: 0,
						max: 100,
						message: 'Insira um valor válido para meta'
					}
				}
			},
			'PesoAtividadeEngenharia[]': {
				enabled: false,
				validators: {
					notEmpty: {
						message: 'Campo Obrigatório'
					},
					between: {
						min: 0,
						max: 100,
						message: 'Insira um valor válido para meta'
					}
				}
			},
			'NomeAtividadeFiscalizacao[]': {
				enabled: false,
				validators: {
					notEmpty: {
						message: 'Campo Obrigatório'
					}
				}
			},
			'MetaDescritivaAtividadeFiscalizacao[]': {
				enabled: false,
				validators: {
					notEmpty: {
						message: 'Campo Obrigatório'
					}
				}
			},
			'ResponsavelAtividadeFiscalizacao[]': {
				enabled: false,
				validators: {
					notEmpty: {
						message: 'Campo Obrigatório'
					}
				}
			},
			'ParceiroAtividadeFiscalizacao[]': {
				enabled: false,
				validators: {
					notEmpty: {
						message: 'Campo Obrigatório'
					}
				}
			},
			'MetaAtividadeFiscalizacao[]': {
				enabled: false,
				validators: {
					notEmpty: {
						message: 'Campo Obrigatório'
					},
					between: {
						min: 0,
						max: 100,
						message: 'Insira um valor válido para meta'
					}
				}
			},
			'PesoAtividadeFiscalizacao[]': {
				enabled: false,
				validators: {
					notEmpty: {
						message: 'Campo Obrigatório'
					},
					between: {
						min: 0,
						max: 100,
						message: 'Insira um valor válido para meta'
					}
				}
			},
			'NomeAtividadeEspeciais[]': {
				enabled: false,
				validators: {
					notEmpty: {
						message: 'Campo Obrigatório'
					}
				}
			},
			'MetaDescritivaAtividadeEspeciais[]': {
				enabled: false,
				validators: {
					notEmpty: {
						message: 'Campo Obrigatório'
					}
				}
			},
			'ResponsavelAtividadeEspeciais[]': {
				enabled: false,
				validators: {
					notEmpty: {
						message: 'Campo Obrigatório'
					}
				}
			},
			'ParceiroAtividadeEspeciais[]': {
				enabled: false,
				validators: {
					notEmpty: {
						message: 'Campo Obrigatório'
					}
				}
			},
			'MetaAtividadeEspeciais[]': {
				enabled: false,
				validators: {
					notEmpty: {
						message: 'Campo Obrigatório'
					},
					between: {
						min: 0,
						max: 100,
						message: 'Insira um valor válido para meta'
					}
				}
			},
			'PesoAtividadeEspeciais[]': {
				enabled: false,
				validators: {
					notEmpty: {
						message: 'Campo Obrigatório'
					},
					between: {
						min: 0,
						max: 100,
						message: 'Insira um valor válido para meta'
					}
				}
			},
			'NomeAtividadeSDMC[]': {
				enabled: false,
				validators: {
					notEmpty: {
						message: 'Campo Obrigatório'
					}
				}
			},
			'MetaDescritivaAtividadeSDMC[]': {
				enabled: false,
				validators: {
					notEmpty: {
						message: 'Campo Obrigatório'
					}
				}
			},
			'ResponsavelAtividadeSDMC[]': {
				enabled: false,
				validators: {
					notEmpty: {
						message: 'Campo Obrigatório'
					}
				}
			},
			'ParceiroAtividadeSDMC[]': {
				enabled: false,
				validators: {
					notEmpty: {
						message: 'Campo Obrigatório'
					}
				}
			},
			'MetaAtividadeSDMC[]': {
				enabled: false,
				validators: {
					notEmpty: {
						message: 'Campo Obrigatório'
					},
					between: {
						min: 0,
						max: 100,
						message: 'Insira um valor válido para meta'
					}
				}
			},
			'PesoAtividadeSDMC[]': {
				enabled: false,
				validators: {
					notEmpty: {
						message: 'Campo Obrigatório'
					},
					between: {
						min: 0,
						max: 100,
						message: 'Insira um valor válido para meta'
					}
				}
			},
			'pesoeducacao': {
				validators: {
					notEmpty: {
						message: 'Campo Obrigatório'
					},
					between: {
						min: 0,
						max: 100,
						message: 'Insira um valor válido para meta'
					}
				}
			},
			'pesofiscalizacao': {
				validators: {
					notEmpty: {
						message: 'Campo Obrigatório'
					},
					between: {
						min: 0,
						max: 100,
						message: 'Insira um valor válido para meta'
					}
				}
			},
			'pesosdmc': {
				validators: {
					between: {
						min: 0,
						max: 100,
						message: 'Insira um valor válido para meta'
					}
				}
			},
			'pesoespeciais': {
				validators: {
					between: {
						min: 0,
						max: 100,
						message: 'Insira um valor válido para meta'
					}
				}
			},
			'pesoengenharia': {
				validators: {
					notEmpty: {
						message: 'Campo Obrigatório'
					},
					between: {
						min: 0,
						max: 100,
						message: 'Insira um valor válido para meta'
					}
				}
			}
		}


	})
.on('success.field.fv', function (e, data) {
                // data.fv      --> The FormValidation instance
                // data.element --> The field element

                var $tabPane = data.element.parents('.tab-pane'),
                tabId = $tabPane.attr('id'),
                $icon = $('a[href="#' + tabId + '"][data-toggle="tab"]')
                .parent()
                .find('i')
                .removeClass('glyphicon-ok glyphicon-remove');

                // Check if all fields in tab are valid
                var isValidTab = data.fv.isValidContainer($tabPane);
                if (isValidTab !== null) {
                	$icon.addClass(isValidTab ? 'glyphicon-ok' : 'glyphicon-remove');
                }
            })
.on('success.form.fv', function (e, data) {

                // Prevent form submission
                e.preventDefault();
                
                if (parseInt($('#pesoeducacao').val())+parseInt($('#pesoengenharia').val())+parseInt($('#pesofiscalizacao').val())+parseInt($('#pesoespeciais').val())+parseInt($('#pesosdmc').val()) != 100){
                	alertify.alert('Verifique os pesos dos projetos', 'A soma dos pesos dos projetos deve ser 100');
                	return false;

                }

                var $form = $(e.target),
                fv = $form.data('formValidation');

                $('input[type=text]').val(function () {
                	return replaceSpecialChars(this.value);
                })

                $('input:disabled, select:disabled').each(function () {
                	$(this).removeAttr('disabled');
                });

// Use Ajax to submit form data
$.ajax({
	url: 'plano.php',
	type: 'POST',
	data: $form.serialize(),
	success: function (result) {
		console.log(result)
		if (result !== 'erro ao gravar') {
			$('#table').bootstrapTable('refresh', {url: './plano.php'});
			resetAllValues();
                            // $form.data('formValidation').resetForm();
                            $('#salvar').prop("disabled", false);
                            $('#salvar').removeClass("disabled");
                            $('#adicionarPlano').show();
                            // $('#table').bootstrapTable('removeAll');
                            $('#_tabelaResults').show();
                            $('#HiddenEdit').remove();
                            alertify.success('Gravado com sucesso'); 
                        }
//                        if (result !== 'erro ao gravar') {
//
//                            resetAllValues();
//                            $form.data('formValidation').resetForm();
//
//                            $('#adicionarPlano').show();
//                            // $('#table').bootstrapTable('removeAll');
//                            $('#table').bootstrapTable('refresh', {url: './quadro_multiplo.php'});
//                            $('#_tabelaResults').show();
//                            $('#HiddenEdit').remove();
//                        } else {
//                            console.log(result);
//                        }
}
});
})
            // Called when a field is invalid
            .on('err.field.fv', function (e, data) {
            	console.log(e)
            	console.log(data)
            	var $invalidFields = data.fv.getInvalidFields().eq(0);
//               if ($("#"+data.field).closest('.tab-pane').prev().hasClass("active")=='false'){
//                   $('.active').removeClass('active');
//                   $('.tab-pane.active').removeClass('active')
//               }
                // data.element --> The field element

                var $tabPane = data.element.parents('.tab-pane'),
                tabId = $tabPane.attr('id');
                //console.log($tabPane)
                $('a[href="#' + tabId + '"][data-toggle="tab"]')
                .parent()
                .find('i')
                .removeClass('glyphicon-ok')
                .addClass('glyphicon-remove');

                if ($('#_Projetos').find($("#" + data.field)).length == 1) {
                	$('.glyphicon.glyphicon-ok').closest('li').removeClass('active');
                	$('.active').removeClass('active');
                	$tabPane.addClass('active');
                	$('.glyphicon.glyphicon-remove').closest('li').first().addClass('active');

                }
                $("#" + data.field).focus();



            });





        });

$(document).on('change', '.metaProposta', function () {
    // console.log($(this).val())

    var campo = (this.id).slice(0, 4)
    var OutroCampo = (this.id).slice(4)

    if (campo == 'Peso' && ($('#Meta' + OutroCampo).val() != '' || $('#Meta' + OutroCampo).val() != 0)) {
    	var resultado = $(this).val() * 100 / $('#Meta' + OutroCampo).val();
    	$('#CumprimentoMeta' + OutroCampo).val(resultado.toFixed(2));

    }
    if (campo == 'Meta' && ($('#Peso' + OutroCampo).val() != '' || $('#Peso' + OutroCampo).val() != 0)) {
    	var resultado = $(this).val() * 100 / $('#Peso' + OutroCampo).val();
    	$('#CumprimentoMeta' + OutroCampo).val(resultado.toFixed(2));
    }

});