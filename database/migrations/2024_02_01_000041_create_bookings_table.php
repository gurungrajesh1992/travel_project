<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->string('booking_ref', 50)->unique();
            $table->foreignId('tour_id')->constrained('tours');
            $table->foreignId('departure_id')->nullable()->constrained('tour_departures')->nullOnDelete();
            $table->foreignId('vendor_id')->nullable()->constrained('vendors')->nullOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('coupon_id')->nullable()->constrained('coupons')->nullOnDelete();
            $table->foreignId('guide_id')->nullable()->constrained('guides')->nullOnDelete();

            $table->enum('booking_type', ['instant', 'inquiry'])->default('instant');

            $table->string('guest_name', 150)->nullable();
            $table->string('guest_email', 150)->nullable();
            $table->string('guest_phone', 30)->nullable();

            $table->unsignedInteger('num_adults')->default(1);
            $table->unsignedInteger('num_children')->default(0);
            $table->foreignId('pricing_tier_id')->nullable()->constrained('tour_pricing_tiers')->nullOnDelete();

            $table->decimal('subtotal', 10, 2)->default(0);
            $table->decimal('discount_amount', 10, 2)->default(0);
            $table->decimal('total_amount', 10, 2)->default(0);
            $table->decimal('deposit_required', 10, 2)->nullable();

            $table->enum('booking_status', ['pending', 'confirmed', 'cancelled', 'completed'])->default('pending');
            $table->enum('payment_status', ['unpaid', 'partial', 'paid', 'refunded'])->default('unpaid');
            $table->text('cancellation_reason')->nullable();
            $table->timestamp('cancelled_at')->nullable();

            $table->text('special_requests')->nullable();
            $table->enum('source', ['website', 'admin', 'api'])->default('website');

            $table->timestamps();

            $table->index('booking_status');
            $table->index('payment_status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
