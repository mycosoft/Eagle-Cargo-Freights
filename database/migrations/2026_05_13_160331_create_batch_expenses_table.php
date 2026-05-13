<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('batch_expenses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('batch_id')->constrained('shipment_batches')->cascadeOnDelete();
            $table->string('type');
            $table->decimal('amount', 12, 2)->default(0);
            $table->string('currency', 3)->default('UGX');
            $table->text('description')->nullable();
            $table->foreignId('created_by')->constrained('users');
            $table->timestamp('created_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('batch_expenses');
    }
};
