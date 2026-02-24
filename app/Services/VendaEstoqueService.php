<?php

namespace App\Services;

use App\Models\Produto;
use App\Models\ProdutosUnidades;
use App\Models\VendaItem;

class VendaEstoqueService
{
    public static function disponibilidadeAtual(Produto $produto): array
    {
        return EstoqueChegadaService::disponibilidadeParaVenda($produto);
    }

    public static function aplicarSaidaItem(VendaItem $vendaItem, int $quantidade): void
    {
        if ($quantidade <= 0) {
            return;
        }

        $produto = Produto::findOrFail($vendaItem->produto_id);
        $disponibilidade = self::disponibilidadeAtual($produto);

        if ($quantidade > $disponibilidade['total']) {
            throw new \RuntimeException(
                "O produto '{$produto->nome}' possui apenas {$disponibilidade['total']} unidades disponíveis para venda."
            );
        }

        $unidadesDisponiveis = ProdutosUnidades::query()
            ->where('produto_id', $produto->id)
            ->where('status', 'disponivel')
            ->lockForUpdate()
            ->limit($quantidade)
            ->get();

        if ($unidadesDisponiveis->isNotEmpty()) {
            $vendaItem->unidades()->attach($unidadesDisponiveis->pluck('id')->toArray());
            ProdutosUnidades::whereIn('id', $unidadesDisponiveis->pluck('id')->toArray())
                ->update(['status' => 'vendido']);
        }

        $quantidadeVirtual = $quantidade - $unidadesDisponiveis->count();
        if ($quantidadeVirtual > 0) {
            EstoqueChegadaService::reservarChegadaPorVendaItem($vendaItem, $quantidadeVirtual);
        }

        $observacao = "Venda ID: {$vendaItem->venda_id}";
        if ($quantidadeVirtual > 0) {
            $observacao .= " ({$quantidadeVirtual} unidade(s) consumidas do estoque a chegar)";
        }

        MovimentacaoService::registrar([
            'produto_id' => $produto->id,
            'quantidade' => $quantidade,
            'tipo' => 'saida',
            'observacao' => $observacao,
        ]);
    }
}
