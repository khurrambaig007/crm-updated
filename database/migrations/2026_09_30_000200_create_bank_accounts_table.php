<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bank_accounts', function (Blueprint $table) {
            $table->id();

            // Bank identity
            $table->string('bank', 191)->nullable();
            $table->string('beneficiary_name', 191)->nullable();
            $table->string('bank_name', 191)->nullable();
            $table->string('account', 191)->nullable();
            $table->string('iban', 191)->nullable();
            $table->string('swift', 191)->nullable();
            $table->text('address')->nullable();

            // User-defined label/value pairs added at runtime
            $table->json('custom_fields')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bank_accounts');
    }
};
