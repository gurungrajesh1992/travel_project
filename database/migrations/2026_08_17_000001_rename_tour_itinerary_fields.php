<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tour_itineraries', function (Blueprint $table) {
            $table->renameColumn('title', 'area');
            $table->renameColumn('accommodation', 'transportation');
            $table->renameColumn('walking_hours', 'time');
            $table->renameColumn('description', 'detail_itinerary');
        });
    }

    public function down(): void
    {
        Schema::table('tour_itineraries', function (Blueprint $table) {
            $table->renameColumn('area', 'title');
            $table->renameColumn('transportation', 'accommodation');
            $table->renameColumn('time', 'walking_hours');
            $table->renameColumn('detail_itinerary', 'description');
        });
    }
};
