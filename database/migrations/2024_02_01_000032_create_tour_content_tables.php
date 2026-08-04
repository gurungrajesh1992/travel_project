<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tour_highlights', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tour_id')->constrained('tours')->cascadeOnDelete();
            $table->string('highlight_text');
            $table->integer('sort_order')->default(0);
        });

        Schema::create('tour_itineraries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tour_id')->constrained('tours')->cascadeOnDelete();
            $table->foreignId('destination_id')->nullable()->constrained('destinations')->nullOnDelete();
            $table->unsignedInteger('day_number');
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('altitude', 50)->nullable();
            $table->string('meals', 100)->nullable();
            $table->string('accommodation', 100)->nullable();
            $table->string('walking_hours', 20)->nullable();
            $table->decimal('distance_km', 5, 1)->nullable();
        });

        Schema::create('tour_itinerary_media', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tour_itinerary_id')->constrained('tour_itineraries')->cascadeOnDelete();
            $table->string('file_path');
            $table->string('caption')->nullable();
            $table->integer('sort_order')->default(0);
        });

        Schema::create('tour_cost_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tour_id')->constrained('tours')->cascadeOnDelete();
            $table->enum('type', ['include', 'exclude']);
            $table->string('detail_text');
            $table->integer('sort_order')->default(0);
        });

        Schema::create('tour_media', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tour_id')->constrained('tours')->cascadeOnDelete();
            $table->enum('media_type', ['image', 'video'])->default('image');
            $table->string('file_path')->nullable();
            $table->string('video_url')->nullable();
            $table->string('caption')->nullable();
            $table->integer('sort_order')->default(0);
        });

        Schema::create('tour_faqs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tour_id')->constrained('tours')->cascadeOnDelete();
            $table->string('question');
            $table->text('answer');
            $table->integer('sort_order')->default(0);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tour_faqs');
        Schema::dropIfExists('tour_media');
        Schema::dropIfExists('tour_cost_details');
        Schema::dropIfExists('tour_itinerary_media');
        Schema::dropIfExists('tour_itineraries');
        Schema::dropIfExists('tour_highlights');
    }
};
