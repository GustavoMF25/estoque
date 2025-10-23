<?php

namespace App\Providers;

use App\Models\Empresa;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class ViewServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        // Executa toda vez que for renderizar uma view
        View::composer('*', function ($view) {
            if (auth()->check() && auth()->user()->empresa_id) {
                $empresa = Empresa::find(auth()->user()->empresa_id);
                $view->with('empresa', $empresa);
            } else {
                $view->with('empresa', null);
            }
        });
    }
}
