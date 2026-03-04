<?php

namespace App\Livewire;

use App\Models\ProdutoChegada;
use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;

class ProdutoChegadaTable extends DataTableComponent
{
    protected $model = ProdutoChegada::class;

    public function configure(): void
    {
        $this->setPrimaryKey('id')
            ->setTableAttributes([
                'class' => 'table table-bordered table-striped table-hover align-middle',
            ])
            ->setPaginationEnabled(true);
    }

    public function builder(): Builder
    {
        return ProdutoChegada::query()
            ->with(['produto', 'criadoPor'])
            ->select('produto_chegadas.*')
            ->orderByRaw("CASE WHEN produto_chegadas.status = 'aberto' THEN 0 ELSE 1 END")
            ->orderByDesc('produto_chegadas.created_at');
    }

    public function columns(): array
    {
        return [
            Column::make('#', 'id')
                ->sortable(fn (Builder $query, string $direction) => $query->orderBy('produto_chegadas.id', $direction)),
            Column::make('Produto', 'produto.nome')
                ->format(fn ($value, $row) => $row->produto->nome ?? 'Produto removido')
                ->searchable()
                ->sortable(),
            Column::make('Quantidade', 'quantidade_total')
                ->sortable(fn (Builder $query, string $direction) => $query->orderBy('produto_chegadas.quantidade_total', $direction)),
            Column::make('Comprometida', 'quantidade_comprometida')
                ->sortable(fn (Builder $query, string $direction) => $query->orderBy('produto_chegadas.quantidade_comprometida', $direction)),
            Column::make('Disponível', 'id')
                ->format(fn ($value, $row) => (int) $row->quantidade_disponivel),
            Column::make('Previsão', 'previsao_chegada')
                ->format(fn ($value) => $value ? $value->format('d/m/Y') : '-')
                ->sortable(fn (Builder $query, string $direction) => $query->orderBy('produto_chegadas.previsao_chegada', $direction)),
            Column::make('Status', 'status')
                ->format(function ($value) {
                    if ($value === 'aberto') {
                        return '<span class="badge badge-warning">Aberto</span>';
                    }

                    if ($value === 'recebido') {
                        return '<span class="badge badge-success">Recebido</span>';
                    }

                    return '<span class="badge badge-secondary">Cancelado</span>';
                })
                ->html()
                ->sortable(fn (Builder $query, string $direction) => $query->orderBy('produto_chegadas.status', $direction)),
            Column::make('Criado por', 'criadoPor.name')
                ->format(fn ($value, $row) => $row->criadoPor->name ?? '-')
                ->searchable()
                ->sortable(),
            Column::make('Ações', 'id')
                ->format(fn ($value, $row) => view('components.table.produto-chegada-actions', ['chegada' => $row])),
        ];
    }
}
