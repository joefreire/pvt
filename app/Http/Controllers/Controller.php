<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Models\Processo;
use Auth;
use DB;
use App\Exports\CadastroInicial;
use App\Models\ListaUnicaPendencias;
use App\Models\QuadroMultiplo;
use App\Models\Cidades;
use App\Models\Coordenadores;
use App\Models\Implantacao;
use App\Models\Instituicoes;
use App\Models\Qualidade;
use App\Models\Analise;
use App\Models\Monitoramento;
use App\Models\Acoes;
use App\Models\User;
use App\Models\Sim;
use App\Models\Sih;
use App\Models\Vitimas;
use App\Models\LinkagemSim;
use App\Models\LinkagemSih;
use Illuminate\Http\Request;


class Controller extends BaseController
{
	use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
	public function __construct() {

	}

	public function index()
	{
		return view('home');

	}
	public function relatorioGeral(Request $request)
	{
		return view('relatorioGeral');

	}
	public function relatorioIndicadores(Request $request)
	{
		return view('relatorioIndicadores');

	}
	public function relatorioIndicadoresData(Request $request)
	{
		if(Auth::user()->tipo == 1){
			$CodCidade = $request->CodCidade;
			$cidade = Cidades::find($request->CodCidade);
			if(empty($cidade)){
				return response()->json(['error'=> 'campos inválidos, você precisa informar a cidade']);
			}
			$CodCidade = $cidade->codigo;	
			$CidadeAcidente = $cidade->municipio;	
			$EstadoAcidente = $cidade->uf;	
		}elseif(Auth::user()->tipo == 2){

			$cidade = Cidades::find($request->CodCidade);
			if(empty($cidade)){
				return response()->json(['error'=> 'campos inválidos, você precisa informar a cidade'],401);
			}
			if($cidade->uf != Auth::user()->cidade->uf){
				return response()->json(['error'=> 'sem permissão'],401);
			}	
			$CodCidade = $cidade->codigo;	
			$CidadeAcidente = $cidade->municipio;	
			$EstadoAcidente = $cidade->uf;	
		}else{
			$CodCidade = Auth::user()->CodCidade;	
		}
		if(empty($CodCidade) || empty($request->Ano) )
		{
			return response()->json(['error'=> 'campos inválidos, você precisa informar a cidade'],401);
		}
		$cidade = Cidades::find($CodCidade);


		$sim = Sim::where('Ano',$request->Ano)	
		->where('CodCidade',$CodCidade)
		->where('Trimestre',$request->Trimestre)	
		->where(function ($q) {
			$q->whereRAW('(LEFT( "CAUSABAS", 1) = ? OR LEFT( "CAUSABAS_O", 1) = ?)',array('V','V'));
			
		});

		$simTransito = Sim::where('Ano',$request->Ano)	
		->where('CodCidade',$CodCidade)
		->where('Trimestre',$request->Trimestre)	
		->where(function ($q) {
			$q->whereRAW('(LEFT( "CAUSABAS", 1) = ? OR LEFT( "CAUSABAS_O", 1) = ?)',array('V','V'));
		});

		$simResto = Sim::where('Ano',$request->Ano)	
		->where('CodCidade',$CodCidade)
		->where('Trimestre',$request->Trimestre)	
		->whereDoesntHave('Linkagem')
		->where(function ($q) {
			$q->whereRAW('(LEFT( "CAUSABAS", 1) = ? OR LEFT( "CAUSABAS_O", 1) = ?)',array('V','V'))
			->WhereDoesntHave('Linkagem');
		});

		$vitimas = Vitimas::where('vitimas_quadro_multiplo.Ano',$request->Ano)	
		->where('vitimas_quadro_multiplo.CodCidade',$CodCidade)
		->where('vitimas_quadro_multiplo.Trimestre',$request->Trimestre)			
		->where(function($q) {
			$q->where('GravidadeLesao', 'FATAL')
			->orWhere('GravidadeLesao', 'FATAL LOCAL')
			->orWhere('GravidadeLesao', 'FATAL POSTERIOR');
		});

		$vitimasNaoLinkadas = $vitimas->whereDoesntHave('LinkagemSim');
		
		$vitimasLinkagem = Vitimas::where('vitimas_quadro_multiplo.Ano',$request->Ano)	
		->where('vitimas_quadro_multiplo.CodCidade',$CodCidade)
		->where('vitimas_quadro_multiplo.Trimestre',$request->Trimestre)			
		->where(function($q) {
			$q->where('GravidadeLesao', 'FATAL')
			->orWhere('GravidadeLesao', 'FATAL LOCAL')
			->orWhere('GravidadeLesao', 'FATAL POSTERIOR');
		})->whereHas('LinkagemSim', function ($queryEmp) {
			$queryEmp->where('deleted_at', null)->where('ParVerdadeiro', 1);
		});


		$fatores = DB::table('fatores_risco_quadro_multiplo')
		->leftJoin('vitimas_quadro_multiplo', 'fatores_risco_quadro_multiplo.idQuadroMultiplo', '=', 'vitimas_quadro_multiplo.idQuadroMultiplo')
		->where('vitimas_quadro_multiplo.Ano',$request->Ano)	
		->where('vitimas_quadro_multiplo.CodCidade',$CodCidade)
		->where('vitimas_quadro_multiplo.Trimestre',$request->Trimestre);

		$absoluto = $simResto->count() + $vitimasNaoLinkadas->count() +$vitimasLinkagem->count();		
		$absoluto_linkagem = $vitimasLinkagem->count();
		$residentes_linkagem = $vitimasLinkagem->where('MunicipioVitima', $cidade->municipio)->count();
		$residentes = $simResto->where('CODMUNRES', $CodCidade)->count() + $vitimasNaoLinkadas->where('MunicipioVitima', $cidade->municipio)->count() + $vitimasLinkagem->where('MunicipioVitima', $cidade->municipio)->count();

		$result = array(
			'obitos_ocorridos' => $absoluto,
			'obitos_absoluto_linkagem' => $absoluto_linkagem,
			'obitos_linkagem_ocorridos' => ($absoluto_linkagem - $residentes_linkagem),			
			'fator_alcool' => $fatores->where('fatores_risco_quadro_multiplo.Alcool','>',0)->count(),
			'fator_velocidade' => $fatores->where('fatores_risco_quadro_multiplo.Velocidade','>',0)->count(),
			'obitos_residentes' => $residentes,
			'obitos_linkagem_residentes' => $residentes_linkagem,
		);

		return response()->json($result);


	}
	public function situacao()
	{
		return view('cadastroInicial');

	}
	public function situacaoStatus(Request $request)
	{
		$municipio = $request['CodCidade'];

		$implantacao = Implantacao::where('CodCidade',$municipio)
		->where('Ano',$request['Ano'])
		->orderBy('id','desc')->first();
		$qualidade = Qualidade::where('CodCidade',$municipio)
		->where('Ano',$request['Ano'])
		->orderBy('id','desc')->first();
		$analise = Analise::where('CodCidade',$municipio)
		->where('Ano',$request['Ano'])
		->orderBy('id','desc')->first();
		$acoes = Analise::where('CodCidade',$municipio)
		->where('Ano',$request['Ano'])
		->orderBy('id','desc')->first();
		$monitoramento = Analise::where('CodCidade',$municipio)
		->where('Ano',$request['Ano'])
		->orderBy('id','desc')->first();

		if (!empty($monitoramento)) {
			echo 'Monitoramento';
			exit;
		} else if (!empty($acoes)) {
			echo 'Acoes';
			exit;
		} else if (!empty($analise)) {
			echo 'Analise';
			exit;
		} else if (!empty($qualidade)) {
			echo 'Qualidade';
			exit;
		} else if (!empty($implantacao)) {
			echo 'Implantacao';
			exit;
		} else {
			$coordenadores = Coordenadores::where('CodCidade',$municipio)
			->where('Ano',$request['Ano'])
			->orderBy('id','desc')->first();
			if (!empty($coordenadores)) {
				echo 'Coordenadores';
				exit;
			}
		}
		exit;

	}
	public function exportarCadastroInicial(Request $request)
	{
		return \Excel::download(new CadastroInicial($request->Ano, $request->CodCidade), 'SituacaoPVT.xlsx');

	}
	public function copiaDados(Request $request)
	{
		if(empty($request->Ano) || empty($request->CodCidade)){
			return 'SEM DADOS';
		}
		$municipio = $request['CodCidade'];
		$ano = $request['Ano']-1;

		$implantacao = Implantacao::where('CodCidade',$municipio)
		->where('Ano',$ano)
		->orderBy('id','desc')->first();
		$instituicoesImplantacao = Instituicoes::where('CodCidade',$municipio)
		->where('Ano',$ano)
		->where('TABELA', 'IMPLANTACAO')->get();

		$qualidade = Qualidade::where('CodCidade',$municipio)
		->where('Ano',$ano)
		->orderBy('id','desc')->first();
		$instituicoesQualidade = Instituicoes::where('CodCidade',$municipio)
		->where('Ano',$ano)
		->where('TABELA', 'QUALIDADE')->get();

		$analise = Analise::where('CodCidade',$municipio)
		->where('Ano',$ano)
		->orderBy('id','desc')->first();
		$acoes = Acoes::where('CodCidade',$municipio)
		->where('Ano',$ano)
		->orderBy('id','desc')->first();
		$monitoramento = Monitoramento::where('CodCidade',$municipio)
		->where('Ano',$ano)
		->orderBy('id','desc')->first();
		$coordenadores = Coordenadores::where('CodCidade',$municipio)
		->where('Ano',$ano)
		->orderBy('id','desc')->first();

		if(empty($coordenadores)){
			return "Sem dados para copiar";
		}else{
			$coordenadoresNovo = $coordenadores->replicate();
			$coordenadoresNovo->Ano = $request['Ano'];
			$coordenadoresNovo->save();
		}		
		if(!empty($monitoramento)){			
			$monitoramentoNovo = $monitoramento->replicate();
			$monitoramentoNovo->Ano = $request['Ano'];
			$monitoramentoNovo->save();
		}	
		if(!empty($acoes)){			
			$acoesNovo = $acoes->replicate();
			$acoesNovo->Ano = $request['Ano'];
			$acoesNovo->save();
		}
		if(!empty($analise)){			
			$analiseNovo = $analise->replicate();
			$analiseNovo->Ano = $request['Ano'];
			$analiseNovo->save();
		}
		if(!empty($qualidade)){			
			$qualidadeNovo = $qualidade->replicate();
			$qualidadeNovo->Ano = $request['Ano'];
			$qualidadeNovo->save();
			foreach($instituicoesQualidade as $instituicao){
				$instituicoesQualidadeNovo = $instituicao->replicate();
				$instituicoesQualidadeNovo->Ano = $request['Ano'];
				$instituicoesQualidadeNovo->idSalva = $qualidadeNovo->id;
				$instituicoesQualidadeNovo->save();
			}
		}
		if(!empty($implantacao)){			
			$implantacaoNovo = $implantacao->replicate();
			$implantacaoNovo->Ano = $request['Ano'];
			$implantacaoNovo->save();
			foreach($instituicoesImplantacao as $instituicaoImplantacao){
				$instituicaoImplantacaoNovo = $instituicaoImplantacao->replicate();
				$instituicaoImplantacaoNovo->Ano = $request['Ano'];
				$instituicaoImplantacaoNovo->idSalva = $implantacaoNovo->id;
				$instituicaoImplantacaoNovo->save();
			}
		}
		return "OK";

	}
	public function coordenadores(Request $request){
		$coordenador = new Coordenadores();    
		$coordenador->Ano = $request['Ano'];
		if($request['valores']['coordenaTEM'] == 'SIM'){
			$coordenador->coordenaTEM ='1';
		}else{
			$coordenador->coordenaTEM = '0';
		}
		$coordenador->Nome = $request['valores']['COORDENADOR1'];
		$coordenador->Instiuicao = $request['valores']['INSTITUICAO1'];
		$coordenador->Email = $request['valores']['EMAIL1'];
		$coordenador->Telefone =$request['valores']['TEL1'];
		$coordenador->Telefone1 = $request['valores']['TEL1-2'];

		$coordenador->Coordenador2 = $request['valores']['COORDENADOR2'];
		$coordenador->Instituicao2 = $request['valores']['INSTITUICAO2'];
		$coordenador->Email2 = $request['valores']['EMAIL2'];
		$coordenador->Telefone2 =$request['valores']['TEL2'];
		$coordenador->Telefone2_2 = $request['valores']['TEL2-2'];


		$coordenador->Coordenador3 = $request['valores']['COORDENADOR3'];
		$coordenador->Instituicao3 = $request['valores']['INSTITUICAO3'];
		$coordenador->Email3 = $request['valores']['EMAIL3'];
		$coordenador->Telefone3 =$request['valores']['TEL3'];
		$coordenador->Telefone3_2 = $request['valores']['TEL3-2'];

		$user = Auth::user();

		$coordenador->user_id = $user->id;
		if($user->tipo < 3){
			$coordenador->CodCidade = $request['CodCidade'];
		}else{
			$coordenador->CodCidade = $user->cidade->codigo;
		}

		$salvar = $coordenador->save();
		if($salvar > 0){
			echo "gravado";
		}else{
			echo "erro";
		}


	}
	public function implantacao(Request $request){
		$implantacao = new Implantacao();  
		$REGREUNIAOCI = filter_input(INPUT_POST, 'REGREUNIAOCI', FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES); 
		$PERIODIC = filter_input(INPUT_POST, 'PERIODIC', FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES); 
		$DATAREUNIAOCPVT= filter_input(INPUT_POST, 'DATAREUNIAOCPVT', FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES); 
		$REGREUNIAOCPVT = filter_input(INPUT_POST, 'REGREUNIAOCPVT', FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES);
		$COMISSAO = filter_input(INPUT_POST, 'COMISSAO', FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES);         
		if ($COMISSAO === 'NAO'){
			$DTDECRETO = '';
			$DECRETO = '';
			$NOMECOMISSAO = '';
			$UPDECRETO = '';
		}
		else{
			$DTDECRETO = filter_input(INPUT_POST, 'DTDECRETO', FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES);
			$DECRETO = filter_input(INPUT_POST, 'DECRETO', FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES); 
			$NOMECOMISSAO = filter_input(INPUT_POST, 'NOMECOMISSAO', FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES); 
			if (!empty($request->UPDECRETO)){
				$validator = \Validator::make($request->all(), 
					[
						'UPDECRETO' => 'required|file|max:10000', 
					]
				);
				if ($validator->fails()) {
					return response()->json('Tamanho do arquivo maior que 10MB.', 405);
				}
				$ext = $request->UPDECRETO->getClientOriginalExtension();
				$file = strtoupper(uniqid().'-'.$request->CodCidade.'-'.$request->Ano);
				$path = $request->file('UPDECRETO')->storeAs(
					'public',  'IMPLANTACAO/'.$file.'.'.strtoupper($ext)
				);
				$UPDECRETO = 'IMPLANTACAO/'.$file.'.'.strtoupper($ext);
			}else{
				$UPDECRETO = 'NÃO SE APLICA';
			}
			if (empty($DECRETO)){
				$DECRETO = '';
			}
			if (empty($DTDECRETO)){
				$DTDECRETO = '';
			}            
		}
		$implantacao->Ano = $request['Ano']; 
		$implantacao->COMISSAO = $COMISSAO; 
		$implantacao->DTDECRETO = $DTDECRETO;
		$implantacao->DECRETO = $DECRETO;
		$implantacao->NOMECOMISSAO = $NOMECOMISSAO;
		$implantacao->UPDECRETO = $UPDECRETO;
		$periodos = ['SEMANAL','QUINZENAL','MENSAL','BIMESTRAL','QUADRIMESTRAL'];
		$registro = ['ATA','RELATÓRIO'];
		if ($PERIODIC === 'Outra'){
			$PERIODIC= filter_input(INPUT_POST, 'outradata', FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES); 
		}else if(!in_array($PERIODIC, $periodos)){
			$PERIODIC= filter_input(INPUT_POST, 'outradata', FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES); 
		}
		$implantacao->PERIODIC = $PERIODIC;

		if (($REGREUNIAOCI === 'Outra') || (!in_array($REGREUNIAOCI, $registro))){
			$REGREUNIAOCI= filter_input(INPUT_POST, 'REGREUNIAOCIoutra', FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES); 
		}
		$implantacao->REGREUNIAOCI = $REGREUNIAOCI;
		$implantacao->DTREUNIAOCI = filter_input(INPUT_POST, 'DTREUNIAOCI', FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES);

		if (($DATAREUNIAOCPVT === 'Outra')|| (!in_array($DATAREUNIAOCPVT, $periodos))){
			$DATAREUNIAOCPVT= filter_input(INPUT_POST, 'DATAREUNIAOCPVToutra', FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES); 
		}
		$implantacao->DTREUNIAOCPVT = $DATAREUNIAOCPVT;
		if (($REGREUNIAOCPVT === 'Outra')|| (!in_array($REGREUNIAOCPVT, $registro))){
			$REGREUNIAOCPVT= filter_input(INPUT_POST, 'REGREUNIAOCPVToutra', FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES); 
		}
		$implantacao->REGREUNIAOCPVT = $REGREUNIAOCPVT;
		$user = Auth::user();

		$implantacao->user_id = $user->id;
		if($user->tipo < 3){
			$implantacao->CodCidade = $request['CodCidade'];
		}else{
			$implantacao->CodCidade = $user->cidade->codigo;
		}

		$implantacao->save();

		if(!empty($implantacao)){
			if ($COMISSAO === 'SIM'){
				$this->salva_instituicao($request['instituicao'], $request['setor'], $request['origem'], $implantacao->CodCidade, $implantacao->Ano, $implantacao->id,'implantacao');
			}
			echo "gravado";
		}else{
			echo "erro";
		}


	}
	public function salva_instituicao($instituicoes,$setor,$origem,$cidade, $ano, $idSalva, $tabela){

		$qtd=count($instituicoes);
		for ($i = 0; $i <= $qtd; $i++) {
			if (!empty($instituicoes[$i])&&!empty($setor[$i])&&!empty($origem[$i])){ 
				$instituicao = new Instituicoes(); 
				$instituicao->NOME = strtoupper($instituicoes[$i]);
				$instituicao->SETOR = strtoupper($setor[$i]);
				$instituicao->ORIGEM = strtoupper($origem[$i]);
				$instituicao->CodCidade = $cidade;
				$instituicao->Ano = $ano;
				$instituicao->user_id = Auth::user()->id;
				$instituicao->idSalva = $idSalva;
				$instituicao->TABELA = $tabela;
				$instituicao->save(); 
			}
		}
	}
	public function qualidade(Request $request){


		$QUALIDADE = new Qualidade();  
		$COMISSAOGD = filter_input(INPUT_POST, 'COMISSAOGD', FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES); 
		$COMISSAODOC = filter_input(INPUT_POST, 'COMISSAODOC', FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES); 
		$DTCOMISSAO = filter_input(INPUT_POST, 'DTCOMISSAO', FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES); 
		$NCOMISSAO = filter_input(INPUT_POST, 'NCOMISSAO', FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES);

		if ($COMISSAOGD === 'NAO'){
			$COMISSAOFORM = 'NÃO SE APLICA';
			$COMISSAODOC = '';
			$DTCOMISSAO = '';
			$NCOMISSAO = '';
			$UPDECRETOCOMISSAO = 'NÃO SE APLICA';
		}else{
			$COMISSAOFORM = filter_input(INPUT_POST, 'COMISSAOFORM', FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES);  
			if ($COMISSAOFORM === 'SIM'){
				$COMISSAODOC = filter_input(INPUT_POST, 'COMISSAODOC', FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES); 
				$DTCOMISSAO = filter_input(INPUT_POST, 'DTCOMISSAO', FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES); 
				$NCOMISSAO = filter_input(INPUT_POST, 'NCOMISSAO', FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES);
			}
			if (!empty($request->UPDECRETOCOMISSAO) ){
				$validator = \Validator::make($request->all(), 
					[
						'UPDECRETOCOMISSAO' => 'required|file|max:10000', 
					]
				);
				if ($validator->fails()) {
					return response()->json('Tamanho do arquivo maior que 10MB.', 405);
				}
				$ext = $request->UPDECRETOCOMISSAO->getClientOriginalExtension();
				$file = strtoupper(uniqid().'-'.$request->CodCidade.'-'.$request->Ano);
				$path = $request->file('UPDECRETOCOMISSAO')->storeAs(
					'public',  'QUALIDADE/'.$file.'.'.strtoupper($ext)
				);
				$UPDECRETOCOMISSAO = 'QUALIDADE/'.$file.'.'.$ext;
			}else{
				$UPDECRETOCOMISSAO = 'NÃO SE APLICA';
			}
			if (empty($DTCOMISSAO)){
				$DTCOMISSAO = '';
			}
			if (empty($NCOMISSAO)){
				$NCOMISSAO = '';
			}
			if (empty($COMISSAODOC)){
				$COMISSAODOC = '';
			}            
		}
		$QUALIDADE->Ano = $request['Ano']; 
		$QUALIDADE->UPDECRETOCOMISSAO = $UPDECRETOCOMISSAO; 
		$QUALIDADE->DTCOMISSAO = $DTCOMISSAO;
		$QUALIDADE->NCOMISSAO = substr($NCOMISSAO,0,254);
		$QUALIDADE->COMISSAODOC = $COMISSAODOC;
		$QUALIDADE->COMISSAOFORM = $COMISSAOFORM;
		$QUALIDADE->COMISSAOGD = $COMISSAOGD;


		$QUALIDADE->MAPEAMENTO = filter_input(INPUT_POST, 'MAPEAMENTO', FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES); 
		$QUALIDADE->LIMPEZA = filter_input(INPUT_POST, 'LIMPEZA', FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES); 
		$QUALIDADE->LISTAUNICA = filter_input(INPUT_POST, 'LISTAUNICA', FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES); 
		$QUALIDADE->FATORRISCO = filter_input(INPUT_POST, 'FATORRISCO', FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES); 
		$QUALIDADE->INDICADOROBITO = filter_input(INPUT_POST, 'INDICADOROBITO', FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES); 


		$LINKAGE = filter_input(INPUT_POST, 'LINKAGE', FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES); 
		$BASESOBITO = filter_input(INPUT_POST, 'BASESOBITO', FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES); 
		$BASEFERIDO = filter_input(INPUT_POST, 'BASEFERIDO', FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES); 
		$BASESAT = filter_input(INPUT_POST, 'BASESAT', FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES); 



		if ($BASEFERIDO == 'SIM' && $BASESOBITO == 'SIM' && $BASESAT == 'SIM'){
			$BASESUTILIZADASLINKAGE = '';
			if ($LINKAGE === 'SIM'){
				$PRILINKAGE = filter_input(INPUT_POST, 'PRILINKAGE', FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES). '/' .filter_input(INPUT_POST, 'PRIMEIROANOLINKAGE', FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES);
				$ULTLINKAGE = filter_input(INPUT_POST, 'ULTLINKAGE', FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES). '/' .filter_input(INPUT_POST, 'ULTLINKAGEANOLINKAGE', FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES);
				foreach ($request['bases_utilizadas'] as $key => $value) {
					$BASESUTILIZADASLINKAGE = $BASESUTILIZADASLINKAGE.",".$value;
					if ($value == 'OUTRAS'){
						$BASESUTILIZADASLINKAGE.=",".filter_input(INPUT_POST, 'bases_utilizadas_outras', FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES); 
					}
					if ($value == 'REGISTRO DE INTERNAÇÃO DE HOSPITAIS'){
						$BASESUTILIZADASLINKAGE.=",".filter_input(INPUT_POST, 'bases_utilizadas_hospital', FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES); 
					}
				}
				$COMOFOILISTAVITIMAS = 'NAO SE APLICA';
				$NAOLINKOBITO = 'NAO SE APLICA';
				$NAOLINKFER = 'NAO SE APLICA';
				$LINKAGE = $LINKAGE.$BASESUTILIZADASLINKAGE;
			}else{        
				$PRILINKAGE = 'NAO SE APLICA';
				$ULTLINKAGE = 'NAO SE APLICA';
				$COMOFOILISTAVITIMAS = filter_input(INPUT_POST, 'COMOFOILISTAVITIMAS', FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES);
				$NAOLINKOBITO=filter_input(INPUT_POST, 'NAOLINKOBITO', FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES);
				$NAOLINKFER=filter_input(INPUT_POST, 'NAOLINKFER', FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES);
			}
		}else{
			$LINKAGE = "NAO SE APLICA";
			$COMOFOILISTAVITIMAS = "NAO SE APLICA";
			$NAOLINKOBITO = "NAO SE APLICA";
			$PRILINKAGE = "NAO SE APLICA";
			$ULTLINKAGE = "NAO SE APLICA";
			$NAOLINKFER = "NAO SE APLICA";
		}

		if ($BASESAT === 'SIM'){
			foreach ($request['base_dados'] as $key => $value) {
				$BASESAT= $BASESAT.",".$value;
				if ($value == 'OUTRAS'){
					$BASESAT=$BASESAT.",".filter_input(INPUT_POST, 'base_dados_outras', FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES); 
				}
			}

		}

		if ($BASESOBITO === 'SIM'){
			foreach ($request['base_obitos'] as $key => $value) {
				$BASESOBITO = $BASESOBITO.",".$value;

			}

		}

		if ($BASEFERIDO === 'SIM'){
			foreach ($request['base_feridos'] as $key => $value) {
				$BASEFERIDO = $BASEFERIDO.','.$value;
				if ($value == 'REGISTRO DE INTERNAÇÃO DE HOSPITAIS'){
					$BASEFERIDO=$BASEFERIDO.",".filter_input(INPUT_POST, 'base_feridos_hospital', FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES); 
				}
			}

		}

		$QUALIDADE->BASEFERIDO = $BASEFERIDO;
		$QUALIDADE->BASESOBITO = $BASESOBITO;
		$QUALIDADE->BASESAT = $BASESAT;
		$QUALIDADE->COMOFOILISTAVITIMAS = $COMOFOILISTAVITIMAS;
		$QUALIDADE->NAOLINKOBITO = $NAOLINKOBITO;
		$QUALIDADE->ULTLINKAGE = $ULTLINKAGE;
		$QUALIDADE->PRILINKAGE = $PRILINKAGE;
		$QUALIDADE->NAOLINKFER = $NAOLINKFER;
		$QUALIDADE->LINKAGE = $LINKAGE;


		$user = Auth::user();

		$QUALIDADE->user_id = $user->id;
		if($user->tipo < 3){
			$QUALIDADE->CodCidade = $request['CodCidade'];
		}else{
			$QUALIDADE->CodCidade = $user->cidade->codigo;
		}

		$QUALIDADE->save();     
		if(!empty($QUALIDADE)){
			if ($COMISSAOFORM === 'SIM'){
				$this->salva_instituicao($request['Qualidade_instituicao'], $request['Qualidade_setor'], $request['Qualidade_origem'], $QUALIDADE->CodCidade,$QUALIDADE->Ano, $QUALIDADE->id,'qualidade');
			}
			echo "gravado";
		}else{
			echo "erro";
		}
	}
	public function buscaCoordenadores(Request $request){
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
		if(empty($CodCidade) || empty($request->Ano) )
		{
			return redirect()->back()->with('error','campos inválidos');
		}
		$cod =  Coordenadores::with('user')->where('Ano', $request->Ano)->where('CodCidade',$CodCidade)->orderBy('id','desc')->first();
		if (!empty($cod)){
			$cod['AlteradoPor'] = $cod['user']['nome'];    
			echo json_encode($cod);
		}else{
			echo 'vazio';
		}

	}
	public function buscaAcoes(Request $request){
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
		if(empty($CodCidade) || empty($request->Ano) )
		{
			return redirect()->back()->with('error','campos inválidos');
		}
		$cod =  Acoes::with('user')->where('Ano', $request->Ano)->where('CodCidade',$CodCidade)->orderBy('id','desc')->first();
		if (!empty($cod)){
			$cod['AlteradoPor'] = $cod['user']['nome'];    
			echo json_encode($cod);
		}else{
			echo 'vazio';
		}

	}
	public function buscaMonitoramento(Request $request){
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
		if(empty($CodCidade) || empty($request->Ano) )
		{
			return redirect()->back()->with('error','campos inválidos');
		}
		$cod =  Monitoramento::with('user')->where('Ano', $request->Ano)->where('CodCidade',$CodCidade)->orderBy('id','desc')->first();
		if (!empty($cod)){
			$cod['AlteradoPor'] = $cod['user']['nome'];    
			echo json_encode($cod);
		}else{
			echo 'vazio';
		}

	}
	public function buscaAnalise(Request $request){
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
		if(empty($CodCidade) || empty($request->Ano) )
		{
			return redirect()->back()->with('error','campos inválidos');
		}
		$cod =  Analise::with('user')->where('Ano', $request->Ano)->where('CodCidade',$CodCidade)->orderBy('id','desc')->first();
		if (!empty($cod)){
			$cod['AlteradoPor'] = $cod['user']['nome'];    
			echo json_encode($cod);
		}else{
			echo 'vazio';
		}

	}
	public function analise(Request $request){
		$ANALISE = new Analise();  
		$IDENTIFICACAORISCO = filter_input(INPUT_POST, 'IDENTIFICACAORISCO', FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES); 
		if ($IDENTIFICACAORISCO === 'SIM'){
			$ULTIMORISCO = filter_input(INPUT_POST, 'ULTIMOSEMESTRERISCO', FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES).'/'.filter_input(INPUT_POST, 'ULTIMORISCO', FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES); 
			$FATORESRISCOACIDENTES = filter_input(INPUT_POST, 'FATORESRISCOACIDENTES', FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES); 
			$CONDUTARISCOACIDENTES = filter_input(INPUT_POST, 'CONDUTARISCOACIDENTES', FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES);
			$FATORESGRAVIDADE = filter_input(INPUT_POST, 'FATORESGRAVIDADE', FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES);
			$FATORESFATAL = filter_input(INPUT_POST, 'FATORESFATAL', FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES);
			if ($FATORESRISCOACIDENTES === 'SIM'){
				$FATORESRISCOACIDENTES_SIM = filter_input(INPUT_POST, 'FATORESRISCOACIDENTES_SIM', FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES); 
			}
		}else {
			$ULTIMORISCO = ''; 
			$FATORESRISCOACIDENTES = ''; 
			$CONDUTARISCOACIDENTES = '';
			$FATORESGRAVIDADE = '';
			$FATORESFATAL = '';
		}
		$IDENTIFICACAORISCOCADA = filter_input(INPUT_POST, 'IDENTIFICACAORISCOCADA', FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES);
		if ($IDENTIFICACAORISCOCADA === 'SIM'){
			$ULTIMORISCOCADA = filter_input(INPUT_POST, 'ULTIMOSEMESTRERISCOCADA', FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES).'/'.filter_input(INPUT_POST, 'ULTIMORISCOCADA', FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES); 
			$FATORESRISCOACIDENTESCADA = filter_input(INPUT_POST, 'FATORESRISCOACIDENTESCADA', FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES);
			$CONDUTARISCOACIDENTESCADA = filter_input(INPUT_POST, 'CONDUTARISCOACIDENTESCADA', FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES);
			$FATORESGRAVIDADECADA = filter_input(INPUT_POST, 'FATORESGRAVIDADECADA', FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES);
			$FATORESFATALCADA = filter_input(INPUT_POST, 'FATORESFATALCADA', FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES);
		}else {
			$ULTIMORISCOCADA = ''; 
			$FATORESRISCOACIDENTESCADA = '';
			$CONDUTARISCOACIDENTESCADA = '';
			$FATORESGRAVIDADECADA = '';
			$FATORESFATALCADA= '';
		}
		$CONSTRUCAOQUADROMULTIPLO = filter_input(INPUT_POST, 'CONSTRUCAOQUADROMULTIPLO', FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES);
		if ($CONSTRUCAOQUADROMULTIPLO === 'SIM'){
			$ULTIMOCONSTRUCAOQUADROMULTIPLO = filter_input(INPUT_POST, 'ULTIMOSEMESTRECONSTRUCAOQUADROMULTIPLO', FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES).'/'.filter_input(INPUT_POST, 'ULTIMOCONSTRUCAOQUADROMULTIPLO', FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES); 
		}else {
			$ULTIMOCONSTRUCAOQUADROMULTIPLO = ''; 

		}
		$FATORESCHAVE = filter_input(INPUT_POST, 'FATORESCHAVE', FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES);
		if ($FATORESCHAVE === 'SIM'){
			$PRINCIPAISFATORESCHAVE = '';
			$ULTIMOFATORESCHAVE = filter_input(INPUT_POST, 'ULTIMOSEMESTREFATORESCHAVE', FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES).'/'.filter_input(INPUT_POST, 'ULTIMOFATORESCHAVE', FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES); 
			foreach ($request['PRINCIPAISFATORESCHAVE'] as $key => $value) {
				if ($key == 0){
					$PRINCIPAISFATORESCHAVE = $value; 
				}else{
					$PRINCIPAISFATORESCHAVE = $PRINCIPAISFATORESCHAVE.",".$value;  
				}
				if ($value == 'OUTRO'){
					$PRINCIPAISFATORESCHAVE.=",".filter_input(INPUT_POST, 'PRINCIPAISFATORESCHAVE_OUTRO', FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES); 
				}

			}
		}else {
			$ULTIMOCONSTRUCAOQUADROMULTIPLO = ''; 
			$PRINCIPAISFATORESCHAVE = ''; 
			$ULTIMOFATORESCHAVE = '';

		}
		$GRUPOSVITIMAS = filter_input(INPUT_POST, 'GRUPOSVITIMAS', FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES);
		if ($GRUPOSVITIMAS === 'SIM'){
			$PRINCIPAISGRUPOSVITIMAS = '';
			$ULTIMOGRUPOSVITIMAS = filter_input(INPUT_POST, 'ULTIMOSEMESTREGRUPOSVITIMAS', FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES).'/'.filter_input(INPUT_POST, 'ULTIMOGRUPOSVITIMAS', FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES); 
			foreach ($request['PRINCIPAISGRUPOSVITIMAS'] as $key => $value) {
				if ($key == 0){
					$PRINCIPAISGRUPOSVITIMAS = $value; 
				}else{
					$PRINCIPAISGRUPOSVITIMAS = $PRINCIPAISGRUPOSVITIMAS.",".$value;  
				}
				if ($value == 'OUTRO'){
					$PRINCIPAISGRUPOSVITIMAS.=",".filter_input(INPUT_POST, 'PRINCIPAISGRUPOSVITIMAS_OUTRO', FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES); 
				}
			}
		}else {
			$ULTIMOGRUPOSVITIMAS = ''; 
			$PRINCIPAISGRUPOSVITIMAS = ''; 

		}
		$PROGRAMAPRIORITARIOS= filter_input(INPUT_POST, 'PROGRAMAPRIORITARIOS', FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES);
		if ($PROGRAMAPRIORITARIOS === 'SIM'){
			$ULTIMOPROGRAMAPRIORITARIOS = filter_input(INPUT_POST, 'ULTIMOPROGRAMAPRIORITARIOS', FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES);

		}else {
			$ULTIMOPROGRAMAPRIORITARIOS = ''; 

		}


		$ANALISE->Ano = $request['Ano']; 
		$ANALISE->IDENTIFICACAORISCO = $IDENTIFICACAORISCO; 
		$ANALISE->IDENTIFICACAORISCO = $IDENTIFICACAORISCO;
		$ANALISE->FATORESRISCOACIDENTES = $FATORESRISCOACIDENTES;
		$ANALISE->FATORESRISCOACIDENTES_SIM = $FATORESRISCOACIDENTES_SIM ?? '';
		$ANALISE->AMOSTRA = $request->AMOSTRA;
		$ANALISE->CONDUTARISCOACIDENTES = $CONDUTARISCOACIDENTES;
		$ANALISE->FATORESGRAVIDADE = $FATORESGRAVIDADE;
		$ANALISE->FATORESFATAL = $FATORESFATAL;
		$ANALISE->IDENTIFICACAORISCOCADA = $IDENTIFICACAORISCOCADA;
		$ANALISE->ULTIMORISCOCADA = $ULTIMORISCOCADA;
		$ANALISE->ULTIMORISCO = $ULTIMORISCO;
		$ANALISE->FATORESRISCOACIDENTESCADA = $FATORESRISCOACIDENTESCADA;
		$ANALISE->CONDUTARISCOACIDENTESCADA = $CONDUTARISCOACIDENTESCADA;
		$ANALISE->PRINCIPAISFATORESCHAVE = $PRINCIPAISFATORESCHAVE;
		$ANALISE->PRINCIPAISGRUPOSVITIMAS = $PRINCIPAISGRUPOSVITIMAS;
		$ANALISE->FATORESGRAVIDADECADA = $FATORESGRAVIDADECADA;
		$ANALISE->FATORESFATALCADA = $FATORESFATALCADA;
		$ANALISE->FATORESCHAVE = $FATORESCHAVE;
		$ANALISE->ULTIMOFATORESCHAVE = $ULTIMOFATORESCHAVE;
		$ANALISE->GRUPOSVITIMAS = $GRUPOSVITIMAS;
		$ANALISE->ULTIMOGRUPOSVITIMAS = $ULTIMOGRUPOSVITIMAS;
		$ANALISE->CONSTRUCAOQUADROMULTIPLO = $CONSTRUCAOQUADROMULTIPLO;
		$ANALISE->ULTIMOCONSTRUCAOQUADROMULTIPLO = $ULTIMOCONSTRUCAOQUADROMULTIPLO;
		$ANALISE->PROGRAMAPRIORITARIOS = $PROGRAMAPRIORITARIOS;
		$ANALISE->ULTIMOPROGRAMAPRIORITARIOS = $ULTIMOPROGRAMAPRIORITARIOS;


		$user = Auth::user();

		$ANALISE->user_id = $user->id;
		if($user->tipo < 3){
			$ANALISE->CodCidade = $request['CodCidade'];
		}else{
			$ANALISE->CodCidade = $user->cidade->codigo;
		}



		$ANALISE->save();     
		if(!empty($ANALISE)){
			echo "gravado";
		}else{
			echo "erro";
		}

	}
	public function acoes(Request $request){
		$ACOES = new Acoes();
		$ACOESINTEGRADAS = filter_input(INPUT_POST, 'ACOESINTEGRADAS', FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES);
		if ($ACOESINTEGRADAS === 'SIM') {
			$ULTIMOACOESINTEGRADAS = filter_input(INPUT_POST, 'ULTIMOACOESINTEGRADAS', FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES);
			$PRINCIPAISACOESINTEGRADAS = filter_input(INPUT_POST, 'PRINCIPAISACOESINTEGRADAS', FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES);
			foreach ($request['PRINCIPAISACOESINTEGRADAS'] as $key => $value) {
				if ($key == 0) {
					$PRINCIPAISACOESINTEGRADAS = $value;
				} else {
					$PRINCIPAISACOESINTEGRADAS = $PRINCIPAISACOESINTEGRADAS . "," . $value;
				}
				if ($value == 'OUTRO') {
					$PRINCIPAISACOESINTEGRADAS.="," . filter_input(INPUT_POST, 'PRINCIPAISACOESINTEGRADAS_OUTRO', FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES);
				}
			}
		} else {
			$ULTIMOACOESINTEGRADAS = '';
			$PRINCIPAISACOESINTEGRADAS = '';
		}


		$ACOES->Ano = $request['Ano'];
		$ACOES->ACOESINTEGRADAS = $ACOESINTEGRADAS;
		$ACOES->ULTIMOACOESINTEGRADAS = $ULTIMOACOESINTEGRADAS;
		$ACOES->PRINCIPAISACOESINTEGRADAS = $PRINCIPAISACOESINTEGRADAS;
		$user = Auth::user();

		$ACOES->user_id = $user->id;
		if($user->tipo < 3){
			$ACOES->CodCidade = $request['CodCidade'];
		}else{
			$ACOES->CodCidade = $user->cidade->codigo;
		}



		$ACOES->save();     
		if(!empty($ACOES)){
			echo "gravado";
		}else{
			echo "erro";
		}

	}
	public function monitoramento(Request $request){
		$MONITORAMENTO = new Monitoramento();  
		$BEBERDIRIGIR = filter_input(INPUT_POST, 'BEBERDIRIGIR', FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES); 
		if ($BEBERDIRIGIR === 'SIM'){
			$ULTIMOBEBERDIRIGIR = filter_input(INPUT_POST, 'ULTIMOBEBERDIRIGIR', FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES); 
			$QUADROBEBERDIRIGIR = filter_input(INPUT_POST, 'QUADROBEBERDIRIGIR', FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES); 
		}else {
			$ULTIMOBEBERDIRIGIR = ''; 
			$QUADROBEBERDIRIGIR = ''; 
		}
		$VELOCIDADE = filter_input(INPUT_POST, 'VELOCIDADE', FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES); 
		if ($VELOCIDADE === 'SIM'){
			$ULTIMOVELOCIDADE = filter_input(INPUT_POST, 'ULTIMOVELOCIDADE', FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES); 
			$QUADROVELOCIDADE = filter_input(INPUT_POST, 'QUADROVELOCIDADE', FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES); 
		}else {
			$ULTIMOVELOCIDADE = ''; 
			$QUADROVELOCIDADE = ''; 
		}
		$DEFINIDOMUNICIPIO = filter_input(INPUT_POST, 'DEFINIDOMUNICIPIO', FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES); 
		if ($DEFINIDOMUNICIPIO === 'SIM'){
			$ULTIMODEFINIDOMUNICIPIO = filter_input(INPUT_POST, 'ULTIMODEFINIDOMUNICIPIO', FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES); 
			$QUADRODEFINIDOMUNICIPIO = filter_input(INPUT_POST, 'QUADRODEFINIDOMUNICIPIO', FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES); 
		}else {
			$ULTIMODEFINIDOMUNICIPIO = ''; 
			$QUADRODEFINIDOMUNICIPIO = ''; 
		}
		$QUADROGRUPOVITIMAS = filter_input(INPUT_POST, 'QUADROGRUPOVITIMAS', FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES); 
		if ($QUADROGRUPOVITIMAS === 'SIM'){
			$ULTIMOQUADROGRUPOVITIMAS = filter_input(INPUT_POST, 'ULTIMOQUADROGRUPOVITIMAS', FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES); 
			$PRINCIPAISFATORESCHAVE = '';
			foreach ($request['QUADROGRUPOVITIMAS_QUAIS'] as $key => $value) {
				if ($key == 0){
					$PRINCIPAISFATORESCHAVE = $value; 
				}else{
					$PRINCIPAISFATORESCHAVE = $PRINCIPAISFATORESCHAVE.",".$value;  
				}

			}

		}else {
			$ULTIMOQUADROGRUPOVITIMAS = ''; 
			$PRINCIPAISFATORESCHAVE = ''; 
		}


		$MONITORAMENTO->Ano = $request['Ano'];
		$MONITORAMENTO->BEBERDIRIGIR = $BEBERDIRIGIR; 
		$MONITORAMENTO->ULTIMOBEBERDIRIGIR = $ULTIMOBEBERDIRIGIR; 
		$MONITORAMENTO->QUADROBEBERDIRIGIR = $QUADROBEBERDIRIGIR; 
		$MONITORAMENTO->VELOCIDADE = $VELOCIDADE; 
		$MONITORAMENTO->ULTIMOVELOCIDADE = $ULTIMOVELOCIDADE; 
		$MONITORAMENTO->QUADROVELOCIDADE = $QUADROVELOCIDADE; 
		$MONITORAMENTO->DEFINIDOMUNICIPIO = $DEFINIDOMUNICIPIO; 
		$MONITORAMENTO->ULTIMODEFINIDOMUNICIPIO = $ULTIMODEFINIDOMUNICIPIO; 
		$MONITORAMENTO->QUADRODEFINIDOMUNICIPIO = $QUADRODEFINIDOMUNICIPIO; 

		$MONITORAMENTO->QUADROGRUPOVITIMAS_QUAIS = $PRINCIPAISFATORESCHAVE; 
		$MONITORAMENTO->QUADROGRUPOVITIMAS = $QUADROGRUPOVITIMAS; 
		$MONITORAMENTO->ULTIMOQUADROGRUPOVITIMAS = $ULTIMOQUADROGRUPOVITIMAS; 

		$user = Auth::user();

		$MONITORAMENTO->user_id = $user->id;
		if($user->tipo < 3){
			$MONITORAMENTO->CodCidade = $request['CodCidade'];
		}else{
			$MONITORAMENTO->CodCidade = $user->cidade->codigo;
		}



		$MONITORAMENTO->save();     
		if(!empty($MONITORAMENTO)){
			echo "gravado";
		}else{
			echo "erro";
		}

	}    

