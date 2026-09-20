<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->string('reporting_no', 191)->nullable()->after('booking_no');
            $table->dropIndex('bookings_booking_no_index');
            $table->unique('booking_no');
            $table->unique('reporting_no');
        });
    }

    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropUnique('bookings_booking_no_unique');
            $table->dropUnique('bookings_reporting_no_unique');
            $table->index('booking_no');
            $table->dropColumn('reporting_no');
        });
    }
};
