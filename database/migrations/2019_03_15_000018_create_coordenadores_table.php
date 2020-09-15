<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCoordenadoresTable extends Migration
{
    /**
     * Schema table name to migrate
     * @var string
     */
    public $tableName = 'coordenadores';

    /**
     * Run the migrations.
     * @table coordenadores
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->tableName, function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('Ano');
            $table->unsignedInteger('CodCidade'); 
            $table->unsignedInteger('user_id');
            $table->string('coordenaTEM', 5); 
            $table->string('Nome')->nullable()->default(null);
            $table->string('Instiuicao')->nullable()->default(null);
            $table->string('Email')->nullable()->default(null);
            $table->string('Telefone')->nullable()->default(null);
            $table->string('Telefone1')->nullable()->default(null);
            $table->string('Coordenador2')->nullable()->default(null);
            $table->string('Instituicao2')->nullable()->default(null);
            $table->string('Email2')->nullable()->default(null);
            $table->string('Telefone2')->nullable()->default(null);
            $table->string('Telefone2_2')->nullable()->default(null);
            $table->string('Coordenador3')->nullable()->default(null);
            $table->string('Instituicao3')->nullable()->default(null);
            $table->string('Email3')->nullable()->default(null);
            $table->string('Telefone3')->nullable()->default(null);
            $table->string('Telefone3_2')->nullable()->default(null);                     
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
     public function down()
     {
       Schema::dropIfExists($this->tableName);
     }
}
