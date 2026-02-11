<?php

namespace App\Actions\Fortify;

use App\Models\Team;
use App\Models\User;
use App\Models\Empresa;
use App\Models\Modulo;
use App\Models\Assinaturas;
use App\Models\Fatura;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use MercadoPago\Client\Preference\PreferenceClient;
use MercadoPago\Exceptions\MPApiException;
use MercadoPago\MercadoPagoConfig;
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
            'empresa_nome' => ['nullable', 'string', 'max:255'],
            'empresa_cnpj' => ['nullable', 'string', 'max:20'],
            'terms' => Jetstream::hasTermsAndPrivacyPolicyFeature() ? ['accepted', 'required'] : '',
        ])->validate();

        return DB::transaction(function () use ($input) {
            $empresaNome = trim((string) ($input['empresa_nome'] ?? ''));
            if ($empresaNome === '') {
                $empresaNome = trim((string) ($input['name'] ?? 'Nova Empresa')) . ' LTDA';
            }

            $cnpjInformado = preg_replace('/\D/', '', (string) ($input['empresa_cnpj'] ?? ''));
            $cnpj = $cnpjInformado !== '' ? $cnpjInformado : $this->gerarCnpjTecnico();

            $empresa = Empresa::create([
                'nome' => $empresaNome,
                'cnpj' => $cnpj,
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

            $trialDias = (int) env('ONBOARDING_TRIAL_DIAS', 7);
            $valorPlano = (float) env('ONBOARDING_VALOR_INICIAL', 149.00);
            $trialExpiraEm = now()->addDays($trialDias);

            $assinatura = Assinaturas::create([
                'empresa_id' => $empresa->id,
                'plano' => 'Plano Trial',
                'valor_mensal' => $valorPlano,
                'data_inicio' => now(),
                'data_vencimento' => $trialExpiraEm,
                'status' => 'ativo',
                'metodo_pagamento' => 'manual',
                'periodicidade' => 'mensal',
                'em_teste' => true,
                'trial_expira_em' => $trialExpiraEm,
            ]);

            $this->criarFaturaInicial($assinatura, $empresa, $input['email'], $valorPlano);

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

    protected function criarFaturaInicial(Assinaturas $assinatura, Empresa $empresa, string $email, float $valor): void
    {
        $codigo = 'FAT-' . now()->format('YmdHis') . '-' . strtoupper(Str::random(4));

        $fatura = Fatura::create([
            'assinatura_id' => $assinatura->id,
            'empresa_id' => $empresa->id,
            'codigo' => $codigo,
            'valor' => $valor,
            'data_vencimento' => Carbon::parse($assinatura->trial_expira_em ?? now()->addDays(7))->toDateString(),
            'status' => 'pendente',
            'metodo_pagamento' => 'pix',
            'observacoes' => 'Fatura inicial gerada automaticamente no cadastro.',
        ]);

        $token = env('MERCADO_PAGO_ACCESS_TOKEN');
        if (empty($token)) {
            Log::warning('Cadastro automático sem token do Mercado Pago. Fatura criada sem link.', [
                'empresa_id' => $empresa->id,
                'fatura_id' => $fatura->id,
            ]);
            return;
        }

        try {
            MercadoPagoConfig::setAccessToken($token);
            $client = new PreferenceClient();

            $preference = $client->create([
                'items' => [
                    [
                        'title' => "Assinatura {$assinatura->plano} - {$empresa->nome}",
                        'quantity' => 1,
                        'unit_price' => (float) $fatura->valor,
                        'currency_id' => 'BRL',
                    ],
                ],
                'payer' => [
                    'email' => $email,
                ],
                'external_reference' => (string) $fatura->id,
                'notification_url' => env('MERCADO_PAGO_WEBHOOK_URL'),
                'back_urls' => [
                    'success' => route('faturas.pagamento.sucesso', $fatura->id),
                    'failure' => route('faturas.pagamento.erro', $fatura->id),
                ],
                'auto_return' => 'approved',
            ]);

            $fatura->update([
                'link_pagamento' => $preference->init_point ?? null,
                'referencia_externa' => (string) ($preference->id ?? $fatura->id),
            ]);
        } catch (MPApiException $e) {
            Log::error('Falha Mercado Pago no onboarding automático.', [
                'empresa_id' => $empresa->id,
                'fatura_id' => $fatura->id,
                'status_code' => optional($e->getApiResponse())->getStatusCode(),
                'response' => optional($e->getApiResponse())->getContent(),
            ]);
        } catch (\Throwable $e) {
            Log::error('Falha ao gerar link de pagamento no onboarding automático.', [
                'empresa_id' => $empresa->id,
                'fatura_id' => $fatura->id,
                'erro' => $e->getMessage(),
            ]);
        }
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

    protected function gerarCnpjTecnico(): string
    {
        do {
            $cnpj = str_pad((string) random_int(1, 99999999999999), 14, '0', STR_PAD_LEFT);
        } while (Empresa::where('cnpj', $cnpj)->exists());

        return $cnpj;
    }
}
