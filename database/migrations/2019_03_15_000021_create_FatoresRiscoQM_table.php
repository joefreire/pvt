<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFatoresriscoqmTable extends Migration
{
    /**
     * Schema table name to migrate
     * @var string
     */
    public $tableName = 'fatores_risco_quadro_multiplo';

    /**
     * Run the migrations.
     * @table FatoresRiscoQM
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
    		$table->integer('Velocidade')->default(0);
    		$table->string('TipoVelocidade', 80)->nullable()->default(null);
    		$table->string('UsuarioContributivo_Velocidade', 80)->nullable()->default(null);
    		$table->integer('Alcool')->default(0);
    		$table->string('UsuarioContributivo_Alcool', 80)->nullable()->default(null);
    		$table->integer('Veiculo')->default(0);
    		$table->string('UsuarioContributivo_Veiculo', 80)->nullable()->default(null);
    		$table->integer('Infraestrutura')->default(0);
    		$table->string('TipoInfraestrutura', 80)->nullable()->default(null);
    		$table->integer('Fadiga')->default(0);
    		$table->string('UsuarioContributivo_Fadiga', 80)->nullable()->default(null);
    		$table->integer('Visibilidade')->default(0);
    		$table->integer('Drogas')->default(0);
    		$table->string('UsuarioContributivo_Drogas', 80)->nullable()->default(null);
    		$table->string('TipoDroga', 100)->nullable()->default(null);
    		$table->integer('Distacao')->default(0);
    		$table->string('UsuarioContributivo_Distacao', 80)->nullable()->default(null);
    		$table->string('TipoDistracao')->nullable()->default(null);
    		$table->integer('AvancarSinal')->default(0);
    		$table->string('UsuarioContributivo_AvancarSinal', 80)->nullable()->default(null);
    		$table->string('CondutorSemHabilitacao')->default(0);
    		$table->string('UsuarioContributivo_CondutorSemHabilitacao', 80)->nullable()->default(null);
    		$table->integer('LocalProibido')->default(0);
    		$table->string('UsuarioContributivo_LocalProibido', 80)->nullable()->default(null);
    		$table->integer('LocalImproprio')->default(0);
    		$table->string('UsuarioContributivo_LocalImproprio', 80)->nullable()->default(null);
    		$table->integer('MudancaFaixa')->default(0);
    		$table->string('UsuarioContributivo_MudancaFaixa', 80)->nullable()->default(null);
    		$table->integer('DistanciaMinima')->default(0);
    		$table->string('UsuarioContributivo_DistanciaMinima', 80)->nullable()->default(null);
    		$table->integer('Preferencia')->default(0);
    		$table->string('UsuarioContributivo_Preferencia', 80)->nullable()->default(null);
    		$table->string('PreferenciaPedestre')->default(0);
    		$table->string('UsuarioContributivo_PreferenciaPedestre', 80)->nullable()->default(null);
    		$table->integer('ImprudenciaPedestre')->default(0);
    		$table->string('UsuarioContributivo_ImprudenciaPedestre', 80)->nullable()->default(null);
    		$table->integer('Capacete')->default(0);
    		$table->string('UsuarioContributivo_Capacete', 80)->nullable()->default(null);
    		$table->integer('CintoSeguranca')->default(0);
    		$table->string('UsuarioContributivo_CintoSeguranca', 80)->nullable()->default(null);
    		$table->integer('EquipamentoProtecao')->default(0);
    		$table->integer('GerenciamentoTrauma')->default(0);
    		$table->integer('ObjetosLateraisVia')->default(0);
    		$table->integer('outra_protecao')->default(0);
    		$table->string('definicao_outra_protecao')->nullable()->default(null);
    		$table->timestamps();
            $table->softDeletes();

    		$table->foreign('idQuadroMultiplo')->references('id')->on('quadro_multiplo');
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
