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
        // 1. Department Head (deferred until employees table exists)
        Schema::table('departments', function (Blueprint $table) {
            $table->foreign(['organization_id', 'head_employee_id'])
                  ->references(['organization_id', 'id'])
                  ->on('employees')
                  ->onDelete('restrict');
        });

        // 2. Employee Manager Self-Reference
        Schema::table('employees', function (Blueprint $table) {
            $table->foreign(['organization_id', 'manager_id'])
                  ->references(['organization_id', 'id'])
                  ->on('employees')
                  ->onDelete('restrict');
        });

        // 3. Attendance Record Kiosk Device
        Schema::table('attendance_records', function (Blueprint $table) {
            $table->foreign(['organization_id', 'kiosk_device_id'])
                  ->references(['organization_id', 'id'])
                  ->on('kiosk_devices')
                  ->onDelete('restrict');
        });

        // 4. Kiosk Check-In Events (Employee & Visit references)
        Schema::table('kiosk_checkin_events', function (Blueprint $table) {
            $table->foreign(['organization_id', 'employee_id'])
                  ->references(['organization_id', 'id'])
                  ->on('employees')
                  ->onDelete('restrict');

            $table->foreign(['organization_id', 'visit_id'])
                  ->references(['organization_id', 'id'])
                  ->on('visits')
                  ->onDelete('restrict');
        });

        // 5. Evacuation Records (Employee & Visit references)
        Schema::table('evacuation_records', function (Blueprint $table) {
            $table->foreign(['organization_id', 'employee_id'])
                  ->references(['organization_id', 'id'])
                  ->on('employees')
                  ->onDelete('restrict');

            $table->foreign(['organization_id', 'visit_id'])
                  ->references(['organization_id', 'id'])
                  ->on('visits')
                  ->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('evacuation_records', function (Blueprint $table) {
            $table->dropForeign(['organization_id', 'visit_id']);
            $table->dropForeign(['organization_id', 'employee_id']);
        });

        Schema::table('kiosk_checkin_events', function (Blueprint $table) {
            $table->dropForeign(['organization_id', 'visit_id']);
            $table->dropForeign(['organization_id', 'employee_id']);
        });

        Schema::table('attendance_records', function (Blueprint $table) {
            $table->dropForeign(['organization_id', 'kiosk_device_id']);
        });

        Schema::table('employees', function (Blueprint $table) {
            $table->dropForeign(['organization_id', 'manager_id']);
        });

        Schema::table('departments', function (Blueprint $table) {
            $table->dropForeign(['organization_id', 'head_employee_id']);
        });
    }
};
