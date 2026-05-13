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
        Schema::table('shipments', function (Blueprint $table) {
            // Package Details
            $table->integer('num_packages')->nullable()->after('weight');
            $table->decimal('length', 8, 2)->nullable()->after('num_packages');
            $table->decimal('width', 8, 2)->nullable()->after('length');
            $table->decimal('height', 8, 2)->nullable()->after('width');
            $table->enum('package_type', ['box', 'pallet', 'envelope', 'custom'])->nullable()->after('height');
            $table->boolean('fragile')->default(false)->after('package_type');
            $table->text('special_instructions')->nullable()->after('fragile');

            // Pricing & Billing
            $table->decimal('shipping_cost', 10, 2)->nullable()->after('special_instructions');
            $table->decimal('insurance_value', 10, 2)->nullable()->after('shipping_cost');
            $table->enum('payment_method', ['cash', 'card', 'bank_transfer', 'cod'])->nullable()->after('insurance_value');
            $table->enum('payment_status', ['pending', 'paid', 'refunded'])->default('pending')->after('payment_method');

            // Sender Information
            $table->string('sender_name')->nullable()->after('payment_status');
            $table->string('sender_phone')->nullable()->after('sender_name');
            $table->text('sender_address')->nullable()->after('sender_phone');

            // Receiver Information
            $table->string('receiver_name')->nullable()->after('sender_address');
            $table->string('receiver_phone')->nullable()->after('receiver_name');
            $table->text('receiver_address')->nullable()->after('receiver_phone');

            // Additional Details
            $table->enum('service_type', ['express', 'standard', 'economy'])->nullable()->after('receiver_address');
            $table->text('delivery_instructions')->nullable()->after('service_type');
            $table->string('reference_number')->nullable()->after('delivery_instructions');
            $table->text('special_notes')->nullable()->after('reference_number');

            // Customs Information
            $table->boolean('is_international')->default(false)->after('special_notes');
            $table->decimal('customs_value', 10, 2)->nullable()->after('is_international');
            $table->text('customs_description')->nullable()->after('customs_value');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('shipments', function (Blueprint $table) {
            $table->dropColumn([
                'num_packages', 'length', 'width', 'height', 'package_type', 'fragile', 'special_instructions',
                'shipping_cost', 'insurance_value', 'payment_method', 'payment_status',
                'sender_name', 'sender_phone', 'sender_address',
                'receiver_name', 'receiver_phone', 'receiver_address',
                'service_type', 'delivery_instructions', 'reference_number', 'special_notes',
                'is_international', 'customs_value', 'customs_description',
            ]);
        });
    }
};
