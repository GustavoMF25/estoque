<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Fabricante extends Model
{
    use HasFactory;

    protected $fillable = ['nome', 'site'];

    public function produtos()
    {
        return $this->hasMany(Produto::class);
    }
}
