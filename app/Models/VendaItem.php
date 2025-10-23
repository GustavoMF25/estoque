<?php

namespace App\Models;

use App\Models\Traits\BelongsToEmpresa;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VendaItem extends Model
{
    use HasFactory, BelongsToEmpresa;

    protected $table = 'venda_itens';


    protected $fillable = [
        'venda_id',
        'produto_id',
        'valor_unitario',
        'valor_total',
    ];

    public function venda()
    {
        return $this->belongsTo(Venda::class);
    }

    public function produto()
    {
        return $this->belongsTo(Produto::class);
    }

    public function unidades()
    {
        return $this->belongsToMany(
            \App\Models\ProdutosUnidades::class, // model relacionada
            'venda_item_unidades',               // nome exato da tabela pivô
            'venda_item_id',                     // FK que representa este model
            'produto_unidade_id'                 // FK da model relacionada
        );
    }

    public function getQuantidadeAttribute()
    {
        return $this->unidades()->count();
    }
}
