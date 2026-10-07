<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('integration_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained('organizations')->onDelete('cascade');
            $table->string('type');
            $table->text('config_encrypted');
            $table->string('status', 40)->default('connected');
            $table->timestampsTz();

            $table->unique(['organization_id', 'type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('integration_settings');
    }
};
