<?php

namespace App\Livewire;

use App\Models\User;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;
use Rappasoft\LaravelLivewireTables\Views\Columns\ImageColumn;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class UserTable extends DataTableComponent
{
    protected $model = User::class;

    protected $listeners = ['refreshTabelaUsuarios' => '$refresh'];

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

    public function builder(): Builder
    {
        $query =  User::query()
            ->select([
                'users.id',
                'users.name',
                'users.email',
                'users.cpf', // 🔥 o que estava faltando!
                'users.status',
                'users.perfil',
                'users.profile_photo_path',
                'users.created_at',
            ]);
        return $query;
    }

    public function columns(): array
    {
        return [
            ImageColumn::make('Avatar')
                ->location(function ($row) {
                    return asset('storage/' . $row->profile_photo_path);
                })
                ->attributes(fn($row) => [
                    'class' => 'img-circle',
                    'style' => 'width: 50px;',
                    'alt' => $row->nome . ' Avatar',
                ]),
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
                    $user = User::findOrFail($value);
                    return view('components.table.btn-table-actions', [
                        "remove" => [
                            'route' => route('usuarios.destroy', $value),
                        ],
                        'edit' => [
                            'title' => 'Editar Usuario → ' . $user->name,
                            'componente' => 'usuario.atualizar-usuario',
                            'props' => ['userId' => $user->id , 'formId' => 'formUserUpdate'],
                            'formId' => 'formUserUpdate',
                            'permitir' => Auth::id() == $user->id
                        ]
                    ]);
                })
        ];
    }
}
