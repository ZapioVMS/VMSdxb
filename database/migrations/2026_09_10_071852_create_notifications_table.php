<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained('organizations')->onDelete('cascade');
            $table->bigInteger('visit_id')->nullable();
            $table->string('recipient_type', 40);
            $table->string('channel', 40);
            $table->bigInteger('template_id')->nullable();
            $table->string('status', 40)->default('queued');
            $table->timestampTz('sent_at')->nullable();
            $table->string('provider_message_id')->nullable();

            $table->foreign(['organization_id', 'visit_id'])->references(['organization_id', 'id'])->on('visits')->onDelete('set null');
            $table->foreign(['organization_id', 'template_id'])->references(['organization_id', 'id'])->on('notification_templates')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
