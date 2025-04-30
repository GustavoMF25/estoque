<?php
namespace App\Livewire;

use App\Models\User;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;

class UserTable extends DataTableComponent
{
    protected $model = User::class;

    public function configure(): void
    {
        $this->setPrimaryKey('id')
            ->setFilterLayout('slide-down')
            ->setPaginationEnabled(true)
            ->setPerPageAccepted([5, 10, 25, 50])
            ->setDefaultPerPage(10);
    }


    public function columns(): array
    {
        return [
            Column::make('Nome', 'name')->searchable()->sortable(),
            Column::make('Email', 'email')->searchable()->sortable(),
        ];
    }
}
