<?php

namespace App\Http\Controllers;

use App\Models\Estoque;
use App\Models\Loja;
use App\Services\MovimentacaoService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EstoqueController extends Controller
{
    public function index()
    {
        return view('estoque.index');
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

        Estoque::create($request->only('nome', 'descricao', 'quantidade_maxima', 'loja_id', 'localizacao'));

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

        return redirect()->route('estoques.index')->with('success', 'Estoque atualizado com sucesso!');
    }

    public function destroy(Estoque $estoque)
    {
        try {
            if (!optional(auth()->user())->isAdmin()) {
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

            return redirect()->route('estoques.index')->with('success', 'Estoque e produtos restaurados com sucesso!');
        } catch (Exception $e) {
            return redirect()->route('estoques.index')->with('error', 'Erro ao restaurar estoque: ' . $e->getMessage());
        }
    }
}
