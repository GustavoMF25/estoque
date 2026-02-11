<?php

namespace App\Services;

use App\Models\AuditLog;
use Illuminate\Support\Facades\Log;

class AuditLogger
{
    /**
     * Registro centralizado de eventos de auditoria.
     *
     * @param  string  $action
     * @param  array<string, mixed>  $context
     */
    public static function info(string $action, array $context = []): void
    {
        $user = auth()->user();
        $ip = self::resolveIp();
        $agent = self::resolveUserAgent();

        try {
            AuditLog::create([
                'user_id' => $user?->id,
                'empresa_id' => $user->empresa_id ?? null,
                'action' => $action,
                'details' => $context,
                'ip_address' => $ip,
                'user_agent' => $agent,
            ]);
        } catch (\Throwable $e) {
            Log::warning('Falha ao persistir auditoria.', [
                'action' => $action,
                'exception' => $e->getMessage(),
            ]);
        }

        $baseContext = [
            'action' => $action,
            'user_id' => $user?->id,
            'ip' => $ip,
            'user_agent' => $agent,
            'timestamp' => now()->toIso8601String(),
        ];

        Log::info('[AUDIT] ' . $action, array_merge($baseContext, $context));
    }

    protected static function resolveIp(): ?string
    {
        try {
            return request()->ip();
        } catch (\Throwable $e) {
            return null;
        }
    }

    protected static function resolveUserAgent(): ?string
    {
        try {
            return request()->header('User-Agent');
        } catch (\Throwable $e) {
            return null;
        }
    }
}
