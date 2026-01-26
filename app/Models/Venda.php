<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Venda extends Model
{
    use HasFactory;

    protected $table = 'vendas';

    protected $fillable = [
        'empresa_id',
        'loja_id',
        'user_id',
        'protocolo',
        'valor_total',
        'status',
        'aprovacao_status',
        'aprovacao_motivo',
        'aprovacao_detalhes',
        'aprovacao_admin_id',
        'cliente_id',
        'desconto',
        'valor_final'
    ];

    protected $casts = [
        'aprovacao_detalhes' => 'array',
    ];

    protected static function booted()
    {
        static::updated(function (self $venda) {
            if ($venda->wasChanged('status') && $venda->status === 'cancelada') {
                $venda->restaurarUnidadesVendidas();
            }
        });
    }

    public function restaurarUnidadesVendidas(): void
    {
        $this->loadMissing('itens.unidades');

        foreach ($this->itens as $item) {
            foreach ($item->unidades as $unidade) {
                if ($unidade->status !== 'disponivel') {
                    $unidade->update(['status' => 'disponivel']);
                }
            }
            $item->unidades()->detach();
        }
    }

    public function itens()
    {
        return $this->hasMany(VendaItem::class);
    }

    public function empresa()
    {
        return $this->belongsTo(Empresa::class);
    }

    public function loja()
    {
        return $this->belongsTo(Loja::class);
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function cliente()
    {
        return $this->belongsTo(\App\Models\Cliente::class, 'cliente_id');
    }
}
