<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Crypt;
use Session;
use Auth;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/';

    public function login(Request $request)
    {
        $this->validateLogin($request);

        $user = User::where('email', $request->email)->first();
        if(!empty($user) && $user->reset_senha == 1){
            Session::put('email', $user->email);
            return redirect()->route('reset_password')->with('success', 'Você deve cadastrar uma senha para acessar a plataforma');

        }

        // If the class is using the ThrottlesLogins trait, we can automatically throttle
        // the login attempts for this application. We'll key this by the username and
        // the IP address of the client making these requests into this application.
        if ($this->hasTooManyLoginAttempts($request)) {
            $this->fireLockoutEvent($request);

            return $this->sendLockoutResponse($request);
        }

        if ($this->attemptLogin($request)) {
            return $this->sendLoginResponse($request);
        }

        // If the login attempt was unsuccessful we will increment the number of attempts
        // to login and redirect the user back to the login form. Of course, when this
        // user surpasses their maximum number of attempts they will get locked out.
        $this->incrementLoginAttempts($request);

        return $this->sendFailedLoginResponse($request);
    }
    /**
     * Validate the user login request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return void
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    protected function validateLogin(Request $request)
    {
        $request->validate([
            $this->username() => 'required|string',
        ]);
    }
    protected function attemptLogin(Request $request)
    {
        return $this->guard()->attempt(
            $this->credentials($request), $request->filled('remember')
        );
    }


    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }
    public function resetPassword(Request $request)
    {
        if(!\Session::has('email')){
            return redirect()->route('home')->with('error', 'Você deve enviar o email cadastrado');
        }
        Session::put('email', Session::get('email'));
        return view('auth.passwords.reset');
    }
    public function salvaPassword(Request $request)
    {
        if(!\Session::has('email')){
            return redirect()->back()->with('error', 'Usuário não encontrado');
        }else{
            $email = Session::get('email');
        }
        $rules = [
            'password'      => 'required|confirmed|min:6',
        ];
        $mensagens = [
        ];

        $this->validate($request, $rules, $mensagens);
        $user = User::where('email', $email)->first();

        if(!empty($user)){
            $user->update([
                'password' => \Hash::make($request->password),
                'reset_senha' => 0
            ]);
            Auth::loginUsingId($user->id);
            return redirect()->route('home')->with('success', 'Senha atualizada');
        }else{

            return redirect()->route('home')->with('error', 'Usuário não encontrado');
        }
    }
}
