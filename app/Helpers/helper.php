<?php

function tirarAcentos2($string){
	return trim(preg_replace(array("/(á|à|ã|â|ä)/","/(Á|À|Ã|Â|Ä)/","/(é|è|ê|ë)/","/(É|È|Ê|Ë)/","/(í|ì|î|ï)/","/(Í|Ì|Î|Ï)/","/(ó|ò|õ|ô|ö)/","/(Ó|Ò|Õ|Ô|Ö)/","/(ú|ù|û|ü)/","/(Ú|Ù|Û|Ü)/","/(ñ)/","/(Ñ)/","/(Ç)/","/(ç)/","/(  )/"),explode(" ","a A e E i I o O u U n N C c  "),$string));
}


function tirarAcentos($text) {
	$utf8 = array(
		'/[áàâãªäāẵ]/u'   =>   'a',
		'/[ÁÀÂÃÄ]/u'    =>   'A',
		'/[ÍÌÎÏ]/u'     =>   'I',
		'/[íìîï]/u'     =>   'i',
		'/[éèêë]/u'     =>   'e',
		'/[ÉÈÊË]/u'     =>   'E',
		'/[óòôõºö]/u'   =>   'o',
		'/[ÓÒÔÕÖ]/u'    =>   'O',
		'/[úùûü]/u'     =>   'u',
		'/[ÚÙÛÜ]/u'     =>   'U',
		'/&/'           =>   'e',
		'/ç/'           =>   'c',
		'/Ç/'           =>   'C',
		'/ñ/'           =>   'n',
		'/Ñ/'           =>   'N',
		"/'/"           =>   '',
		'/"/'           =>   '',
        '/º/'           =>   '', // UTF-8 hyphen to "normal" hyphen
        '/–/'           =>   '-', // UTF-8 hyphen to "normal" hyphen
        '/[’‘‹›‚]/u'    =>   ' ', // Literally a single quote
        '/[“”«»„]/u'    =>   ' ', // Double quote
        '/ /'           =>   ' ', // nonbreaking space (equiv. to 0x160)
    );
	$string = preg_replace(array_keys($utf8), array_values($utf8), $text);
	$string = preg_replace('/[^A-Za-z0-9 \-]/', '', $string);

	return $string;
}

function cleanString($string) {
   return strtoupper(preg_replace('/[^A-Za-z0-9\-]/', '', $string)); // Removes special chars.
}

function getDistance($latitude1, $longitude1, $latitude2, $longitude2) {

	$earth_radius = 6371;

	$dLat = deg2rad($latitude2 - $latitude1);
	$dLon = deg2rad($longitude2 - $longitude1);

	$a = sin($dLat/2) * sin($dLat/2) + cos(deg2rad($latitude1)) * cos(deg2rad($latitude2)) * sin($dLon/2) * sin($dLon/2);
	$c = 2 * asin(sqrt($a));
	$d = $earth_radius * $c;

	return $d;

}
function utf8_for_xml($string)
{
	return preg_replace('/[^\x{0009}\x{000a}\x{000d}\x{0020}-\x{D7FF}\x{E000}-\x{FFFD}]+/u',
		' ', $string);
}


function validaNomeCompleto($nomeCompleto)
{
	return trim(strtoupper(tirarAcentos($nomeCompleto)));

}
function validaTipoLesao($tipolesao)
{
	$tipolesao = trim(strtoupper(tirarAcentos($tipolesao)));
	if ($tipolesao == 'LEVES' || $tipolesao == 'LEVE' || $tipolesao == 'COM FERIMENTOS LEVES' || $tipolesao == 'COM FERIMENTOS LEVES' || $tipolesao == 'FERIDAS LEVES' || $tipolesao == 'FERIMENTO LEVE') {
		return 'LEVE';
	} else if ($tipolesao == 'NAO INFORMADO' || $tipolesao == 'IGNORADO' || $tipolesao == 'NDA' || $tipolesao == 'NADA') {
		return 'NAO INFORMADO';
	} else if ($tipolesao == 'SEM LESAO' || $tipolesao == 'SEM LESOES' || $tipolesao == 'SEM FERIMENTOS' || $tipolesao == 'SEM FERIMENTO' || $tipolesao == 'SEM FERIDA') {
		return 'SEM LESOES';
	} else if ($tipolesao == 'GRAVE' || $tipolesao == 'GRAVES' || $tipolesao == 'LESOES GRAVES' || $tipolesao == 'COM LESOES GRAVES') {
		return 'GRAVE';
	} else if ($tipolesao == 'MODERADA' || $tipolesao == 'MODERADAS' || $tipolesao == 'LESOES MODERADAS' || $tipolesao == 'COM LESOES MODERADAS') {
		return 'MODERADA';
	} else if ($tipolesao == 'FATAL' || $tipolesao == 'OBITO' || $tipolesao == 'MORTE' || $tipolesao == 'FALECIMENTO' || $tipolesao == 'VITIMA FATAL') {
		return 'FATAL';
	} else if ($tipolesao == 'NAO ESPECIFICADAS' || $tipolesao == 'NAO FATAL' || $tipolesao == 'LESOES' || $tipolesao == 'COM LESOES' || $tipolesao == 'COM FERIMENTOS' || $tipolesao == 'FERIDO') {
		return 'LESOES NAO ESPECIFICADAS';
	} else if ($tipolesao == 'FATAL LOCAL' || $tipolesao == 'FATAL NO LOCAL') {
		return 'FATAL LOCAL';
	} else if ($tipolesao == 'FATAL POSTERIOR' || $tipolesao == 'FATAL HOSPITAL' || $tipolesao == 'FATAL NO HOSPITAL') {
		return 'FATAL POSTERIOR';
	} else {
		return 'NAO INFORMADO';
	}
}

