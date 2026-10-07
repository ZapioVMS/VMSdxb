<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('buildings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained('organizations')->onDelete('cascade');
            $table->foreignId('site_id')->constrained('sites')->onDelete('cascade');
            $table->string('name');
            $table->string('code')->nullable();
            $table->string('status', 40)->default('active');
            $table->timestampsTz();

            $table->unique(['organization_id', 'id']);
            $table->foreign(['organization_id', 'site_id'])->references(['organization_id', 'id'])->on('sites')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('buildings');
    }
};
