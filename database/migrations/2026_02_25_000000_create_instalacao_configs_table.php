<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('instalacao_configs', function (Blueprint $table) {
            $table->id();
            $table->boolean('is_installed')->default(false);
            $table->string('installation_id')->nullable()->unique();
            $table->text('license_token')->nullable();
            $table->string('license_status')->default('pending');
            $table->timestamp('activated_at')->nullable();
            $table->timestamp('last_validation_at')->nullable();
            $table->text('validation_message')->nullable();
            $table->string('api_url')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('instalacao_configs');
    }
};
