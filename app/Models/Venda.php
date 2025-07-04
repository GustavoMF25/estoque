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
    ];

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
}
