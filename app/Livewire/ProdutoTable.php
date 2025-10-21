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
        $query = Produto::query()
            ->withCount([
                'movimentacoes',
                'unidades as disponiveis_count' => function ($q) {
                    $q->where('status', 'disponivel');
                },
                'unidades as vendidos_count' => function ($q) {
                    $q->where('status', 'vendido');
                },
            ])
            ->with(['ultimaMovimentacao', 'estoque'])
            ->whereHas('ultimaMovimentacao', function ($q) {
                $q->whereNotIn('tipo', ['cancelamento']);
            })
            ->where('ativo', 1)
            ->orderBy('nome', 'asc');

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
                        return    $query->doesntHave('ultimaMovimentacao');
                    }
                    return  $query->where('ultima_movimentacao', $value);
                })
        ];
    }


    public function columns(): array
    {
        return [
            Column::make('Nome', 'Nome')
                ->sortable()
                ->searchable(),
            Column::make('Disponíveis', 'id')
                ->format(fn($value, $row) => $row->disponiveis_count)
                ->searchable()
                ->sortable(),
            Column::make('Vendidos', 'id')
                ->format(fn($value, $row) => $row->vendidos_count)
                ->searchable()
                ->sortable(),
            Column::make('Ações', 'id')
                ->format(function ($value, $row) {
                    return view('components.table.btn-table-actions', [
                        "show" => [
                            'route' => route('produtos.show', ['id' => $value, 'nome' => $row->nome, 'estoque_id' => $row->estoque_id]),
                            'title' => 'Estoque → ' . $row->nome,
                            'componente' => '',
                            'modal' => false,
                            'props' => ''
                        ],

                    ]);
                }),
        ];
    }
}
