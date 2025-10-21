<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('produtos_unidades', function (Blueprint $table) {
            $table->id();
            $table->foreignId('produto_id')->constrained('produtos')->cascadeOnDelete();
            $table->string('codigo_unico', 100);
            $table->unique(['produto_id', 'codigo_unico'], 'produto_codigo_unique');
            $table->enum('status', ['disponivel', 'vendido', 'reservado', 'defeito'])->default('disponivel');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('produtos_unidades');
    }
};
