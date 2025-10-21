<?php

// app/Services/VendaService.php
namespace App\Services;

use App\Models\Movimentacao;
use App\Models\ProdutoVinculos;
use Exception;

class VendaService
{
    public static function validarEstoque($venda)
    {
        foreach ($venda->itens as $item) {
            $disponivel = Movimentacao::where('produto_id', $item->produto_id)
                ->sum('quantidade_disponivel');

            if ($disponivel < $item->quantidade) {
                throw new Exception("Estoque insuficiente para o produto {$item->produto->nome}");
            }
        }
    }

    public static function aplicarDescontoCombo($venda)
    {
        $descontoTotal = 0;

        foreach ($venda->itens as $item) {
            $vinculos = ProdutoVinculos::where('produto_principal_id', $item->produto_id)->get();

            foreach ($vinculos as $vinculo) {
                $qtdNecessaria = $vinculo->quantidade;

                $temPrincipal = $venda->itens->where('produto_id', $item->produto_id)->count() > 0;
                $temVinculado = $venda->itens
                    ->where('produto_id', $vinculo->produto_vinculado_id)
                    ->where('quantidade', '>=', $qtdNecessaria)
                    ->count() > 0;

                if ($temPrincipal && $temVinculado) {
                    $subtotalCombo = $venda->itens
                        ->whereIn('produto_id', [$item->produto_id, $vinculo->produto_vinculado_id])
                        ->sum('valor_total');

                    $desconto = ($subtotalCombo * $vinculo->desconto_combo) / 100;
                    $descontoTotal += $desconto;
                }
            }
        }

        $venda->desconto = $descontoTotal;
        $venda->valor_final = $venda->valor_total - $descontoTotal;
        $venda->save();

        return $venda;
    }
}
