<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('deliveries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained()->cascadeOnDelete();
            $table->foreignId('site_id')->nullable()->constrained()->nullOnDelete();
            $table->string('delivery_code', 30)->unique()->nullable();
            $table->string('carrier', 100);
            $table->string('tracking_number', 100)->nullable();
            $table->string('size', 100)->default('Small Box');
            $table->string('description', 255);
            $table->foreignId('recipient_employee_id')->nullable()->constrained('employees')->nullOnDelete();
            $table->string('recipient_name', 150);
            $table->string('recipient_email', 150)->nullable();
            $table->string('department', 100)->nullable();
            $table->dateTime('received_at');
            $table->string('received_by', 150)->nullable();
            $table->string('status', 50)->default('awaiting-pickup'); // awaiting-pickup, collected
            $table->dateTime('collected_at')->nullable();
            $table->string('collected_by', 150)->nullable();
            $table->dateTime('notified_at')->nullable();
            $table->boolean('notify_recipient')->default(true);
            $table->text('notes')->nullable();
            $table->text('photo_path')->nullable();
            $table->timestamps();

            $table->index(['organization_id', 'status']);
            $table->index(['site_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('deliveries');
    }
};
