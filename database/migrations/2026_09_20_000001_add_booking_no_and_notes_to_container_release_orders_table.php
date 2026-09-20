<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('container_release_orders', function (Blueprint $table) {
            $table->string('booking_no', 191)->nullable()->after('booking_id');
            $table->text('notes')->nullable()->after('pofd_id');

            $table->index('booking_no');
        });
    }

    public function down(): void
    {
        Schema::table('container_release_orders', function (Blueprint $table) {
            $table->dropIndex(['booking_no']);
            $table->dropColumn(['booking_no', 'notes']);
        });
    }
};
