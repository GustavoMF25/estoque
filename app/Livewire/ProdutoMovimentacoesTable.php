<?php

namespace App\Livewire;

use App\Models\Movimentacao;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Component;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;

class ProdutoMovimentacoesTable extends DataTableComponent
{

    protected $model = Movimentacao::class;

    protected $listeners = ['refreshTabelaMovimentacoes' => '$refresh'];

    public $produtoId;

    public function mount($produtoId)
    {
        $this->produtoId = $produtoId;
    }

    public function configure(): void
    {
        $this->setPrimaryKey('id')
            ->setTableAttributes([
                'class' => 'table table-bordered table-striped table-hover align-middle',
            ])
            ->setPaginationEnabled(true)
            ->setPerPage(10);
    }

    public function builder(): Builder
    {
        // dd(Movimentacao::with('usuario')->where('produto_id', $this->produtoId)->get());
        return Movimentacao::with('usuario')->where('produto_id', $this->produtoId);
    }


    public function columns(): array
    {
        return [
            Column::make('Tipo', 'tipo')
                ->format(fn($value) => ucfirst($value))
                ->sortable()
                ->searchable(),

            Column::make('Quantidade', 'quantidade')
                ->sortable()
                ->searchable(),
            Column::make('Usuario', 'usuario.name')
                ->sortable()
                ->searchable(),
            Column::make('Data', 'created_at')
                ->format(fn($value) => $value->format('d/m/Y H:i'))
                ->sortable(),

            Column::make('Observação', 'observacao')
                ->format(fn($value) => $value ?? '—')
                ->searchable(),
        ];
    }
}
