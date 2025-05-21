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
        Schema::create('movimentacoes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('produto_id')->constrained('produtos')->onDelete('cascade');
            $table->enum('tipo', [
                'entrada',
                'saida',
                'disponivel',
                'ajuste_positivo',
                'ajuste_negativo',
                'transferencia',
                'reserva',
                'cancelamento',
                'danificado',
                'expirado',
                'retorno'
            ]);
            $table->integer('quantidade')->default(1);
            $table->text('observacao')->nullable();
            $table->foreignId('user_id')->nullable()->constrained('users');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('movimentacoes');
    }
};
