<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('venda_itens', function (Blueprint $table) {
            $table->unsignedInteger('quantidade')->default(0)->after('produto_id');
        });

        DB::statement('
            UPDATE venda_itens vi
            SET quantidade = (
                SELECT COUNT(*)
                FROM venda_item_unidades viu
                WHERE viu.venda_item_id = vi.id
            )
        ');
    }

    public function down(): void
    {
        Schema::table('venda_itens', function (Blueprint $table) {
            $table->dropColumn('quantidade');
        });
    }
};
