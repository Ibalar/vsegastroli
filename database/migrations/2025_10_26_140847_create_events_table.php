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
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description');
            $table->string('slug')->unique();
            $table->foreignId('category_id')->constrained('categories')->onDelete('cascade');
            $table->foreignId('city_id')->constrained('cities')->onDelete('cascade');
            $table->foreignId('venue_id')->constrained('venues')->onDelete('cascade');
            $table->dateTime('start_date');
            $table->string('poster_url')->nullable();
            $table->json('images')->nullable();
            $table->string('organizer_code')->nullable();
            $table->string('booking_code')->nullable();
            $table->decimal('price_min', 10, 2)->nullable();
            $table->decimal('price_max', 10, 2)->nullable();
            $table->boolean('is_popular')->default(false);
            $table->boolean('is_new')->default(false);
            $table->enum('status', ['draft', 'published', 'cancelled'])->default('draft');
            $table->string('meta_title', 255)->nullable();
            $table->string('meta_description', 500)->nullable();
            $table->timestamps();

            $table->index('slug');
            $table->index('category_id');
            $table->index('city_id');
            $table->index('venue_id');
            $table->index('start_date');
            $table->index('status');
            $table->index('is_popular');
            $table->index('is_new');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};