function validaTipoObito($TIPOBITO) {

	if ($TIPOBITO == 1) {
		return 'FETAL';
	} else if ($TIPOBITO == 2) {
		return 'NAO FETAL';
	} else {
		return 'IGNORADO';
	}
}
function validaRacaCor($TIPOBITO) {

	if ($TIPOBITO == 1) {
		return 'BRANCA';
	} else if ($TIPOBITO == 2) {
		return 'PRETA';
	} else if ($TIPOBITO == 3) {
		return 'AMARELA';
	} else if ($TIPOBITO == 4) {
		return 'PARDA';
	} else if ($TIPOBITO == 5) {
		return 'INDIGENA';
	} else {
		return 'IGNORADO';
	}
}


function convertCharset($in_str)
{
	$cur_encoding = mb_detect_encoding($in_str) ;
	if($cur_encoding == "UTF-8" && mb_check_encoding($in_str,"UTF-8"))
		return $in_str;
	else
		return utf8_encode($in_str);
}

function calculaIdade($DataNascimento, $DataAcidente)
{
	if($DataNascimento == "99/99/9999" || $DataAcidente == "99/99/9999"){
		return '999';
	}
	if(!empty($DataNascimento) && !empty($DataAcidente)){
		try {
			$DataNascimento = \Carbon\Carbon::createFromFormat('d/m/Y',$DataNascimento);
			$DataAcidente = \Carbon\Carbon::createFromFormat('d/m/Y',$DataAcidente);
			return $DataNascimento->diffInYears($DataAcidente);
		} catch (\ErrorException $e) {
			return '999';
		}
	}else{
		return '999';
	} 
}
function calculaFaixaEtaria($idade)
{
	if(!empty($idade) && $idade != 'Sem Informação'){
		if($idade <= 4){
			return "Menor que 4 anos";
		}elseif($idade >= 5 && $idade <= 9){
			return "5 a 9 Anos";
		}elseif($idade >= 10 && $idade <= 17){
			return "10 a 17 Anos";
		}elseif($idade >= 18 && $idade <= 24){
			return "18 a 24 Anos";
		}elseif($idade >= 25 && $idade <= 29){
			return "25 a 29 Anos";
		}elseif($idade >= 30 && $idade <= 39){
			return "30 a 39 Anos";
		}elseif($idade >= 40 && $idade <= 49){
			return "40 a 49 Anos";
		}elseif($idade >= 50 && $idade <= 59){
			return "50 a 59 Anos";
		}elseif($idade >= 60 && $idade <= 69){
			return "60 a 69 Anos";
		}elseif($idade == 999){
			return "Sem Informação";
		}else{
			return "Maior que 70 Anos";
		}
	}else{
		return 'Sem Informação';
	} 
}
function calculaDia($data)
{
	if($data == "99/99/9999"){
		return 'Sem Informação';
	}
	if(!empty($data)){
		try {
			$data = \Carbon\Carbon::createFromFormat('d/m/Y',$data);
			return $data->locale('pt_BR')->dayName;
		} catch (\ErrorException $e) {
			return 'Sem Informação';
		}
	}else{
		return 'Sem Informação';
	} 
}


