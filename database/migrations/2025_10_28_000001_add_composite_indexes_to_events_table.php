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
        Schema::table('events', function (Blueprint $table) {
            $table->index(['city_id', 'category_id', 'status', 'start_date'], 'idx_events_city_cat_status_date');
            $table->index(['status', 'is_popular', 'start_date'], 'idx_events_popular');
            $table->index(['status', 'is_new', 'start_date'], 'idx_events_new');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->dropIndex('idx_events_city_cat_status_date');
            $table->dropIndex('idx_events_popular');
            $table->dropIndex('idx_events_new');
        });
    }
};
