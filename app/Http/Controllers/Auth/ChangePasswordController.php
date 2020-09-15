<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use App\Models\User;
use App\Models\Cidades;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;

use DB;
use Auth;

class ChangePasswordController extends Controller
{

    public function __construct()
    {
        $this->middleware('web');
    }
    public function showNewPassword()
    {
        if(!Auth::guest()){
            return view('auth.passwords.reset');
        }else{
            return view('home');
        }
        
    }

    public function newPassword(Request $request)
    {
        if(!Auth::guest()){
            $user = User::find(Auth::id());
             $user->password = Hash::make($request->password);
            $user->update();
            return redirect()->route('home')->with('success','Senha atualizada');
        }else{
            return view('home');
        }
        
    }
}
