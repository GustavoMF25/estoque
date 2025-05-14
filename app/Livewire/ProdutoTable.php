<?php

namespace App\Livewire;

use App\Helpers\FormatHelper;
use App\Models\Produto;
use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;
use Rappasoft\LaravelLivewireTables\Views\Columns\ImageColumn;

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
        $query =  Produto::query();
        if (optional(auth()->user())->isAdmin()) {
            $query->withTrashed();
        }
        $query->select([
            'produtos.id',
            'produtos.nome',
            'produtos.codigo_barras',
            'produtos.imagem', // 🔥 o que estava faltando!
            'produtos.preco',
            'produtos.estoque_id',
            'produtos.created_at',
        ])
            ->with('estoque', 'ultimaMovimentacao');
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
            Column::make('Nome', 'Nome')->sortable()->searchable(),

            Column::make('Código de Barras', 'codigo_barras')->searchable()
                ->sortable()
                ->searchable(),
            Column::make('Preço', 'preco')
                ->format(function ($value) {
                    return FormatHelper::brl($value);
                })
                ->searchable(),
            Column::make('Estoque', 'estoque.nome')->searchable(),
            Column::make('Status', 'ultimaMovimentacao.tipo')
                ->label(fn($row) => ucfirst(str_replace('_', ' ', optional($row->ultimaMovimentacao)->tipo ?? 'sem movimentação')))
                ->searchable()
                ->sortable(),
            Column::make('Criado em', 'created_at')->sortable()->format(fn($value) => $value->format('d/m/Y')),
            Column::make('Ações', 'id')
                ->format(function ($value, $row) {
                    $produto = Produto::with(['estoque', 'movimentacoes'])->withTrashed()->findOrFail($value);
                    return view('components.table.btn-table-actions', [
                        "remove" => [
                            'route' => route('produtos.destroy', $value),
                        ],
                        'show' => [
                            'title' => 'Estoque → ' . $row->nome,
                            'view' => view('produto.show', ['produto' => $produto])->render()
                        ],
                        // 'edit' => [
                        //     'title' => 'Editar Produto: ' . $produto->nome,
                        //     'view' => '<livewire:produto-editar :produto-id="' . $produto->id . '" />'
                        // ]
                    ]);
                }),
        ];
    }
}
