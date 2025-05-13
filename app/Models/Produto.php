<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Produto extends Model
{
    use SoftDeletes;

    protected $table = 'produtos';

    protected $dates = ['deleted_at'];

    protected $fillable = [
        'nome',
        'codigo_barras',
        'imagem',
        'unidade',
        'preco',
        'estoque_minimo',
        'estoque_id',
        'ativo'
    ];

    public function estoque()
    {
        return $this->belongsTo(Estoque::class)->withTrashed();
    }

    public static function gerarCodigoBarrasUnico(): string
    {
        do {
            $codigo = 'CB' . now()->format('Hmidmy') . '-' . rand(100, 999);
        } while (self::where('codigo_barras', $codigo)->exists());

        return $codigo;
    }

    public function movimentacoes()
    {
        return $this->hasMany(Movimentacao::class);
    }

    public function ultimaMovimentacao()
    {
        return $this->hasOne(Movimentacao::class)->latestOfMany();
    }
}
