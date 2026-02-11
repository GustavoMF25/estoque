<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Categoria extends Model
{
    protected $fillable = ['nome', 'descricao', 'limite_venda_padrao', 'ativo'];

    public function produtos()
    {
        return $this->hasMany(Produto::class);
    }
}
