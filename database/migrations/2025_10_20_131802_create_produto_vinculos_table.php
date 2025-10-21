<?php
// database/migrations/2025_10_20_000000_create_produto_vinculos_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('produto_vinculos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('produto_principal_id')->constrained('produtos')->cascadeOnDelete();
            $table->foreignId('produto_vinculado_id')->constrained('produtos')->cascadeOnDelete();
            $table->integer('quantidade')->default(1);
            $table->decimal('desconto_combo', 5, 2)->default(10.00); // %
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('produto_vinculos');
    }
};
