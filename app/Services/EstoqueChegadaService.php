<?php

namespace App\Services;

use App\Models\Produto;
use App\Models\ProdutoChegada;
use App\Models\ProdutoChegadaReserva;
use App\Models\ProdutosUnidades;
use App\Models\Venda;
use App\Models\VendaItem;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class EstoqueChegadaService
{
    public static function disponibilidadeParaVenda(Produto $produto): array
    {
        $fisico = (int) $produto->unidades()->where('status', 'disponivel')->count();

        $aChegarDisponivel = (int) ProdutoChegada::query()
            ->where('produto_id', $produto->id)
            ->where('status', 'aberto')
            ->selectRaw('COALESCE(SUM(quantidade_total - quantidade_comprometida), 0) as total')
            ->value('total');

        return [
            'fisico' => $fisico,
            'a_chegar' => max(0, $aChegarDisponivel),
            'total' => max(0, $fisico + $aChegarDisponivel),
        ];
    }

    public static function reservarChegadaPorVendaItem(VendaItem $vendaItem, int $quantidadeNecessaria): void
    {
        if ($quantidadeNecessaria <= 0) {
            return;
        }

        $chegadas = ProdutoChegada::query()
            ->where('produto_id', $vendaItem->produto_id)
            ->where('status', 'aberto')
            ->whereColumn('quantidade_comprometida', '<', 'quantidade_total')
            ->orderByRaw('previsao_chegada IS NULL')
            ->orderBy('previsao_chegada')
            ->orderBy('id')
            ->lockForUpdate()
            ->get();

        $restante = $quantidadeNecessaria;

        foreach ($chegadas as $chegada) {
            if ($restante <= 0) {
                break;
            }

            $disponivel = max(0, (int) $chegada->quantidade_total - (int) $chegada->quantidade_comprometida);
            if ($disponivel <= 0) {
                continue;
            }

            $quantidadeReservada = min($disponivel, $restante);

            $chegada->update([
                'quantidade_comprometida' => (int) $chegada->quantidade_comprometida + $quantidadeReservada,
            ]);

            ProdutoChegadaReserva::create([
                'produto_chegada_id' => $chegada->id,
                'venda_item_id' => $vendaItem->id,
                'quantidade' => $quantidadeReservada,
            ]);

            $restante -= $quantidadeReservada;
        }

        if ($restante > 0) {
            throw new \RuntimeException("Não há estoque a chegar suficiente para o produto '{$vendaItem->produto->nome}'.");
        }
    }

    public static function liberarReservasDaVenda(Venda $venda): void
    {
        $venda->loadMissing('itens.reservasChegada.chegada');

        foreach ($venda->itens as $item) {
            foreach ($item->reservasChegada as $reserva) {
                if ($reserva->chegada && $reserva->chegada->status === 'aberto') {
                    $chegada = ProdutoChegada::lockForUpdate()->find($reserva->produto_chegada_id);
                    if ($chegada) {
                        $chegada->quantidade_comprometida = max(0, (int) $chegada->quantidade_comprometida - (int) $reserva->quantidade);
                        $chegada->save();
                    }
                }

                $reserva->delete();
            }
        }
    }

    public static function receberChegada(ProdutoChegada $chegada, ?int $userId = null, ?string $observacao = null): void
    {
        DB::transaction(function () use ($chegada, $userId, $observacao) {
            /** @var ProdutoChegada $chegadaLock */
            $chegadaLock = ProdutoChegada::query()
                ->with(['produto', 'reservas.vendaItem.venda'])
                ->lockForUpdate()
                ->findOrFail($chegada->id);

            if ($chegadaLock->status !== 'aberto') {
                throw new \RuntimeException('Somente lotes com status aberto podem ser recebidos.');
            }

            $produto = $chegadaLock->produto;
            $quantidadeTotal = (int) $chegadaLock->quantidade_total;
            if ($quantidadeTotal <= 0) {
                throw new \RuntimeException('Quantidade inválida para recebimento.');
            }

            $ultimaUnidade = $produto->unidades()->orderByDesc('id')->first();
            $indiceBase = $ultimaUnidade ? $ultimaUnidade->id + 1 : 1;
            $unidadesCriadas = collect();

            for ($i = 0; $i < $quantidadeTotal; $i++) {
                $unidadesCriadas->push(ProdutosUnidades::create([
                    'produto_id' => $produto->id,
                    'codigo_unico' => ProdutoUnidadeService::gerarCodigo($produto, $indiceBase + $i),
                    'status' => 'disponivel',
                ]));
            }

            self::aplicarReservasNasUnidades($chegadaLock, $unidadesCriadas);

            $chegadaLock->update([
                'status' => 'recebido',
                'recebido_por' => $userId,
                'recebido_em' => Carbon::now(),
                'observacao' => $observacao ?: $chegadaLock->observacao,
            ]);

            MovimentacaoService::registrar([
                'produto_id' => $produto->id,
                'tipo' => 'entrada',
                'quantidade' => $quantidadeTotal,
                'observacao' => "Recebimento de estoque a chegar (lote #{$chegadaLock->id}).",
            ]);

            MovimentacaoService::registrar([
                'produto_id' => $produto->id,
                'tipo' => 'disponivel',
                'quantidade' => $quantidadeTotal,
                'observacao' => "Unidades recebidas de estoque a chegar (lote #{$chegadaLock->id}).",
            ]);
        });
    }

    private static function aplicarReservasNasUnidades(ProdutoChegada $chegada, Collection $unidadesCriadas): void
    {
        $ponteiro = 0;

        foreach ($chegada->reservas as $reserva) {
            $vendaItem = $reserva->vendaItem;
            if (!$vendaItem || !$vendaItem->venda || $vendaItem->venda->status === 'cancelada') {
                $reserva->delete();
                continue;
            }

            $qtd = (int) $reserva->quantidade;
            if ($qtd <= 0) {
                $reserva->delete();
                continue;
            }

            $ids = [];
            for ($i = 0; $i < $qtd; $i++) {
                if (!isset($unidadesCriadas[$ponteiro])) {
                    break;
                }

                $unidade = $unidadesCriadas[$ponteiro];
                $unidade->update(['status' => 'vendido']);
                $ids[] = $unidade->id;
                $ponteiro++;
            }

            if (!empty($ids)) {
                $vendaItem->unidades()->attach($ids);
            }

            $reserva->delete();
        }

        $chegada->update(['quantidade_comprometida' => 0]);
    }
}
