<?php

namespace App\Livewire;

use App\Helpers\FormatHelper;
use App\Models\Produto;
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
        return Produto::query()
            ->with(['categoria', 'fabricante', 'estoque', 'ultimaMovimentacao'])
            ->withCount([
                'unidades as disponiveis_count' => function ($q) {
                    $q->where('status', 'disponivel');
                },
                'unidades as vendidos_count' => function ($q) {
                    $q->where('status', 'vendido');
                },
            ])
            ->Ativo()
            ->orderBy('nome', 'asc');
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
                    ''                  => 'Todos',
                    'entrada'           => 'Entrada',
                    'disponivel'        => 'Disponível',
                    'saida'             => 'Vendido',
                    'cancelamento'      => 'Cancelado',
                    'sem_movimentacao'  => 'Sem movimentação',
                ])
                ->filter(function (Builder $query, $value) {
                    if ($value === 'sem_movimentacao') {
                        return $query->doesntHave('ultimaMovimentacao');
                    }

                    if ($value) {
                        return $query->whereHas('ultimaMovimentacao', function ($q) use ($value) {
                            $q->where('tipo', $value);
                        });
                    }
                }),
        ];
    }

    public function columns(): array
    {
        return [
            ImageColumn::make('Imagem', 'imagem')
                ->location(function ($row) {
                    return $row->imagem
                        ? asset('storage/' . $row->imagem)
                        : '/imagens/no-image.png';
                })
                ->attributes(fn() => [
                    'class' => 'img-thumbnail',
                    'style' => 'max-height:45px; object-fit:contain;',
                ]),

            Column::make('Nome', 'nome')
                ->sortable()
                ->searchable(),

            Column::make('Estoque', 'estoque.nome')
                ->sortable()
                ->searchable(),

            Column::make('Preço', 'preco')
                ->format(fn($value) => FormatHelper::brl($value))
                ->sortable(),
            Column::make('Disponíveis', 'id')
                ->format(
                    fn($value, $row) =>
                    view('components.table.status-venda-badge', ['disponivel' => $row->disponiveis_count])
                )
                ->sortable(),

            Column::make('Vendidos', 'id')
                ->format(
                    fn($value, $row) =>
                    view('components.table.status-venda-badge', ['vendido' => $row->vendidos_count])
                )
                ->sortable(),

            Column::make('Valor recebido', 'id')
                ->format(
                    fn($value, $row) =>
                    FormatHelper::brl(($row->vendidos_count ?? 0) * $row->preco)
                ),

            Column::make('Categoria', 'categoria.nome')
                ->sortable()
                ->searchable(),

            Column::make('Fabricante', 'fabricante.nome')
                ->sortable()
                ->searchable(),


            Column::make('Ações', 'id')
                ->format(function ($value, $row) {
                    return view('components.table.btn-table-actions', [
                        'show' => [
                            'route'      => route('produtos.show', [
                                'id'         => $value,
                                'nome'       => $row->nome,
                                'estoque_id' => $row->estoque_id,
                            ]),
                            'title'      => 'Ver produto: ' . $row->nome,
                            'componente' => '',
                            'modal'      => false,
                            'props'      => '',
                        ],
                    ]);
                }),
        ];
    }
}
