<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->string('booking_no', 191)->nullable();
            $table->string('approval_no', 191)->nullable();
            $table->string('reference_no', 191)->nullable();
            $table->date('booking_date')->nullable();
            $table->unsignedInteger('carrier')->nullable();
            $table->tinyInteger('cntr_owner')->nullable();
            $table->date('sailing_date');
            $table->unsignedBigInteger('commodity')->nullable();
            $table->tinyInteger('non_dg')->nullable();
            $table->unsignedBigInteger('vessel_voyage')->nullable();
            $table->unsignedBigInteger('pol')->nullable();
            $table->unsignedBigInteger('pofd')->nullable();
            $table->unsignedBigInteger('pot_1')->nullable();
            $table->unsignedBigInteger('pot_2')->nullable();
            $table->unsignedBigInteger('shipper_bp')->nullable();
            $table->unsignedBigInteger('agent_pol')->nullable();
            $table->unsignedBigInteger('agent_pofd')->nullable();
            $table->unsignedBigInteger('agent_1')->nullable();
            $table->unsignedBigInteger('agent_2')->nullable();
            $table->tinyInteger('act_shipper')->nullable();
            $table->tinyInteger('freight_type')->nullable();
            $table->tinyInteger('freight_type_sub')->nullable();
            $table->tinyInteger('consignee')->nullable();
            $table->tinyInteger('srr')->nullable();
            $table->tinyInteger('services')->nullable();
            $table->tinyInteger('services_sub')->nullable();
            $table->boolean('thru_bl')->default(false);
            $table->timestamps();
            $table->tinyInteger('booking_status')->default(1);
            $table->boolean('is_split_booking')->default(false);

            $table->foreign('commodity')->references('id')->on('commodities')->restrictOnDelete();
            $table->foreign('vessel_voyage')->references('id')->on('vessel_voyages')->restrictOnDelete();
            $table->foreign('pol')->references('id')->on('pols')->restrictOnDelete();
            $table->foreign('pofd')->references('id')->on('pods')->restrictOnDelete();
            $table->foreign('pot_1')->references('id')->on('pols')->restrictOnDelete();
            $table->foreign('pot_2')->references('id')->on('pols')->restrictOnDelete();
            $table->foreign('shipper_bp')->references('id')->on('shipper_bps')->restrictOnDelete();
            $table->foreign('agent_pol')->references('id')->on('agents')->restrictOnDelete();
            $table->foreign('agent_pofd')->references('id')->on('agents')->restrictOnDelete();
            $table->foreign('agent_1')->references('id')->on('agents')->restrictOnDelete();
            $table->foreign('agent_2')->references('id')->on('agents')->restrictOnDelete();

            $table->index('booking_no');
            $table->index('reference_no');
            $table->index('booking_date');
            $table->index('booking_status');
            $table->index('commodity');
            $table->index('vessel_voyage');
            $table->index('pol');
            $table->index('pofd');
            $table->index('pot_1');
            $table->index('pot_2');
            $table->index('shipper_bp');
            $table->index('agent_pol');
            $table->index('agent_pofd');
            $table->index('agent_1');
            $table->index('agent_2');
            $table->index(['pol', 'pofd']);
        });

        Schema::create('booking_bl_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->nullable()->constrained('bookings')->cascadeOnDelete();
            $table->string('bl_info_date', 191)->nullable();
            $table->string('bl_info_agent', 191)->nullable();
            $table->string('bl_info_booking_no', 300)->nullable();
            $table->string('bl_info_sailing_date', 191)->nullable();
            $table->string('bl_info_accounting_date', 191)->nullable();
            $table->string('bl_info_carrier_mbl_no', 191)->nullable();
            $table->string('bl_info_bl_number', 191)->nullable();
            $table->unsignedBigInteger('bl_info_vessel_voyage_1')->nullable();
            $table->unsignedBigInteger('bl_info_vessel_voyage_2')->nullable();
            $table->boolean('bl_info_transshipment')->default(false);
            $table->tinyInteger('bl_info_booking_si_status')->nullable();
            $table->string('booking_info_pol', 191)->nullable();
            $table->string('booking_info_pofd', 191)->nullable();
            $table->string('booking_info_pot_1', 191)->nullable();
            $table->string('booking_info_pot_2', 191)->nullable();
            $table->string('booking_info_shipper_bp', 191)->nullable();
            $table->string('booking_info_cntr_owner', 191)->nullable();
            $table->string('booking_info_agent_pofd', 191)->nullable();
            $table->string('booking_info_agent_1', 191)->nullable();
            $table->string('booking_info_agent_2', 191)->nullable();
            $table->string('booking_info_consignee', 191)->nullable();
            $table->string('booking_info_reference', 191)->nullable();
            $table->string('release_instruction_date', 191)->nullable();
            $table->string('release_instruction_status', 191)->nullable();
            $table->string('release_instruction_type', 191)->nullable();
            $table->string('release_instruction_description', 191)->nullable();
            $table->string('delivery_order_doc', 191)->nullable();
            $table->string('delivery_order_date', 191)->nullable();
            $table->string('delivery_order_validity_date', 191)->nullable();
            $table->string('delivery_order_agent', 191)->nullable();
            $table->string('delivery_order_vessel_voyage', 191)->nullable();
            $table->text('delivery_order_deliver_to')->nullable();
            $table->text('delivery_order_remarks')->nullable();
            $table->string('lock_info_pol_locked_to', 191)->nullable();
            $table->string('lock_info_pol_locked_on', 191)->nullable();
            $table->string('lock_info_pot_1_locked_to', 191)->nullable();
            $table->string('lock_info_pot_1_locked_on', 191)->nullable();
            $table->string('lock_info_pot_2_locked_to', 191)->nullable();
            $table->string('lock_info_pot_2_locked_on', 191)->nullable();
            $table->string('lock_info_pofd_locked_to', 191)->nullable();
            $table->string('lock_info_pofd_locked_on', 191)->nullable();
            $table->string('lock_info_locked_detail', 191)->nullable();
            $table->timestamps();

            $table->index('bl_info_booking_no');
            $table->index('bl_info_bl_number');
        });

        Schema::create('booking_info_equipments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('size')->nullable()->constrained('container_sizes')->restrictOnDelete();
            $table->foreignId('type')->nullable()->constrained('container_types')->restrictOnDelete();
            $table->double('quantity', 8, 2)->default(0);
            $table->string('gross_weight', 500)->nullable();
            $table->string('packages', 500)->nullable();
            $table->integer('unit')->nullable();
            $table->string('cargo_volumn', 500)->nullable();
            $table->foreignId('booking_id')->nullable()->constrained('bookings')->cascadeOnDelete();
            $table->tinyInteger('approval_status')->nullable();
            $table->timestamps();

            $table->index(['booking_id', 'size', 'type']);
        });

        Schema::create('booking_other_infos', function (Blueprint $table) {
            $table->id();
            $table->text('special_req')->nullable();
            $table->integer('free_days_pol')->nullable();
            $table->integer('detention_free_pofd')->nullable();
            $table->boolean('detention_tariff')->nullable();
            $table->string('detention_currency', 191)->nullable();
            $table->text('message')->nullable();
            $table->foreignId('booking_id')->nullable()->constrained('bookings')->cascadeOnDelete();
            $table->timestamps();
        });

        Schema::create('bl_container_infos', function (Blueprint $table) {
            $table->id();
            $table->longText('blinfo_container_info')->nullable();
            $table->longText('blinfo_bl_detail')->nullable();
            $table->longText('blinfo_refnos')->nullable();
            $table->foreignId('booking_id')->nullable()->constrained('bookings')->cascadeOnDelete();
            $table->timestamps();
        });

        Schema::create('booking_costs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('charge_id')->nullable()->constrained('charges')->restrictOnDelete();
            $table->foreignId('container_size_id')->nullable()->constrained('container_sizes')->restrictOnDelete();
            $table->foreignId('container_type_id')->nullable()->constrained('container_types')->restrictOnDelete();
            $table->text('quantity')->nullable();
            $table->double('mrg', 8, 2)->nullable();
            $table->double('cost', 8, 2)->nullable();
            $table->double('amount', 8, 2)->nullable();
            $table->string('currency', 191)->nullable();
            $table->double('ex_rate', 8, 4)->nullable();
            $table->double('amount_in_dollar', 8, 2)->nullable();
            $table->text('pa_party_tpa_agent')->nullable();
            $table->foreignId('booking_id')->nullable()->constrained('bookings')->cascadeOnDelete();
            $table->string('freight_type', 191)->nullable();
            $table->boolean('hide')->default(false);
            $table->string('remarks', 1000)->nullable();
            $table->foreignId('slot_term')->nullable()->constrained('slot_terms')->restrictOnDelete();
            $table->timestamps();

            $table->index(['booking_id', 'container_size_id', 'container_type_id'], 'bk_costs_bk_sz_ty_idx');
        });

        Schema::create('booking_revenues', function (Blueprint $table) {
            $table->id();
            $table->foreignId('charge_id')->nullable()->constrained('charges')->restrictOnDelete();
            $table->foreignId('container_size_id')->nullable()->constrained('container_sizes')->restrictOnDelete();
            $table->foreignId('container_type_id')->nullable()->constrained('container_types')->restrictOnDelete();
            $table->text('quantity')->nullable();
            $table->double('mrg', 8, 2)->nullable();
            $table->double('rate', 8, 2)->nullable();
            $table->double('amount', 8, 2)->nullable();
            $table->string('currency', 191)->nullable();
            $table->double('ex_rate', 8, 4)->nullable();
            $table->double('amount_in_dollar', 8, 2)->nullable();
            $table->text('pa_party_tpa_agent')->nullable();
            $table->foreignId('booking_id')->nullable()->constrained('bookings')->cascadeOnDelete();
            $table->string('freight_type', 191)->nullable();
            $table->boolean('hide')->default(false);
            $table->string('remarks', 1000)->nullable();
            $table->timestamps();

            $table->index(['booking_id', 'container_size_id', 'container_type_id'], 'bk_revenues_bk_sz_ty_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('booking_revenues');
        Schema::dropIfExists('booking_costs');
        Schema::dropIfExists('bl_container_infos');
        Schema::dropIfExists('booking_other_infos');
        Schema::dropIfExists('booking_info_equipments');
        Schema::dropIfExists('booking_bl_details');
        Schema::dropIfExists('bookings');
    }
};
