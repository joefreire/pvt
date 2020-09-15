<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePlanoAcoesTable extends Migration
{
    /**
     * Schema table name to migrate
     * @var string
     */
    public $tableName = 'plano_acoes';

    /**
     * Run the migrations.
     * @table plano_acoes
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
            $table->string('NomePrograma');
            $table->string('PesoPrograma', 3);
            $table->text('ObjetivoPrograma');
            $table->text('Publico')->nullable()->default(null);
            $table->string('IndicadorIntermediarioPrograma');
            $table->string('MetaIntermediaria', 10);
            $table->string('MetaIntermediariaDescritiva');
            $table->string('IndicadorFinalPrograma');
            $table->string('MetaFinal', 10);
            $table->string('MetaFinalDescritiva');
            $table->string('CoordenadorPrograma');
            $table->string('ParceriasPublicas', 10)->nullable()->default(0);
            $table->string('ParceriasPrivadas', 10)->nullable()->default(0);
            $table->string('ParceriasCivil', 10)->nullable()->default(0);
            $table->text('SecretariasEnvolvidas')->nullable()->default(null);

            $table->timestamps();
            $table->softDeletes();

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
