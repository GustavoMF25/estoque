<?php

namespace App\Models;

use App\Models\Traits\BelongsToEmpresa;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Produto extends Model
{
    use SoftDeletes;
    use BelongsToEmpresa;

    protected $table = 'produtos';

    protected $dates = ['deleted_at'];

    protected $fillable = [
        'nome',
        'codigo_barras',
        'imagem',
        'unidade',
        'preco',
        'valor_entrada',
        'valor_venda',
        'estoque_minimo',
        'estoque_id',
        'categoria_id',
        'fabricante_id',
        'ativo'
    ];

    /**
     * 🧮 Escopo: unidades disponíveis
     */
    public function scopeAtivo($query)
    {
        return $query->where('produtos.ativo', true);
    }

    public function estoque()
    {
        return $this->belongsTo(Estoque::class)->withTrashed();
    }

    public static function gerarCodigoBarrasUnico(): string
    {
        do {
            $codigo = 'CB' . now()->format('Hmidmy') . '-' . rand(100, 999);
        } while (self::where('codigo_barras', $codigo)->exists());

        return $codigo;
    }

    public function getValorRecebidoAttribute()
    {
        return $this->unidadesVendidas()->count() * $this->preco;
    }

    public function getDisponiveisAttribute()
    {
        return $this->unidades()->Disponiveis()->count();
    }

    public function movimentacoes()
    {
        return $this->hasMany(Movimentacao::class);
    }

    public function ultimaMovimentacao()
    {
        return $this->hasOne(Movimentacao::class)->latestOfMany();
    }

    public function categoria()
    {
        return $this->belongsTo(Categoria::class);
    }

    public function fabricante()
    {
        return $this->belongsTo(Fabricante::class, 'fabricante_id');
    }

    public function vinculos()
    {
        return $this->hasMany(ProdutoVinculos::class, 'produto_principal_id');
    }

    public function vinculadoEm()
    {
        return $this->hasMany(ProdutoVinculos::class, 'produto_vinculado_id');
    }

    public function unidades()
    {
        return $this->hasMany(ProdutosUnidades::class, 'produto_id');
    }
}
