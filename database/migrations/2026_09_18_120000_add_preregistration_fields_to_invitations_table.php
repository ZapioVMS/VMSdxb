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
        Schema::table('invitations', function (Blueprint $table) {
            $table->string('recurring', 50)->default('none')->nullable()->after('status');
            $table->string('meeting_room')->nullable()->after('recurring');
            $table->integer('duration')->nullable()->default(60)->after('meeting_room');
            $table->boolean('send_confirmation_email')->default(true)->after('duration');
            $table->boolean('notify_host_on_arrival')->default(true)->after('send_confirmation_email');
            $table->boolean('send_qr_self_checkin')->default(true)->after('notify_host_on_arrival');
            $table->boolean('requires_nda')->default(false)->after('send_qr_self_checkin');
            $table->boolean('wifi_guest_access')->default(false)->after('requires_nda');
            $table->boolean('parking_required')->default(false)->after('wifi_guest_access');
            $table->boolean('escort_required')->default(false)->after('parking_required');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('invitations', function (Blueprint $table) {
            $table->dropColumn([
                'recurring',
                'meeting_room',
                'duration',
                'send_confirmation_email',
                'notify_host_on_arrival',
                'send_qr_self_checkin',
                'requires_nda',
                'wifi_guest_access',
                'parking_required',
                'escort_required',
            ]);
        });
    }
};
