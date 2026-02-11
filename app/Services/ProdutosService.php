<?php

namespace App\Services;

use App\Models\Estoque;
use App\Models\Produto;
use Exception;
use Illuminate\Support\Facades\DB;

class ProdutosService
{
    public static function cadastraProduto($request)
    {
        $validated = $request->validate([
            'nome' => 'required|string|max:255',
            'codigo_barras' => 'nullable|string|max:50',
            'unidade' => 'nullable|string|max:10',
            'preco' => 'nullable|numeric|min:0',
            'valor_entrada' => 'nullable|numeric|min:0',
            'valor_venda' => 'nullable|numeric|min:0',
            'estoque_id' => 'required|exists:estoques,id',
            'categoria_id' => 'required|exists:categoria,id',
            'quantidade' => 'required|integer|min:1',
            'ativo' => 'boolean',
            'imagem' => 'nullable|image|max:2048',
        ]);

        $validated['imagem'] = $request->file('imagem') ?? null;

        return self::handleCadastroProduto($validated);
    }

    public static function handleCadastroProduto(array $data)
    {
        $estoque = Estoque::withCount('produtos')->findOrFail($data['estoque_id']);
        $totalAtual = $estoque->produtos_count;
        $quantidadeMaxima = $estoque->quantidade_maxima;

        $quantidade = max((int) ($data['quantidade'] ?? 1), 1);

        if ($quantidadeMaxima > 0 && ($totalAtual + $quantidade) > $quantidadeMaxima) {
            AuditLogger::info('produto.cadastro.limite', [
                'estoque_id' => $data['estoque_id'],
                'quantidade_atual' => $totalAtual,
                'quantidade_maxima' => $quantidadeMaxima,
                'quantidade_solicitada' => $quantidade,
            ]);

            return redirect()->route('produtos.index')
                ->with('error', 'Não é possível cadastrar: o estoque excederia o limite máximo de ' . $quantidadeMaxima . ' itens.');
        }

        return DB::transaction(function () use ($data, $quantidade) {
            $imagem = $data['imagem'] ?? null;
            if ($imagem instanceof \Illuminate\Http\UploadedFile) {
                $imagem = $imagem->store('produtos', 'public');
            }

            $valorEntrada = (float) ($data['valor_entrada'] ?? 0);
            $valorVenda = (float) ($data['valor_venda'] ?? ($data['preco'] ?? 0));

            $produto = Produto::create([
                'nome' => $data['nome'],
                'codigo_barras' => Produto::gerarCodigoBarrasUnico(),
                'unidade' => $data['unidade'] ?? 'un',
                'preco' => $valorVenda,
                'valor_entrada' => $valorEntrada,
                'valor_venda' => $valorVenda,
                'estoque_id' => $data['estoque_id'],
                'categoria_id' => $data['categoria_id'] ?? null,
                'fabricante_id' => $data['fabricante_id'] ?? null,
                'ativo' => $data['ativo'] ?? true,
                'imagem' => $imagem,
            ]);

            AuditLogger::info('produto.created', [
                'produto_id' => $produto->id,
                'nome' => $produto->nome,
                'estoque_id' => $produto->estoque_id,
                'quantidade_planejada' => $quantidade,
            ]);

            self::criarUnidadesEHistorico($produto, $quantidade);

            return $produto;
        });
    }

    public static function cadProdutoRequest($request)
    {
        $validated = $request->validate([
            'nome' => 'required|string|max:255',
            'codigo_barras' => 'nullable|string|max:50',
            'unidade' => 'nullable|string|max:10',
            'preco' => 'nullable|numeric|min:0',
            'valor_entrada' => 'nullable|numeric|min:0',
            'valor_venda' => 'nullable|numeric|min:0',
            'estoque_id' => 'required|exists:estoques,id',
            'quantidade' => 'required|integer|min:1',
            'categoria_id' => 'nullable|exists:categorias,id',
            'fabricante_id' => 'nullable|exists:fabricantes,id',
            'ativo' => 'boolean',
            'imagem' => 'nullable|image|max:2048',
        ]);

        $validated['imagem'] = $request->file('imagem') ?? null;

        return self::handleCadastroProduto($validated);
    }

    protected static function criarUnidadesEHistorico(Produto $produto, int $quantidade): void
    {
        $unidades = ProdutoUnidadeService::adicionarUnidades(
            $produto,
            $quantidade,
            'Cadastro inicial do produto'
        );

        AuditLogger::info('produto.unidades.iniciais', [
            'produto_id' => $produto->id,
            'quantidade' => $quantidade,
            'unidades_ids' => $unidades->pluck('id'),
        ]);
    }
}