	public function buscaImplantacao(Request $request){
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
		if(empty($CodCidade) || empty($request->Ano) )
		{
			return redirect()->back()->with('error','campos inválidos');
		}
		$implantacao =  Implantacao::with('user','Instituicoes')
		->where('Ano', $request->Ano)
		->where('CodCidade',$CodCidade)
		->orderBy('id','desc')
		->first();

		if (!empty($implantacao->Instituicoes)){
			$implantacao['AlteradoPor'] = $implantacao['user']['nome'];   
			echo json_encode($implantacao);
		}else{
			echo 'vazio';
		}

	}
	public function buscaQualidade(Request $request){
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
		if(empty($CodCidade) || empty($request->Ano) )
		{
			return redirect()->back()->with('error','campos inválidos');
		}
		$qualidade =  Qualidade::with('user','Instituicoes')->where('Ano', $request->Ano)->where('CodCidade',$CodCidade)->orderBy('id','desc')->first();

		
		if (!empty($qualidade)){
			$qualidade['AlteradoPor'] = $qualidade['user']['nome'];   
			echo json_encode($qualidade);
		}else{
			echo 'vazio';
		}

	}
	public function relatorioGeralData(Request $request)
	{
		if(Auth::user()->tipo == 1){
			$CodCidade = $request->CodCidade;
			$cidade = Cidades::find($request->CodCidade);
			if(empty($cidade)){
				return response()->json(['error'=> 'campos inválidos, você precisa informar a cidade']);
			}
			$CodCidade = $cidade->codigo;	
			$CidadeAcidente = $cidade->municipio;	
			$EstadoAcidente = $cidade->uf;	
		}elseif(Auth::user()->tipo == 2){

			$cidade = Cidades::find($request->CodCidade);
			if(empty($cidade)){
				return response()->json(['error'=> 'campos inválidos, você precisa informar a cidade'],401);
			}
			if($cidade->uf != Auth::user()->cidade->uf){
				return response()->json(['error'=> 'sem permissão'],401);
			}	
			$CodCidade = $cidade->codigo;	
			$CidadeAcidente = $cidade->municipio;	
			$EstadoAcidente = $cidade->uf;	
		}else{
			$CodCidade = Auth::user()->CodCidade;	
		}
		if(empty($CodCidade) || empty($request->Ano) )
		{
			return response()->json(['error'=> 'campos inválidos, você precisa informar a cidade'],401);
		}
		$request->CodCidade = $CodCidade;
		return $this->BuscaSelecao($request);


	}
	public function BuscaSelecao(Request $request) {

		if ($request['base'] == "Acidentes") {
			$base = QuadroMultiplo::where('Ano',$request->Ano)	
			->where('CodCidade',$request->CodCidade)
			->where('Trimestre',$request->Trimestre)
			->get();
			if ($request['linhas'] == "DiaSemana") {
				$linhas = $base->groupBy('DiaSemana');
			}elseif ($request['linhas'] == "Horario") {
				$linhas = $base->groupBy('HoraAcidente');
			}elseif ($request['linhas'] == "QtdVitimas") {
				$linhas = $base->groupBy('QtdVitimas');
			}elseif ($request['linhas'] == "FonteDados") {	
				$linhas = $base->groupBy('FonteDados');	
			}elseif ($request['linhas'] == "TipoAcidente") {
				$linhas = $base->groupBy('TipoAcidente');	
			}
			//colunas
			if ($request['colunas'] == "Frequencia") {
				$result = [];
				foreach ($linhas as $key => $value) {					
					$result[$key] = $value->count();
				}
			}
			if ($request['colunas'] == "TipoAcidente") {
				$result = [];
				$colunas = $base->groupBy('TipoAcidente');
				$colunas = array_keys($colunas->toArray());
				foreach ($linhas as $key => $value) {			
					foreach ($colunas as $coluna) {
						$result[$key][$coluna] = $value->where('TipoAcidente',$coluna)->count();
					}				
				}
			}
			if ($request['colunas'] == "FrequenciaProtecaoInadequada") {
				$base = $base->load('FatoresRisco');
				$result = [];
				$base = $base->filter(function ($value, $key) {
					return !empty($value->FatoresRisco);
				});
				foreach ($linhas as $key => $value) {		
					$result[$key]['CintoSeguranca'] = $base->where($request->linhas,$key)
					->where('FatoresRisco.CintoSeguranca')->count();
					$result[$key]['EquipamentoProtecao'] = $base->where($request->linhas,$key)->where('FatoresRisco.EquipamentoProtecao')->count();
					$result[$key]['GerenciamentoTrauma'] = $base->where($request->linhas,$key)->where('FatoresRisco.GerenciamentoTrauma')->count();
					$result[$key]['ObjetosLateraisVia'] = $base->where($request->linhas,$key)->where('FatoresRisco.ObjetosLateraisVia')->count();
					$result[$key]['Capacete'] = $base->where($request->linhas,$key)->where('FatoresRisco.Capacete')->count();
					$result[$key]['OutraProtecao'] = $base->where($request->linhas,$key)->where('FatoresRisco.outra_protecao')->count();
				}
			}
			if ($request['colunas'] == "ProtecaoInadequada") {
				$base = $base->load('FatoresRisco');
				$result = [];
				$base = $base->filter(function ($value, $key) {
					return !empty($value->FatoresRisco);
				});
				foreach ($linhas as $key => $value) {		
					$result[$key]['CintoSeguranca'] = $base->where($request->linhas,$key)->sum('FatoresRisco.CintoSeguranca');
					$result[$key]['EquipamentoProtecao'] = $base->where($request->linhas,$key)->sum('FatoresRisco.EquipamentoProtecao');
					$result[$key]['GerenciamentoTrauma'] = $base->where($request->linhas,$key)->sum('FatoresRisco.GerenciamentoTrauma');
					$result[$key]['ObjetosLateraisVia'] = $base->where($request->linhas,$key)->sum('FatoresRisco.ObjetosLateraisVia');
					$result[$key]['Capacete'] = $base->where($request->linhas,$key)->sum('FatoresRisco.Capacete');
					$result[$key]['OutraProtecao'] = $base->where($request->linhas,$key)->sum('FatoresRisco.outra_protecao');
				}
			}
			if ($request['colunas'] == "FatorRisco") {
				$base = $base->load('FatoresRisco');
				$result = [];
				$base = $base->filter(function ($value, $key) {
					return !empty($value->FatoresRisco);
				});
				foreach ($linhas as $key => $value) {		
					$result[$key]['Velocidade'] = $base->where($request->linhas,$key)->sum('FatoresRisco.Velocidade');
					$result[$key]['Alcool'] = $base->where($request->linhas,$key)->sum('FatoresRisco.Alcool');
					$result[$key]['Infraestrutura'] = $base->where($request->linhas,$key)->sum('FatoresRisco.Infraestrutura');
					$result[$key]['Drogas'] = $base->where($request->linhas,$key)->sum('FatoresRisco.Drogas');
					$result[$key]['Fadiga'] = $base->where($request->linhas,$key)->sum('FatoresRisco.Fadiga');
					$result[$key]['Distacao'] = $base->where($request->linhas,$key)->sum('FatoresRisco.Distacao');
					$result[$key]['Visibilidade'] = $base->where($request->linhas,$key)->sum('FatoresRisco.Visibilidade');
				}
			}
			if ($request['colunas'] == "FrequenciaFatorRisco") {
				$base = $base->load('FatoresRisco');
				$result = [];
				$base = $base->filter(function ($value, $key) {
					return !empty($value->FatoresRisco);
				});
				foreach ($linhas as $key => $value) {		
					$result[$key]['Velocidade'] = $base->where($request->linhas,$key)
					->where('FatoresRisco.Velocidade','>',0)
					->count();
					$result[$key]['Alcool'] = $base->where($request->linhas,$key)->where('FatoresRisco.Alcool','>',0)
					->count();
					$result[$key]['Infraestrutura'] = $base->where($request->linhas,$key)->where('quadro_multiplo.FatoresRisco.Infraestrutura','>',0)
					->count();
					$result[$key]['Drogas'] = $base->where($request->linhas,$key)->where('FatoresRisco.Drogas','>',0)
					->count();
					$result[$key]['Fadiga'] = $base->where($request->linhas,$key)->where('FatoresRisco.Fadiga','>',0)
					->count();
					$result[$key]['Distacao'] = $base->where($request->linhas,$key)->where('FatoresRisco.Distacao','>',0)
					->count();
					$result[$key]['Visibilidade'] = $base->where($request->linhas,$key)->where('FatoresRisco.Visibilidade','>',0)
					->count();
				}
			}
			if ($request['colunas'] == "FrequenciaCondutaRisco") {
				$base = $base->load('FatoresRisco');
				$result = [];
				$base = $base->filter(function ($value, $key) {
					return !empty($value->FatoresRisco);
				});
				foreach ($linhas as $key => $value) {	
					$result[$key]['AvancarSinal'] = $base->where($request->linhas,$key)
					->where('FatoresRisco.AvancarSinal','>',0)
					->count();
					$result[$key]['CondutorSemHabilitacao'] = $base->where($request->linhas,$key)
					->where('FatoresRisco.CondutorSemHabilitacao','>',0)
					->count();
					$result[$key]['LocalProibido'] = $base->where($request->linhas,$key)
					->where('FatoresRisco.LocalProibido','>',0)
					->count();
					$result[$key]['LocalImproprio'] = $base->where($request->linhas,$key)
					->where('FatoresRisco.LocalImproprio','>',0)
					->count();
					$result[$key]['MudancaFaixa'] = $base->where($request->linhas,$key)
					->where('FatoresRisco.MudancaFaixa','>',0)
					->count();
					$result[$key]['DistanciaMinima'] = $base->where($request->linhas,$key)
					->where('FatoresRisco.DistanciaMinima','>',0)
					->count();
					$result[$key]['Preferencia'] = $base->where($request->linhas,$key)
					->where('FatoresRisco.Preferencia','>',0)
					->count();
					$result[$key]['PreferenciaPedestre'] = $base->where($request->linhas,$key)
					->where('FatoresRisco.PreferenciaPedestre','>',0)
					->count();
					$result[$key]['ImprudenciaPedestre'] = $base->where($request->linhas,$key)
					->where('FatoresRisco.ImprudenciaPedestre','>',0)
					->count();	
				}
			}
			if ($request['colunas'] == "CondutaRisco") {
				$base = $base->load('FatoresRisco');
				$result = [];
				$base = $base->filter(function ($value, $key) {
					return !empty($value->FatoresRisco);
				});
				foreach ($linhas as $key => $value) {		
					$result[$key]['AvancarSinal'] = $base->where($request->linhas,$key)->sum('FatoresRisco.AvancarSinal');
					$result[$key]['CondutorSemHabilitacao'] = $base->where($request->linhas,$key)->sum('FatoresRisco.CondutorSemHabilitacao');
					$result[$key]['LocalProibido'] = $base->where($request->linhas,$key)->sum('FatoresRisco.LocalProibido');
					$result[$key]['LocalImproprio'] = $base->where($request->linhas,$key)->sum('FatoresRisco.LocalImproprio');
					$result[$key]['MudancaFaixa'] = $base->where($request->linhas,$key)->sum('FatoresRisco.MudancaFaixa');
					$result[$key]['DistanciaMinima'] = $base->where($request->linhas,$key)->sum('FatoresRisco.DistanciaMinima');
					$result[$key]['Preferencia'] = $base->where($request->linhas,$key)->sum('FatoresRisco.Preferencia');
					$result[$key]['PreferenciaPedestre'] = $base->where($request->linhas,$key)->sum('FatoresRisco.PreferenciaPedestre');
					$result[$key]['ImprudenciaPedestre'] = $base->where($request->linhas,$key)->sum('FatoresRisco.ImprudenciaPedestre');
				}
			}

			if ($request['colunas'] == "UsuarioContributivo") {
				$base = $base->load('FatoresRisco');
				$result = [];
				$base = $base->filter(function ($value, $key) {
					return !empty($value->FatoresRisco);
				});
				foreach ($linhas as $key => $value) {		
					$result[$key]['CONDUTOR OU PASSAGEIRO DE VEÍCULO LEVE'] = $base->where($request->linhas,$key)
					->filter(function ($value, $key) {
						return $value->FatoresRisco->UsuarioContributivo_Velocidade == 'CONDUTOR OU PASSAGEIRO DE VEÍCULO LEVE' || 
						$value->FatoresRisco->UsuarioContributivo_Alcool == 'CONDUTOR OU PASSAGEIRO DE VEÍCULO LEVE' || 
						$value->FatoresRisco->UsuarioContributivo_Veiculo == 'CONDUTOR OU PASSAGEIRO DE VEÍCULO LEVE' || 
						$value->FatoresRisco->UsuarioContributivo_Fadiga == 'CONDUTOR OU PASSAGEIRO DE VEÍCULO LEVE' ||
						$value->FatoresRisco->UsuarioContributivo_Drogas == 'CONDUTOR OU PASSAGEIRO DE VEÍCULO LEVE' ||
						$value->FatoresRisco->UsuarioContributivo_Distacao == 'CONDUTOR OU PASSAGEIRO DE VEÍCULO LEVE' ||
						$value->FatoresRisco->UsuarioContributivo_AvancarSinal == 'CONDUTOR OU PASSAGEIRO DE VEÍCULO LEVE' ||
						$value->FatoresRisco->UsuarioContributivo_CondutorSemHabilitacao == 'CONDUTOR OU PASSAGEIRO DE VEÍCULO LEVE' ||
						$value->FatoresRisco->UsuarioContributivo_LocalProibido == 'CONDUTOR OU PASSAGEIRO DE VEÍCULO LEVE' ||
						$value->FatoresRisco->UsuarioContributivo_LocalImproprio == 'CONDUTOR OU PASSAGEIRO DE VEÍCULO LEVE' ||
						$value->FatoresRisco->UsuarioContributivo_MudancaFaixa == 'CONDUTOR OU PASSAGEIRO DE VEÍCULO LEVE' ||
						$value->FatoresRisco->UsuarioContributivo_DistanciaMinima == 'CONDUTOR OU PASSAGEIRO DE VEÍCULO LEVE' ||
						$value->FatoresRisco->UsuarioContributivo_Preferencia == 'CONDUTOR OU PASSAGEIRO DE VEÍCULO LEVE' ||
						$value->FatoresRisco->UsuarioContributivo_PreferenciaPedestre == 'CONDUTOR OU PASSAGEIRO DE VEÍCULO LEVE' ||
						$value->FatoresRisco->UsuarioContributivo_ImprudenciaPedestre == 'CONDUTOR OU PASSAGEIRO DE VEÍCULO LEVE' ||
						$value->FatoresRisco->UsuarioContributivo_Capacete == 'CONDUTOR OU PASSAGEIRO DE VEÍCULO LEVE' ||
						$value->FatoresRisco->UsuarioContributivo_CintoSeguranca == 'CONDUTOR OU PASSAGEIRO DE VEÍCULO LEVE';
					})
					->count();	
					$result[$key]['CONDUTOR OU PASSAGEIRO DE VEÍCULO PESADO'] = $base->where($request->linhas,$key)
					->filter(function ($value, $key) {
						return $value->FatoresRisco->UsuarioContributivo_Velocidade == 'CONDUTOR OU PASSAGEIRO DE VEÍCULO PESADO' || 
						$value->FatoresRisco->UsuarioContributivo_Alcool == 'CONDUTOR OU PASSAGEIRO DE VEÍCULO PESADO' || 
						$value->FatoresRisco->UsuarioContributivo_Veiculo == 'CONDUTOR OU PASSAGEIRO DE VEÍCULO PESADO' || 
						$value->FatoresRisco->UsuarioContributivo_Fadiga == 'CONDUTOR OU PASSAGEIRO DE VEÍCULO PESADO' ||
						$value->FatoresRisco->UsuarioContributivo_Drogas == 'CONDUTOR OU PASSAGEIRO DE VEÍCULO PESADO' ||
						$value->FatoresRisco->UsuarioContributivo_Distacao == 'CONDUTOR OU PASSAGEIRO DE VEÍCULO PESADO' ||
						$value->FatoresRisco->UsuarioContributivo_AvancarSinal == 'CONDUTOR OU PASSAGEIRO DE VEÍCULO PESADO' ||
						$value->FatoresRisco->UsuarioContributivo_CondutorSemHabilitacao == 'CONDUTOR OU PASSAGEIRO DE VEÍCULO PESADO' ||
						$value->FatoresRisco->UsuarioContributivo_LocalProibido == 'CONDUTOR OU PASSAGEIRO DE VEÍCULO PESADO' ||
						$value->FatoresRisco->UsuarioContributivo_LocalImproprio == 'CONDUTOR OU PASSAGEIRO DE VEÍCULO PESADO' ||
						$value->FatoresRisco->UsuarioContributivo_MudancaFaixa == 'CONDUTOR OU PASSAGEIRO DE VEÍCULO PESADO' ||
						$value->FatoresRisco->UsuarioContributivo_DistanciaMinima == 'CONDUTOR OU PASSAGEIRO DE VEÍCULO PESADO' ||
						$value->FatoresRisco->UsuarioContributivo_Preferencia == 'CONDUTOR OU PASSAGEIRO DE VEÍCULO PESADO' ||
						$value->FatoresRisco->UsuarioContributivo_PreferenciaPedestre == 'CONDUTOR OU PASSAGEIRO DE VEÍCULO PESADO' ||
						$value->FatoresRisco->UsuarioContributivo_ImprudenciaPedestre == 'CONDUTOR OU PASSAGEIRO DE VEÍCULO PESADO' ||
						$value->FatoresRisco->UsuarioContributivo_Capacete == 'CONDUTOR OU PASSAGEIRO DE VEÍCULO PESADO' ||
						$value->FatoresRisco->UsuarioContributivo_CintoSeguranca == 'CONDUTOR OU PASSAGEIRO DE VEÍCULO PESADO';
					})
					->count();
					$result[$key]['CONDUTOR OU PASSAGEIRO DE MOTO'] = $base->where($request->linhas,$key)
					->filter(function ($value, $key) {
						return $value->FatoresRisco->UsuarioContributivo_Velocidade == 'CONDUTOR OU PASSAGEIRO DE MOTO' || 
						$value->FatoresRisco->UsuarioContributivo_Alcool == 'CONDUTOR OU PASSAGEIRO DE MOTO' || 
						$value->FatoresRisco->UsuarioContributivo_Veiculo == 'CONDUTOR OU PASSAGEIRO DE MOTO' || 
						$value->FatoresRisco->UsuarioContributivo_Fadiga == 'CONDUTOR OU PASSAGEIRO DE MOTO' ||
						$value->FatoresRisco->UsuarioContributivo_Drogas == 'CONDUTOR OU PASSAGEIRO DE MOTO' ||
						$value->FatoresRisco->UsuarioContributivo_Distacao == 'CONDUTOR OU PASSAGEIRO DE MOTO' ||
						$value->FatoresRisco->UsuarioContributivo_AvancarSinal == 'CONDUTOR OU PASSAGEIRO DE MOTO' ||
						$value->FatoresRisco->UsuarioContributivo_CondutorSemHabilitacao == 'CONDUTOR OU PASSAGEIRO DE MOTO' ||
						$value->FatoresRisco->UsuarioContributivo_LocalProibido == 'CONDUTOR OU PASSAGEIRO DE MOTO' ||
						$value->FatoresRisco->UsuarioContributivo_LocalImproprio == 'CONDUTOR OU PASSAGEIRO DE MOTO' ||
						$value->FatoresRisco->UsuarioContributivo_MudancaFaixa == 'CONDUTOR OU PASSAGEIRO DE MOTO' ||
						$value->FatoresRisco->UsuarioContributivo_DistanciaMinima == 'CONDUTOR OU PASSAGEIRO DE MOTO' ||
						$value->FatoresRisco->UsuarioContributivo_Preferencia == 'CONDUTOR OU PASSAGEIRO DE MOTO' ||
						$value->FatoresRisco->UsuarioContributivo_PreferenciaPedestre == 'CONDUTOR OU PASSAGEIRO DE MOTO' ||
						$value->FatoresRisco->UsuarioContributivo_ImprudenciaPedestre == 'CONDUTOR OU PASSAGEIRO DE MOTO' ||
						$value->FatoresRisco->UsuarioContributivo_Capacete == 'CONDUTOR OU PASSAGEIRO DE MOTO' ||
						$value->FatoresRisco->UsuarioContributivo_CintoSeguranca == 'CONDUTOR OU PASSAGEIRO DE MOTO';
					})
					->count();
					$result[$key]['CONDUTOR OU PASSAGEIRO DE BICICLETA'] = $base->where($request->linhas,$key)
					->filter(function ($value, $key) {
						return $value->FatoresRisco->UsuarioContributivo_Velocidade == 'CONDUTOR OU PASSAGEIRO DE BICICLETA' || 
						$value->FatoresRisco->UsuarioContributivo_Alcool == 'CONDUTOR OU PASSAGEIRO DE BICICLETA' || 
						$value->FatoresRisco->UsuarioContributivo_Veiculo == 'CONDUTOR OU PASSAGEIRO DE BICICLETA' || 
						$value->FatoresRisco->UsuarioContributivo_Fadiga == 'CONDUTOR OU PASSAGEIRO DE BICICLETA' ||
						$value->FatoresRisco->UsuarioContributivo_Drogas == 'CONDUTOR OU PASSAGEIRO DE BICICLETA' ||
						$value->FatoresRisco->UsuarioContributivo_Distacao == 'CONDUTOR OU PASSAGEIRO DE BICICLETA' ||
						$value->FatoresRisco->UsuarioContributivo_AvancarSinal == 'CONDUTOR OU PASSAGEIRO DE BICICLETA' ||
						$value->FatoresRisco->UsuarioContributivo_CondutorSemHabilitacao == 'CONDUTOR OU PASSAGEIRO DE BICICLETA' ||
						$value->FatoresRisco->UsuarioContributivo_LocalProibido == 'CONDUTOR OU PASSAGEIRO DE BICICLETA' ||
						$value->FatoresRisco->UsuarioContributivo_LocalImproprio == 'CONDUTOR OU PASSAGEIRO DE BICICLETA' ||
						$value->FatoresRisco->UsuarioContributivo_MudancaFaixa == 'CONDUTOR OU PASSAGEIRO DE BICICLETA' ||
						$value->FatoresRisco->UsuarioContributivo_DistanciaMinima == 'CONDUTOR OU PASSAGEIRO DE BICICLETA' ||
						$value->FatoresRisco->UsuarioContributivo_Preferencia == 'CONDUTOR OU PASSAGEIRO DE BICICLETA' ||
						$value->FatoresRisco->UsuarioContributivo_PreferenciaPedestre == 'CONDUTOR OU PASSAGEIRO DE BICICLETA' ||
						$value->FatoresRisco->UsuarioContributivo_ImprudenciaPedestre == 'CONDUTOR OU PASSAGEIRO DE BICICLETA' ||
						$value->FatoresRisco->UsuarioContributivo_Capacete == 'CONDUTOR OU PASSAGEIRO DE BICICLETA' ||
						$value->FatoresRisco->UsuarioContributivo_CintoSeguranca == 'CONDUTOR OU PASSAGEIRO DE BICICLETA';
					})
					->count();
					$result[$key]['CONDUTOR OU PASSAGEIRO DE ÔNIBUS/VAN'] = $base->where($request->linhas,$key)
					->filter(function ($value, $key) {
						return $value->FatoresRisco->UsuarioContributivo_Velocidade == 'CONDUTOR OU PASSAGEIRO DE ÔNIBUS/VAN' || 
						$value->FatoresRisco->UsuarioContributivo_Alcool == 'CONDUTOR OU PASSAGEIRO DE ÔNIBUS/VAN' || 
						$value->FatoresRisco->UsuarioContributivo_Veiculo == 'CONDUTOR OU PASSAGEIRO DE ÔNIBUS/VAN' || 
						$value->FatoresRisco->UsuarioContributivo_Fadiga == 'CONDUTOR OU PASSAGEIRO DE ÔNIBUS/VAN' ||
						$value->FatoresRisco->UsuarioContributivo_Drogas == 'CONDUTOR OU PASSAGEIRO DE ÔNIBUS/VAN' ||
						$value->FatoresRisco->UsuarioContributivo_Distacao == 'CONDUTOR OU PASSAGEIRO DE ÔNIBUS/VAN' ||
						$value->FatoresRisco->UsuarioContributivo_AvancarSinal == 'CONDUTOR OU PASSAGEIRO DE ÔNIBUS/VAN' ||
						$value->FatoresRisco->UsuarioContributivo_CondutorSemHabilitacao == 'CONDUTOR OU PASSAGEIRO DE ÔNIBUS/VAN' ||
						$value->FatoresRisco->UsuarioContributivo_LocalProibido == 'CONDUTOR OU PASSAGEIRO DE ÔNIBUS/VAN' ||
						$value->FatoresRisco->UsuarioContributivo_LocalImproprio == 'CONDUTOR OU PASSAGEIRO DE ÔNIBUS/VAN' ||
						$value->FatoresRisco->UsuarioContributivo_MudancaFaixa == 'CONDUTOR OU PASSAGEIRO DE ÔNIBUS/VAN' ||
						$value->FatoresRisco->UsuarioContributivo_DistanciaMinima == 'CONDUTOR OU PASSAGEIRO DE ÔNIBUS/VAN' ||
						$value->FatoresRisco->UsuarioContributivo_Preferencia == 'CONDUTOR OU PASSAGEIRO DE ÔNIBUS/VAN' ||
						$value->FatoresRisco->UsuarioContributivo_PreferenciaPedestre == 'CONDUTOR OU PASSAGEIRO DE ÔNIBUS/VAN' ||
						$value->FatoresRisco->UsuarioContributivo_ImprudenciaPedestre == 'CONDUTOR OU PASSAGEIRO DE ÔNIBUS/VAN' ||
						$value->FatoresRisco->UsuarioContributivo_Capacete == 'CONDUTOR OU PASSAGEIRO DE ÔNIBUS/VAN' ||
						$value->FatoresRisco->UsuarioContributivo_CintoSeguranca == 'CONDUTOR OU PASSAGEIRO DE ÔNIBUS/VAN';
					})
					->count();
					$result[$key]['PEDESTRE'] = $base->where($request->linhas,$key)
					->filter(function ($value, $key) {
						return $value->FatoresRisco->UsuarioContributivo_Velocidade == 'PEDESTRE' || 
						$value->FatoresRisco->UsuarioContributivo_Alcool == 'PEDESTRE' || 
						$value->FatoresRisco->UsuarioContributivo_Veiculo == 'PEDESTRE' || 
						$value->FatoresRisco->UsuarioContributivo_Fadiga == 'PEDESTRE' ||
						$value->FatoresRisco->UsuarioContributivo_Drogas == 'PEDESTRE' ||
						$value->FatoresRisco->UsuarioContributivo_Distacao == 'PEDESTRE' ||
						$value->FatoresRisco->UsuarioContributivo_AvancarSinal == 'PEDESTRE' ||
						$value->FatoresRisco->UsuarioContributivo_CondutorSemHabilitacao == 'PEDESTRE' ||
						$value->FatoresRisco->UsuarioContributivo_LocalProibido == 'PEDESTRE' ||
						$value->FatoresRisco->UsuarioContributivo_LocalImproprio == 'PEDESTRE' ||
						$value->FatoresRisco->UsuarioContributivo_MudancaFaixa == 'PEDESTRE' ||
						$value->FatoresRisco->UsuarioContributivo_DistanciaMinima == 'PEDESTRE' ||
						$value->FatoresRisco->UsuarioContributivo_Preferencia == 'PEDESTRE' ||
						$value->FatoresRisco->UsuarioContributivo_PreferenciaPedestre == 'PEDESTRE' ||
						$value->FatoresRisco->UsuarioContributivo_ImprudenciaPedestre == 'PEDESTRE' ||
						$value->FatoresRisco->UsuarioContributivo_Capacete == 'PEDESTRE' ||
						$value->FatoresRisco->UsuarioContributivo_CintoSeguranca == 'PEDESTRE';
					})
					->count();
				}
			}
		}
		//busca vitimas
		if ($request['base'] == "Vitimas") {
			$base = Vitimas::where('Ano',$request->Ano)	
			->where('CodCidade',$request->CodCidade)
			->where('Trimestre',$request->Trimestre)->get();
			
			//APENAS DADOS VITIMAS
			if ($request['linhas'] == "Gravidade") {
				
				$linhas = $base->groupBy('GravidadeLesao');	
			}elseif ($request['linhas'] == "FaixaEtaria") {
				
				$linhas = $base->groupBy('FaixaEtaria');	
			}elseif ($request['linhas'] == "Sexo") {
				
				$linhas = $base->groupBy('Sexo');
			}elseif ($request['linhas'] == "Condicao") {
				
				$linhas = $base->groupBy('CondicaoVitima');
			}elseif ($request['linhas'] == "MeioTransporte") {
				
				$linhas = $base->groupBy('MeioTransporte');
			}elseif ($request['linhas'] == "MunicipioRes") {
				
				$linhas = $base->groupBy('MunicipioResidencia');
			}
			//precisa de vincular com o acidente
			elseif ($request['linhas'] == "CausaBase") {
				$base->load('LinkagemSim');
				$base->whereHas('LinkagemSim');
				$linhas = $base->groupBy('LinkagemSim.sim.CAUSABAS');
			}elseif ($request['linhas'] == "FonteDados") {	
				$linhas = $base->groupBy('FonteDados');	
			}elseif ($request['linhas'] == "TipoAcidente") {
				$linhas = $base->groupBy('TipoAcidente');	
			}
			//colunas
			if ($request['colunas'] == "Frequencia") {
				$result = [];
				foreach ($linhas as $key => $value) {					
					$result[$key] = $value->count();
				}
			}

			if ($request['colunas'] == "Gravidade") {
				$result = [];
				$colunas = $base->groupBy('GravidadeLesao');
				$colunas = array_keys($colunas->toArray());
				foreach ($linhas as $key => $value) {			
					foreach ($colunas as $coluna) {
						$result[$key][$coluna] = $value->where('GravidadeLesao',$coluna)->count();
					}				
				}
			}
			if ($request['colunas'] == "Condicao") {
				$result = [];
				$colunas = $base->groupBy('CondicaoVitima');
				$colunas = array_keys($colunas->toArray());
				foreach ($linhas as $key => $value) {			
					foreach ($colunas as $coluna) {
						$result[$key][$coluna] = $value->where('CondicaoVitima',$coluna)->count();
					}				
				}
			}
			if ($request['colunas'] == "MeioTransporte") {
				$result = [];
				$colunas = $base->groupBy('MeioTransporte');
				$colunas = array_keys($colunas->toArray());
				foreach ($linhas as $key => $value) {			
					foreach ($colunas as $coluna) {
						$result[$key][$coluna] = $value->where('MeioTransporte',$coluna)->count();
					}				
				}
			}
			if ($request['colunas'] == "Sexo") {
				$result = [];
				$colunas = $base->groupBy('Sexo');
				$colunas = array_keys($colunas->toArray());
				foreach ($linhas as $key => $value) {			
					foreach ($colunas as $coluna) {
						$result[$key][$coluna] = $value->where('Sexo',$coluna)->count();
					}				
				}
			}


			if ($request['colunas'] == "FaixaEtaria") {
				$result = [];
				$colunas = $base->groupBy('FaixaEtaria');
				$colunas = array_keys($colunas->toArray());
				foreach ($linhas as $key => $value) {			
					foreach ($colunas as $coluna) {
						$result[$key][$coluna] = $value->where('FaixaEtaria',$coluna)->count();
					}				
				}
			}
			if ($request['colunas'] == "FonteDados") {
				$result = [];
				$colunas = $base->groupBy('FonteDados');
				$colunas = array_keys($colunas->toArray());
				foreach ($linhas as $key => $value) {			
					foreach ($colunas as $coluna) {
						$result[$key][$coluna] = $value->where('FonteDados',$coluna)->count();
					}				
				}

			}
			if ($request['colunas'] == "Horario") {
				$base = $base->load('QuadroMultiplo');
				$result = [];
				$colunas = $base->groupBy('QuadroMultiplo.HoraAcidente');			
				$colunas = array_keys($colunas->toArray());
				foreach ($linhas as $key => $value) {		
					foreach ($colunas as $coluna) {
						$result[$key][$coluna] = $value->where('QuadroMultiplo.HoraAcidente',$coluna)->count();
					}				
				}

			}
			if ($request['colunas'] == "Dia") {
				$base = $base->load('QuadroMultiplo');
				$result = [];
				$colunas = $base->groupBy('QuadroMultiplo.DiaSemana');			
				$colunas = array_keys($colunas->toArray());
				foreach ($linhas as $key => $value) {	
					foreach ($colunas as $coluna) {
						$result[$key][$coluna] = $value->where('QuadroMultiplo.DiaSemana',$coluna)->count();
					}				
				}
			}
		}
		//apenas pares linkados no sih
		if ($request['base'] == "PARES_SIH") {
			$base = LinkagemSih::with('vitima','quadro_multiplo')->where('Ano',$request->Ano)	
			->where('CodCidade',$request->CodCidade)
			->where('Trimestre',$request->Trimestre)
			->where('ParVerdadeiro',1)
			->get();
			if ($request['linhas'] == "Gravidade") {
				$base->load('vitima');
				$linha = 'vitima.GravidadeLesao';
				$linhas = $base->groupBy('vitima.GravidadeLesao');	
			}elseif ($request['linhas'] == "DiaSemana") {
				$base->load('quadro_multiplo');
				$linha = 'quadro_multiplo.DiaSemana';
				$linhas = $base->groupBy('quadro_multiplo.DiaSemana');
			}elseif ($request['linhas'] == "Horario") {
				$base->load('quadro_multiplo');
				$linha = 'quadro_multiplo.HoraAcidente';
				$linhas = $base->groupBy('quadro_multiplo.HoraAcidente');
			}elseif ($request['linhas'] == "QtdVitimas") {
				$base->load('quadro_multiplo');
				$linha = 'quadro_multiplo.QtdVitimas';
				$linhas = $base->groupBy('quadro_multiplo.QtdVitimas');
			}elseif ($request['linhas'] == "CausaBase") {
				$base->load('sih');
				$linha = 'sih.DIAG_PRI';
				$linhas = $base->groupBy('sih.DIAG_PRI');
			}elseif ($request['linhas'] == "FonteDados") {	
				$base->load('quadro_multiplo');
				$linha = 'quadro_multiplo.FonteDados';
				$linhas = $base->groupBy('quadro_multiplo.FonteDados');	
			}elseif ($request['linhas'] == "TipoAcidente") {
				$base->load('quadro_multiplo');
				$linha = 'quadro_multiplo.TipoAcidente';
				$linhas = $base->groupBy('quadro_multiplo.TipoAcidente');	
			}elseif ($request['linhas'] == "FaixaEtaria") {
				$base->load('vitima');
				$linha = 'vitima.FaixaEtaria';
				$linhas = $base->groupBy('vitima.FaixaEtaria');	
			}elseif ($request['linhas'] == "Sexo") {
				$base->load('vitima');
				$linha = 'vitima.Sexo';
				$linhas = $base->groupBy('vitima.Sexo');
			}elseif ($request['linhas'] == "Condicao") {
				$base->load('vitima');
				$linha = 'vitima.CondicaoVitima';
				$linhas = $base->groupBy('vitima.CondicaoVitima');
			}elseif ($request['linhas'] == "MeioTransporte") {
				$base->load('vitima');
				$linha = 'vitima.MeioTransporte';
				$linhas = $base->groupBy('vitima.MeioTransporte');
			}elseif ($request['linhas'] == "MunicipioRes") {
				$base->load('vitima');
				$linha = 'vitima.MunicipioResidencia';
				$linhas = $base->groupBy('vitima.MunicipioResidencia');
			}
			//colunas
			if ($request['colunas'] == "Frequencia") {
				$result = [];
				foreach ($linhas as $key => $value) {					
					$result[$key] = $value->count();
				}
			}

			if ($request['colunas'] == "Gravidade") {
				$result = [];				
				$colunas = $base->groupBy('vitima.GravidadeLesao');
				$colunas = array_keys($colunas->toArray());
				foreach ($linhas as $key => $value) {			
					foreach ($colunas as $coluna) {
						$result[$key][$coluna] = $value->where('vitima.GravidadeLesao',$coluna)->count();
					}				
				}
			}
			if ($request['colunas'] == "Condicao") {
				$result = [];
				$colunas = $base->groupBy('vitima.CondicaoVitima');
				$colunas = array_keys($colunas->toArray());
				foreach ($linhas as $key => $value) {			
					foreach ($colunas as $coluna) {
						$result[$key][$coluna] = $value->where('vitima.CondicaoVitima',$coluna)->count();
					}				
				}
			}
			if ($request['colunas'] == "MeioTransporte") {
				$result = [];
				$colunas = $base->groupBy('vitima.MeioTransporte');
				$colunas = array_keys($colunas->toArray());
				foreach ($linhas as $key => $value) {			
					foreach ($colunas as $coluna) {
						$result[$key][$coluna] = $value->where('vitima.MeioTransporte',$coluna)->count();
					}				
				}
			}
			if ($request['colunas'] == "Sexo") {
				$result = [];
				$colunas = $base->groupBy('vitima.Sexo');
				$colunas = array_keys($colunas->toArray());
				foreach ($linhas as $key => $value) {			
					foreach ($colunas as $coluna) {
						$result[$key][$coluna] = $value->where('vitima.Sexo',$coluna)->count();
					}				
				}
			}


			if ($request['colunas'] == "FaixaEtaria") {
				$result = [];
				$colunas = $base->groupBy('vitima.FaixaEtaria');
				$colunas = array_keys($colunas->toArray());
				foreach ($linhas as $key => $value) {			
					foreach ($colunas as $coluna) {
						$result[$key][$coluna] = $value->where('vitima.FaixaEtaria',$coluna)->count();
					}				
				}
			}
			if ($request['colunas'] == "FonteDados") {
				$base = $base->load('quadro_multiplo');
				$result = [];
				$colunas = $base->groupBy('quadro_multiplo.FonteDados');
				$colunas = array_keys($colunas->toArray());
				foreach ($linhas as $key => $value) {			
					foreach ($colunas as $coluna) {
						$result[$key][$coluna] = $value->where('quadro_multiplo.FonteDados',$coluna)->count();
					}				
				}

			}
			if ($request['colunas'] == "Horario") {
				$base = $base->load('quadro_multiplo');
				$result = [];
				$colunas = $base->groupBy('quadro_multiplo.HoraAcidente');			
				$colunas = array_keys($colunas->toArray());
				foreach ($linhas as $key => $value) {		
					foreach ($colunas as $coluna) {
						$result[$key][$coluna] = $value->where('quadro_multiplo.HoraAcidente',$coluna)->count();
					}				
				}

			}
			if ($request['colunas'] == "Dia") {
				$base = $base->load('quadro_multiplo');
				$result = [];
				$colunas = $base->groupBy('quadro_multiplo.DiaSemana');			
				$colunas = array_keys($colunas->toArray());
				foreach ($linhas as $key => $value) {	
					foreach ($colunas as $coluna) {
						$result[$key][$coluna] = $value->where('quadro_multiplo.DiaSemana',$coluna)->count();
					}				
				}
			}

			if ($request['colunas'] == "TipoAcidente") {
				$base = $base->load('quadro_multiplo');
				$result = [];
				$colunas = $base->groupBy('quadro_multiplo.TipoAcidente');
				$colunas = array_keys($colunas->toArray());
				foreach ($linhas as $key => $value) {			
					foreach ($colunas as $coluna) {
						$result[$key][$coluna] = $value->where('quadro_multiplo.TipoAcidente',$coluna)->count();
					}				
				}
			}
			if ($request['colunas'] == "FrequenciaProtecaoInadequada") {
				$base = $base->load('quadro_multiplo.FatoresRisco');
				$result = [];
				$base = $base->filter(function ($value, $key) {
					return !empty($value->quadro_multiplo->FatoresRisco);
				});
				foreach ($linhas as $key => $value) {		
					$result[$key]['CintoSeguranca'] = $base->where($linha,$key)
					->where('quadro_multiplo.FatoresRisco.CintoSeguranca','>',0)
					->count();
					$result[$key]['EquipamentoProtecao'] = $base->where($linha,$key)
					->where('quadro_multiplo.FatoresRisco.EquipamentoProtecao','>',0)
					->count();
					$result[$key]['GerenciamentoTrauma'] = $base->where($linha,$key)
					->where('quadro_multiplo.FatoresRisco.GerenciamentoTrauma','>',0)
					->count();
					$result[$key]['ObjetosLateraisVia'] = $base->where($linha,$key)
					->where('quadro_multiplo.FatoresRisco.ObjetosLateraisVia','>',0)
					->count();
					$result[$key]['Capacete'] = $base->where($linha,$key)
					->where('quadro_multiplo.FatoresRisco.Capacete','>',0)
					->count();
					$result[$key]['OutraProtecao'] = $base->where($linha,$key)
					->where('quadro_multiplo.FatoresRisco.outra_protecao','>',0)
					->count();
				}
			}
			if ($request['colunas'] == "ProtecaoInadequada") {
				$base = $base->load('quadro_multiplo.FatoresRisco');
				$result = [];
				$base = $base->filter(function ($value, $key) {
					return !empty($value->quadro_multiplo->FatoresRisco);
				});
				foreach ($linhas as $key => $value) {		
					$result[$key]['CintoSeguranca'] = $base->where($linha,$key)->sum('quadro_multiplo.FatoresRisco.CintoSeguranca');
					$result[$key]['EquipamentoProtecao'] = $base->where($linha,$key)->sum('quadro_multiplo.FatoresRisco.EquipamentoProtecao');
					$result[$key]['GerenciamentoTrauma'] = $base->where($linha,$key)->sum('quadro_multiplo.FatoresRisco.GerenciamentoTrauma');
					$result[$key]['ObjetosLateraisVia'] = $base->where($linha,$key)->sum('quadro_multiplo.FatoresRisco.ObjetosLateraisVia');
					$result[$key]['Capacete'] = $base->where($linha,$key)->sum('quadro_multiplo.FatoresRisco.Capacete');
					$result[$key]['OutraProtecao'] = $base->where($linha,$key)->sum('quadro_multiplo.FatoresRisco.outra_protecao');
				}
			}
			if ($request['colunas'] == "FatorRisco") {
				$base = $base->load('quadro_multiplo.FatoresRisco');
				$result = [];
				$base = $base->filter(function ($value, $key) {
					return !empty($value->quadro_multiplo->FatoresRisco);
				});
				foreach ($linhas as $key => $value) {		
					$result[$key]['Velocidade'] = $base->where($linha,$key)->sum('quadro_multiplo.FatoresRisco.Velocidade');
					$result[$key]['Alcool'] = $base->where($linha,$key)->sum('quadro_multiplo.FatoresRisco.Alcool');
					$result[$key]['Infraestrutura'] = $base->where($linha,$key)->sum('quadro_multiplo.FatoresRisco.Infraestrutura');
					$result[$key]['Drogas'] = $base->where($linha,$key)->sum('quadro_multiplo.FatoresRisco.Drogas');
					$result[$key]['Fadiga'] = $base->where($linha,$key)->sum('quadro_multiplo.FatoresRisco.Fadiga');
					$result[$key]['Distacao'] = $base->where($linha,$key)->sum('quadro_multiplo.FatoresRisco.Distacao');
					$result[$key]['Visibilidade'] = $base->where($linha,$key)->sum('quadro_multiplo.FatoresRisco.Visibilidade');
				}
			}
			if ($request['colunas'] == "FrequenciaFatorRisco") {
				$base = $base->load('quadro_multiplo.FatoresRisco');
				$result = [];
				$base = $base->filter(function ($value, $key) {
					return !empty($value->quadro_multiplo->FatoresRisco);
				});
				foreach ($linhas as $key => $value) {		
					$result[$key]['Velocidade'] = $base->where($linha,$key)
					->where('quadro_multiplo.FatoresRisco.Velocidade','>',0)
					->count();
					$result[$key]['Alcool'] = $base->where($linha,$key)
					->where('quadro_multiplo.FatoresRisco.Alcool','>',0)
					->count();
					$result[$key]['Infraestrutura'] = $base->where($linha,$key)->where('quadro_multiplo.FatoresRisco.Infraestrutura','>',0)
					->count();
					$result[$key]['Drogas'] = $base->where($linha,$key)->where('quadro_multiplo.FatoresRisco.Drogas','>',0)
					->count();
					$result[$key]['Fadiga'] = $base->where($linha,$key)->where('quadro_multiplo.FatoresRisco.Fadiga','>',0)
					->count();
					$result[$key]['Distacao'] = $base->where($linha,$key)->where('quadro_multiplo.FatoresRisco.Distacao','>',0)
					->count();
					$result[$key]['Visibilidade'] = $base->where($linha,$key)->where('quadro_multiplo.FatoresRisco.Visibilidade','>',0)
					->count();
				}
			}
			if ($request['colunas'] == "FrequenciaCondutaRisco") {
				$base = $base->load('quadro_multiplo.FatoresRisco');
				$result = [];
				$base = $base->filter(function ($value, $key) {
					return !empty($value->quadro_multiplo->FatoresRisco);
				});
				foreach ($linhas as $key => $value) {
					$result[$key]['AvancarSinal'] = $base->where($linha,$key)
					->where('quadro_multiplo.FatoresRisco.AvancarSinal','>',0)
					->count();
					$result[$key]['CondutorSemHabilitacao'] = $base->where($linha,$key)
					->where('quadro_multiplo.FatoresRisco.CondutorSemHabilitacao','>',0)
					->count();
					$result[$key]['LocalProibido'] = $base->where($linha,$key)
					->where('quadro_multiplo.FatoresRisco.LocalProibido','>',0)
					->count();
					$result[$key]['LocalImproprio'] = $base->where($linha,$key)
					->where('quadro_multiplo.FatoresRisco.LocalImproprio','>',0)
					->count();
					$result[$key]['MudancaFaixa'] = $base->where($linha,$key)
					->where('quadro_multiplo.FatoresRisco.MudancaFaixa','>',0)
					->count();
					$result[$key]['DistanciaMinima'] = $base->where($linha,$key)
					->where('quadro_multiplo.FatoresRisco.DistanciaMinima','>',0)
					->count();
					$result[$key]['Preferencia'] = $base->where($linha,$key)
					->where('quadro_multiplo.FatoresRisco.Preferencia','>',0)
					->count();
					$result[$key]['PreferenciaPedestre'] = $base->where($linha,$key)
					->where('quadro_multiplo.FatoresRisco.PreferenciaPedestre','>',0)
					->count();
					$result[$key]['ImprudenciaPedestre'] = $base->where($linha,$key)
					->where('quadro_multiplo.FatoresRisco.ImprudenciaPedestre','>',0)
					->count();
				}
			}
			if ($request['colunas'] == "CondutaRisco") {
				$base = $base->load('quadro_multiplo.FatoresRisco');
				$result = [];
				$base = $base->filter(function ($value, $key) {
					return !empty($value->quadro_multiplo->FatoresRisco);
				});
				foreach ($linhas as $key => $value) {		
					$result[$key]['AvancarSinal'] = $base->where($linha,$key)->sum('quadro_multiplo.FatoresRisco.AvancarSinal');
					$result[$key]['CondutorSemHabilitacao'] = $base->where($linha,$key)->sum('quadro_multiplo.FatoresRisco.CondutorSemHabilitacao');
					$result[$key]['LocalProibido'] = $base->where($linha,$key)->sum('quadro_multiplo.FatoresRisco.LocalProibido');
					$result[$key]['LocalImproprio'] = $base->where($linha,$key)->sum('quadro_multiplo.FatoresRisco.LocalImproprio');
					$result[$key]['MudancaFaixa'] = $base->where($linha,$key)->sum('quadro_multiplo.FatoresRisco.MudancaFaixa');
					$result[$key]['DistanciaMinima'] = $base->where($linha,$key)->sum('quadro_multiplo.FatoresRisco.DistanciaMinima');
					$result[$key]['Preferencia'] = $base->where($linha,$key)->sum('quadro_multiplo.FatoresRisco.Preferencia');
					$result[$key]['PreferenciaPedestre'] = $base->where($linha,$key)->sum('quadro_multiplo.FatoresRisco.PreferenciaPedestre');
					$result[$key]['ImprudenciaPedestre'] = $base->where($linha,$key)->sum('quadro_multiplo.FatoresRisco.ImprudenciaPedestre');
				}
			}

			if ($request['colunas'] == "UsuarioContributivo") {
				$base = $base->load('quadro_multiplo.FatoresRisco');
				$result = [];
				$base = $base->filter(function ($value, $key) {
					return !empty($value->quadro_multiplo->FatoresRisco);
				});
				foreach ($linhas as $key => $value) {		
					$result[$key]['CONDUTOR OU PASSAGEIRO DE VEÍCULO LEVE'] = $base->where($linha,$key)
					->filter(function ($value, $key) {
						return $value->quadro_multiplo->FatoresRisco->UsuarioContributivo_Velocidade == 'CONDUTOR OU PASSAGEIRO DE VEÍCULO LEVE' || 
						$value->quadro_multiplo->FatoresRisco->UsuarioContributivo_Alcool == 'CONDUTOR OU PASSAGEIRO DE VEÍCULO LEVE' || 
						$value->quadro_multiplo->FatoresRisco->UsuarioContributivo_Veiculo == 'CONDUTOR OU PASSAGEIRO DE VEÍCULO LEVE' || 
						$value->quadro_multiplo->FatoresRisco->UsuarioContributivo_Fadiga == 'CONDUTOR OU PASSAGEIRO DE VEÍCULO LEVE' ||
						$value->quadro_multiplo->FatoresRisco->UsuarioContributivo_Drogas == 'CONDUTOR OU PASSAGEIRO DE VEÍCULO LEVE' ||
						$value->quadro_multiplo->FatoresRisco->UsuarioContributivo_Distacao == 'CONDUTOR OU PASSAGEIRO DE VEÍCULO LEVE' ||
						$value->quadro_multiplo->FatoresRisco->UsuarioContributivo_AvancarSinal == 'CONDUTOR OU PASSAGEIRO DE VEÍCULO LEVE' ||
						$value->quadro_multiplo->FatoresRisco->UsuarioContributivo_CondutorSemHabilitacao == 'CONDUTOR OU PASSAGEIRO DE VEÍCULO LEVE' ||
						$value->quadro_multiplo->FatoresRisco->UsuarioContributivo_LocalProibido == 'CONDUTOR OU PASSAGEIRO DE VEÍCULO LEVE' ||
						$value->quadro_multiplo->FatoresRisco->UsuarioContributivo_LocalImproprio == 'CONDUTOR OU PASSAGEIRO DE VEÍCULO LEVE' ||
						$value->quadro_multiplo->FatoresRisco->UsuarioContributivo_MudancaFaixa == 'CONDUTOR OU PASSAGEIRO DE VEÍCULO LEVE' ||
						$value->quadro_multiplo->FatoresRisco->UsuarioContributivo_DistanciaMinima == 'CONDUTOR OU PASSAGEIRO DE VEÍCULO LEVE' ||
						$value->quadro_multiplo->FatoresRisco->UsuarioContributivo_Preferencia == 'CONDUTOR OU PASSAGEIRO DE VEÍCULO LEVE' ||
						$value->quadro_multiplo->FatoresRisco->UsuarioContributivo_PreferenciaPedestre == 'CONDUTOR OU PASSAGEIRO DE VEÍCULO LEVE' ||
						$value->quadro_multiplo->FatoresRisco->UsuarioContributivo_ImprudenciaPedestre == 'CONDUTOR OU PASSAGEIRO DE VEÍCULO LEVE' ||
						$value->quadro_multiplo->FatoresRisco->UsuarioContributivo_Capacete == 'CONDUTOR OU PASSAGEIRO DE VEÍCULO LEVE' ||
						$value->quadro_multiplo->FatoresRisco->UsuarioContributivo_CintoSeguranca == 'CONDUTOR OU PASSAGEIRO DE VEÍCULO LEVE';
					})
					->count();	
					$result[$key]['CONDUTOR OU PASSAGEIRO DE VEÍCULO PESADO'] = $base->where($linha,$key)
					->filter(function ($value, $key) {
						return $value->quadro_multiplo->FatoresRisco->UsuarioContributivo_Velocidade == 'CONDUTOR OU PASSAGEIRO DE VEÍCULO PESADO' || 
						$value->quadro_multiplo->FatoresRisco->UsuarioContributivo_Alcool == 'CONDUTOR OU PASSAGEIRO DE VEÍCULO PESADO' || 
						$value->quadro_multiplo->FatoresRisco->UsuarioContributivo_Veiculo == 'CONDUTOR OU PASSAGEIRO DE VEÍCULO PESADO' || 
						$value->quadro_multiplo->FatoresRisco->UsuarioContributivo_Fadiga == 'CONDUTOR OU PASSAGEIRO DE VEÍCULO PESADO' ||
						$value->quadro_multiplo->FatoresRisco->UsuarioContributivo_Drogas == 'CONDUTOR OU PASSAGEIRO DE VEÍCULO PESADO' ||
						$value->quadro_multiplo->FatoresRisco->UsuarioContributivo_Distacao == 'CONDUTOR OU PASSAGEIRO DE VEÍCULO PESADO' ||
						$value->quadro_multiplo->FatoresRisco->UsuarioContributivo_AvancarSinal == 'CONDUTOR OU PASSAGEIRO DE VEÍCULO PESADO' ||
						$value->quadro_multiplo->FatoresRisco->UsuarioContributivo_CondutorSemHabilitacao == 'CONDUTOR OU PASSAGEIRO DE VEÍCULO PESADO' ||
						$value->quadro_multiplo->FatoresRisco->UsuarioContributivo_LocalProibido == 'CONDUTOR OU PASSAGEIRO DE VEÍCULO PESADO' ||
						$value->quadro_multiplo->FatoresRisco->UsuarioContributivo_LocalImproprio == 'CONDUTOR OU PASSAGEIRO DE VEÍCULO PESADO' ||
						$value->quadro_multiplo->FatoresRisco->UsuarioContributivo_MudancaFaixa == 'CONDUTOR OU PASSAGEIRO DE VEÍCULO PESADO' ||
						$value->quadro_multiplo->FatoresRisco->UsuarioContributivo_DistanciaMinima == 'CONDUTOR OU PASSAGEIRO DE VEÍCULO PESADO' ||
						$value->quadro_multiplo->FatoresRisco->UsuarioContributivo_Preferencia == 'CONDUTOR OU PASSAGEIRO DE VEÍCULO PESADO' ||
						$value->quadro_multiplo->FatoresRisco->UsuarioContributivo_PreferenciaPedestre == 'CONDUTOR OU PASSAGEIRO DE VEÍCULO PESADO' ||
						$value->quadro_multiplo->FatoresRisco->UsuarioContributivo_ImprudenciaPedestre == 'CONDUTOR OU PASSAGEIRO DE VEÍCULO PESADO' ||
						$value->quadro_multiplo->FatoresRisco->UsuarioContributivo_Capacete == 'CONDUTOR OU PASSAGEIRO DE VEÍCULO PESADO' ||
						$value->quadro_multiplo->FatoresRisco->UsuarioContributivo_CintoSeguranca == 'CONDUTOR OU PASSAGEIRO DE VEÍCULO PESADO';
					})
					->count();
					$result[$key]['CONDUTOR OU PASSAGEIRO DE MOTO'] = $base->where($linha,$key)
					->filter(function ($value, $key) {
						return $value->quadro_multiplo->FatoresRisco->UsuarioContributivo_Velocidade == 'CONDUTOR OU PASSAGEIRO DE MOTO' || 
						$value->quadro_multiplo->FatoresRisco->UsuarioContributivo_Alcool == 'CONDUTOR OU PASSAGEIRO DE MOTO' || 
						$value->quadro_multiplo->FatoresRisco->UsuarioContributivo_Veiculo == 'CONDUTOR OU PASSAGEIRO DE MOTO' || 
						$value->quadro_multiplo->FatoresRisco->UsuarioContributivo_Fadiga == 'CONDUTOR OU PASSAGEIRO DE MOTO' ||
						$value->quadro_multiplo->FatoresRisco->UsuarioContributivo_Drogas == 'CONDUTOR OU PASSAGEIRO DE MOTO' ||
						$value->quadro_multiplo->FatoresRisco->UsuarioContributivo_Distacao == 'CONDUTOR OU PASSAGEIRO DE MOTO' ||
						$value->quadro_multiplo->FatoresRisco->UsuarioContributivo_AvancarSinal == 'CONDUTOR OU PASSAGEIRO DE MOTO' ||
						$value->quadro_multiplo->FatoresRisco->UsuarioContributivo_CondutorSemHabilitacao == 'CONDUTOR OU PASSAGEIRO DE MOTO' ||
						$value->quadro_multiplo->FatoresRisco->UsuarioContributivo_LocalProibido == 'CONDUTOR OU PASSAGEIRO DE MOTO' ||
						$value->quadro_multiplo->FatoresRisco->UsuarioContributivo_LocalImproprio == 'CONDUTOR OU PASSAGEIRO DE MOTO' ||
						$value->quadro_multiplo->FatoresRisco->UsuarioContributivo_MudancaFaixa == 'CONDUTOR OU PASSAGEIRO DE MOTO' ||
						$value->quadro_multiplo->FatoresRisco->UsuarioContributivo_DistanciaMinima == 'CONDUTOR OU PASSAGEIRO DE MOTO' ||
						$value->quadro_multiplo->FatoresRisco->UsuarioContributivo_Preferencia == 'CONDUTOR OU PASSAGEIRO DE MOTO' ||
						$value->quadro_multiplo->FatoresRisco->UsuarioContributivo_PreferenciaPedestre == 'CONDUTOR OU PASSAGEIRO DE MOTO' ||
						$value->quadro_multiplo->FatoresRisco->UsuarioContributivo_ImprudenciaPedestre == 'CONDUTOR OU PASSAGEIRO DE MOTO' ||
						$value->quadro_multiplo->FatoresRisco->UsuarioContributivo_Capacete == 'CONDUTOR OU PASSAGEIRO DE MOTO' ||
						$value->quadro_multiplo->FatoresRisco->UsuarioContributivo_CintoSeguranca == 'CONDUTOR OU PASSAGEIRO DE MOTO';
					})
					->count();
					$result[$key]['CONDUTOR OU PASSAGEIRO DE BICICLETA'] = $base->where($linha,$key)
					->filter(function ($value, $key) {
						return $value->quadro_multiplo->FatoresRisco->UsuarioContributivo_Velocidade == 'CONDUTOR OU PASSAGEIRO DE BICICLETA' || 
						$value->quadro_multiplo->FatoresRisco->UsuarioContributivo_Alcool == 'CONDUTOR OU PASSAGEIRO DE BICICLETA' || 
						$value->quadro_multiplo->FatoresRisco->UsuarioContributivo_Veiculo == 'CONDUTOR OU PASSAGEIRO DE BICICLETA' || 
						$value->quadro_multiplo->FatoresRisco->UsuarioContributivo_Fadiga == 'CONDUTOR OU PASSAGEIRO DE BICICLETA' ||
						$value->quadro_multiplo->FatoresRisco->UsuarioContributivo_Drogas == 'CONDUTOR OU PASSAGEIRO DE BICICLETA' ||
						$value->quadro_multiplo->FatoresRisco->UsuarioContributivo_Distacao == 'CONDUTOR OU PASSAGEIRO DE BICICLETA' ||
						$value->quadro_multiplo->FatoresRisco->UsuarioContributivo_AvancarSinal == 'CONDUTOR OU PASSAGEIRO DE BICICLETA' ||
						$value->quadro_multiplo->FatoresRisco->UsuarioContributivo_CondutorSemHabilitacao == 'CONDUTOR OU PASSAGEIRO DE BICICLETA' ||
						$value->quadro_multiplo->FatoresRisco->UsuarioContributivo_LocalProibido == 'CONDUTOR OU PASSAGEIRO DE BICICLETA' ||
						$value->quadro_multiplo->FatoresRisco->UsuarioContributivo_LocalImproprio == 'CONDUTOR OU PASSAGEIRO DE BICICLETA' ||
						$value->quadro_multiplo->FatoresRisco->UsuarioContributivo_MudancaFaixa == 'CONDUTOR OU PASSAGEIRO DE BICICLETA' ||
						$value->quadro_multiplo->FatoresRisco->UsuarioContributivo_DistanciaMinima == 'CONDUTOR OU PASSAGEIRO DE BICICLETA' ||
						$value->quadro_multiplo->FatoresRisco->UsuarioContributivo_Preferencia == 'CONDUTOR OU PASSAGEIRO DE BICICLETA' ||
						$value->quadro_multiplo->FatoresRisco->UsuarioContributivo_PreferenciaPedestre == 'CONDUTOR OU PASSAGEIRO DE BICICLETA' ||
						$value->quadro_multiplo->FatoresRisco->UsuarioContributivo_ImprudenciaPedestre == 'CONDUTOR OU PASSAGEIRO DE BICICLETA' ||
						$value->quadro_multiplo->FatoresRisco->UsuarioContributivo_Capacete == 'CONDUTOR OU PASSAGEIRO DE BICICLETA' ||
						$value->quadro_multiplo->FatoresRisco->UsuarioContributivo_CintoSeguranca == 'CONDUTOR OU PASSAGEIRO DE BICICLETA';
					})
					->count();
					$result[$key]['CONDUTOR OU PASSAGEIRO DE ÔNIBUS/VAN'] = $base->where($linha,$key)
					->filter(function ($value, $key) {
						return $value->quadro_multiplo->FatoresRisco->UsuarioContributivo_Velocidade == 'CONDUTOR OU PASSAGEIRO DE ÔNIBUS/VAN' || 
						$value->quadro_multiplo->FatoresRisco->UsuarioContributivo_Alcool == 'CONDUTOR OU PASSAGEIRO DE ÔNIBUS/VAN' || 
						$value->quadro_multiplo->FatoresRisco->UsuarioContributivo_Veiculo == 'CONDUTOR OU PASSAGEIRO DE ÔNIBUS/VAN' || 
						$value->quadro_multiplo->FatoresRisco->UsuarioContributivo_Fadiga == 'CONDUTOR OU PASSAGEIRO DE ÔNIBUS/VAN' ||
						$value->quadro_multiplo->FatoresRisco->UsuarioContributivo_Drogas == 'CONDUTOR OU PASSAGEIRO DE ÔNIBUS/VAN' ||
						$value->quadro_multiplo->FatoresRisco->UsuarioContributivo_Distacao == 'CONDUTOR OU PASSAGEIRO DE ÔNIBUS/VAN' ||
						$value->quadro_multiplo->FatoresRisco->UsuarioContributivo_AvancarSinal == 'CONDUTOR OU PASSAGEIRO DE ÔNIBUS/VAN' ||
						$value->quadro_multiplo->FatoresRisco->UsuarioContributivo_CondutorSemHabilitacao == 'CONDUTOR OU PASSAGEIRO DE ÔNIBUS/VAN' ||
						$value->quadro_multiplo->FatoresRisco->UsuarioContributivo_LocalProibido == 'CONDUTOR OU PASSAGEIRO DE ÔNIBUS/VAN' ||
						$value->quadro_multiplo->FatoresRisco->UsuarioContributivo_LocalImproprio == 'CONDUTOR OU PASSAGEIRO DE ÔNIBUS/VAN' ||
						$value->quadro_multiplo->FatoresRisco->UsuarioContributivo_MudancaFaixa == 'CONDUTOR OU PASSAGEIRO DE ÔNIBUS/VAN' ||
						$value->quadro_multiplo->FatoresRisco->UsuarioContributivo_DistanciaMinima == 'CONDUTOR OU PASSAGEIRO DE ÔNIBUS/VAN' ||
						$value->quadro_multiplo->FatoresRisco->UsuarioContributivo_Preferencia == 'CONDUTOR OU PASSAGEIRO DE ÔNIBUS/VAN' ||
						$value->quadro_multiplo->FatoresRisco->UsuarioContributivo_PreferenciaPedestre == 'CONDUTOR OU PASSAGEIRO DE ÔNIBUS/VAN' ||
						$value->quadro_multiplo->FatoresRisco->UsuarioContributivo_ImprudenciaPedestre == 'CONDUTOR OU PASSAGEIRO DE ÔNIBUS/VAN' ||
						$value->quadro_multiplo->FatoresRisco->UsuarioContributivo_Capacete == 'CONDUTOR OU PASSAGEIRO DE ÔNIBUS/VAN' ||
						$value->quadro_multiplo->FatoresRisco->UsuarioContributivo_CintoSeguranca == 'CONDUTOR OU PASSAGEIRO DE ÔNIBUS/VAN';
					})
					->count();
					$result[$key]['PEDESTRE'] = $base->where($linha,$key)
					->filter(function ($value, $key) {
						return $value->quadro_multiplo->FatoresRisco->UsuarioContributivo_Velocidade == 'PEDESTRE' || 
						$value->quadro_multiplo->FatoresRisco->UsuarioContributivo_Alcool == 'PEDESTRE' || 
						$value->quadro_multiplo->FatoresRisco->UsuarioContributivo_Veiculo == 'PEDESTRE' || 
						$value->quadro_multiplo->FatoresRisco->UsuarioContributivo_Fadiga == 'PEDESTRE' ||
						$value->quadro_multiplo->FatoresRisco->UsuarioContributivo_Drogas == 'PEDESTRE' ||
						$value->quadro_multiplo->FatoresRisco->UsuarioContributivo_Distacao == 'PEDESTRE' ||
						$value->quadro_multiplo->FatoresRisco->UsuarioContributivo_AvancarSinal == 'PEDESTRE' ||
						$value->quadro_multiplo->FatoresRisco->UsuarioContributivo_CondutorSemHabilitacao == 'PEDESTRE' ||
						$value->quadro_multiplo->FatoresRisco->UsuarioContributivo_LocalProibido == 'PEDESTRE' ||
						$value->quadro_multiplo->FatoresRisco->UsuarioContributivo_LocalImproprio == 'PEDESTRE' ||
						$value->quadro_multiplo->FatoresRisco->UsuarioContributivo_MudancaFaixa == 'PEDESTRE' ||
						$value->quadro_multiplo->FatoresRisco->UsuarioContributivo_DistanciaMinima == 'PEDESTRE' ||
						$value->quadro_multiplo->FatoresRisco->UsuarioContributivo_Preferencia == 'PEDESTRE' ||
						$value->quadro_multiplo->FatoresRisco->UsuarioContributivo_PreferenciaPedestre == 'PEDESTRE' ||
						$value->quadro_multiplo->FatoresRisco->UsuarioContributivo_ImprudenciaPedestre == 'PEDESTRE' ||
						$value->quadro_multiplo->FatoresRisco->UsuarioContributivo_Capacete == 'PEDESTRE' ||
						$value->quadro_multiplo->FatoresRisco->UsuarioContributivo_CintoSeguranca == 'PEDESTRE';
					})
					->count();
				}
			}
		}
		//apenas pares linkados no sim
		if ($request['base'] == "PARES_SIM") {
			$base = LinkagemSim::where('Ano',$request->Ano)	
			->where('CodCidade',$request->CodCidade)
			->where('Trimestre',$request->Trimestre)
			->where('ParVerdadeiro',1)
			->get();
			if ($request['linhas'] == "Gravidade") {
				$base->load('vitima');
				$linha = 'vitima.GravidadeLesao';
				$linhas = $base->groupBy('vitima.GravidadeLesao');	
			}elseif ($request['linhas'] == "DiaSemana") {
				$base->load('quadro_multiplo');
				$linha = 'quadro_multiplo.DiaSemana';
				$linhas = $base->groupBy('quadro_multiplo.DiaSemana');
			}elseif ($request['linhas'] == "Horario") {
				$base->load('quadro_multiplo');
				$linha = 'quadro_multiplo.HoraAcidente';
				$linhas = $base->groupBy('quadro_multiplo.HoraAcidente');
			}elseif ($request['linhas'] == "QtdVitimas") {
				$base->load('quadro_multiplo');
				$linha = 'quadro_multiplo.QtdVitimas';
				$linhas = $base->groupBy('quadro_multiplo.QtdVitimas');
			}elseif ($request['linhas'] == "CausaBase") {
				$base->load('sim');
				$linha = 'sim.CAUSABAS';
				$linhas = $base->groupBy('sim.CAUSABAS');
			}elseif ($request['linhas'] == "FonteDados") {	
				$base->load('quadro_multiplo');
				$linha = 'quadro_multiplo.FonteDados';
				$linhas = $base->groupBy('quadro_multiplo.FonteDados');	
			}elseif ($request['linhas'] == "TipoAcidente") {
				$base->load('quadro_multiplo');
				$linha = 'quadro_multiplo.TipoAcidente';
				$linhas = $base->groupBy('quadro_multiplo.TipoAcidente');	
			}elseif ($request['linhas'] == "FaixaEtaria") {
				$base->load('vitima');
				$linha = 'vitima.FaixaEtaria';
				$linhas = $base->groupBy('vitima.FaixaEtaria');	
			}elseif ($request['linhas'] == "Sexo") {
				$base->load('vitima');
				$linha = 'vitima.Sexo';
				$linhas = $base->groupBy('vitima.Sexo');
			}elseif ($request['linhas'] == "Condicao") {
				$base->load('vitima');
				$linha = 'vitima.CondicaoVitima';
				$linhas = $base->groupBy('vitima.CondicaoVitima');
			}elseif ($request['linhas'] == "MeioTransporte") {
				$base->load('vitima');
				$linha = 'vitima.MeioTransporte';
				$linhas = $base->groupBy('vitima.MeioTransporte');
			}elseif ($request['linhas'] == "MunicipioRes") {
				$base->load('vitima');
				$linha = 'vitima.MunicipioResidencia';
				$linhas = $base->groupBy('vitima.MunicipioResidencia');
			}
			//colunas
			if ($request['colunas'] == "Frequencia") {
				$result = [];
				foreach ($linhas as $key => $value) {					
					$result[$key] = $value->count();
				}
			}

			if ($request['colunas'] == "Gravidade") {
				$result = [];				
				$colunas = $base->groupBy('vitima.GravidadeLesao');
				$colunas = array_keys($colunas->toArray());
				foreach ($linhas as $key => $value) {			
					foreach ($colunas as $coluna) {
						$result[$key][$coluna] = $value->where('vitima.GravidadeLesao',$coluna)->count();
					}				
				}
			}
			if ($request['colunas'] == "Condicao") {
				$result = [];
				$colunas = $base->groupBy('vitima.CondicaoVitima');
				$colunas = array_keys($colunas->toArray());
				foreach ($linhas as $key => $value) {			
					foreach ($colunas as $coluna) {
						$result[$key][$coluna] = $value->where('vitima.CondicaoVitima',$coluna)->count();
					}				
				}
			}
			if ($request['colunas'] == "MeioTransporte") {
				$result = [];
				$colunas = $base->groupBy('vitima.MeioTransporte');
				$colunas = array_keys($colunas->toArray());
				foreach ($linhas as $key => $value) {			
					foreach ($colunas as $coluna) {
						$result[$key][$coluna] = $value->where('vitima.MeioTransporte',$coluna)->count();
					}				
				}
			}
			if ($request['colunas'] == "Sexo") {
				$result = [];
				$colunas = $base->groupBy('vitima.Sexo');
				$colunas = array_keys($colunas->toArray());
				foreach ($linhas as $key => $value) {			
					foreach ($colunas as $coluna) {
						$result[$key][$coluna] = $value->where('vitima.Sexo',$coluna)->count();
					}				
				}
			}


			if ($request['colunas'] == "FaixaEtaria") {
				$result = [];
				$colunas = $base->groupBy('vitima.FaixaEtaria');
				$colunas = array_keys($colunas->toArray());
				foreach ($linhas as $key => $value) {			
					foreach ($colunas as $coluna) {
						$result[$key][$coluna] = $value->where('vitima.FaixaEtaria',$coluna)->count();
					}				
				}
			}
			if ($request['colunas'] == "FonteDados") {
				$base = $base->load('quadro_multiplo');
				$result = [];
				$colunas = $base->groupBy('quadro_multiplo.FonteDados');
				$colunas = array_keys($colunas->toArray());
				foreach ($linhas as $key => $value) {			
					foreach ($colunas as $coluna) {
						$result[$key][$coluna] = $value->where('quadro_multiplo.FonteDados',$coluna)->count();
					}				
				}

			}
			if ($request['colunas'] == "Horario") {
				$base = $base->load('quadro_multiplo');
				$result = [];
				$colunas = $base->groupBy('quadro_multiplo.HoraAcidente');			
				$colunas = array_keys($colunas->toArray());
				foreach ($linhas as $key => $value) {		
					foreach ($colunas as $coluna) {
						$result[$key][$coluna] = $value->where('quadro_multiplo.HoraAcidente',$coluna)->count();
					}				
				}

			}
			if ($request['colunas'] == "Dia") {
				$base = $base->load('quadro_multiplo');
				$result = [];
				$colunas = $base->groupBy('quadro_multiplo.DiaSemana');			
				$colunas = array_keys($colunas->toArray());
				foreach ($linhas as $key => $value) {	
					foreach ($colunas as $coluna) {
						$result[$key][$coluna] = $value->where('quadro_multiplo.DiaSemana',$coluna)->count();
					}				
				}
			}

			if ($request['colunas'] == "TipoAcidente") {
				$base = $base->load('quadro_multiplo');
				$result = [];
				$colunas = $base->groupBy('quadro_multiplo.TipoAcidente');
				$colunas = array_keys($colunas->toArray());
				foreach ($linhas as $key => $value) {			
					foreach ($colunas as $coluna) {
						$result[$key][$coluna] = $value->where('quadro_multiplo.TipoAcidente',$coluna)->count();
					}				
				}
			}
			if ($request['colunas'] == "FrequenciaProtecaoInadequada") {
				$base = $base->load('quadro_multiplo.FatoresRisco');
				$result = [];
				$base = $base->filter(function ($value, $key) {
					return !empty($value->quadro_multiplo->FatoresRisco);
				});
				foreach ($linhas as $key => $value) {		
					$result[$key]['CintoSeguranca'] = $base->where($linha,$key)
					->sum('quadro_multiplo.FatoresRisco.CintoSeguranca');
					$result[$key]['EquipamentoProtecao'] = $base->where($linha,$key)
					->sum('quadro_multiplo.FatoresRisco.EquipamentoProtecao');
					$result[$key]['GerenciamentoTrauma'] = $base->where($linha,$key)
					->sum('quadro_multiplo.FatoresRisco.GerenciamentoTrauma');
					$result[$key]['ObjetosLateraisVia'] = $base->where($linha,$key)
					->sum('quadro_multiplo.FatoresRisco.ObjetosLateraisVia');
					$result[$key]['Capacete'] = $base->where($linha,$key)
					->sum('quadro_multiplo.FatoresRisco.Capacete');
					$result[$key]['OutraProtecao'] = $base->where($linha,$key)
					->sum('quadro_multiplo.FatoresRisco.outra_protecao');
				}
			}
			if ($request['colunas'] == "ProtecaoInadequada") {
				$base = $base->load('quadro_multiplo.FatoresRisco');
				$result = [];
				$base = $base->filter(function ($value, $key) {
					return !empty($value->quadro_multiplo->FatoresRisco);
				});
				foreach ($linhas as $key => $value) {		
					$result[$key]['CintoSeguranca'] = $base->where($linha,$key)->sum('quadro_multiplo.FatoresRisco.CintoSeguranca');
					$result[$key]['EquipamentoProtecao'] = $base->where($linha,$key)->sum('quadro_multiplo.FatoresRisco.EquipamentoProtecao');
					$result[$key]['GerenciamentoTrauma'] = $base->where($linha,$key)->sum('quadro_multiplo.FatoresRisco.GerenciamentoTrauma');
					$result[$key]['ObjetosLateraisVia'] = $base->where($linha,$key)->sum('quadro_multiplo.FatoresRisco.ObjetosLateraisVia');
					$result[$key]['Capacete'] = $base->where($linha,$key)->sum('quadro_multiplo.FatoresRisco.Capacete');
					$result[$key]['OutraProtecao'] = $base->where($linha,$key)->sum('quadro_multiplo.FatoresRisco.outra_protecao');
				}
			}
			if ($request['colunas'] == "FatorRisco") {
				$base = $base->load('quadro_multiplo.FatoresRisco');
				$result = [];
				$base = $base->filter(function ($value, $key) {
					return !empty($value->quadro_multiplo->FatoresRisco);
				});
				foreach ($linhas as $key => $value) {		
					$result[$key]['Velocidade'] = $base->where($linha,$key)->sum('quadro_multiplo.FatoresRisco.Velocidade');
					$result[$key]['Alcool'] = $base->where($linha,$key)->sum('quadro_multiplo.FatoresRisco.Alcool');
					$result[$key]['Infraestrutura'] = $base->where($linha,$key)->sum('quadro_multiplo.FatoresRisco.Infraestrutura');
					$result[$key]['Drogas'] = $base->where($linha,$key)->sum('quadro_multiplo.FatoresRisco.Drogas');
					$result[$key]['Fadiga'] = $base->where($linha,$key)->sum('quadro_multiplo.FatoresRisco.Fadiga');
					$result[$key]['Distacao'] = $base->where($linha,$key)->sum('quadro_multiplo.FatoresRisco.Distacao');
					$result[$key]['Visibilidade'] = $base->where($linha,$key)->sum('quadro_multiplo.FatoresRisco.Visibilidade');
				}
			}
			if ($request['colunas'] == "FrequenciaFatorRisco") {
				$base = $base->load('quadro_multiplo.FatoresRisco');
				$result = [];
				$base = $base->filter(function ($value, $key) {
					return !empty($value->quadro_multiplo->FatoresRisco);
				});
				foreach ($linhas as $key => $value) {		
					$result[$key]['Velocidade'] = $base->where($linha,$key)
					->where('quadro_multiplo.FatoresRisco.Velocidade','>',0)
					->count();
					$result[$key]['Alcool'] = $base->where($linha,$key)->where('quadro_multiplo.FatoresRisco.Alcool','>',0)
					->count();
					$result[$key]['Infraestrutura'] = $base->where($linha,$key)->where('quadro_multiplo.FatoresRisco.Infraestrutura','>',0)
					->count();
					$result[$key]['Drogas'] = $base->where($linha,$key)->where('quadro_multiplo.FatoresRisco.Drogas','>',0)
					->count();
					$result[$key]['Fadiga'] = $base->where($linha,$key)->where('quadro_multiplo.FatoresRisco.Fadiga','>',0)
					->count();
					$result[$key]['Distacao'] = $base->where($linha,$key)->where('quadro_multiplo.FatoresRisco.Distacao','>',0)
					->count();
					$result[$key]['Visibilidade'] = $base->where($linha,$key)->where('quadro_multiplo.FatoresRisco.Visibilidade','>',0)
					->count();
				}
			}
			if ($request['colunas'] == "FrequenciaCondutaRisco") {
				$base = $base->load('quadro_multiplo.FatoresRisco');
				$result = [];
				$base = $base->filter(function ($value, $key) {
					return !empty($value->quadro_multiplo->FatoresRisco);
				});
				foreach ($linhas as $key => $value) {		
					$result[$key]['AvancarSinal'] = $base->where($linha,$key)
					->where('quadro_multiplo.FatoresRisco.AvancarSinal','>',0)
					->count();
					$result[$key]['CondutorSemHabilitacao'] = $base->where($linha,$key)
					->where('quadro_multiplo.FatoresRisco.CondutorSemHabilitacao','>',0)
					->count();
					$result[$key]['LocalProibido'] = $base->where($linha,$key)
					->where('quadro_multiplo.FatoresRisco.LocalProibido','>',0)
					->count();
					$result[$key]['LocalImproprio'] = $base->where($linha,$key)
					->where('quadro_multiplo.FatoresRisco.LocalImproprio','>',0)
					->count();
					$result[$key]['MudancaFaixa'] = $base->where($linha,$key)
					->where('quadro_multiplo.FatoresRisco.MudancaFaixa','>',0)
					->count();
					$result[$key]['DistanciaMinima'] = $base->where($linha,$key)
					->where('quadro_multiplo.FatoresRisco.DistanciaMinima','>',0)
					->count();
					$result[$key]['Preferencia'] = $base->where($linha,$key)
					->where('quadro_multiplo.FatoresRisco.Preferencia','>',0)
					->count();
					$result[$key]['PreferenciaPedestre'] = $base->where($linha,$key)
					->where('quadro_multiplo.FatoresRisco.PreferenciaPedestre','>',0)
					->count();
					$result[$key]['ImprudenciaPedestre'] = $base->where($linha,$key)
					->where('quadro_multiplo.FatoresRisco.ImprudenciaPedestre','>',0)
					->count();
				}
			}
			if ($request['colunas'] == "CondutaRisco") {
				$base = $base->load('quadro_multiplo.FatoresRisco');
				$result = [];
				$base = $base->filter(function ($value, $key) {
					return !empty($value->quadro_multiplo->FatoresRisco);
				});
				foreach ($linhas as $key => $value) {		
					$result[$key]['AvancarSinal'] = $base->where($linha,$key)->sum('quadro_multiplo.FatoresRisco.AvancarSinal');
					$result[$key]['CondutorSemHabilitacao'] = $base->where($linha,$key)->sum('quadro_multiplo.FatoresRisco.CondutorSemHabilitacao');
					$result[$key]['LocalProibido'] = $base->where($linha,$key)->sum('quadro_multiplo.FatoresRisco.LocalProibido');
					$result[$key]['LocalImproprio'] = $base->where($linha,$key)->sum('quadro_multiplo.FatoresRisco.LocalImproprio');
					$result[$key]['MudancaFaixa'] = $base->where($linha,$key)->sum('quadro_multiplo.FatoresRisco.MudancaFaixa');
					$result[$key]['DistanciaMinima'] = $base->where($linha,$key)->sum('quadro_multiplo.FatoresRisco.DistanciaMinima');
					$result[$key]['Preferencia'] = $base->where($linha,$key)->sum('quadro_multiplo.FatoresRisco.Preferencia');
					$result[$key]['PreferenciaPedestre'] = $base->where($linha,$key)->sum('quadro_multiplo.FatoresRisco.PreferenciaPedestre');
					$result[$key]['ImprudenciaPedestre'] = $base->where($linha,$key)->sum('quadro_multiplo.FatoresRisco.ImprudenciaPedestre');
				}
			}

			if ($request['colunas'] == "UsuarioContributivo") {
				$base = $base->load('quadro_multiplo.FatoresRisco');
				$result = [];
				$base = $base->filter(function ($value, $key) {
					return !empty($value->quadro_multiplo->FatoresRisco);
				});
				foreach ($linhas as $key => $value) {		
					$result[$key]['CONDUTOR OU PASSAGEIRO DE VEÍCULO LEVE'] = $base->where($linha,$key)
					->filter(function ($value, $key) {
						return $value->quadro_multiplo->FatoresRisco->UsuarioContributivo_Velocidade == 'CONDUTOR OU PASSAGEIRO DE VEÍCULO LEVE' || 
						$value->quadro_multiplo->FatoresRisco->UsuarioContributivo_Alcool == 'CONDUTOR OU PASSAGEIRO DE VEÍCULO LEVE' || 
						$value->quadro_multiplo->FatoresRisco->UsuarioContributivo_Veiculo == 'CONDUTOR OU PASSAGEIRO DE VEÍCULO LEVE' || 
						$value->quadro_multiplo->FatoresRisco->UsuarioContributivo_Fadiga == 'CONDUTOR OU PASSAGEIRO DE VEÍCULO LEVE' ||
						$value->quadro_multiplo->FatoresRisco->UsuarioContributivo_Drogas == 'CONDUTOR OU PASSAGEIRO DE VEÍCULO LEVE' ||
						$value->quadro_multiplo->FatoresRisco->UsuarioContributivo_Distacao == 'CONDUTOR OU PASSAGEIRO DE VEÍCULO LEVE' ||
						$value->quadro_multiplo->FatoresRisco->UsuarioContributivo_AvancarSinal == 'CONDUTOR OU PASSAGEIRO DE VEÍCULO LEVE' ||
						$value->quadro_multiplo->FatoresRisco->UsuarioContributivo_CondutorSemHabilitacao == 'CONDUTOR OU PASSAGEIRO DE VEÍCULO LEVE' ||
						$value->quadro_multiplo->FatoresRisco->UsuarioContributivo_LocalProibido == 'CONDUTOR OU PASSAGEIRO DE VEÍCULO LEVE' ||
						$value->quadro_multiplo->FatoresRisco->UsuarioContributivo_LocalImproprio == 'CONDUTOR OU PASSAGEIRO DE VEÍCULO LEVE' ||
						$value->quadro_multiplo->FatoresRisco->UsuarioContributivo_MudancaFaixa == 'CONDUTOR OU PASSAGEIRO DE VEÍCULO LEVE' ||
						$value->quadro_multiplo->FatoresRisco->UsuarioContributivo_DistanciaMinima == 'CONDUTOR OU PASSAGEIRO DE VEÍCULO LEVE' ||
						$value->quadro_multiplo->FatoresRisco->UsuarioContributivo_Preferencia == 'CONDUTOR OU PASSAGEIRO DE VEÍCULO LEVE' ||
						$value->quadro_multiplo->FatoresRisco->UsuarioContributivo_PreferenciaPedestre == 'CONDUTOR OU PASSAGEIRO DE VEÍCULO LEVE' ||
						$value->quadro_multiplo->FatoresRisco->UsuarioContributivo_ImprudenciaPedestre == 'CONDUTOR OU PASSAGEIRO DE VEÍCULO LEVE' ||
						$value->quadro_multiplo->FatoresRisco->UsuarioContributivo_Capacete == 'CONDUTOR OU PASSAGEIRO DE VEÍCULO LEVE' ||
						$value->quadro_multiplo->FatoresRisco->UsuarioContributivo_CintoSeguranca == 'CONDUTOR OU PASSAGEIRO DE VEÍCULO LEVE';
					})
					->count();	
					$result[$key]['CONDUTOR OU PASSAGEIRO DE VEÍCULO PESADO'] = $base->where($linha,$key)
					->filter(function ($value, $key) {
						return $value->quadro_multiplo->FatoresRisco->UsuarioContributivo_Velocidade == 'CONDUTOR OU PASSAGEIRO DE VEÍCULO PESADO' || 
						$value->quadro_multiplo->FatoresRisco->UsuarioContributivo_Alcool == 'CONDUTOR OU PASSAGEIRO DE VEÍCULO PESADO' || 
						$value->quadro_multiplo->FatoresRisco->UsuarioContributivo_Veiculo == 'CONDUTOR OU PASSAGEIRO DE VEÍCULO PESADO' || 
						$value->quadro_multiplo->FatoresRisco->UsuarioContributivo_Fadiga == 'CONDUTOR OU PASSAGEIRO DE VEÍCULO PESADO' ||
						$value->quadro_multiplo->FatoresRisco->UsuarioContributivo_Drogas == 'CONDUTOR OU PASSAGEIRO DE VEÍCULO PESADO' ||
						$value->quadro_multiplo->FatoresRisco->UsuarioContributivo_Distacao == 'CONDUTOR OU PASSAGEIRO DE VEÍCULO PESADO' ||
						$value->quadro_multiplo->FatoresRisco->UsuarioContributivo_AvancarSinal == 'CONDUTOR OU PASSAGEIRO DE VEÍCULO PESADO' ||
						$value->quadro_multiplo->FatoresRisco->UsuarioContributivo_CondutorSemHabilitacao == 'CONDUTOR OU PASSAGEIRO DE VEÍCULO PESADO' ||
						$value->quadro_multiplo->FatoresRisco->UsuarioContributivo_LocalProibido == 'CONDUTOR OU PASSAGEIRO DE VEÍCULO PESADO' ||
						$value->quadro_multiplo->FatoresRisco->UsuarioContributivo_LocalImproprio == 'CONDUTOR OU PASSAGEIRO DE VEÍCULO PESADO' ||
						$value->quadro_multiplo->FatoresRisco->UsuarioContributivo_MudancaFaixa == 'CONDUTOR OU PASSAGEIRO DE VEÍCULO PESADO' ||
						$value->quadro_multiplo->FatoresRisco->UsuarioContributivo_DistanciaMinima == 'CONDUTOR OU PASSAGEIRO DE VEÍCULO PESADO' ||
						$value->quadro_multiplo->FatoresRisco->UsuarioContributivo_Preferencia == 'CONDUTOR OU PASSAGEIRO DE VEÍCULO PESADO' ||
						$value->quadro_multiplo->FatoresRisco->UsuarioContributivo_PreferenciaPedestre == 'CONDUTOR OU PASSAGEIRO DE VEÍCULO PESADO' ||
						$value->quadro_multiplo->FatoresRisco->UsuarioContributivo_ImprudenciaPedestre == 'CONDUTOR OU PASSAGEIRO DE VEÍCULO PESADO' ||
						$value->quadro_multiplo->FatoresRisco->UsuarioContributivo_Capacete == 'CONDUTOR OU PASSAGEIRO DE VEÍCULO PESADO' ||
						$value->quadro_multiplo->FatoresRisco->UsuarioContributivo_CintoSeguranca == 'CONDUTOR OU PASSAGEIRO DE VEÍCULO PESADO';
					})
					->count();
					$result[$key]['CONDUTOR OU PASSAGEIRO DE MOTO'] = $base->where($linha,$key)
					->filter(function ($value, $key) {
						return $value->quadro_multiplo->FatoresRisco->UsuarioContributivo_Velocidade == 'CONDUTOR OU PASSAGEIRO DE MOTO' || 
						$value->quadro_multiplo->FatoresRisco->UsuarioContributivo_Alcool == 'CONDUTOR OU PASSAGEIRO DE MOTO' || 
						$value->quadro_multiplo->FatoresRisco->UsuarioContributivo_Veiculo == 'CONDUTOR OU PASSAGEIRO DE MOTO' || 
						$value->quadro_multiplo->FatoresRisco->UsuarioContributivo_Fadiga == 'CONDUTOR OU PASSAGEIRO DE MOTO' ||
						$value->quadro_multiplo->FatoresRisco->UsuarioContributivo_Drogas == 'CONDUTOR OU PASSAGEIRO DE MOTO' ||
						$value->quadro_multiplo->FatoresRisco->UsuarioContributivo_Distacao == 'CONDUTOR OU PASSAGEIRO DE MOTO' ||
						$value->quadro_multiplo->FatoresRisco->UsuarioContributivo_AvancarSinal == 'CONDUTOR OU PASSAGEIRO DE MOTO' ||
						$value->quadro_multiplo->FatoresRisco->UsuarioContributivo_CondutorSemHabilitacao == 'CONDUTOR OU PASSAGEIRO DE MOTO' ||
						$value->quadro_multiplo->FatoresRisco->UsuarioContributivo_LocalProibido == 'CONDUTOR OU PASSAGEIRO DE MOTO' ||
						$value->quadro_multiplo->FatoresRisco->UsuarioContributivo_LocalImproprio == 'CONDUTOR OU PASSAGEIRO DE MOTO' ||
						$value->quadro_multiplo->FatoresRisco->UsuarioContributivo_MudancaFaixa == 'CONDUTOR OU PASSAGEIRO DE MOTO' ||
						$value->quadro_multiplo->FatoresRisco->UsuarioContributivo_DistanciaMinima == 'CONDUTOR OU PASSAGEIRO DE MOTO' ||
						$value->quadro_multiplo->FatoresRisco->UsuarioContributivo_Preferencia == 'CONDUTOR OU PASSAGEIRO DE MOTO' ||
						$value->quadro_multiplo->FatoresRisco->UsuarioContributivo_PreferenciaPedestre == 'CONDUTOR OU PASSAGEIRO DE MOTO' ||
						$value->quadro_multiplo->FatoresRisco->UsuarioContributivo_ImprudenciaPedestre == 'CONDUTOR OU PASSAGEIRO DE MOTO' ||
						$value->quadro_multiplo->FatoresRisco->UsuarioContributivo_Capacete == 'CONDUTOR OU PASSAGEIRO DE MOTO' ||
						$value->quadro_multiplo->FatoresRisco->UsuarioContributivo_CintoSeguranca == 'CONDUTOR OU PASSAGEIRO DE MOTO';
					})
					->count();
					$result[$key]['CONDUTOR OU PASSAGEIRO DE BICICLETA'] = $base->where($linha,$key)
					->filter(function ($value, $key) {
						return $value->quadro_multiplo->FatoresRisco->UsuarioContributivo_Velocidade == 'CONDUTOR OU PASSAGEIRO DE BICICLETA' || 
						$value->quadro_multiplo->FatoresRisco->UsuarioContributivo_Alcool == 'CONDUTOR OU PASSAGEIRO DE BICICLETA' || 
						$value->quadro_multiplo->FatoresRisco->UsuarioContributivo_Veiculo == 'CONDUTOR OU PASSAGEIRO DE BICICLETA' || 
						$value->quadro_multiplo->FatoresRisco->UsuarioContributivo_Fadiga == 'CONDUTOR OU PASSAGEIRO DE BICICLETA' ||
						$value->quadro_multiplo->FatoresRisco->UsuarioContributivo_Drogas == 'CONDUTOR OU PASSAGEIRO DE BICICLETA' ||
						$value->quadro_multiplo->FatoresRisco->UsuarioContributivo_Distacao == 'CONDUTOR OU PASSAGEIRO DE BICICLETA' ||
						$value->quadro_multiplo->FatoresRisco->UsuarioContributivo_AvancarSinal == 'CONDUTOR OU PASSAGEIRO DE BICICLETA' ||
						$value->quadro_multiplo->FatoresRisco->UsuarioContributivo_CondutorSemHabilitacao == 'CONDUTOR OU PASSAGEIRO DE BICICLETA' ||
						$value->quadro_multiplo->FatoresRisco->UsuarioContributivo_LocalProibido == 'CONDUTOR OU PASSAGEIRO DE BICICLETA' ||
						$value->quadro_multiplo->FatoresRisco->UsuarioContributivo_LocalImproprio == 'CONDUTOR OU PASSAGEIRO DE BICICLETA' ||
						$value->quadro_multiplo->FatoresRisco->UsuarioContributivo_MudancaFaixa == 'CONDUTOR OU PASSAGEIRO DE BICICLETA' ||
						$value->quadro_multiplo->FatoresRisco->UsuarioContributivo_DistanciaMinima == 'CONDUTOR OU PASSAGEIRO DE BICICLETA' ||
						$value->quadro_multiplo->FatoresRisco->UsuarioContributivo_Preferencia == 'CONDUTOR OU PASSAGEIRO DE BICICLETA' ||
						$value->quadro_multiplo->FatoresRisco->UsuarioContributivo_PreferenciaPedestre == 'CONDUTOR OU PASSAGEIRO DE BICICLETA' ||
						$value->quadro_multiplo->FatoresRisco->UsuarioContributivo_ImprudenciaPedestre == 'CONDUTOR OU PASSAGEIRO DE BICICLETA' ||
						$value->quadro_multiplo->FatoresRisco->UsuarioContributivo_Capacete == 'CONDUTOR OU PASSAGEIRO DE BICICLETA' ||
						$value->quadro_multiplo->FatoresRisco->UsuarioContributivo_CintoSeguranca == 'CONDUTOR OU PASSAGEIRO DE BICICLETA';
					})
					->count();
					$result[$key]['CONDUTOR OU PASSAGEIRO DE ÔNIBUS/VAN'] = $base->where($linha,$key)
					->filter(function ($value, $key) {
						return $value->quadro_multiplo->FatoresRisco->UsuarioContributivo_Velocidade == 'CONDUTOR OU PASSAGEIRO DE ÔNIBUS/VAN' || 
						$value->quadro_multiplo->FatoresRisco->UsuarioContributivo_Alcool == 'CONDUTOR OU PASSAGEIRO DE ÔNIBUS/VAN' || 
						$value->quadro_multiplo->FatoresRisco->UsuarioContributivo_Veiculo == 'CONDUTOR OU PASSAGEIRO DE ÔNIBUS/VAN' || 
						$value->quadro_multiplo->FatoresRisco->UsuarioContributivo_Fadiga == 'CONDUTOR OU PASSAGEIRO DE ÔNIBUS/VAN' ||
						$value->quadro_multiplo->FatoresRisco->UsuarioContributivo_Drogas == 'CONDUTOR OU PASSAGEIRO DE ÔNIBUS/VAN' ||
						$value->quadro_multiplo->FatoresRisco->UsuarioContributivo_Distacao == 'CONDUTOR OU PASSAGEIRO DE ÔNIBUS/VAN' ||
						$value->quadro_multiplo->FatoresRisco->UsuarioContributivo_AvancarSinal == 'CONDUTOR OU PASSAGEIRO DE ÔNIBUS/VAN' ||
						$value->quadro_multiplo->FatoresRisco->UsuarioContributivo_CondutorSemHabilitacao == 'CONDUTOR OU PASSAGEIRO DE ÔNIBUS/VAN' ||
						$value->quadro_multiplo->FatoresRisco->UsuarioContributivo_LocalProibido == 'CONDUTOR OU PASSAGEIRO DE ÔNIBUS/VAN' ||
						$value->quadro_multiplo->FatoresRisco->UsuarioContributivo_LocalImproprio == 'CONDUTOR OU PASSAGEIRO DE ÔNIBUS/VAN' ||
						$value->quadro_multiplo->FatoresRisco->UsuarioContributivo_MudancaFaixa == 'CONDUTOR OU PASSAGEIRO DE ÔNIBUS/VAN' ||
						$value->quadro_multiplo->FatoresRisco->UsuarioContributivo_DistanciaMinima == 'CONDUTOR OU PASSAGEIRO DE ÔNIBUS/VAN' ||
						$value->quadro_multiplo->FatoresRisco->UsuarioContributivo_Preferencia == 'CONDUTOR OU PASSAGEIRO DE ÔNIBUS/VAN' ||
						$value->quadro_multiplo->FatoresRisco->UsuarioContributivo_PreferenciaPedestre == 'CONDUTOR OU PASSAGEIRO DE ÔNIBUS/VAN' ||
						$value->quadro_multiplo->FatoresRisco->UsuarioContributivo_ImprudenciaPedestre == 'CONDUTOR OU PASSAGEIRO DE ÔNIBUS/VAN' ||
						$value->quadro_multiplo->FatoresRisco->UsuarioContributivo_Capacete == 'CONDUTOR OU PASSAGEIRO DE ÔNIBUS/VAN' ||
						$value->quadro_multiplo->FatoresRisco->UsuarioContributivo_CintoSeguranca == 'CONDUTOR OU PASSAGEIRO DE ÔNIBUS/VAN';
					})
					->count();
					$result[$key]['PEDESTRE'] = $base->where($linha,$key)
					->filter(function ($value, $key) {
						return $value->quadro_multiplo->FatoresRisco->UsuarioContributivo_Velocidade == 'PEDESTRE' || 
						$value->quadro_multiplo->FatoresRisco->UsuarioContributivo_Alcool == 'PEDESTRE' || 
						$value->quadro_multiplo->FatoresRisco->UsuarioContributivo_Veiculo == 'PEDESTRE' || 
						$value->quadro_multiplo->FatoresRisco->UsuarioContributivo_Fadiga == 'PEDESTRE' ||
						$value->quadro_multiplo->FatoresRisco->UsuarioContributivo_Drogas == 'PEDESTRE' ||
						$value->quadro_multiplo->FatoresRisco->UsuarioContributivo_Distacao == 'PEDESTRE' ||
						$value->quadro_multiplo->FatoresRisco->UsuarioContributivo_AvancarSinal == 'PEDESTRE' ||
						$value->quadro_multiplo->FatoresRisco->UsuarioContributivo_CondutorSemHabilitacao == 'PEDESTRE' ||
						$value->quadro_multiplo->FatoresRisco->UsuarioContributivo_LocalProibido == 'PEDESTRE' ||
						$value->quadro_multiplo->FatoresRisco->UsuarioContributivo_LocalImproprio == 'PEDESTRE' ||
						$value->quadro_multiplo->FatoresRisco->UsuarioContributivo_MudancaFaixa == 'PEDESTRE' ||
						$value->quadro_multiplo->FatoresRisco->UsuarioContributivo_DistanciaMinima == 'PEDESTRE' ||
						$value->quadro_multiplo->FatoresRisco->UsuarioContributivo_Preferencia == 'PEDESTRE' ||
						$value->quadro_multiplo->FatoresRisco->UsuarioContributivo_PreferenciaPedestre == 'PEDESTRE' ||
						$value->quadro_multiplo->FatoresRisco->UsuarioContributivo_ImprudenciaPedestre == 'PEDESTRE' ||
						$value->quadro_multiplo->FatoresRisco->UsuarioContributivo_Capacete == 'PEDESTRE' ||
						$value->quadro_multiplo->FatoresRisco->UsuarioContributivo_CintoSeguranca == 'PEDESTRE';
					})
					->count();
				}
			}
		}
		//apenas dados do sih
		if ($request['base'] == "SIH") {
			$base = Sih::where('Ano',$request->Ano)	
			->where('CodCidade',$request->CodCidade)
			->where('Trimestre',$request->Trimestre)			
			->get();
			if ($request['linhas'] == "Sexo") {
				$linhas = $base->groupBy('SEXO');
			}
			if ($request['linhas'] == "FaixaEtaria") {
				$linhas = $base->groupBy('faixa_etaria');
			}
						//colunas
			if ($request['colunas'] == "Frequencia") {
				$result = [];
				foreach ($linhas as $key => $value) {					
					$result[$key] = $value->count();
				}
			}
		}
		//apenas dados do sih
		if ($request['base'] == "SIM") {
			$base = Sim::where('Ano',$request->Ano)	
			->where('CodCidade',$request->CodCidade)
			->where('Trimestre',$request->Trimestre)			
			->get();
			if ($request['linhas'] == "Sexo") {
				$linhas = $base->groupBy('SEXO');
			}
			if ($request['linhas'] == "FaixaEtaria") {
				$linhas = $base->groupBy('faixa_etaria');
			}
						//colunas
			if ($request['colunas'] == "Frequencia") {
				$result = [];
				foreach ($linhas as $key => $value) {					
					$result[$key] = $value->count();
				}
			}
		}

		



		$i = 0;
		if(!isset($result)){
			\Log::error($request->all());
		}
		if (isset($result) && !empty($result) && is_array($result) || is_object($result)) {

			echo '<table id="table_resultados" class="table table-bordered table-hover">
			<thead class="table-head">
			<tr>';
			$linhas = array_keys($result);
			$colunas = $result[$linhas[0]];
			
			if ($request['colunas'] != "Frequencia") {				
				foreach ($colunas as $key => $col) {
					if ($i == 0) {
						echo '<th>' . $request['colunas'] . '</th>';
						echo '<th class="nomes">' . preg_replace('/\s\s+/', ' ', tirarAcentos($key)). '</th>';
					} else {
						echo '<th class="nomes">' . preg_replace('/\s\s+/', ' ', tirarAcentos($key)). '</th>';

					}
					$i++;
				}
				echo '<th class="nomes">Total</th>';
			}else{
				
				echo '<th>' . $request['linhas'] . '</th>';
				echo '<th class="nomes">FREQUENCIA</th>';
				
			}


			echo '</tr></thead><tbody>';

			$k = 0;
			$j = 0;
			$total = 0;
			$totais = array();
			if ($request['colunas'] != "Frequencia") {
				//dd($result);
				foreach ($result as $linha => $valor) {
					$k++;
					$total = 0;
					echo '<tr>';
					echo '<td class="GRUPOS">' . $linha . '</td>';
					//dd($valor);
					foreach ($valor as $id => $row) {
						if(!empty($id)){
							$j++;
							if ($row == null) {
								$row = 0;
							}
							$total = $total + $row;
							echo '<td class="' . str_replace(' ', '', $id) . '">' . $row . '</td>';
						}

					}
					echo '<td class="Total">' . $total . '</td>';

					if ($k == count($result)) {
						echo '<tr id="Totais"><td class="GRUPOS">TOTAL</td></tr>';						
					}



				}
			}else{
				foreach ($linhas as $linha) {
					$total = $total + $result[$linha];
					echo '<tr>';
					echo '<td class="GRUPOS">' . $linha . '</td>';
					echo '<td class="FREQUENCIA">' . $result[$linha] . '</td></tr>';
				}
				echo '<tr id="Totais"><td class="GRUPOS">TOTAL</td>';
			}

			echo '</tbody></table>';
		}else{
			echo 'sem_dados';
		}
		exit;
		if (count($result) > 0) {
			echo json_encode($result);
		}else{
			echo 'sem_dados';
		}
	}

}
