<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kiosk_devices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained('organizations')->onDelete('cascade');
            $table->foreignId('site_id')->constrained('sites')->onDelete('cascade');
            $table->string('name');
            $table->string('device_identifier');
            $table->string('status', 40)->default('online');
            $table->timestampTz('last_seen_at')->nullable();
            $table->boolean('photo_capture')->default(true);
            $table->boolean('qr_scanning')->default(true);
            $table->boolean('id_scanning')->default(false);
            $table->boolean('auto_print_badge')->default(true);
            $table->boolean('require_agreement')->default(true);
            $table->integer('idle_timeout')->default(60);
            $table->string('language', 10)->default('en');
            $table->jsonb('printer_settings')->nullable();

            $table->unique(['organization_id', 'device_identifier']);
            $table->unique(['organization_id', 'id']);
            $table->foreign(['organization_id', 'site_id'])->references(['organization_id', 'id'])->on('sites')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kiosk_devices');
    }
};
