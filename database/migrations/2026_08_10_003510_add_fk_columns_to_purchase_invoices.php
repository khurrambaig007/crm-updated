<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('purchase_invoices', function (Blueprint $table) {
            $table->renameColumn('invoice_date', 'purchase_invoice_date');

            $table->foreignId('vendor_id')->nullable()->after('period_to')->constrained('suppliers')->nullOnDelete();
            $table->foreignId('port_id')->nullable()->after('vendor_id')->constrained('pols')->nullOnDelete();
            $table->foreignId('settlement_type_id')->nullable()->after('payment_center')->constrained('settlement_types')->nullOnDelete();
            $table->foreignId('sub_company_id')->nullable()->after('settlement_type_id')->constrained('sub_companies')->nullOnDelete();
        });

        Schema::table('purchase_invoices', function (Blueprint $table) {
            $table->dropColumn('vendor');
            $table->dropColumn('port');
            $table->dropColumn('settlement_type');
            $table->dropColumn('sub_company');
        });
    }

    public function down(): void
    {
        Schema::table('purchase_invoices', function (Blueprint $table) {
            $table->tinyInteger('vendor')->nullable()->after('period_to');
            $table->longText('port')->nullable()->after('vendor');
            $table->bigInteger('settlement_type')->nullable()->after('payment_center');
            $table->bigInteger('sub_company')->nullable()->after('settlement_type');
        });

        Schema::table('purchase_invoices', function (Blueprint $table) {
            $table->dropForeign(['vendor_id']);
            $table->dropColumn('vendor_id');
            $table->dropForeign(['port_id']);
            $table->dropColumn('port_id');
            $table->dropForeign(['settlement_type_id']);
            $table->dropColumn('settlement_type_id');
            $table->dropForeign(['sub_company_id']);
            $table->dropColumn('sub_company_id');
        });

        Schema::table('purchase_invoices', function (Blueprint $table) {
            $table->renameColumn('purchase_invoice_date', 'invoice_date');
        });
    }
};
