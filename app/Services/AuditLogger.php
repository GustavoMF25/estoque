<?php

namespace App\Services;

use App\Models\AuditLog;
use Illuminate\Support\Facades\Log;

class AuditLogger
{
    /**
     * Registra evento de auditoria no banco e no arquivo de log padrão.
     *
     * @param  array<string, mixed>  $contexto
     */
    public static function info(string $acao, array $contexto = []): void
    {
        $usuario = auth()->user();
        $ip = self::resolverIp();
        $userAgent = self::resolverUserAgent();

        $payload = [
            'action' => $acao,
            'user_id' => $usuario?->id,
            'empresa_id' => $contexto['empresa_id'] ?? $usuario->empresa_id ?? null,
            'details' => $contexto,
            'ip_address' => $ip,
            'user_agent' => $userAgent,
        ];

        try {
            AuditLog::create($payload);
        } catch (\Throwable $e) {
            Log::warning('Falha ao registrar auditoria no banco.', [
                'action' => $acao,
                'exception' => $e->getMessage(),
            ]);
        }

        Log::info('[AUDIT] ' . $acao, array_merge($payload, [
            'timestamp' => now()->toIso8601String(),
        ]));
    }

    protected static function resolverIp(): ?string
    {
        try {
            return request()->ip();
        } catch (\Throwable $e) {
            return null;
        }
    }

    protected static function resolverUserAgent(): ?string
    {
        try {
            return request()->header('User-Agent');
        } catch (\Throwable $e) {
            return null;
        }
    }
}
