<?php

namespace App\Models;

use App\Models\Traits\BelongsToEmpresa;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProdutosUnidades extends Model
{
    use HasFactory;

    protected $table = 'produtos_unidades';

    protected $fillable = [
        'produto_id',
        'codigo_unico',
        'status',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * 🔗 Relacionamento: Unidade pertence a um Produto
     */
    public function produto()
    {
        return $this->belongsTo(Produto::class, 'produto_id');
    }

    /**
     * 🔗 Relacionamento opcional: pode estar vinculada a uma movimentação
     */
    public function movimentacoes()
    {
        return $this->hasMany(Movimentacao::class, 'produto_id', 'produto_id');
    }

    /**
     * 🧮 Escopo: unidades disponíveis
     */
    public function scopeDisponiveis($query)
    {
        return $query->where('status', 'disponivel');
    }

    /**
     * 🧮 Escopo: unidades vendidas
     */
    public function scopeVendidas($query)
    {
        return $query->where('status', 'vendido');
    }

    /**
     * 🧩 Gera um código legível para exibição (ex: “Cadeira #00005”)
     */
    public function getCodigoFormatadoAttribute()
    {
        return "{$this->produto->nome} #{$this->codigo_unico}";
    }

    /**
     * ⚙️ Atualiza o status da unidade (por ex: venda, reserva, defeito)
     */
    public function alterarStatus(string $novoStatus)
    {
        $statusPermitidos = ['disponivel', 'vendido', 'reservado', 'defeito'];

        if (!in_array($novoStatus, $statusPermitidos)) {
            throw new \InvalidArgumentException("Status '{$novoStatus}' inválido para unidade.");
        }

        $this->status = $novoStatus;
        $this->save();

        return $this;
    }

    // /**
    //  * 🧾 Helper: registra movimentação associada à unidade
    //  */
    // public function registrarMovimentacao(string $tipo, string $observacao = null)
    // {
    //     return Movimentacao::create([
    //         'produto_id' => $this->produto_id,
    //         'tipo' => $tipo,
    //         'quantidade' => 1,
    //         'observacao' => $observacao ?? "Movimentação da unidade {$this->codigo_unico}",
    //     ]);
    // }
}
