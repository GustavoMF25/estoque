<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('vendas', function (Blueprint $table) {
            $table->string('aprovacao_status')->nullable()->after('status');
            $table->text('aprovacao_motivo')->nullable()->after('aprovacao_status');
            $table->json('aprovacao_detalhes')->nullable()->after('aprovacao_motivo');
            $table->foreignId('aprovacao_admin_id')->nullable()->after('aprovacao_detalhes')->constrained('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('vendas', function (Blueprint $table) {
            $table->dropForeign(['aprovacao_admin_id']);
            $table->dropColumn(['aprovacao_status', 'aprovacao_motivo', 'aprovacao_detalhes', 'aprovacao_admin_id']);
        });
    }
};
