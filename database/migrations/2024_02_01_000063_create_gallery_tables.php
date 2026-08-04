<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('gallery_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->string('slug', 100)->unique();
            $table->integer('sort_order')->default(0);
            $table->boolean('status')->default(true);
        });

        Schema::create('gallery_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('gallery_category_id')->nullable()->constrained('gallery_categories')->nullOnDelete();
            $table->foreignId('tour_id')->nullable()->constrained('tours')->nullOnDelete();
            $table->enum('media_type', ['image', 'video'])->default('image');
            $table->string('file_path')->nullable();
            $table->string('video_url')->nullable();
            $table->string('caption')->nullable();
            $table->boolean('is_featured')->default(false);
            $table->integer('sort_order')->default(0);
            $table->timestamp('created_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('gallery_items');
        Schema::dropIfExists('gallery_categories');
    }
};
