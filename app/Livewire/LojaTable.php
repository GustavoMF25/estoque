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
            Column::make('Razão Social', 'razao_social')->searchable()->sortable(),
            Column::make('CNPJ', 'cnpj')->searchable()->sortable(),
            Column::make('Endereço', 'endereco')->searchable(),
            Column::make('Telefone', 'telefone')->searchable(),
            Column::make('E-mail', 'email')->searchable(),
            Column::make('Contatos', 'contatos')
                ->format(function ($value, $row) {
                    $contatos = collect($row->contatos ?? [])
                        ->map(function ($item) {
                            $name = trim((string) ($item['name'] ?? ''));
                            $numero = trim((string) ($item['numero'] ?? ''));
                            if ($name !== '' && $numero !== '') {
                                return $name . ': ' . $numero;
                            }
                            return $name !== '' ? $name : $numero;
                        })
                        ->filter()
                        ->values();

                    return $contatos->isNotEmpty()
                        ? e($contatos->implode(' | '))
                        : '-';
                }),
            Column::make('Criado em', 'created_at')->sortable()->format(fn($value) => $value->format('d/m/Y')),
            Column::make('Ações', 'id')->format(function ($value, $row) {
                return view('components.table.btn-table-actions', [
                    'edit' => [
                        'title' => 'Editar loja',
                        'route' => route('lojas.edit', $row->id),
                        'permitir' => true,
                    ],
                    'remove' => [
                        'route' => route('lojas.destroy', $row->id),
                    ],
                    'show' => '',
                    'restore' => '',
                ]);
            }),
        ];
    }

    public function builder(): Builder
    {
        $empresaId = auth()->user()->empresa_id ?? 1;

        return Loja::query()
            ->where('empresa_id', $empresaId);
    }
}
