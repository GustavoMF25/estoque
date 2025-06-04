<?php

namespace App\Livewire\Produto;

use App\Helpers\FormatHelper;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;
use App\Models\Produto;
use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\Views\Columns\ImageColumn;

class ProdutosVisualizarTable extends DataTableComponent
{
    public $nome;
    public $estoque_id;
    public $ultima_movimentacao;

    public function configure(): void
    {
        $this->setPrimaryKey('id');
    }

    public function builder(): Builder
    {
        $query = Produto::with(['estoque', 'ultimaMovimentacao'])->withTrashed();

        if (!empty($this->nome)) {
            $query->where('produtos.nome', 'LIKE', "%{$this->nome}%");
        }

        if (!empty($this->estoque_id)) {
            $query->where('produtos.estoque_id', $this->estoque_id);
        }
        if (!empty($this->ultima_movimentacao)) {
            $query->whereHas('ultimaMovimentacao', function ($q) {
                $q->where('tipo', $this->ultima_movimentacao);
            });
        }
        $query->select([
            'produtos.id',
            'produtos.nome',
            'produtos.imagem',
            'produtos.preco',
            'produtos.estoque_id',
            'produtos.created_at',
        ]);

        return $query;
    }

    public function columns(): array
    {
        return [
            ImageColumn::make('Imagem')
                ->location(function ($row) {
                    return asset('storage/' . $row->imagem);
                })
                ->attributes(fn($row) => [
                    'class' => 'img-circle',
                    'style' => 'width: 40px;',
                    'alt' => $row->nome . ' Avatar',
                ]),
            Column::make('Nome', 'nome')
                ->sortable()
                ->searchable(),
            Column::make('Preço', 'preco')
                ->format(function ($value, $row) {
                    // dd($row);
                    return FormatHelper::brl($value);
                })
                ->searchable(),
            Column::make('Estoque', 'estoque.nome')
                ->searchable(),
            Column::make('Status', 'ultimaMovimentacao.tipo')
                ->label(fn($row) => ucfirst(str_replace('_', ' ', optional($row->ultimaMovimentacao)->tipo ?? 'sem movimentação')))
                ->searchable()
                ->sortable(),
            Column::make('Criado em', 'created_at')->sortable()->format(fn($value) => $value->format('d/m/Y')),
            // Column::make('Ações', 'id')
            //     ->format(function ($value, $row) {
            //         $produto = Produto::with(['estoque', 'movimentacoes'])->withTrashed()->findOrFail($value);
            //         return view('components.table.btn-table-actions', [
            //             "remove" => [
            //                 'route' => route('produtos.destroy', $value),
            //             ],
            //             'show' => [
            //                 'title' => 'Estoque → ' . $row->nome,
            //                 'componente' => 'produto.produto-visualizar',
            //                 'props' => ['produtoId' => $produto->id]
            //             ],
            //             // 'edit' => [
            //             //    'title' => 'Editar Estoque → ' . $row->nome,
            //             //     'componente' => 'produto.produto-visualizar',
            //             //     'props' => ['produtoId' => $produto->id]
            //             // ]
            //         ]);
            //     }),
        ];
    }
}
