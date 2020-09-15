<?php

namespace App\Http\Middleware;

use Closure;
use Auth;

class Admin
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure                 $next
     *
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if(!Auth::user()){

            if ($request->ajax() || $request->wantsJson()) {
                return response('sem permissão', 401);
            }else{
                Auth::logout();
                return redirect('/')->with('error','Você deve estar logado para acessar esse conteúdo');
            }

        }
        return $next($request);
    }
}
