<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Assinaturas;
use Carbon\Carbon;

class VerificarAssinaturasVencidas extends Command
{
    protected $signature = 'assinaturas:verificar-vencidas';
    protected $description = 'Verifica e inativa assinaturas vencidas automaticamente';

    public function handle()
    {
        $hoje = Carbon::now();

        $assinaturas = Assinaturas::where('status', 'ativa')
            ->whereDate('data_vencimento', '<', $hoje)
            ->get();

        if ($assinaturas->isEmpty()) {
            $this->info('Nenhuma assinatura vencida encontrada.');
            return Command::SUCCESS;
        }

        foreach ($assinaturas as $assinatura) {
            $assinatura->update(['status' => 'inativa']);

            $this->line("🚫 Assinatura ID {$assinatura->id} ({$assinatura->empresa->nome}) foi marcada como inativa.");
        }

        $this->info('Verificação concluída com sucesso.');
        return Command::SUCCESS;
    }
}
