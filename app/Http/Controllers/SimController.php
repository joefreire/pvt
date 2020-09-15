<?php

namespace App\Http\Controllers;
use App\Models\ListaUnicaPendencias;
use App\Models\QuadroMultiplo;
use App\Models\Cidades;
use App\Models\User;
use App\Models\Sim;
use App\Models\Processo;
use App\Models\Vitimas;
use App\Models\LinkagemSim;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Storage;
use Session;
use Auth;
use DB;
use Validator;
Use OneSignal;
Use DataTables;
use App\Jobs\ProcessaSim;
use XBase\Table;

class SimController extends Controller
{
	public $unica;
	public $buscaSIM;
	public function __construct()
	{
		//$this->middleware('auth');
	}

	public function index()
	{
		Session::put('CodCidade', Auth::user()->CodCidade);
		Session::put('user_id', Auth::id());
		if(!empty(Auth::user()->cidade)){
			Session::put('CidadeAcidente', Auth::user()->cidade->municipio);
			Session::put('EstadoAcidente', Auth::user()->cidade->uf);
		}
		return view('sim');
	}
	public function relatorio()
	{
		Session::put('CodCidade', Auth::user()->CodCidade);
		Session::put('user_id', Auth::id());
		if(!empty(Auth::user()->cidade)){
			Session::put('CidadeAcidente', Auth::user()->cidade->municipio);
			Session::put('EstadoAcidente', Auth::user()->cidade->uf);
		}
		return view('relatorioSim');
	}
	public function relatorioPares()
	{
		Session::put('CodCidade', Auth::user()->CodCidade);
		Session::put('user_id', Auth::id());
		if(!empty(Auth::user()->cidade)){
			Session::put('CidadeAcidente', Auth::user()->cidade->municipio);
			Session::put('EstadoAcidente', Auth::user()->cidade->uf);
		}
		return view('relatorioParesSim');
	}
	public function dataParesSim(Request $request)
	{
		//validação permissão
		if(Auth::user()->tipo == 1){
			if(empty($request->CodCidade)){
				return [];
			}
			$CodCidade = $request->CodCidade;
		}elseif(Auth::user()->tipo == 2){
			if(empty($request->CodCidade)){
				return [];
			}
			$cidade = Cidades::find($request->CodCidade);
			if($cidade->uf != Auth::user()->cidade->uf){
				return response()->json(['error'=> 'sem permissão']);
			}	
			$CodCidade = $cidade->codigo;	
		}else{
			$CodCidade = Auth::user()->CodCidade;	
		}
		if(empty($CodCidade) || empty($request->Trimestre) || empty($request->Ano)){
			return [];
		}
		$data = LinkagemSim::with('sim','quadro_multiplo','vitima')
		->where('linkagem_sim.Ano', $request->Ano)
		->where('linkagem_sim.CodCidade', $CodCidade)
		->where('linkagem_sim.Trimestre', $request->Trimestre);

		return Datatables::eloquent($data)
		->addColumn('acoes', function ($lista) {			
			return $lista->id;
		})
		->addColumn('SexoSIM', function ($lista) {
			return $lista->sim->sexo;
		})
		->addColumn('dados_vitima', function ($lista) {
			return $lista->vitima->filterFields();
		})
		->addColumn('dados_sim', function ($lista) {
			return $lista->sim->filterFields();
		})
		->addColumn('dados_acidente', function ($lista) {
			return $lista->quadro_multiplo->filterFields();
		})
		->addColumn('acidente_transito', function ($lista) {
			if((!empty($lista->CAUSABAS) && $lista->CAUSABAS[0] == 'V') || (!empty($lista->CAUSABAS_O) && $lista->CAUSABAS_O[0] == 'V')){
				return 'Sim';
			}else{
				return 'Não';
			}
		})
		->toJson();

	}
	public function dataSim(Request $request)
	{
		//validação permissão
		if(Auth::user()->tipo == 1){
			if(empty($request->CodCidade)){
				return [];
			}
			$CodCidade = $request->CodCidade;
		}elseif(Auth::user()->tipo == 2){
			if(empty($request->CodCidade)){
				return [];
			}
			$cidade = Cidades::find($request->CodCidade);
			if($cidade->uf != Auth::user()->cidade->uf){
				return response()->json(['error'=> 'sem permissão']);
			}	
			$CodCidade = $cidade->codigo;	
		}else{
			$CodCidade = Auth::user()->CodCidade;	
		}
		if(empty($CodCidade) || empty($request->Trimestre) || empty($request->Ano)){
			return [];
		}
		$data = Sim::with('Linkagem')
		->when($request->Filtro != '', function ($q) use ($request){
			if($request->Filtro == 'ApenasLinkadosSIH'){
				return $q->whereHas('Linkagem');
			}
			if($request->Filtro == 'ApenasVerdadeiros'){
				return $q->whereHas('Linkagem', function ($queryEmp) {
					$queryEmp->where('deleted_at', null)->where('ParVerdadeiro', 1);
				});
			}
			if($request->Filtro == 'ApenasTransito'){
				return $q->whereRAW('(LEFT( "CAUSABAS", 1) = ? OR LEFT( "CAUSABAS_O", 1) = ?)',array('V','V'));
			}
		})
		->where('upload_sim.Ano', $request->Ano)
		->where('upload_sim.CodCidade', $CodCidade)
		->where('upload_sim.Trimestre', $request->Trimestre);


		return Datatables::eloquent($data)
		->addColumn('acoes', function ($lista) {			
			return $lista->id;
		})
		->addColumn('linkado', function ($lista) {
			if($lista->Linkagem->count() > 0){
				return 'Linkado';
			}else{
				return 'Não Linkado';
			}
		})
		->addColumn('ParVerdadeiro', function ($lista) {
			if($lista->Linkagem->count() > 0){
				$likagens = $lista->Linkagem->where('ParVerdadeiro','!=',null);
				$verdadeiro = $lista->Linkagem->where('ParVerdadeiro',1)->count();
				$falso = $lista->Linkagem->where('ParVerdadeiro',0)->count();
				if($verdadeiro > 0){
					return 'Sim';
				}elseif($falso > 0){
					return 'Não';
				}else{
					return 'Não Verificado';
				}
			}else{
				return 'Não Linkado';
			}
		})
		->addColumn('acidente_transito', function ($lista) {
			if((!empty($lista->CAUSABAS) && $lista->CAUSABAS[0] == 'V') || (!empty($lista->CAUSABAS_O) && $lista->CAUSABAS_O[0] == 'V')){
				return 'Sim';
			}else{
				return 'Não';
			}
		})
		->toJson();

	}
	public function pares()
	{
		Session::put('CodCidade', Auth::user()->CodCidade);
		Session::put('user_id', Auth::id());
		if(!empty(Auth::user()->cidade)){
			Session::put('CidadeAcidente', Auth::user()->cidade->municipio);
			Session::put('EstadoAcidente', Auth::user()->cidade->uf);
		}
		return view('paresSim');
	}
	public function dataLinkagem(Request $request)
	{
		//validação permissão
		if(Auth::user()->tipo == 1){
			if(empty($request->CodCidade)){
				return [];
			}
			$CodCidade = $request->CodCidade;
		}elseif(Auth::user()->tipo == 2){
			if(empty($request->CodCidade)){
				return [];
			}
			$cidade = Cidades::find($request->CodCidade);
			if($cidade->uf != Auth::user()->cidade->uf){
				return response()->json(['error'=> 'sem permissão']);
			}	
			$CodCidade = $cidade->codigo;	
		}else{
			$CodCidade = Auth::user()->CodCidade;	
		}
		if(empty($CodCidade) || empty($request->Trimestre) || empty($request->Ano)){
			return [];
		}
		$data = LinkagemSim::with('vitima', 'quadro_multiplo', 'sim')
		->where('linkagem_sim.Ano', $request->Ano)
		->where('linkagem_sim.Trimestre', $request->Trimestre)
		->where('linkagem_sim.CodCidade', $CodCidade)
		->whereNull('ParVerdadeiro');
		//dd($data->take(1)->skip(23//)->get());
		
		// if(Auth::user()->tipo < 3){
		// 	$data->where('CodCidade', $request->CodCidade);
		// }else{
		// 	$data->where('CodCidade', Session::get('CodCidade'));
		// }


		return Datatables::eloquent($data)
		->addColumn('acoes', function ($lista) {			
			return $lista->id;
		})
		->addColumn('SexoSIM', function ($lista) {
			return $lista->sim->sexo;
		})
		->addColumn('dados_vitima', function ($lista) {
			return $lista->vitima->filterFields();
		})
		->addColumn('dados_sim', function ($lista) {
			return $lista->sim->filterFields();
		})
		->addColumn('dados_acidente', function ($lista) {
			return $lista->quadro_multiplo->filterFields();
		})
		->toJson();

	}
	public function deleteDados(Request $request)
	{
		//validação permissão
		if(Auth::user()->tipo == 1){
			$CodCidade = $request->CodCidade;
			if(empty($request->CodCidade)){
				return [];
			}
			$cidade = Cidades::find($request->CodCidade);
			if(empty($cidade)){
				return redirect()->back()->with('error','campos inválidos, você precisa informar a cidade');
			}
			$CodCidade = $cidade->codigo;	
			$CidadeAcidente = $cidade->municipio;	
			$EstadoAcidente = $cidade->uf;	
		}elseif(Auth::user()->tipo == 2){
			if(empty($request->CodCidade)){
				return [];
			}
			$cidade = Cidades::find($request->CodCidade);
			if(empty($cidade)){
				return redirect()->back()->with('error','campos inválidos, você precisa informar a cidade');
			}
			if($cidade->uf != Auth::user()->cidade->uf){
				return response()->json(['error'=> 'sem permissão'],405);
			}	
			$CodCidade = $cidade->codigo;	
			$CidadeAcidente = $cidade->municipio;	
			$EstadoAcidente = $cidade->uf;	
		}else{
			$CodCidade = Auth::user()->CodCidade;	
		}

		$linkagmesim = LinkagemSim::where('Ano', $request->Ano)
		->where('Trimestre', $request->Trimestre)
		->where('CodCidade', $CodCidade)->forceDelete();

		$sim = Sim::where('Ano', $request->Ano)
		->where('Trimestre', $request->Trimestre)
		->where('CodCidade', $CodCidade)->forceDelete();

		return response()->json('Dados deletados');


	}
	public function gravaSim(Request $request)
	{
		if(Auth::user()->tipo == 1){
			if(isset($request->CodCidade)){
				$CodCidade = $request->CodCidade;
			}else{
				$CodCidade = Session::get('CodCidade');
			}
			
		}elseif(Auth::user()->tipo == 2){
			if(empty($request->CodCidade)){
				return [];
			}
			if(isset($request->CodCidade)){
				$cidade = Cidades::find($request->CodCidade);
				if($cidade->uf != Auth::user()->cidade->uf){
					return response()->json(['error'=> 'sem permissão']);
				}	
				$CodCidade = $cidade->codigo;	
			}else{
				$CodCidade = Auth::user()->CodCidade;
			}		
		}else{
			$CodCidade = Auth::user()->CodCidade;	
		}
		$request->validate([
			'arquivo' => 'required|file|max:500000|mimes:dbf,zip',
			'Ano' => 'required',
			'Trimestre' => 'required',
		]);
		$ext = $request->arquivo->getClientOriginalExtension();
		$uploadfile = $request->arquivo;
		$file = Auth::id().'-'.Auth::user()->CodCidade.'-'.$request->Ano.'-'.$request->Trimestre;
		$path = Storage::disk('sim')->getAdapter()->getPathPrefix();
		if ($ext === 'zip' || $ext === 'ZIP'){
			$zip = new \ZipArchive();
			if( $zip->open( $uploadfile )  === true){ 
				if(pathinfo($zip->getNameIndex(0), PATHINFO_EXTENSION) ==='DBF' || pathinfo($zip->getNameIndex(0), PATHINFO_EXTENSION) ==='dbf'){
					$zip->renameName($zip->getNameIndex(0),$file.'.dbf');
					$zip->extractTo($path,$file.'.dbf');
					$zip->close();
					$path = Storage::disk('sim')->path($file.'.dbf');
				}else{
					return redirect()->back()->with('error', 'Arquivo ZIP não contém DBF');
				}
			}else{
				return redirect()->back()->with('error', 'Não foi possivel abrir o arquivo ZIP');
			}
		}else{
			//DBF
			$path = $request->file('arquivo')->storeAs(
				'public',  'sim/'.$file.'.'.$ext
			);
			$path = Storage::disk('sim')->getAdapter()->getPathPrefix().$file.'.'.$ext;
		}
		$table = new Table($path);
		$rows = array_keys($table->columns);
		if(!in_array('numerodo', $rows) ){
			return redirect()->back()->with('error','ARQUIVO SIM SEM NUMERODO');
		}
		if(!(in_array('nome', $rows) ) ){
			return redirect()->back()->with('error','ARQUIVO SIM SEM NOME');
		}
		if(!(in_array('dtobito', $rows) ) ){
			return redirect()->back()->with('error','ARQUIVO SIM SEM dtobito');
		}
		if(!(in_array('dtnasc', $rows) )) {
			return redirect()->back()->with('error','ARQUIVO SIM SEM dtnasc');
		}

		$user        = Auth::user();
		$dataImport['Ano'] = $request->Ano;
		$dataImport['Trimestre'] = $request->Trimestre;
		$dataImport['CodCidade'] = $CodCidade;
		$dataImport['user_id'] = $user->id;
		//$qtdRegistros = $table->recordCount;

		$processo = Processo::create([
			'user_id'   => Auth::id(),
			'Ano'   => $dataImport['Ano'],
			'Trimestre'   => $dataImport['Trimestre'],
			'CodCidade'   => $dataImport['CodCidade'],
			'Log'   => 'Na fila para processamento',
			'Processo'   => 'Sim',
			'Status'   => 0,
		]);

		$processa = ProcessaSim::dispatch($user, $dataImport, $processo, $path);

		return redirect()->route('sim')->with('success','Sim enviado, por favor aguarde enquanto ela está sendo processada');
	}



