<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NotaModelo extends Model
{
    protected $table = 'nota_modelos';

    protected $fillable = [
        'nome',
        'conteudo_frente',
        'conteudo_verso',
        'icone',
        'ativo',
    ];
}
