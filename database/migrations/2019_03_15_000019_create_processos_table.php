<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProcessosTable extends Migration
{
    /**
     * Schema table name to migrate
     * @var string
     */
    public $tableName = 'processos';

    /**
     * Run the migrations.
     * @table processos
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->tableName, function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('user_id');
            $table->unsignedInteger('CodCidade');
            $table->unsignedTinyInteger('Trimestre');
            $table->unsignedInteger('Ano');
            $table->string('Processo');
            $table->integer('Status')->default(0)->comment = '0-Iniciado | 1-Finalizado | 2-Cancelado | 3-Error';
            $table->longText('Log')->nullable()->default(null);
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('CodCidade')->references('codigo')->on('municipios_ibge');
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
