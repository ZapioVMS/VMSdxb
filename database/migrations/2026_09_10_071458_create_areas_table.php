<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('areas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained('organizations')->onDelete('cascade');
            $table->foreignId('floor_id')->constrained('floors')->onDelete('cascade');
            $table->string('name');
            $table->string('code')->nullable();
            $table->integer('capacity')->nullable();
            $table->string('status', 40)->default('active');

            $table->unique(['organization_id', 'id']);
            $table->foreign(['organization_id', 'floor_id'])->references(['organization_id', 'id'])->on('floors')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('areas');
    }
};
