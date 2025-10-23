<?php

namespace App\Support;

class Tenancy
{
    protected static ?int $empresaId = null;

    public static function setEmpresaId(?int $id): void
    {
        self::$empresaId = $id;
    }

    public static function empresaId(): ?int
    {
        return self::$empresaId;
    }
}
