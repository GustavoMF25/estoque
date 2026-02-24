<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('lojas', function (Blueprint $table) {
            $table->string('razao_social')->nullable()->after('nome');
            $table->string('cnpj', 20)->nullable()->after('razao_social');
            $table->string('email')->nullable()->after('telefone');
            $table->string('logo')->nullable()->after('email');

            $table->unique('cnpj');
        });
    }

    public function down(): void
    {
        Schema::table('lojas', function (Blueprint $table) {
            $table->dropUnique(['cnpj']);
            $table->dropColumn(['razao_social', 'cnpj', 'email', 'logo']);
        });
    }
};
