<?php

namespace App\Jobs;
set_time_limit(0);
ini_set('max_execution_time', 0); 
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;

use App\Models\User;
use App\Models\Sih;
use Exception;
use App\Models\Processo;
use App\Models\Vitimas;
use App\Models\LinkagemSih;
use OneSignal;
use DB;
use XBase\Table;

class ProcessaSih implements ShouldQueue
{
	use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

	public $user;
	public $dataImport;
	public $file;
	public $processo;
	public $unica;
	public $buscaSIH;
	public $timeout = 0;
	public $tries = 1;
	/**
	 * Create a new job instance.
	 *
	 * @return void
	 */
	public function __construct(User $user, $dataImport, Processo $processo, $file)
	{
		set_time_limit(0);
		$this->file        = $file;
		$this->dataImport        = $dataImport;
		$this->user        = $user;
		$this->processo        = $processo;
	}

	/**
	 * Execute the job.
	 *
	 * @return void
	 */
	public function handle()
	{
		$this->processo->Status = 0;
		$this->processo->Log = "Arquivo SIH em processamento";
		$this->processo->save();
		
		try {
			$table = new Table($this->file);
			$rows = array_keys($table->columns);
			$qtdRegistros = $table->recordCount;
			$dataImport = $this->dataImport;
			$this->processo->Status = 0;
			$this->processo->Log = "Em processamento";
			$this->processo->save();

			$deletaSih = DB::table('upload_sih')
			->where('Ano',$this->dataImport['Ano'])
			->where('Trimestre', $this->dataImport['Trimestre'])
			->where('CodCidade',$this->dataImport['Trimestre'])->delete();

			$deletaLinkagem = DB::table('linkagem_sih')
			->where('Ano',$this->dataImport['Ano'])
			->where('Trimestre', $this->dataImport['Trimestre'])
			->where('CodCidade',$this->dataImport['Trimestre'])->delete();


			while ($record = $table->nextRecord()) {
				$sih = Sih::create([
					'Ano'                   => $this->dataImport['Ano'],
					'Trimestre'             => $this->dataImport['Trimestre'],
					'CodCidade'             => $this->dataImport['CodCidade'],
					'user_id'               => $this->dataImport['user_id'],

					'NOMEBUSCA'             => \BuscaBR::encode($record->nome),
					'NOME'                  => validaNomeCompleto($record->nome),
					'NUM_AIH'                  => $record->num_aih,

					'DT_NASC'              => in_array('dt_nasc',$rows) ? converteData($record->dt_nasc) : null,
					'DT_INTERNA'              => in_array('dt_interna',$rows) ? converteData($record->dt_interna) : null,
					'DT_SAIDA'              => in_array('dt_saida',$rows) ? converteData($record->dt_saida) : null,
					'DT_EMISSAO'              => in_array('dt_emissao',$rows) ? converteData($record->dt_emissao) : null,
					
					'NOME_RESP'               => in_array('nome_resp',$rows) ? validaNomeCompleto($record->nome_resp) : null,
					'NOME_MAE'               => in_array('nome_mae',$rows) ? validaNomeCompleto($record->nome_mae) : null,
					'SEXO'                  => in_array('sexo',$rows) ? $record->sexo : null,
					'RACA_COR'               => in_array('raca_cor',$rows) ? $record->raca_cor : null,

					'LOGR'               => in_array('logr',$rows) ? $record->logr : null,
					'LOGR_BAIR'               => in_array('logr_bair',$rows) ? $record->logr_bair : null,
					'LOGR_COMPL'               => in_array('logr_compl',$rows) ? $record->logr_compl : null,
					'LOGR_N'               => in_array('logr_n',$rows) ? $record->logr_n : null,
					'MUNICIP'               => in_array('municip',$rows) ? $record->municip : null,
					'CEP'            => in_array('cep',$rows) ? $record->cep : null,
					'PRONTUARIO'            => in_array('prontuario',$rows) ? $record->prontuario : null,
					'FONE'            => in_array('fone',$rows) ? $record->fone : null,
					'DIAG_PRI'            => in_array('diag_pri',$rows) ? $record->diag_pri : null,
					'DIAG_SEC'            => in_array('diag_sec',$rows) ? $record->diag_sec : null,
					'DIAG_OBITO'            => in_array('diag_obito',$rows) ? $record->diag_obito : null,
					'MOT_SAIDA'            => in_array('mot_saida',$rows) ? $record->mot_saida : null,
					'PROC_SOLIC'            => in_array('proc_solic',$rows) ? $record->proc_solic : null,
					'PROC_REALI'            => in_array('proc_reali',$rows) ? $record->proc_reali : null,
					'CNS'            => in_array('cns',$rows) ? $record->cns : null,
					
				]);
			}

			//pega todos os sih
			$this->buscaSIH = Sih::where('Ano',$this->dataImport['Ano'])
			->where('CodCidade',$this->dataImport['CodCidade'])
			->where('Trimestre',$this->dataImport['Trimestre'])->get();


			if ($this->buscaSIH->count() > 0) {
				$this->processo->Status = 0;
				$this->processo->Log = "Arquivo SIH em Linkagem";
				$this->processo->save();
				$pares = 0;
				$vitimasPercorridas = 0;
				$totalVitimas = Vitimas::where('Ano',$this->dataImport['Ano'])
				->where('CodCidade',$this->dataImport['CodCidade'])
				->where('Trimestre',$this->dataImport['Trimestre'])->count();
//vitimas quadro multiplo
				foreach (Vitimas::where('Ano',$this->dataImport['Ano'])
					->where('CodCidade',$this->dataImport['CodCidade'])
					->where('Trimestre',$this->dataImport['Trimestre'])->cursor() as $unica) 
				{	
					if((($vitimasPercorridas/$totalVitimas)*100) > 25 && (($vitimasPercorridas/$totalVitimas)*100) < 26){
						$this->processo->Status = 0;
						$this->processo->Log = "Arquivo SIH em Linkagem - 25%";
						$this->processo->save();
					}
					if((($vitimasPercorridas/$totalVitimas)*100) > 50 && (($vitimasPercorridas/$totalVitimas)*100) < 51){
						$this->processo->Status = 0;
						$this->processo->Log = "Arquivo SIH em Linkagem - 50%";
						$this->processo->save();
					}
					if((($vitimasPercorridas/$totalVitimas)*100) > 75 && (($vitimasPercorridas/$totalVitimas)*100) < 76){
						$this->processo->Status = 0;
						$this->processo->Log = "Arquivo SIH em Linkagem - 75%";
						$this->processo->save();
					}


					$this->unica = $unica;

					$linkagem = $this->linkagem();
					if(!empty($linkagem)){
						$linkagem_sih = LinkagemSih::create([
							'Score' => $linkagem['Score'],
							'idListaUnica' => $unica->id,
							'idUploadSIH' => $linkagem['idUploadSIH'],
							'idQuadroMultiplo' => $unica->idQuadroMultiplo,
							'user_id'   => $this->dataImport['user_id'],
							'Ano'   => $this->dataImport['Ano'],
							'Trimestre'   => $this->dataImport['Trimestre'],
							'CodCidade'   => $this->dataImport['CodCidade'],
						]);
							//dump($linkagem_sih);
						$pares++;							
					}
					$vitimasPercorridas++;
				}
			}
			$this->processo->Status = 1;
			$this->processo->Log = "Sucesso, SIH:".$qtdRegistros." Pares:".$pares;
			$this->processo->save();
			\Log::alert('Sucesso ao importar SIH ');

		} catch (\Exception $e) {
			//dd($e);
			\Log::alert('Erro ao importar SIH : '.$e->getMessage().' '. $e->getLine());
			DB::table('upload_sih')
			->where('Ano',$this->dataImport['Ano'])
			->where('CodCidade',$this->dataImport['CodCidade'])
			->where('Trimestre',$this->dataImport['Trimestre'])
			->delete();
			DB::table('linkagem_sih')
			->where('Ano',$this->dataImport['Ano'])
			->where('CodCidade',$this->dataImport['CodCidade'])
			->where('Trimestre',$this->dataImport['Trimestre'])
			->delete();
			$this->processo->Status = 3;
			$this->processo->Log = "Erro ao processar SIH 2 ".$e->getMessage();
			$this->processo->save();

		}
		// OneSignal::sendNotificationUsingTags(
		// 	"O processo do SIH".$this->processo->id." terminou",
		// 	array(
		// 		["field" => "tag",
		// 		"key" => "user_id",
		// 		"relation" => "=", 
		// 		"value" => $this->user->id]
		// 	),
		// 	$url = null,
		// 	$data = null,
		// 	$buttons = null,
		// 	$schedule = null
		// );
	}
	public function linkagem(){

		$unica = $this->unica;
		$Nome = explode(' ', $unica->NomeCompleto);
		$PrimeiroNome = $Nome[0];
		$SobreNome = end($Nome);

		if($unica->NomeCompleto == 'INGNORADO' 
			|| $unica->NomeCompleto == ''
			|| $unica->NomeCompleto == ' '
			|| $unica->NomeCompleto == 'SEM NOME'
			|| $unica->NomeCompleto == 'SEM INFO'
			|| $unica->NomeCompleto == 'SEM INFORMACAO'
			|| $unica->NomeCompleto == 'NAO IDENTIFICADO'
			|| $unica->NomeCompleto == 'NAO INFORMADO'
			|| $unica->NomeCompleto == 'IGNORADO')
		{
			$buscaEtapa = $this->buscaSIH
			->where('DT_INTERNA', $unica->DataAcidente)
			->where('DT_NASC', $unica->DataNascimento)
			->where('SEXO', $unica->Sexo)
			->first();
			if(!empty($buscaEtapa)){
			//possivel par 85
				return ['idUploadSIH'=>$buscaEtapa->id,'Score' => 80];
			}
			$buscaEtapa = $this->buscaSIH
			->where('DT_INTERNA', $unica->DataAcidente)
			->where('DT_NASC', $unica->DataNascimento)
			->first();
			if(!empty($buscaEtapa)){
					//possivel par 
				return ['idUploadSIH'=>$buscaEtapa->id,'Score' => 79];
			}			
			$buscaEtapa = $this->buscaSIH
			->where('DT_INTERNA', $unica->DataAcidente)
			->first();
			if(!empty($buscaEtapa)){
					//possivel par 
				return ['idUploadSIH'=>$buscaEtapa->id,'Score' => 78];
			}		

			$buscaEtapa = $this->buscaSIH
			->where('CNS', $unica->NUMSUS)
			->where('CNS', '!=', null)
			->where('CNS', '!=', '')
			->first();
			if(!empty($buscaEtapa) && !empty($unica->NUMSUS) ){
				//possivel par
				return ['idUploadSIH'=>$buscaEtapa->id,'Score' => 75];
			}
		}else{

			//Busca com nome igual
			$buscaNome = Sih::where('Ano',$this->dataImport['Ano'])
			->where('CodCidade',$this->dataImport['CodCidade'])
			->where('Trimestre',$this->dataImport['Trimestre'])
			->where('NOME', $unica->NomeCompleto)->get();

			if($buscaNome->count() > 0){
				$buscaEtapa = $buscaNome
				->where('DT_INTERNA', $unica->DataAcidente)
				->where('DT_NASC', $unica->DataNascimento)
				->where('SEXO', $unica->Sexo)->first();
				if(!empty($buscaEtapa)){
					//possivel par 99
					return ['idUploadSIH'=>$buscaEtapa->id,'Score' => 99];
				}
				$buscaEtapa = $buscaNome
				->where('DT_INTERNA', $unica->DataAcidente)
				->filter(function ($q) use ($unica, $PrimeiroNome, $SobreNome) {
					return $q->SEXO == $unica->Sexo || $q->DT_NASC == $unica->DataNascimento;
				})
				->first();
				if(!empty($buscaEtapa)){
						//possivel par 98
					return ['idUploadSIH'=>$buscaEtapa->id,'Score' => 98];
				}
				$buscaEtapa = $buscaNome
				->where('DT_INTERNA', $unica->DataAcidente)
				->first();
				if(!empty($buscaEtapa)){
				//possivel par
					return ['idUploadSIH'=>$buscaEtapa->id,'Score' => 98];
				}
				$buscaEtapa = $buscaNome
				->where('DT_NASC', $unica->DT_NASC)
				->first();
				if(!empty($buscaEtapa)){
				//possivel par
					return ['idUploadSIH'=>$buscaEtapa->id,'Score' => 97];
				}
				$buscaEtapa = $buscaNome
				->where('NOME_MAE', $unica->NomeMae)		
				->where('NOME_MAE', '!=', null)	
				->where('NOME_MAE', '!=', '')		
				->first();
				if(!empty($buscaEtapa)){
				//possivel par
					return ['idUploadSIH'=>$buscaEtapa->id,'Score' => 94];
				}
				$buscaEtapa = $buscaNome
				->where('CNS', $unica->NUMSUS)	
				->where('CNS', '!=', null)
				->where('CNS', '!=', '')
				->first();
				if(!empty($buscaEtapa)){
				//possivel par
					return ['idUploadSIH'=>$buscaEtapa->id,'Score' => 92];
				}
				$buscaEtapa = $buscaNome			
				->first();
				if(!empty($buscaEtapa)){
				//possivel par
					return ['idUploadSIH'=>$buscaEtapa->id,'Score' => 81];
				}

			}
			$buscaDatas =  Sih::where('Ano',$this->dataImport['Ano'])
			->where('CodCidade',$this->dataImport['CodCidade'])
			->where('Trimestre',$this->dataImport['Trimestre'])
			->where('DT_INTERNA', $unica->DataAcidente)
			->where('DT_NASC', $unica->DataNascimento)
			->get();

			if($buscaDatas->count() > 0){
			//busca com dataobito igual
				$buscaEtapa = $buscaDatas
				->where('SEXO', $unica->Sexo)
				->filter(function($q) use ($unica, $PrimeiroNome, $SobreNome) {
					return $q->NOME == $unica->NomeCompleto ||
					$q->NOMEBUSCA == $unica->NomeBusca ||
					($q->primeiro_nome == $PrimeiroNome &&
						$q->ultimo_nome == $SobreNome);
				})		
				->first();
				if(!empty($buscaEtapa)){
				//possivel par 97
					return ['idUploadSIH'=>$buscaEtapa->id,'Score' => 97];
				}
				$buscaEtapa = $buscaDatas
				->filter(function($q) use ($unica, $PrimeiroNome, $SobreNome) {
					return $q->NOME == $unica->NomeCompleto ||
					$q->NOMEBUSCA == $unica->NomeBusca ||
					soundex($q->NomeCompleto) == soundex($unica->NomeCompleto) ||
					($q->primeiro_nome == $PrimeiroNome &&
						$q->ultimo_nome == $SobreNome);
				})		->first();
				if(!empty($buscaEtapa)){
				//possivel par 97
					return ['idUploadSIH'=>$buscaEtapa->id,'Score' => 96];
				}
				$buscaEtapa = $buscaDatas
				->first();
				if(!empty($buscaEtapa)){
				//possivel par
					return ['idUploadSIH'=>$buscaEtapa->id,'Score' => 94];
				}
			}

			$buscaOBITO = $this->buscaSIH
			->where('DT_INTERNA', $unica->DataAcidente);
			if($buscaOBITO->count() > 0){
				$buscaEtapa = $buscaOBITO
				->filter(function($q) use ($unica, $PrimeiroNome, $SobreNome) {
					return $q->NOME == $unica->NomeCompleto ||
					$q->NOMEBUSCA == $unica->NomeBusca ||
					($q->primeiro_nome == $PrimeiroNome &&
						$q->ultimo_nome == $SobreNome);
				})
				->first();
				if(!empty($buscaEtapa)){
				//possivel par 97
					return ['idUploadSIH'=>$buscaEtapa->id,'Score' => 96];
				}
				$buscaEtapa = $buscaOBITO
				->where('CNS', $unica->NUMSUS)
				->where('CNS', '!=', null)
				->where('CNS', '!=', '')
				->first();
				if(!empty($buscaEtapa)){
				//possivel par 97
					return ['idUploadSIH'=>$buscaEtapa->id,'Score' => 90];
				}
				$buscaEtapa = $buscaOBITO
				->where('NOME_MAE', $unica->NomeMae)	
				->where('NOME_MAE', '!=', null)	
				->where('NOME_MAE', '!=', '')	
				->first();
				if(!empty($buscaEtapa)){
				//possivel par 97
					return ['idUploadSIH'=>$buscaEtapa->id,'Score' => 93];
				}
				$buscaEtapa = $buscaOBITO
				->filter(function($q) use ($unica, $PrimeiroNome, $SobreNome) {
					return $q->NOME == $unica->NomeCompleto ||
					$q->NOMEBUSCA == $unica->NomeBusca ||
					soundex($q->NomeCompleto) == soundex($unica->NomeCompleto) ||
					($q->primeiro_nome == $PrimeiroNome &&
						$q->ultimo_nome == $SobreNome);
				})	
				->first();
				if(!empty($buscaEtapa)){
				//possivel par 97
					return ['idUploadSIH'=>$buscaEtapa->id,'Score' => 87];
				}
			}
			$buscaNascimento = $this->buscaSIH
			->where('DT_NASC', $unica->DataNascimento);
			if($buscaNascimento->count() > 0){
				$buscaEtapa = $buscaNascimento
				->filter(function($q) use ($unica, $PrimeiroNome, $SobreNome) {
					return $q->NOME == $unica->NomeCompleto ||
					$q->NOMEBUSCA == $unica->NomeBusca ||
					($q->primeiro_nome == $PrimeiroNome &&
						$q->ultimo_nome == $SobreNome);
				})
				->first();
				if(!empty($buscaEtapa)){
				//possivel par 97
					return ['idUploadSIH'=>$buscaEtapa->id,'Score' => 96];
				}
				$buscaEtapa = $buscaNascimento
				->where('CNS', $unica->NUMSUS)
				->where('CNS', '!=', null)
				->where('CNS', '!=', '')
				->first();
				if(!empty($buscaEtapa)){
				//possivel par 97
					return ['idUploadSIH'=>$buscaEtapa->id,'Score' => 90];
				}
				$buscaEtapa = $buscaNascimento
				->where('NOME_MAE', $unica->NomeMae)	
				->where('NOME_MAE', '!=', null)	
				->where('NOME_MAE', '!=', '')	
				->first();
				if(!empty($buscaEtapa)){
				//possivel par 97
					return ['idUploadSIH'=>$buscaEtapa->id,'Score' => 93];
				}
				$buscaEtapa = $buscaNascimento
				->filter(function($q) use ($unica, $PrimeiroNome, $SobreNome) {
					return $q->NOME == $unica->NomeCompleto ||
					$q->NOMEBUSCA == $unica->NomeBusca ||
					soundex($q->NomeCompleto) == soundex($unica->NomeCompleto) ||
					($q->primeiro_nome == $PrimeiroNome &&
						$q->ultimo_nome == $SobreNome);
				})	
				->first();
				if(!empty($buscaEtapa)){
				//possivel par 97
					return ['idUploadSIH'=>$buscaEtapa->id,'Score' => 87];
				}
			}

			$buscaNomeBusca = $this->buscaSIH
			->where('NOMEBUSCA', $unica->NomeBusca);
			if($buscaNomeBusca->count() > 0){

				$buscaEtapa = $buscaNomeBusca
				->where('CNS', $unica->NUMSUS)
				->where('CNS', '!=', null)
				->where('CNS', '!=', '')
				->first();
				if(!empty($buscaEtapa)){
				//possivel par 97
					return ['idUploadSIH'=>$buscaEtapa->id,'Score' => 90];
				}

				$buscaEtapa = $buscaNomeBusca
				->where('NOME_MAE', $unica->NomeMae)	
				->where('NOME_MAE', '!=', null)	
				->where('NOME_MAE', '!=', '')	
				->first();
				if(!empty($buscaEtapa)){
				//possivel par 97
					return ['idUploadSIH'=>$buscaEtapa->id,'Score' => 93];
				}
				$buscaEtapa = $buscaNomeBusca
				->filter(function($q) use ($unica, $PrimeiroNome, $SobreNome) {
					return $q->NOME == $unica->NomeCompleto ||
					$q->NOMEBUSCA == $unica->NomeBusca ||
					soundex($q->NomeCompleto) == soundex($unica->NomeCompleto) ||
					($q->primeiro_nome == $PrimeiroNome &&
						$q->ultimo_nome == $SobreNome);
				})	
				->first();
				if(!empty($buscaEtapa)){
				//possivel par 97
					return ['idUploadSIH'=>$buscaEtapa->id,'Score' => 87];
				}
			}
			$buscaNomeMAE = $this->buscaSIH
			->where('NOME_MAE', $unica->NomeMae)
			->where('NOME_MAE', '!=', null)
			->where('NOME_MAE', '!=', '')
			->filter(function($q) {
				return $q->NOME_MAE != '' || $q->NOME_MAE != null;
			});
			if($buscaNomeMAE->count() > 0){

				$buscaEtapa = $buscaNomeMAE
				->where('CNS', $unica->NUMSUS)
				->where('CNS', '!=', null)
				->where('CNS', '!=', '')
				->first();
				if(!empty($buscaEtapa)){
				//possivel par 97
					return ['idUploadSIH'=>$buscaEtapa->id,'Score' => 90];
				}

				$buscaEtapa = $buscaNomeMAE
				->filter(function($q) use ($unica, $PrimeiroNome, $SobreNome) {
					return $q->NOME == $unica->NomeCompleto ||
					$q->NOMEBUSCA == $unica->NomeBusca ||
					soundex($q->NomeCompleto) == soundex($unica->NomeCompleto) ||
					($q->primeiro_nome == $PrimeiroNome &&
						$q->ultimo_nome == $SobreNome);
				})		
				->first();
				if(!empty($buscaEtapa)){
				//possivel par 97
					return ['idUploadSIH'=>$buscaEtapa->id,'Score' => 87];
				}
			}



		}


	}
	/**
	 * The job failed to process.
	 *
	 * @param  Exception  $exception
	 * @return void
	 */
	public function failed(Exception $exception)
	{
		//dd($exception);
		$this->processo->Status = 3;
		$this->processo->Log = "Erro ao processar SIH";
		$this->processo->save();
		\Log::alert('Erro ao importar geral SIH : '.$exception->getMessage());
		DB::statement('SET FOREIGN_KEY_CHECKS=0;');
		$deletaSih = DB::table('upload_sih')
		->where('Ano',$this->dataImport['Ano'])
		->where('Trimestre', $this->dataImport['Trimestre'])
		->where('CodCidade',$this->dataImport['Trimestre'])->delete();

		$deletaLinkagem = DB::table('linkagem_sih')
		->where('Ano',$this->dataImport['Ano'])
		->where('Trimestre', $this->dataImport['Trimestre'])
		->where('CodCidade',$this->dataImport['Trimestre'])->delete();
		DB::statement('SET FOREIGN_KEY_CHECKS=1;');
		// OneSignal::sendNotificationUsingTags(
		// 	"O processo do SIH".$this->processo->id." terminou com erro",
		// 	array(
		// 		["field" => "tag",
		// 		"key" => "user_id",
		// 		"relation" => "=", 
		// 		"value" => $this->user->id]
		// 	),
		// 	$url = null,
		// 	$data = null,
		// 	$buttons = null,
		// 	$schedule = null
		// );
	}

}
