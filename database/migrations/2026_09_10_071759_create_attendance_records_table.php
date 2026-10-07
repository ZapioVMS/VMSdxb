<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attendance_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained('organizations')->onDelete('cascade');
            $table->foreignId('employee_id')->constrained('employees')->onDelete('cascade');
            $table->foreignId('site_id')->constrained('sites')->onDelete('cascade');
            $table->bigInteger('kiosk_device_id')->nullable();
            $table->date('attendance_date');
            $table->timestampTz('check_in_at');
            $table->timestampTz('check_out_at')->nullable();
            $table->string('status', 40)->default('present');
            $table->string('source', 40)->default('kiosk');
            $table->timestampsTz();

            $table->foreign(['organization_id', 'employee_id'])->references(['organization_id', 'id'])->on('employees')->onDelete('cascade');
            $table->foreign(['organization_id', 'site_id'])->references(['organization_id', 'id'])->on('sites')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attendance_records');
    }
};
