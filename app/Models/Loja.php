<?php

namespace App\Models;

use App\Models\Traits\BelongsToEmpresa;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Loja extends Model
{
    use HasFactory, BelongsToEmpresa;

    protected $fillable = [
        'empresa_id',
        'nome',
        'endereco',
        'telefone',
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