<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('label_collections');
        Schema::dropIfExists('labels');
        Schema::dropIfExists('costs');

        Schema::create('costs', function (Blueprint $table) {
            $table->id();

            // Dropdowns
            $table->foreignId('pol_id')->nullable()->constrained('pols')->cascadeOnDelete();
            $table->foreignId('pod_id')->nullable()->constrained('pods')->cascadeOnDelete();
            $table->foreignId('container_type_id')->nullable()->constrained('container_types')->cascadeOnDelete();
            $table->foreignId('feeder_id')->nullable()->constrained('feeders')->cascadeOnDelete();
            $table->foreignId('container_size_id')->nullable()->constrained('container_sizes')->cascadeOnDelete();
            $table->foreignId('slot_term_id')->nullable()->constrained('slot_terms')->cascadeOnDelete();
            $table->foreignId('pod_agent_id')->nullable()->constrained('agents')->cascadeOnDelete();
            $table->foreignId('pol_agent_id')->nullable()->constrained('agents')->cascadeOnDelete();

            // Cost fields
            $table->string('slot', 191)->nullable();
            $table->string('dthc', 191)->nullable();
            $table->string('wrr', 191)->nullable();
            $table->string('ts_thc', 191)->nullable();
            $table->string('ts_commission', 191)->nullable();

            // Collection fields
            $table->string('of', 191)->nullable();
            $table->string('pod_rebate', 191)->nullable();
            $table->string('free_days', 191)->nullable();

            // Calculated fields
            $table->string('total_cost', 191)->nullable();
            $table->string('total_collection', 191)->nullable();
            $table->string('net_shipping', 191)->nullable();

            $table->timestamps();

            $table->index(['pol_id', 'pod_id', 'container_size_id', 'container_type_id'], 'costs_route_sz_ty_idx');
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
    }

    public function down(): void
    {
        Schema::dropIfExists('label_collections');
        Schema::dropIfExists('labels');
        Schema::dropIfExists('costs');
    }
};
