<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('roles', function (Blueprint $table) {
            $table->string('role_key')->nullable()->after('organization_id');
            $table->boolean('is_protected')->default(false)->after('role_key');
        });

        Schema::table('permissions', function (Blueprint $table) {
            $table->string('key')->unique()->nullable()->after('id');
            $table->string('action')->nullable()->after('key');
            $table->boolean('is_active')->default(true)->after('description');
        });

        Schema::table('role_permissions', function (Blueprint $table) {
            // Using string for access_level to support Postgres enum constraints easily or just string
            $table->string('access_level')->default('none')->after('permission_id');
        });

        Schema::table('user_roles', function (Blueprint $table) {
            $table->foreignId('assigned_by')->nullable()->constrained('users')->nullOnDelete()->after('site_id');
            $table->timestamp('assigned_at')->nullable()->after('assigned_by');
        });
    }

    public function down(): void
    {
        Schema::table('user_roles', function (Blueprint $table) {
            $table->dropForeign(['assigned_by']);
            $table->dropColumn(['assigned_by', 'assigned_at']);
        });

        Schema::table('role_permissions', function (Blueprint $table) {
            $table->dropColumn('access_level');
        });

        Schema::table('permissions', function (Blueprint $table) {
            $table->dropColumn(['key', 'action', 'is_active']);
        });

        Schema::table('roles', function (Blueprint $table) {
            $table->dropColumn(['role_key', 'is_protected']);
        });
    }
};
