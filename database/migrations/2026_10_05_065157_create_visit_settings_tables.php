<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('visit_notification_settings', function (Blueprint $table) {
            $table->id();

            $table->foreignId('organization_id')
                ->unique()
                ->constrained('organizations')
                ->cascadeOnDelete();

            // Arrival notification channels
            $table->boolean('arrival_email_enabled')->default(true);
            $table->boolean('arrival_push_enabled')->default(false);
            $table->boolean('arrival_sms_enabled')->default(false);
            $table->boolean('arrival_desktop_enabled')->default(false);

            // NULL = disabled; otherwise minutes after initial arrival alert.
            $table->smallInteger('arrival_reminder_minutes')->nullable();

            // Pre-registration notifications
            $table->boolean('prereg_host_notification_enabled')->default(true);

            $table->boolean('morning_briefing_enabled')->default(false);
            $table->time('morning_briefing_time')->nullable();

            $table->boolean('prereg_visitor_reminder_enabled')->default(false);
            $table->smallInteger('prereg_visitor_reminder_minutes')->default(15);

            // Escalation when the host has not responded
            $table->boolean('escalation_enabled')->default(false);
            $table->string('escalation_recipient_code', 40)
                ->default('department_head');

            $table->smallInteger('escalation_after_minutes')->default(10);

            // Checkout / overstay
            $table->boolean('checkout_notification_enabled')->default(false);
            $table->boolean('overstay_alert_enabled')->default(false);
            $table->smallInteger('overstay_threshold_minutes')->default(60);

            $table->timestamps();
        });

        DB::statement("
            ALTER TABLE visit_notification_settings
            ADD CONSTRAINT vns_recipient_check CHECK (
                escalation_recipient_code IN (
                    'department_head',
                    'front_desk',
                    'security_team',
                    'administrator'
                )
            ),
            ADD CONSTRAINT vns_minutes_check CHECK (
                (arrival_reminder_minutes IS NULL
                    OR arrival_reminder_minutes BETWEEN 1 AND 1440)
                AND prereg_visitor_reminder_minutes BETWEEN 1 AND 10080
                AND escalation_after_minutes BETWEEN 1 AND 1440
                AND overstay_threshold_minutes BETWEEN 1 AND 10080
            ),
            ADD CONSTRAINT vns_briefing_check CHECK (
                NOT morning_briefing_enabled
                OR morning_briefing_time IS NOT NULL
            )
        ");

        Schema::create('visit_approval_settings', function (Blueprint $table) {
            $table->id();

            $table->foreignId('organization_id')
                ->unique()
                ->constrained('organizations')
                ->cascadeOnDelete();

            // Approval of the advance booking itself
            // none | host | administrator
            $table->string('preregistration_approval_mode', 30)
                ->default('none');

            // Admission of walk-ins
            // none | host | administrator
            $table->string('walkin_approval_mode', 30)
                ->default('host');

            // Admission of an already approved/approval-exempt preregistration
            // automatic | host | administrator
            $table->string('preregistered_arrival_approval_mode', 30)
                ->default('automatic');

            $table->timestamps();
        });

        DB::statement("
            ALTER TABLE visit_approval_settings
            ADD CONSTRAINT vas_prereg_check CHECK (
                preregistration_approval_mode IN (
                    'none', 'host', 'administrator'
                )
            ),
            ADD CONSTRAINT vas_walkin_check CHECK (
                walkin_approval_mode IN (
                    'none', 'host', 'administrator'
                )
            ),
            ADD CONSTRAINT vas_arrival_check CHECK (
                preregistered_arrival_approval_mode IN (
                    'automatic', 'host', 'administrator'
                )
            )
        ");

        DB::statement("
            ALTER TABLE organization_settings
            ALTER COLUMN value TYPE jsonb
            USING value::jsonb
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("
            ALTER TABLE organization_settings
            ALTER COLUMN value DROP DEFAULT
        ");

        DB::statement("
            ALTER TABLE organization_settings
            ALTER COLUMN value TYPE text
            USING value::text
        ");

        Schema::dropIfExists('visit_approval_settings');
        Schema::dropIfExists('visit_notification_settings');
    }
};
