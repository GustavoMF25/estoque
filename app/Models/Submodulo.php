<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Submodulo extends Model
{
    protected $fillable = ['modulo_id', 'nome', 'rota', 'icone', 'ativo'];

    public function modulo()
    {
        return $this->belongsTo(Modulo::class);
    }
}
