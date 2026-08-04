<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('difficulty_levels', function (Blueprint $table) {
            $table->id();
            $table->string('name', 50);
            $table->string('description', 255)->nullable();
            $table->integer('sort_order')->default(0);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('difficulty_levels');
    }
};
