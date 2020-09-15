<?php
namespace App\Providers;
use App\Auth\CacheUserProvider;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
    ];
    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        // Caching user
        Auth::provider('cache-user', function() {
            return resolve(CacheUserProvider::class);
        });
        if(!Auth::guest()){
            $processos = Processo::where('user_id',Auth::id())->get();
            view()->share('processos', $processos);
        }
    }
}