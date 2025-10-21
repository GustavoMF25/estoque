<?php

namespace Database\Seeders;

use App\Models\Movimentacao;
use App\Models\ProdutosAgrupados;
use App\Models\ProdutosUnidades;
use App\Models\Produto;
use App\Models\VendaItem;
use App\Services\MovimentacaoService;
use App\Services\ProdutoUnidadeService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MigrarProdutosParaUnidadesSeeder extends Seeder
{
    public function run(): void
    {
        DB::beginTransaction();

        try {
            $grupos = ProdutosAgrupados::query()
                // ->where('nome', 'AGATA 1,30 x 1,30 Laminado')
                ->orderBy('ultima_movimentacao', 'desc')
                ->orderBy('nome', 'asc')
                ->whereNotIn('ultima_movimentacao', ['cancelamento'])->get();

            if ($grupos->isEmpty()) {
                $this->command->warn('⚠️ Nenhum produto encontrado na view produtos_agrupados_view.');
                return;
            }

            foreach ($grupos as $grupo) {
                $produtos = Produto::where('nome', $grupo->nome)
                    ->where('estoque_id', $grupo->estoque_id)
                    ->whereNull('deleted_at')
                    ->get();

                if ($produtos->isEmpty()) continue;

                $idsProdutos = $produtos->pluck('id');

                // 🔢 Quantidades
                $quantidadeTotal = $grupo->quantidade_produtos;
                $produtoPrincipal = $produtos->first();

                if ($grupo->ultima_movimentacao == 'disponivel') {
                    // 🔵 Desativa duplicados
                    $produtos->skip(1)->each(function ($p) {
                        $p->ativo = false;
                        $p->save();
                    });

                    if ($grupo->imagem) {
                        $produtoPrincipal->imagem = $grupo->imagem;
                        $produtoPrincipal->save();
                    }

                    if ($quantidadeTotal > 0) {
                        $existe = Movimentacao::where('produto_id', $produtoPrincipal->id)
                            ->where('tipo', 'disponivel')
                            ->where('observacao', 'like', '%Migração automática%')
                            ->exists();

                        if (!$existe) {
                            MovimentacaoService::registrar([
                                'produto_id' => $produtoPrincipal->id,
                                'tipo' => 'disponivel',
                                'quantidade' => $quantidadeTotal,
                                'observacao' => "Migração automática - {$quantidadeTotal} unidades disponíveis.",
                            ]);
                        }
                    }
                    $codigo = 1;
                    // Unidades disponíveis
                    for ($i = 1; $i <= $quantidadeTotal; $i++, $codigo++) {
                        ProdutosUnidades::firstOrCreate(
                            [
                                'produto_id' => $produtoPrincipal->id,
                                'codigo_unico' => ProdutoUnidadeService::gerarCodigo($produtoPrincipal, $codigo),
                            ],
                            [
                                'status' => 'disponivel',
                            ]
                        );
                    }
                }

                if ($grupo->ultima_movimentacao == 'saida') {
                    // 🔵 Desativa duplicados
                    $produtos->skip(1)->each(function ($p) {
                        $p->ativo = false;
                        $p->save();
                    });

                    if ($quantidadeTotal > 0) {
                        $existe = Movimentacao::where('produto_id', $produtoPrincipal->id)
                            ->where('tipo', 'saida')
                            ->where('observacao', 'like', '%Migração automática%')
                            ->exists();

                        if (!$existe) {
                            MovimentacaoService::registrar([
                                'produto_id' => $produtoPrincipal->id,
                                'tipo' => 'saida',
                                'quantidade' => $quantidadeTotal,
                                'observacao' => "Migração automática - {$quantidadeTotal} unidades vendidas.",
                            ]);
                        }
                    }

                    // 🧱 Cria unidades conforme status
                    $codigo = 1;

                    // Unidades vendidas
                    for ($i = 1; $i <= $quantidadeTotal; $i++, $codigo++) {
                        $unidade = ProdutosUnidades::firstOrCreate(
                            [
                                'produto_id' => $produtoPrincipal->id,
                                'codigo_unico' => 'vend_' . ProdutoUnidadeService::gerarCodigo($produtoPrincipal, $codigo),
                            ],
                            [
                                'status' => 'vendido',
                            ]
                        );
                    }
                }

                /**
                 * 🟠 Caso a última movimentação seja 'entrada':
                 * - desativa duplicados
                 * - mantém apenas 1 ativo
                 * - não cria movimentações nem unidades
                 */
                if ($grupo->ultima_movimentacao != 'saida' || $grupo->ultima_movimentacao != 'disponivel') {
                    $produtos->skip(1)->each(function ($p) {
                        $p->ativo = false;
                        $p->save();
                    });

                    $produtoPrincipal->update([
                        'ativo' => true,
                    ]);

                    $this->command->warn("🔕 Produto '{$grupo->nome}' consolidado (sem unidades, última movimentação = {$grupo->ultima_movimentacao}).");
                }

                $this->command->info("✅ Produto '{$grupo->nome}' migrado: {$quantidadeTotal} unidade(s) com status '{$grupo->ultima_movimentacao}'.");
            }

            DB::commit();
            $this->command->info('🎯 Migração concluída com sucesso (disponíveis e vendidos migrados, entradas/saídas consolidadas).');
        } catch (\Exception $e) {
            DB::rollBack();
            $this->command->error('❌ Erro: ' . $e->getMessage());
        }
    }
}
