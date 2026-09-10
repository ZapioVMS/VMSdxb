<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('agreements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained('organizations')->onDelete('cascade');
            $table->string('name');
            $table->string('version')->default('1.0');
            $table->bigInteger('visitor_type_id')->nullable();
            $table->timestampTz('valid_from');
            $table->timestampTz('valid_until')->nullable();
            $table->string('status', 40)->default('active');

            $table->unique(['organization_id', 'id']);
            $table->foreign(['organization_id', 'visitor_type_id'])->references(['organization_id', 'id'])->on('visitor_types')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('agreements');
    }
};
