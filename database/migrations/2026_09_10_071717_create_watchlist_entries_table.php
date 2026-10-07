<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('watchlist_entries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained('organizations')->onDelete('cascade');
            $table->string('name');
            $table->foreignId('id_type_id')->nullable()->constrained('id_types')->onDelete('set null');
            $table->string('id_number_hash', 64)->nullable();
            $table->text('reason');
            $table->string('severity', 40)->default('serious');
            $table->string('status', 40)->default('active');

            $table->unique(['organization_id', 'id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('watchlist_entries');
    }
};
