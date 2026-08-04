<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vendors', function (Blueprint $table) {
            $table->id();
            $table->string('business_name', 150);
            $table->string('owner_name', 150);
            $table->string('email', 150)->unique();
            $table->string('phone', 30)->nullable();
            $table->string('logo')->nullable();
            $table->decimal('commission_rate', 5, 2)->default(0);
            $table->enum('status', ['pending', 'active', 'suspended'])->default('pending');
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vendors');
    }
};
