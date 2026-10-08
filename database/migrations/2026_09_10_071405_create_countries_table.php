<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('countries', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->char('iso_code', 2)->unique();
            $table->string('isd_code', 8);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('countries');
    }
};
