<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('po_cancels', function (Blueprint $table) {
            $table->dropIndex(['doc_no']);
            $table->dropIndex(['trans_no']);
            $table->dropColumn(['doc_no', 'trans_no']);

            $table->foreignId('invoice_id')->constrained('container_purchase_invoices')->restrictOnDelete();
            $table->foreignId('container_purchase_detail_id')->nullable()->constrained('container_purchases')->restrictOnDelete();

            $table->index('invoice_id');
        });
    }

    public function down(): void
    {
        Schema::table('po_cancels', function (Blueprint $table) {
            $table->dropForeign(['invoice_id', 'container_purchase_detail_id']);
            $table->dropIndex(['invoice_id']);
            $table->dropColumn(['invoice_id', 'container_purchase_detail_id']);

            $table->unsignedBigInteger('doc_no');
            $table->unsignedBigInteger('trans_no');
            $table->index('doc_no');
            $table->index('trans_no');
        });
    }
};
