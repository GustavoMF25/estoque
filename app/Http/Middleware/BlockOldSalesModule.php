<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Carbon\Carbon;

class BlockOldSalesModule
{
    public function handle(Request $request, Closure $next)
    {
        // Data de liberação antiga
        $dataInicial = Carbon::parse('2026-01-15');
        $diasPermitidos = 5;

        // ⏳ Se ainda não chegou 2026, ignora o middleware
        if (Carbon::now()->lt($dataInicial)) {
            return $next($request);
        }

        // 📊 Calcula quantos dias se passaram e quantos faltam
        $diasPassados = $dataInicial->diffInDays(Carbon::now());
        $diasRestantes = max(0, $diasPermitidos - $diasPassados);

        if ($diasPassados > $diasPermitidos) {
            // Redireciona para página de aviso
            return response()->view('modulo-vendas-expirado', [
                'linkNovaVersao' => 'https://estoque.syntaxweb.com.br',
                'dias' => $diasPassados,
            ]);
        }

        $mensagem = "⚠️ O módulo de vendas ficará disponível por mais {$diasRestantes} dia(s).";
        session()->flash('error', $mensagem);


        return $next($request);
    }
}