function validaLocalOCor($valor) {

	if ($valor == 1) {
		return 'HOSPITAL';
	} else if ($valor == 2) {
		return 'OUTROS ESTABELECIMENTOS DE SAUDE';
	} else if ($valor == 3) {
		return 'DOMICILIO';
	} else if ($valor == 4) {
		return 'VIA PUBLICA';
	} else if ($valor == 5) {
		return 'OUTROS';
	} else if ($valor == 9) {
		return 'IGNORADO';
	} else {
		return 'NAO INFORMADO';
	}
}
function converteIdade($idade) {

	if (!empty($idade) && is_numeric($idade)) {
		$idade1 = str_split($idade, 1);
		if (strlen($idade) == 3) {
			if ($idade1[0] == 0) {
				return '0';
			} elseif ($idade1[0] == 1) {
				return '0';
			} elseif ($idade1[0] == 2) {
				return '0';
			} elseif ($idade1[0] == 3) {
				return '0';
			} elseif ($idade1[0] == 4) {
				return $idade1[1] . $idade1[2];
			} elseif ($idade1[0] == 5) {
				return '1' . $idade1[1] . $idade1[2];
			}
		} else {
			return $idade;
		}
	} else {
		return '999';
	}
}
function converteData($data) {
	
	if (DateTime::createFromFormat('d/m/Y', $data) !== false) {
		return $data;
	}
	try{
		$dataFormat = new DateTime($data);
		if ($dataFormat !== false) {
			return $dataFormat->format('d/m/Y');
		}
	} catch (Exception $e) {

	}
	if (!empty($data) && strlen($data) == 8) {

		if ((checkdate(substr($data, 2, 2), substr($data, 0, 2), substr($data, 4, 4)))) {
			return substr($data, 0, 2) . '/' . substr($data, 2, 2) . '/' . substr($data, 4, 4);
		} elseif (checkdate(substr($data, 2, 2), substr($data, 0, 2), substr($data, 6, 4))) {
			return substr($data, 0, 2) . '/' . substr($data, 2, 2) . '/' . substr($data, 4, 4);
		} elseif (checkdate(substr($data, 4, 2), substr($data, 0, 2), substr($data, 6, 4))) {
			return substr($data, 6, 2) . '/' . substr($data, 4, 2) . '/' . substr($data, 0, 4);
		} elseif (checkdate(substr($data, 4, 2), substr($data, 6, 2), substr($data, 0, 4))) {
			return substr($data, 6, 2) . '/' . substr($data, 4, 2) . '/' . substr($data, 0, 4);
		} else {
			return '99/99/9999';
		}
	} else if (!empty($data) && strlen($data) == 7) {
		$data = '0' . $data;
		if ((checkdate(substr($data, 2, 2), substr($data, 0, 2), substr($data, 4, 4)))) {
			return substr($data, 0, 2) . '/' . substr($data, 2, 2) . '/' . substr($data, 4, 4);
		} elseif (checkdate(substr($data, 2, 2), substr($data, 0, 2), substr($data, 6, 4))) {
			return substr($data, 0, 2) . '/' . substr($data, 2, 2) . '/' . substr($data, 4, 4);
		} elseif (checkdate(substr($data, 4, 2), substr($data, 0, 2), substr($data, 6, 4))) {
			return substr($data, 6, 2) . '/' . substr($data, 4, 2) . '/' . substr($data, 0, 4);
		} elseif (checkdate(substr($data, 4, 2), substr($data, 6, 2), substr($data, 0, 4))) {
			return substr($data, 6, 2) . '/' . substr($data, 4, 2) . '/' . substr($data, 0, 4);
		} else {
			return '99/99/9999';
		}
	} else {
		return '99/99/9999';
	}
}