	public function teste(){
		$user        = Auth::user();
		$dataImport['Ano'] = 2015;
		$dataImport['Trimestre'] = 1;
		$dataImport['CodCidade'] = 270430;
		$dataImport['user_id'] = $user->id;
		$path = '';
		$processo = Processo::create([
			'user_id'   => Auth::id(),
			'Ano'   => $dataImport['Ano'],
			'Trimestre'   => $dataImport['Trimestre'],
			'CodCidade'   => $dataImport['CodCidade'],
			'Log'   => 'Na fila para processamento',
			'Processo'   => 'Sim2',
			'Status'   => 0,
		]);
		$processa = ProcessaSim::dispatch($user, $dataImport, $processo, $path);
	}
	public function checkSim(Request $request)
	{
		if(Auth::user()->tipo == 1){
			if(isset($request->CodCidade)){
				$CodCidade = $request->CodCidade;
			}else{
				$CodCidade = Session::get('CodCidade');
			}
			
		}elseif(Auth::user()->tipo == 2){
			if(isset($request->CodCidade)){
				$cidade = Cidades::find($request->CodCidade);
				if($cidade->uf != Auth::user()->cidade->uf){
					return response()->json(['error'=> 'sem permissão']);
				}	
				$CodCidade = $cidade->codigo;	
			}else{
				$CodCidade = Auth::user()->CodCidade;
			}		
		}else{
			$CodCidade = Auth::user()->CodCidade;	
		}
		$lista = Vitimas::where('Ano', $request->Ano)
		->where('Trimestre', $request->Trimestre)
		->where('CodCidade', $CodCidade)->count();

		$processo = Processo::where('Ano', $request->Ano)
		->where('Trimestre', $request->Trimestre)
		->where('Status', 0)
		->where('Processo', 'Sim')
		->where('CodCidade', $CodCidade)->count();

		$pendecias = ListaUnicaPendencias::where('Ano', $request->Ano)
		->where('Trimestre', $request->Trimestre)
		->where('CodCidade', $CodCidade)->count();

		$sim = Sim::where('Ano', $request->Ano)
		->where('Trimestre', $request->Trimestre)
		->where('CodCidade', $CodCidade)->count();

		$linkagem = LinkagemSim::where('Ano', $request->Ano)
		->where('Trimestre', $request->Trimestre)
		->where('CodCidade', $CodCidade)->count();

		return response()->json(
			[
				'sim'=> $sim,
				'pendencias' => $pendecias,
				'processo' => $processo,
				'lista' => $lista, 
				'linkagem' => $linkagem, 
			]);
	}


