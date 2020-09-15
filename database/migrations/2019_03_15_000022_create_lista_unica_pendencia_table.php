<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateListaUnicaPendenciaTable extends Migration
{
    /**
     * Schema table name to migrate
     * @var string
     */
    public $tableName = 'lista_unica_pendencia';

    /**
     * Run the migrations.
     * @table lista_unica
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->tableName, function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('Ano');
            $table->unsignedTinyInteger('Trimestre');
            $table->unsignedInteger('CodCidade');
            $table->unsignedInteger('user_id');
            $table->string('FonteDados')->nullable()->default(null);
            $table->string('Boletim')->nullable()->default(null);
            $table->string('IdentificadorAcidente')->nullable()->default(null);
            $table->string('DataAcidente')->nullable()->default(null);
            $table->string('HoraAcidente')->nullable()->default(null);
            $table->string('NomeCompleto')->nullable()->default(null);
            $table->string('NomeMae')->nullable()->default(null);
            $table->string('DataNascimento')->nullable()->default(null);
            $table->string('Sexo')->nullable()->default(null);
            $table->string('CondicaoVitima')->nullable()->default(null);
            $table->string('TipoAcidente')->nullable()->default(null);
            $table->string('GravidadeLesao')->nullable()->default(null);
            $table->string('TipoVeiculo')->nullable()->default(null);
            $table->string('Placa')->nullable()->default(null);
            $table->string('CepAcidente')->nullable()->default(null);
            $table->string('TipoLogradouro')->nullable()->default(null);
            $table->string('RuaAvenida')->nullable()->default(null);
            $table->string('Numero')->nullable()->default(null);
            $table->string('Bairro')->nullable()->default(null);
            $table->string('Complemento')->nullable()->default(null);
            $table->string('Quadra')->nullable()->default(null);
            $table->string('Lote')->nullable()->default(null);
            $table->string('LocalAcidenteVia')->nullable()->default(null);
            $table->string('CidadeAcidente')->nullable()->default(null);
            $table->string('EstadoAcidente')->nullable()->default(null);
            $table->string('VelocidadeVia')->nullable()->default(null);
            $table->string('CoordenadaX')->nullable()->default(null);
            $table->string('CoordenadaY')->nullable()->default(null);
            $table->string('Descricao')->nullable()->default(null);
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
