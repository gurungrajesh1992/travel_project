<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('phone', 30)->nullable()->after('email');
            $table->string('country', 100)->nullable()->after('phone');
            $table->string('social_provider', 20)->default('none')->after('password');
            $table->string('social_id')->nullable()->after('social_provider');
            $table->string('avatar')->nullable()->after('social_id');
            $table->string('role', 20)->default('customer')->after('avatar');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['phone', 'country', 'social_provider', 'social_id', 'avatar', 'role']);
        });
    }
};
