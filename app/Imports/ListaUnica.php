<?php

namespace App\Imports;

use App\Models\Vitimas;
use App\Models\QuadroMultiplo;
use App\Models\ListaUnicaPendencias;
use Maatwebsite\Excel\Concerns\ToArray;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\Importable;
use App\Models\Processo;
use DB;
class ListaUnica implements ToArray, WithHeadingRow, WithMultipleSheets, ShouldAutoSize
{

	public $dataImport;
    public $timeout = 0;

    /**
     * ListaUnica constructor.
     * @param $dataImport
     */
    public function __construct($dataImport)
    {
        set_time_limit(0);
        $this->dataImport = $dataImport;
    }
    /**
     * @param array $$row
     *
     * @return User|null
     */
    public function array(array $rows)
    {

    	$dataImport = $this->dataImport;
    	$processo = Processo::find($dataImport['processo']->id);
    	$pendencia = 0;
    	$sucesso = 0;
    	$error = 0;

        DB::beginTransaction();
        try{
           foreach ($rows as $row){


              if(array_filter($row)) {
                 $row['nome_completo']   = validaNomeCompleto($row['nome_completo']);
                 $row['nome_da_mae']        = validaNomeCompleto($row['nome_da_mae']);
                 $row['boletim']        = validaNomeCompleto($row['boletim']);
                 $row['condicao_da_vitima'] = validaCondicaoVitima($row['condicao_da_vitima']);
                 $row['gravidade_da_lesao'] = validaTipoLesao($row['gravidade_da_lesao']);
                 $row['tipo_veiculo']    = validaTipoVeiculo($row['tipo_veiculo']);
                 $row['sexo']          = validaSexo($row['sexo']);
                 $row['tipo_acidente']  = validaTipoAcidente($row['tipo_acidente']);
                 $row['hora_do_acidente']   = validaHora($row['hora_do_acidente']);
                 $row['fonte_de_dados']     = validaFonteDados($row['fonte_de_dados']);
            	//verifica se as datas estão como tipo date
                 if(gettype($row['data_do_acidente']) == 'integer' && $row['data_do_acidente'] != null){
                    $row['data_do_acidente'] = $this->transformDate($row['data_do_acidente'])
                    ->format('d/m/Y');
                }else{
                    $row['data_do_acidente']   = validaData($row['data_do_acidente']);
                }
                if(gettype($row['data_de_nascimento']) == 'integer' && $row['data_de_nascimento'] != null){
                    $row['data_de_nascimento'] = $this->transformDate($row['data_de_nascimento'])
                    ->format('d/m/Y');
                }else{
                    $row['data_de_nascimento']   = validaData($row['data_de_nascimento']);
                }
                $row['uf_acidente']   = converterEstado($row['uf_acidente']);

                if ($row['data_do_acidente'] == '99/99/9999' || 
                    empty($row['fonte_de_dados']) ||
                    empty($row['boletim'])  ||
                    empty($row['nome_completo']) ){
                    $pendencia++;
                $ListaUnicaPendencias = ListaUnicaPendencias::create([
                    'Ano'                   => $dataImport['Ano'],
                    'Trimestre'             => $dataImport['Trimestre'],
                    'CodCidade'             => $dataImport['CodCidade'],
                    'DataAcidente'          => $row['data_do_acidente'],
                    'DataNascimento'        => $row['data_de_nascimento'],
                    'NomeMae'               => $row['nome_da_mae'],
                    'Sexo'                  => $row['sexo'],
                    'CondicaoVitima'        => $row['condicao_da_vitima'],
                    'IdentificadorAcidente' => $row['fonte_de_dados'] . '/' . $row['boletim'],
                    'Boletim'               => $row['boletim'],
                    'FonteDados'            => $row['fonte_de_dados'],
                    'NomeCompleto'          => $row['nome_completo'],
                    'TipoAcidente'          => $row['tipo_acidente'],
                    'GravidadeLesao'        => $row['gravidade_da_lesao'],
                    'TipoLogradouro'        => $row['tipo_logradouro'],
                    'RuaAvenida'            => $row['endereco_do_acidente'],
                    'Placa'                 => $row['placa'],
                    'HoraAcidente'          => $row['hora_do_acidente'],
                    'Descricao'             => $row['descricao'],
                    'Numero'                => $row['numero'],
                    'Bairro'                => $row['bairro'],
                    'Complemento'           => $row['complemento'],
                    'Quadra'                => $row['quadra'],
                    'Lote'                  => $row['lote'],
                    'VelocidadeVia'         => $row['velocidade_via'],
                    'CidadeAcidente'        => $row['cidade_acidente'],
                    'EstadoAcidente'        => $row['uf_acidente'],
                    'VelocidadeVia'         => $row['velocidade_via'],
                    'CoordenadaX'           => $row['coordenada_x'],
                    'CoordenadaY'           => $row['coordenada_y'],
                    'user_id'               => $dataImport['user_id'],
                ]);
            }else{    			
             if(empty($row['cidade_acidente'])){
                $row['cidade_acidente'] = $dataImport['CidadeAcidente'];
            }
            if(empty($row['uf_acidente'])){
                $row['uf_acidente'] = $dataImport['EstadoAcidente'];
            }
            $buscaAcidente = QuadroMultiplo::where('IdentificadorAcidente', $row['fonte_de_dados'].'/'.$row['boletim'])
            ->where('Trimestre',$dataImport['Trimestre'])
            ->where('Ano',$dataImport['Ano'])
            ->where('CodCidade',$dataImport['CodCidade'])
            ->first();
            if(empty($buscaAcidente)){
                    //calcula lat long
                if(!empty($row['endereco_do_acidente']) 
                    &&  empty($row['bairro'])                     
                    && ( empty($row['coordenada_x']) && empty($row['coordenada_y']) ) ) 
                {
                    if(isset($ultimaConsulta)){
                        if($ultimaConsulta->diffInSeconds(\Carbon\Carbon::now() < 1)){
                            sleep(1);
                        }
                    }else{
                        $ultimaConsulta = \Carbon\Carbon::now();
                    }

                    $admin = new \App\Http\Controllers\AdminController();
                    $request = new \Illuminate\Http\Request();
                    $request['endereco'] = $row['endereco_do_acidente'].
                    ', '.$row['numero'].
                    ' '.$row['lote'].
                    ' '.$row['quadra'].
                    ' - '.$row['bairro'].
                    ' '.$row['cidade_acidente'].
                    ' '.$row['uf_acidente'].
                    ' Brazil';
                    $data = $admin->getCoordenada($request);
                    $data = $data->getData();
                    if(isset($data->lat) && isset($data->lon)){                        
                        $row['coordenada_x'] = $data->lat;
                        $row['coordenada_y'] = $data->lon;
                    }
                }

                $quadro = QuadroMultiplo::create([
                    'Ano'   => $dataImport['Ano'],
                    'Trimestre'   => $dataImport['Trimestre'],
                    'CodCidade'   => $dataImport['CodCidade'],
                    'DataAcidente'          => $row['data_do_acidente'],
                    'Boletim'               => $row['boletim'],
                    'FonteDados'            => $row['fonte_de_dados'],
                    'TipoAcidente'          => $row['tipo_acidente'],
                    'VelocidadeVia'         => $row['velocidade_via'],
                    'HoraAcidente'          => $row['hora_do_acidente'],
                    'RuaAvenida'            => $row['endereco_do_acidente'],
                    'Numero'                => $row['numero'],
                    'Bairro'                => $row['bairro'],
                    'Complemento'           => $row['complemento'],
                    'Quadra'                => $row['quadra'],
                    'Lote'                  => $row['lote'],
                    'CidadeAcidente'        => $row['cidade_acidente'],
                    'EstadoAcidente'        => $row['uf_acidente'],
                    'VelocidadeVia'         => $row['velocidade_via'],
                    'CoordenadaX'           => $row['coordenada_x'],
                    'CoordenadaY'           => $row['coordenada_y'],
                    'IdentificadorAcidente' => $row['fonte_de_dados'] . '/' . $row['boletim'],
                    'user_id'               => $dataImport['user_id'],
                ]);
            }else{
                $quadro = $buscaAcidente;
            }
            $idade = calculaIdade($row['data_de_nascimento'], $row['data_do_acidente']);
            $FaixaEtaria = calculaFaixaEtaria($idade);

            $vitima = Vitimas::create([
                'idQuadroMultiplo'   => $quadro->id,
                'Ano'   => $dataImport['Ano'],
                'Trimestre'   => $dataImport['Trimestre'],
                'CodCidade'   => $dataImport['CodCidade'],
                'NomeCompleto'=> $row['nome_completo'],
                'NomeBusca'   => \BuscaBR::encode($row['nome_completo']),
                'NomeMae'        => $row['nome_da_mae'],
                'DataNascimento' => $row['data_de_nascimento'],
                'Idade' => $idade,
                'FaixaEtaria' => $FaixaEtaria,
                'GravidadeLesao' => $row['gravidade_da_lesao'],
                'Sexo'           => $row['sexo'],
                'MeioTransporte' => $row['tipo_veiculo'],
                'CondicaoVitima' => $row['condicao_da_vitima'],
                'Placa'          => $row['placa'],
                'Descricao'      => $row['descricao'],
                'DataAcidente'   => $row['data_do_acidente'],
                'user_id'        => $dataImport['user_id'],
            ]);
            $sucesso++;
        }
    	}//vazio
    }//each
    DB::commit();   
    $processo->Status = 1;
    $processo->Log = "Sucesso :".$sucesso." Pendências: ".$pendencia." Erros: ".$error;
    $processo->save();
} catch (\Exception $e) {
    DB::rollBack();
    $processo->Status = 3;
    $processo->Log = "Error ao processar lista unica";
    $processo->save();
    \Log::alert('Erro ao importar SIM : '.$e->getMessage());
}
return true;

}
public function chunkSize(): int
{
	return 5;
}
public function sheets(): array
{
	return [
		0 => $this,
	];
}
/**
 * Transform a date value into a Carbon object.
 *
 * @return \Carbon\Carbon|null
 */
public function transformDate($value, $format = 'd/m/Y')
{
	try {
		return \Carbon\Carbon::instance(\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($value));
	} catch (\ErrorException $e) {
		return \Carbon\Carbon::createFromFormat($format, $value);
	}
}
}
