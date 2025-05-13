<?php

namespace App\Livewire;

use App\Models\Estoque;
use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;

class EstoqueTable extends DataTableComponent
{
    protected $model = Estoque::class;
    public $modalAberto = false;
    public $modalTitulo = '';
    public $modalConteudo = '';

    public function abrirModal($titulo, $conteudo)
    {
        $this->modalTitulo = $titulo;
        $this->modalConteudo = $conteudo;
        $this->modalAberto = true;
    }

    public function fecharModal()
    {
        $this->modalAberto = false;
        $this->modalTitulo = '';
        $this->modalConteudo = '';
    }

    public function builder(): Builder
    {
        $query =  Estoque::query();
        if (optional(auth()->user())->isAdmin()) {
            $query->withTrashed();
        }
        $query->select([
            'estoques.id',
            'estoques.nome',
            'estoques.descricao',
            'estoques.quantidade_maxima',
            'estoques.status',
            'estoques.created_at',
        ]);
        return $query;
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

    public function columns(): array
    {
        return [
            Column::make('Nome', 'nome')->searchable()->sortable(),
            Column::make('Descricao', 'descricao')->searchable(),
            Column::make('Quantidade máxima', 'quantidade_maxima')->searchable(),
            Column::make('Status', 'status')->searchable(),
            Column::make('Criado em', 'created_at')->sortable()->format(fn($value) => $value->format('d/m/Y')),
            Column::make('Ações', 'id')
                ->format(function ($value, $row) {
                    return view('components.table.btn-table-actions', [
                        "remove" => [
                            'route' => route('estoques.destroy', $value),
                        ],
                        'show' => [
                            'title' => 'Estoque → ' . $row->nome,
                            'view' => view('estoque.show', ['estoque' => \App\Models\Estoque::withTrashed()->find($value)])->render()
                        ]
                    ]);
                }),
        ];
    }
}
