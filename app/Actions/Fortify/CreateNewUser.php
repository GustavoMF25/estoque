<?php

namespace App\Actions\Fortify;

use App\Models\Team;
use App\Models\User;
use App\Models\Empresa;
use App\Models\Modulo;
use App\Models\Assinaturas;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Laravel\Fortify\Contracts\CreatesNewUsers;
use Laravel\Jetstream\Jetstream;

class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules;

    /**
     * Create a newly registered user.
     *
     * @param  array<string, string>  $input
     */
    public function create(array $input): User
    {
        Validator::make($input, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => $this->passwordRules(),
            'empresa_nome' => ['required', 'string', 'max:255'],
            'empresa_cnpj' => ['nullable', 'string', 'max:20'],
            'terms' => Jetstream::hasTermsAndPrivacyPolicyFeature() ? ['accepted', 'required'] : '',
        ])->validate();

        return DB::transaction(function () use ($input) {
            $empresa = Empresa::create([
                'nome' => $input['empresa_nome'],
                'cnpj' => $input['empresa_cnpj'] ?? null,
                'email' => $input['email'],
            ]);

            $modulos = Modulo::all();
            foreach ($modulos as $modulo) {
                $empresa->modulos()->attach($modulo->id, [
                    'status' => 'ativo',
                    'ativo' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            Assinaturas::create([
                'empresa_id' => $empresa->id,
                'plano' => 'Plano Trial',
                'valor_mensal' => 0,
                'data_inicio' => now(),
                'data_vencimento' => now()->addDays(7),
                'status' => 'ativo',
                'metodo_pagamento' => 'manual',
                'periodicidade' => 'mensal',
                'em_teste' => true,
                'trial_expira_em' => now()->addDays(7),
            ]);

            return tap(User::create([
                'name' => $input['name'],
                'email' => $input['email'],
                'empresa_id' => $empresa->id,
                'perfil' => 'admin',
                'password' => Hash::make($input['password']),
            ]), function (User $user) {
                $this->createTeam($user);
            });
        });
    }

    /**
     * Create a personal team for the user.
     */
    protected function createTeam(User $user): void
    {
        $user->ownedTeams()->save(Team::forceCreate([
            'user_id' => $user->id,
            'name' => explode(' ', $user->name, 2)[0]."'s Team",
            'personal_team' => true,
        ]));
    }
}
