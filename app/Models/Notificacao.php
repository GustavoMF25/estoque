<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notificacao extends Model
{
    protected $table = 'notificacoes';

    protected $fillable = [
        'user_id',
        'titulo',
        'mensagem',
        'tipo',
        'dados',
        'lida_em',
    ];

    protected $casts = [
        'dados' => 'array',
        'lida_em' => 'datetime',
    ];
}
