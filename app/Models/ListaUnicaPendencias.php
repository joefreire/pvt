<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class ListaUnicaPendencias extends Model implements Auditable
{
	use \OwenIt\Auditing\Auditable;
	protected $primaryKey = 'id';
	protected $guarded = [
		'id'
	];
	protected $table = 'lista_unica_pendencia';
	public $timestamps = true;

	protected $no_uppercase = [
		'Ano',
		'Trimestre',
		'CodCidade',
	];
	protected $auditEvents = [
		'deleted',
		'updated',
		'restored',
	];
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
