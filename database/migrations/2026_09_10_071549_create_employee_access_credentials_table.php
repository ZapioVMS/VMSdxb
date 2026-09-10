<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('employee_access_credentials', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained('organizations')->onDelete('cascade');
            $table->foreignId('employee_id')->constrained('employees')->onDelete('cascade');
            $table->string('credential_type', 40)->default('qr');
            $table->string('token_hash', 64);
            $table->boolean('qr_enabled')->default(true);
            $table->timestampTz('valid_from');
            $table->timestampTz('valid_until')->nullable();
            $table->string('status', 40)->default('active');
            $table->timestampTz('last_used_at')->nullable();

            $table->foreign(['organization_id', 'employee_id'])->references(['organization_id', 'id'])->on('employees')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employee_access_credentials');
    }
};
