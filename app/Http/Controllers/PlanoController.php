<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Cidades;
use App\Models\Plano;
use App\Models\Projeto;
use Auth;
Use DataTables;
use Session;
use DB;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Crypt;

class PlanoController extends Controller
{

	public function index()
	{
		Session::put('CodCidade', Auth::user()->CodCidade);
		Session::put('user_id', Auth::id());
		if(!empty(Auth::user()->cidade)){
			Session::put('CidadeAcidente', Auth::user()->cidade->municipio);
			Session::put('EstadoAcidente', Auth::user()->cidade->uf);
		}
		return view('plano');
	}

	public function BuscaProgramas(Request $request)
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
		if(empty($CodCidade) || empty($request->Ano)){
			return [];
		}
		if(isset($request['id'])){
			$data = Plano::where('Ano', $request->Ano)
			->where('CodCidade', $CodCidade)
			->where('id', $request['id'])
			->first();
			if(!empty($data)){
				return response()->json($data);
			}else{
				return response()->json(['error'=> 'Plano não encontrado']);
			}
		}
		$data = Plano::where('Ano', $request->Ano)
		->where('CodCidade', $CodCidade)->get()->toArray();

		return $data;

	}	

	public function BuscaProjetos(Request $request)
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
		if(empty($CodCidade) || empty($request->Ano)){
			return response()->json(['error'=> 'sem resultado']);
		}
		if(isset($request['idPlano'])){
			$projetos = DB::table('projeto_plano')->where('idPlano',$request['idPlano'])->get();
			$data = Projeto::with('planos')->where('Ano', $request->Ano)
			->where('CodCidade', $CodCidade)
			->whereIn('id', $projetos->pluck('idProjeto'))
			->get();
			if($data->count() > 0){
				foreach ($data as $projeto) {
					$projeto->PesoPrograma = $projeto->planos->where('id',$request['idPlano'])->first()->pivot->PesoPlano;
					$dataFormatada[] = $projeto;
				}
				return response()->json($dataFormatada);
			}else{
				return [];
			}			

		}
		if(isset($request['idProjeto'])){
			$data = Projeto::with('planos')->where('Ano', $request->Ano)
			->where('CodCidade', $CodCidade)
			->where('id', $request['idProjeto'])
			->first();
			return response()->json($data);
		}
		$data = Projeto::with('planos')->where('Ano', $request->Ano)
		->where('CodCidade', $CodCidade)->get();

		return $data;

	}	
	public function removerProjeto(Request $request)
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
		if(empty($CodCidade) || empty($request->Ano)){
			return response()->json(['error'=> 'sem resultado']);
		}
		if(isset($request['idProjeto'])){
			$data = Projeto::with('planos')->where('Ano', $request->Ano)
			->where('CodCidade', $CodCidade)
			->where('id', $request['idProjeto'])
			->first();
			if(!empty($data)){
				$data->delete();
			}			
			return response()->json(['sucess'=>'removido']);
		}
		return response()->json(['error'=> 'Erro ao remover']);

	}	
	public function removerProjetoPrograma(Request $request)
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
		if(empty($CodCidade) || empty($request->Ano)){
			return response()->json(['error'=> 'sem resultado']);
		}
		if(isset($request['idProjeto']) && isset($request['idPlano'])){
			DB::table('projeto_plano')
			->where('idPlano',$request['idPlano'])
			->where('idProjeto',$request['idProjeto'])->delete();	
			return response()->json(['sucess'=>'removido']);
		}
		return response()->json(['error'=> 'Erro ao remover']);

	}	
	public function removerPlano(Request $request)
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
		if(empty($CodCidade) || empty($request->Ano)){
			return response()->json(['error'=> 'sem resultado']);
		}
		if(isset($request['idPrograma'])){
			$data = Plano::where('Ano', $request->Ano)
			->where('CodCidade', $CodCidade)
			->where('id', $request['idPrograma'])
			->first();
			if(!empty($data)){
				$data->delete();
			}			
			return response()->json(['sucess'=>'removido']);
		}
		return response()->json(['error'=> 'Erro ao remover']);

	}	


	public function gravaPlano(Request $request)
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
		if(empty($CodCidade) || empty($request->Ano)){
			return response()->json(['error'=> 'erro parametros invalidos']);
		}
		if (empty($request['idPlano'])){
			$PLANO = new Plano();
		}else{
			$PLANO = Plano::where('Ano', $request->Ano)
			->where('CodCidade', $CodCidade)
			->where('id', $request['idPlano'])
			->first();
			if(empty($PLANO)){
				return response()->json(['error'=> 'erro ao editar']);
			}
		}

		$PLANO->Ano = $request['Ano'];
		$PLANO->CodCidade = $CodCidade;
		$PLANO->user_id = Auth::id();


		$PLANO->NomePrograma = $request['NomePrograma'];
		$PLANO->PesoPrograma = $request['PesoPrograma'];
		$PLANO->ObjetivoPrograma = $request['ObjetivoPrograma'];
		$PLANO->Publico = $request['Publico'];
		$PLANO->IndicadorIntermediarioPrograma = $request['IndicadorIntermediarioPrograma'];
		$PLANO->MetaIntermediaria = $request['MetaIntermediaria'];
		$PLANO->MetaIntermediariaDescritiva = $request['MetaIntermediariaDescritiva'];
		$PLANO->IndicadorFinalPrograma = $request['IndicadorFinalPrograma'];
		$PLANO->MetaFinal = $request['MetaFinal'];
		$PLANO->MetaFinalDescritiva = $request['MetaFinalDescritiva'];
		$PLANO->CoordenadorPrograma = $request['CoordenadorPrograma'];
		$PLANO->SecretariasEnvolvidas = $request['SecretariasEnvolvidas'];
		$PLANO->ParceriasPublicas = $request['ParceriasPublicas'];
		$PLANO->ParceriasPrivadas = $request['ParceriasPrivadas'];
		$PLANO->ParceriasCivil = $request['ParceriasCivil'];

		$salvar = $PLANO->save();
		return response()->json(['success'=> $salvar]);
	}
	public function PesoTotalProjeto(Request $request)
	{
		if(isset($request['idPrograma'])){
			$result = DB::table('projeto_plano')
			->where('idPlano',$request['idPrograma'])
			->where('idProjeto','!=',$request['idProjeto'])
			->select(DB::raw('SUM("PesoPlano") as total'))
			->get();

			if($result->first()->total == null){
				return response()->json(0);
			}else{
				return response()->json($result->first()->total + (int)$request->pesoPrograma);
			}
		}
	}
	public function PesoPorProjeto(Request $request)
	{
		if(Auth::user()->tipo == 1){
			$CodCidade = $request->CodCidade;
		}elseif(Auth::user()->tipo == 2){
			$cidade = Cidades::find($request->CodCidade);
			if($cidade->uf != Auth::user()->cidade->uf){
				return response()->json(['error'=> 'sem permissão'], 500);
			}	
			$CodCidade = $cidade->codigo;	
		}else{
			$CodCidade = Auth::user()->CodCidade;	
		}
		if(empty($CodCidade) || empty($request->Ano)){
			return response()->json(['error'=> 'erro parametros invalidos'], 500);
		}
		if(!empty($request->TipoProjeto)){
			$result = Projeto::where('Ano', $request->Ano)
			->where('CodCidade', $CodCidade)
			->where('id','!=', $request->idProjeto)
			->where('TipoProjeto', mb_strtoupper($request->TipoProjeto))
			->get();
			if($result->count() == 0){
				return response()->json(0);
			}else{
				return response()->json($result->sum('PesoProjeto') + (int)$request->PesoProjeto);
			}
		}else{
			return response()->json(0);
		}
	}
	public function TotalPlano(Request $request)
	{
		if(Auth::user()->tipo == 1){
			$CodCidade = $request->CodCidade;
		}elseif(Auth::user()->tipo == 2){
			$cidade = Cidades::find($request->CodCidade);
			if($cidade->uf != Auth::user()->cidade->uf){
				return response()->json(['error'=> 'sem permissão'], 500);
			}	
			$CodCidade = $cidade->codigo;	
		}else{
			$CodCidade = Auth::user()->CodCidade;	
		}
		if(empty($CodCidade) || empty($request->Ano)){
			return response()->json(['error'=> 'erro parametros invalidos'], 500);
		}
		$result = Plano::where('Ano', $request->Ano)
		->where('CodCidade', $CodCidade)
		->when(!empty($request->idPlano), function ($q) use ($request) {
			return $q->where('id','!=',$request->idPlano);
		})
		->select(DB::raw('SUM("PesoPrograma"::INTEGER) as total'))->get();
		if($result->first()->total == null){
			return response()->json((int)$request->pesoPrograma);
		}else{
			return response()->json($result->first()->total + (int)$request->pesoPrograma);
		}

	}
	public function gravaProjeto(Request $request)
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
		if(empty($CodCidade) || empty($request->Ano)){
			return response()->json(['error'=> 'erro parametros invalidos']);
		}
		//dd($request->all());
		if (empty($request['idProjeto'])){
			$PROJETO = new Projeto();
		}else{
			$PROJETO = Projeto::where('Ano', $request->Ano)
			->where('CodCidade', $CodCidade)
			->where('id', $request['idProjeto'])
			->first();
			if(empty($PROJETO)){
				return response()->json(['error'=> 'erro ao editar']);
			}
		}
		$PROJETO->Ano = $request['Ano'];
		$PROJETO->CodCidade = $CodCidade;
		$PROJETO->user_id = Auth::id();

		$PROJETO->NomeProjeto = $request['NomeProjeto'];
		$PROJETO->ResponsavelProjeto = $request['ResponsavelProjeto'];
		$PROJETO->TipoProjeto = $request['TipoProjeto'];
		$PROJETO->UnidadeProjeto = $request['UnidadeProjeto'];
		$PROJETO->ObjetivoProjeto = $request['ObjetivoProjeto'];
		$PROJETO->PesoProjeto = $request['PesoProjeto'] ?? 0;
		$PROJETO->CustoProjeto = $request['CustoProjeto'] ?? 0;
		$PROJETO->DescricaoProjeto = $request['DescricaoProjeto'];
		$PROJETO->Janeiro = (double)(empty($request['Janeiro'])?0.0:(double)$request['Janeiro']);
		$PROJETO->Fevereiro = (double)(empty($request['Fevereiro'])?0.0:(double)$request['Fevereiro']);
		$PROJETO->Marco = (double)(empty($request['Marco'])?0.0:(double)$request['Marco']);
		$PROJETO->Abril = (double)(empty($request['Abril'])?0.0:(double)$request['Abril']);
		$PROJETO->Maio = (double)(empty($request['Maio'])?0.0:(double)$request['Maio']);
		$PROJETO->Junho = (double)(empty($request['Junho'])?0.0:(double)$request['Junho']);
		$PROJETO->Julho = (double)(empty($request['Julho'])?0.0:(double)$request['Julho']);
		$PROJETO->Agosto = (double)(empty($request['Agosto'])?0.0:(double)$request['Agosto']);
		$PROJETO->Setembro = (double)(empty($request['Setembro'])?0.0:(double)$request['Setembro']);
		$PROJETO->Outubro = (double)(empty($request['Outubro'])?0.0:(double)$request['Outubro']);
		$PROJETO->Novembro = (double)(empty($request['Novembro'])?0.0:(double)$request['Novembro']);
		$PROJETO->Dezembro = (double)(empty($request['Dezembro'])?0.0:(double)$request['Dezembro']);

		$PROJETO->save();
		\DB::table('projeto_plano')->where('idProjeto',$PROJETO->id)->delete();
		foreach ($request->ProjetoPrograma as $key => $programa) {
			$PROJETO->planos()->attach([$programa => ['PesoPlano'=> $request->PesoPrograma[$key] ]]);
		}
		
	}

}
