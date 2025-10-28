<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('empresa_modulos', function (Blueprint $table) {
            $table->enum('status', ['ativo', 'bloqueado', 'expirado'])->default('ativo');
            $table->timestamp('expira_em')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('empresa_modulos', function (Blueprint $table) {
            $table->dropColumn(['status', 'expira_em']);
        });
    }
};