function validaTipoVeiculo($tipoveiculo)
{
	$tipoveiculo = trim(strtoupper(tirarAcentos($tipoveiculo)));
	$explode     = explode(' ', $tipoveiculo);

	if (count($explode) > 1) {
		if ($explode[0] == 'COND') {
			if ($explode[1] == 'AUTO') {
				$tipoveiculo == 'AUTOMOVEL';
			} else if ($explode[1] == 'CAMINHAO' || $explode[1] == 'CAMINHAO-TRATOR' || $explode[1] == 'CAMIONETA') {
				$tipoveiculo == 'CAMINHAO';
			} else if ($explode[1] == 'CARRROCA') {
				$tipoveiculo == 'CARRROCA';
			} else if ($explode[1] == 'ONIBUS' || $explode[1] == 'MICROONIBUS') {
				$tipoveiculo == 'ONIBUS/VAN';
			} else if ($explode[1] == 'BONDE' || $explode[1] == 'REBOQUE' || $explode[1] == 'TRATOR') {
				$tipoveiculo == 'OUTROS';
			} else if ($explode[1] == 'TRICICLO') {
				$tipoveiculo == 'TRICICLO';
			}
		}
	}
	if ($tipoveiculo == 'MOTO' || $tipoveiculo == 'MOTOCICLISTA' || $tipoveiculo == 'COND MOTONETA' || $tipoveiculo == 'CICLOMOTOR' || $tipoveiculo == 'COND CICLOMOTOR' || $tipoveiculo == 'MOTONETA') {
		return 'MOTOCICLETA';
	}
	if ($tipoveiculo == 'CARRO' || $tipoveiculo == 'COND AUTO') {
		return 'AUTOMOVEL';
	}
	if ($tipoveiculo == 'CICLISTA') {
		return 'BICICLETA';
	}
	if ($tipoveiculo == '' || $tipoveiculo == ' ') {
		return 'NAO INFORMADO';
	}

	if ($tipoveiculo == 'AUTOMOVEL' ||
		$tipoveiculo == 'MOTOCICLETA' ||
		$tipoveiculo == 'CAMINHAO' ||
		$tipoveiculo == 'CARROCA' ||
		$tipoveiculo == 'ONIBUS/VAN' ||
		$tipoveiculo == 'TRICICLO' ||
		$tipoveiculo == 'OUTROS' ||
		$tipoveiculo == 'PEDESTRE' ||
		$tipoveiculo == 'NAO INFORMADO' ||
		$tipoveiculo == 'BICICLETA') {
		return $tipoveiculo;
} else {
	return 'NAO INFORMADO';
}
}
function validaSexo($sexo)
{
	$sexo = trim(strtoupper(tirarAcentos($sexo)));
	if ($sexo == 'M' || $sexo == 'MASCULINO' || $sexo == '1') {
		return 'MASCULINO';
	} else if ($sexo == 'F' || $sexo == 'FEMININO' || $sexo == '2') {
		return 'FEMININO';
	} else if ($sexo == 'IGNORADO' || $sexo == 'IGN' || $sexo == '999') {
		return 'IGNORADO';
	} else if ($sexo == 'NAO INFORMADO' || $sexo == 'NDA' || $sexo == 'NENHUM' || $sexo == 'NI') {
		return 'NAO INFORMADO';
	} else {
		return 'NAO INFORMADO';
	}
}
function validaTrimestre($data){
	if (DateTime::createFromFormat('d/m/Y', $data) !== false) {
		$date = explode("/", $data);
		if ($date[1] <= 3) return 1;
		if ($date[1] <= 6) return 2;
		if ($date[1] <= 9) return 3;

		return 4;
	}else{
		return 0;
	}
	return 0;
}

function validaAno($data){
	if (DateTime::createFromFormat('d/m/Y', $data) !== false) {
		$date = explode("/", $data);
		return $date[2];
	}else{
		return 0;
	}
	return 0;
}

