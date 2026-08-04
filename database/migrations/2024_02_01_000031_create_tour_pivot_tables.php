<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tour_destinations', function (Blueprint $table) {
            $table->foreignId('tour_id')->constrained('tours')->cascadeOnDelete();
            $table->foreignId('destination_id')->constrained('destinations')->cascadeOnDelete();
            $table->boolean('is_primary')->default(false);
            $table->primary(['tour_id', 'destination_id']);
        });

        Schema::create('tour_categories', function (Blueprint $table) {
            $table->foreignId('tour_id')->constrained('tours')->cascadeOnDelete();
            $table->foreignId('category_id')->constrained('categories')->cascadeOnDelete();
            $table->boolean('is_primary')->default(false);
            $table->primary(['tour_id', 'category_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tour_categories');
        Schema::dropIfExists('tour_destinations');
    }
};
