<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('assinaturas', function (Blueprint $table) {
            $table->boolean('em_teste')->default(false)->after('periodicidade');
            $table->timestamp('trial_expira_em')->nullable()->after('em_teste');
        });
    }

    public function down(): void
    {
        Schema::table('assinaturas', function (Blueprint $table) {
            $table->dropColumn(['em_teste', 'trial_expira_em']);
        });
    }
};
