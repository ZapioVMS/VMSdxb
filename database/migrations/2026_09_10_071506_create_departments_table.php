<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('departments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained('organizations')->onDelete('cascade');
            $table->string('name');
            $table->string('code');
            $table->text('description')->nullable();
            $table->bigInteger('head_employee_id')->nullable();
            $table->string('contact_email', 254)->nullable();
            $table->string('contact_phone', 32)->nullable();
            $table->boolean('kiosk_selectable')->default(true);
            $table->boolean('require_nda')->default(false);
            $table->boolean('escort_required')->default(false);
            $table->string('status', 40)->default('active');

            $table->unique(['organization_id', 'code']);
            $table->unique(['organization_id', 'id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('departments');
    }
};
PHP;
