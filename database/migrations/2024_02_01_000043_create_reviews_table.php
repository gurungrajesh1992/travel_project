<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tour_id')->constrained('tours');
            $table->foreignId('booking_id')->nullable()->constrained('bookings')->nullOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('reviewer_name', 150);
            $table->string('reviewer_country', 100)->nullable();
            $table->unsignedTinyInteger('rating');
            $table->text('review_text')->nullable();
            $table->boolean('is_approved')->default(false);
            $table->timestamp('created_at')->nullable();

            $table->index('is_approved');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reviews');
    }
};
