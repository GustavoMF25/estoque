<?php

namespace App\Http\Controllers;

use App\Models\Produto;
use App\Models\ProdutoChegada;
use App\Services\EstoqueChegadaService;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class ProdutoChegadaController extends Controller
{
    public function index()
    {
        $produtos = Produto::query()->ativo()->orderBy('nome')->get(['id', 'nome']);

        return view('produto-chegadas.index', compact('produtos'));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'produto_id' => 'required|exists:produtos,id',
            'quantidade_total' => 'required|integer|min:1',
            'previsao_chegada' => 'nullable|string|max:10',
            'observacao' => 'nullable|string|max:1000',
        ]);

        $previsaoChegada = $this->normalizarPrevisaoChegada($request->input('previsao_chegada'));

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

    public function edit(ProdutoChegada $produtoChegada)
    {
        if ($produtoChegada->status !== 'aberto') {
            return redirect()->route('produto-chegadas.index')
                ->with('error', 'Somente lotes abertos podem ser editados.');
        }

        $produtos = Produto::query()->ativo()->orderBy('nome')->get(['id', 'nome']);

        return view('produto-chegadas.edit', compact('produtoChegada', 'produtos'));
    }

    public function update(Request $request, ProdutoChegada $produtoChegada): RedirectResponse
    {
        if ($produtoChegada->status !== 'aberto') {
            return redirect()->route('produto-chegadas.index')
                ->with('error', 'Somente lotes abertos podem ser editados.');
        }

        $request->validate([
            'produto_id' => 'required|exists:produtos,id',
            'quantidade_total' => 'required|integer|min:1',
            'previsao_chegada' => 'nullable|string|max:10',
            'observacao' => 'nullable|string|max:1000',
        ]);

        $quantidadeTotal = (int) $request->quantidade_total;
        $quantidadeComprometida = (int) $produtoChegada->quantidade_comprometida;

        if ($quantidadeTotal < $quantidadeComprometida) {
            return redirect()->back()->withInput()->with(
                'error',
                "A quantidade total não pode ser menor que a comprometida ({$quantidadeComprometida})."
            );
        }

        if (
            (int) $request->produto_id !== (int) $produtoChegada->produto_id
            && $quantidadeComprometida > 0
        ) {
            return redirect()->back()->withInput()->with(
                'error',
                'Não é permitido alterar o produto quando o lote possui itens comprometidos.'
            );
        }

        $previsaoChegada = $this->normalizarPrevisaoChegada($request->input('previsao_chegada'));

        $produtoChegada->update([
            'produto_id' => (int) $request->produto_id,
            'quantidade_total' => $quantidadeTotal,
            'previsao_chegada' => $previsaoChegada,
            'observacao' => $request->observacao,
        ]);

        return redirect()->route('produto-chegadas.index')
            ->with('success', 'Lote de estoque a chegar atualizado com sucesso.');
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

    private function normalizarPrevisaoChegada(?string $valor): ?string
    {
        $valor = trim((string) $valor);

        if ($valor === '') {
            return null;
        }

        $formatosAceitos = ['Y-m-d', 'd/m/Y'];

        foreach ($formatosAceitos as $formato) {
            try {
                $data = Carbon::createFromFormat($formato, $valor);
                if ($data && $data->format($formato) === $valor) {
                    return $data->format('Y-m-d');
                }
            } catch (\Throwable $e) {
                // Tenta o próximo formato suportado.
            }
        }

        throw ValidationException::withMessages([
            'previsao_chegada' => 'Data inválida. Use o formato aaaa-mm-dd ou dd/mm/aaaa.',
        ]);
    }
}
