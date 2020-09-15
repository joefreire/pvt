<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateVitimasquadromultiploTable extends Migration
{
    /**
     * Schema table name to migrate
     * @var string
     */
    public $tableName = 'vitimas_quadro_multiplo';

    /**
     * Run the migrations.
     * @table VitimasQuadroMultiplo
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->tableName, function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('idQuadroMultiplo');
            $table->unsignedInteger('Ano');
            $table->unsignedTinyInteger('Trimestre');
            $table->unsignedInteger('CodCidade');
            $table->unsignedInteger('user_id');
            $table->string('DataAcidente', 50);
            $table->string('GravidadeLesao', 50);
            $table->string('NomeCompleto');
            $table->string('NomeBusca')->nullable()->default(null);
            $table->string('NomeMae')->nullable()->default(null);
            $table->string('DataNascimento', 50);
            $table->string('Sexo', 20)->nullable()->default(null);
            $table->string('MeioTransporte', 50)->nullable()->default(null);
            $table->string('CondicaoVitima', 50)->nullable()->default(null);
            $table->string('Placa')->nullable()->default(null);
            $table->string('InfluenciaAlcool', 20)->nullable()->default(null);
            $table->string('ComprovaAlcoolemia', 60)->nullable()->default(null);
            $table->string('ValorAlcoolemia', 50)->nullable()->default(null);
            $table->string('ComprovaBafometro', 50)->nullable()->default(null);
            $table->string('ValorBafometro', 50)->nullable()->default(null);
            $table->string('EnderecoVitima')->nullable()->default(null);
            $table->string('BairroVitima')->nullable()->default(null);
            $table->string('NumeroVitima')->nullable()->default(null);
            $table->string('CEPVitima')->nullable()->default(null);
            $table->string('MunicipioVitima', 100)->nullable()->default(null);
            $table->string('EstadoVitima')->nullable()->default(null);
            $table->string('CoordVitimaX')->nullable()->default(null);
            $table->string('CoordVitimaY')->nullable()->default(null);
            $table->longText('Descricao')->nullable()->default(null);
            $table->string('NUMSUS')->nullable()->default(null);
            $table->string('CBO')->nullable()->default(null);
            $table->timestamps();
            $table->softDeletes();
            
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('idQuadroMultiplo')->references('id')->on('quadro_multiplo')->onDelete('cascade');
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
