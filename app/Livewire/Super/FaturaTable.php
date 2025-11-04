<?php

namespace App\Livewire\Super;

use App\Helpers\FormatHelper;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\Views\Column;
use App\Models\Fatura;

class FaturaTable extends DataTableComponent
{
    protected $model = Fatura::class;

    // Recebe assinatura_id (opcional) para filtrar faturas específicas
    public ?int $assinaturaId = null;

    public function configure(): void
    {
        $this->setPrimaryKey('id')
            ->setDefaultSort('data_vencimento', 'desc')
            ->setTableAttributes([
                'class' => 'table table-bordered table-striped table-hover align-middle',
            ])
            ->setPaginationEnabled(true)
            ->setPerPageAccepted([10, 25, 50])
            ->setPerPage(10);
    }

    public function builder(): Builder
    {
        $query = Fatura::query()->with(['empresa', 'assinatura']);

        // Se for usada dentro da tela de uma assinatura específica
        if ($this->assinaturaId) {
            $query->where('assinatura_id', $this->assinaturaId);
        }

        return $query;
    }

    public function columns(): array
    {
        return [
            Column::make('Código', 'codigo')
                ->searchable()
                ->sortable(),

            Column::make('Empresa', 'empresa.nome')
                ->searchable()
                ->sortable(),

            Column::make('Valor', 'valor')
                ->format(fn($value) => FormatHelper::brl($value))
                ->sortable(),

            Column::make('Vencimento', 'data_vencimento')
                ->format(fn($value) => \Carbon\Carbon::parse($value)->format('d/m/Y'))
                ->sortable(),

            Column::make('Status', 'status')
                ->format(function ($value, $row) {
                    $color = match ($value) {
                        'pago' => 'success',
                        'pendente' => 'warning',
                        'cancelado' => 'danger',
                        default => 'secondary'
                    };
                    return "<span class='badge bg-{$color} text-uppercase'>{$value}</span>";
                })
                ->html()
                ->sortable(),

            Column::make('Pagamento', 'data_pagamento')
                ->format(fn($value) => $value ? \Carbon\Carbon::parse($value)->format('d/m/Y') : '-')
                ->sortable(),
            Column::make('Link', 'link_pagamento ')
                ->format(function ($value) {
                    // Verifica se o link de pagamento existe
                    if ($value) {
                        // Retorna o link formatado como botão
                        return '<a href="' . $value . '" target="_blank" class="btn btn-sm btn-outline-primary">
                        <i class="fa fa-credit-card"></i> Pagar
                    </a>';
                    }
                    // Caso o link não exista, retorna um texto indicativo
                    return "-";
                })
                ->html()
                ->sortable(),

            Column::make('Ações', 'id')
                ->format(function ($value, $row) {
                    return view('livewire.partials.faturas-actions', ['row' => $row]);
                })
                ->html(),
        ];
    }
}
