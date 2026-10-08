<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('organizations', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('trade_license_no')->nullable();
            $table->string('timezone', 64)->default('Asia/Dubai');
            $table->string('default_language', 10)->default('en');
            $table->string('status', 40)->default('active');
            $table->timestampsTz();

            $table->unique(['id', 'name']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('organizations');
    }
};
