<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateImplantacaoTable extends Migration
{
    /**
     * Schema table name to migrate
     * @var string
     */
    public $tableName = 'implantacao';

    /**
     * Run the migrations.
     * @table implantacao
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
            $table->string('COMISSAO');
            $table->string('DTDECRETO');
            $table->string('DECRETO');
            $table->string('PERIODIC');
            $table->string('REGREUNIAOCI');
            $table->string('DTREUNIAOCI');
            $table->string('DTREUNIAOCPVT');
            $table->string('REGREUNIAOCPVT');
            $table->string('UPDECRETO');
            $table->string('NOMECOMISSAO');
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
