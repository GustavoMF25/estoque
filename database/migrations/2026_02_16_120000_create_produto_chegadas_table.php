<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('produto_chegadas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('produto_id')->constrained('produtos')->cascadeOnDelete();
            $table->unsignedInteger('quantidade_total');
            $table->unsignedInteger('quantidade_comprometida')->default(0);
            $table->date('previsao_chegada')->nullable();
            $table->enum('status', ['aberto', 'recebido', 'cancelado'])->default('aberto');
            $table->text('observacao')->nullable();
            $table->foreignId('criado_por')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('recebido_por')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('recebido_em')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('produto_chegadas');
    }
};
