<?php

namespace App\Models;

use App\Models\Traits\BelongsToEmpresa;
use Illuminate\Database\Eloquent\Model;

class Categoria extends Model
{
    use BelongsToEmpresa;
    
    protected $fillable = ['nome', 'descricao', 'ativo'];

    public function produtos()
    {
        return $this->hasMany(Produto::class);
    }
}
