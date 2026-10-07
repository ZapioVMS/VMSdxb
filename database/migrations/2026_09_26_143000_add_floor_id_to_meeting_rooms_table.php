<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('meeting_rooms', function (Blueprint $table) {
            if (!Schema::hasColumn('meeting_rooms', 'floor_id')) {
                $table->foreignId('floor_id')
                    ->nullable()
                    ->after('site_id')
                    ->constrained('floors')
                    ->nullOnDelete();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('meeting_rooms', function (Blueprint $table) {
            if (Schema::hasColumn('meeting_rooms', 'floor_id')) {
                $table->dropForeign(['floor_id']);
                $table->dropColumn('floor_id');
            }
        });
    }
};
