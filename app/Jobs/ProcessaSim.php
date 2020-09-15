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
use App\Models\Sim;
use Exception;
use App\Models\Processo;
use App\Models\Vitimas;
use App\Models\LinkagemSim;
use OneSignal;
use DB;
use XBase\Table;

class ProcessaSim implements ShouldQueue
{
	use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

	public $user;
	public $dataImport;
	public $file;
	public $processo;
	public $unica;
	public $buscaSIM;
	public $timeout = 0;
	/**
	 * Create a new job instance.
	 *
	 * @return void
	 */
	public function __construct(User $user, $dataImport, Processo $processo, $file)
	{
		
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
		$this->processo->Log = "Arquivo SIM em processamento";
		$this->processo->save();
		
		try {
			$table = new Table($this->file);
			$rows = array_keys($table->columns);
			$qtdRegistros = $table->recordCount;
			$dataImport = $this->dataImport;
			$this->processo->Status = 0;
			$this->processo->Log = "Em processamento";
			$this->processo->save();
			while ($record = $table->nextRecord()) {
				$sim = Sim::create([
					'Ano'                   => $this->dataImport['Ano'],
					'Trimestre'             => $this->dataImport['Trimestre'],
					'CodCidade'             => $this->dataImport['CodCidade'],
					'user_id'               => $this->dataImport['user_id'],

					'NOMEBUSCA'             => \BuscaBR::encode($record->nome),
					'TIPOBITO'              => in_array('tipobito',$rows) ? $record->tipobito : null,
					'HORAOBITO'             => in_array('horaobito',$rows) ? $record->horaobito : null,
					'NATURAL'               => in_array('natural',$rows) ? $record->natural : null,
					'CODMUNCART'            => in_array('codmuncart',$rows) ? $record->codmuncart : null,
					'NUMERODO'              => $record->numerodo,
					'NUMERODV'              => in_array('numerodv',$rows) ? $record->numerodv : null,

					'DTOBITO'               => converteData($record->dtobito),
					'DTNASC'                => converteData($record->dtnasc),

					'NOME'                  => validaNomeCompleto($record->nome),
					'NOMEPAI'               => in_array('nomepai',$rows) ? validaNomeCompleto($record->nomepai) : null,
					'NOMEMAE'               => in_array('nomemae',$rows) ? validaNomeCompleto($record->nomemae) : null,

					'IDADE'                 => in_array('idade',$rows) ? $record->idade : null,
					'SEXO'                  => in_array('sexo',$rows) ? $record->sexo : null,
					'RACACOR'               => in_array('racacor',$rows) ? $record->racacor : null,
					'LOCOCOR'               => in_array('lococor',$rows) ? $record->lococor : null,
					'CODMUNOCOR'            => in_array('codmunocor',$rows) ? $record->codmunocor : null,
					'BAIOCOR'               => in_array('baiocor',$rows) ? $record->baiocor : null,
					'ENDOCOR'               => in_array('endocor',$rows) ? $record->endocor : null,
					'NUMENDOCOR'            => in_array('numendocor',$rows) ? $record->numendocor : null,
					'COMPLOCOR'             => in_array('complocor',$rows) ? $record->complocor : null,
					'CEPOCOR'               => in_array('cepocor',$rows) ? $record->cepocor : null,
					'CAUSABAS'              => in_array('causabas',$rows) ? $record->causabas : null,
					'CAUSABAS_O'            => in_array('causabas_o',$rows) ? $record->causabas_o : null,
					'ENDRES'                => in_array('endres',$rows) ? $record->endres : null,
					'BAIRES'                => in_array('baires',$rows) ? $record->baires : null,
					'CODMUNRES'             => in_array('codmunres',$rows) ? $record->codmunres : null,
					'NUMRES'                => in_array('numres',$rows) ? $record->numres : null,
					'COMPLRES'              => in_array('complres',$rows) ? $record->complres : null,
					'CEPRES'                => in_array('cepres',$rows) ? $record->cepres : null,
					'NUMSUS'                => in_array('numsus',$rows) ? $record->numsus : null,

					'LINHAA'                => in_array('linhaa',$rows) ? $record->linhaa : null,
					'LINHAB'                => in_array('linhab',$rows) ? $record->linhab : null,
					'LINHAC'                => in_array('linhac',$rows) ? $record->linhac : null,
					'LINHAD'                => in_array('linhad',$rows) ? $record->linhad : null,
					'LINHAII'               => in_array('linhaii',$rows) ? $record->linhaii : null,

					'LINHAA_O'              => in_array('linhaa_o',$rows) ? $record->linhaa_o : null,
					'LINHAB_O'              => in_array('linhab_o',$rows) ? $record->linhab_o : null,
					'LINHAC_O'              => in_array('linhac_o',$rows) ? $record->linhac_o : null,
					'LINHAD_O'              => in_array('linhad_o',$rows) ? $record->linhad_o : null,
					'LINHAII_O'             => in_array('linhaii_o',$rows) ? $record->linhaii_o : null,
					'DTCADASTRO'            => in_array('dtcadastro',$rows) ? $record->dtcadastro : null,
					'DTREGCART'             => in_array('dtregcart',$rows) ? $record->dtregcart : null,
					'CODCART'               => in_array('codcart',$rows) ? $record->codcart : null,
					'NUMREGCART'            => in_array('numregcart',$rows) ? $record->numregcart : null,           
					'CODESTCART'            => in_array('codestcart',$rows) ? $record->codestcart : null,
					'CODMUNNATU'            => in_array('codmunnatu',$rows) ? $record->codmunnatu : null,
					'ESTCIV'                => in_array('estciv',$rows) ? $record->estciv : null,
					'OCUP'                  => in_array('ocup',$rows) ? $record->ocup : null,
					'ASSISTMED'             => in_array('assistmed',$rows) ? $record->assistmed : null,
					'CIRURGIA'              => in_array('cirurgia',$rows) ? $record->cirurgia : null,
					'NECROPSIA'             => in_array('necropsia',$rows) ? $record->necropsia : null,
					'EXAME'                 => in_array('exame',$rows) ? $record->exame : null,
					'FONTE'                 => in_array('fonte',$rows) ? $record->fonte : null,
					'CONTATO'               => in_array('contato',$rows) ? $record->contato : null,
					'DTATESTADO'            => in_array('dtatestado',$rows) ? $record->dtatestado : null,
					'ATESTANTE'             => in_array('atestante',$rows) ? $record->atestante : null,
					'NUMERODN'              => in_array('numerodn',$rows) ? $record->numerodn : null,
					'DSTEMPO'               => in_array('dstempo',$rows) ? $record->dstempo : null,
					'DSEXPLICA'             => in_array('dsexplica',$rows) ? $record->dsexplica : null,
					'MEDICO'                => in_array('medico',$rows) ? $record->medico : null,
					'CRM'                   => in_array('crm',$rows) ? $record->crm : null,
					'CIRCOBITO'             => in_array('circobito',$rows) ? $record->circobito : null,
					'ACIDTRAB'              => in_array('acidtrab',$rows) ? $record->acidtrab : null,
					'DSEVENTO'              => in_array('dsevento',$rows) ? $record->dsevento : null,
					'ENDACID'               => in_array('endacid',$rows) ? $record->endacid : null,
					'NUMEROLOTE'            => in_array('numerolote',$rows) ? $record->numerolote: null,
					'TPPOS'                 => in_array('tppos',$rows) ? $record->tppos : null,
					'DTINVESTIG'            => in_array('dtinvestig',$rows) ? $record->dtinvestig : null,
					'CRITICA'               => in_array('critica',$rows) ? $record->critica : null,
					'ESC'                   => in_array('esc',$rows) ? $record->esc : null,
				]);
}

//pega todos os sim
$this->buscaSIM = Sim::where('Ano',$this->dataImport['Ano'])
->where('CodCidade',$this->dataImport['CodCidade'])
->where('Trimestre',$this->dataImport['Trimestre'])->get();


if ($this->buscaSIM->count() > 0) {
	$this->processo->Status = 0;
	$this->processo->Log = "Arquivo SIM em Linkagem";
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
			$this->processo->Log = "Arquivo SIM em Linkagem - 25%";
			$this->processo->save();
		}
		if((($vitimasPercorridas/$totalVitimas)*100) > 50 && (($vitimasPercorridas/$totalVitimas)*100) < 51){
			$this->processo->Status = 0;
			$this->processo->Log = "Arquivo SIM em Linkagem - 50%";
			$this->processo->save();
		}
		if((($vitimasPercorridas/$totalVitimas)*100) > 75 && (($vitimasPercorridas/$totalVitimas)*100) < 76){
			$this->processo->Status = 0;
			$this->processo->Log = "Arquivo SIM em Linkagem - 75%";
			$this->processo->save();
		}


		$this->unica = $unica;

		$linkagem = $this->linkagem();
		if(!empty($linkagem)){
			$linkagem_sim = LinkagemSim::create([
				'Score' => $linkagem['Score'],
				'idListaUnica' => $unica->id,
				'idUploadSIM' => $linkagem['idUploadSIM'],
				'idQuadroMultiplo' => $unica->idQuadroMultiplo,
				'user_id'   => $this->dataImport['user_id'],
				'Ano'   => $this->dataImport['Ano'],
				'Trimestre'   => $this->dataImport['Trimestre'],
				'CodCidade'   => $this->dataImport['CodCidade'],
			]);
							//dump($linkagem_sim);
			$pares++;							
		}
		$vitimasPercorridas++;
	}
}
$this->processo->Status = 1;
$this->processo->Log = "Sucesso, SIM:".$qtdRegistros." Pares:".$pares;
$this->processo->save();
\Log::alert('Sucesso ao importar SIM ');

} catch (\Exception $e) {
			//dd($e);
	\Log::alert('Erro ao importar SIM : '.$e->getMessage().' '. $e->getLine());
	DB::table('upload_sim')
	->where('Ano',$this->dataImport['Ano'])
	->where('CodCidade',$this->dataImport['CodCidade'])
	->where('Trimestre',$this->dataImport['Trimestre'])
	->delete();
	DB::table('linkagem_sim')
	->where('Ano',$this->dataImport['Ano'])
	->where('CodCidade',$this->dataImport['CodCidade'])
	->where('Trimestre',$this->dataImport['Trimestre'])
	->delete();
	$this->processo->Status = 3;
	$this->processo->Log = "Erro ao processar SIM 2 ".$e->getMessage();
	$this->processo->save();

}
OneSignal::sendNotificationUsingTags(
	"O processo do SIM ".$this->processo->id." terminou",
	array(
		["field" => "tag",
		"key" => "user_id",
		"relation" => "=", 
		"value" => $this->user->id]
	),
	$url = null,
	$data = null,
	$buttons = null,
	$schedule = null
);
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
		$buscaEtapa = $this->buscaSIM
		->where('DTOBITO', $unica->DataAcidente)
		->where('DTNASC', $unica->DataNascimento)
		->where('SEXO', $unica->Sexo)
		->first();
		if(!empty($buscaEtapa)){
			//possivel par 85
			return ['idUploadSIM'=>$buscaEtapa->id,'Score' => 80];
		}
		$buscaEtapa = $this->buscaSIM
		->where('DTOBITO', $unica->DataAcidente)
		->where('DTNASC', $unica->DataNascimento)
		->first();
		if(!empty($buscaEtapa)){
					//possivel par 
			return ['idUploadSIM'=>$buscaEtapa->id,'Score' => 79];
		}			
		$buscaEtapa = $this->buscaSIM
		->where('DTOBITO', $unica->DataAcidente)
		->first();
		if(!empty($buscaEtapa)){
					//possivel par 
			return ['idUploadSIM'=>$buscaEtapa->id,'Score' => 78];
		}		
		$buscaEtapa = $this->buscaSIM
		->where('NOMEMAE', $unica->NomeMae)
		->where('NOMEMAE','!=',null)
		->where('NOMEMAE','!=','')
		->filter(function($q) use ($unica, $PrimeiroNome, $SobreNome) {
			return $q->DTNASC == $unica->dt_nasc_contrario ||
			$q->DTNASC == $unica->DataNascimento;
		})
		->filter(function($q) use ($unica, $PrimeiroNome, $SobreNome) {
			return $q->DTOBITO == $unica->dt_acidente_contrario ||
			$q->DTOBITO == $unica->DataAcidente;
		})
		->first();
		if(!empty($buscaEtapa)){
				//possivel par 
			return ['idUploadSIM'=>$buscaEtapa->id,'Score' => 77];
		}
		$buscaEtapa = Sim::where('Ano',$this->dataImport['Ano'])
		->where('CodCidade',$this->dataImport['CodCidade'])
		->where('Trimestre',$this->dataImport['Trimestre'])
		->where(function ($q) use ($unica){
			$q->where('DTNASC', $unica->dt_nasc_contrario)
			->orWhere('DTNASC', $unica->DataNascimento);
		})->where(function ($q) use ($unica){
			$q->where('DTOBITO', $unica->dt_acidente_contrario)
			->orWhere('DTOBITO', $unica->DataAcidente);
		})
		->first();
		if(!empty($buscaEtapa)){
				//possivel par 
			return ['idUploadSIM'=>$buscaEtapa->id,'Score' => 76];
		}
		$buscaEtapa = $this->buscaSIM
		->where('NUMSUS', $unica->NUMSUS)
		->where('NUMSUS', '!=', null)
		->where('NUMSUS', '!=', '')
		->first();
		if(!empty($buscaEtapa) && !empty($unica->NUMSUS) ){
				//possivel par
			return ['idUploadSIM'=>$buscaEtapa->id,'Score' => 75];
		}
	}else{

			//Busca com nome igual
		$buscaNome = Sim::where('Ano',$this->dataImport['Ano'])
		->where('CodCidade',$this->dataImport['CodCidade'])
		->where('Trimestre',$this->dataImport['Trimestre'])
		->where('NOME', $unica->NomeCompleto)->get();

		if($buscaNome->count() > 0){
			$buscaEtapa = $buscaNome
			->where('DTOBITO', $unica->DataAcidente)
			->where('DTNASC', $unica->DataNascimento)
			->where('SEXO', $unica->Sexo)->first();
			if(!empty($buscaEtapa)){
					//possivel par 99
				return ['idUploadSIM'=>$buscaEtapa->id,'Score' => 99];
			}
			$buscaEtapa = $buscaNome
			->where('DTOBITO', $unica->DataAcidente)
			->filter(function ($q) use ($unica, $PrimeiroNome, $SobreNome) {
				return $q->SEXO == $unica->Sexo || $q->DTNASC == $unica->DataNascimento;
			})
			->first();
			if(!empty($buscaEtapa)){
						//possivel par 98
				return ['idUploadSIM'=>$buscaEtapa->id,'Score' => 98];
			}
			$buscaEtapa = $buscaNome
			->where('DTOBITO', $unica->DataAcidente)
			->first();
			if(!empty($buscaEtapa)){
				//possivel par
				return ['idUploadSIM'=>$buscaEtapa->id,'Score' => 98];
			}
			$buscaEtapa = $buscaNome
			->where('DTNASC', $unica->DTNASC)
			->first();
			if(!empty($buscaEtapa)){
				//possivel par
				return ['idUploadSIM'=>$buscaEtapa->id,'Score' => 97];
			}
			$buscaEtapa = $buscaNome
			->where('NOMEMAE', $unica->NomeMae)		
			->where('NOMEMAE', '!=', null)	
			->where('NOMEMAE', '!=', '')		
			->first();
			if(!empty($buscaEtapa)){
				//possivel par
				return ['idUploadSIM'=>$buscaEtapa->id,'Score' => 94];
			}
			$buscaEtapa = $buscaNome
			->where('NUMSUS', $unica->NUMSUS)	
			->where('NUMSUS', '!=', null)
			->where('NUMSUS', '!=', '')
			->first();
			if(!empty($buscaEtapa)){
				//possivel par
				return ['idUploadSIM'=>$buscaEtapa->id,'Score' => 92];
			}
			$buscaEtapa = $buscaNome
			->filter(function($q) use ($unica, $PrimeiroNome, $SobreNome) {
				return $q->DTNASC == $unica->dt_nasc_contrario ||
				$q->DTNASC == $unica->DataNascimento;
			})
			->filter(function($q) use ($unica, $PrimeiroNome, $SobreNome) {
				return $q->DTOBITO == $unica->dt_acidente_contrario ||
				$q->DTOBITO == $unica->DataAcidente;
			})
			->first();
			if(!empty($buscaEtapa)){
						//possivel par 92
				return ['idUploadSIM'=>$buscaEtapa->id,'Score' => 92];
			}
			$buscaEtapa = $buscaNome
			->filter(function($q) use ($unica, $PrimeiroNome, $SobreNome) {
				return $q->DTNASC == $unica->dt_nasc_contrario ||
				$q->DTNASC == $unica->DataNascimento;
			})
			->first();
			if(!empty($buscaEtapa)){
						//possivel par 92
				return ['idUploadSIM'=>$buscaEtapa->id,'Score' => 92];
			}
			$buscaEtapa = $buscaNome
			->filter(function($q) use ($unica, $PrimeiroNome, $SobreNome) {
				return $q->DTOBITO == $unica->dt_acidente_contrario ||
				$q->DTOBITO == $unica->DataAcidente;
			})
			->first();
			if(!empty($buscaEtapa)){
						//possivel par 92
				return ['idUploadSIM'=>$buscaEtapa->id,'Score' => 92];
			}
			$buscaEtapa = $buscaNome
			->filter(function($q) use ($unica) {
				if(!empty($unica->data_nascimento_formatada) && $unica->DataNascimento != '99/99/9999'){
					return $q->data_nascimento_formatada <= $unica->data_nascimento_formatada->addDays(5) ||
					$q->data_nascimento_formatada >= $unica->data_nascimento_formatada->subDays(5);
				}
			})
			->filter(function($q) use ($unica) {
				if(!empty($unica->data_acidente_formatada) && $unica->DataNascimento != '99/99/9999'){
					return $q->data_obito_formatada <= $unica->data_acidente_formatada->addDays(5) ||
					$q->data_obito_formatada >= $unica->data_acidente_formatada->subDays(5);
				}
			})
			->first();
			if(!empty($buscaEtapa)){
				//possivel par 90
				return ['idUploadSIM'=>$buscaEtapa->id,'Score' => 90];
			}
			$buscaEtapa = $buscaNome
			->filter(function($q) use ($unica) {
				if(!empty($unica->data_acidente_formatada) && $unica->DataNascimento != '99/99/9999'){
					return $q->data_obito_formatada <= $unica->data_acidente_formatada->addDays(5) ||
					$q->data_obito_formatada >= $unica->data_acidente_formatada->subDays(5);
				}
			})
			->first();
			if(!empty($buscaEtapa)){
				//possivel par 90
				return ['idUploadSIM'=>$buscaEtapa->id,'Score' => 90];
			}
			$buscaEtapa = $buscaNome
			->filter(function($q) use ($unica) {
				if(!empty($unica->data_nascimento_formatada) && $unica->DataNascimento != '99/99/9999'){
					return $q->data_nascimento_formatada <= $unica->data_nascimento_formatada->addDays(5) ||
					$q->data_nascimento_formatada >= $unica->data_nascimento_formatada->subDays(5);
				}
			})
			->first();
			if(!empty($buscaEtapa)){
				//possivel par 90
				return ['idUploadSIM'=>$buscaEtapa->id,'Score' => 90];
			}			
			$buscaEtapa = $buscaNome			
			->first();
			if(!empty($buscaEtapa)){
				//possivel par
				return ['idUploadSIM'=>$buscaEtapa->id,'Score' => 81];
			}

		}
		$buscaDatas =  Sim::where('Ano',$this->dataImport['Ano'])
		->where('CodCidade',$this->dataImport['CodCidade'])
		->where('Trimestre',$this->dataImport['Trimestre'])
		->where('DTOBITO', $unica->DataAcidente)
		->where('DTNASC', $unica->DataNascimento)
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
				return ['idUploadSIM'=>$buscaEtapa->id,'Score' => 97];
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
				return ['idUploadSIM'=>$buscaEtapa->id,'Score' => 96];
			}

			$buscaEtapa = $buscaDatas
			->first();
			if(!empty($buscaEtapa)){
				//possivel par
				return ['idUploadSIM'=>$buscaEtapa->id,'Score' => 94];
			}
		}

		$buscaOBITO = $this->buscaSIM
		->where('DTOBITO', $unica->DataAcidente);
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
				return ['idUploadSIM'=>$buscaEtapa->id,'Score' => 96];
			}
			$buscaEtapa = $buscaOBITO
			->where('NUMSUS', $unica->NUMSUS)
			->where('NUMSUS', '!=', null)
			->where('NUMSUS', '!=', '')
			->first();
			if(!empty($buscaEtapa)){
				//possivel par 97
				return ['idUploadSIM'=>$buscaEtapa->id,'Score' => 90];
			}
			$buscaEtapa = $buscaOBITO
			->where('NOMEMAE', $unica->NomeMae)	
			->where('NOMEMAE', '!=', null)	
			->where('NOMEMAE', '!=', '')	
			->first();
			if(!empty($buscaEtapa)){
				//possivel par 97
				return ['idUploadSIM'=>$buscaEtapa->id,'Score' => 93];
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
				return ['idUploadSIM'=>$buscaEtapa->id,'Score' => 87];
			}
		}
		$buscaNascimento = $this->buscaSIM
		->where('DTNASC', $unica->DataNascimento);
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
				return ['idUploadSIM'=>$buscaEtapa->id,'Score' => 96];
			}
			$buscaEtapa = $buscaNascimento
			->where('NUMSUS', $unica->NUMSUS)
			->where('NUMSUS', '!=', null)
			->where('NUMSUS', '!=', '')
			->first();
			if(!empty($buscaEtapa)){
				//possivel par 97
				return ['idUploadSIM'=>$buscaEtapa->id,'Score' => 90];
			}
			$buscaEtapa = $buscaNascimento
			->where('NOMEMAE', $unica->NomeMae)	
			->where('NOMEMAE', '!=', null)	
			->where('NOMEMAE', '!=', '')	
			->first();
			if(!empty($buscaEtapa)){
				//possivel par 97
				return ['idUploadSIM'=>$buscaEtapa->id,'Score' => 93];
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
				return ['idUploadSIM'=>$buscaEtapa->id,'Score' => 87];
			}
		}

		$buscaNomeBusca = $this->buscaSIM
		->where('NOMEBUSCA', $unica->NomeBusca);
		if($buscaNomeBusca->count() > 0){

			$buscaEtapa = $buscaNomeBusca
			->where('NUMSUS', $unica->NUMSUS)
			->where('NUMSUS', '!=', null)
			->where('NUMSUS', '!=', '')
			->first();
			if(!empty($buscaEtapa)){
				//possivel par 97
				return ['idUploadSIM'=>$buscaEtapa->id,'Score' => 90];
			}

			$buscaEtapa = $buscaNomeBusca
			->where('NOMEMAE', $unica->NomeMae)	
			->where('NOMEMAE', '!=', null)	
			->where('NOMEMAE', '!=', '')	
			->first();
			if(!empty($buscaEtapa)){
				//possivel par 97
				return ['idUploadSIM'=>$buscaEtapa->id,'Score' => 93];
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
				return ['idUploadSIM'=>$buscaEtapa->id,'Score' => 87];
			}
		}
		$buscaNomeMAE = $this->buscaSIM
		->where('NOMEMAE', $unica->NomeMae)
		->where('NOMEMAE', '!=', null)
		->where('NOMEMAE', '!=', '')
		->filter(function($q) {
			return $q->NOMEMAE != '' || $q->NOMEMAE != null;
		});
		if($buscaNomeMAE->count() > 0){

			$buscaEtapa = $buscaNomeMAE
			->where('NUMSUS', $unica->NUMSUS)
			->where('NUMSUS', '!=', null)
			->where('NUMSUS', '!=', '')
			->first();
			if(!empty($buscaEtapa)){
				//possivel par 97
				return ['idUploadSIM'=>$buscaEtapa->id,'Score' => 90];
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
				return ['idUploadSIM'=>$buscaEtapa->id,'Score' => 87];
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
		$this->processo->Log = "Erro ao processar SIM";
		$this->processo->save();
		\Log::alert('Erro ao importar geral SIM : '.$exception->getMessage());
		OneSignal::sendNotificationUsingTags(
			"O processo do SIM ".$this->processo->id." terminou com erro",
			array(
				["field" => "tag",
				"key" => "user_id",
				"relation" => "=", 
				"value" => $this->user->id]
			),
			$url = null,
			$data = null,
			$buttons = null,
			$schedule = null
		);
	}

}
