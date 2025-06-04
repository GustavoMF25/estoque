<?php

namespace App\Livewire;

use App\Helpers\FormatHelper;
use App\Models\Produto;
use App\Models\ProdutosAgrupados;
use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;
use Rappasoft\LaravelLivewireTables\Views\Columns\ImageColumn;
use Rappasoft\LaravelLivewireTables\Views\Filters\SelectFilter;

class ProdutoTable extends DataTableComponent
{

    protected $model = Produto::class;

    public function configure(): void
    {
        $this->setPrimaryKey('id')
            ->setTableAttributes([
                'class' => 'table table-bordered table-striped table-hover align-middle',
            ])
            ->setPaginationEnabled(true)
            ->setPerPage(10);
    }

    public function builder(): Builder
    {
        $query =  ProdutosAgrupados::query();
        $query->select([
            'nome',
            'imagem', // 🔥 o que estava faltando!
            'preco',
            'estoque_id',
            'quantidade_produtos',
            'data_criacao',
            'estoque_nome',
        ])->orderBy('nome', 'asc');
        return $query;
    }

    public function filters(): array
    {
        return [
            SelectFilter::make('Estoque')
                ->options(
                    \App\Models\Estoque::pluck('nome', 'id')->prepend('Todos', '')->toArray()
                )
                ->filter(function (Builder $query, $value) {
                    if ($value) {
                        $query->where('estoque_id', $value);
                    }
                }),

            SelectFilter::make('Status')
                ->options([
                    '' => 'Todos',
                    'entrada' => 'Entrada',
                    'disponivel' => 'Disponível',
                    'saida' => 'Vendido',
                    'sem_movimentacao' => 'Sem Movimentação',
                ])
                ->filter(function (Builder $query, $value) {
                    if ($value === 'sem_movimentacao') {
                        $query->doesntHave('ultimaMovimentacao');
                    } elseif ($value) {
                        $query->whereHas('ultimaMovimentacao', fn($q) => $q->where('tipo', $value));
                    }
                })
        ];
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
            Column::make('Nome', 'Nome')->sortable()->searchable(),

            Column::make('Quantidade', 'quantidade_produtos')->searchable()
                ->sortable()
                ->searchable(),
            Column::make('Preço', 'preco')
                ->format(function ($value) {
                    return FormatHelper::brl($value);
                })
                ->searchable(),
            Column::make('Estoque', 'estoque_nome')->searchable(),
            Column::make('Status', 'ultima_movimentacao')
                ->format(function ($value, $row) {
                    return ucfirst(str_replace('_', ' ', $value ?? 'sem movimentação'));
                })
                ->searchable()
                ->sortable(),
            Column::make('Ações', 'nome')
                ->format(function ($value, $row) {
                    // dump($row);
                    // return '';
                    return view('components.table.btn-table-actions', [
                        // "remove" => [
                        //     'route' => route('produtos.destroy', $value),
                        // ],
                        'show' => [
                            'title' => 'Estoque → ' . $row->nome,
                            'componente' => 'produto.produto-visualizar',
                            'props' => ['nome' => $row->nome, 'estoque_id' => $row->estoque_id, 'ultima_movimentacao' => $row->ultima_movimentacao]
                        ],
                        // 'edit' => [
                        //    'title' => 'Editar Estoque → ' . $row->nome,
                        //     'componente' => 'produto.produto-visualizar',
                        //     'props' => ['produtoId' => $produto->id]
                        // ]
                    ]);
                }),
        ];
    }
}
