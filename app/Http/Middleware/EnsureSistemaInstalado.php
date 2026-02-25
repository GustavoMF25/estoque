<?php

namespace App\Http\Middleware;

use App\Models\InstalacaoConfig;
use App\Services\InstalacaoLicencaService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Symfony\Component\HttpFoundation\Response;

class EnsureSistemaInstalado
{
    public function __construct(private InstalacaoLicencaService $instalacaoLicencaService)
    {
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->is('up') || $request->is('storage/*')) {
            return $next($request);
        }

        if ($request->routeIs('instalacao.*')) {
            return $next($request);
        }

        try {
            $temTabelaInstalacao = Schema::hasTable('instalacao_configs');
        } catch (\Throwable) {
            return redirect()->route('instalacao.index');
        }

        if (!$temTabelaInstalacao) {
            return redirect()->route('instalacao.index');
        }

        $config = InstalacaoConfig::query()->first();
        if (!$config || !$config->is_installed) {
            return redirect()->route('instalacao.index');
        }

        $precisaValidar = !$config->last_validation_at || $config->last_validation_at->lt(now()->subHours(6));
        if ($precisaValidar) {
            $resultado = $this->instalacaoLicencaService->validarLicencaAtual();
            if (!$resultado['ok']) {
                return redirect()->route('instalacao.index')
                    ->withErrors(['token' => $resultado['message']]);
            }
        }

        return $next($request);
    }
}
