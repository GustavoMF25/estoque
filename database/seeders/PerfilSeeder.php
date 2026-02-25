<?php

namespace Database\Seeders;

use App\Models\Perfil;
use Illuminate\Database\Seeder;

class PerfilSeeder extends Seeder
{
    public function run(): void
    {
        $perfis = [
            ['nome' => 'Administrador', 'slug' => 'admin', 'descricao' => 'Acesso total ao Lite de estoque', 'ativo' => true],
            ['nome' => 'Operador de Estoque', 'slug' => 'operador', 'descricao' => 'Operação diária de estoque', 'ativo' => true],
        ];

        foreach ($perfis as $perfil) {
            Perfil::updateOrCreate(
                ['slug' => $perfil['slug']],
                $perfil
            );
        }
    }
}
