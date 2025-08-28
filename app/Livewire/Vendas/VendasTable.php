<?php

namespace App\Livewire\Vendas;

use App\Helpers\FormatHelper;
use App\Models\User;
use App\Models\Venda;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;

class VendasTable extends DataTableComponent
{
    protected $model = Venda::class;

    protected $listeners = ['refreshTabelaVendas' => '$refresh'];

    public function configure(): void
    {
        $this->setPrimaryKey('id')
            ->setTableAttributes([
                'class' => 'table table-bordered table-striped table-hover align-middle',
            ])
            ->setDefaultSort('created_at', 'desc')
            ->setPaginationEnabled(true)
            ->setPerPageAccepted([5, 10, 25, 50])
            ->setDefaultPerPage(5);
    }

    public function builder(): Builder
    {
        return Venda::query()->with('usuario', 'loja');
    }

    public function columns(): array
    {
        return [
            Column::make("ID", "id")
                ->sortable()
                ->searchable(),

            Column::make("Protocolo", "protocolo")
                ->sortable()
                ->searchable(),

            Column::make("Usuário", "usuario.name")
                ->sortable()
                ->searchable(),
            Column::make("Valor Total", "valor_total")
                ->sortable()
                ->format(fn($value) => 'R$ ' . FormatHelper::brl($value)),
            Column::make("Status", "status")
                ->sortable()
                ->format(function ($value) {
                    return match ($value) {
                        'paga' => '<span class="badge badge-success">Paga</span>',
                        'aberta' => '<span class="badge badge-warning">Aberta</span>',
                        'cancelada' => '<span class="badge badge-danger">Cancelada</span>',
                        default => '<span class="badge badge-secondary">' . ucfirst($value) . '</span>',
                    };
                })
                ->html(),

            Column::make("Data", "created_at")
                ->sortable()
                ->format(fn($value) => \Carbon\Carbon::parse($value)->format('d/m/Y H:i')),
            Column::make('Ações', 'id')
                ->format(function ($value, $row) {
                    $venda = Venda::findOrFail($value);
                    return view('components.table.btn-table-actions', [
                       'edit' => [
                            'title' => 'Editar protocolo',
                            'componente' => 'vendas.atualizar-venda',
                            'props' => ['id' => $value , 'formId' => 'formUpdateVenda'],
                            'formId' => 'formUpdateVenda',
                            'permitir' =>  Auth::id() == $venda->user_id || optional(auth()->user())->isAdmin()
                       ],
                        "remove" => '',
                        'show' => '',
                        'restore' => '',
                        'pdf' => [
                            'route' => route('vendas.nota', $value)
                        ]
                    ]);
                }),
        ];
    }
}
