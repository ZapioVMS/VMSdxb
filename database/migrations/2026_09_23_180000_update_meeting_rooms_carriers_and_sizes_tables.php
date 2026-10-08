<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('meeting_rooms', function (Blueprint $table) {
            if (!Schema::hasColumn('meeting_rooms', 'site_id')) {
                $table->foreignId('site_id')->nullable()->after('organization_id')->constrained('sites')->cascadeOnDelete();
            }
            if (!Schema::hasColumn('meeting_rooms', 'capacity')) {
                $table->integer('capacity')->default(8)->nullable()->after('name');
            }
        });

        Schema::table('delivery_carriers', function (Blueprint $table) {
            if (!Schema::hasColumn('delivery_carriers', 'code')) {
                $table->string('code', 50)->nullable()->after('name');
            }
        });

        Schema::table('package_sizes', function (Blueprint $table) {
            if (!Schema::hasColumn('package_sizes', 'description')) {
                $table->text('description')->nullable()->after('name');
            }
        });
    }

    public function down(): void
    {
        Schema::table('meeting_rooms', function (Blueprint $table) {
            if (Schema::hasColumn('meeting_rooms', 'site_id')) {
                $table->dropConstrainedForeignId('site_id');
            }
            if (Schema::hasColumn('meeting_rooms', 'capacity')) {
                $table->dropColumn('capacity');
            }
        });

        Schema::table('delivery_carriers', function (Blueprint $table) {
            if (Schema::hasColumn('delivery_carriers', 'code')) {
                $table->dropColumn('code');
            }
        });

        Schema::table('package_sizes', function (Blueprint $table) {
            if (Schema::hasColumn('package_sizes', 'description')) {
                $table->dropColumn('description');
            }
        });
    }
};
