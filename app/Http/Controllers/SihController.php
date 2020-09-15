<?php

namespace App\Http\Controllers;
use App\Models\ListaUnicaPendencias;
use App\Models\QuadroMultiplo;
use App\Models\Cidades;
use App\Models\User;
use App\Models\Sih;
use App\Models\Processo;
use App\Models\Vitimas;
use App\Models\LinkagemSih;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Storage;
use Session;
use Auth;
use DB;
use Validator;
Use OneSignal;
Use DataTables;
use App\Jobs\ProcessaSih;
use XBase\Table;

class SihController extends Controller
{
	public $unica;
	public $buscaSih;
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
		return view('sih');
	}
	public function relatorio()
	{
		Session::put('CodCidade', Auth::user()->CodCidade);
		Session::put('user_id', Auth::id());
		if(!empty(Auth::user()->cidade)){
			Session::put('CidadeAcidente', Auth::user()->cidade->municipio);
			Session::put('EstadoAcidente', Auth::user()->cidade->uf);
		}
		return view('relatorioSih');
	}
	public function relatorioPares()
	{
		Session::put('CodCidade', Auth::user()->CodCidade);
		Session::put('user_id', Auth::id());
		if(!empty(Auth::user()->cidade)){
			Session::put('CidadeAcidente', Auth::user()->cidade->municipio);
			Session::put('EstadoAcidente', Auth::user()->cidade->uf);
		}
		return view('relatorioParesSih');
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

		$linkagmesih = LinkagemSih::where('Ano', $request->Ano)
		->where('Trimestre', $request->Trimestre)
		->where('CodCidade', $CodCidade)->forceDelete();

		$sih = Sih::where('Ano', $request->Ano)
		->where('Trimestre', $request->Trimestre)
		->where('CodCidade', $CodCidade)->forceDelete();

		return response()->json('Dados deletados');


	}
	public function dataParesSih(Request $request)
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
		$data = LinkagemSih::with('sih','quadro_multiplo','vitima')
		->where('linkagem_sih.Ano', $request->Ano)
		->where('linkagem_sih.CodCidade', $CodCidade)
		->where('linkagem_sih.Trimestre', $request->Trimestre);

		return Datatables::eloquent($data)
		->addColumn('acoes', function ($lista) {			
			return $lista->id;
		})
		->addColumn('SexoSih', function ($lista) {
			return $lista->sih->sexo;
		})
		->addColumn('dados_vitima', function ($lista) {
			return $lista->vitima->filterFields();
		})
		->addColumn('dados_sih', function ($lista) {
			return $lista->sih->filterFields();
		})
		->addColumn('dados_acidente', function ($lista) {
			return $lista->quadro_multiplo->filterFields();
		})
		->addColumn('acidente_transito', function ($lista) {
			if(!empty($lista->sih->DIAG_PRI) && $lista->sih->DIAG_PRI[0] == 'V' && substr($lista->sih->DIAG_PRI,2,1) < 9 ){
				return 'Sim';
			}else{
				return 'Não';
			}
		})
		->toJson();

	}
	public function dataSih(Request $request)
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
		$data = Sih::with('Linkagem')		
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
				return $q->whereRAW('(LEFT( "DIAG_PRI", 1) = ? OR LEFT( "DIAG_PRI", 1) = ?)',array('V','V'));
			}
		})
		->where('upload_sih.Ano', $request->Ano)
		->where('upload_sih.CodCidade', $CodCidade)
		->where('upload_sih.Trimestre', $request->Trimestre);

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
			if(!empty($lista->CAUSABAS) && $lista->CAUSABAS[0] == 'V' && substr($lista->CAUSABAS,2,1) < 9 ){
				return 'Sih';
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
		return view('paresSih');
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

		$data = LinkagemSih::with('vitima', 'quadro_multiplo', 'Sih')
		->where('linkagem_sih.Ano', $request->Ano)
		->where('linkagem_sih.Trimestre', $request->Trimestre)
		->where('linkagem_sih.CodCidade', $CodCidade)
		->whereNull('ParVerdadeiro');

		
		// if(Auth::user()->tipo < 3){
		// 	$data->where('CodCidade', $request->CodCidade);
		// }else{
		// 	$data->where('CodCidade', Session::get('CodCidade'));
		// }


		return Datatables::eloquent($data)
		->addColumn('acoes', function ($lista) {			
			return $lista->id;
		})
		->addColumn('SexoSih', function ($lista) {
			return $lista->sih->sexo;
		})
		->addColumn('dados_vitima', function ($lista) {
			return $lista->vitima->filterFields();
		})
		->addColumn('dados_sih', function ($lista) {
			return $lista->sih->filterFields();
		})
		->addColumn('dados_acidente', function ($lista) {
			return $lista->quadro_multiplo->filterFields();
		})
		->toJson();

	}
	public function gravaSih(Request $request)
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
		$request->validate([
			'arquivo' => 'required|file|max:500000|mimes:dbf,zip',
			'Ano' => 'required',
			'Trimestre' => 'required',
		]);
		$ext = $request->arquivo->getClientOriginalExtension();
		$uploadfile = $request->arquivo;
		$file = Auth::id().'-'.Auth::user()->CodCidade.'-'.$request->Ano.'-'.$request->Trimestre;
		$path = Storage::disk('sih')->getAdapter()->getPathPrefix();
		if ($ext === 'zip' || $ext === 'ZIP'){
			$zip = new \ZipArchive();
			if( $zip->open( $uploadfile )  === true){ 
				if(pathinfo($zip->getNameIndex(0), PATHINFO_EXTENSION) ==='DBF' || pathinfo($zip->getNameIndex(0), PATHINFO_EXTENSION) ==='dbf'){
					$zip->renameName($zip->getNameIndex(0),$file.'.dbf');
					$zip->extractTo($path,$file.'.dbf');
					$zip->close();
					$path = Storage::disk('sih')->path($file.'.dbf');
				}else{
					return redirect()->back()->with('error', 'Arquivo ZIP não contém DBF');
				}
			}else{
				return redirect()->back()->with('error', 'Não foi possivel abrir o arquivo ZIP');
			}
		}else{
			//DBF
			$path = $request->file('arquivo')->storeAs(
				'public',  'sih/'.$file.'.'.$ext
			);
			$path = Storage::disk('sih')->getAdapter()->getPathPrefix().$file.'.'.$ext;
		}
		$table = new Table($path);
		$rows = array_keys($table->columns);
		if(!in_array('num_aih', $rows)){
			return redirect()->back()->with('error','ARQUIVO Sih SEM NUMERODO');
		}
		if(!(in_array('nome', $rows)) ){
			return redirect()->back()->with('error','ARQUIVO Sih SEM NOME');
		}

		$user        = Auth::user();
		$dataImport['Ano'] = $request->Ano;
		$dataImport['Trimestre'] = $request->Trimestre;
		$dataImport['CodCidade'] = $CodCidade;
		$dataImport['user_id'] = $user->id;
		$qtdRegistros = $table->recordCount;

		$processo = Processo::create([
			'user_id'   => Auth::id(),
			'Ano'   => $dataImport['Ano'],
			'Trimestre'   => $dataImport['Trimestre'],
			'CodCidade'   => $dataImport['CodCidade'],
			'Log'   => 'Na fila para processamento',
			'Processo'   => 'Sih',
			'Status'   => 0,
		]);

		$processo = ProcessaSih::dispatch($user, $dataImport, $processo, $path);
		// dd($processo);


		return redirect()->route('sih')->with('success','Arquivo SIH enviado, por favor aguarde enquanto ela está sendo processada');
	}



	public function checkSih(Request $request)
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
		->where('Processo', 'Sih')
		->where('CodCidade', $CodCidade)->count();

		$pendecias = ListaUnicaPendencias::where('Ano', $request->Ano)
		->where('Trimestre', $request->Trimestre)
		->where('CodCidade', $CodCidade)->count();

		$Sih = Sih::where('Ano', $request->Ano)
		->where('Trimestre', $request->Trimestre)
		->where('CodCidade', $CodCidade)->count();

		$linkagem = LinkagemSih::where('Ano', $request->Ano)
		->where('Trimestre', $request->Trimestre)
		->where('CodCidade', $CodCidade)->count();

		return response()->json(
			[
				'sih'=> $Sih,
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
			$linkagem = LinkagemSih::find($value);
			if(!empty($linkagem)){
				$count++;
				if($request->tipo == 1){
					$linkagem->ParVerdadeiro = (bool)$request->tipo;
					$Vitima = Vitimas::findOrFail($linkagem->idListaUnica);
					$Sih = Sih::findOrFail($linkagem->idUploadSIH);
					if(!($Vitima->Sexo == 'FEMININO' || $Vitima->Sexo == 'MASCULINO')
						|| empty($Vitima->Sexo))
					{
						$Vitima->Sexo = $Sih->sexo;
					}
					if($Vitima->NomeMae == '' || empty($Vitima->NomeMae)){
						$Vitima->NomeMae = $Sih->NOME_MAE;
					}
					if($Vitima->NUMSUS == '' || empty($Vitima->NUMSUS)){
						$Vitima->NUMSUS = $Sih->CNS;
					}
					if($Vitima->DataNascimento == '99/99/9999' || empty($Vitima->DataNascimento)){
						$Vitima->DataNascimento = $Sih->DT_NASC;
						$idade = calculaIdade($Vitima->DataNascimento, $Vitima->DataAcidente);
						$FaixaEtaria = calculaFaixaEtaria($idade);
						$Vitima->Idade = $idade;
						$Vitima->FaixaEtaria = $FaixaEtaria;
					}
					if($Vitima->EnderecoVitima == '' || empty($Vitima->EnderecoVitima)){
						$Vitima->EnderecoVitima = $Sih->LOGR;
					}
					if($Vitima->BairroVitima == '' || empty($Vitima->BairroVitima)){
						$Vitima->BairroVitima = $Sih->LOGR_BAIR;
					}
					if($Vitima->NumeroVitima == '' || empty($Vitima->NumeroVitima)){
						$Vitima->NumeroVitima = $Sih->LOGR_N;
					}
					if($Vitima->CEPVitima == '' || empty($Vitima->CEPVitima)){
						$Vitima->CEPVitima = $Sih->CEP;
					}
					if($Vitima->MunicipioVitima == '' || empty($Vitima->MunicipioVitima)){
						if(!empty($Sih->MUNICIP)){
							$cidade = Cidades::where('codigo', $Sih->MUNICIP)->first();
							if(!empty($cidade)){
								$Vitima->MunicipioVitima = $cidade->municipio;
								$Vitima->EstadoVitima = $cidade->uf;
							}
						}
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
			return redirect(route('sih.pares').'?Ano='.$linkagem->Ano.'&Trimestre='.$linkagem->Trimestre)->with('success',$count.' pares foram verificados');
		}else{
			return redirect(route('sih.pares'))->with('error','Sem pares verificados');
		}
	}

}
