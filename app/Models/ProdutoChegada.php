<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProdutoChegada extends Model
{
    use HasFactory;

    protected $table = 'produto_chegadas';

    protected $fillable = [
        'produto_id',
        'quantidade_total',
        'quantidade_comprometida',
        'previsao_chegada',
        'status',
        'observacao',
        'criado_por',
        'recebido_por',
        'recebido_em',
    ];

    protected $casts = [
        'previsao_chegada' => 'date',
        'recebido_em' => 'datetime',
    ];

    public function produto()
    {
        return $this->belongsTo(Produto::class);
    }

    public function reservas()
    {
        return $this->hasMany(ProdutoChegadaReserva::class, 'produto_chegada_id');
    }

    public function criadoPor()
    {
        return $this->belongsTo(User::class, 'criado_por');
    }

    public function recebidoPor()
    {
        return $this->belongsTo(User::class, 'recebido_por');
    }

    public function getQuantidadeDisponivelAttribute(): int
    {
        return max(0, (int) $this->quantidade_total - (int) $this->quantidade_comprometida);
    }
}
