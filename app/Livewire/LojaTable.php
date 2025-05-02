<?php

namespace App\Livewire;

use App\Models\Loja;
use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;

class LojaTable extends DataTableComponent
{
    protected $model = Loja::class;

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
            Column::make('ID', 'id')->sortable(),
            Column::make('Nome', 'nome')->searchable()->sortable(),
            Column::make('Endereço', 'endereco')->searchable(),
            Column::make('Telefone', 'telefone')->searchable(),
            Column::make('Criado em', 'created_at')->sortable()->format(fn($value) => $value->format('d/m/Y')),
        ];
    }
}
