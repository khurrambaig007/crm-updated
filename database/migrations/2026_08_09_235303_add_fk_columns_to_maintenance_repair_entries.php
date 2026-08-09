<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('maintenance_repair_entries', function (Blueprint $table) {
            $table->foreignId('agent_id')->nullable()->after('agent')->constrained('agents')->nullOnDelete();
            $table->foreignId('agent_id_2')->nullable()->after('agent_id')->constrained('agents')->nullOnDelete();
            $table->foreignId('vessel_voyage_id')->nullable()->after('vessel_voyage')->constrained('vessel_voyages')->nullOnDelete();
            $table->foreignId('liable_party_id')->nullable()->after('liable_party')->constrained('p_a_s')->nullOnDelete();
            $table->foreignId('vendor_id')->nullable()->after('vendor')->constrained('suppliers')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('maintenance_repair_entries', function (Blueprint $table) {
            $table->dropForeign(['agent_id']);
            $table->dropColumn('agent_id');
            $table->dropForeign(['agent_id_2']);
            $table->dropColumn('agent_id_2');
            $table->dropForeign(['vessel_voyage_id']);
            $table->dropColumn('vessel_voyage_id');
            $table->dropForeign(['liable_party_id']);
            $table->dropColumn('liable_party_id');
            $table->dropForeign(['vendor_id']);
            $table->dropColumn('vendor_id');
        });
    }
};
