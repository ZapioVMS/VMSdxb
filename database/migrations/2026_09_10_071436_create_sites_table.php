<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sites', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained('organizations')->onDelete('cascade');
            $table->string('name');
            $table->string('name_ar')->nullable();
            $table->string('city')->nullable();
            $table->string('code');
            $table->text('address')->nullable();
            $table->string('timezone', 64)->default('Asia/Dubai');
            $table->integer('capacity')->nullable();
            $table->jsonb('visiting_hours')->nullable();
            $table->string('status', 40)->default('active');
            $table->timestampsTz();

            $table->unique(['organization_id', 'code']);
            $table->unique(['organization_id', 'id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sites');
    }
};
