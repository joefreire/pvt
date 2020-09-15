<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Schema;
class QuadroMultiplo extends Model implements Auditable
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
	protected $table = 'quadro_multiplo';
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
		'user_id',
		'deleted_at',
		'created_at',
		'updated_at',
		'id',
	];
    protected $casts = [
        'HoraAcidente' => 'integer',
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
	public function LinkagemSim()
	{
		return $this->HasMany(LinkagemSim::class, 'idQuadroMultiplo', 'id');
	}
	public function LinkagemSih()
	{
		return $this->HasMany(LinkagemSih::class, 'idQuadroMultiplo', 'id');
	}
	public function vitimas()
	{
		return $this->HasMany(Vitimas::class, 'idQuadroMultiplo', 'id');
	}
	public function FatoresRisco()
	{
		return $this->HasOne(FatoresRisco::class, 'idQuadroMultiplo', 'id');
	}
	public function getCreatedAtAttribute($value)
	{
		return (!empty($value) ? \Carbon\Carbon::parse($value)->format('d/m/Y') : null);
	}
	public function getDataAcidenteAttribute($value)
	{
		$data = $this->getOriginal('DataAcidente');
		$data = validaData($data);
		if($data == "99/99/9999"){
			return '99/99/9999';
		}else{
			return (!empty($data) ? \Carbon\Carbon::createFromFormat('d/m/Y',$data)->format('d/m/Y') : null);
		}
	}
	public function getQtdVitimasAttribute($value)
	{
		return $this->vitimas->count();
	}
	public function getDiaSemanaAttribute($value)
	{
		if($this->DataAcidente == "99/99/9999"){
			return 'Sem Informação';
		}
		if(!empty($this->DataAcidente)){
			try {
				$data = \Carbon\Carbon::createFromFormat('d/m/Y',$this->DataAcidente);
				return $data->locale('pt_BR')->dayName;
			} catch (\ErrorException $e) {
				return 'Sem Informação';
			}
		}else{
			return 'Sem Informação';
		} 
	}
	public function getQtdTiposVitimasAttribute($value)
	{
		$fatal = 0;
		$graves = 0;
		$outras = 0;
		if($this->qtd_vitimas == 0){
			return ['fatal'=>$fatal, 'graves'=>$graves, 'outras'=>$outras];
		}else{
			foreach ($this->vitimas as $vitima) {
				if( $vitima->GravidadeLesao == 'FATAL' 
					|| $vitima->GravidadeLesao == 'FATAL LOCAL' 
					|| $vitima->GravidadeLesao == 'FATAL POSTERIOR'){
					$fatal++;
			}elseif($vitima->GravidadeLesao == 'MODERADA' || 
				$vitima->GravidadeLesao == 'GRAVE' ||
				$vitima->GravidadeLesao == 'LESOES LEVES' ||
				$vitima->GravidadeLesao = 'COM LESOES'){
				$graves++;
			}else{
				$outras++;
			}
		}
		return ['fatal'=> (int)$fatal, 'graves'=>(int)$graves, 'outras'=>(int)$outras];
	}
}
public static function getTiposAcidente()
{
	$tiposAcidente = 
	array(
		'ABALROAMENTO',
		'ABALROAMENTO LATERAL NO MESMO SENTIDO',
		'ATROPELAMENTO',
		'ATROPELAMENTO DE ANIMAL',
		'CAPOTAGEM',
		'CHOQUE',
		'CHOQUE COM VEICULO ESTACIONADO',
		'COLISAO',
		'COLISAO TRASEIRA',
		'COLISAO FRONTAL',
		'SAIDA DE PISTA',
		'TOMBAMENTO',
		'OUTRO',
		'NAO INFORMADO',
	);

	return $tiposAcidente;
}
public function getUpdatedAtAttribute($value)
{
	return (!empty($value) ? \Carbon\Carbon::parse($value)->format('d/m/Y') : null);
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
