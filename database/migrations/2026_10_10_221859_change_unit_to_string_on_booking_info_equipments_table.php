<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Unit of measure (KGS/MT/LBS), not a number.
     *
     * The column was an int with an integer cast, an integer validation rule and
     * an agNumberCellEditor in the Equipments grid. Text could not be entered at
     * all, and the only values that could be stored were meaningless bare
     * integers. Widening the column lets it hold the same unit codes the BL
     * Detail screen uses.
     */
    public function up(): void
    {
        Schema::table('booking_info_equipments', function (Blueprint $table): void {
            $table->string('unit', 16)->nullable()->change();
        });

        // Junk integers typed into the numeric editor (e.g. 15) are not valid
        // units, so clear them rather than carrying "15" forward as text.
        DB::table('booking_info_equipments')->whereNotNull('unit')->update(['unit' => null]);
    }

    public function down(): void
    {
        // Anything non-numeric cannot be represented by the old int column.
        DB::table('booking_info_equipments')->whereNotNull('unit')->update(['unit' => null]);

        Schema::table('booking_info_equipments', function (Blueprint $table): void {
            $table->integer('unit')->nullable()->change();
        });
    }
};
