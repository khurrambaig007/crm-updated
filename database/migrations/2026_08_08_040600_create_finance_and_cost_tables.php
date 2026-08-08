<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('costs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pol_id')->nullable()->constrained('pols')->cascadeOnDelete();
            $table->foreignId('pod_id')->nullable()->constrained('pols')->cascadeOnDelete();
            $table->foreignId('feeder_id')->nullable()->constrained('feeders')->cascadeOnDelete();
            $table->foreignId('agent_id')->nullable()->constrained('agents')->cascadeOnDelete();
            $table->string('term', 191)->nullable();
            $table->string('total', 191)->nullable();
            $table->string('slot', 191)->nullable();
            $table->string('pol_agent', 191)->nullable();
            $table->string('dthc', 191)->nullable();
            $table->string('wrr', 191)->nullable();
            $table->string('lss', 191)->nullable();
            $table->string('dg', 191)->nullable();
            $table->string('pod_agent', 191)->nullable();
            $table->timestamps();
            $table->string('of', 191)->nullable();
            $table->string('lthc', 191)->nullable();
            $table->string('pod_r', 191)->nullable();
            $table->string('free_days', 191)->nullable();
            $table->string('total_collection', 191)->nullable();
            $table->string('net_total', 191)->nullable();
            $table->foreignId('container_type_id')->nullable()->constrained('container_types')->cascadeOnDelete();
            $table->foreignId('container_size_id')->nullable()->constrained('container_sizes')->cascadeOnDelete();
            $table->foreignId('slot_term_id')->nullable()->constrained('slot_terms')->cascadeOnDelete();
            $table->foreignId('pol_commission_id')->nullable()->constrained('agents')->cascadeOnDelete();
            $table->foreignId('pod_commission_id')->nullable()->constrained('agents')->cascadeOnDelete();

            $table->index(['pol_id', 'pod_id', 'agent_id', 'container_size_id', 'container_type_id'], 'costs_route_agent_sz_ty_idx');
        });

        Schema::create('labels', function (Blueprint $table) {
            $table->id();
            $table->string('key', 191)->nullable();
            $table->string('value', 191)->nullable();
            $table->foreignId('cost_id')->nullable()->constrained('costs')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->cascadeOnDelete();
            $table->timestamps();

            $table->index('key');
        });

        Schema::create('label_collections', function (Blueprint $table) {
            $table->id();
            $table->string('key', 191)->nullable();
            $table->string('value', 191)->nullable();
            $table->foreignId('cost_id')->nullable()->constrained('costs')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->cascadeOnDelete();
            $table->timestamps();

            $table->index('key');
        });

        Schema::create('agent_receipt_payments', function (Blueprint $table) {
            $table->id();
            $table->string('transaction_no', 191)->nullable();
            $table->date('receipt_date')->nullable();
            $table->longText('agent')->nullable();
            $table->tinyInteger('mode')->nullable();
            $table->tinyInteger('sub_mode')->nullable();
            $table->string('currency', 100)->nullable();
            $table->double('exchange_rate', 8, 6)->nullable();
            $table->string('remarks', 1500)->nullable();
            $table->tinyInteger('act_mode')->nullable();
            $table->double('bank_charges', 8, 2)->nullable();
            $table->longText('ac_code')->nullable();
            $table->string('cheque_no', 191)->nullable();
            $table->date('cheque_date')->nullable();
            $table->double('gain_loss', 8, 2)->nullable();
            $table->double('total_amount', 8, 2)->nullable();
            $table->double('total_amount_1', 8, 2)->nullable();
            $table->string('approved_by', 191)->nullable();
            $table->string('approved_on', 191)->nullable();
            $table->timestamps();

            $table->index('transaction_no');
            $table->index('receipt_date');
            $table->index('cheque_no');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('agent_receipt_payments');
        Schema::dropIfExists('label_collections');
        Schema::dropIfExists('labels');
        Schema::dropIfExists('costs');
    }
};
