<?php

namespace App\Models;

use App\Models\Traits\BelongsToEmpresa;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Fabricante extends Model
{
    use HasFactory, BelongsToEmpresa;

    protected $fillable = ['nome', 'site'];

    public function produtos()
    {
        return $this->hasMany(Produto::class);
    }
}
