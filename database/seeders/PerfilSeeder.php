<?php

namespace Database\Seeders;

use App\Models\Perfil;
use Illuminate\Database\Seeder;

class PerfilSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $perfis = [
            ['nome' => 'Administrador', 'slug' => 'admin', 'descricao' => 'Acesso total ao sistema', 'ativo' => true],
            ['nome' => 'Gerente', 'slug' => 'gerente', 'descricao' => 'Gestão operacional e comercial', 'ativo' => true],
            ['nome' => 'Operador', 'slug' => 'operador', 'descricao' => 'Operação de estoque e vendas', 'ativo' => true],
            ['nome' => 'Vendedor', 'slug' => 'vendedor', 'descricao' => 'Operação de vendas', 'ativo' => true],
            ['nome' => 'Escritório', 'slug' => 'escritorio', 'descricao' => 'Emissão de nota e gestão de produtos', 'ativo' => true],
        ];

        foreach ($perfis as $perfil) {
            Perfil::updateOrCreate(
                ['slug' => $perfil['slug']],
                $perfil
            );
        }
    }
}
