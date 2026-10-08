<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notification_templates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained('organizations')->onDelete('cascade');
            $table->string('name');
            $table->string('event');
            $table->string('channel', 40);
            $table->string('language', 10)->default('en');
            $table->text('subject')->nullable();
            $table->text('body');
            $table->jsonb('variables_json')->nullable();
            $table->string('status', 40)->default('active');

            $table->unique(['organization_id', 'id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notification_templates');
    }
};
