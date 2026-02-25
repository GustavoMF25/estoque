<?php

namespace App\Services;

use App\Models\Produto;
use App\Models\ProdutoChegada;
use App\Models\ProdutosUnidades;
use Carbon\Carbon;
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

    public static function receberChegada(ProdutoChegada $chegada, ?int $userId = null, ?string $observacao = null): void
    {
        DB::transaction(function () use ($chegada, $userId, $observacao) {
            /** @var ProdutoChegada $chegadaLock */
            $chegadaLock = ProdutoChegada::query()
                ->with('produto')
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
}
