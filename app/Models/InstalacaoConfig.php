<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InstalacaoConfig extends Model
{
    protected $table = 'instalacao_configs';

    protected $fillable = [
        'is_installed',
        'installation_id',
        'setup_completed_at',
        'onboarding_completed_at',
        'license_token',
        'license_status',
        'activated_at',
        'last_validation_at',
        'validation_message',
        'api_url',
    ];

    protected $casts = [
        'is_installed' => 'boolean',
        'setup_completed_at' => 'datetime',
        'onboarding_completed_at' => 'datetime',
        'activated_at' => 'datetime',
        'last_validation_at' => 'datetime',
    ];
}
