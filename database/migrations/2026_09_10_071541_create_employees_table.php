<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained('organizations')->onDelete('cascade');
            $table->foreignId('site_id')->constrained('sites')->onDelete('cascade');
            $table->foreignId('department_id')->constrained('departments')->onDelete('cascade');
            $table->string('employee_code');
            $table->string('first_name');
            $table->string('last_name')->nullable();
            $table->string('email', 254)->nullable();
            $table->string('phone', 32)->nullable();
            $table->string('job_title')->nullable();
            $table->bigInteger('manager_id')->nullable();
            $table->bigInteger('shift_id')->nullable();
            $table->string('photo_path')->nullable();
            $table->string('status', 40)->default('active');

            $table->unique(['organization_id', 'employee_code']);
            $table->unique(['organization_id', 'id']);
            $table->unique(['organization_id', 'site_id', 'id']);
            
            $table->foreign(['organization_id', 'site_id'])->references(['organization_id', 'id'])->on('sites')->onDelete('cascade');
            $table->foreign(['organization_id', 'department_id'])->references(['organization_id', 'id'])->on('departments')->onDelete('cascade');
            $table->foreign(['organization_id', 'shift_id'])->references(['organization_id', 'id'])->on('shifts')->onDelete('restrict');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};
