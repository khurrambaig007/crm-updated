<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sales_invoices', function (Blueprint $table) {
            $table->string('currency_code', 10)->default('PKR')->after('id');
            $table->string('status', 20)->default('unpaid')->after('total_amount');
        });

        DB::table('sales_invoices')->whereNull('currency_code')->update(['currency_code' => 'PKR']);
        DB::table('sales_invoices')->whereNull('status')->update(['status' => 'unpaid']);
    }

    public function down(): void
    {
        Schema::table('sales_invoices', function (Blueprint $table) {
            $table->dropColumn(['currency_code', 'status']);
        });
    }
};
