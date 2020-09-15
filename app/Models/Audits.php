<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Audits extends Model
{
	protected $primaryKey = 'id';
	protected $guarded = [
		'id'
	];
	protected $table = 'audits';
	public $timestamps = true;
	public function user()
	{
		return $this->HasOne(User::class, 'id', 'user_id');
	}
}
