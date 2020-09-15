<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUploadsimTable extends Migration
{
    /**
     * Schema table name to migrate
     * @var string
     */
    public $tableName = 'upload_sim';

    /**
     * Run the migrations.
     * @table UploadSIM
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
            $table->string('NOME')->nullable()->default(null);
            $table->string('DTOBITO')->nullable()->default(null);
            $table->string('CAUSABAS')->nullable()->default(null);          
            $table->string('NOMEMAE')->nullable()->default(null);
            $table->string('NOMEPAI')->nullable()->default(null);
            $table->string('SEXO')->nullable()->default(null);
            $table->string('DTNASC')->nullable()->default(null);
            $table->string('NUMERODO')->nullable()->default(null);
            $table->string('NUMERODV')->nullable()->default(null);
            $table->string('NOMEBUSCA')->nullable()->default(null);
            $table->string('TIPOBITO')->nullable()->default(null);
            $table->string('HORAOBITO')->nullable()->default(null);
            $table->string('NATURAL')->nullable()->default(null);
            $table->string('CODMUNNATU')->nullable()->default(null);
            $table->string('IDADE')->nullable()->default(null);
            $table->string('RACACOR')->nullable()->default(null);
            $table->string('LOCOCOR')->nullable()->default(null);
            $table->string('CODMUNOCOR')->nullable()->default(null);
            $table->string('BAIOCOR')->nullable()->default(null);
            $table->string('ENDOCOR')->nullable()->default(null);
            $table->string('NUMENDOCOR')->nullable()->default(null);
            $table->string('COMPLOCOR')->nullable()->default(null);
            $table->string('CEPOCOR')->nullable()->default(null);
            
            $table->string('CAUSABAS_O')->nullable()->default(null);
            $table->string('ENDRES')->nullable()->default(null);
            $table->string('BAIRES', 200)->nullable()->default(null);
            $table->string('CODMUNRES', 200)->nullable()->default(null);
            $table->string('NUMRES', 200)->nullable()->default(null);
            $table->string('COMPLRES', 200)->nullable()->default(null);
            $table->string('CEPRES')->nullable()->default(null);

            $table->string('NUMSUS', 200)->nullable()->default(null);
            $table->string('LINHAA', 200)->nullable()->default(null);
            $table->string('LINHAB', 200)->nullable()->default(null);
            $table->string('LINHAC', 200)->nullable()->default(null);
            $table->string('LINHAD', 200)->nullable()->default(null);
            $table->string('LINHAII', 200)->nullable()->default(null);
            $table->string('LINHAA_O', 200)->nullable()->default(null);
            $table->string('LINHAB_O', 200)->nullable()->default(null);
            $table->string('LINHAC_O', 200)->nullable()->default(null);
            $table->string('LINHAD_O', 200)->nullable()->default(null);
            $table->string('LINHAII_O', 200)->nullable()->default(null);
            $table->string('DTCADASTRO', 200)->nullable()->default(null);
            $table->string('DTREGCART', 200)->nullable()->default(null);
            $table->string('CODMUNCART', 200)->nullable()->default(null);
            $table->string('CODCART', 200)->nullable()->default(null);
            $table->string('NUMREGCART', 200)->nullable()->default(null);
            $table->string('CODESTCART', 200)->nullable()->default(null);
            $table->string('ESTCIV', 200)->nullable()->default(null);
            $table->string('OCUP', 200)->nullable()->default(null);
            $table->string('ASSISTMED', 200)->nullable()->default(null);
            $table->string('CIRURGIA', 200)->nullable()->default(null);
            $table->string('NECROPSIA', 200)->nullable()->default(null);
            $table->string('ESTCIVIL', 200)->nullable()->default(null);
            $table->string('EXAME', 200)->nullable()->default(null);
            $table->string('FONTE', 200)->nullable()->default(null);
            $table->string('CONTATO', 200)->nullable()->default(null);
            $table->string('DTATESTADO', 200)->nullable()->default(null);
            $table->string('ATESTANTE', 200)->nullable()->default(null);
            $table->string('NUMERODN', 200)->nullable()->default(null);
            $table->string('DSTEMPO', 200)->nullable()->default(null);
            $table->string('DSEXPLICA', 200)->nullable()->default(null);
            $table->string('MEDICO', 200)->nullable()->default(null);
            $table->string('CRM', 200)->nullable()->default(null);
            $table->string('CIRCOBITO', 200)->nullable()->default(null);
            $table->string('ACIDTRAB', 200)->nullable()->default(null);
            $table->string('DSEVENTO', 200)->nullable()->default(null);
            $table->string('ENDACID', 200)->nullable()->default(null);
            $table->string('NUMEROLOTE', 200)->nullable()->default(null);
            $table->string('TPPOS', 200)->nullable()->default(null);
            $table->string('DTINVESTIG', 200)->nullable()->default(null);
            $table->string('CRITICA', 200)->nullable()->default(null);
            $table->string('ESC', 200)->nullable()->default(null);
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
