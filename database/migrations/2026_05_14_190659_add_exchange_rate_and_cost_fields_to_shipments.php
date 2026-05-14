<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('shipments', function (Blueprint $table) {
            if (!Schema::hasColumn('shipments', 'cost_price')) {
                $table->decimal('cost_price', 12, 2)->nullable()->after('special_instructions');
            }
            if (!Schema::hasColumn('shipments', 'items')) {
                $table->json('items')->nullable()->after('cost_price');
            }
        });
    }

    public function down(): void
    {
        Schema::table('shipments', function (Blueprint $table) {
            $table->dropColumn(['cost_price', 'items']);
        });
    }
};
