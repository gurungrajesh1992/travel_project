<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Phase-2 stub: no business logic/UI built against this table yet.
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('flight_bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->nullable()->constrained('bookings')->nullOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('provider', 100)->nullable();
            $table->string('pnr', 50)->nullable();
            $table->string('from_airport', 10)->nullable();
            $table->string('to_airport', 10)->nullable();
            $table->timestamp('departure_at')->nullable();
            $table->enum('status', ['pending', 'confirmed', 'cancelled'])->default('pending');
            $table->json('raw_response')->nullable();
            $table->timestamp('created_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('flight_bookings');
    }
};
