<?php

namespace App\Livewire;

use App\Models\AuditLog;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;
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
                ->config(['placeholder' => 'ex: produto.created ou venda'])
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
                ->format(function ($value) {
                    $label = $this->humanizeAction((string) $value);
                    $class = $this->actionBadgeClass((string) $value);

                    return '<div class="d-flex flex-column gap-1">'
                        . '<span class="badge ' . e($class) . '">' . e($label) . '</span>'
                        . '<small class="text-muted">' . e($value) . '</small>'
                        . '</div>';
                })
                ->html(),
            Column::make('Resumo', 'details')
                ->label(fn($row) => $this->buildSummary($row))
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
                        return '<span class="text-muted small">Sem detalhes técnicos</span>';
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

    private function humanizeAction(string $action): string
    {
        $map = [
            'produto.created' => 'Produto cadastrado',
            'produto.cadastro.controller' => 'Cadastro de produto iniciado',
            'produto.cadastro.falhou' => 'Falha ao cadastrar produto',
            'produto.unidades.iniciais' => 'Unidades iniciais criadas',
            'produto.unidades.adicionadas' => 'Unidades adicionadas',
            'produto.unidades.adicao.iniciada' => 'Adição de unidades iniciada',
            'produto.unidades.adicao.concluida' => 'Adição de unidades concluída',
            'produto.unidades.status_alterado' => 'Status da unidade alterado',
            'produto.movimentacao.entrada' => 'Entrada no estoque',
            'produto.movimentacao.disponivel' => 'Item marcado como disponível',
            'produto.venda.realizada' => 'Venda de produto registrada',
            'estoque.created' => 'Estoque cadastrado',
            'estoque.updated' => 'Estoque atualizado',
            'estoque.removido' => 'Estoque removido',
            'estoque.remocao.negada' => 'Remoção de estoque negada',
            'estoque.restaurado' => 'Estoque restaurado',
            'venda.aprovada' => 'Venda aprovada',
            'venda.recusada' => 'Venda recusada',
        ];

        if (isset($map[$action])) {
            return $map[$action];
        }

        $normalized = str_replace(['.', '_'], ' ', $action);
        return Str::of($normalized)->headline()->toString();
    }

    private function actionBadgeClass(string $action): string
    {
        $failWords = ['falhou', 'negada', 'recusada', 'erro', 'cancelada'];
        $warnWords = ['pendente', 'atrasad', 'alerta'];

        foreach ($failWords as $word) {
            if (Str::contains($action, $word)) {
                return 'badge-danger';
            }
        }

        foreach ($warnWords as $word) {
            if (Str::contains($action, $word)) {
                return 'badge-warning';
            }
        }

        if (Str::contains($action, ['created', 'concluida', 'aprovada', 'restaurado'])) {
            return 'badge-success';
        }

        return 'badge-info';
    }

    private function buildSummary($row): string
    {
        $details = is_array($row->details) ? $row->details : [];
        $action = (string) $row->action;

        $parts = [];
        $nome = $this->firstFilled($details, ['produto_nome', 'produto', 'nome', 'categoria', 'fabricante']);
        $qtd = $this->firstFilled($details, ['quantidade', 'quantidade_vendida', 'quantidade_planejada']);
        $estoqueId = $this->firstFilled($details, ['estoque_id']);
        $vendaId = $this->firstFilled($details, ['venda_id']);

        if ($nome) {
            $parts[] = "Item: {$nome}";
        }
        if ($qtd !== null && $qtd !== '') {
            $parts[] = "Qtd: {$qtd}";
        }
        if ($estoqueId) {
            $parts[] = "Estoque #{$estoqueId}";
        }
        if ($vendaId) {
            $parts[] = "Venda #{$vendaId}";
        }

        if (empty($parts)) {
            $fallback = match (true) {
                Str::contains($action, 'estoque') => 'Operação relacionada ao estoque',
                Str::contains($action, 'produto') => 'Operação relacionada ao produto',
                Str::contains($action, 'venda') => 'Operação relacionada à venda',
                default => 'Evento registrado no sistema',
            };

            return '<span class="text-muted small">' . e($fallback) . '</span>';
        }

        return '<span class="small">' . e(implode(' | ', $parts)) . '</span>';
    }

    private function firstFilled(array $details, array $keys): mixed
    {
        foreach ($keys as $key) {
            if (array_key_exists($key, $details) && $details[$key] !== null && $details[$key] !== '') {
                return is_scalar($details[$key]) ? $details[$key] : null;
            }
        }
        return null;
    }
}
