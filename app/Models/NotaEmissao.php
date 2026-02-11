<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NotaEmissao extends Model
{
    protected $table = 'nota_emissoes';

    protected $fillable = [
        'venda_id',
        'modelo_id',
        'cliente_id',
        'user_id',
        'cliente_nome',
        'cliente_documento',
        'cliente_email',
        'cliente_telefone',
        'cep',
        'rua',
        'numero',
        'complemento',
        'bairro',
        'cidade',
        'estado',
        'conteudo_frente',
        'conteudo_verso',
    ];

    public function venda()
    {
        return $this->belongsTo(Venda::class);
    }

    public function modelo()
    {
        return $this->belongsTo(NotaModelo::class, 'modelo_id');
    }

    public function cliente()
    {
        return $this->belongsTo(Cliente::class, 'cliente_id');
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
