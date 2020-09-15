<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateInstituicoesTable extends Migration
{
    /**
     * Schema table name to migrate
     * @var string
     */
    public $tableName = 'instituicoes';

    /**
     * Run the migrations.
     * @table qualidade
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
            $table->unsignedInteger('idSalva');
            $table->string('NOME');
            $table->string('SETOR');
            $table->string('ORIGEM');
            $table->string('TABELA');

            $table->timestamps();
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
