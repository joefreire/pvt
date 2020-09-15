<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLinkagemsihTable extends Migration
{
    /**
     * Schema table name to migrate
     * @var string
     */
    public $tableName = 'linkagem_sih';

    /**
     * Run the migrations.
     * @table LinkagemSIH
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->tableName, function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('CodCidade');
            $table->unsignedInteger('Ano');
            $table->unsignedInteger('user_id');
            $table->unsignedTinyInteger('Trimestre');
            $table->unsignedInteger('idListaUnica');
            $table->unsignedInteger('idUploadSIH');
            $table->unsignedInteger('idQuadroMultiplo');
            $table->integer('Score');
            $table->tinyInteger('ParVerdadeiro')->nullable()->default(null);
            $table->string('TipoFalso', 20)->nullable()->default('Não Verificado');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('idQuadroMultiplo')->references('id')->on('quadro_multiplo');
            $table->foreign('idListaUnica')->references('id')->on('vitimas_quadro_multiplo');
            $table->foreign('idUploadSIH')->references('id')->on('upload_sih');
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
