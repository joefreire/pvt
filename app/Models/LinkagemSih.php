<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
use Illuminate\Database\Eloquent\SoftDeletes;
use \Carbon\Carbon;
class LinkagemSih extends Model implements Auditable
{
	use \OwenIt\Auditing\Auditable;
	use SoftDeletes;
	protected $primaryKey = 'id';
	protected $guarded = [
		'id'
	];
	protected $table = 'linkagem_sih';
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
	public function getCreatedAtAttribute($value)
	{
		return (!empty($value) ? \Carbon\Carbon::parse($value)->format('d/m/Y') : null);
	}
	public function getUpdatedAtAttribute($value)
	{
		return (!empty($value) ? \Carbon\Carbon::parse($value)->format('d/m/Y') : null);
	}
	public function vitima()
	{
		return $this->HasOne(Vitimas::class, 'id', 'idListaUnica');
	}
	public function quadro_multiplo()
	{
		return $this->HasOne(QuadroMultiplo::class, 'id', 'idQuadroMultiplo');
	}
	public function sih()
	{
		return $this->HasOne(Sih::class, 'id', 'idUploadSIH');
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
