<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Modulo extends Model
{
    protected $fillable = ['nome', 'slug', 'icone', 'ativo'];

    public function submodulos()
    {
        return $this->hasMany(Submodulo::class);
    }
}
