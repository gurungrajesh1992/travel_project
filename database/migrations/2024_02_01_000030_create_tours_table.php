<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tours', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vendor_id')->nullable()->constrained('vendors')->nullOnDelete();
            $table->foreignId('primary_destination_id')->nullable()->constrained('destinations')->nullOnDelete();
            $table->foreignId('primary_category_id')->nullable()->constrained('categories')->nullOnDelete();
            $table->foreignId('difficulty_id')->nullable()->constrained('difficulty_levels')->nullOnDelete();
            $table->foreignId('guide_id')->nullable()->constrained('guides')->nullOnDelete();

            $table->string('title');
            $table->string('slug')->unique();
            $table->string('short_description', 500)->nullable();
            $table->longText('full_description')->nullable();

            $table->unsignedInteger('duration_days')->nullable();
            $table->unsignedInteger('duration_nights')->nullable();
            $table->string('max_altitude', 50)->nullable();
            $table->unsignedInteger('group_size_min')->default(1);
            $table->unsignedInteger('group_size_max')->nullable();
            $table->string('best_season', 150)->nullable();

            $table->decimal('base_price', 10, 2)->nullable();
            $table->string('currency', 10)->default('USD');
            $table->unsignedInteger('total_seats')->nullable();

            $table->string('thumbnail')->nullable();
            $table->string('map_embed_url', 500)->nullable();

            $table->string('meta_title')->nullable();
            $table->string('meta_description', 500)->nullable();

            $table->boolean('is_featured')->default(false);
            $table->enum('booking_mode', ['instant', 'inquiry', 'both'])->default('both');
            $table->enum('status', ['draft', 'published', 'archived'])->default('draft');

            $table->timestamps();
            $table->softDeletes();

            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tours');
    }
};
