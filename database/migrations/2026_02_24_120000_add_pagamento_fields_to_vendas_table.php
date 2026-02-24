<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('vendas', function (Blueprint $table) {
            $table->string('forma_pagamento')->nullable()->after('valor_final');
            $table->string('status_pagamento')->nullable()->after('forma_pagamento');
        });
    }

    public function down(): void
    {
        Schema::table('vendas', function (Blueprint $table) {
            $table->dropColumn(['forma_pagamento', 'status_pagamento']);
        });
    }
};
