<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Schema;

class Projeto extends Model implements Auditable
{
	use \OwenIt\Auditing\Auditable;
	use SoftDeletes;

	protected $primaryKey = 'id';
	protected $guarded = [
		'id'
	];
	protected $table = 'projetos';
	public $timestamps = true;
	protected $appends = ['total', 'realizado','NomesProgramas','realizadoPerCent'];

	protected $no_uppercase = [
		'Ano',
		'Trimestre',
		'CodCidade',
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
	public function filterFields()
	{
		return collect($this)->except($this->relatorio)->toArray();
	}
	public function getTableColumns()
	{
		$colunas = array_diff(Schema::getColumnListing($this->table),$this->relatorio);
		return $colunas;
	}
	public function planos()
	{
		return $this->belongsToMany('App\Models\Plano', 'projeto_plano', 'idProjeto', 'idPlano')->withPivot('PesoPlano');
	}
	public function user()
	{
		return $this->HasOne(User::class, 'id', 'user_id');
	}
	public function Cidade()
	{
		return $this->HasOne(Cidades::class, 'codigo', 'CodCidade');
	}
	public function getTotalAttribute()
	{
		$valor = $this->Janeiro+
		$this->Fevereiro+
		$this->Marco+
		$this->Abril+
		$this->Maio+
		$this->Junho+
		$this->Julho+
		$this->Agosto+
		$this->Setembro+
		$this->Novembro+
		$this->Dezembro;

		return round($valor, 2);

	}
	public function getNomesProgramasAttribute()
	{
		$valor = $this->planos->pluck('NomePrograma')->toArray();
		$valor = implode (", ", $valor);
		return $valor;
	}
	public function getRealizadoAttribute()
	{
		if($this->ObjetivoProjeto != '' && $this->ObjetivoProjeto > 0){
			$valor = ($this->total / $this->ObjetivoProjeto ) * 100;
		}else{
			$valor = 0;
		}
		return round($valor, 2);
	}
	public function getRealizadoPerCentAttribute()
	{
		return $this->realizado.'%';
	}
	public function getCreatedAtAttribute($value)
	{
		return (!empty($value) ? \Carbon\Carbon::parse($value)->format('d/m/Y') : null);
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