function validaData($data)
{
	if($data == null){
		return '99/99/9999';
	}
	if (DateTime::createFromFormat('d/m/Y', $data) !== false) {
		return $data;
	}
	try{
		$dataFormat = new DateTime($data);
		if ($dataFormat !== false) {
			return $dataFormat->format('d/m/Y');
		}
	} catch (Exception $e) {

	}

	if(strlen($data) == 10){
		if (count(explode("/", $data)) == 3) {
			$date = explode("/", $data);
			if (DateTime::createFromFormat('d/m/Y', $date[1].'/'.$date[0].'/'.$date[2]) !== false) {
				return $date[1].'/'.$date[0].'/'.$date[2];
			}
		}
	}
	if (empty($data)) {
		return '99/99/9999';
	} elseif (strlen($data) < 5) {
		return '99/99/9999';
	} elseif ((strlen($data) == 5) && (is_numeric($data))) {
		if (checkdate(substr($data, 1, 2), '0' . substr($data, 0, 1), substr($data, 3, 4))) {
			return '0' . substr($data, 0, 0) . '/' . substr($data, 1, 2) . '/' . substr($data, 3, 4);
		} else {
			return '99/99/9999';
		}
	} elseif (strlen($data) == 6) {
            //100517
		if (!(count(explode('-', $data)) > 0 || count(explode('/', $data)) > 0 || count(explode('.', $data)) > 0 || count(explode(',', $data)) > 0)) {

			if (checkdate(substr($data, 2, 2), substr($data, 0, 2), substr($data, 4, 4))) {
				return substr($data, 0, 2) . '/' . substr($data, 2, 2) . '/' . substr($data, 4, 4);
			} else {
				return '99/99/9999';
			}
		} else {
			if (!is_numeric(substr($data, -4))) {
				$yy = substr($data, -2);
				if ($yy <= 99 && $yy > date("y")) {
					$yy += 1900;
				} else {
					$yy += 2000;
				}
				if (is_numeric(substr($data, 0, 1)) && is_numeric(substr($data, 2, 1))) {
					$estado ='0' . substr($data, 0, 1) . '/0' . substr($data, 2, 1) . '/' . $yy;
				}

				if ((checkdate(substr($data, 0, 2), substr($data, 3, 2), substr($data, 6, 4)))) {
					return substr($data, 3, 2) . '/' . substr($data, 0, 2) . '/' . substr($data, 6, 4);
				} elseif (checkdate(substr($data, 3, 2), substr($data, 0, 2), substr($data, 6, 4))) {
					return substr($data, 0, 2) . '/' . substr($data, 3, 2) . '/' . substr($data, 6, 4);
				} else {
					return '99/99/9999';
				}
			}
		}
	} elseif (strlen($data) == 7) {
		if (!is_numeric(substr($data, -4))) {
			$yy = substr($data, -2);
			if ($yy <= 99 && $yy > date("y")) {
				$yy += 1900;
			} else {
				$yy += 2000;
			}
                // 1/1/15    10/2/15    2/10/15   mes dia ano
			if (is_numeric(substr($data, 0, 2)) && is_numeric(substr($data, 3, 2))) {
                    //10/12/15 leng 8 nao entra
				$estado =substr($data, 0, 2) . '/' . substr($data, 3, 2) . '/' . $yy;
			} elseif (is_numeric(substr($data, 0, 2)) && is_numeric(substr($data, 3, 1))) {
                    //10/2/15
				$estado =substr($data, 0, 2) . '/0' . substr($data, 3, 1) . '/' . $yy;
			} elseif (is_numeric(substr($data, 0, 1)) && is_numeric(substr($data, 2, 2))) {
                    //1/10/15
				$estado ='0' . substr($data, 0, 1) . '/' . substr($data, 2, 2) . '/' . $yy;
			}

			if ((checkdate(substr($data, 0, 2), substr($data, 3, 2), substr($data, 6, 4)))) {
				return substr($data, 3, 2) . '/' . substr($data, 0, 2) . '/' . substr($data, 6, 4);
			} elseif (checkdate(substr($data, 3, 2), substr($data, 0, 2), substr($data, 6, 4))) {
				return substr($data, 0, 2) . '/' . substr($data, 3, 2) . '/' . substr($data, 6, 4);
			} else {
				return '99/99/9999';
			}
		}
	} elseif (strlen($data) == 8) {
            //10102010
		if (!(count(explode('-', $data)) > 0 || count(explode('/', $data)) > 0 || count(explode('.', $data)) > 0 || count(explode(',', $data)) > 0)) {

			if ((checkdate(substr($data, 2, 2), substr($data, 0, 2), substr($data, 4, 4)))) {
				return substr($data, 0, 2) . '/' . substr($data, 2, 2) . '/' . substr($data, 4, 4);
			} elseif (checkdate(substr($data, 4, 2), substr($data, 6, 2), substr($data, 0, 4))) {
                    //20101220
				return substr($data, 6, 2) . '/' . substr($data, 4, 2) . '/' . substr($data, 0, 4);
			} elseif (checkdate(substr($data, 6, 2), substr($data, 4, 2), substr($data, 0, 4))) {
                    //20102012
				return substr($data, 4, 2) . '/' . substr($data, 6, 2) . '/' . substr($data, 0, 4);
			} else {
				return '99/99/9999';
			}
		} else {
                //12/20/99 20-11-65
			if (!is_numeric(substr($data, -4))) {
				$yy = substr($data, -2);
				if ($yy <= 99 && $yy > date("y")) {
					$yy += 1900;
				} else {
					$yy += 2000;
				}
				if (is_numeric(substr($data, 0, 2)) && is_numeric(substr($data, 3, 2))) {
					$estado =substr($data, 0, 2) . '/' . substr($data, 3, 2) . '/' . $yy;
				}
				if ((checkdate(substr($data, 0, 2), substr($data, 3, 2), substr($data, 6, 4)))) {
					return substr($data, 3, 2) . '/' . substr($data, 0, 2) . '/' . substr($data, 6, 4);
				} elseif (checkdate(substr($data, 3, 2), substr($data, 0, 2), substr($data, 6, 4))) {
					return substr($data, 0, 2) . '/' . substr($data, 3, 2) . '/' . substr($data, 6, 4);
				} else {
					return '99/99/9999';
				}
			}
		}
	} elseif (strlen($data) == 10) {
		if (count(explode("/", $data)) > 1) {
			$estado =explode("/", $data);
			if (checkdate($data[1], $data[0], $data[2])) {
				return $data[0] . '/' . $data[1] . '/' . $data[2];
			} else {
				return '99/99/9999';
			}
		} elseif (count(explode("-", $data)) > 1) {
			$estado =explode("-", $data);
			if (checkdate($data[1], $data[0], $data[2])) {
				return $data[0] . '/' . $data[1] . '/' . $data[2];
			} else {
				return '99/99/9999';
			}
		} elseif (count(explode(",", $data)) > 1) {

			$estado =explode(",", $data);
			if (checkdate($data[1], $data[0], $data[2])) {
				return $data[0] . '/' . $data[1] . '/' . $data[2];
			} else {
				return '99/99/9999';
			}
		} elseif (count(explode(".", $data)) > 1) {

			$estado =explode(".", $data);
			if (checkdate($data[1], $data[0], $data[2])) {
				return $data[0] . '/' . $data[1] . '/' . $data[2];
			} else {
				return '99/99/9999';
			}
		} else {
			return '99/99/9999';
		}
	}
	return '99/99/9999';
}

