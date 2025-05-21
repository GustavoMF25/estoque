<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;


class VerificaPerfil
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$perfis): Response
    {
        $usuario = Auth::user();

        if (!$usuario) {
            return redirect('/login');
        }

        if (!in_array($usuario->perfil, $perfis)) {
            abort(403, 'Acesso não autorizado');
        }

        return $next($request);
    }
}
