<?php

namespace App\Livewire\Produto;

use App\Helpers\FormatHelper;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;
use App\Models\Produto;
use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\Views\Columns\ImageColumn;

class ProdutosVisualizarTable extends DataTableComponent
{
    public $nome;
    public $estoque_id;
    public $ultima_movimentacao;

    protected $listeners = ['refreshTabelaVisualizarProduto' => '$refresh'];

    public function configure(): void
    {
        $this->setPrimaryKey('id')
            ->setTableAttributes([
                'class' => 'table table-bordered table-striped table-hover align-middle',
            ])
            ->setPaginationEnabled(true)
            ->setPerPageAccepted([5, 10, 25, 50])
            ->setDefaultPerPage(5);
    }

    public function builder(): Builder
    {
        $query = Produto::with(['estoque', 'ultimaMovimentacao', 'ultimaMovimentacao.usuario'])->withTrashed();

        if (!empty($this->nome)) {
            $query->where('produtos.nome', 'LIKE', "%{$this->nome}%");
        }

        if (!empty($this->estoque_id)) {
            $query->where('produtos.estoque_id', $this->estoque_id);
        }
        if (!empty($this->ultima_movimentacao)) {
            $query->whereHas('ultimaMovimentacao', function ($q) {
                $q->where('tipo', $this->ultima_movimentacao);
            });
        }
        $query->select([
            'produtos.id',
            'produtos.nome',
            'produtos.imagem',
            'produtos.preco',
            'produtos.estoque_id',
            'produtos.created_at',
        ]);

        return $query;
    }

    public function columns(): array
    {
        return [
            Column::make('Nome', 'nome')
                ->sortable()
                ->searchable(),
            Column::make('Preço', 'preco')
                ->format(function ($value, $row) {
                    return FormatHelper::brl($value);
                })
                ->searchable(),
            Column::make('Estoque', 'estoque.nome')
                ->searchable(),
            Column::make('Status', 'ultimaMovimentacao.tipo')
                ->label(fn($row) => view('components.table.status-badge', ['status' => optional($row->ultimaMovimentacao)->tipo]))
                ->searchable()
                ->sortable(),
            Column::make('Vendido por', 'ultimaMovimentacao.user_id')
                // ->label(fn($row) => optional($row->ultimaMovimentacao->usuario)->name ?? '—')
                ->format(function ($value, $row) {
                    if ($row->ultimaMovimentacao->tipo == 'saida') {
                        return optional($row->ultimaMovimentacao->usuario)->name;
                    }
                })
                ->searchable()
                ->sortable(),
            Column::make('Criado em', 'created_at')
                ->sortable()
                ->format(fn($value) => $value->format('d/m/Y')),
            Column::make('Ações', 'id')
                ->format(function ($value, $row) {
                    if ($row->ultimaMovimentacao->tipo != 'saida') {
                        return view('components.table.btn-table-actions', [
                            'remove' => [
                                'route' => route('produtos.destroy', $value),
                            ],
                            'edit' => [
                                'title' => 'Editar Estoque → ' . $row->nome,
                                'componente' => 'produto-editar',
                                'props' => ['produtoId' => $row->id],
                                'formId' => 'salvar-editar-produto'
                            ]
                        ]);
                    }
                }),
        ];
    }
}
