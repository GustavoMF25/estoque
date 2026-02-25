<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProdutoChegadaReserva extends Model
{
    use HasFactory;

    protected $table = 'produto_chegada_reservas';

    protected $fillable = [
        'produto_chegada_id',
        'venda_item_id',
        'quantidade',
    ];

    public function chegada()
    {
        return $this->belongsTo(ProdutoChegada::class, 'produto_chegada_id');
    }

}
