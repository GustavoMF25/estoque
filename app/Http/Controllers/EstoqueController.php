<?php

namespace App\Http\Controllers;

use App\Models\Estoque;
use App\Models\Loja;
use App\Services\AuditLogger;
use App\Services\MovimentacaoService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EstoqueController extends Controller
{
    public function index()
    {
        $estoques = Estoque::query()
            ->with('loja')
            ->withCount([
                'produtos as produtos_ativos_count' => fn($q) => $q->where('ativo', true),
            ])
            ->orderBy('nome')
            ->get();

        $unidadesPorEstoque = DB::table('produtos_unidades as pu')
            ->join('produtos as p', 'p.id', '=', 'pu.produto_id')
            ->whereNull('p.deleted_at')
            ->selectRaw('
                p.estoque_id,
                COUNT(*) as unidades_totais,
                SUM(CASE WHEN pu.status = "disponivel" THEN 1 ELSE 0 END) as unidades_disponiveis,
                SUM(CASE WHEN pu.status = "vendido" THEN 1 ELSE 0 END) as unidades_vendidas
            ')
            ->groupBy('p.estoque_id')
            ->get()
            ->keyBy('estoque_id');

        $chegadasPorEstoque = DB::table('produto_chegadas as pc')
            ->join('produtos as p', 'p.id', '=', 'pc.produto_id')
            ->whereNull('p.deleted_at')
            ->where('pc.status', 'aberto')
            ->selectRaw('
                p.estoque_id,
                SUM(pc.quantidade_total) as a_chegar_total,
                SUM(pc.quantidade_comprometida) as a_chegar_comprometida,
                SUM(pc.quantidade_total - pc.quantidade_comprometida) as a_chegar_disponivel
            ')
            ->groupBy('p.estoque_id')
            ->get()
            ->keyBy('estoque_id');

        $cards = $estoques->map(function (Estoque $estoque) use ($unidadesPorEstoque, $chegadasPorEstoque) {
            $unidades = $unidadesPorEstoque->get($estoque->id);
            $chegadas = $chegadasPorEstoque->get($estoque->id);

            $unidadesTotais = (int) ($unidades->unidades_totais ?? 0);
            $limite = (int) ($estoque->quantidade_maxima ?? 0);
            $ocupacao = $limite > 0 ? round(($unidadesTotais / $limite) * 100, 1) : null;

            return [
                'id' => $estoque->id,
                'nome' => $estoque->nome,
                'status' => $estoque->status,
                'loja' => $estoque->loja?->nome,
                'produtos_ativos' => (int) ($estoque->produtos_ativos_count ?? 0),
                'unidades_totais' => $unidadesTotais,
                'unidades_disponiveis' => (int) ($unidades->unidades_disponiveis ?? 0),
                'unidades_vendidas' => (int) ($unidades->unidades_vendidas ?? 0),
                'a_chegar_disponivel' => (int) ($chegadas->a_chegar_disponivel ?? 0),
                'limite' => $limite,
                'ocupacao_percentual' => $ocupacao,
            ];
        });

        return view('estoque.index', compact('cards'));
    }

    public function create()
    {
        return view('estoque.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nome' => 'required|string|max:255',
            'descricao' => 'nullable|string',
            'localizacao' => 'required|string|max:150',
            'quantidade_maxima' => 'nullable|integer|min:0',
            'loja_id' => 'nullable|exists:lojas,id',
        ]);

        $estoque = Estoque::create($request->only('nome', 'descricao', 'quantidade_maxima', 'loja_id', 'localizacao'));

        AuditLogger::info('estoque.created', [
            'estoque_id' => $estoque->id,
            'loja_id' => $estoque->loja_id,
        ]);

        return redirect()->route('estoques.index')->with('success', 'Estoque criado com sucesso!');
    }

    public function edit(Estoque $estoque)
    {
        $lojas = Loja::all();
        return view('estoques.edit', compact('estoque', 'lojas'));
    }

    public function update(Request $request, Estoque $estoque)
    {
        $request->validate([
            'nome' => 'required|string|max:255',
            'descricao' => 'nullable|string',
            'quantidade_maxima' => 'nullable|integer',
            'loja_id' => 'nullable|exists:lojas,id',
        ]);

        $estoque->update($request->only('nome', 'descricao', 'quantidade_maxima', 'loja_id'));

        AuditLogger::info('estoque.updated', [
            'estoque_id' => $estoque->id,
            'loja_id' => $estoque->loja_id,
        ]);

        return redirect()->route('estoques.index')->with('success', 'Estoque atualizado com sucesso!');
    }

    public function destroy(Estoque $estoque)
    {
        try {
            if (!optional(auth()->user())->isAdmin()) {
                AuditLogger::info('estoque.remocao.negada', [
                    'estoque_id' => $estoque->id,
                ]);
                return redirect()->route('estoques.index')->with('error', 'Estoque não removido, sem permissão.');
            }

            $produtos = $estoque->produtos()->get();
            foreach ($produtos as $produto) {
                MovimentacaoService::registrar([
                    'produto_id' => $produto->id,
                    'tipo' => 'cancelamento',
                    'quantidade' => 1,
                    'observacao' => 'Estoque removido: movimentação automática de cancelamento'
                ]);
            }
            $estoque->status = 'inativo';
            $estoque->save();
            $estoque->delete();

            AuditLogger::info('estoque.removido', [
                'estoque_id' => $estoque->id,
                'produtos_afetados' => $produtos->pluck('id')->all(),
            ]);

            return redirect()->route('estoques.index')->with('success', 'Estoque removido com sucesso!');
        } catch (Exception $err) {
            return redirect()->route('estoques.index')->with('error', 'Estoque não removido: ' . $err->getMessage());
        }
    }




    public function restore($id)
    {
        try {
            DB::transaction(function () use ($id) {
                $estoque = Estoque::withTrashed()->findOrFail($id);
                $estoque->restore();

                $produtos = $estoque->produtos()->withTrashed()->get();

                foreach ($produtos as $produto) {
                    $produto->restore();
                    $movCancelamento = $produto->movimentacoes()
                        ->where('tipo', 'cancelamento')
                        ->latest()
                        ->first();

                    if ($movCancelamento) {
                        $movAnterior = $produto->movimentacoes()
                            ->where('id', '<', $movCancelamento->id)
                            ->latest()
                            ->first();
                        if ($movAnterior) {
                            $produto->ultimaMovimentacao()->update([
                                'tipo' => $movAnterior->tipo
                            ]);
                        }
                    }
                }
            });

            AuditLogger::info('estoque.restaurado', [
                'estoque_id' => $id,
            ]);

            return redirect()->route('estoques.index')->with('success', 'Estoque e produtos restaurados com sucesso!');
        } catch (Exception $e) {
            return redirect()->route('estoques.index')->with('error', 'Erro ao restaurar estoque: ' . $e->getMessage());
        }
    }
}
