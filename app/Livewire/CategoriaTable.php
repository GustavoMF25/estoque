<?php

namespace App\Livewire;

use App\Models\Categoria;
use App\Models\Estoque;
use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;

class CategoriaTable extends DataTableComponent
{
    protected $model = Categoria::class;

    public function builder(): Builder
    {
        return Categoria::query();
    }

    public function configure(): void
    {
        $this->setPrimaryKey('id')
            ->setTableAttributes([
                'class' => 'table table-bordered table-striped table-hover align-middle',
            ])
            ->setPaginationEnabled(true)
            ->setPerPageAccepted([5, 10, 25, 50])
            ->setDefaultPerPage(5);
    }

    public function columns(): array
    {
        return [
            Column::make('Nome', 'nome')->searchable()->sortable(),
            Column::make('Descricao', 'descricao')->searchable(),
            Column::make('Status', 'ativo')
                ->format(function ($value, $row) {
                    if($value){
                        return 'Ativo';
                    }
                    return 'Inativo';
                })
                ->searchable()
                ->sortable(),
            Column::make('Ações', 'id')
                ->format(function ($value, $row) {
                    return view('components.table.btn-table-actions', [
                        "remove" => [
                            'route' => route('categorias.destroy', $value),
                        ],
                        // 'show' => [
                        //     'title' => 'Estoque → ' . $row->nome,
                        //     'componente' => 'estoque.estoque-visualizar',
                        //     'props' => ['estoqueId' => $value],
                        //     'modal' => true,
                        //     'route' => ''
                        // ],
                        // 'restore' => [
                        //     'route' => route('estoques.restore', $value)
                        // ]
                    ]);
                }),
        ];
    }
}
