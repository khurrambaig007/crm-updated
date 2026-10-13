<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Ref NO's screen columns.
     *
     * Every BL Info tab keeps its fields on this one-per-booking table under a
     * screen prefix (bl_info_, booking_info_, release_instruction_,
     * delivery_order_, lock_info_, bl_detail_), so Ref NO's follows suit with
     * refno_ rather than getting a table of its own.
     */
    public function up(): void
    {
        Schema::table('booking_bl_details', function (Blueprint $table): void {
            $table->string('refno_invoice_no', 191)->nullable();
            $table->date('refno_invoice_date')->nullable();
            $table->string('refno_export_no', 191)->nullable();
            $table->date('refno_export_date')->nullable();
            $table->string('refno_contact_no', 191)->nullable();
            $table->date('refno_contact_date')->nullable();
            $table->string('refno_lc_no', 191)->nullable();
            $table->date('refno_lc_date')->nullable();
            $table->string('refno_shipper_ref', 191)->nullable();
            $table->string('refno_client_ref', 191)->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('booking_bl_details', function (Blueprint $table): void {
            $table->dropColumn([
                'refno_invoice_no',
                'refno_invoice_date',
                'refno_export_no',
                'refno_export_date',
                'refno_contact_no',
                'refno_contact_date',
                'refno_lc_no',
                'refno_lc_date',
                'refno_shipper_ref',
                'refno_client_ref',
            ]);
        });
    }
};