function validaHora($hora)
{

	if ($hora == '') {
		return '99';
	}
	if ($hora == 'NAO INFORMADO' || $hora == '999') {
		return '99';
	}
	if (count(explode(":", $hora)) > 1) {
		$hora = explode(":", $hora);
		if (!empty($hora) && $hora[0] <= 24) {
			return $hora[0];
		} else {
			return '99';
		}
	} else {
		if (strlen($hora) === 4) {
			if (substr($hora, 0, 2) < 24) {
				return substr($hora, 0, 2);
			} else {
				return '99';
			}
		} else if (strlen($hora) === 3) {
			if (substr($hora, 0, 1) < 24) {
				return '0' . \substr($hora, 0, 1);
			} else {
				return '99';
			}
		}  else if (strlen($hora) === 2) {
			if($hora < 24){
				return $hora;
			}else{
				return 00;
			}
		} else {
			return '00';
		}
	}
	return '99';
}

function validaTipoAcidente($tipoAcidente) {


	$tipoAcidente =  trim(strtoupper(tirarAcentos($tipoAcidente)));
	$tiposAcidente = \App\Models\QuadroMultiplo::getTiposAcidente();

	if(in_array($tipoAcidente, $tiposAcidente)){
		return $tipoAcidente;
	}	

	if ($tipoAcidente == 'CHOQUE MECANICO COM VITIMA' || $tipoAcidente == 'COLISAO' || $tipoAcidente == 'CHOQUE' || $tipoAcidente == 'CHOQUE MECANICO SEM VITIMA' || $tipoAcidente == 'COLISAO DE VEICULOS COM VITIMA') {
		return 'COLISÃO';
	}
	if ($tipoAcidente == 'ABALROAMENTO COM VITIMA' || $tipoAcidente == 'ABALROAMENTO') {
		return 'ABALROAMENTO';
	}
	if ($tipoAcidente == 'ATROPELAMENTO DE PESSOA SEM VITIMA FATAL' || $tipoAcidente == 'ATROPELAMENTO' || $tipoAcidente == 'ATROPELAMENTO DE PESSOA COM VITIMA FATAL' || $tipoAcidente == 'ATROPELAMENTO DE ANIMAL COM VITIMA') {
		return 'ATROPELAMENTO';
	}
	if ($tipoAcidente == 'CAPOTAMENTO/TOMBAMENTO COM VITIMA' || $tipoAcidente == 'CAPOTAMENTO' || $tipoAcidente == 'CAPOTAMENTO/TOMBAMENTO SEM VITIMA') {
		return 'CAPOTAGEM';
	}

	if ($tipoAcidente == 'OUTROS COM VITIMA' ||
		$tipoAcidente == 'QUEDA DE PESSOA DE VEICULO' ||
		$tipoAcidente == 'QUEDA E/OU VAZAMENTO DE CARGA DE VEICULO C/ VITIMA' ||
		$tipoAcidente == 'VAZAMENTO DE CARGA DE VEICULO' ||
		$tipoAcidente == 'QUEDA DE PESSOA') {

		return 'OUTRO';
}
if ($tipoAcidente == '' || $tipoAcidente == 'NAO INFORMADO' || $tipoAcidente == 'SEM INFORMACAO' ||
	$tipoAcidente == ' ' ) {
	return 'NAO INFORMADO';
}
if ($tipoAcidente == 'ATROPELAMENTO DE ANIMAL') {
	return 'ATROPELAMENTO DE ANIMAL';
}
if ($tipoAcidente == 'ABALROAMENTO TRANSVERSAL') {
	return 'ABALROAMENTO TRANSVERSAL';
}
if ($tipoAcidente == 'CHOQUE COM VEICULO ESTACIONADO') {
	return 'CHOQUE COM VEICULO ESTACIONADO';
}
if ($tipoAcidente == 'COLISAO FRONTAL' || $tipoAcidente == 'COLISÃO FRONTAL') {
	return 'COLISÃO FRONTAL';
}
if ($tipoAcidente == 'ABALROAMENTO LATERAL NO MESMO SENTIDO') {
	return 'ABALROAMENTO LATERAL NO MESMO SENTIDO';
}
if ($tipoAcidente == 'SAIDA DE PISTA') {
	return 'SAÍDA DE PISTA';
}
if ($tipoAcidente == 'TOMBAMENTO') {
	return 'TOMBAMENTO';
}
if ($tipoAcidente == 'COLISAO TRASEIRA' || $tipoAcidente == 'COLISÃO TRASEIRA') {
	return 'COLISÃO TRASEIRA';
}
if ($tipoAcidente == 'CHOQUE COM OBJETO FIXO') {
	return 'CHOQUE COM OBJETO FIXO';
}
if ($tipoAcidente == 'ABALROAMENTO LATERAL SENTIDO OPOSTO') {
	return 'ABALROAMENTO LATERAL SENTIDO OPOSTO';
}
if ($tipoAcidente == 'ATROPELAMENTO DE ANIMAL') {
	return 'ATROPELAMENTO DE ANIMAL';
}
if ($tipoAcidente == 'ABALROAMENTO TRANSVERSAL') {
	return 'ABALROAMENTO TRANSVERSAL';
}

return 'OUTRO';
}

