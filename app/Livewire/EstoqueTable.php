<?php

namespace App\Livewire;

use App\Models\Estoque;
use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;

class EstoqueTable extends DataTableComponent
{
    protected $model = Estoque::class;

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
            // Column::make('#', 'id')->sortable(),
            Column::make('Nome', 'nome')->searchable()->sortable(),
            Column::make('Descricao', 'descricao')->searchable(),
            Column::make('Quantidade máxima', 'quantidade_maxima')->searchable(),
            Column::make('Status', 'status')->searchable(),
            Column::make('Criado em', 'created_at')->sortable()->format(fn($value) => $value->format('d/m/Y')),
            Column::make('Ações', 'id')
            ->format(function ($value){
                return view('components.table.btn-table-actions', [
                    "remove" => [
                        'route' => route('estoques.destroy', $value),
                    ]
                ]);
            }),
        ];
    }
}
