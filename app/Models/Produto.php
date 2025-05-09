<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Produto extends Model
{
    protected $table = 'produtos';

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
        return $this->belongsTo(Estoque::class);
    }
}
