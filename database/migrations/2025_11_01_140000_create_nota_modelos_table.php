<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('nota_modelos', function (Blueprint $table) {
            $table->id();
            $table->string('nome');
            $table->longText('conteudo_frente');
            $table->longText('conteudo_verso');
            $table->boolean('ativo')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('nota_modelos');
    }
};
