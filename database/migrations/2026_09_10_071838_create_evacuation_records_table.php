<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('evacuation_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained('organizations')->onDelete('cascade');
            $table->foreignId('evacuation_event_id')->constrained('evacuation_events')->onDelete('cascade');
            $table->string('person_type', 40);
            $table->bigInteger('employee_id')->nullable();
            $table->bigInteger('visit_id')->nullable();
            $table->string('status', 40)->default('unaccounted');
            $table->foreignId('marked_safe_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestampTz('marked_safe_at')->nullable();

            $table->foreign(['organization_id', 'evacuation_event_id'])->references(['organization_id', 'id'])->on('evacuation_events')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('evacuation_records');
    }
};
