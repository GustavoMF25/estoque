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
            'ultima_movimentacao'
        ])
        ->orderBy('ultima_movimentacao', 'desc')
        ->orderBy('nome', 'asc')
        ->whereNotIn('ultima_movimentacao', ['cancelamento']);
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
            Column::make('Quantidade', 'quantidade_produtos')->searchable()
                ->sortable()
                ->searchable(),
            Column::make('Status', 'ultima_movimentacao')
                ->format(function ($value, $row) {
                    return view('components.table.status-badge', ['status' => $value] );
                })
                ->searchable()
                ->sortable(),
            Column::make('Ações', 'nome')
                ->format(function ($value, $row) {
                    return view('components.table.btn-table-actions', [
                        "show" => [
                            'route' => route('produtos.show', ['nome' => $row->nome, 'estoque_id' => $row->estoque_id, 'ultima_movimentacao' => $row->ultima_movimentacao]),
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
