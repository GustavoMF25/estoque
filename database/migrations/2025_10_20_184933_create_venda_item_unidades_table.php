<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('venda_item_unidades', function (Blueprint $table) {
            $table->id();
            $table->foreignId('venda_item_id')->constrained('venda_itens')->cascadeOnDelete();
            $table->foreignId('produto_unidade_id')->constrained('produtos_unidades')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['venda_item_id', 'produto_unidade_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('venda_item_unidades');
    }
};
