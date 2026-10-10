<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * BL Info > Container Info, one row per Booking Equipment line.
     *
     * This is NOT the legacy `bl_container_infos` table, which holds one row per
     * booking with three longText blobs reserved for screens that do not exist
     * yet. A container list is repeating data, so it gets its own table at the
     * same grain as booking_info_equipments.
     *
     * equipment_id is the sync key: exactly one Container Info row per equipment
     * line. It is nullable so a user can add a container row with no matching
     * equipment, and unique so a line can never fan out into two rows.
     */
    public function up(): void
    {
        Schema::create('booking_bl_container_infos', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('booking_id')->constrained('bookings')->cascadeOnDelete();
            $table->foreignId('equipment_id')->nullable()->constrained('booking_info_equipments')->cascadeOnDelete();

            // Identification
            $table->unsignedTinyInteger('container_info_cntr_owner')->nullable();
            $table->string('container_info_size', 191)->nullable();
            $table->string('container_info_container_no', 191)->nullable();
            $table->string('container_info_seal_no', 191)->nullable();
            $table->string('container_info_seal2_no', 191)->nullable();
            $table->string('container_info_load_type', 191)->nullable();

            // Weights and measures
            $table->string('container_info_gross_weight', 191)->nullable();
            $table->string('container_info_net_weight', 191)->nullable();
            $table->string('container_info_cbm', 191)->nullable();
            $table->string('container_info_packages', 191)->nullable();
            $table->string('container_info_unit', 16)->nullable();
            $table->string('container_info_temperature', 191)->nullable();

            // Slots. Left FK-free like the rest of booking_bl_details; these
            // lists are maintained outside the booking lifecycle.
            $table->unsignedBigInteger('container_info_slot_operator')->nullable();
            $table->unsignedBigInteger('container_info_slot_operator_ts1')->nullable();
            $table->unsignedBigInteger('container_info_slot_operator_ts2')->nullable();

            // Cargo and remarks
            $table->string('container_info_imo_class', 191)->nullable();
            $table->string('container_info_un_number', 191)->nullable();
            $table->string('container_info_remarks', 191)->nullable();

            $table->timestamps();

            $table->unique('equipment_id');
            $table->index('booking_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('booking_bl_container_infos');
    }
};
