<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('visits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained('organizations')->onDelete('cascade');
            $table->foreignId('visitor_id')->constrained('visitors')->onDelete('cascade');
            $table->foreignId('site_id')->constrained('sites')->onDelete('cascade');
            $table->bigInteger('host_employee_id')->nullable();
            $table->bigInteger('department_id')->nullable();
            $table->bigInteger('purpose_id')->nullable();
            $table->bigInteger('visitor_type_id')->nullable();
            $table->timestampTz('scheduled_start_at')->nullable();
            $table->timestampTz('scheduled_end_at')->nullable();
            $table->timestampTz('check_in_at')->nullable();
            $table->timestampTz('check_out_at')->nullable();
            $table->string('status', 40)->default('scheduled');
            $table->string('source', 40)->default('walkin');
            $table->text('notes')->nullable();

            $table->unique(['organization_id', 'id']);
            $table->foreign(['organization_id', 'visitor_id'])->references(['organization_id', 'id'])->on('visitors')->onDelete('cascade');
            $table->foreign(['organization_id', 'site_id'])->references(['organization_id', 'id'])->on('sites')->onDelete('cascade');
            // Enforce that host_employee belongs to the EXACT SAME organization AND site as the visit
            $table->foreign(['organization_id', 'site_id', 'host_employee_id'])->references(['organization_id', 'site_id', 'id'])->on('employees')->onDelete('restrict');
            $table->foreign(['organization_id', 'department_id'])->references(['organization_id', 'id'])->on('departments')->onDelete('restrict');
            $table->foreign(['organization_id', 'purpose_id'])->references(['organization_id', 'id'])->on('visit_purposes')->onDelete('restrict');
            $table->foreign(['organization_id', 'visitor_type_id'])->references(['organization_id', 'id'])->on('visitor_types')->onDelete('restrict');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('visits');
    }
};
