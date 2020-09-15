<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAcoesTable extends Migration
{
    /**
     * Schema table name to migrate
     * @var string
     */
    public $tableName = 'acoes';

    /**
     * Run the migrations.
     * @table acoes
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
            $table->string('ACOESINTEGRADAS', 10);
            $table->string('ULTIMOACOESINTEGRADAS', 10);
            $table->text('PRINCIPAISACOESINTEGRADAS');

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
