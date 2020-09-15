<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUploadsihTable extends Migration
{
    /**
     * Schema table name to migrate
     * @var string
     */
    public $tableName = 'upload_sih';

    /**
     * Run the migrations.
     * @table UploadSIH
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->tableName, function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('Ano');
            $table->unsignedInteger('Trimestre');
            $table->unsignedInteger('CodCidade');
            $table->unsignedInteger('user_id');
            $table->string('NUM_AIH')->nullable()->default(null);
            $table->string('NOME');
            $table->string('NOMEBUSCA');
            $table->string('DT_NASC')->nullable()->default(null);
            $table->string('SEXO')->nullable()->default(null);

            $table->string('RACA_COR')->nullable()->default(null);
            $table->string('NOME_RESP')->nullable()->default(null);
            $table->string('NOME_MAE')->nullable()->default(null);
            $table->string('LOGR')->nullable()->default(null);
            $table->string('LOGR_N')->nullable()->default(null);
            $table->string('LOGR_BAIR', 255)->nullable()->default(null);
            $table->string('LOGR_COMPL')->nullable()->default(null);
            $table->string('MUNICIP', 150)->nullable()->default(null);
            $table->string('CEP', 80)->nullable()->default(null);
            $table->string('DIAG_PRI')->nullable()->default(null);
            $table->string('DIAG_SEC')->nullable()->default(null);
            $table->string('DIAG_OBITO')->nullable()->default(null);
            $table->string('DT_EMISSAO', 80)->nullable()->default(null);
            $table->string('DT_INTERNA', 80)->nullable()->default(null);
            $table->string('DT_SAIDA', 80)->nullable()->default(null);            
            
           
            $table->string('PROC_SOLIC')->nullable()->default(null);
            $table->string('PROC_REALI')->nullable()->default(null);
            $table->string('MOT_SAIDA')->nullable()->default(null);
            
            $table->string('PRONTUARIO')->nullable()->default(null);           
            $table->string('FONE')->nullable()->default(null);
            $table->string('CNS')->nullable()->default(null);
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
