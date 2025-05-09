<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Estoque extends Model
{
    use HasFactory;

    protected $fillable = [
        'loja_id',
        'nome',
        'localizacao',
        'descricao',
        'quantidade_maxima',
        'status',        
    ];

    public function loja()
    {
        return $this->belongsTo(Loja::class);
    }
}
