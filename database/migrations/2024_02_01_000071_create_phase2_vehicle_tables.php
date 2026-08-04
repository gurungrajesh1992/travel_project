<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Phase-2 stub: no business logic/UI built against these tables yet.
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vehicle_types', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
        });

        Schema::create('vehicles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vendor_id')->nullable()->constrained('vendors')->nullOnDelete();
            $table->foreignId('vehicle_type_id')->nullable()->constrained('vehicle_types')->nullOnDelete();
            $table->string('name', 150);
            $table->unsignedInteger('capacity')->default(4);
            $table->decimal('price_per_day', 10, 2);
            $table->boolean('status')->default(true);
            $table->timestamps();
        });

        Schema::create('vehicle_bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vehicle_id')->constrained('vehicles');
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->date('pickup_date');
            $table->date('return_date');
            $table->decimal('total_amount', 10, 2)->default(0);
            $table->enum('status', ['pending', 'confirmed', 'cancelled'])->default('pending');
            $table->timestamp('created_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vehicle_bookings');
        Schema::dropIfExists('vehicles');
        Schema::dropIfExists('vehicle_types');
    }
};
