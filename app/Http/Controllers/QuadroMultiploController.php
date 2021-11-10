<?php

namespace App\Http\Controllers;
use App\Models\ListaUnicaPendencias;
use App\Models\QuadroMultiplo;
use App\Models\Cidades;
use App\Models\User;
use App\Models\Vitimas;
use App\Models\FatoresRisco;
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

class QuadroMultiploController extends Controller
{
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
		return view('quadroMultiplo');
	}
	public function deletaVitima(Request $request)
	{
		Session::put('CodCidade', Auth::user()->CodCidade);
		Session::put('user_id', Auth::id());
		if(!empty(Auth::user()->cidade)){
			Session::put('CidadeAcidente', Auth::user()->cidade->municipio);
			Session::put('EstadoAcidente', Auth::user()->cidade->uf);
		}
		if(!empty($request->id_vitima)){
			$vitima = Vitimas::find($request->id_vitima);

			if(!empty($vitima)){
				if(Auth::user()->tipo == 2){
					$cidade = Cidades::find($vitima->CodCidade);
					if($cidade->uf != Auth::user()->cidade->uf){
						return response()->json(['error'=> 'sem permissão'], 405);
					}
				}elseif(Auth::user()->tipo >= 3 && $vitima->CodCidade != Auth::user()->CodCidade){
					return response()->json(['error'=> 'sem permissão'], 405);
				}
				$vitima->forceDelete();
				return response()->json('ok');
			}else{
				return response()->json(['error'=> 'erro ao deletar'], 405);
			}
		}
	}
	public function deletaAcidente(Request $request)
	{
		Session::put('CodCidade', Auth::user()->CodCidade);
		Session::put('user_id', Auth::id());
		if(!empty(Auth::user()->cidade)){
			Session::put('CidadeAcidente', Auth::user()->cidade->municipio);
			Session::put('EstadoAcidente', Auth::user()->cidade->uf);
		}

		if(!empty($request->id_acidente)){
			$acidente = QuadroMultiplo::find($request->id_acidente);

			if(!empty($acidente)){
				if(Auth::user()->tipo == 2){
					$cidade = Cidades::find($acidente->CodCidade);
					if($cidade->uf != Auth::user()->cidade->uf){
						return response()->json(['error'=> 'sem permissão'], 405);
					}
				}elseif(Auth::user()->tipo >= 3 && $acidente->CodCidade != Auth::user()->CodCidade){
					return response()->json(['error'=> 'sem permissão'], 405);
				}
				$vitimas = Vitimas::where('idQuadroMultiplo',$acidente->id)->get();
				foreach ($vitimas as $vitima) {
					$vitima->forceDelete();
				}
				$acidente->forceDelete();
				return response()->json('ok');
			}else{
				return response()->json(['error'=> 'erro ao deletar'], 405);
			}
		}
	}
	public function relatorio()
	{
		Session::put('CodCidade', Auth::user()->CodCidade);
		Session::put('user_id', Auth::id());
		if(!empty(Auth::user()->cidade)){
			Session::put('CidadeAcidente', Auth::user()->cidade->municipio);
			Session::put('EstadoAcidente', Auth::user()->cidade->uf);
		}
		return view('relatorioQuadroMultiplo');
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
		$Fatais = DB::table('vitimas_quadro_multiplo')
		->select('vitimas_quadro_multiplo.idQuadroMultiplo', DB::raw('count(vitimas_quadro_multiplo.id) as QtdFatais'))
		->where('Ano', $request->Ano)
		->where('Trimestre', $request->Trimestre)
		->where('CodCidade', $CodCidade)
		->where(function($q) {
			$q->where('GravidadeLesao', 'FATAL')
			->orWhere('GravidadeLesao', 'FATAL LOCAL')
			->orWhere('GravidadeLesao', 'FATAL POSTERIOR');
		})
		->groupBy('idQuadroMultiplo');
		$Leves = DB::table('vitimas_quadro_multiplo')
		->select('vitimas_quadro_multiplo.idQuadroMultiplo', DB::raw('count(vitimas_quadro_multiplo.id) as QtdLeves'))
		->where('Ano', $request->Ano)
		->where('Trimestre', $request->Trimestre)
		->where('CodCidade', $CodCidade)
		->where(function($q) {
			$q->where('GravidadeLesao', 'MODERADA')
			->orWhere('GravidadeLesao', 'GRAVE')
			->orWhere('GravidadeLesao', 'COM LESOES')
			->orWhere('GravidadeLesao', 'LESOES LEVES')
			->orWhere('GravidadeLesao', 'NAO INFORMADO')
			->orWhere('GravidadeLesao', 'LESOES NAO ESPECIFICADAS')
			->orWhere('GravidadeLesao', 'SEM LESOES');
		})
		->groupBy('idQuadroMultiplo');
		//dd($Fatais, $Leves);
		$data = QuadroMultiplo::with('vitimas', 'FatoresRisco')
		// ->leftJoin('vitimas_quadro_multiplo', 'vitimas_quadro_multiplo.idQuadroMultiplo', '=', 'quadro_multiplo.id')
		->select('quadro_multiplo.*', \DB::raw('fatais.QtdFatais'), \DB::raw('leves.QtdLeves'))	
		->leftJoinSub($Fatais, 'fatais', function ($join) {
			$join->on('quadro_multiplo.id', '=', 'fatais.idQuadroMultiplo');
		})
		->leftJoinSub($Leves, 'leves', function ($join) {
			$join->on('quadro_multiplo.id', '=', 'leves.idQuadroMultiplo');
		})
		->when($request->Filtro != '', function ($q) use ($request){
			if($request->Filtro == 'ApenasFatais'){
				return $q->whereHas('vitimas', function($query) {
					$query->where('GravidadeLesao', 'FATAL')
					->orWhere('GravidadeLesao', 'FATAL LOCAL')
					->orWhere('GravidadeLesao', 'FATAL POSTERIOR');
				});
			}elseif($request->Filtro == 'ApenasFeridos'){
				return $q->whereHas('vitimas', function($query) {
					$query->where('GravidadeLesao', 'MODERADA')
					->orWhere('GravidadeLesao', 'GRAVE')
					->orWhere('GravidadeLesao', 'COM LESOES')
					->orWhere('GravidadeLesao', 'LESOES LEVES');
				});
			}elseif($request->Filtro == 'ApenasLinkadosSIM'){
				return $q->whereHas('LinkagemSim');
			}elseif($request->Filtro == 'ApenasLinkadosSIH'){
				return $q->whereHas('LinkagemSih');
			}elseif($request->Filtro == 'SemFatores'){
				return $q->doesntHave('FatoresRisco');
			}elseif($request->Filtro == 'ComFatores'){
				return $q->whereHas('FatoresRisco');
			}
		})		
		->when(!empty($request->vitima), function ($q) use ($request, $CodCidade){
			$vitimas = Vitimas::where('NomeCompleto','ILIKE','%'.$request->vitima.'%')
			->where('Ano', $request->Ano)
			->where('CodCidade', $CodCidade)
			->where('Trimestre', $request->Trimestre)->pluck('idQuadroMultiplo')->toArray();
			$q->whereIn('id',$vitimas);
		})
		->where('quadro_multiplo.Ano', $request->Ano)
		->where('quadro_multiplo.CodCidade', $CodCidade)
		->where('quadro_multiplo.Trimestre', $request->Trimestre);


		return Datatables::eloquent($data)
		->addColumn('acoes', function ($lista) {			
			return $lista->id;

		})
		// ->addColumn('QtdVitimas', function ($lista) {
		// 	return $lista->qtd_tipos_vitimas;
		// })
		->addColumn('NomeVitimas', function ($lista) {
			if($lista->vitimas->count() == 0){
				return '';
			}else{
				return $lista->vitimas->pluck('NomeCompleto')->implode(', ');
			}

		})
		->toJson();

	}
	public function checkQuadroMultiplo(Request $request)
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
			return response()->json(['error'=> 'campos invalidos']);
		}
		$lista = Vitimas::where('Ano', $request->Ano)
		->where('Trimestre', $request->Trimestre);
		$lista->where('CodCidade', $CodCidade);
		$lista->first();

		$pendecias = ListaUnicaPendencias::where('Ano', $request->Ano)
		->where('Trimestre', $request->Trimestre);
		$pendecias->where('CodCidade', $CodCidade);

		$pendecias->first();

		if (!empty($pendecias)) {
			return 'PENDENCIAS';
		}
		if (empty($lista)) {
			return 'VAZIO';
		}
	}
	public function saveQuadroMultiplo(Request $request)
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
		//dd($request->all());
		$validator = Validator::make($request->all(), [
			'FonteDados' => 'required',
			'IdentificadorAcidente' => 'required',
			'Ano' => 'required',
			'Trimestre' => 'required',
			'TipoAcidente' => 'required',
			'TipoVitima.*' => 'required',
			'VitimaDataNascimento.*' => 'required|regex:/[0-9]{2}\/[0-9]{2}\/[0-9]{4}/',
			'NomeCompleto.*' => 'required',
			'DataAcidente' => 'required|date_format:d/m/Y',
		]);
		if ($validator->fails()) {    
			return response()->json(['error'=>$validator->errors()->all()]);
		}

		if(!empty($request->idQuadroMultiplo)){
			$quadro = QuadroMultiplo::find($request->idQuadroMultiplo);
			if(empty($quadro)){
				return response()->json(['error'=> 'Erro ao editar']); 
			}
		}else{
			$quadro = new QuadroMultiplo();
		}
		$quadro->Ano = $request['Ano'];
		$quadro->Trimestre = $request['Trimestre'];
		$quadro->CodCidade = $CodCidade;
		$quadro->user_id = Auth::id();

		$quadro->FonteDados = tirarAcentos($request['FonteDados']);
		$quadro->Boletim = tirarAcentos($request['IdentificadorAcidente']);
		$quadro->IdentificadorAcidente = tirarAcentos($request['FonteDados']) . '/' . $request['IdentificadorAcidente'];
		$quadro->IdentificadorAcidente2 = tirarAcentos($request['FonteDados2']) . '/' . $request['IdentificadorAcidente2'];
		$quadro->IdentificadorAcidente3 = tirarAcentos($request['FonteDados3']) . '/' . $request['IdentificadorAcidente3'];

		$quadro->TipoAcidente = tirarAcentos($request['TipoAcidente']);
		$quadro->RuaAvenida = tirarAcentos($request['Endereco']);
		$quadro->DataAcidente = $request['DataAcidente'];
		$quadro->Numero = tirarAcentos($request['Numero']);
		$quadro->Complemento = tirarAcentos($request['Complemento']);
		$quadro->Quadra = tirarAcentos($request['Quadra']);
		$quadro->Lote = tirarAcentos($request['Lote']);
		$quadro->VelocidadeVia = $request['velocidade_via'];
		$quadro->Bairro = tirarAcentos($request['Bairro']);
		if(empty($request['MunicipioAcidente'])){
			$cidade = Cidades::find($request->CodCidade);
			$quadro->CidadeAcidente = $cidade->municipio;
		}else{
			$quadro->CidadeAcidente = tirarAcentos($request['MunicipioAcidente']);
		}		
		if(empty($request['EstadoAcidente'])){
			$cidade = Cidades::find($request->CodCidade);
			$quadro->EstadoAcidente = $cidade->uf;
		}else{
			$quadro->EstadoAcidente = tirarAcentos($request['EstadoAcidente']);
		}
		$quadro->CepAcidente = tirarAcentos($request['CEP']);
		$quadro->CoordenadaX = $request['CoordX'];
		$quadro->CoordenadaY = $request['CoordY'];

		if (empty($request['HoraAcidente'])) {
			$quadro->HoraAcidente = '99';
		} else {
			$quadro->HoraAcidente = validaHora($request['HoraAcidente']);
		}
		$quadro->save();
		//Fatores Risco
		$FatoresRisco = FatoresRisco::updateOrCreate(
			['idQuadroMultiplo' => $quadro->id ],
			[
				'idQuadroMultiplo' => $quadro->id,
				'Ano' => $quadro->Ano,
				'user_id' =>  Auth::id(),
				'Trimestre' => $quadro->Trimestre,
				'CodCidade' => $quadro->CodCidade,

				'Velocidade' => $request->Velocidade ?? 0,
				'TipoVelocidade' => $request->Velocidade > 0 ? $request->TipoVelocidade : null,
				'UsuarioContributivo_Velocidade' => $request->Velocidade > 0 ? $request->UsuarioContributivo_Velocidade : null,
				'Alcool' => $request->Alcool ?? 0,
				'UsuarioContributivo_Alcool' => $request->Alcool > 0 ? $request->UsuarioContributivo_Alcool : null,
				'Infraestrutura' => $request->Infraestrutura,
				'TipoInfraestrutura' => $request->Infraestrutura > 0 ? $request->TipoInfraestrutura : null,
				'Veiculo' => $request->Veiculo ?? 0,
				'UsuarioContributivo_Veiculo' => $request->Veiculo > 0 ? $request->UsuarioContributivo_Veiculo : null,
				'Fadiga' => $request->Fadiga ?? 0,
				'UsuarioContributivo_Fadiga' => $request->Fadiga > 0 ? $request->UsuarioContributivo_Fadiga : null,
				'Visibilidade' => $request->Visibilidade ?? 0,
				'Drogas' => $request->Drogas ?? 0,
				'TipoDroga' => $request->Drogas > 0 ? $request->TipoDroga : null,
				'UsuarioContributivo_Drogas' => $request->Drogas > 0 ? $request->UsuarioContributivo_Drogas : null,
				'Distacao' => $request->Distacao ?? 0,
				'UsuarioContributivo_Distacao' => $request->Distacao > 0 ? $request->UsuarioContributivo_Distacao : null,
				'AvancarSinal' => $request->AvancarSinal ?? 0,
				'UsuarioContributivo_AvancarSinal' => $request->AvancarSinal > 0 ? $request->UsuarioContributivo_AvancarSinal : null,
				'CondutorSemHabilitacao' => $request->CondutorSemHabilitacao ?? 0,
				'UsuarioContributivo_CondutorSemHabilitacao' => $request->CondutorSemHabilitacao > 0 ? $request->UsuarioContributivo_CondutorSemHabilitacao : null,
				'LocalProibido' => $request->LocalProibido ?? 0,
				'UsuarioContributivo_LocalProibido' => $request->LocalProibido > 0 ? $request->UsuarioContributivo_LocalProibido : null,
				'LocalImproprio' => $request->LocalImproprio ?? 0,
				'UsuarioContributivo_LocalImproprio' => $request->LocalImproprio > 0 ? $request->UsuarioContributivo_LocalImproprio : null,
				'MudancaFaixa' => $request->MudancaFaixa ?? 0,
				'UsuarioContributivo_MudancaFaixa' => $request->MudancaFaixa > 0 ? $request->UsuarioContributivo_MudancaFaixa : null,
				'DistanciaMinima' => $request->DistanciaMinima ?? 0,
				'UsuarioContributivo_DistanciaMinima' => $request->DistanciaMinima > 0 ? $request->UsuarioContributivo_DistanciaMinima : null,
				'Preferencia' => $request->Preferencia ?? 0,
				'UsuarioContributivo_Preferencia' =>  $request->Preferencia > 0 ? $request->UsuarioContributivo_Preferencia : null,
				'PreferenciaPedestre' => $request->PreferenciaPedestre ?? 0,
				'UsuarioContributivo_PreferenciaPedestre' =>  $request->PreferenciaPedestre > 0 ? $request->UsuarioContributivo_PreferenciaPedestre : null,
				'ImprudenciaPedestre' => $request->ImprudenciaPedestre ?? 0,
				'UsuarioContributivo_ImprudenciaPedestre' => $request->ImprudenciaPedestre > 0 ? $request->UsuarioContributivo_ImprudenciaPedestre : null,
				'CintoSeguranca' => $request->CintoSeguranca ?? 0,
				'UsuarioContributivo_CintoSeguranca' => $request->CintoSeguranca > 0 ? $request->UsuarioContributivo_CintoSeguranca : null,
				'EquipamentoProtecao' => $request->EquipamentoProtecao ?? 0,
				'GerenciamentoTrauma' => $request->GerenciamentoTrauma ?? 0,
				'ObjetosLateraisVia' => $request->ObjetosLateraisVia ?? 0,
				'Capacete' => $request->Capacete ?? 0,
				'UsuarioContributivo_Capacete' => $request->Capacete > 0 ? $request->UsuarioContributivo_Capacete : null,
				'outra_protecao' => $request->outra_protecao ?? 0,
				'definicao_outra_protecao' => $request->outra_protecao > 0 ? $request->definicao_outra_protecao : null,
			]);
		if(!empty($request->TipoVitima)){
			foreach ($request->TipoVitima as $key => $vitima) {
				if($request->id_vitima[$key] == 'null'){
					$idVitima =  0;
				}else{
					$idVitima = $request->id_vitima[$key];
				}
				$VitimaQM = Vitimas::updateOrCreate(
					['id' => $idVitima  ],
					[
						'idQuadroMultiplo' => $quadro->id ?? null,
						'Ano' => $quadro->Ano,
						'user_id' =>  Auth::id(),
						'Trimestre' => $quadro->Trimestre,
						'CodCidade' => $quadro->CodCidade,
						'NomeCompleto' => validaNomeCompleto($request->VitimaNome[$key]),
						'NomeBusca' => \BuscaBR::encode(validaNomeCompleto($request->VitimaNome[$key])),
						'NomeMae' => isset($request->VitimaNomeMae[$key]) ? validaNomeCompleto($request->VitimaNomeMae[$key]) : '',
						'Sexo' => isset($request->SexoVitima[$key]) ? validaSexo($request->SexoVitima[$key]) : 'NAO INFORMADO',
						'DataNascimento' => $request->VitimaDataNascimento[$key],
						'Idade' => calculaIdade($request->VitimaDataNascimento[$key], $quadro->DataAcidente),
						'FaixaEtaria' => calculaFaixaEtaria(calculaIdade($request->VitimaDataNascimento[$key], $quadro->DataAcidente)),
						'DataAcidente' => $quadro->DataAcidente,
						'GravidadeLesao' => $request->TipoVitima[$key] ?? 'NAO INFORMADO',
						'Placa' => $request->Placa[$key] ?? null,
						'MeioTransporte' => $request->MeioVitima[$key] ?? 'NAO INFORMADO',
						'CondicaoVitima' => $request->CondVitima[$key] ?? 'NAO INFORMADO',

						'CBO' => $request->CBO[$key] ?? null,
						'NUMSUS' => $request->NUMSUS[$key] ?? null,

						'CEPVitima' => $request->CEPVitima[$key],
						'EnderecoVitima' => $request->EnderecoVitima[$key] ?? null,
						'BairroVitima' => $request->BairroVitima[$key] ?? null,
						'NumeroVitima' => $request->NumeroVitima[$key] ?? null,
						'MunicipioVitima' => $request->MunicipioVitima[$key] ?? null,
						'EstadoVitima' => $request->EstadoVitima[$key] ?? null,
						'CoordVitimaX' => $request->CoordVitimaX[$key] ?? null,
						'CoordVitimaY' => $request->CoordVitimaY[$key] ?? null,
						'Descricao' => $request->Descricao[$key] ?? null,

						'InfluenciaAlcool' => $request->VitimaInfluencia[$key] ?? null,
						'ComprovaAlcoolemia' => $request->ComprovaAlcool[$key] ?? null,
						'ValorAlcoolemia' => $request->ValorAlcoolemia[$key] ?? null,
						'ComprovaBafometro' => $request->ComprovaBafometro[$key] ?? null,
						'ValorBafometro' => $request->ValorBafometro[$key] ?? null,
					]);
			}
		}

		return response()->json(['sucess'=> 'OK']); 
	}
	public function dataQuadroMultiplo(Request $request)
	{
		//validação permissão
		if(Auth::user()->tipo == 1){
			if(isset($request->CodCidade)){
				$CodCidade = $request->CodCidade;
			}else{
				$CodCidade = Session::get('CodCidade');
			}		
		}elseif(Auth::user()->tipo == 2){
			$cidade = Cidades::find($request->CodCidade);
			if(empty($cidade)){
				return response()->json(['error'=> 'campos invalidos']);
			}
			if($cidade->uf != Auth::user()->cidade->uf){
				return response()->json(['error'=> 'sem permissão']);
			}	
			$CodCidade = $cidade->codigo;	
		}else{
			$CodCidade = Auth::user()->CodCidade;	
		}
		if(empty($CodCidade) || empty($request->Ano) || empty($request->Trimestre))
		{
			return redirect()->back()->with('error','campos inválidos');
		}
		$Fatais = DB::table('vitimas_quadro_multiplo')
		->select('vitimas_quadro_multiplo.idQuadroMultiplo', DB::raw('count(vitimas_quadro_multiplo.id) as QtdFatais'))
		->where('Ano', $request->Ano)
		->where('Trimestre', $request->Trimestre)
		->where('CodCidade', $CodCidade)
		->where(function($q) {
			$q->where('GravidadeLesao', 'FATAL')
			->orWhere('GravidadeLesao', 'FATAL LOCAL')
			->orWhere('GravidadeLesao', 'FATAL POSTERIOR');
		})
		->groupBy('idQuadroMultiplo');
		$Leves = DB::table('vitimas_quadro_multiplo')
		->select('vitimas_quadro_multiplo.idQuadroMultiplo', DB::raw('count(vitimas_quadro_multiplo.id) as QtdLeves'))
		->where('Ano', $request->Ano)
		->where('Trimestre', $request->Trimestre)
		->where('CodCidade', $CodCidade)
		->where(function($q) {
			$q->where('GravidadeLesao', 'MODERADA')
			->orWhere('GravidadeLesao', 'GRAVE')
			->orWhere('GravidadeLesao', 'COM LESOES')
			->orWhere('GravidadeLesao', 'LESOES LEVES')
			->orWhere('GravidadeLesao', 'NAO INFORMADO')
			->orWhere('GravidadeLesao', 'LESOES NAO ESPECIFICADAS')
			->orWhere('GravidadeLesao', 'SEM LESOES');
		})
		->groupBy('idQuadroMultiplo');
		//dd($Fatais, $Leves);
		$quadro = QuadroMultiplo::with('vitimas', 'FatoresRisco')
		// ->leftJoin('vitimas_quadro_multiplo', 'vitimas_quadro_multiplo.idQuadroMultiplo', '=', 'quadro_multiplo.id')
		->select('quadro_multiplo.*', \DB::raw('fatais.QtdFatais'), \DB::raw('leves.QtdLeves'))	
		// ->groupby('quadro_multiplo.id')	
		->leftJoinSub($Fatais, 'fatais', function ($join) {
			$join->on('quadro_multiplo.id', '=', 'fatais.idQuadroMultiplo');
		})
		->leftJoinSub($Leves, 'leves', function ($join) {
			$join->on('quadro_multiplo.id', '=', 'leves.idQuadroMultiplo');
		})
		->when($request->Filtro != '', function ($q) use ($request){
			if($request->Filtro == 'ApenasFatais'){
				return $q->whereHas('vitimas', function($query) {
					$query->where('GravidadeLesao', 'FATAL')
					->orWhere('GravidadeLesao', 'FATAL LOCAL')
					->orWhere('GravidadeLesao', 'FATAL POSTERIOR');
				});
			}elseif($request->Filtro == 'ApenasFeridos'){
				return $q->whereHas('vitimas', function($query) {
					$query->where('GravidadeLesao', 'MODERADA')
					->orWhere('GravidadeLesao', 'GRAVE')
					->orWhere('GravidadeLesao', 'COM LESOES')
					->orWhere('GravidadeLesao', 'LESOES LEVES');
				});
			}elseif($request->Filtro == 'ApenasLinkadosSIM'){
				return $q->whereHas('LinkagemSim');
			}elseif($request->Filtro == 'ApenasLinkadosSIH'){
				return $q->whereHas('LinkagemSih');
			}elseif($request->Filtro == 'SemFatores'){
				return $q->doesntHave('FatoresRisco');
			}elseif($request->Filtro == 'ComFatores'){
				return $q->whereHas('FatoresRisco');
			}
		})
		->where('quadro_multiplo.Ano', $request->Ano)
		->where('quadro_multiplo.Trimestre', $request->Trimestre)
		->where('quadro_multiplo.CodCidade', $CodCidade);
		//dd($quadro->get()->count(), $CodCidade, $request->CodCidade,$request->Ano, $request->Trimestre);
		return Datatables::of($quadro)
		->addColumn('TotalFatores', function ($lista) {
			if(empty($lista->FatoresRisco)){
				return "Não Preenchido";
			}elseif ($lista->FatoresRisco->total == 0) {
				return "Não Preenchido";
			}else{
				return "Preenchidos";
			}
		})
		// ->addColumn('QtdVitimas', function ($lista) {
		// 	return $lista->qtd_tipos_vitimas;
		// })
		->addColumn('NomeVitimas', function ($lista) {
			if($lista->vitimas->count() == 0){
				return '';
			}else{
				return $lista->vitimas->pluck('NomeCompleto')->implode(', ');
			}

		})
		->addColumn('acoes', function ($lista) {			
			return $lista->id;
		})
		->toJson();

	}

}
