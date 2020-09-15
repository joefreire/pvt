<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAnaliseTable extends Migration
{
    /**
     * Schema table name to migrate
     * @var string
     */
    public $tableName = 'analise';

    /**
     * Run the migrations.
     * @table analise
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
            $table->string('IDENTIFICACAORISCO', 10);
            $table->string('ULTIMORISCO', 10);
            $table->string('FATORESRISCOACIDENTES', 10);
            $table->string('FATORESRISCOACIDENTES_SIM', 40)->nullable()->default(null);
            $table->string('AMOSTRA')->nullable()->default(null);
            $table->string('CONDUTARISCOACIDENTES', 10);
            $table->string('FATORESGRAVIDADE', 10);
            $table->string('FATORESFATAL', 10);
            $table->string('IDENTIFICACAORISCOCADA', 10);
            $table->string('ULTIMORISCOCADA', 10);
            $table->string('FATORESRISCOACIDENTESCADA', 10);
            $table->string('CONDUTARISCOACIDENTESCADA', 10);
            $table->string('FATORESGRAVIDADECADA', 10);
            $table->string('FATORESFATALCADA', 10);
            $table->string('FATORESCHAVE', 10);
            $table->string('ULTIMOFATORESCHAVE', 10);
            $table->text('PRINCIPAISFATORESCHAVE');
            $table->string('GRUPOSVITIMAS', 10);
            $table->string('ULTIMOGRUPOSVITIMAS', 10);
            $table->text('PRINCIPAISGRUPOSVITIMAS');
            $table->string('CONSTRUCAOQUADROMULTIPLO', 10);
            $table->string('ULTIMOCONSTRUCAOQUADROMULTIPLO', 10);
            $table->string('PROGRAMAPRIORITARIOS', 10);
            $table->string('ULTIMOPROGRAMAPRIORITARIOS', 10);
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
