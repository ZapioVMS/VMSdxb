<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('gate_passes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('organization_id')->nullable()->index();
            $table->unsignedBigInteger('site_id')->nullable()->index();
            $table->string('pass_code')->unique();
            $table->string('type')->default('visitor'); // 'visitor' or 'delivery'
            $table->string('office_unit'); // Office / Unit to visit

            // Visitor specific fields
            $table->string('visitor_name')->nullable();
            $table->string('company_plate')->nullable();
            $table->string('purpose')->nullable();
            $table->string('id_type')->nullable();
            $table->string('id_number')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->date('expected_date')->nullable();
            $table->string('expected_duration')->nullable();

            // Delivery specific fields
            $table->string('carrier')->nullable();
            $table->string('package_size')->nullable();
            $table->string('tracking_number')->nullable();
            $table->string('description')->nullable();

            // Common fields
            $table->text('notes')->nullable();
            $table->string('status')->default('active'); // 'active', 'redeemed', 'cancelled'
            $table->timestamp('redeemed_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->timestamps();

            // Foreign keys if tables exist
            if (Schema::hasTable('organizations')) {
                $table->foreign('organization_id')->references('id')->on('organizations')->onDelete('cascade');
            }
            if (Schema::hasTable('sites')) {
                $table->foreign('site_id')->references('id')->on('sites')->onDelete('set null');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gate_passes');
    }
};
