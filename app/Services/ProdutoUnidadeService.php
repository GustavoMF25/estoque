<?php

namespace App\Services;

use App\Models\Produto;
use App\Models\ProdutosUnidades;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ProdutoUnidadeService
{
    protected static array $movimentacoesPorStatus = [
        'disponivel' => 'entrada',
        'vendido' => 'saida',
        'reservado' => 'reserva',
        'defeito' => 'danificado',
        'indisponivel' => 'cancelamento',
    ];

    public static function gerarCodigo(Produto $produto, int $indice = 1): string
    {
        $prefixo = strtoupper(Str::slug(substr($produto->nome, 0, 5)));

        return "{$prefixo}-" . str_pad($indice, 5, '0', STR_PAD_LEFT);
    }

    public static function adicionarUnidades(Produto $produto, int $quantidade, ?string $contexto = null): Collection
    {
        $quantidade = max($quantidade, 1);

        return DB::transaction(function () use ($produto, $quantidade, $contexto) {
            $indiceBase = self::proximoIndice($produto);
            $unidades = collect();

            for ($i = 0; $i < $quantidade; $i++) {
                $codigo = self::gerarCodigo($produto, $indiceBase + $i);
                $unidades->push(
                    ProdutosUnidades::create([
                        'produto_id' => $produto->id,
                        'codigo_unico' => $codigo,
                        'status' => 'disponivel',
                    ])
                );
            }

            $descricao = $contexto ?? "Cadastro de {$quantidade} unidade(s) para {$produto->nome}";

            MovimentacaoService::registrar([
                'produto_id' => $produto->id,
                'tipo' => 'entrada',
                'quantidade' => $quantidade,
                'observacao' => $descricao,
            ]);

            MovimentacaoService::registrar([
                'produto_id' => $produto->id,
                'tipo' => 'disponivel',
                'quantidade' => $quantidade,
                'observacao' => "Unidades disponiveis - {$descricao}",
            ]);

            AuditLogger::info('produto.unidades.adicionadas', [
                'produto_id' => $produto->id,
                'quantidade' => $quantidade,
                'descricao' => $descricao,
                'unidades_ids' => $unidades->pluck('id'),
            ]);

            return $unidades;
        });
    }

    public static function alterarStatus(Collection|array $unidades, string $novoStatus, ?string $tipoMovimentacao = null, ?string $observacao = null): void
    {
        $unidades = collect($unidades)->filter();

        if ($unidades->isEmpty()) {
            return;
        }

        DB::transaction(function () use ($unidades, $novoStatus, $tipoMovimentacao, $observacao) {
            $produtoId = $unidades->first()->produto_id;

            $unidades->each(function (ProdutosUnidades $unidade) use ($novoStatus) {
                $unidade->alterarStatus($novoStatus);
            });

            MovimentacaoService::registrar([
                'produto_id' => $produtoId,
                'tipo' => $tipoMovimentacao ?? self::tipoMovimentacaoPorStatus($novoStatus),
                'quantidade' => $unidades->count(),
                'observacao' => $observacao,
            ]);

            AuditLogger::info('produto.unidades.status_alterado', [
                'produto_id' => $produtoId,
                'quantidade' => $unidades->count(),
                'novo_status' => $novoStatus,
                'observacao' => $observacao,
            ]);
        });
    }

    public static function tipoMovimentacaoPorStatus(string $status): string
    {
        return self::$movimentacoesPorStatus[$status] ?? 'ajuste_negativo';
    }

    protected static function proximoIndice(Produto $produto): int
    {
        $ultimoCodigo = $produto->unidades()
            ->orderByDesc('id')
            ->value('codigo_unico');

        return self::extrairIndice($ultimoCodigo) + 1;
    }

    protected static function extrairIndice(?string $codigo): int
    {
        if (empty($codigo)) {
            return 0;
        }

        if (preg_match('/(\d+)$/', $codigo, $matches)) {
            return (int) $matches[1];
        }

        return 0;
    }
}
