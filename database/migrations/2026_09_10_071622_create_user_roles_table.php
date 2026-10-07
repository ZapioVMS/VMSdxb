<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_roles', function (Blueprint $table) {
            $table->foreignId('organization_id')->constrained('organizations')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('role_id')->constrained('roles')->onDelete('cascade');
            $table->foreignId('site_id')->nullable()->constrained('sites')->onDelete('cascade');

            $table->foreign(['organization_id', 'user_id'])->references(['organization_id', 'id'])->on('users')->onDelete('cascade');
            $table->foreign(['organization_id', 'role_id'])->references(['organization_id', 'id'])->on('roles')->onDelete('cascade');
            $table->foreign(['organization_id', 'site_id'])->references(['organization_id', 'id'])->on('sites')->onDelete('cascade');
        });

        DB::statement('CREATE UNIQUE INDEX user_roles_global_unique ON user_roles (organization_id, user_id, role_id) WHERE site_id IS NULL');
        DB::statement('CREATE UNIQUE INDEX user_roles_site_unique ON user_roles (organization_id, user_id, role_id, site_id) WHERE site_id IS NOT NULL');
    }

    public function down(): void
    {
        Schema::dropIfExists('user_roles');
    }
};
