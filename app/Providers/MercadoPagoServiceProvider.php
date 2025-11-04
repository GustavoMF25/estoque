<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use MercadoPago\MercadoPagoConfig;

class MercadoPagoServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        MercadoPagoConfig::setAccessToken(config('services.mercadopago.access_token'));
    }
}
