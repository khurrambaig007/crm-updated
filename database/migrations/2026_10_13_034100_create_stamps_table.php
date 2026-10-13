<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Stamp library for the System Operations > Stamps Info screen.
     *
     * `image` holds the path on the public disk, not a blob — the same shape the
     * company logo uses, so both resolve through Storage::disk('public').
     */
    public function up(): void
    {
        Schema::create('stamps', function (Blueprint $table): void {
            $table->id();
            $table->string('name', 191)->nullable();
            $table->string('image', 191)->nullable();
            $table->timestamps();

            $table->index('name');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stamps');
    }
};
