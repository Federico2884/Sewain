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
        Schema::table('items', function (Blueprint $table) {
            // `price` stays as the per-day (headline) price. Hourly & monthly
            // prices are optional — when null, that unit isn't offered.
            $table->decimal('price_jam', 15, 2)->nullable()->after('price');
            $table->decimal('price_bulan', 15, 2)->nullable()->after('price_jam');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('items', function (Blueprint $table) {
            $table->dropColumn(['price_jam', 'price_bulan']);
        });
    }
};
