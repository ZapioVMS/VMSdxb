<?php
$file = "resources/views/settings/index.blade.php";
$c = file_get_contents($file);

$old = <<<EOF
                        <div class="grid grid-2">
                            <!-- Guest Arrival Alerts -->
                            <div class="card">
                                <div class="section-title">Visitor Arrival Alerts</div>
                                <div class="cell-sub">Triggered the moment a guest checks in at reception or kiosk.</div>
                                <div class="switchrow"><div><div class="sr-label">Email Notification</div></div><label class="switch"><input type="checkbox" name="arrival_email" value="1" {{ !empty(\$hostAlerts['arrival']['email']) ? 'checked' : '' }}><span class="track"><span class="thumb"></span></span></label></div>
                                <div class="switchrow"><div><div class="sr-label">Push Notification</div></div><label class="switch"><input type="checkbox" name="arrival_push" value="1" {{ !empty(\$hostAlerts['arrival']['push']) ? 'checked' : '' }}><span class="track"><span class="thumb"></span></span></label></div>
                                <div class="switchrow"><div><div class="sr-label">SMS Alert to Host Mobile</div></div><label class="switch"><input type="checkbox" name="arrival_sms" value="1" {{ !empty(\$hostAlerts['arrival']['sms']) ? 'checked' : '' }}><span class="track"><span class="thumb"></span></span></label></div>
                                <div class="switchrow"><div><div class="sr-label">Desktop Browser Popup</div></div><label class="switch"><input type="checkbox" name="arrival_desktop" value="1" {{ !empty(\$hostAlerts['arrival']['desktop']) ? 'checked' : '' }}><span class="track"><span class="thumb"></span></span></label></div>
                                <div class="field" style="margin-top: 12px;">
                                    <label>Reminder if Host Does Not Acknowledge</label>
                                    <select name="arrival_reminder_minutes">
                                        <option value="0" {{ (\$hostAlerts['arrival']['reminder_minutes'] ?? '') == '0' ? 'selected' : '' }}>Do not repeat</option>
                                        <option value="5" {{ (\$hostAlerts['arrival']['reminder_minutes'] ?? '') == '5' ? 'selected' : '' }}>Repeat after 5 minutes</option>
                                        <option value="10" {{ (\$hostAlerts['arrival']['reminder_minutes'] ?? '') == '10' ? 'selected' : '' }}>Repeat after 10 minutes</option>
                                    </select>
                                </div>
                            </div>

                            <!-- Pre-Registration Reminders -->
                            <div class="card">
                                <div class="section-title">Pre-Registration Reminders</div>
                                <div class="cell-sub">Upcoming visit schedules and morning agendas.</div>
                                <div class="switchrow"><div><div class="sr-label">Notify on Invite Creation</div></div><label class="switch"><input type="checkbox" name="prereg_notify" value="1" {{ !empty(\$hostAlerts['pre_reg']['notify']) ? 'checked' : '' }}><span class="track"><span class="thumb"></span></span></label></div>
                                <div class="switchrow"><div><div class="sr-label">Morning Briefing Email</div></div><label class="switch"><input type="checkbox" name="prereg_briefing" value="1" {{ !empty(\$hostAlerts['pre_reg']['morning_briefing']) ? 'checked' : '' }}><span class="track"><span class="thumb"></span></span></label></div>
                                <div class="switchrow"><div><div class="sr-label">15-Minute Pre-Arrival Alert</div></div><label class="switch"><input type="checkbox" name="prereg_reminder_15" value="1" {{ !empty(\$hostAlerts['pre_reg']['reminder_15']) ? 'checked' : '' }}><span class="track"><span class="thumb"></span></span></label></div>
                            </div>

                            <!-- Escalation Rules -->
                            <div class="card">
                                <div class="section-title">Unattended Visitor Escalation</div>
                                <div class="cell-sub">Action when host does not receive or acknowledge visitor</div>
                                <div class="field">
                                    <label>Escalate To</label>
                                    <select name="escalation_backup_host">
                                        <option value="Department Head">Department Head</option>
                                        <option value="Front Desk">Front Desk Receptionist</option>
                                        <option value="Security Team">Security Operations Center</option>
                                    </select>
                                </div>
                                <div class="field">
                                    <label>Escalate After</label>
                                    <select name="escalate_after_minutes">
                                        <option value="5">5 minutes</option>
                                        <option value="10" selected>10 minutes</option>
                                        <option value="15">15 minutes</option>
                                    </select>
                                </div>
                            </div>

                            <!-- Checkout & Overstay -->
                            <div class="card">
                                <div class="section-title">Checkout & Overstay Rules</div>
                                <div class="cell-sub">Automatic duration monitoring</div>
                                <div class="switchrow"><div><div class="sr-label">Notify Host on Checkout</div></div><label class="switch"><input type="checkbox" name="checkout_notify" value="1" {{ !empty(\$hostAlerts['checkout']['notify_on_checkout']) ? 'checked' : '' }}><span class="track"><span class="thumb"></span></span></label></div>
                                <div class="switchrow"><div><div class="sr-label">Alert Security on Overstay</div></div><label class="switch"><input type="checkbox" name="checkout_overstay_alert" value="1" {{ !empty(\$hostAlerts['checkout']['alert_on_overstay']) ? 'checked' : '' }}><span class="track"><span class="thumb"></span></span></label></div>
                                <div class="field" style="margin-top: 12px;">
                                    <label>Overstay Grace Period</label>
                                    <select name="checkout_overstay_minutes">
                                        <option value="30">30 minutes past scheduled end</option>
                                        <option value="60" selected>60 minutes past scheduled end</option>
                                        <option value="120">120 minutes past scheduled end</option>
                                    </select>
                                </div>
                            </div>
                        </div>
