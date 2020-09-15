<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateQualidadeTable extends Migration
{
    /**
     * Schema table name to migrate
     * @var string
     */
    public $tableName = 'qualidade';

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
            $table->string('COMISSAOGD', 15);
            $table->string('COMISSAOFORM', 15);
            $table->string('COMISSAODOC', 15);
            $table->string('DTCOMISSAO', 15);
            $table->string('NCOMISSAO', 20);
            $table->string('UPDECRETOCOMISSAO');
            $table->text('BASESAT');
            $table->text('BASESOBITO');
            $table->text('BASEFERIDO');
            $table->string('MAPEAMENTO', 15);
            $table->string('LIMPEZA', 15);
            $table->string('LISTAUNICA', 15);
            $table->string('FATORRISCO', 15);
            $table->string('INDICADOROBITO', 15);
            $table->text('LINKAGE');
            $table->string('PRILINKAGE', 80);
            $table->string('ULTLINKAGE', 80);
            $table->string('COMOFOILISTAVITIMAS');
            $table->string('NAOLINKOBITO', 15);
            $table->string('NAOLINKFER', 15);
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
