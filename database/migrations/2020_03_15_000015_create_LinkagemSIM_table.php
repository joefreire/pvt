<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLinkagemsimTable extends Migration
{
    /**
     * Schema table name to migrate
     * @var string
     */
    public $tableName = 'linkagem_sim';

    /**
     * Run the migrations.
     * @table LinkagemSIM
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->tableName, function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('Ano');
            $table->unsignedInteger('Trimestre');
            $table->unsignedInteger('CodCidade');
            $table->unsignedInteger('idListaUnica');
            $table->unsignedInteger('idUploadSIM');
            $table->unsignedInteger('idQuadroMultiplo');
            $table->unsignedInteger('user_id');
            $table->integer('Score');
            $table->tinyInteger('ParVerdadeiro')->nullable()->default(null);
            $table->string('TipoFalso', 20)->nullable()->default('Não Verificado');
            $table->timestamps();
            $table->softDeletes();

            
            $table->foreign('idQuadroMultiplo')->references('id')->on('quadro_multiplo');
            $table->foreign('idListaUnica')->references('id')->on('vitimas_quadro_multiplo');
            $table->foreign('idUploadSIM')->references('id')->on('upload_sim');
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
