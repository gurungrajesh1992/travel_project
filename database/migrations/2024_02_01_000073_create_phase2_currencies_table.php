<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Phase-2 stub: no business logic/UI built against this table yet.
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('currencies', function (Blueprint $table) {
            $table->id();
            $table->string('code', 10)->unique();
            $table->string('name', 100);
            $table->string('symbol', 10)->nullable();
            $table->decimal('exchange_rate_to_usd', 12, 6)->default(1);
            $table->boolean('is_default')->default(false);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('currencies');
    }
};
