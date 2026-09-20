<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('container_release_orders', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('booking_id')->nullable();
            $table->string('reference_no', 191)->nullable();
            $table->date('booking_date')->nullable();
            $table->tinyInteger('cntr_owner')->nullable();
            $table->unsignedBigInteger('commodity_id')->nullable();
            $table->tinyInteger('dg_status')->nullable();
            $table->unsignedBigInteger('pol_id')->nullable();
            $table->unsignedBigInteger('pofd_id')->nullable();
            $table->timestamps();

            $table->foreign('booking_id')->references('id')->on('bookings')->restrictOnDelete();
            $table->foreign('commodity_id')->references('id')->on('commodities')->restrictOnDelete();
            $table->foreign('pol_id')->references('id')->on('pols')->restrictOnDelete();
            $table->foreign('pofd_id')->references('id')->on('pods')->restrictOnDelete();

            $table->index('booking_id');
            $table->index('reference_no');
            $table->index('booking_date');
            $table->index('commodity_id');
            $table->index('pol_id');
            $table->index('pofd_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('container_release_orders');
    }
};
