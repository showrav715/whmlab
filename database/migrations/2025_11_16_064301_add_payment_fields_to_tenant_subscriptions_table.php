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
        Schema::table('tenant_subscriptions', function (Blueprint $table) {
            $table->string('payment_id')->nullable()->after('plan_features');
            $table->string('receipt_id')->nullable()->after('payment_id');
            $table->enum('status', ['active', 'expired', 'cancelled', 'trial'])->default('active')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tenant_subscriptions', function (Blueprint $table) {
            $table->dropColumn(['payment_id', 'receipt_id']);
            $table->enum('status', ['active', 'expired', 'cancelled'])->default('active')->change();
        });
    }
};
