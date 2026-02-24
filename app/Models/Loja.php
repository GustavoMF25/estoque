<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Loja extends Model
{
    use HasFactory;

    protected $fillable = [
        'empresa_id',
        'nome',
        'razao_social',
        'cnpj',
        'endereco',
        'telefone',
        'contatos',
        'email',
        'logo',
    ];

    protected $casts = [
        'contatos' => 'array',
    ];

    public function empresa()
    {
        return $this->belongsTo(Empresa::class);
    }

    public function estoques()
    {
        return $this->hasMany(Estoque::class);
    }
}
