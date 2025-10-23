<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use App\Support\Tenancy;

class SetEmpresaContext
{
    public function handle($request, Closure $next)
    {
        $empresaId = null;

        if (auth()->check()) {
            $empresaId = auth()->user()->empresa_id;
        } else {
            // fallback opcional (ex.: subdomínio, sessão, header)
            $empresaId = $request->session()->get('empresa_id');
        }

        Tenancy::setEmpresaId($empresaId);

        return $next($request);
    }
}
