<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * BL Detail screen columns.
     *
     * Lookup columns follow `bl_info_vessel_voyage_1` and stay FK-free: the rest
     * of booking_bl_details resolves its references in the request layer, and
     * these lists are maintained outside the booking lifecycle.
     */
    public function up(): void
    {
        Schema::table('booking_bl_details', function (Blueprint $table): void {
            // Parties
            $table->unsignedBigInteger('bl_detail_shipper')->nullable();
            $table->unsignedBigInteger('bl_detail_consignee')->nullable();
            $table->string('bl_detail_notify_party_1')->nullable();
            $table->string('bl_detail_notify_party_2')->nullable();

            // Routing
            $table->unsignedBigInteger('bl_detail_pol')->nullable();
            $table->unsignedBigInteger('bl_detail_vessel_voyage')->nullable();
            $table->unsignedBigInteger('bl_detail_pofd')->nullable();
            $table->string('bl_detail_place_of_delivery')->nullable();

            // Freight and delivery terms
            $table->string('bl_detail_original_bls')->nullable();
            $table->string('bl_detail_units', 16)->nullable();
            $table->string('bl_detail_freight_type')->nullable();
            $table->unsignedBigInteger('bl_detail_delivery_agent')->nullable();

            // Cargo particulars
            $table->text('bl_detail_marks_and_numbers')->nullable();
            $table->text('bl_detail_no_of_packages')->nullable();
            $table->text('bl_detail_description_of_goods')->nullable();
            $table->text('bl_detail_gross_weight')->nullable();
            $table->text('bl_detail_measurement')->nullable();

            // Issuance
            $table->date('bl_detail_on_board_date')->nullable();
            $table->string('bl_detail_place_of_issue')->nullable();
            $table->date('bl_detail_date_of_issue')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('booking_bl_details', function (Blueprint $table): void {
            $table->dropColumn([
                'bl_detail_shipper',
                'bl_detail_consignee',
                'bl_detail_notify_party_1',
                'bl_detail_notify_party_2',
                'bl_detail_pol',
                'bl_detail_vessel_voyage',
                'bl_detail_pofd',
                'bl_detail_place_of_delivery',
                'bl_detail_original_bls',
                'bl_detail_units',
                'bl_detail_freight_type',
                'bl_detail_delivery_agent',
                'bl_detail_marks_and_numbers',
                'bl_detail_no_of_packages',
                'bl_detail_description_of_goods',
                'bl_detail_gross_weight',
                'bl_detail_measurement',
                'bl_detail_on_board_date',
                'bl_detail_place_of_issue',
                'bl_detail_date_of_issue',
            ]);
        });
    }
};
