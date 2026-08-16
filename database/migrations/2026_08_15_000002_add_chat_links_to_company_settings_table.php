<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('company_settings', function (Blueprint $table) {
            $table->string('whatsapp_number')->nullable()->after('contact_number');
            $table->string('kakao_url')->nullable()->after('whatsapp_number');
        });
    }

    public function down(): void
    {
        Schema::table('company_settings', function (Blueprint $table) {
            $table->dropColumn(['whatsapp_number', 'kakao_url']);
        });
    }
};
