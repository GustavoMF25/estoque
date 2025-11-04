<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Fatura extends Model
{
    use HasFactory;

    protected $fillable = [
        'assinatura_id',
        'empresa_id',
        'codigo',
        'valor',
        'data_vencimento',
        'data_pagamento',
        'status',
        'metodo_pagamento',
        'referencia_externa',
        'observacoes',
        'link_pagamento',
    ];

    public function assinatura()
    {
        return $this->belongsTo(Assinaturas::class, 'assinatura_id', 'id');
    }

    public function empresa()
    {
        return $this->belongsTo(Empresa::class);
    }
}
