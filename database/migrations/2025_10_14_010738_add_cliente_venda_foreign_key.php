<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        if (Schema::hasTable('vendas') && !Schema::hasColumn('vendas', 'cliente_id')) {
            Schema::table('vendas', function (Blueprint $table) {
                $table->foreignId('cliente_id')
                    ->nullable()
                    ->constrained('clientes')
                    ->nullOnDelete();
            });
        }
    }

    public function down(): void {
        if (Schema::hasTable('vendas') && Schema::hasColumn('vendas', 'cliente_id')) {
            Schema::table('vendas', function (Blueprint $table) {
                $table->dropConstrainedForeignId('cliente_id');
            });
        }
    }
};
