<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
use Illuminate\Database\Eloquent\SoftDeletes;
use \Carbon\Carbon;
use Illuminate\Support\Facades\Schema;
class Vitimas extends Model implements Auditable
{
	use \OwenIt\Auditing\Auditable;
	use SoftDeletes;
	protected $primaryKey = 'id';
	protected $guarded = [
		'id'
	];
	protected $with =['LinkagemSim.sim', 'LinkagemSih.sih'];
	protected $auditEvents = [
		'deleted',
		'updated',
		'restored',
	];
	protected $table = 'vitimas_quadro_multiplo';
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
		'NomeBusca',
		'user_id',
		'deleted_at',
		'created_at',
		'updated_at',
		'idQuadroMultiplo',
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
		return $this->HasOne(Cidades::class, 'id', 'CodCidade');
	}
	public function QuadroMultiplo()
	{
		return $this->HasOne(QuadroMultiplo::class, 'id', 'idQuadroMultiplo');
	}
	public function LinkagemSim()
	{
		return $this->HasOne(LinkagemSim::class, 'idListaUnica','id');
	}
	public function LinkagemSih()
	{
		return $this->HasOne(LinkagemSih::class, 'idListaUnica','id');
	}
	public function Fatores()
	{
		return $this->HasOne(FatoresRisco::class, 'idQuadroMultiplo','idQuadroMultiplo');
	}
	public function getCreatedAtAttribute($value)
	{
		return (!empty($value) ? \Carbon\Carbon::parse($value)->format('d/m/Y') : null);
	}
	public function getUpdatedAtAttribute($value)
	{
		return (!empty($value) ? \Carbon\Carbon::parse($value)->format('d/m/Y') : null);
	}

	public function getMunicipioResidenciaAttribute($value)
	{
		if(!empty($this->MunicipioVitima)){
			return $this->MunicipioVitima;
		}else{
			return 'Sem Informação';
		} 
	}
	public function getDtNascContrarioAttribute($value)
	{
		$data = $this->getOriginal('DataNascimento');
		$dataParte = explode('/', $data);
		if(isset($dataParte[0]) && isset($dataParte[1]) && isset($dataParte[2])){
			return $dataParte[1].'/'.$dataParte[0].'/'.$dataParte[2];
		}
	}
	public function getDtAcidenteContrarioAttribute($value)
	{
		$data = $this->getOriginal('DataAcidente');
		$dataParte = explode('/', $data);
		if(isset($dataParte[0]) && isset($dataParte[1]) && isset($dataParte[2])){
			return $dataParte[1].'/'.$dataParte[0].'/'.$dataParte[2];
		}
	}
	public function getDataNascimentoAttribute($value)
	{
		$data = $this->getOriginal('DataNascimento');
		$data = validaData($data);
		if($data == "99/99/9999"){
			return '99/99/9999';
		}else{
			return (!empty($data) ? \Carbon\Carbon::createFromFormat('d/m/Y',$data)->format('d/m/Y') : null);
		}
		
	}
	public function getDataNascimentoFormatadaAttribute($value)
	{
		$data = $this->getOriginal('DataNascimento');
		if(!empty($data) && $data != ''){
			try{
				$data = Carbon::createFromFormat('d/m/Y',$data);
				return $data;
			}catch(\Exception $err) {
				return null;
			}
		}

	}
	public function getIdadeAttribute($value)
	{
		$data = $this->getOriginal('Idade');
		if($data == null){
			return 999;
		}else{
			return $data;
		}

	}

	public function setAttribute($key, $value)
	{
		parent::setAttribute($key, $value);
		if($this->attributes[$key] == 'Idade' && $this->value == null){
			$this->attributes[$key] = 999;
			$this->attributes['FaixaEtaria'] = 'SEM INFORMAÇÃO';
		}
		if (is_string($value)) {
			if($this->no_uppercase !== null){
				if (!in_array($key, $this->no_uppercase)) {					
					$this->attributes[$key] = mb_convert_case(trim($value), MB_CASE_UPPER, "UTF-8");					
				}
			}
		}
	}
}
