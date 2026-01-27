<?php

namespace App\Livewire;

use App\Models\AuditLog;
use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;
use Rappasoft\LaravelLivewireTables\Views\Filters\NumberFilter;
use Rappasoft\LaravelLivewireTables\Views\Filters\TextFilter;

class AuditLogTable extends DataTableComponent
{
    protected $model = AuditLog::class;

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
            ->setDefaultPerPage(10);
    }

    public function builder(): Builder
    {
        return AuditLog::query()
            ->with('user')
            ->orderByDesc('created_at');
    }

    public function filters(): array
    {
        return [
            TextFilter::make('Ação')
                ->config(['placeholder' => 'ex: produto.created'])
                ->filter(function (Builder $query, string $value) {
                    $query->where('action', 'like', '%' . $value . '%');
                }),
            NumberFilter::make('Usuário (ID)')
                ->config(['min' => 1, 'placeholder' => 'ID do usuário'])
                ->filter(function (Builder $query, $value) {
                    $query->where('user_id', $value);
                }),
        ];
    }

    public function columns(): array
    {
        return [
            Column::make('#', 'id')
                ->sortable(),
            Column::make('Ação', 'action')
                ->sortable()
                ->searchable()
                ->format(fn($value) => '<span class="badge badge-info">' . e($value) . '</span>')
                ->html(),
            Column::make('Usuário', 'user.name')
                ->sortable()
                ->searchable()
                ->format(function ($value, $row) {
                    if ($row->user) {
                        return e($row->user->name) . ' <span class="text-muted small">(#' . e($row->user_id) . ')</span>';
                    }
                    return '<span class="text-muted">N/A</span>';
                })
                ->html(),
            Column::make('IP', 'ip_address')
                ->searchable()
                ->sortable()
                ->format(fn($value) => $value ?: '—'),
            Column::make('Navegador', 'user_agent')
                ->searchable()
                ->format(function ($value) {
                    $text = $value ? e(\Illuminate\Support\Str::limit($value, 60)) : '—';
                    $title = $value ? ' title="' . e($value) . '"' : '';
                    return '<span class="text-muted small text-truncate d-inline-block" style="max-width: 220px;"' . $title . '>' . $text . '</span>';
                })
                ->html(),
            Column::make('Detalhes', 'details')
                ->format(function ($value, $row) {
                    $details = $row->details ? json_encode($row->details, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) : null;
                    if (!$details) {
                        return '<span class="text-muted small">Sem detalhes</span>';
                    }
                    return '<details><summary class="small text-primary">Ver detalhes</summary><pre class="mb-0 small mt-2">'
                        . e($details)
                        . '</pre></details>';
                })
                ->html(),
            Column::make('Registrado em', 'created_at')
                ->sortable()
                ->format(fn($value) => $value->format('d/m/Y H:i:s')),
        ];
    }
}
