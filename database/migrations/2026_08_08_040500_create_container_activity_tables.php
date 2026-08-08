<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('container_activities', function (Blueprint $table) {
            $table->id();
            $table->string('doc_no', 191)->nullable();
            $table->string('activity_date', 191)->nullable();
            $table->longText('agent')->nullable();
            $table->string('activity', 191)->nullable();
            $table->string('free_days', 191)->nullable();
            $table->string('bl_number', 191)->nullable();
            $table->string('booking_number', 191)->nullable();
            $table->longText('final_destination_code')->nullable();
            $table->longText('pod_code')->nullable();
            $table->string('destination_agent', 191)->nullable();
            $table->boolean('thru_bl')->default(false);
            $table->string('sailing_date', 191)->nullable();
            $table->longText('vessel_yoyage')->nullable();
            $table->longText('location')->nullable();
            $table->string('carrier', 191)->nullable();
            $table->longText('ts_1_port')->nullable();
            $table->longText('ts_1_agent')->nullable();
            $table->longText('ts_2_port')->nullable();
            $table->longText('ts_2_agent')->nullable();
            $table->longText('ts_3_port')->nullable();
            $table->longText('ts_3_agent')->nullable();
            $table->tinyText('remarks')->nullable();
            $table->timestamps();

            $table->index('doc_no');
            $table->index('activity_date');
            $table->index('bl_number');
            $table->index('booking_number');
        });

        Schema::create('container_activity_details', function (Blueprint $table) {
            $table->id();
            $table->string('containe_no', 191)->nullable();
            $table->string('size_type', 191)->nullable();
            $table->string('principle', 191)->nullable();
            $table->string('bl_number', 191)->nullable();
            $table->string('booking_number', 191)->nullable();
            $table->string('status', 191)->nullable();
            $table->string('cargo_type', 191)->nullable();
            $table->boolean('one_door_open')->default(false);
            $table->string('last_activity', 191)->nullable();
            $table->string('system_remarks', 191)->nullable();
            $table->string('vessel_ts1', 191)->nullable();
            $table->string('voyage_ts1', 191)->nullable();
            $table->string('sailing_date_ts1', 191)->nullable();
            $table->string('vessel_ts2', 191)->nullable();
            $table->string('voyage_ts2', 191)->nullable();
            $table->string('sailing_date_ts2', 191)->nullable();
            $table->string('vessel_ts3', 191)->nullable();
            $table->string('voyage_ts3', 191)->nullable();
            $table->string('sailing_date_ts3', 191)->nullable();
            $table->foreignId('container_activity_id')->nullable()->constrained('container_activities')->cascadeOnDelete();
            $table->timestamps();

            $table->index('containe_no');
            $table->index('bl_number');
            $table->index('booking_number');
            $table->index('status');
        });

        Schema::create('maintenance_repair_entries', function (Blueprint $table) {
            $table->id();
            $table->string('trans_id', 191)->nullable();
            $table->longText('agent')->nullable();
            $table->string('container_no', 191)->nullable();
            $table->string('ca_doc_no', 191)->nullable();
            $table->longText('activity')->nullable();
            $table->longText('vessel_voyage')->nullable();
            $table->string('owner', 191)->nullable();
            $table->string('load_port', 191)->nullable();
            $table->string('location', 191)->nullable();
            $table->longText('liable_party')->nullable();
            $table->longText('vendor')->nullable();
            $table->string('status', 191)->nullable();
            $table->dateTime('trans_date')->nullable();
            $table->longText('depot')->nullable();
            $table->boolean('depot_open')->nullable();
            $table->longText('size')->nullable();
            $table->longText('type')->nullable();
            $table->dateTime('activity_date')->nullable();
            $table->string('loc_status', 191)->nullable();
            $table->dateTime('vessel_date')->nullable();
            $table->longText('kind')->nullable();
            $table->string('estimate_ref', 191)->nullable();
            $table->string('work_order_ref', 191)->nullable();
            $table->longText('currency')->nullable();
            $table->double('ex_rate')->nullable();
            $table->tinyInteger('approved_status')->nullable();
            $table->double('total_cost_fc', 8, 2)->nullable();
            $table->double('total_cost_lc', 8, 2)->nullable();
            $table->string('remarks', 191)->nullable();
            $table->string('approved_by', 191)->nullable();
            $table->string('approved_on', 191)->nullable();
            $table->boolean('approved')->default(false);
            $table->timestamps();

            $table->index('trans_id');
            $table->index('container_no');
            $table->index('status');
            $table->index('estimate_ref');
            $table->index('work_order_ref');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('maintenance_repair_entries');
        Schema::dropIfExists('container_activity_details');
        Schema::dropIfExists('container_activities');
    }
};
