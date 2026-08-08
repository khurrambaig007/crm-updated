<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pols', function (Blueprint $table) {
            $table->dropIndex('pols_location_code_index');
            $table->renameColumn('location_code', 'port_code');
            $table->index('port_code');
            $table->foreignId('container_size_id')
                ->nullable()
                ->constrained('container_sizes')
                ->restrictOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('pols', function (Blueprint $table) {
            $table->dropForeign(['container_size_id']);
            $table->dropColumn('container_size_id');
            $table->dropIndex('pols_port_code_index');
            $table->renameColumn('port_code', 'location_code');
            $table->index('location_code');
        });
    }
};
