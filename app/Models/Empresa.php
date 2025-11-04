<?php

namespace App\Models;

use App\Models\Fatura;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Empresa extends Model
{
    use HasFactory;

    protected $fillable = [
        'nome',
        'razao_social',
        'cnpj',
        'telefone',
        'email',
        'logo',
        'endereco',
        'configuracoes'
    ];

    protected $casts = [
        'configuracoes' => 'array',
    ];

    public function modulos()
    {
        return $this->belongsToMany(Modulo::class, 'empresa_modulos')
            ->withPivot(['ativo', 'status', 'expira_em']) // ✅ importante
            ->withTimestamps();
    }

    public function assinatura()
    {
        return $this->hasOne(Assinaturas::class);
    }

    public function faturas()
    {
        return $this->hasMany(Fatura::class);
    }
}
