<?php

namespace Database\Seeders;

use App\Models\Produto;
use App\Models\ProdutosUnidades;
use App\Models\Venda;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ReprocessarVendasAntigasSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::beginTransaction();

        try {
            $vendas = Venda::with('itens')->get();
            $contadorVinculos = 0;

            if ($vendas->isEmpty()) {
                echo "⚠️ Nenhuma venda encontrada.\n";
                return;
            }

            foreach ($vendas as $venda) {
                foreach ($venda->itens as $item) {

                    // Evita duplicar vínculos
                    if ($item->unidades()->exists()) {
                        continue;
                    }

                    // Busca produto (inclui inativos e soft deleted
                    if ($item->produto) {
                        $produto = Produto::where('nome', $item->produto->nome)->Ativo()->first();
                        if (!$produto) {
                            echo "❌ VendaItem {$item->id} sem produto vinculado.\n";
                            continue;
                        }

                        // Cada item representa 1 unidade no modelo antigo
                        $quantidade = 1;

                        // 🔍 1) Busca unidades 'vendido' ainda não vinculadas
                        $unidades = ProdutosUnidades::where('produto_id', $produto->id)
                            ->where('status', 'vendido')
                            ->whereDoesntHave('vendaItens')
                            ->limit($quantidade)
                            ->get();

                        // 🔍 2) Se não encontrou, cria unidade 'fantasma'
                        if ($unidades->isEmpty()) {
                            $fake = ProdutosUnidades::create([
                                'produto_id'   => $produto->id,
                                'codigo_unico' => strtoupper(Str::slug($produto->nome)) . '-ANT-' . uniqid(),
                                'status'       => 'vendido',
                            ]);
                            $unidades->push($fake);

                            echo "⚠️ Criada unidade fantasma para produto '{$produto->nome}' (sem vendidas livres)\n";
                        } else {
                            echo "🔗 Unidade vendida livre encontrada para produto '{$produto->nome}'\n";
                        }

                        // 🔗 3) Vincula as unidades à venda
                        $item->unidades()->attach($unidades->pluck('id')->toArray());

                        // 🔄 4) Atualiza o status das unidades
                        foreach ($unidades as $unidade) {
                            $unidade->update(['status' => 'vendido']);
                        }

                        // 🔁 5) Atualiza o produto_id do VendaItem conforme a unidade
                        if ($unidades->isNotEmpty()) {
                            $novaUnidade = $unidades->first();
                            $item->update(['produto_id' => $novaUnidade->produto_id]);

                            echo "🔄 Corrigido produto_id de VendaItem {$item->id} → {$novaUnidade->produto_id}\n";
                        }

                        $contadorVinculos += $quantidade;

                        echo "✅ Venda #{$venda->id} | Item {$item->id} ({$produto->nome}) → {$quantidade} unidade vinculada(s)\n";
                    }
                }
            }

            DB::commit();
            echo "\n🎉 Total de {$contadorVinculos} unidades vinculadas e produtos atualizados com sucesso!\n";
        } catch (\Exception $e) {
            DB::rollBack();
            echo "❌ Erro ao reprocessar vendas: {$e->getMessage()}\n";
        }
    }
}
