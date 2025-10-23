<?php

namespace App\Models;

use App\Models\Traits\BelongsToEmpresa;
use Illuminate\Database\Eloquent\Model;

class Cliente extends Model
{
    use BelongsToEmpresa;
    
    protected $table = 'clientes';

    protected $fillable = [
        'nome',
        'email',
        'telefone',
        'documento',
        'ativo',
        'observacoes'
    ];

    public function enderecos()
    {
        return $this->hasMany(EnderecoCliente::class, 'cliente_id', 'id');
    }

    public function enderecoPadrao()
    {
        return $this->hasOne(EnderecoCliente::class, 'cliente_id', 'id')->where('padrao', true);
    }

    public function scopeAtivos($q)
    {
        return $q->where('ativo', true);
    }

    public function vendas()
    {
        return $this->hasMany(Venda::class, 'cliente_id');
    }
}
