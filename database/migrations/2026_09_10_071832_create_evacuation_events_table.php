<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('evacuation_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained('organizations')->onDelete('cascade');
            $table->foreignId('site_id')->constrained('sites')->onDelete('cascade');
            $table->foreignId('started_by')->constrained('users')->onDelete('cascade');
            $table->timestampTz('started_at');
            $table->timestampTz('ended_at')->nullable();
            $table->string('status', 40)->default('active');
            $table->text('notes')->nullable();

            $table->unique(['organization_id', 'id']);
            $table->foreign(['organization_id', 'site_id'])->references(['organization_id', 'id'])->on('sites')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('evacuation_events');
    }
};
