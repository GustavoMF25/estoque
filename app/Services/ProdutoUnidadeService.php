<?php 

namespace App\Services;

use Illuminate\Support\Str;

class ProdutoUnidadeService
{
    public static function gerarCodigo($produto, $indice = 1)
    {
        $prefixo = strtoupper(Str::slug(substr($produto->nome, 0, 5)));
        return "{$prefixo}-" . str_pad($indice, 5, '0', STR_PAD_LEFT);
    }
}
