<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('container_activities', function (Blueprint $table) {
            // activity_date: string → date
            $table->date('activity_date')->nullable()->change();

            // sailing_date: string → date
            $table->date('sailing_date')->nullable()->change();

            // vessel_yoyage (longText) → vessel_voyage_id (FK) + voyage_number (text)
            $table->dropColumn('vessel_yoyage');
            $table->foreignId('vessel_voyage_id')->nullable()->after('sailing_date')
                ->constrained('vessel_voyages')->nullOnDelete();
            $table->string('voyage_number', 191)->nullable()->after('vessel_voyage_id');
        });
    }

    public function down(): void
    {
        Schema::table('container_activities', function (Blueprint $table) {
            $table->dropForeign(['vessel_voyage_id']);
            $table->dropColumn(['vessel_voyage_id', 'voyage_number']);
            $table->longText('vessel_yoyage')->nullable();

            $table->string('activity_date', 191)->nullable()->change();
            $table->string('sailing_date', 191)->nullable()->change();
        });
    }
};
