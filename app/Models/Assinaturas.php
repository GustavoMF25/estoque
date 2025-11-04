<?php

namespace App\Models;

use App\Models\Fatura;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Assinaturas extends Model
{
    use HasFactory;

    protected $table = 'assinaturas';

    protected $fillable = [
        'empresa_id',
        'plano',
        'data_inicio',
        'data_vencimento',
        'status',
        'valor_mensal',
        'metodo_pagamento',
        'ultima_confirmacao',
    ];

    protected $casts = [
        'data_inicio' => 'date',
        'data_vencimento' => 'date',
        'ultima_confirmacao' => 'datetime',
        'valor_mensal' => 'decimal:2',
    ];

    /**
     * 🔗 Relacionamento com empresa (multi-empresa)
     */
    public function empresa()
    {
        return $this->belongsTo(Empresa::class, 'empresa_id');
    }

    /**
     * 🔄 Verifica se a assinatura está ativa
     */
    public function isAtiva(): bool
    {
        return $this->status === 'ativo' && $this->data_vencimento->isFuture();
    }

    /**
     * ⏰ Verifica se está próxima do vencimento (ex: 5 dias)
     */
    public function isProximaDoVencimento(int $dias = 5): bool
    {
        return $this->data_vencimento->isBetween(
            now(),
            now()->addDays($dias)
        );
    }

    /**
     * ❌ Verifica se está vencida
     */
    public function isVencida(): bool
    {
        return $this->data_vencimento->isPast() && $this->status !== 'cancelado';
    }

    /**
     * ⚙️ Atualiza o status automaticamente com base nas datas
     */
    public function atualizarStatus(): void
    {
        if ($this->isVencida()) {
            $this->status = 'atrasado';
        } elseif ($this->isAtiva()) {
            $this->status = 'ativo';
        } else {
            $this->status = 'pendente';
        }

        $this->save();
    }

    public function faturas()
    {
        return $this->hasMany(Fatura::class, 'assinatura_id', 'id');
    }

    /**
     * 🔁 Renova a assinatura por mais 30 dias
     */
    public function renovar(int $dias = 30): void
    {
        $this->data_vencimento = Carbon::parse($this->data_vencimento)->addDays($dias);
        $this->status = 'ativo';
        $this->ultima_confirmacao = now();
        $this->save();
    }
}
