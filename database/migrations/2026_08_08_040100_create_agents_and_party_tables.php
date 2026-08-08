<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('agents', function (Blueprint $table) {
            $table->id();
            $table->string('name', 191)->nullable();
            $table->string('code', 191)->nullable();
            $table->string('city', 191)->nullable();
            $table->string('country', 191)->nullable();
            $table->timestamps();
            $table->foreignId('pol_id')->nullable()->constrained('pols')->cascadeOnDelete();
            $table->string('currency', 191)->nullable();
            $table->string('amount', 191)->nullable();

            $table->index('name');
            $table->index('code');
            $table->index('city');
        });

        Schema::create('suppliers', function (Blueprint $table) {
            $table->id();
            $table->string('name', 191);
            $table->string('email', 191)->nullable();
            $table->string('contact', 191)->nullable();
            $table->string('address', 3000);
            $table->foreignId('location_id')->nullable()->constrained('pols')->restrictOnDelete();
            $table->timestamps();

            $table->index('name');
            $table->index('email');
        });

        Schema::create('shipper_bps', function (Blueprint $table) {
            $table->id();
            $table->string('code', 191)->nullable();
            $table->foreignId('agent_id')->constrained('agents')->restrictOnDelete();
            $table->string('name', 191)->nullable();
            $table->foreignId('port_id')->constrained('pols')->restrictOnDelete();
            $table->string('type', 191)->nullable();
            $table->string('tax_id', 191)->nullable();
            $table->string('phone_no', 191)->nullable();
            $table->string('web', 191)->nullable();
            $table->string('address', 1000)->nullable();
            $table->string('email', 191)->nullable();
            $table->string('fax', 191)->nullable();
            $table->boolean('shipper')->nullable();
            $table->boolean('ca')->nullable();
            $table->boolean('consignee')->nullable();
            $table->timestamps();

            $table->index('code');
            $table->index('name');
        });

        Schema::create('p_a_s', function (Blueprint $table) {
            $table->id();
            $table->string('name', 191)->nullable();
            $table->string('code', 191)->nullable();
            $table->string('email', 191)->nullable();
            $table->string('address', 191)->nullable();
            $table->string('phone_No', 191)->nullable();
            $table->string('website', 191)->nullable();
            $table->foreignId('agent_id')->nullable()->constrained('agents')->restrictOnDelete();
            $table->string('type', 191)->nullable();
            $table->string('line_type', 191)->nullable();
            $table->timestamps();
            $table->foreignId('agent_name')->nullable()->constrained('agents')->restrictOnDelete();

            $table->index('code');
        });

        Schema::create('agent_container_sizes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('agent_id')->nullable()->constrained('agents')->cascadeOnDelete();
            $table->foreignId('container_size_id')->nullable()->constrained('container_sizes')->cascadeOnDelete();
            $table->timestamps();

            $table->index(['agent_id', 'container_size_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('agent_container_sizes');
        Schema::dropIfExists('p_a_s');
        Schema::dropIfExists('shipper_bps');
        Schema::dropIfExists('suppliers');
        Schema::dropIfExists('agents');
    }
};
