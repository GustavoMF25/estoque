<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProdutosAgrupados extends Model
{
    use HasFactory;

    protected $table = 'produtos_agrupados_view'; 
    public $timestamps = false;
    protected $fillable = ['imagem', 'nome', 'quantidade_produtos', 'preco', 'estoque_nome', 'ultima_movimentacao'];
}
