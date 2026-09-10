<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('visitor_types', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained('organizations')->onDelete('cascade');
            $table->string('name');
            $table->boolean('requires_approval')->default(false);
            $table->boolean('requires_nda')->default(false);
            $table->boolean('requires_escort')->default(false);
            $table->string('status', 40)->default('active');

            $table->unique(['organization_id', 'name']);
            $table->unique(['organization_id', 'id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('visitor_types');
    }
};
