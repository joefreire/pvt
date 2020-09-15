<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Cidades;
use Auth;
Use DataTables;
use Session;
use DB;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Crypt;

class AdminController extends Controller
{


	public function usuarios()
	{
		if(Auth::user()->tipo == 4){
			return redirect()->route('home')->with('error','sem permissao');
		}
		return view('admin.listaUsuarios');
	}
	public function getUsuarios(Request $request)
	{
		if ($request->ajax()) {
			$users = User::with('cidade');
			if(Auth::user()->tipo == 2){
				$cidades = Cidades::where('uf',Auth::user()->cidade->uf)->pluck('codigo');
				$users->whereIn('CodCidade',$cidades);
			}elseif(Auth::user()->tipo == 3){
				$users->where('CodCidade',Auth::user()->CodCidade);
			}

			return Datatables::eloquent($users)
			->addColumn('action', function ($lista) {
				$id = base64_encode($lista->id);
				return '<div class="btn-group">
				<button data-toggle="dropdown" class="btn btn-primary btn-xs btn-flat dropdown-toggle"><i class="fa fa-gear"></i> <span class="caret"></span>
				</button>
				<ul class="dropdown-menu pull-right">
				<li>
				<a href="'.route('user.edit',$id).'"><span class="glyphicon glyphicon-file"></span>Editar</a>
				</li>
				<li>
				<a href="'.route('user.reset',$id).'">Resetar Senha</a>
				</li>
				</ul></div>';
			})
			->toJson();
		}
		return view('admin.listaUsuarios');
	}
	public function getCidades(Request $request)
	{
		if(Auth::user()->tipo >= 3){
			return Auth::user()->CodCidade;
		}
		$cidade = Cidades::where('municipio', $request->Cidade)->where('uf', $request->Estado)->first();
		return $cidade->codigo;

	}
	public function auditoria(Request $request)
	{
		if(Auth::user()->tipo != 1){
			return redirect()->route('home')->with('error','sem permissao');
		}
		if ($request->ajax()) {
			$lista = \App\Models\Audits::with('user')
			->leftJoin('users', 'users.id', '=', 'audits.user_id')
			->select('audits.*');

			return Datatables::eloquent($lista)
			->toJson();
		}
		return view('admin.listaLogs');
	}
	public function getCoordenada(Request $request)
	{
		$validator = \Validator::make($request->all(), [
			'endereco' => 'required',
		]);
		if ($validator->fails()) {    
			return response()->json($validator->messages(), 405);
		}
		$curl = curl_init();

		curl_setopt_array($curl, array(
			CURLOPT_URL => "https://us1.locationiq.com/v1/search.php?key=a3634e135b189e&q=".urlencode($request->endereco)."&format=json",
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => "",
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 0,
			CURLOPT_FOLLOWLOCATION => true,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => "GET",
			CURLOPT_HTTPHEADER => array(
				"Cookie: __cfduid=d0d8c8ef5e591df1993e22b03d15ae50f1586091223"
			),
		));

		$response = curl_exec($curl);
		$err = curl_error($curl);
		curl_close($curl);
		if ($err) {
			return response()->json(["cURL Error #:" . $err], 405); 
		} else {
			$resultado = json_decode($response);
			if(!empty($resultado[0])){
				return response()->json($resultado[0], 200); 
			}else{
				return response()->json('vazio', 404); 
			}
			
		}

	}


}
