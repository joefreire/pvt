<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;
class Sih extends Model implements Auditable
{
	use \OwenIt\Auditing\Auditable;
	use SoftDeletes;

	protected $primaryKey = 'id';
	protected $guarded = [
		'id'
	];
	protected $auditEvents = [
		'deleted',
		'updated',
		'restored',
	];
	protected $table = 'upload_sih';
	protected $appends = ['idade','faixa_etaria'];
	public $timestamps = true;

	protected $no_uppercase = [
		'Ano',
		'Trimestre',
		'CodCidade',
		'user_id',
		'deleted_at',
		'created_at',
		'updated_at',
	];
	protected $relatorio  = [
		'Ano',
		'Trimestre',
		'CodCidade',
		'NOMEBUSCA',
		'user_id',
		'deleted_at',
		'created_at',
		'updated_at',
		'id',
	];

	public function filterFields()
	{
		return collect($this)->except($this->relatorio)->toArray();
	}
	public function getTableColumns()
	{
		$colunas = array_diff(Schema::getColumnListing($this->table),$this->relatorio);
		return $colunas;
	}
	public function user()
	{
		return $this->HasOne(User::class, 'id', 'user_id');
	}
	public function Cidade()
	{
		return $this->HasOne(Cidades::class, 'codigo', 'CodCidade');
	}	
	public function Linkagem()
	{
		return $this->HasMany(LinkagemSih::class, 'idUploadSIH', 'id');
	}	
	public function getCreatedAtAttribute($value)
	{
		return (!empty($value) ? \Carbon\Carbon::parse($value)->format('d/m/Y') : null);
	}
	public function getUpdatedAtAttribute($value)
	{
		return (!empty($value) ? \Carbon\Carbon::parse($value)->format('d/m/Y') : null);
	}
	//TODO: nascimento SIH
	public function getIdadeAttribute($value)
	{		
		if($this->DT_NASC == "99/99/9999" || $this->DT_INTERNA == "99/99/9999"){
			return 'Sem Informação';
		}
		if(!empty($this->DT_NASC) && !empty($this->DT_INTERNA)){
			try {
				$DT_NASC = \Carbon\Carbon::createFromFormat('d/m/Y',$this->DT_NASC);
				$DT_INTERNA = \Carbon\Carbon::createFromFormat('d/m/Y',$this->DT_INTERNA);
				return $DT_NASC->diffInYears($DT_INTERNA);
			} catch (\ErrorException $e) {
				return 'Sem Informação';
			}
		}else{
			return 'Sem Informação';
		} 
	}
	public function getFaixaEtariaAttribute($value)
	{
		if(!empty($this->idade) && $this->idade != 'Sem Informação'){
			if($this->idade <= 4){
				return "Menor que 4 anos";
			}elseif($this->idade >= 5 && $this->idade <= 9){
				return "5 a 9 Anos";
			}elseif($this->idade >= 10 && $this->idade <= 17){
				return "10 a 17 Anos";
			}elseif($this->idade >= 18 && $this->idade <= 24){
				return "18 a 24 Anos";
			}elseif($this->idade >= 25 && $this->idade <= 29){
				return "25 a 29 Anos";
			}elseif($this->idade >= 30 && $this->idade <= 39){
				return "30 a 39 Anos";
			}elseif($this->idade >= 40 && $this->idade <= 49){
				return "40 a 49 Anos";
			}elseif($this->idade >= 50 && $this->idade <= 59){
				return "50 a 59 Anos";
			}elseif($this->idade >= 60 && $this->idade <= 69){
				return "60 a 69 Anos";
			}else{
				return "Maior que 70 Anos";
			}
		}else{
			return 'Sem Informação';
		} 
	}
	public function getSexoAttribute($value)
	{

		$sexo = trim(strtoupper(tirarAcentos($this->getOriginal('SEXO'))));
		
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
	public function getPrimeiroNomeAttribute($value)
	{
		$nome = trim(strtoupper(tirarAcentos($this->getOriginal('NOME'))));
		$nome = explode(' ', $nome);
		if(isset($nome[0])){
			return $nome[0];
		}else{
			return null;
		}
		
	}
	public function getUltimoNomeAttribute($value)
	{
		$nome = trim(strtoupper(tirarAcentos($this->getOriginal('NOME'))));
		$nome = explode(' ', $nome);
		if(count($nome)> 0){
			return end($nome);
		}else{
			return null;
		}
		
	}
	public function getDataObitoFormatadaAttribute($value)
	{
		$data = $this->getOriginal('DTOBITO');
		if(!empty($data) && $data != ''){
			try{
				$data = Carbon::createFromFormat('d/m/Y',$data);
				return $data;
			}catch(\Exception $err) {
				return null;
			}
		}
	}
	public function getDataNascimentoFormatadaAttribute($value)
	{
		$data = $this->getOriginal('DTNASC');
		if($data == "99/99/9999"){
			return 'Sem Informação';
		}
		if(!empty($data) && $data != ''){
			try{
				$data = Carbon::createFromFormat('d/m/Y',$data);
				return $data;
			}catch(\Exception $err) {
				return null;
			}
		}

	}
	public function getDtNascContrarioAttribute($value)
	{
		$data = $this->getOriginal('DTNASC');
		$dataParte = explode('/', $data);
		if(isset($dataParte[0]) && isset($dataParte[1]) && isset($dataParte[2])){
			return $dataParte[1].'/'.$dataParte[0].'/'.$dataParte[2];
		}
	}
	public function getDtAcidenteContrarioAttribute($value)
	{
		$data = $this->getOriginal('DTOBITO');
		$dataParte = explode('/', $data);
		if(isset($dataParte[0]) && isset($dataParte[1]) && isset($dataParte[2])){
			return $dataParte[1].'/'.$dataParte[0].'/'.$dataParte[2];
		}
	}
	public function setAttribute($key, $value)
	{
		parent::setAttribute($key, $value);
		if (is_string($value)) {
			if($this->no_uppercase !== null){
				if (!in_array($key, $this->no_uppercase)) {					
					$this->attributes[$key] = mb_convert_case(trim($value), MB_CASE_UPPER, "UTF-8");					
				}
			}
		}
	}
}
