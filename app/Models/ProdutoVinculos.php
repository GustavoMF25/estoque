<?php
// app/Models/ProdutoVinculo.php
namespace App\Models;

use App\Models\Traits\BelongsToEmpresa;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProdutoVinculos extends Model
{
    use HasFactory, BelongsToEmpresa;

    protected $table = 'produto_vinculos';

    protected $fillable = [
        'produto_principal_id',
        'produto_vinculado_id',
        'quantidade',
        'desconto_combo',
    ];

    public function principal()
    {
        return $this->belongsTo(Produto::class, 'produto_principal_id');
    }

    public function vinculado()
    {
        return $this->belongsTo(Produto::class, 'produto_vinculado_id');
    }
}
