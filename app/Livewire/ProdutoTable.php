<?php

namespace App\Livewire;

use App\Models\Produto;
use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;

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


    public function columns(): array
    {
        return [
            Column::make('ID', 'id')->sortable(),
            Column::make('Código de Barras', 'codigo_barras')->searchable()->sortable(),
            Column::make('Preço', 'preco')->searchable(),
            Column::make('Estoque', 'estoque.nome')->searchable(),
            Column::make('Criado em', 'created_at')->sortable()->format(fn($value) => $value->format('d/m/Y')),
            Column::make('Ações', 'id')
            ->format(function ($value){
                return view('components.table.btn-table-actions', [
                    "remove" => [
                        'route' => route('produtos.destroy', $value),
                    ]
                ]);
            }),
        ];
    }
}
