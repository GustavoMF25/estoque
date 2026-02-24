<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('produto_chegada_reservas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('produto_chegada_id')->constrained('produto_chegadas')->cascadeOnDelete();
            $table->foreignId('venda_item_id')->constrained('venda_itens')->cascadeOnDelete();
            $table->unsignedInteger('quantidade');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('produto_chegada_reservas');
    }
};
