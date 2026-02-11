<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Atualizando enum do campo 'perfil' para incluir 'vendedor'
            $table->enum('perfil', ['admin', 'gerente', 'operador', 'vendedor'])->default('operador')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Revertendo para os valores anteriores
            $table->enum('perfil', ['admin', 'gerente', 'operador'])->default('operador')->change();
        });
    }
};