<?php

namespace App\Livewire\Produto;

use App\Models\ProdutosUnidades;
use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;

class ProdutoUnidadesTable extends DataTableComponent
{
    protected $model = ProdutosUnidades::class;

    public int $produtoId;

    public function mount(int $produtoId)
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
            ->setPerPageAccepted([5, 10, 25, 50])
            ->setDefaultPerPage(5);
    }

    public function builder(): Builder
    {
        return ProdutosUnidades::query()
            ->where('produto_id', $this->produtoId)
            ->orderBy('created_at', 'desc');
    }

    public function columns(): array
    {
        return [
            Column::make('Código', 'codigo_unico')
                ->format(fn ($value, $row) => $row->codigo_formatado)
                ->searchable(),

            Column::make('Status', 'status')
                ->format(fn ($value) => ucfirst($value))
                ->searchable()
                ->sortable(),

            Column::make('Criada em', 'created_at')
                ->format(fn ($value) => $value->format('d/m/Y H:i'))
                ->sortable(),
        ];
    }
}
