<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('coupons', function (Blueprint $table) {
            $table->id();
            $table->string('code', 50)->unique();
            $table->enum('type', ['percentage', 'fixed']);
            $table->decimal('value', 10, 2);
            $table->decimal('min_booking_amount', 10, 2)->nullable();
            $table->decimal('max_discount_amount', 10, 2)->nullable();
            $table->unsignedInteger('usage_limit')->nullable();
            $table->unsignedInteger('used_count')->default(0);
            $table->date('valid_from')->nullable();
            $table->date('valid_until')->nullable();
            $table->boolean('status')->default(true);
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('coupon_tours', function (Blueprint $table) {
            $table->foreignId('coupon_id')->constrained('coupons')->cascadeOnDelete();
            $table->foreignId('tour_id')->constrained('tours')->cascadeOnDelete();
            $table->primary(['coupon_id', 'tour_id']);
        });

        Schema::create('coupon_categories', function (Blueprint $table) {
            $table->foreignId('coupon_id')->constrained('coupons')->cascadeOnDelete();
            $table->foreignId('category_id')->constrained('categories')->cascadeOnDelete();
            $table->primary(['coupon_id', 'category_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('coupon_categories');
        Schema::dropIfExists('coupon_tours');
        Schema::dropIfExists('coupons');
    }
};
