<?php

namespace App\Livewire\Vendas;

use App\Helpers\FormatHelper;
use App\Models\User;
use App\Models\Venda;
use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;

class VendasTable extends DataTableComponent
{
    protected $model = Venda::class;

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
                    return view('components.table.btn-table-actions', [
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
