<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('badges', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained('organizations')->onDelete('cascade');
            $table->foreignId('visit_id')->constrained('visits')->onDelete('cascade');
            $table->string('badge_number');
            $table->foreignId('template_id')->constrained('badge_templates')->onDelete('cascade');
            $table->timestampTz('issued_at');
            $table->timestampTz('expires_at');
            $table->timestampTz('printed_at')->nullable();
            $table->timestampTz('returned_at')->nullable();
            $table->string('status', 40)->default('issued');

            $table->unique(['organization_id', 'badge_number']);
            $table->foreign(['organization_id', 'visit_id'])->references(['organization_id', 'id'])->on('visits')->onDelete('cascade');
            $table->foreign(['organization_id', 'template_id'])->references(['organization_id', 'id'])->on('badge_templates')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('badges');
    }
};
