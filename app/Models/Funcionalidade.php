<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Funcionalidade extends Model
{
    use HasFactory;

    protected $table = 'funcionalidades';

    protected $fillable = [
        'nome',
        'slug',
        'descricao',
        'modulo',
        'rota',
        'icone',
        'ordem',
        'visivel_menu',
        'ativo',
    ];

    protected $casts = [
        'visivel_menu' => 'boolean',
        'ativo' => 'boolean',
    ];

    public function perfis()
    {
        return $this->belongsToMany(
            User::class,
            'perfil_funcionalidade',
            'funcionalidade_id',
            'perfil',
            'id',
            'perfil'
        );
    }
}
