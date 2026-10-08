<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('approvals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained('organizations')->onDelete('cascade');
            $table->foreignId('visit_id')->constrained('visits')->onDelete('cascade');
            $table->foreignId('approver_user_id')->constrained('users')->onDelete('cascade');
            $table->string('status', 40)->default('pending');
            $table->timestampTz('decision_at')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->text('comments')->nullable();

            $table->foreign(['organization_id', 'visit_id'])->references(['organization_id', 'id'])->on('visits')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('approvals');
    }
};
