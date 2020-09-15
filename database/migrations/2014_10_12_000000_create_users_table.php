<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('nome');
            $table->string('instituicao')->nullable()->default(null);
            $table->string('formacao')->nullable()->default(null);
            $table->string('telefone')->nullable()->default(null);
            $table->string('atividade')->nullable()->default(null);
            $table->unsignedInteger('CodCidade');
            $table->string('email')->unique();
            $table->string('password');
            $table->integer('reset_senha')->default('1');
            $table->integer('tipo')->default('4');
            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('CodCidade')->references('codigo')->on('municipios_ibge');
        });
        
        DB::table('users')->insert(
            array('nome'=>'Administrador',
               'instituicao' => 'Admin', 
               'CodCidade' => 310620, 
               'email' => 'admin@pvt.com.br',
               'password' => \Hash::make('123123'), 
               'reset_senha' => 0,
               'tipo' => 1 )
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
