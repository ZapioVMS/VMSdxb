<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kiosk_checkin_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained('organizations')->onDelete('cascade');
            $table->foreignId('kiosk_device_id')->constrained('kiosk_devices')->onDelete('cascade');
            $table->bigInteger('employee_id')->nullable();
            $table->bigInteger('visit_id')->nullable();
            $table->string('event_type', 40);
            $table->string('result', 40)->default('success');
            $table->text('reason')->nullable();
            $table->timestampTz('created_at');

            $table->foreign(['organization_id', 'kiosk_device_id'])->references(['organization_id', 'id'])->on('kiosk_devices')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kiosk_checkin_events');
    }
};
