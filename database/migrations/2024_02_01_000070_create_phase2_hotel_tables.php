<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Phase-2 stub: no business logic/UI built against these tables yet.
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hotels', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vendor_id')->nullable()->constrained('vendors')->nullOnDelete();
            $table->foreignId('destination_id')->nullable()->constrained('destinations')->nullOnDelete();
            $table->string('name', 200);
            $table->string('slug', 200)->unique();
            $table->unsignedTinyInteger('star_rating')->nullable();
            $table->text('description')->nullable();
            $table->string('address')->nullable();
            $table->boolean('status')->default(true);
            $table->timestamps();
        });

        Schema::create('hotel_rooms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hotel_id')->constrained('hotels')->cascadeOnDelete();
            $table->string('room_type', 150);
            $table->decimal('price_per_night', 10, 2);
            $table->unsignedInteger('capacity')->default(2);
            $table->unsignedInteger('total_rooms')->default(1);
            $table->boolean('status')->default(true);
        });

        Schema::create('hotel_bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hotel_room_id')->constrained('hotel_rooms');
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->date('check_in');
            $table->date('check_out');
            $table->unsignedInteger('guests')->default(1);
            $table->decimal('total_amount', 10, 2)->default(0);
            $table->enum('status', ['pending', 'confirmed', 'cancelled'])->default('pending');
            $table->timestamp('created_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hotel_bookings');
        Schema::dropIfExists('hotel_rooms');
        Schema::dropIfExists('hotels');
    }
};
