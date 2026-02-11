<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('vendas', function (Blueprint $table) {
            $table->decimal('desconto', 15, 2)->default(0)->after('valor_total');
            $table->decimal('valor_final', 15, 2)->default(0)->after('desconto');
        });
    }

    public function down(): void
    {
        Schema::table('vendas', function (Blueprint $table) {
            $table->dropColumn(['desconto', 'valor_final']);
        });
    }
};
