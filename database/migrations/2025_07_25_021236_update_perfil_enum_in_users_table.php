<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasTable('users') || !Schema::hasColumn('users', 'perfil')) {
            return;
        }

        if (DB::getDriverName() === 'mysql') {
            DB::statement(
                "ALTER TABLE users MODIFY perfil ENUM('admin','gerente','operador','vendedor') NOT NULL DEFAULT 'operador'"
            );
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (!Schema::hasTable('users') || !Schema::hasColumn('users', 'perfil')) {
            return;
        }

        if (DB::getDriverName() === 'mysql') {
            DB::statement(
                "ALTER TABLE users MODIFY perfil ENUM('admin','gerente','operador') NOT NULL DEFAULT 'operador'"
            );
        }
    }
};
