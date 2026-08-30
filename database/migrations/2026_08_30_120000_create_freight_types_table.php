<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('freight_types', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->timestamps();
        });

        DB::table('freight_types')->insert([
            ['name' => 'Regular', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Zero', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Negative', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('freight_types');
    }
};
