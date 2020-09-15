<?php

namespace App\Providers;
use App\Models\User;
use App\Observers\UserObserver;
use Illuminate\Support\ServiceProvider;
use App\Models\Processo;
use Auth;
use Session;
class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        \Carbon\Carbon::setLocale($this->app->getLocale());
        \setlocale(LC_TIME, 'pt_BR', 'pt_BR.utf-8', 'pt_BR.utf-8', 'portuguese');
        \date_default_timezone_set('America/Sao_Paulo');
        \Schema::defaultStringLength(191);
        User::observe(UserObserver::class);
        view()->composer('*', function($view)
        {
            if (Auth::check()) {
                if(!Session::has('CodCidade')){
                    Session::put('CodCidade', Auth::user()->CodCidade);
                }
                if(!Session::has('user_id')){
                    Session::put('user_id', Auth::id());
                }
                $processos = Processo::with('Cidade')->where('user_id',Auth::id())->orderBy('created_at'    ,'desc')->take('10')->get();
                $view->with('processos', $processos );
            }else {
                $view->with('processos', []);
            }
        });
        
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
