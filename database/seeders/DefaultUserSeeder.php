<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DefaultUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'super@syntaxweb.com'],
            [
                'name' => 'Administrador',
                'email' => 'super@syntaxweb.com',
                'email_verified_at' => now(),
                'perfil' => 'superadmin',
                'empresa_id' => 1,
                'password' => Hash::make('123qwe!!'), // 🔒
                'remember_token' => \Str::random(10),
            ]
        );
    }
}