function validaCondicaoVitima($CondicaoVitima) {
	$CondicaoVitima = trim(strtoupper(tirarAcentos($CondicaoVitima)));
	if ($CondicaoVitima === 'CONDUTOR DE BICICLETA' || $CondicaoVitima === 'BICICLETEIRO') {
		return 'CICLISTA';
	}
	if ($CondicaoVitima === 'COND AUTO' || $CondicaoVitima === 'COND AUTOMOVEL') {
		return 'CONDUTOR AUTOMOVEL';
	}
	if ($CondicaoVitima === 'COND ONIBUS' || $CondicaoVitima === 'COND BUS') {
		return 'CONDUTOR ONIBUS';
	}
	if ($CondicaoVitima === 'COND CAMINHAO' || $CondicaoVitima === 'VEICULO PESADO') {
		return 'CONDUTOR VEICULO PESADO';
	}
	if ($CondicaoVitima === 'NI' || $CondicaoVitima === '' || $CondicaoVitima === 'NAO INFORMADO') {
		return 'NAO INFORMADO';
	}

	if ($CondicaoVitima == '' ||
		$CondicaoVitima == 'CONDUTOR' ||
		$CondicaoVitima == 'PEDESTRE' ||
		$CondicaoVitima == 'CONDUTOR AUTOMOVEL' ||
		$CondicaoVitima == 'CONDUTOR MOTO' ||
		$CondicaoVitima == 'CONDUTOR VEICULO PESADO' ||
		$CondicaoVitima == 'CONDUTOR ONIBUS' ||
		$CondicaoVitima == 'CONDUTOR OUTROS' ||
		$CondicaoVitima == 'NAO INFORMADO' ||
		$CondicaoVitima == 'CICLISTA' ||
		$CondicaoVitima == 'PASSAGEIRO') {
		return $CondicaoVitima;
} else {
	return 'NAO INFORMADO';
}
}

