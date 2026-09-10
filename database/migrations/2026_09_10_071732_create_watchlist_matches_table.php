<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('watchlist_matches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained('organizations')->onDelete('cascade');
            $table->foreignId('visit_id')->constrained('visits')->onDelete('cascade');
            $table->foreignId('watchlist_entry_id')->constrained('watchlist_entries')->onDelete('cascade');
            $table->string('match_type', 40)->default('fuzzy_name');
            $table->decimal('score', 5, 4)->nullable();
            $table->foreignId('verified_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestampTz('verified_at')->nullable();
            $table->string('decision', 40)->nullable();

            $table->foreign(['organization_id', 'visit_id'])->references(['organization_id', 'id'])->on('visits')->onDelete('cascade');
            $table->foreign(['organization_id', 'watchlist_entry_id'])->references(['organization_id', 'id'])->on('watchlist_entries')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('watchlist_matches');
    }
};
