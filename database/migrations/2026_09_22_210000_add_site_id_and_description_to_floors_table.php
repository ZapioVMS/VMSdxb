<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('floors', function (Blueprint $table) {
            if (!Schema::hasColumn('floors', 'site_id')) {
                $table->foreignId('site_id')->nullable()->after('organization_id')->constrained('sites')->cascadeOnDelete();
            }
            if (!Schema::hasColumn('floors', 'description')) {
                $table->text('description')->nullable()->after('name');
            }
            $table->foreignId('building_id')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('floors', function (Blueprint $table) {
            if (Schema::hasColumn('floors', 'site_id')) {
                $table->dropConstrainedForeignId('site_id');
            }
            if (Schema::hasColumn('floors', 'description')) {
                $table->dropColumn('description');
            }
            $table->foreignId('building_id')->nullable(false)->change();
        });
    }
};
