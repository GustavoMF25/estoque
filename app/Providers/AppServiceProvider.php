<?php

namespace App\Providers;

use App\Models\Empresa;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {

        $this->ajustarDriverSessaoDuranteInstalacao();    
    }

    private function ajustarDriverSessaoDuranteInstalacao(): void
    {
        if (Config::get('session.driver') !== 'database') {
            return;
        }

        $tabelaSessao = (string) Config::get('session.table', 'sessions');

        try {
            if (!Schema::hasTable($tabelaSessao)) {
                Config::set('session.driver', 'file');
            }
            View::share('empresa', Empresa::first());
        } catch (\Throwable) {
            Config::set('session.driver', 'file');
        }
    }
}
