<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('organization_id')->nullable()->after('id')->constrained('organizations')->onDelete('cascade');
            $table->bigInteger('employee_id')->nullable()->after('organization_id');
            $table->string('status', 40)->default('active')->after('password');
            $table->timestampTz('last_login_at')->nullable()->after('status');
            $table->boolean('two_factor_enabled')->default(false)->after('last_login_at');

            $table->unique(['organization_id', 'id']);
            $table->foreign(['organization_id', 'employee_id'])->references(['organization_id', 'id'])->on('employees')->onDelete('restrict');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['organization_id', 'employee_id']);
            $table->dropUnique(['organization_id', 'id']);
            $table->dropColumn(['organization_id', 'employee_id', 'status', 'last_login_at', 'two_factor_enabled']);
        });
    }
};
