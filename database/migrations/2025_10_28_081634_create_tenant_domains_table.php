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
        Schema::create('tenant_domains', function (Blueprint $table) {
            $table->id();
            $table->string('domain')->unique();
            $table->string('tenant_id');
            $table->string('type')->default('subdomain'); // subdomain or custom
            $table->boolean('is_primary')->default(false);
            $table->timestamps();

            $table->index(['domain']);
            $table->index(['tenant_id']);
            $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tenant_domains');
    }
};
