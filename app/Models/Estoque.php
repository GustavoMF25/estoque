<?php

namespace App\Models;

use App\Models\Traits\BelongsToEmpresa;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Estoque extends Model
{
    use HasFactory, SoftDeletes, BelongsToEmpresa;

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

    public function produtos()
{
    return $this->hasMany(Produto::class);
}
}
