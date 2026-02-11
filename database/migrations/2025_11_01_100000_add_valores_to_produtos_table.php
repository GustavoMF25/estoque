<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('produtos', function (Blueprint $table) {
            $table->decimal('valor_entrada', 10, 2)->default(0)->after('preco');
            $table->decimal('valor_venda', 10, 2)->default(0)->after('valor_entrada');
        });

        DB::table('produtos')->update([
            'valor_venda' => DB::raw('preco'),
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('produtos', function (Blueprint $table) {
            $table->dropColumn(['valor_entrada', 'valor_venda']);
        });
    }
};
