<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sales_invoices', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_number', 11)->nullable()->unique();
            $table->foreignId('party_id')->constrained('p_a_s')->restrictOnDelete();
            $table->date('invoice_date')->nullable();
            $table->date('due_date')->nullable();
            $table->string('our_reference', 191)->nullable();
            $table->string('customer_contact', 191)->nullable();
            $table->text('remarks')->nullable();
            $table->decimal('vat_rate', 5, 2)->default(0);
            $table->decimal('subtotal', 12, 2)->default(0);
            $table->decimal('vat_amount', 12, 2)->default(0);
            $table->decimal('total_amount', 12, 2)->default(0);
            $table->timestamps();

            $table->index('invoice_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sales_invoices');
    }
};
