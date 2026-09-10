<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('visit_agreements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained('organizations')->onDelete('cascade');
            $table->foreignId('visit_id')->constrained('visits')->onDelete('cascade');
            $table->foreignId('agreement_id')->constrained('agreements')->onDelete('cascade');
            $table->timestampTz('accepted_at');
            $table->string('signature_type', 40)->default('draw');
            $table->text('signature_data')->nullable();
            $table->foreignId('witness_user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->timestampTz('email_copy_sent_at')->nullable();

            $table->foreign(['organization_id', 'visit_id'])->references(['organization_id', 'id'])->on('visits')->onDelete('cascade');
            $table->foreign(['organization_id', 'agreement_id'])->references(['organization_id', 'id'])->on('agreements')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('visit_agreements');
    }
};
