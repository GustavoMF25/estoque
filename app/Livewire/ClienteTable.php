<?php

namespace App\Livewire;

use App\Models\Cliente;
use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;

class ClienteTable extends DataTableComponent
{
    protected $model = Cliente::class;

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
        $query =  Cliente::query();
        return $query;
    }

    public function columns(): array
    {
        return [
            Column::make('Nome', 'Nome')
                ->sortable()
                ->searchable(),
            Column::make('Email', 'email')
                ->sortable()
                ->searchable(),
            Column::make('Telefone', 'telefone')
                ->format(function ($value, $row) {
                    return view('components.table.status-badge', ['status' => $value]);
                })
                ->searchable()
                ->sortable(),
            Column::make('Cpf', 'documento')
                ->format(function ($value, $row) {
                    return $value;
                }),
            Column::make('Opções', 'id')
                ->format(function ($value, $row) {
                    $cliente = Cliente::where('id', $value)->first();

                    return view('components.table.btn-table-actions', [
                        'edit' => [
                            'title' => 'Editar Cliente',
                            'componente' => 'clientes.cliente-editar',
                            'props' => ['id' => $value],
                            'formId' => 'salvar-editar-cliente'
                        ],
                        'custonComponent' => [
                            'title' => 'Editar Cliente',
                            'componente' => 'clientes.endereco-editar',
                            'props' => ['id' => $cliente->enderecoPadrao->id],
                            'formId' => 'salvar-editar-endereco',
                            'icon' => 'fas fa-truck-moving'
                        ]

                    ]);
                }),
        ];
    }
}
