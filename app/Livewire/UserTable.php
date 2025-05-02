<?php

namespace App\Livewire;

use App\Models\User;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;

class UserTable extends DataTableComponent
{
    protected $model = User::class;

    public function configure(): void
    {
        $this->setPrimaryKey('id')
            ->setPaginationEnabled(true)
            ->setPerPageAccepted([5, 10, 25, 50])
            ->setTableAttributes([
                'class' => 'table table-bordered table-striped table-hover align-middle',
            ])
            ->setPaginationTheme('bootstrap')
            ->setPaginationMethod('standard')
            ->setDefaultPerPage(5);
    }


    public function columns(): array
    {
        return [
            Column::make('Nome', 'name')
                ->searchable()
                ->sortable(),

            Column::make('Email', 'email')
                ->searchable()
                ->sortable(),

            Column::make('CPF', 'cpf')
                ->searchable()
                ->sortable(),

            Column::make('Status', 'status')
                ->sortable()
                ->format(fn($value) => $value === 'ativo'
                    ? '<span class="badge badge-success">Ativo</span>'
                    : '<span class="badge badge-secondary">Inativo</span>')
                ->html(),

            Column::make('Perfil', 'perfil')
                ->sortable()
                ->searchable(),

            Column::make('Criado em', 'created_at')
                ->sortable()
                ->format(fn($value) => $value->format('d/m/Y H:i')),

            Column::make('Ações', 'id')
                ->format(function ($value) {
                    return view('components.table.btn-table-actions', [
                        "remove" => [
                            'route' => route('usuarios.destroy', $value),
                        ]
                    ]);
                })
        ];
    }
}
