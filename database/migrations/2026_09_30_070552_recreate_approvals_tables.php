<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('approvals');

        Schema::create('approval_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained('organizations')->onDelete('cascade');
            $table->foreignId('branch_id')->nullable()->constrained('sites')->onDelete('set null');
            
            $table->string('request_type');
            
            $table->morphs('approvable');
            
            $table->foreignId('requester_user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('requested_for_user_id')->nullable()->constrained('users')->onDelete('set null');
            
            $table->string('status')->default('pending');
            
            $table->timestamp('submitted_at')->nullable();
            
            $table->foreignId('decided_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('decided_at')->nullable();
            $table->text('decision_reason')->nullable();
            
            $table->integer('version')->default(1);
            $table->timestamps();
        });

        Schema::create('approval_actions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('approval_request_id')->constrained('approval_requests')->onDelete('cascade');
            $table->foreignId('actor_user_id')->nullable()->constrained('users')->onDelete('set null');
            
            $table->string('action_type');
            $table->text('reason')->nullable();
            
            $table->string('previous_status')->nullable();
            $table->string('new_status');
            
            $table->timestamp('decision_time')->useCurrent();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('approval_actions');
        Schema::dropIfExists('approval_requests');

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
};
