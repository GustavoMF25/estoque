<?php

namespace App\Livewire;

use App\Models\Categoria;
use App\Models\Estoque;
use App\Models\Fabricante;
use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;

class FabricanteTable extends DataTableComponent
{
    protected $model = Fabricante::class;

    public function builder(): Builder
    {
        return Fabricante::query();
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
            Column::make('Site', 'site')->searchable(),
            Column::make('Ações', 'id')
                ->format(function ($value, $row) {
                    return view('components.table.btn-table-actions', [
                        "remove" => [
                            'route' => route('fabricantes.destroy', $value),
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
