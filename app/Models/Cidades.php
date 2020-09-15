<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cidades extends Model
{
   	protected $primaryKey = 'codigo';
	protected $guarded = [
		'codigo'
	];
	protected $table = 'municipios_ibge';
	public $timestamps = false;
}
