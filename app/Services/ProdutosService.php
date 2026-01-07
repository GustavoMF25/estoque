<?php

namespace App\Services;

use App\Models\Estoque;
use App\Models\Produto;
use App\Models\ProdutosUnidades;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;

class ProdutosService
{
    public static function cadastraProduto($request)
    {
        return self::cadProdutoRequest($request);
    }

    /**
     * Manipula o cadastro de um produto e de suas unidades físicas.
     *
     * @param  array<string, mixed>  $data
     */
    public static function handleCadastroProduto(array $data)
    {
        return DB::transaction(function () use ($data) {
            $quantidade = (int) ($data['quantidade'] ?? 1);

            if ($quantidade < 1) {
                throw new \InvalidArgumentException('Quantidade deve ser maior ou igual a 1.');
            }

            $estoque = Estoque::findOrFail($data['estoque_id']);
            $totalAtual = ProdutosUnidades::whereHas('produto', function ($query) use ($estoque) {
                $query->where('estoque_id', $estoque->id);
            })->count();
            $quantidadeMaxima = (int) $estoque->quantidade_maxima;

            if ($quantidadeMaxima > 0 && ($totalAtual + $quantidade) > $quantidadeMaxima) {
                throw new \RuntimeException('Não é possível cadastrar: o estoque excederia o limite máximo de ' . $quantidadeMaxima . ' itens.');
            }

            $imagem = $data['imagem'] ?? null;
            if ($imagem instanceof UploadedFile) {
                $imagem = $imagem->store('produtos', 'public');
            }

            $produto = Produto::create([
                'nome' => $data['nome'],
                'codigo_barras' => Produto::gerarCodigoBarrasUnico(),
                'unidade' => $data['unidade'] ?? 'un',
                'preco' => $data['preco'],
                'estoque_id' => $data['estoque_id'],
                'categoria_id' => $data['categoria_id'] ?? null,
                'fabricante_id' => $data['fabricante_id'] ?? null,
                'ativo' => $data['ativo'] ?? true,
                'imagem' => $imagem,
            ]);

            AuditLogger::info('produto.created', [
                'produto_id' => $produto->id,
                'estoque_id' => $produto->estoque_id,
                'quantidade_planejada' => $quantidade,
            ]);

            $indiceBase = optional($produto->unidades()->orderByDesc('id')->first())->id ?? 0;
            $unidadesCriadas = [];

            for ($i = 0; $i < $quantidade; $i++) {
                $codigo = ProdutoUnidadeService::gerarCodigo($produto, $indiceBase + $i + 1);
                $unidade = ProdutosUnidades::create([
                    'produto_id' => $produto->id,
                    'codigo_unico' => $codigo,
                    'status' => 'disponivel',
                ]);
                $unidadesCriadas[] = $unidade->id;
            }

            AuditLogger::info('produto.unidades.created', [
                'produto_id' => $produto->id,
                'unidades_ids' => $unidadesCriadas,
                'quantidade' => $quantidade,
            ]);

            MovimentacaoService::registrar([
                'produto_id' => $produto->id,
                'tipo' => 'entrada',
                'quantidade' => $quantidade,
                'observacao' => 'Cadastro inicial do produto',
            ]);

            AuditLogger::info('produto.movimentacao.entrada', [
                'produto_id' => $produto->id,
                'quantidade' => $quantidade,
            ]);

            MovimentacaoService::registrar([
                'produto_id' => $produto->id,
                'tipo' => 'disponivel',
                'quantidade' => $quantidade,
                'observacao' => 'Unidades disponíveis para venda',
            ]);

            AuditLogger::info('produto.movimentacao.disponivel', [
                'produto_id' => $produto->id,
                'quantidade' => $quantidade,
            ]);

            return $produto;
        });
    }

    public static function cadProdutoRequest($request)
    {
        $validated = $request->validate([
            'nome' => 'required|string|max:255',
            'codigo_barras' => 'nullable|string|max:50',
            'unidade' => 'nullable|string|max:10',
            'preco' => 'required|numeric|min:0',
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
}