function validaFonteDados($FonteDados) {
	$FonteDados = trim(strtoupper(tirarAcentos($FonteDados)));

	if ($FonteDados == 'BOMBEIROS' || $FonteDados == 'CORPO DE BOMBEIROS' || $FonteDados == 'BOMBEIRO') {
		return 'CORPO DE BOMBEIROS';
	} else if ($FonteDados == 'PRF' || $FonteDados == 'POLICIA RODOVIARIA FEDERAL') {
		return 'POLICIA RODOVIARIA FEDERAL';
	} else if ($FonteDados == 'PM' || $FonteDados == 'POLICIA MILITAR') {
		return 'POLICIA MILITAR';
	} else if ($FonteDados == 'SAMU' || $FonteDados == 'SERVICO DE URGENCIA' || $FonteDados == 'AMBULANCIA') {
		return 'SAMU';
	} else if ($FonteDados == 'DETRAN' || $FonteDados == 'DEPARTAMENTO DE TRANSITO ESTADUAL' || $FonteDados == 'ORGAO ESTADUAL DE TRANSITO') {
		return 'DETRAN';
	} else if ($FonteDados == 'IML' || $FonteDados == 'INSTITUTO MEDICO LEGAL') {
		return 'IML';
	} else if ($FonteDados == 'DELEGACIA DE TRANSITO') {
		return 'DELEGACIA DE TRANSITO';
	} else if ($FonteDados == 'ORGAO MUNICIPAL DE TRANSITO' || $FonteDados == 'BHTRANS' || $FonteDados == 'SECRETARIA MUNICIPAL DE TRANSITO' || $FonteDados == 'TRANSCOM' || $FonteDados == 'REDS' || $FonteDados == 'BHTRANS/REDS') {
		return 'ORGAO MUNICIPAL DE TRANSITO';
	} else if ($FonteDados == 'OUTRO' || $FonteDados == 'OUTROS') {
		return 'OUTRO';
	} else {
		return 'OUTRO';
	}
}
function converterEstado($estado) {
	$estado = trim(strtoupper(tirarAcentos($estado)));
	switch ($estado) {
		/* Estados */
		case "ACRE" :					$estado ="AC";	break;
		case "ALAGOAS" :				$estado ="AL";	break;
		case "AMAZONAS" :				$estado ="AM";	break;
		case "AMAPÁ" :					$estado ="AP";	break;
		case "BAHIA" :					$estado ="BA";	break;
		case "CEARÁ" :					$estado ="CE";	break;
		case "DISTRITO FEDERAL" :		$estado ="DF";	break;
		case "ESPÍRITO SANTO" :			$estado ="ES";	break;
		case "GOIÁS" :					$estado ="GO";	break;
		case "MARANHÃO" :				$estado ="MA";	break;
		case "MINAS GERAIS" :			$estado ="MG";	break;
		case "MATO GROSSO DO SUL" :		$estado ="MS";	break;
		case "MATO GROSSO" :			$estado ="MT";	break;
		case "PARÁ" :					$estado ="PA";	break;
		case "PARAÍBA" :				$estado ="PB";	break;
		case "PERNAMBUCO" :				$estado ="PE";	break;
		case "PIAUÍ" :					$estado ="PI";	break;
		case "PARANÁ" :					$estado ="PR";	break;
		case "RIO DE JANEIRO" :			$estado ="RJ";	break;
		case "RIO GRANDE DO NORTE" :	$estado ="RN";	break;
		case "RONDÔNIA" : 				$estado ="RO";	break;
		case "RORAIMA" :				$estado ="RR";	break;
		case "RIO GRANDE DO SUL" :		$estado ="RS";	break;
		case "SANTA CATARINA" :			$estado ="SC";	break;
		case "SERGIPE" :				$estado ="SE";	break;
		case "SÃO PAULO" :				$estado ="SP";	break;
		case "TOCANTÍNS" :				$estado ="TO";	break;
	}

	if( $estado  == "AC" || 
		$estado  == "AL" || 
		$estado  == "AM" || 
		$estado  == "AP" ||  
		$estado  == "BA" ||  
		$estado  == "CE" ||  
		$estado  == "DF" ||  
		$estado  == "ES" || 
		$estado  == "GO" ||  
		$estado  == "MA" || 
		$estado  == "MG" ||  
		$estado  == "MS" || 
		$estado  == "MT" ||  
		$estado  == "PA" ||  
		$estado  == "PB" ||  
		$estado  == "PE" ||  
		$estado  == "PI" ||  
		$estado  == "PR" ||  
		$estado  == "RJ" ||  
		$estado  == "RN" ||  
		$estado  == "RO" ||  
		$estado  == "RR" ||  
		$estado  == "RS" ||  
		$estado  == "SC" ||  
		$estado  == "SE" || 
		$estado  == "SP" || 
		$estado  == "TO")
	{
		return $estado;
	}else{
		return '';
	}
	return '';
}
function converterUF($uf) {
	switch ($uf) {
		/* UFs */
		case "AC" :	$uf = "Acre";					break;
		case "AL" :	$uf = "Alagoas";				break;
		case "AM" :	$uf = "Amazonas";				break;
		case "AP" :	$uf = "Amapá";					break;
		case "BA" :	$uf = "Bahia";					break;
		case "CE" :	$uf = "Ceará";					break;
		case "DF" :	$uf = "Distrito Federal";		break;
		case "ES" :	$uf = "Espírito Santo";		break;
		case "GO" :	$uf = "Goiás";					break;
		case "MA" :	$uf = "Maranhão";				break;
		case "MG" :	$uf = "Minas Gerais";			break;
		case "MS" :	$uf = "Mato Grosso do Sul";	break;
		case "MT" :	$uf = "Mato Grosso";			break;
		case "PA" :	$uf = "Pará";					break;
		case "PB" :	$uf = "Paraíba";				break;
		case "PE" :	$uf = "Pernambuco";			break;
		case "PI" :	$uf = "Piauí";					break;
		case "PR" :	$uf = "Paraná";				break;
		case "RJ" :	$uf = "Rio de Janeiro";		break;
		case "RN" :	$uf = "Rio Grande do Norte";	break;
		case "RO" :	$uf = "Rondônia";				break;
		case "RR" :	$uf = "Roraima";				break;
		case "RS" :	$uf = "Rio Grande do Sul";		break;
		case "SC" :	$uf = "Santa Catarina";		break;
		case "SE" :	$uf = "Sergipe";				break;
		case "SP" :	$uf = "São Paulo";				break;
		case "TO" :	$uf = "Tocantíns";				break;
	}
	return strtoupper(tirarAcentos($uf));
}
