<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('visitors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained('organizations')->onDelete('cascade');
            $table->string('first_name');
            $table->string('last_name')->nullable();
            $table->string('email', 254)->nullable();
            $table->string('phone', 32)->nullable();
            $table->foreignId('nationality_id')->nullable()->constrained('countries')->onDelete('set null');
            $table->foreignId('id_type_id')->nullable()->constrained('id_types')->onDelete('set null');
            $table->text('id_number')->nullable();
            $table->string('company_name')->nullable();
            $table->string('photo_path')->nullable();
            $table->string('vehicle_plate')->nullable();
            $table->timestampsTz();

            $table->unique(['organization_id', 'id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('visitors');
    }
};
