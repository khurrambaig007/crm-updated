<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('container_activity_details', function (Blueprint $table) {
            // Switch from boolean to string so the field can hold a Yes/No dropdown value.
            $table->string('one_door_open', 16)->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('container_activity_details', function (Blueprint $table) {
            $table->boolean('one_door_open')->default(false)->change();
        });
    }
};
