<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {
        if (Auth::guard($guard)->check()) {
            return redirect('/home');
        }else{
            if($request->route()->getName() == 'reset_password' || $request->route()->getName() == 'salvaPassword'){
                return $next($request);
            }
            if($request->route()->getName() != 'login'){
                return redirect()->route('home')->with('error','Você deve estar logado para acessar esse conteúdo');
            }
            
        }

        return $next($request);
    }
}
