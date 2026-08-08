<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pols', function (Blueprint $table) {
            $table->id();
            $table->string('city', 191)->nullable();
            $table->string('country', 191)->nullable();
            $table->string('location_code', 191)->nullable();
            $table->string('rebate', 191)->nullable();
            $table->timestamps();

            $table->index('city');
            $table->index('country');
            $table->index('location_code');
        });

        Schema::create('pods', function (Blueprint $table) {
            $table->id();
            $table->string('city', 191)->nullable();
            $table->string('country', 191)->nullable();
            $table->string('location_code', 191)->nullable();
            $table->timestamps();

            $table->index('city');
            $table->index('country');
            $table->index('location_code');
        });

        Schema::create('container_sizes', function (Blueprint $table) {
            $table->id();
            $table->string('size', 191)->nullable();
            $table->timestamps();

            $table->index('size');
        });

        Schema::create('container_types', function (Blueprint $table) {
            $table->id();
            $table->string('name', 191)->nullable();
            $table->timestamps();

            $table->index('name');
        });

        Schema::create('container_kinds', function (Blueprint $table) {
            $table->id();
            $table->string('name', 191)->nullable();
            $table->timestamps();

            $table->index('name');
        });

        Schema::create('slot_terms', function (Blueprint $table) {
            $table->id();
            $table->string('term', 191)->nullable();
            $table->timestamps();

            $table->index('term');
        });

        Schema::create('settlement_types', function (Blueprint $table) {
            $table->id();
            $table->string('name', 191)->nullable();
            $table->string('number', 191)->nullable();
            $table->timestamps();

            $table->index('name');
        });

        Schema::create('sub_companies', function (Blueprint $table) {
            $table->id();
            $table->string('name', 191)->nullable();
            $table->string('email', 191)->nullable();
            $table->string('contact', 191)->nullable();
            $table->timestamps();

            $table->index('name');
            $table->index('email');
        });

        Schema::create('currencies', function (Blueprint $table) {
            $table->id();
            $table->date('exchange_rate_date');
            $table->string('exchange_rate', 3000)->nullable();
            $table->timestamps();

            $table->index('exchange_rate_date');
        });

        Schema::create('feeders', function (Blueprint $table) {
            $table->id();
            $table->string('name', 191)->nullable();
            $table->string('code', 191)->nullable();
            $table->text('description')->nullable();
            $table->timestamps();

            $table->index('name');
            $table->index('code');
        });

        Schema::create('investors', function (Blueprint $table) {
            $table->id();
            $table->string('name', 191)->nullable();
            $table->string('contact_number', 191)->nullable();
            $table->string('email', 191)->nullable();
            $table->timestamps();

            $table->index('email');
        });

        Schema::create('commodities', function (Blueprint $table) {
            $table->id();
            $table->string('commodity_number', 191)->nullable();
            $table->timestamps();

            $table->index('commodity_number');
        });

        Schema::create('charges', function (Blueprint $table) {
            $table->id();
            $table->text('name');
            $table->timestamps();
        });

        Schema::create('vessel_voyages', function (Blueprint $table) {
            $table->id();
            $table->string('vessel_name', 191)->nullable();
            $table->string('voyage_number', 191)->nullable();
            $table->timestamps();

            $table->index(['vessel_name', 'voyage_number']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vessel_voyages');
        Schema::dropIfExists('charges');
        Schema::dropIfExists('commodities');
        Schema::dropIfExists('investors');
        Schema::dropIfExists('feeders');
        Schema::dropIfExists('currencies');
        Schema::dropIfExists('sub_companies');
        Schema::dropIfExists('settlement_types');
        Schema::dropIfExists('slot_terms');
        Schema::dropIfExists('container_kinds');
        Schema::dropIfExists('container_types');
        Schema::dropIfExists('container_sizes');
        Schema::dropIfExists('pods');
        Schema::dropIfExists('pols');
    }
};
