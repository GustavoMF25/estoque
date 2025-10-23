<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([EmpresaSeeder::class]);
        $this->call(DefaultUserSeeder::class);
        $this->call(AddEmpresaIdSeeder::class);
        // $this->call(MigrarProdutosParaUnidadesSeeder::class);
        // $this->call(ReprocessarVendasAntigasSeeder::class);
        // \App\Models\User::factory(10)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);
    }
}
