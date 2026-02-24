<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE users MODIFY perfil VARCHAR(50) NOT NULL DEFAULT 'operador'");
            return;
        }

        Schema::table('users', function (Blueprint $table) {
            $table->string('perfil', 50)->default('operador')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE users MODIFY perfil ENUM('admin','gerente','operador','vendedor') NOT NULL DEFAULT 'operador'");
            return;
        }
    }
};
