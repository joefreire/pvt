<?php

namespace App\Http\Controllers\Auth;
use App\Models\User;
use App\Models\Cidades;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Http\Request;
use DB;
use Auth;
use Session;
use Illuminate\Support\Facades\Crypt;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = '/';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
    	$this->middleware('web');
    }
    /**
     * Show the application registration form.
     *
     * @return \Illuminate\Http\Response
     */
    public function showRegistrationForm($id = null)
    {
    	if($id != null){
    		$decodeId =  base64_decode($id);
    		$user = User::findOrFail($decodeId)->toArray();

    		if(Auth::user()->tipo <= $user['tipo']){
    			foreach ($user as $key => $value) {
    				Session::flash('_old_input.'.$key, $value);
    			}
    			return view('auth.register')->with('id',$id);
    		}else{
    			return redirect()->route('home')->with('error', 'sem permissão');
    		}
    	}else{
    		$user = Auth::user()->toArray();
    		foreach ($user as $key => $value) {
    			Session::flash('_old_input.'.$key, $value);
    		}
    		return view('auth.register');
    	}

    }
    public function editUser($user, Request $request)
    {
    	$decodeId =  base64_decode($user);
    	$user = User::findOrFail($decodeId);
    	if(Auth::user()->tipo == 2 && $user->cidade->uf != Auth::user()->cidade->uf){
    		return redirect()->route('getUsuarios')->with('error', 'Sem Permissão');
    	}
    	if(Auth::user()->tipo == 3 && $user->cidade->municipio != Auth::user()->cidade->municipio){
    		return redirect()->route('getUsuarios')->with('error', 'Sem Permissão');
    	}
    	if(Auth::user()->tipo == 4 && $user->id != Auth::user()->id){
    		return redirect()->route('getUsuarios')->with('error', 'Sem Permissão');
    	}
    	if(!empty($request->email)){
    		if(strtolower($request->email) != $user->email && !empty(User::where('email', strtolower($request->email))->first())){
    			return redirect()->back()->withInput()->with('error', 'Email já cadastrado para outro usuario');
    		}
    		$cidade = Cidades::where('municipio', $request->cidade)->where('uf',$request->uf)->first();
    		if(empty($cidade)){
    			return redirect()->route('getUsuarios')->with('error', 'Cidade Inválida'); 
    		}
    		$user->CodCidade = $cidade->codigo;
    		$user->email = $request->email;
    		$user->nome = $request->nome;
    		$user->tipo = $request->tipo;
    		$user->instituicao = $request->instituicao;
    		$user->formacao = $request->formacao;
    		$user->telefone = $request->telefone;
    		$user->atividade = $request->atividade;
    		$user->save();
    		return redirect()->route('getUsuarios')->with('success', 'Dados Atualizados');

    	}else{
    		return redirect()->back()->withInput()->with('error', 'Email obrigatório');
    	}

    }
    public function deleteUser($user)
    {
    	$decodeId =  base64_decode($user);
    	$user = User::findOrFail($decodeId);
    	if(Auth::user()->tipo == 2 && $user->cidade->uf != Auth::user()->cidade->uf){
    		return redirect()->route('getUsuarios')->with('error', 'Sem Permissão');
    	}
    	if(Auth::user()->tipo == 3 && $user->cidade->municipio != Auth::user()->cidade->municipio){
    		return redirect()->route('getUsuarios')->with('error', 'Sem Permissão');
    	}
    	if(Auth::user()->tipo == 4){
    		return redirect()->route('getUsuarios')->with('error', 'Sem Permissão');
    	}
    	$user->email = 'old_'.$user->email;
    	$user->deleted_at = \Carbon\Carbon::now();
    	$user->save();
    	return redirect()->route('getUsuarios')->with('success', 'Usuário Deletado');

    }
    public function showNewUserForm()
    {
    	return view('auth.register');
    }
        /**
     * Handle a registration request for the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
        public function register(Request $request)
        {
        	$this->validator($request->all())->validate();
        	$data = $request->all();
        	if(Auth::guest() || $data['tipo'] <  Auth::user()->tipo  || Auth::user()->tipo == '4') {
        		return redirect()->back()->with('error','Sem Permissão');
        	}
        	$password = 'pvt';
        	if(Auth::user()->tipo == 1){
        		$cidade = Cidades::where('municipio', $data['cidade'])->where('uf',$data['uf'])->first();
        	}elseif(Auth::user()->tipo == 2){
        		$cidade = Cidades::find($request->cidade);
        	}else{
        		$cidade = Cidades::find(Auth::user()->CodCidade);
        	}
        	if(Auth::user()->tipo == 1){
        		$data['tipo'] = $data['tipo'];
        	}else{
        		if($data['tipo'] < Auth::user()->tipo){
        			return redirect()->back()->with('error','Você não pode cadastrar essa permissão');
        		}
        	}

        	if(empty($cidade)){
        		return redirect()->back()->with('error','Cidade inválida');
        	}


        	$user = User::create([
        		'nome' => $data['nome'],
        		'email' => $data['email'],
        		'instituicao' => $data['instituicao'],
        		'formacao' => $data['formacao'],
        		'telefone' => $data['telefone'],
        		'atividade' => $data['atividade'],
        		'tipo' => $data['tipo'],
        		'CodCidade' => $cidade->codigo,
        		'reset_senha' => 1,
        		'password' => Hash::make($password),
        	]);
        	if($user){
        		return redirect()->route('getUsuarios')->with('success','Cadastrado');
        	}else{
        		return redirect()->route('getUsuarios')->with('error','Erro ao cadastrar');
        	}
        }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {

    	return Validator::make($data, [
    		'nome' => ['required', 'string', 'max:255'],
    		'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
    	]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\User
     */
    protected function create(array $data)
    {
    	if(Auth::guest() || Auth::user()->tipo < $data['tipo'] || Auth::user()->tipo == '4') {
    		return redirect()->back()->with('error','Sem Permissão');
    	}
    	$password = 'pvt';
    	$cidade = Cidades::where('municipio', $data['cidade'])->where('uf',$data['uf'])->first();
    	if(empty($cidade)){
    		return redirect()->route('getUsuarios')->with('error','Cidade inválida');
    	}

    	$user = User::create([
    		'nome' => $data['nome'],
    		'email' => $data['email'],
    		'instituicao' => $data['instituicao'],
    		'formacao' => $data['formacao'],
    		'telefone' => $data['telefone'],
    		'atividade' => $data['atividade'],
    		'tipo' => $data['tipo'],
    		'CodCidade' => $cidade->codigo,
    		'reset_senha' => 1,
    		'password' => Hash::make($password),
    	]);
    	return $user;

    }
    public function resetPassword($id)
    {

    	if($id != null){
    		$decodeId =  base64_decode($id);
    		$user = User::findOrFail($decodeId);
    		if(Auth::user()->tipo == 1 || 
    			(Auth::user()->tipo == 2 && $user->cidade->uf == Auth::user()->cidade->uf) ||
    			(Auth::user()->tipo == 3 && $user->CodCidade == Auth::user()->CodCidade))
    		{
    			$user->password =  Hash::make('pvt');
    			$user->reset_senha =  1;
    			$user->save();
    			return redirect()->back()->with('success','Senha resetada');
    		}else{
    			return redirect()->route('getUsuarios')->with('error', 'sem permissão');
    		}
    	}else{
    		return redirect()->route('getUsuarios')->with('error', 'Error');
    	}

    }

    /**
     * The user has been registered.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  mixed  $user
     * @return mixed
     */
    protected function registered(Request $request, $user)
    {
        //
    }
}
