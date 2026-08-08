<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('port_container_sizes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pol_id')->nullable()->constrained('pols')->cascadeOnDelete();
            $table->foreignId('container_size_id')->nullable()->constrained('container_sizes')->cascadeOnDelete();
            $table->timestamps();

            $table->index(['pol_id', 'container_size_id']);
        });

        Schema::create('container_purchases', function (Blueprint $table) {
            $table->id();
            $table->string('train_no', 191)->nullable();
            $table->date('date')->nullable();
            $table->string('normal_purchase', 191)->nullable();
            $table->bigInteger('supplier_id')->unsigned()->nullable();
            $table->bigInteger('port_id')->unsigned()->nullable();
            $table->bigInteger('handling_id')->unsigned()->nullable();
            $table->date('expected_delivery')->nullable();
            $table->string('po_no', 191)->nullable();
            $table->string('release_no', 191)->nullable();
            $table->string('currency', 191)->nullable();
            $table->string('rate', 191)->nullable();
            $table->string('principal', 191)->nullable();
            $table->timestamps();
            $table->string('trans_no', 191);
            $table->string('currency_code', 10);

            $table->index('train_no');
            $table->index('date');
            $table->index('supplier_id');
            $table->index('port_id');
            $table->index('po_no');
            $table->index('release_no');
            $table->index('trans_no');
        });

        Schema::create('container_purchase_models', function (Blueprint $table) {
            $table->id();
            $table->string('auto', 191)->nullable();
            $table->foreignId('container_size_id')->nullable()->constrained('container_sizes')->restrictOnDelete();
            $table->foreignId('container_type_id')->nullable()->constrained('container_types')->restrictOnDelete();
            $table->foreignId('container_kind_id')->nullable()->constrained('container_kinds')->restrictOnDelete();
            $table->string('quantity', 191)->nullable();
            $table->string('price', 191)->nullable();
            $table->string('total', 191)->nullable();
            $table->double('rate')->nullable();
            $table->timestamps();
            $table->foreignId('container_purchase_detail_id')->nullable()->constrained('container_purchases')->restrictOnDelete();

            $table->index('auto');
        });

        Schema::create('container_purchase_releases', function (Blueprint $table) {
            $table->id();
            $table->foreignId('container_purchase_detail_id')->nullable()->constrained('container_purchases')->restrictOnDelete();
            $table->string('container_number', 191)->nullable();
            $table->foreignId('container_size')->nullable()->constrained('container_sizes')->restrictOnDelete();
            $table->foreignId('container_type')->nullable()->constrained('container_types')->restrictOnDelete();
            $table->foreignId('container_kind')->nullable()->constrained('container_kinds')->restrictOnDelete();
            $table->string('m_f_year', 191)->nullable();
            $table->double('rate')->nullable();
            $table->string('remarks', 500)->nullable();
            $table->string('original_container_number', 500)->nullable();
            $table->timestamps();

            $table->index('container_number');
            $table->index('original_container_number');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('container_purchase_releases');
        Schema::dropIfExists('container_purchase_models');
        Schema::dropIfExists('container_purchases');
        Schema::dropIfExists('port_container_sizes');
    }
};
