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
                ->format(function ($value, $row) {
                    if (config('features.sales_approval')) {
                        if ($row->aprovacao_status === 'pendente') {
                            return '<span class="badge badge-info">Pendente aprovação</span>';
                        }
                        if ($row->aprovacao_status === 'recusada') {
                            return '<span class="badge badge-danger">Recusada</span>';
                        }
                        if ($row->aprovacao_status === 'aprovada') {
                            return '<span class="badge badge-success">Aprovada</span>';
                        }
                    }
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
                    $custonComponents = [
                        [
                            'title' => 'Visualizar venda',
                            'componente' => 'vendas.visualizar-venda',
                            'props' => ['id' => $value, 'size' => 'modal-lg'],
                            'formId' => null,
                            'icon' => 'fas fa-eye',
                            'permitir' => true,
                        ],
                    ];
                    if (config('features.note_templates') && optional(auth()->user())->isAdmin()) {
                        $custonComponents[] = [
                            'title' => 'Emitir nota',
                            'componente' => 'vendas.emitir-nota',
                            'props' => ['vendaId' => $value, 'size' => 'modal-lg'],
                            'formId' => null,
                            'icon' => 'fas fa-file-alt',
                            'permitir' => $venda->status !== 'cancelada',
                        ];
                    }
                    if (config('features.sales_approval')
                        && optional(auth()->user())->isAdmin()
                        && $venda->aprovacao_status === 'pendente'
                    ) {
                        $custonComponents[] = [
                            'title' => 'Aprovar venda',
                            'componente' => 'vendas.aprovar-venda',
                            'props' => ['id' => $value],
                            'formId' => null,
                            'icon' => 'fas fa-check',
                            'permitir' => true
                        ];
                    }
                    if (optional(auth()->user())->isAdmin() && $venda->status !== 'cancelada') {
                        $custonComponents[] = [
                            'title' => 'Cancelar venda',
                            'componente' => 'vendas.cancelar-venda',
                            'props' => ['id' => $value],
                            'formId' => null,
                            'icon' => 'fas fa-ban',
                            'action' => 'danger',
                            'permitir' => true,
                        ];
                    }
                    return view('components.table.btn-table-actions', [
                       'custonComponents' => $custonComponents,
                        'edit' => [
                            'title' => 'Editar protocolo',
                            'componente' => 'vendas.atualizar-venda',
                            'props' => ['id' => $value , 'formId' => 'formUpdateVenda'],
                            'formId' => 'formUpdateVenda',
                            'permitir' =>  ($venda->status !== 'cancelada')
                                && (Auth::id() == $venda->user_id || optional(auth()->user())->isAdmin())
                       ],
                        "remove" => '',
                        'show' => '',
                        'restore' => '',
                    ]);
                }),
        ];
    }
}