	public function salvaPares(Request $request)
	{
		$request->validate([
			'tipo' => 'required',
			'ids_pares' => 'required',
		]);
		$count = 0;
		foreach ($request->ids_pares as $value) {
			$linkagem = LinkagemSim::find($value);
			if(!empty($linkagem)){
				$count++;
				if($request->tipo == 1){
					$linkagem->ParVerdadeiro = (bool)$request->tipo;
					//$QM = QuadroMultiplo::find($linkagem->idQuadroMultiplo);
					$Vitima = Vitimas::findOrFail($linkagem->idListaUnica);
					$Sim = Sim::findOrFail($linkagem->idUploadSIM);

					if(!($Vitima->Ano == $Sim->Ano && 
						$Vitima->CodCidade == $Sim->CodCidade && 
						$Vitima->Trimestre == $Sim->Trimestre)){
						return redirect(route('sim.pares'))->with('error','Par não verificado '.$linkagem->id);
					} 

					if(!($Vitima->Sexo == 'FEMININO' || $Vitima->Sexo == 'MASCULINO')
						|| empty($Vitima->Sexo))
					{
						$Vitima->Sexo = $Sim->SEXO;
					}
					if($Vitima->NomeMae == '' || empty($Vitima->NomeMae)){
						$Vitima->NomeMae = $Sim->NOMEMAE;
					}
					if($Vitima->NUMSUS == '' || empty($Vitima->NUMSUS)){
						$Vitima->NUMSUS = $Sim->NUMSUS;
					}
					if($Vitima->DataNascimento == '99/99/9999' || empty($Vitima->DataNascimento)){
						$Vitima->DataNascimento = $Sim->DTNASC;
						$idade = calculaIdade($Vitima->DataNascimento, $Vitima->DataAcidente);
						$FaixaEtaria = calculaFaixaEtaria($idade);
						$Vitima->Idade = $idade;
						$Vitima->FaixaEtaria = $FaixaEtaria;
					}
					if($Vitima->EnderecoVitima == '' || empty($Vitima->EnderecoVitima)){
						$Vitima->EnderecoVitima = $Sim->ENDRES;
					}
					if($Vitima->BairroVitima == '' || empty($Vitima->BairroVitima)){
						$Vitima->BairroVitima = $Sim->BAIRES;
					}
					if($Vitima->NumeroVitima == '' || empty($Vitima->NumeroVitima)){
						$Vitima->NumeroVitima = $Sim->NUMRES;
					}
					if($Vitima->CEPVitima == '' || empty($Vitima->CEPVitima)){
						$Vitima->CEPVitima = $Sim->CEPRES;
					}
					if($Vitima->CBO == '' || empty($Vitima->CBO)){
						$Vitima->CBO = $Sim->OCUP;
					}
					if($Vitima->MunicipioVitima == '' || empty($Vitima->MunicipioVitima)){
						if(!empty($Sim->CODMUNRES)){
							$cidade = Cidades::where('codigo', $Sim->CODMUNRES)->first();
							if(!empty($cidade)){
								$Vitima->MunicipioVitima = $cidade->municipio;
								$Vitima->EstadoVitima = $cidade->uf;
							}
						}
					}
					if(!($Vitima->GravidadeLesao == 'FATAL' 
						|| $Vitima->GravidadeLesao == 'FATAL LOCAL'
						|| $Vitima->GravidadeLesao == 'FATAL POSTERIOR')
						|| empty($Vitima->GravidadeLesao)){
						$Vitima->GravidadeLesao = 'FATAL';
				}
				$Vitima->save();
			}else{

				$linkagem->ParVerdadeiro = (bool)$request->tipo;
				$linkagem->TipoFalso = $request->TipoFalso;
			}
			$linkagem->save();

		}
	}
	if($count>0){
		return redirect(route('sim.pares').'?Ano='.$linkagem->Ano.'&Trimestre='.$linkagem->Trimestre)->with('success',$count.' pares foram verificados');
	}else{
		return redirect(route('sim.pares'))->with('error','Sem pares verificados');
	}

}

}
