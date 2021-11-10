<?php

namespace App\Http\Controllers;
use App\Imports\ListaUnica;
use App\Notifications\ListaReady;
use App\Jobs\ProcessaListaUnica;
use App\Models\ListaUnicaPendencias;
use App\Models\QuadroMultiplo;
use App\Models\Cidades;
use App\Models\Sim;
use App\Models\Sih;
use App\Models\LinkagemSih;
use App\Models\LinkagemSim;
use App\Models\User;
use App\Models\Vitimas;
use App\Models\Processo;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Storage;
use Session;
use Auth;
use DB;
use Validator;
Use OneSignal;
Use DataTables;
use Maatwebsite\Excel\HeadingRowImport;

class ListaUnicaController extends Controller
{
	public function __construct()
	{
        //$this->middleware('auth');
	}

	public function index()
	{
		return view('listaUnicaNew');
	}
	public function relatorio()
	{
		Session::put('CodCidade', Auth::user()->CodCidade);
		Session::put('user_id', Auth::id());
		if(!empty(Auth::user()->cidade)){
			Session::put('CidadeAcidente', Auth::user()->cidade->municipio);
			Session::put('EstadoAcidente', Auth::user()->cidade->uf);
		}
		return view('relatorioListaUnica');
	}
	public function relatorioData(Request $request)
	{
		//validação permissão
		if(Auth::user()->tipo == 1){
			$CodCidade = $request->CodCidade;
		}elseif(Auth::user()->tipo == 2){
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
		$data = Vitimas::with('QuadroMultiplo')
		->where('vitimas_quadro_multiplo.Ano', $request->Ano)
		->where('vitimas_quadro_multiplo.CodCidade', $CodCidade)
		->where('vitimas_quadro_multiplo.Trimestre', $request->Trimestre);


		return Datatables::eloquent($data)
		->toJson();

	}

	public function deleteDados(Request $request)
	{
		//validação permissão
		if(Auth::user()->tipo == 1){
			$CodCidade = $request->CodCidade;
			$cidade = Cidades::find($request->CodCidade);
			if(empty($cidade)){
				return redirect()->back()->with('error','campos inválidos, você precisa informar a cidade');
			}
			$CodCidade = $cidade->codigo;	
			$CidadeAcidente = $cidade->municipio;	
			$EstadoAcidente = $cidade->uf;	
		}elseif(Auth::user()->tipo == 2){

			$cidade = Cidades::find($request->CodCidade);
			if(empty($cidade)){
				return redirect()->back()->with('error','campos inválidos, você precisa informar a cidade');
			}
			if($cidade->uf != Auth::user()->cidade->uf){
				return response()->json(['error'=> 'sem permissão'], 405);
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

		$linkagmesih = LinkagemSih::where('Ano', $request->Ano)
		->where('Trimestre', $request->Trimestre)
		->where('CodCidade', $CodCidade)->forceDelete();

		$sih = Sih::where('Ano', $request->Ano)
		->where('Trimestre', $request->Trimestre)
		->where('CodCidade', $CodCidade)->forceDelete();

		$lista = Vitimas::where('Ano', $request->Ano)
		->where('Trimestre', $request->Trimestre)
		->where('CodCidade', $CodCidade)->forceDelete();

		$pendecias = ListaUnicaPendencias::where('Ano', $request->Ano)
		->where('Trimestre', $request->Trimestre)
		->where('CodCidade', $CodCidade)->forceDelete();	

		$QuadroMultiplo = QuadroMultiplo::where('Ano', $request->Ano)
		->where('Trimestre', $request->Trimestre)
		->where('CodCidade', $CodCidade)->forceDelete();

		return response()->json('Dados deletados');

	}
	public function listaUnicaGrande(Request $request)
	{
		//validação permissão
		if(Auth::user()->tipo == 1){
			$CodCidade = $request->CodCidade;
			$cidade = Cidades::find($request->CodCidade);
			if(empty($cidade)){
				return redirect()->back()->with('error','campos inválidos, você precisa informar a cidade');
			}
			$CodCidade = $cidade->codigo;	
			$CidadeAcidente = $cidade->municipio;	
			$EstadoAcidente = $cidade->uf;	
		}elseif(Auth::user()->tipo == 2){

			$cidade = Cidades::find($request->CodCidade);
			if(empty($cidade)){
				return redirect()->back()->with('error','campos inválidos, você precisa informar a cidade');
			}
			if($cidade->uf != Auth::user()->cidade->uf){
				return response()->json(['error'=> 'sem permissão']);
			}	
			$CodCidade = $cidade->codigo;	
			$CidadeAcidente = $cidade->municipio;	
			$EstadoAcidente = $cidade->uf;	
		}else{
			$CodCidade = Auth::user()->CodCidade;	
		}
		if(empty($CodCidade) || empty($request->Ano) || empty($request->Trimestre))
		{
			return redirect()->back()->with('error','campos inválidos');
		}
		$request->validate([
			'arquivo' => 'required|file|max:50000|mimes:xls,xlsx',
			'Ano' => 'required',
			'Trimestre' => 'required',
		]);

		$file = Auth::id().'-'.Auth::user()->CodCidade.'-'.$request->Ano.'-'.$request->Trimestre;
		$ext = $request->arquivo->getClientOriginalExtension();
		$path = $request->file('arquivo')->storeAs(
			'public',  'listas/'.$file.'.'.$ext
		);
		$path = $file.'.'.$ext;
		$headings = (new HeadingRowImport)->toArray($request->file('arquivo'));
		$rows = $headings[0];

		$campos = array('nome_completo',
			'fonte_de_dados',
			'nome_da_mae',
			'boletim',
			'data_de_nascimento',
			'condicao_da_vitima',
			'gravidade_da_lesao',
			'tipo_veiculo',
			'sexo',
			'tipo_acidente',
			'hora_do_acidente',
			'data_do_acidente',
			'coordenada_x',
			'coordenada_y',
			'cidade_acidente',
			'uf_acidente',
			'quadra',
			'lote',
			'complemento',
			'velocidade_via',
			'numero',
			'bairro',
			'descricao',
			'placa',
			'endereco_do_acidente',
			'tipo_logradouro');
		foreach ($campos as $campo) {
			if(!in_array($campo, $rows[0])){
				return redirect()->back()->with('error','Lista Unica inválida, por favor utilize o <a href="ListaUnicaEXEMPLO.xls">MODELO</a> <BR>Campo: '.str_replace('_', ' ', $campo). ' ');
			}
		}
		$user = Auth::user();

		$user_id        = Session::get('user_id');
		if(!isset($CidadeAcidente)){
			$CidadeAcidente = $user->Cidade->municipio;
		}
		if(!isset($EstadoAcidente)){
			$EstadoAcidente = $user->Cidade->uf;
		}	
		

		$dataImport['Ano'] = $request->Ano;
		$dataImport['Trimestre'] = $request->Trimestre;
		$dataImport['CodCidade'] = $CodCidade;
		$dataImport['user_id'] = $user->id;
		$dataImport['EstadoAcidente'] = $EstadoAcidente;
		$dataImport['CidadeAcidente'] = $CidadeAcidente;

		$processo = Processo::create([
			'user_id'   => Auth::id(),
			'Ano'   => $dataImport['Ano'],
			'Trimestre'   => $dataImport['Trimestre'],
			'CodCidade'   => $dataImport['CodCidade'],
			'Processo'   => 'Lista',
			'Log'   => 'Na fila para processamento',
			'Status'   => 0,
		]);
		$dataImport['processo'] = $processo;

		ProcessaListaUnica::dispatch($user, $dataImport, $processo, $path)->delay(now()->addSeconds(2));
		return redirect()->route('listaUnica')->with('success','Lista Unica enviada, por favor aguarde enquanto ela está sendo processada');
	}

	public function getUsuarios(Request $request)
	{
		if ($request->ajax()) {
			$lista = User::with('Cidade');
			return Datatables::of($lista)->make(true);
		}
		return view('admin.listaUsuarios');
	}

	public function checkListaUnica(Request $request)
	{
		//validação permissão
		if(Auth::user()->tipo == 1){
			$CodCidade = $request->CodCidade;
			$cidade = Cidades::find($request->CodCidade);
			if(empty($cidade)){
				return redirect()->back()->with('error','campos inválidos, você precisa informar a cidade');
			}
			$CodCidade = $cidade->codigo;	
			$CidadeAcidente = $cidade->municipio;	
			$EstadoAcidente = $cidade->uf;	
		}elseif(Auth::user()->tipo == 2){

			$cidade = Cidades::find($request->CodCidade);
			if(empty($cidade)){
				return redirect()->back()->with('error','campos inválidos, você precisa informar a cidade');
			}
			if($cidade->uf != Auth::user()->cidade->uf){
				return response()->json(['error'=> 'sem permissão']);
			}	
			$CodCidade = $cidade->codigo;	
			$CidadeAcidente = $cidade->municipio;	
			$EstadoAcidente = $cidade->uf;	
		}else{
			$CodCidade = Auth::user()->CodCidade;	
		}

		$lista = Vitimas::where('Ano', $request->Ano)
		->where('Trimestre', $request->Trimestre)
		->where('CodCidade', $CodCidade)->count();

		$processo = Processo::where('Ano', $request->Ano)
		->where('Trimestre', $request->Trimestre)
		->where('Status', 0)
		->where('Processo', 'Lista')
		->where('CodCidade', $CodCidade)->count();



		$pendecias = ListaUnicaPendencias::where('Ano', $request->Ano)
		->where('Trimestre', $request->Trimestre)
		->where('CodCidade', $CodCidade)->count();

		$sim = Sim::where('Ano', $request->Ano)
		->where('Trimestre', $request->Trimestre)
		->where('CodCidade', $CodCidade)->count();

		$sih = Sih::where('Ano', $request->Ano)
		->where('Trimestre', $request->Trimestre)
		->where('CodCidade', $CodCidade)->count();

		$linkagem_sih = LinkagemSih::where('Ano', $request->Ano)
		->where('Trimestre', $request->Trimestre)
		->whereNull('ParVerdadeiro')
		->where('CodCidade', $CodCidade)->count();

		$linkagem_sim = LinkagemSim::where('Ano', $request->Ano)
		->where('Trimestre', $request->Trimestre)
		->whereNull('ParVerdadeiro')
		->where('CodCidade', $CodCidade)->count();

		return response()->json(
			[
				'sih'=> $sih,
				'sim'=> $sim,
				'pendencias' => $pendecias,
				'processo' => $processo,
				'lista' => $lista, 
				'linkagem_sim' => $linkagem_sim, 
				'linkagem_sih' => $linkagem_sih, 
			]);

	}

	public function checkPendencias(Request $request)
	{

		$pendecias = ListaUnicaPendencias::where('Ano', $request->Ano)
		->where('Trimestre', $request->Trimestre)
		->when(Auth::user()->tipo < 3, function ($q) use ($request) {
			return $q->where('CodCidade', $request->CodCidade);
		})
		->when(Auth::user()->tipo == 3, function ($q) use ($request) {
			return $q->where('CodCidade', Session::get('CodCidade'));
		})->first();
		if (!empty($pendecias)) {
			return response()->json(['success' => true]);
		}else{
			return response()->json(['success' => false]);
		}

	}

	public function dataPendenciasListaUnica(Request $request)
	{
		//validação permissão
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
		if(empty($CodCidade) || empty($request->Trimestre) || empty($request->Ano)){
			return [];
		}
		$pendecias = ListaUnicaPendencias::where('Ano', $request->Ano)
		->where('Trimestre', $request->Trimestre)
		->where('CodCidade', $CodCidade);

		return Datatables::of($pendecias)->make();

	}
	//Grava item pendencia
	public function gravaLista(Request $request)
	{

		if (!Session::has('Ano') && Session::get('Ano') != $request->Ano) {
			Session::put('Ano', $request->Ano);
		}
		if (!Session::has('Trimestre') && Session::get('Trimestre') != $request->Trimestre) {
			Session::put('Ano', $request->Ano);
		}

		if (isset($request->id)) {
			$ListaUnicaPendencias = ListaUnicaPendencias::find($request->id);
			$request->CodCidade   = $ListaUnicaPendencias->CodCidade;
			$request->Ano         = $ListaUnicaPendencias->Ano;
			$request->Trimestre   = $ListaUnicaPendencias->Trimestre;
		}else{
			return response()->json(['success' => false, 'mensagem'=>'Sem Lista Unica']);
		}

		$request->NomeCompleto   = validaNomeCompleto($request->NomeCompleto);
		$request->NomeMae        = validaNomeCompleto($request->NomeMae);
		$request->Boletim        = validaNomeCompleto($request->Boletim);
		$request->DataNascimento = validaData($request->DataNascimento);
		$request->CondicaoVitima = validaCondicaoVitima($request->CondicaoVitima);
		$request->GravidadeLesao = validaTipoLesao($request->GravidadeLesao);
		$request->TipoVeiculo    = validaTipoVeiculo($request->TipoVeiculo);
		$request->Sexo           = validaSexo($request->Sexo);
		$request->MeioTransporte = validaCondicaoVitima($request->MeioTransporte);
		$request->TipoAcidente   = validaTipoAcidente($request->TipoAcidente);
		$request->Hora           = validaHora($request->Hora);
		$request->FonteDados     = validaFonteDados($request->FonteDados);
		$request->DataAcidente   = validaData($request->DataAcidente);

		if ($request->DataAcidente == '99/99/9999') {
			return response()->json(['success' => false, 'mensagem'=>'Sem Data Acidente']);
		}

		if (empty($request->FonteDados)) {
			return response()->json(['success' => false, 'mensagem'=>'Sem Fonte Dados']);
		}
		if (empty($request->Boletim)) {
			return response()->json(['success' => false, 'mensagem'=>'Sem Boletim']);

		}
		if (empty($request->NomeCompleto)) {
			return response()->json(['success' => false, 'mensagem'=>'Sem Nome Completo']);

		}
		DB::beginTransaction();
		try {
			$quadro = QuadroMultiplo::where('Boletim',$request->Boletim)
			->where('FonteDados',$request->FonteDados)
			->where('Ano',$ListaUnicaPendencias->Ano)
			->where('CodCidade',$ListaUnicaPendencias->CodCidade)
			->where('Trimestre',$ListaUnicaPendencias->Trimestre)->first();
			if(empty($quadro)){
				$quadro = QuadroMultiplo::create([
					'Ano'                   => $ListaUnicaPendencias->Ano,
					'Trimestre'             => $ListaUnicaPendencias->Trimestre,
					'CodCidade'             => $ListaUnicaPendencias->CodCidade,
					'DataAcidente'          => $request->DataAcidente,
					'Boletim'               => $request->Boletim,
					'FonteDados'            => $request->FonteDados,
					'TipoAcidente'          => $request->TipoAcidente,
					'HoraAcidente'          => $request->HoraAcidente,
					'RuaAvenida'            => $request->RuaAvenida,
					'Numero'                => $request->Numero,
					'Bairro'                => $request->Bairro,
					'Complemento'           => $request->Complemento,
					'Quadra'                => $request->Quadra,
					'Lote'                  => $request->Lote,
					'CidadeAcidente'        => (isset($request->CidadeAcidente) ? $request->CidadeAcidente : Session::get('CidadeAcidente')),
					'EstadoAcidente'        => (isset($request->EstadoAcidente) ? $request->EstadoAcidente : Session::get('EstadoAcidente')),
					'CepAcidente'           => $request->CepAcidente,
					'VelocidadeVia'         => $request->VelocidadeVia,
					'CoordenadaX'           => $request->CoordenadaX,
					'CoordenadaY'           => $request->CoordenadaY,
					'IdentificadorAcidente' => $request->FonteDados . '/' . $request->Boletim,
					'user_id'               => Auth::id(),
				]);
			}

			$vitima = Vitimas::create([
				'Ano'              => $ListaUnicaPendencias->Ano,
				'Trimestre'        => $ListaUnicaPendencias->Trimestre,
				'CodCidade'        => $ListaUnicaPendencias->CodCidade,
				'idQuadroMultiplo' => $quadro->id,
				'DataNascimento'   => $request->DataNascimento,
				'DataAcidente'     => $request->DataAcidente,
				'NomeMae'          => $request->NomeMae,
				'Sexo'             => $request->Sexo,
				'CondicaoVitima'   => $request->CondicaoVitima,
				'NomeCompleto'     => $request->NomeCompleto,
				'NomeBusca'        => \BuscaBR::encode($request->NomeCompleto),
				'GravidadeLesao'   => $request->GravidadeLesao,
				'Placa'            => $request->Placa,
				'Idade' 		   => calculaIdade($request->DataNascimento, $quadro->DataAcidente),
				'FaixaEtaria'      => calculaFaixaEtaria(calculaIdade($request->DataNascimento, $quadro->DataAcidente)),
				'MeioTransporte'   => $request->TipoVeiculo,
				'CondicaoVitima'   => $request->CondicaoVitima,
				'MeioTransporte'   => $request->MeioTransporte,
				'Descricao'        => $request->Descricao,
				'user_id'          => Auth::id(),
			]);			
			$ListaUnicaPendencias->delete();
			\DB::commit();
		} catch (\Exception $e) {
			\DB::rollback();
			\Log::error("Insert QuadroMultiplo: " . $e->getMessage());
			return response()->json(['success' => false, 'mensagem'=>$e->getMessage()]);			
		}

		return response()->json(['success' => true]);

	}

}
