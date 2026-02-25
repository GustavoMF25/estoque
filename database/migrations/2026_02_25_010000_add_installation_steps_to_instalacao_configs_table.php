<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('instalacao_configs', function (Blueprint $table) {
            $table->timestamp('setup_completed_at')->nullable()->after('installation_id');
            $table->timestamp('onboarding_completed_at')->nullable()->after('setup_completed_at');
        });
    }

    public function down(): void
    {
        Schema::table('instalacao_configs', function (Blueprint $table) {
            $table->dropColumn(['setup_completed_at', 'onboarding_completed_at']);
        });
    }
};
