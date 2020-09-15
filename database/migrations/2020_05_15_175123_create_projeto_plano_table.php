<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProjetoPlanoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('projeto_plano', function (Blueprint $table) {
            $table->unsignedInteger('idPlano');
            $table->unsignedInteger('idProjeto');
            $table->unsignedInteger('PesoPlano')->default(0);

            $table->foreign('idProjeto')->references('id')->on('projetos');
            $table->foreign('idPlano')->references('id')->on('plano_acoes');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('projeto_plano');
    }
}
