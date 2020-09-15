<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProjetosTable extends Migration
{
    /**
     * Schema table name to migrate
     * @var string
     */
    public $tableName = 'projetos';

    /**
     * Run the migrations.
     * @table ProjetosAcoes
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
            $table->string('TipoProjeto', 20);
            $table->string('PesoProjeto', 10);
            $table->string('NomeProjeto');
            $table->string('UnidadeProjeto')->nullable()->default(null);
            $table->string('ResponsavelProjeto');
            $table->string('ParceiroAtividade')->nullable()->default(null);
            $table->text('DescricaoProjeto')->nullable()->default(null);
            $table->text('ObjetivoProjeto')->nullable()->default(null);
            $table->double('CustoProjeto')->default(0.0);
            $table->double('Janeiro')->default(0.0);
            $table->double('Fevereiro')->default(0.0);
            $table->double('Marco')->default(0.0);
            $table->double('Abril')->default(0.0);
            $table->double('Maio')->default(0.0);
            $table->double('Junho')->default(0.0);
            $table->double('Julho')->default(0.0);
            $table->double('Agosto')->default(0.0);
            $table->double('Setembro')->default(0.0);
            $table->double('Outubro')->default(0.0);
            $table->double('Novembro')->default(0.0);
            $table->double('Dezembro')->default(0.0);
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
