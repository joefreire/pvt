<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Notifications\ResetPassword;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use OwenIt\Auditing\Contracts\Auditable;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable implements Auditable
{
   use \OwenIt\Auditing\Auditable;
   use Notifiable;
   use SoftDeletes;


    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $guarded = [
        'id'
    ];
    protected $with       = ['cidade'];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'reset_senha',
    ];
    public function cidade()
    {
        return $this->HasOne(Cidades::class, 'codigo', 'CodCidade');
    }
    public function getCreatedAtAttribute($value)
    {
        return (!empty($value) ? \Carbon\Carbon::parse($value)->format('d/m/Y') : null);
    }



}
