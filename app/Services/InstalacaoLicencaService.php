<?php

namespace App\Services;

use App\Models\InstalacaoConfig;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class InstalacaoLicencaService
{
    public function getOrCreateConfig(): InstalacaoConfig
    {
        $config = InstalacaoConfig::query()->first();
        if ($config) {
            if (empty($config->installation_id)) {
                $config->installation_id = (string) Str::uuid();
            }
            if (empty($config->api_url)) {
                $config->api_url = $this->apiUrl();
            }
            $config->save();
            return $config;
        }

        return InstalacaoConfig::create([
            'is_installed' => false,
            'installation_id' => (string) Str::uuid(),
            'license_status' => 'pending',
            'api_url' => $this->apiUrl(),
        ]);
    }

    public function ativarToken(string $token): array
    {
        $config = $this->getOrCreateConfig();
        $apiUrl = rtrim((string) ($config->api_url ?: $this->apiUrl()), '/');

        $response = Http::timeout(15)
            ->acceptJson()
            ->post($apiUrl . '/licenses/activate', [
                'token' => trim($token),
                'installation_id' => $config->installation_id,
                'domain' => parse_url((string) config('app.url'), PHP_URL_HOST),
                'system_version' => config('app.name') . '-minimal-estoque',
            ]);

        $body = $response->json();
        $ok = (bool) data_get($body, 'ok', false);
        $message = (string) data_get($body, 'message', 'Falha ao validar token.');

        if (!$ok) {
            $config->update([
                'is_installed' => false,
                'license_status' => 'invalid',
                'validation_message' => $message,
                'last_validation_at' => now(),
            ]);

            return ['ok' => false, 'message' => $message];
        }

        $config->update([
            'is_installed' => true,
            'license_token' => Crypt::encryptString(trim($token)),
            'license_status' => 'active',
            'activated_at' => $config->activated_at ?: now(),
            'last_validation_at' => now(),
            'validation_message' => $message ?: 'Licença ativada.',
            'api_url' => $apiUrl,
        ]);

        return ['ok' => true, 'message' => $message ?: 'Licença ativada com sucesso.'];
    }

    public function validarLicencaAtual(): array
    {
        $config = $this->getOrCreateConfig();
        if (!$config->is_installed || empty($config->license_token) || empty($config->installation_id)) {
            return ['ok' => false, 'message' => 'Sistema não ativado.'];
        }

        $token = Crypt::decryptString($config->license_token);
        $apiUrl = rtrim((string) ($config->api_url ?: $this->apiUrl()), '/');

        $response = Http::timeout(15)
            ->acceptJson()
            ->post($apiUrl . '/licenses/validate', [
                'token' => $token,
                'installation_id' => $config->installation_id,
            ]);

        $body = $response->json();
        $ok = (bool) data_get($body, 'ok', false);
        $message = (string) data_get($body, 'message', 'Falha na validação da licença.');

        $config->update([
            'license_status' => $ok ? 'active' : 'invalid',
            'last_validation_at' => now(),
            'validation_message' => $message,
            'is_installed' => $ok,
        ]);

        return ['ok' => $ok, 'message' => $message];
    }

    private function apiUrl(): string
    {
        return rtrim((string) config('services.gestao_sistemas.api_url'), '/');
    }
}
