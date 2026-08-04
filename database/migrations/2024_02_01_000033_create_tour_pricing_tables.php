<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tour_departures', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tour_id')->constrained('tours')->cascadeOnDelete();
            $table->date('departure_date');
            $table->date('return_date')->nullable();
            $table->unsignedInteger('available_seats')->default(0);
            $table->unsignedInteger('booked_seats')->default(0);
            $table->enum('status', ['open', 'full', 'cancelled'])->default('open');

            $table->index('departure_date');
        });

        Schema::create('tour_seasonal_pricing', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tour_id')->constrained('tours')->cascadeOnDelete();
            $table->string('season_name', 100);
            $table->date('start_date');
            $table->date('end_date');
            $table->decimal('price', 10, 2);
        });

        Schema::create('tour_pricing_tiers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tour_id')->constrained('tours')->cascadeOnDelete();
            $table->enum('tier_type', ['group', 'child', 'private', 'solo']);
            $table->unsignedInteger('min_pax')->nullable();
            $table->unsignedInteger('max_pax')->nullable();
            $table->decimal('price_per_person', 10, 2);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tour_pricing_tiers');
        Schema::dropIfExists('tour_seasonal_pricing');
        Schema::dropIfExists('tour_departures');
    }
};
