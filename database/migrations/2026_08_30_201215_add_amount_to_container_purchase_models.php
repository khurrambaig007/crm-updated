<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('container_purchase_models', function (Blueprint $table) {
            $table->double('amount')->nullable()->after('quantity');
        });
    }

    public function down(): void
    {
        Schema::table('container_purchase_models', function (Blueprint $table) {
            $table->dropColumn('amount');
        });
    }
};
