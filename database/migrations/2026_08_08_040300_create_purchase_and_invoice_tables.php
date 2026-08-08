<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('purchase_invoices', function (Blueprint $table) {
            $table->id();
            $table->date('transaction_date')->nullable();
            $table->string('transaction_number', 191)->nullable();
            $table->tinyInteger('status')->nullable();
            $table->date('invoice_date')->nullable();
            $table->string('invoice_number', 191)->nullable();
            $table->date('period_from')->nullable();
            $table->date('period_to')->nullable();
            $table->tinyInteger('vendor')->nullable();
            $table->longText('port')->nullable();
            $table->longText('payment_center')->nullable();
            $table->bigInteger('settlement_type')->nullable();
            $table->bigInteger('sub_company')->nullable();
            $table->longText('attachments')->nullable();
            $table->double('total', 8, 2)->default(0);
            $table->double('vat', 8, 2)->default(0);
            $table->double('net_amount', 8, 2)->default(0);
            $table->string('approved_by', 191)->nullable();
            $table->date('approved_on')->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->index('transaction_number');
            $table->index('invoice_number');
            $table->index('invoice_date');
            $table->index('status');
        });

        Schema::create('purchase_invoice_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('purchase_invoice_id')->nullable();
            $table->string('charges', 191)->nullable();
            $table->string('type', 191)->nullable();
            $table->string('m_r_number', 191)->nullable();
            $table->string('bl_number', 191)->nullable();
            $table->string('container_number', 191)->nullable();
            $table->string('size', 191)->nullable();
            $table->string('size_type', 191)->nullable();
            $table->double('amount', 8, 2)->default(0);
            $table->string('currency', 191)->nullable();
            $table->string('exchange_rate', 191)->nullable();
            $table->double('amount_dollar', 8, 2)->default(0);
            $table->string('vat_percentage', 191)->nullable();
            $table->double('vat_amount', 8, 2)->default(0);
            $table->double('vat_amount_dollar', 8, 2)->default(0);
            $table->string('remarks', 191)->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('purchase_invoice_id')->references('id')->on('purchase_invoices')->restrictOnDelete();
            $table->index('m_r_number');
            $table->index('bl_number');
            $table->index('container_number');
        });

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

        Schema::create('debite_notes', function (Blueprint $table) {
            $table->id();
            $table->string('doc_no', 191);
            $table->foreignId('invoice_id')->constrained('invoices')->restrictOnDelete();
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
            $table->index(['supplier_id', 'currency_id']);
        });

        Schema::create('po_cancels', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('doc_no');
            $table->date('transaction_date');
            $table->unsignedBigInteger('trans_no');
            $table->timestamps();

            $table->index('doc_no');
            $table->index('trans_no');
            $table->index('transaction_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('po_cancels');
        Schema::dropIfExists('debite_notes');
        Schema::dropIfExists('invoices');
        Schema::dropIfExists('purchase_invoice_details');
        Schema::dropIfExists('purchase_invoices');
    }
};
