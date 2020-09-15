<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateQuadroMultiploTable extends Migration
{
    /**
     * Schema table name to migrate
     * @var string
     */
    public $tableName = 'quadro_multiplo';

    /**
     * Run the migrations.
     * @table quadro_multiplo
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
            $table->string('FonteDados', 250);
            $table->string('Boletim', 250);
            $table->string('IdentificadorAcidente', 501);
            $table->string('DataAcidente', 50);
            $table->string('IdentificadorAcidente2', 100)->nullable()->default(null);
            $table->string('IdentificadorAcidente3', 100)->nullable()->default(null);
            $table->string('TipoAcidente', 250)->nullable()->default(null);
            $table->unsignedInteger('HoraAcidente')->nullable()->default(null);
            $table->text('RuaAvenida')->nullable()->default(null);
            $table->string('Numero')->nullable()->default(null);
            $table->string('Bairro')->nullable()->default(null);
            $table->string('Complemento', 200)->nullable()->default(null);
            $table->string('Quadra')->nullable()->default(null);
            $table->string('Lote')->nullable()->default(null);
            $table->string('CidadeAcidente')->nullable()->default(null);
            $table->string('EstadoAcidente')->nullable()->default(null);
            $table->string('CepAcidente')->nullable()->default(null);
            $table->string('VelocidadeVia')->nullable()->default(null);
            $table->string('CoordenadaX', 80)->nullable()->default(null);
            $table->string('CoordenadaY', 80)->nullable()->default(null);
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
