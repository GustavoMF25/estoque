<?php

namespace App\Models;

use App\Models\Traits\BelongsToEmpresa;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EnderecoCliente extends Model
{
    use HasFactory, BelongsToEmpresa;
    

    protected $table = 'enderecos_clientes';

    protected $fillable = [
        'cliente_id',
        'rotulo',
        'cep',
        'rua',
        'numero',
        'complemento',
        'bairro',
        'cidade',
        'estado',
        'padrao'
    ];

    public function cliente()
    {
        return $this->belongsTo(Clientes::class);
    }
}
