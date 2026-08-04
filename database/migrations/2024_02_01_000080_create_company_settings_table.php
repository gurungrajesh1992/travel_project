<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('company_settings', function (Blueprint $table) {
            $table->id();
            $table->string('logo')->nullable();
            $table->string('name')->default('Travel & Tour Management System');
            $table->string('short_detail', 500)->nullable();
            $table->string('address', 255)->nullable();
            $table->string('email', 150)->nullable();
            $table->string('contact_number', 30)->nullable();
            $table->string('banner_image')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('company_settings');
    }
};
