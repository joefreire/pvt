<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Schema;

class FatoresRisco extends Model implements Auditable
{
	use \OwenIt\Auditing\Auditable;
	use SoftDeletes;

	protected $primaryKey = 'id';
	protected $guarded = [
		'id'
	];
	protected $appends = ['total'];
	protected $table = 'fatores_risco_quadro_multiplo';
	public $timestamps = true;

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
		'idQuadroMultiplo',
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
	public function QuadroMultiplo()
	{
		return $this->HasOne(QuadroMultiplo::class, 'id', 'idQuadroMultiplo');
	}
	public function getTotalAttribute($value)
	{
		return $this->Velocidade+
		$this->Alcool+
		$this->Veiculo+
		$this->Infraestrutura+
		$this->Fadiga+
		$this->Visibilidade+
		$this->Drogas+
		$this->Distacao+
		$this->AvancarSinal+
		$this->CondutorSemHabilitacao+
		$this->LocalProibido+
		$this->LocalImproprio+
		$this->MudancaFaixa+
		$this->DistanciaMinima+
		$this->Preferencia+
		$this->PreferenciaPedestre+
		$this->ImprudenciaPedestre+
		$this->Capacete+
		$this->CintoSeguranca+
		$this->EquipamentoProtecao+
		$this->GerenciamentoTrauma+
		$this->ObjetosLateraisVia+
		$this->outra_protecao;
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