EOF;

$new = <<<EOF
                        <div class="grid grid-2">
                            <!-- Guest Arrival Alerts -->
                            <div class="card">
                                <div class="section-title">Visitor Arrival Alerts</div>
                                <div class="cell-sub">Notify hosts the moment their visitor checks in.</div>
                                <div class="switchrow"><div><div class="sr-label">Email notification</div></div><label class="switch"><input type="checkbox" name="arrival_email" value="1" {{ !empty(\$hostAlerts['arrival']['email']) ? 'checked' : '' }}><span class="track"><span class="thumb"></span></span></label></div>
                                <div class="switchrow"><div><div class="sr-label">Push notification</div></div><label class="switch"><input type="checkbox" name="arrival_push" value="1" {{ !empty(\$hostAlerts['arrival']['push']) ? 'checked' : '' }}><span class="track"><span class="thumb"></span></span></label></div>
                                <div class="switchrow"><div><div class="sr-label">SMS notification</div></div><label class="switch"><input type="checkbox" name="arrival_sms" value="1" {{ !empty(\$hostAlerts['arrival']['sms']) ? 'checked' : '' }}><span class="track"><span class="thumb"></span></span></label></div>
                                <div class="switchrow"><div><div class="sr-label">Desktop notification</div></div><label class="switch"><input type="checkbox" name="arrival_desktop" value="1" {{ !empty(\$hostAlerts['arrival']['desktop']) ? 'checked' : '' }}><span class="track"><span class="thumb"></span></span></label></div>
                                <div class="field" style="margin-top: 12px;">
                                    <label>Send reminder if no response</label>
                                    <select name="arrival_reminder_minutes">
                                        <option value="0" {{ (\$hostAlerts['arrival']['reminder_minutes'] ?? '') == '0' ? 'selected' : '' }}>Do not repeat</option>
                                        <option value="5" {{ (\$hostAlerts['arrival']['reminder_minutes'] ?? '') == '5' ? 'selected' : '' }}>After 5 minutes</option>
                                        <option value="10" {{ (\$hostAlerts['arrival']['reminder_minutes'] ?? '') == '10' ? 'selected' : '' }}>After 10 minutes</option>
                                    </select>
                                </div>
                            </div>

                            <!-- Pre-Registration Reminders -->
                            <div class="card">
                                <div class="section-title">Pre-registration Alerts</div>
                                <div class="cell-sub">Keep hosts and front desk informed ahead of scheduled visits.</div>
                                <div class="switchrow"><div><div class="sr-label">Notify host on new pre-registration</div></div><label class="switch"><input type="checkbox" name="prereg_notify" value="1" {{ !empty(\$hostAlerts['pre_reg']['notify']) ? 'checked' : '' }}><span class="track"><span class="thumb"></span></span></label></div>
                                <div class="switchrow"><div><div class="sr-label">Send daily morning briefing to front desk</div></div><label class="switch"><input type="checkbox" name="prereg_briefing" value="1" {{ !empty(\$hostAlerts['pre_reg']['morning_briefing']) ? 'checked' : '' }}><span class="track"><span class="thumb"></span></span></label></div>
                                <div class="switchrow"><div><div class="sr-label">Remind visitor 15 minutes before visit</div></div><label class="switch"><input type="checkbox" name="prereg_reminder_15" value="1" {{ !empty(\$hostAlerts['pre_reg']['reminder_15']) ? 'checked' : '' }}><span class="track"><span class="thumb"></span></span></label></div>
                                <div class="field" style="margin-top: 12px;">
                                    <label>Morning briefing time</label>
                                    <select name="prereg_briefing_time">
                                        <option value="07:00 AM" {{ (\$hostAlerts['pre_reg']['briefing_time'] ?? '') == '07:00 AM' ? 'selected' : '' }}>07:00 AM</option>
                                        <option value="08:00 AM" {{ (\$hostAlerts['pre_reg']['briefing_time'] ?? '08:00 AM') == '08:00 AM' ? 'selected' : '' }}>08:00 AM</option>
                                        <option value="09:00 AM" {{ (\$hostAlerts['pre_reg']['briefing_time'] ?? '') == '09:00 AM' ? 'selected' : '' }}>09:00 AM</option>
                                    </select>
                                </div>
                            </div>

                            <!-- Escalation Rules -->
                            <div class="card">
                                <div class="section-title">Escalation Rules</div>
                                <div class="cell-sub">Make sure unattended visitors are never left waiting.</div>
                                <div class="field">
                                    <label>Escalate to</label>
                                    <select name="escalation_backup_host">
                                        <option value="Department Head">Department Head</option>
                                        <option value="Front Desk">Front Desk Receptionist</option>
                                        <option value="Security Team">Security Operations Center</option>
                                    </select>
                                </div>
                                <div class="field">
                                    <label>Escalate after</label>
                                    <select name="escalate_after_minutes">
                                        <option value="5">5 minutes</option>
                                        <option value="10" selected>10 minutes</option>
                                        <option value="15">15 minutes</option>
                                    </select>
                                </div>
                                <div class="switchrow" style="margin-top: 16px;"><div><div class="sr-label">Auto-approve pre-registered visitors on arrival</div></div><label class="switch"><input type="checkbox" name="escalation_auto_approve" value="1" {{ !empty(\$hostAlerts['escalation']['auto_approve']) ? 'checked' : '' }}><span class="track"><span class="thumb"></span></span></label></div>
                            </div>

                            <!-- Checkout & Overstay -->
                            <div class="card">
                                <div class="section-title">Checkout & Overstay</div>
                                <div class="cell-sub">Track visitors who stay longer than expected.</div>
                                <div class="switchrow"><div><div class="sr-label">Notify host when visitor checks out</div></div><label class="switch"><input type="checkbox" name="checkout_notify" value="1" {{ !empty(\$hostAlerts['checkout']['notify_on_checkout']) ? 'checked' : '' }}><span class="track"><span class="thumb"></span></span></label></div>
                                <div class="switchrow"><div><div class="sr-label">Alert if visitor overstays</div></div><label class="switch"><input type="checkbox" name="checkout_overstay_alert" value="1" {{ !empty(\$hostAlerts['checkout']['alert_on_overstay']) ? 'checked' : '' }}><span class="track"><span class="thumb"></span></span></label></div>
                                <div class="field" style="margin-top: 12px;">
                                    <label>Overstay threshold</label>
                                    <select name="checkout_overstay_minutes">
                                        <option value="30">30 minutes</option>
                                        <option value="60" selected>60 minutes</option>
                                        <option value="120">120 minutes</option>
                                    </select>
                                </div>
                            </div>
                        </div>
EOF;

\$c = str_replace(\$old, \$new, \$c);
file_put_contents(\$file, \$c);
echo "done";
?>
