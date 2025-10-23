<?php

namespace App\Models\Traits;

use Illuminate\Database\Eloquent\Builder;
use App\Support\Tenancy;

trait BelongsToEmpresa
{
    protected static function bootBelongsToEmpresa()
    {
        // Preenche empresa_id automaticamente ao criar
        static::creating(function ($model) {
            if (empty($model->empresa_id)) {
                $empresaId = Tenancy::empresaId();
                if ($empresaId) {
                    $model->empresa_id = $empresaId;
                }
            }
        });

        // Escopo global: aplica só se houver empresa_id resolvido
        static::addGlobalScope('empresa', function (Builder $builder) {
            $empresaId = Tenancy::empresaId();
            if ($empresaId) {
                $builder->where(
                    $builder->getModel()->getTable().'.empresa_id',
                    $empresaId
                );
            }
        });
    }

    public function empresa()
    {
        return $this->belongsTo(\App\Models\Empresa::class);
    }
}
