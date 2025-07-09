<?php
namespace App\Livewire\Produto;

use App\Helpers\FormatHelper;
use App\Models\Produto;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Component;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;

class ProdutosVisualizarTable extends DataTableComponent
{
    public string $nome = '';
    public int $estoqueId = 0;
    public string $ultimaMovimentacao = '';

    protected $listeners = ['refreshTabelaVisualizarProduto' => '$refresh'];

    public function configure(): void
    {
        $this->setPrimaryKey('id')
            ->setTableAttributes([
                'class' => 'table table-bordered table-striped table-hover align-middle',
            ])
            ->setPaginationEnabled(true)
            ->setDefaultPerPage(5);
    }

    public function builder(): Builder
    {
        $query = Produto::with([
                'estoque',
                'ultimaMovimentacao',
                'ultimaMovimentacao.usuario'
            ])
            ->withTrashed();

        if (!empty($this->nome)) {
            $query->where('produtos.nome', 'LIKE', "%{$this->nome}%");
        }

        if (!empty($this->ultimaMovimentacao)) {
            $query->whereHas('ultimaMovimentacao', function ($q) {
                $q->where('tipo', $this->ultimaMovimentacao);
            });
        }
        $query->distinct();

        return $query;
    }

    public function columns(): array
    {
        return [
            Column::make('ID', 'id')
                ->sortable(),

            Column::make('Nome', 'nome')
                ->sortable()
                ->searchable(),

            Column::make('Preço', 'preco')
                ->format(fn($value) => FormatHelper::brl($value)),

            Column::make('Estoque', 'estoque.nome'),

            Column::make('Status', 'ultimaMovimentacao.tipo')
                ->label(fn($row) => view('components.table.status-badge', [
                    'status' => optional($row->ultimaMovimentacao)->tipo,
                ])),

            Column::make('Vendido por', 'ultimaMovimentacao.user_id')
                ->format(function ($value, $row) {
                    return $row->ultimaMovimentacao->tipo === 'saida'
                        ? optional($row->ultimaMovimentacao->usuario)->name
                        : null;
                }),

            Column::make('Criado em', 'created_at')
                ->sortable()
                ->format(fn($value) => $value->format('d/m/Y')),

            Column::make('Ações', 'id')
                ->format(function ($value, $row) {
                    if ($row->ultimaMovimentacao->tipo !== 'saida' && $row->ultimaMovimentacao->tipo !== 'cancelamento' ) {
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
