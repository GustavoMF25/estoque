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
        Schema::create('funcionalidades', function (Blueprint $table) {
            $table->id();
            $table->string('nome');
            $table->string('slug')->unique();
            $table->string('descricao')->nullable();
            $table->string('modulo')->nullable();
            $table->string('rota')->nullable()->unique();
            $table->string('icone')->nullable();
            $table->unsignedInteger('ordem')->default(0);
            $table->boolean('visivel_menu')->default(true);
            $table->boolean('ativo')->default(true);
            $table->timestamps();
        });

        Schema::create('perfil_funcionalidade', function (Blueprint $table) {
            $table->id();
            $table->string('perfil');
            $table->foreignId('funcionalidade_id')->constrained('funcionalidades')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['perfil', 'funcionalidade_id']);
            $table->index(['perfil']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('perfil_funcionalidade');
        Schema::dropIfExists('funcionalidades');
    }
};
