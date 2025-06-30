<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('vendas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('empresa_id')->constrained('empresas')->cascadeOnDelete();
            $table->foreignId('loja_id')
                ->nullable()
                ->constrained('lojas')
                ->nullOnDelete();

            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('protocolo')->index();
            $table->decimal('valor_total', 15, 2)->default(0);
            $table->enum('status', ['aberta', 'paga', 'cancelada', 'finalizada'])->default('aberta');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vendas');
    }
};
