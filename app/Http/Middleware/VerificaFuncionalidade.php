<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Symfony\Component\HttpFoundation\Response;

class VerificaFuncionalidade
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $usuario = Auth::user();
        if (!$usuario) {
            return $next($request);
        }

        if (!Schema::hasTable('funcionalidades') || !Schema::hasTable('perfil_funcionalidade')) {
            return $next($request);
        }

        $perfilConfigurado = DB::table('perfil_funcionalidade')
            ->where('perfil', $usuario->perfil)
            ->exists();

        if (!$perfilConfigurado) {
            return $next($request);
        }

        $rota = $request->route()?->getName();
        if (!$rota) {
            return $next($request);
        }

        $funcionalidadeId = DB::table('funcionalidades')
            ->where('rota', $rota)
            ->where('ativo', true)
            ->value('id');

        if (!$funcionalidadeId) {
            return $next($request);
        }

        $permitido = DB::table('perfil_funcionalidade')
            ->where('perfil', $usuario->perfil)
            ->where('funcionalidade_id', $funcionalidadeId)
            ->exists();

        if (!$permitido) {
            abort(403, 'Acesso não autorizado para esta funcionalidade.');
        }

        return $next($request);
    }
}
