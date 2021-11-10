<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Processo extends Model
{
	protected $primaryKey = 'id';
	protected $guarded = [
		'id'
	];
	protected $table = 'processos';
	public $timestamps = true;

	public function cidade()
	{
		return $this->HasOne(Cidades::class, 'codigo', 'CodCidade');
	}	
	public function user()
	{
		return $this->HasOne(User::class, 'id', 'user_id');
	}
}
