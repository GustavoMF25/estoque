<?php

namespace App\Services;

use App\Models\Movimentacao;
use Illuminate\Support\Facades\Auth;

class MovimentacaoService
{
    public static function registrar(array $dados): Movimentacao
    {
        return Movimentacao::create([
            'produto_id' => $dados['produto_id'],
            'tipo' => $dados['tipo'],
            'quantidade' => $dados['quantidade'] ?? 1,
            'observacao' => $dados['observacao'] ?? null,
            'user_id' => Auth::id(),
        ]);
    }
}
