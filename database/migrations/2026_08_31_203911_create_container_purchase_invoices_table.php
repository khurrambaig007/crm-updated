<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('container_purchase_invoices', function (Blueprint $table) {
            $table->id();
            $table->string('doc_no', 191);
            $table->string('invoice_no', 191);
            $table->date('invoice_date');
            $table->foreignId('settlement_type_id')->constrained('settlement_types')->restrictOnDelete();
            $table->foreignId('payment_agent_id')->constrained('agents')->restrictOnDelete();
            $table->foreignId('currency_id')->constrained('currencies')->restrictOnDelete();
            $table->double('amount', 8, 2);
            $table->foreignId('supplier_id')->constrained('suppliers')->restrictOnDelete();
            $table->foreignId('location_id')->constrained('pols')->restrictOnDelete();
            $table->foreignId('sub_company_id')->constrained('sub_companies')->restrictOnDelete();
            $table->foreignId('container_purchase_detail_id')->nullable()->constrained('container_purchases')->restrictOnDelete();
            $table->string('currency_exchange_rate', 191)->nullable();
            $table->string('currency_code', 10)->nullable();
            $table->double('total_amount', 8, 2)->nullable();
            $table->timestamps();

            $table->index('doc_no');
            $table->index('invoice_no');
            $table->index('invoice_date');
            $table->index(['supplier_id', 'invoice_date']);
        });

        Schema::table('debite_notes', function (Blueprint $table) {
            $table->dropForeign(['invoice_id']);
            $table->foreign('invoice_id')->references('id')->on('container_purchase_invoices')->restrictOnDelete();
        });

        Schema::dropIfExists('invoices');
    }

    public function down(): void
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->string('doc_no', 191);
            $table->string('invoice_no', 191);
            $table->date('invoice_date');
            $table->foreignId('settlement_type_id')->constrained('settlement_types')->restrictOnDelete();
            $table->foreignId('payment_agent_id')->constrained('agents')->restrictOnDelete();
            $table->foreignId('currency_id')->constrained('currencies')->restrictOnDelete();
            $table->double('amount', 8, 2);
            $table->foreignId('supplier_id')->constrained('suppliers')->restrictOnDelete();
            $table->foreignId('location_id')->constrained('pols')->restrictOnDelete();
            $table->foreignId('sub_company_id')->constrained('sub_companies')->restrictOnDelete();
            $table->foreignId('container_purchase_detail_id')->nullable()->constrained('container_purchases')->restrictOnDelete();
            $table->string('currency_exchange_rate', 191)->nullable();
            $table->string('currency_code', 10)->nullable();
            $table->double('total_amount', 8, 2)->nullable();
            $table->timestamps();

            $table->index('doc_no');
            $table->index('invoice_no');
            $table->index(['supplier_id', 'invoice_date']);
        });

        Schema::table('debite_notes', function (Blueprint $table) {
            $table->dropForeign(['invoice_id']);
            $table->foreign('invoice_id')->references('id')->on('invoices')->restrictOnDelete();
        });

        Schema::dropIfExists('container_purchase_invoices');
    }
};
