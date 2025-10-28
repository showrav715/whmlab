<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('currencies', function (Blueprint $table) {
            $table->id();
            $table->string('name', 50); // Currency name (e.g., US Dollar)
            $table->string('code', 10)->unique(); // Currency code (e.g., USD)
            $table->string('symbol', 10); // Currency symbol (e.g., $)
            $table->decimal('rate', 28, 8)->default(1); // Exchange rate relative to base currency
            $table->boolean('is_default')->default(false); // Is this the default currency
            $table->boolean('status')->default(true); // Active/Inactive
            $table->integer('sort_order')->default(0); // For ordering in dropdown
            $table->timestamps();
        });

        // Insert default currency based on existing settings
        DB::table('currencies')->insert([
            'name' => 'Default Currency',
            'code' => DB::table('general_settings')->value('cur_text') ?? 'USD',
            'symbol' => DB::table('general_settings')->value('cur_sym') ?? '$',
            'rate' => 1.00000000,
            'is_default' => true,
            'status' => true,
            'sort_order' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('currencies');
    }
};