<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('container_activities', function (Blueprint $table) {
            // Nullable FKs for the 3 transshipment ports (description stays in ts_*_port text columns).
            $table->foreignId('ts_1_port_id')->nullable()->after('ts_1_port')
                ->constrained('pols')->nullOnDelete();
            $table->foreignId('ts_2_port_id')->nullable()->after('ts_2_port')
                ->constrained('pols')->nullOnDelete();
            $table->foreignId('ts_3_port_id')->nullable()->after('ts_3_port')
                ->constrained('pols')->nullOnDelete();

            // Nullable FKs for the 3 transshipment agents (description stays in ts_*_agent text columns).
            $table->foreignId('ts_1_agent_id')->nullable()->after('ts_1_agent')
                ->constrained('agents')->nullOnDelete();
            $table->foreignId('ts_2_agent_id')->nullable()->after('ts_2_agent')
                ->constrained('agents')->nullOnDelete();
            $table->foreignId('ts_3_agent_id')->nullable()->after('ts_3_agent')
                ->constrained('agents')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('container_activities', function (Blueprint $table) {
            $table->dropForeign(['ts_1_port_id']);
            $table->dropForeign(['ts_2_port_id']);
            $table->dropForeign(['ts_3_port_id']);
            $table->dropForeign(['ts_1_agent_id']);
            $table->dropForeign(['ts_2_agent_id']);
            $table->dropForeign(['ts_3_agent_id']);
            $table->dropColumn([
                'ts_1_port_id', 'ts_2_port_id', 'ts_3_port_id',
                'ts_1_agent_id', 'ts_2_agent_id', 'ts_3_agent_id',
            ]);
        });
    }
};
