<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * The stamps selected on a bill of lading, one pivot row per selection.
     *
     * A BL can carry several stamps (vessel, signature, freight), so this is a
     * many-to-many rather than a column on booking_bl_details. Selections are
     * written with sync(), which detaches first, so the pair is unique.
     */
    public function up(): void
    {
        Schema::create('booking_bl_detail_stamp', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('booking_bl_detail_id')->constrained('booking_bl_details')->cascadeOnDelete();
            $table->foreignId('stamp_id')->constrained('stamps')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['booking_bl_detail_id', 'stamp_id']);
            $table->index('stamp_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('booking_bl_detail_stamp');
    }
};
