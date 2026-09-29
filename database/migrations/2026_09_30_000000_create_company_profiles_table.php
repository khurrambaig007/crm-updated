<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('company_profiles', function (Blueprint $table) {
            $table->id();

            // Company identity
            $table->string('name', 191)->nullable();
            $table->string('logo', 191)->nullable();
            $table->string('website', 191)->nullable();
            $table->string('number', 191)->nullable();
            $table->json('emails')->nullable();

            // Person in contact
            $table->string('pic_name', 191)->nullable();
            $table->string('pic_email', 191)->nullable();
            $table->string('pic_number', 191)->nullable();

            // User-defined label/value pairs added at runtime
            $table->json('custom_fields')->nullable();

            $table->text('message')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('company_profiles');
    }
};
