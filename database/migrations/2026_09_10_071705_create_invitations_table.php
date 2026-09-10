<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('invitations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained('organizations')->onDelete('cascade');
            $table->foreignId('visit_id')->constrained('visits')->onDelete('cascade');
            $table->string('access_code')->nullable();
            $table->string('qr_token_hash', 64)->nullable();
            $table->integer('expected_visitor_count')->default(1);
            $table->text('items_to_bring')->nullable();
            $table->text('special_instructions')->nullable();
            $table->timestampTz('sent_at')->nullable();
            $table->timestampTz('expires_at');
            $table->string('status', 40)->default('pending');

            $table->foreign(['organization_id', 'visit_id'])->references(['organization_id', 'id'])->on('visits')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invitations');
    }
};
