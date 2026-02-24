<?php

namespace App\Http\Controllers;

use App\Models\Produto;
use App\Models\ProdutoChegada;
use App\Services\EstoqueChegadaService;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ProdutoChegadaController extends Controller
{
    public function index()
    {
        $chegadas = ProdutoChegada::query()
            ->with(['produto', 'criadoPor', 'recebidoPor'])
            ->orderByRaw("CASE WHEN status = 'aberto' THEN 0 ELSE 1 END")
            ->orderByDesc('created_at')
            ->paginate(20);

        $produtos = Produto::query()->ativo()->orderBy('nome')->get(['id', 'nome']);

        return view('produto-chegadas.index', compact('chegadas', 'produtos'));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'produto_id' => 'required|exists:produtos,id',
            'quantidade_total' => 'required|integer|min:1',
            'previsao_chegada' => 'nullable|date_format:d/m/Y',
            'observacao' => 'nullable|string|max:1000',
        ]);

        $previsaoChegada = $request->filled('previsao_chegada')
            ? Carbon::createFromFormat('d/m/Y', $request->previsao_chegada)->format('Y-m-d')
            : null;

        ProdutoChegada::create([
            'produto_id' => (int) $request->produto_id,
            'quantidade_total' => (int) $request->quantidade_total,
            'quantidade_comprometida' => 0,
            'previsao_chegada' => $previsaoChegada,
            'status' => 'aberto',
            'observacao' => $request->observacao,
            'criado_por' => auth()->id(),
        ]);

        return redirect()->route('produto-chegadas.index')
            ->with('success', 'Estoque a chegar registrado com sucesso.');
    }

    public function receber(ProdutoChegada $produtoChegada): RedirectResponse
    {
        try {
            EstoqueChegadaService::receberChegada($produtoChegada, auth()->id());

            return redirect()->route('produto-chegadas.index')
                ->with('success', 'Lote recebido com sucesso.');
        } catch (\Throwable $e) {
            return redirect()->route('produto-chegadas.index')
                ->with('error', 'Não foi possível receber o lote: ' . $e->getMessage());
        }
    }

    public function cancelar(ProdutoChegada $produtoChegada): RedirectResponse
    {
        if ($produtoChegada->status !== 'aberto') {
            return redirect()->route('produto-chegadas.index')->with('error', 'Somente lotes abertos podem ser cancelados.');
        }

        if ((int) $produtoChegada->quantidade_comprometida > 0) {
            return redirect()->route('produto-chegadas.index')->with('error', 'Este lote possui itens comprometidos e não pode ser cancelado.');
        }

        $produtoChegada->update([
            'status' => 'cancelado',
            'recebido_por' => auth()->id(),
            'recebido_em' => now(),
        ]);

        return redirect()->route('produto-chegadas.index')->with('success', 'Lote cancelado com sucesso.');
    }
}
