<?php

namespace App\Http\Controllers;

use App\Models\Area;
use App\Models\Building;
use App\Models\DeliveryCarrier;
use App\Models\Department;
use App\Models\Employee;
use App\Models\Floor;
use App\Models\IdType;
use App\Models\KioskDevice;
use App\Models\MeetingRoom;
use App\Models\Organization;
use App\Models\OrganizationSetting;
use App\Models\VisitNotificationSetting;
use App\Models\VisitApprovalSetting;
use App\Models\PackageSize;
use App\Models\Scopes\TenantScope;
use App\Models\Shift;
use App\Models\Site;
use App\Models\VisitorType;
use App\Models\VisitPurpose;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class SettingsController extends Controller
{
    /**
     * Display the unified settings page with 17 tabs.
     */
    public function index(Request $request): View
    {
        $orgId = TenantScope::getTenantId();
        $org = Organization::find($orgId) ?? Organization::first();

        // Load organization settings
        $settingsRaw = OrganizationSetting::where('organization_id', $orgId ?? 1)
            ->pluck('value', 'key')
            ->toArray();

        // Defaults for general
        $general = $settingsRaw['general'] ?? [
            'org_name' => $org ? $org->name : 'Meridian Business Solutions LLC',
            'trade_license_no' => $org ? $org->trade_license_no : 'CN-1487293',
            'timezone' => $org ? $org->timezone : 'Asia/Dubai',
            'date_format' => 'MMM DD, YYYY',
            'time_format' => '12h',
            'default_language' => $org ? $org->default_language : 'en',
            'business_type' => 'employees',
            'business_hours_start' => '08:00',
            'business_hours_end' => '18:00',
            'working_days' => ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday'],
            'default_visit_duration' => '60',
            'after_hours_checkin' => true,
            'auto_checkout_eod' => true,
        ];

        // Defaults for branding
        $branding = $settingsRaw['branding'] ?? [
            'company_name' => $org ? $org->name : 'Zapio VMS',
            'display_name' => 'Zapio Reception Kiosk',
            'primary_color' => '#2E9E5B',
            'logo_url' => asset('images/logo.png'),
        ];

        // Defaults for kiosk
        $kiosk = $settingsRaw['kiosk'] ?? [
            'photo_capture' => true,
            'qr_scanning' => true,
            'id_scanning' => true,
            'auto_print_badge' => true,
            'require_agreement' => true,
            'idle_timeout' => true,
            'idle_timeout_seconds' => '60',
            'language' => 'en',
            'badge_printer' => 'Brother QL-820NWB',
            'checkin_fields' => ['name', 'company', 'phone', 'email', 'idDoc'],
        ];

        // Defaults for security
        $security = $settingsRaw['security'] ?? [
            'watchlist_screening' => true,
            'id_verification_required' => true,
            'host_approval_required' => false,
            'badge_mandatory' => true,
            'two_factor_admin' => false,
            'session_timeout_minutes' => '30',
        ];

        // Defaults for notifications
        $notifications = $settingsRaw['notifications'] ?? [
            'admin_email' => 'allanjacob03@gmail.com',
            'sms_gateway' => 'Twilio',
            'daily_summary' => true,
            'watchlist_alert' => true,
            'system_health_alerts' => true,
        ];

        // Defaults for privacy
        $privacy = $settingsRaw['privacy'] ?? [
            'retention_days' => '90',
            'photo_retention_days' => '30',
            'privacy_notice_on_kiosk' => true,
            'anonymize_old_records' => true,
            'allow_data_requests' => true,
        ];

        // Defaults for host alerts
        $vns = VisitNotificationSetting::firstOrCreate(['organization_id' => $orgId]);
        $vas = VisitApprovalSetting::firstOrCreate(['organization_id' => $orgId]);

        // Meeting Rooms
        if (Schema::hasTable('meeting_rooms')) {
            $meetingRooms = MeetingRoom::with(['site', 'floor'])->orderBy('name')->get()->map(fn($r) => [
                'id' => $r->id,
                'name' => $r->name,
                'site_id' => $r->site_id,
                'site_name' => $r->site?->name ?? 'Main Office',
                'site_code' => $r->site?->code ?? '',
                'floor_id' => $r->floor_id,
                'floor_name' => $r->floor?->name ?? '',
                'capacity' => $r->capacity ?? 8,
                'bookings' => \App\Models\Invitation::where('meeting_room', $r->name)->count(),
            ])->toArray();
        } else {
            $meetingRooms = $settingsRaw['meeting_rooms'] ?? [
                ['id' => 'room_1', 'name' => 'Boardroom Alpha', 'site_name' => 'Business Bay Tower', 'site_code' => 'BBT', 'floor_id' => null, 'floor_name' => '', 'capacity' => 20, 'bookings' => 0],
                ['id' => 'room_2', 'name' => 'Conference Room 4B', 'site_name' => 'Business Bay Tower', 'site_code' => 'BBT', 'floor_id' => null, 'floor_name' => '', 'capacity' => 10, 'bookings' => 0],
                ['id' => 'room_3', 'name' => 'Meeting Room 2A', 'site_name' => 'Business Bay Tower', 'site_code' => 'BBT', 'floor_id' => null, 'floor_name' => '', 'capacity' => 6, 'bookings' => 0],
                ['id' => 'room_4', 'name' => 'Executive Suite C', 'site_name' => 'DIFC Gate Village', 'site_code' => 'DIFC', 'floor_id' => null, 'floor_name' => '', 'capacity' => 8, 'bookings' => 0],
            ];
        }

        // Delivery Carriers
        if (Schema::hasTable('delivery_carriers')) {
            $deliveryCarriers = DeliveryCarrier::orderBy('name')->get()->map(fn($c) => [
                'id' => $c->id,
                'name' => $c->name,
                'code' => $c->code ?? strtoupper(substr($c->name, 0, 3)),
                'deliveries' => 0,
            ])->toArray();
        } else {
            $deliveryCarriers = $settingsRaw['delivery_carriers'] ?? [
                ['id' => 'carrier_1', 'name' => 'DHL Express', 'code' => 'DHL', 'deliveries' => 0],
                ['id' => 'carrier_2', 'name' => 'FedEx', 'code' => 'FDX', 'deliveries' => 0],
                ['id' => 'carrier_3', 'name' => 'Aramex', 'code' => 'ARMX', 'deliveries' => 0],
                ['id' => 'carrier_4', 'name' => 'Amazon Logistics', 'code' => 'AMZN', 'deliveries' => 0],
                ['id' => 'carrier_5', 'name' => 'Emirates Post', 'code' => 'EMPOST', 'deliveries' => 0],
            ];
        }

        // Package Sizes
        if (Schema::hasTable('package_sizes')) {
            $packageSizes = PackageSize::orderBy('name')->get()->map(fn($s) => [
                'id' => $s->id,
                'name' => $s->name,
                'description' => $s->description ?? '',
            ])->toArray();
        } else {
            $packageSizes = $settingsRaw['package_sizes'] ?? [
                ['id' => 'size_1', 'name' => 'Envelope / Document', 'description' => 'Flat documents, legal papers & letters'],
                ['id' => 'size_2', 'name' => 'Small Box', 'description' => 'Up to 30x20x15 cm (fits in small locker)'],
                ['id' => 'size_3', 'name' => 'Medium Box', 'description' => 'Up to 50x40x30 cm (standard shipping carton)'],
                ['id' => 'size_4', 'name' => 'Large Box', 'description' => 'Over 50 cm or weighing > 10 kg'],
                ['id' => 'size_5', 'name' => 'Pallet / Freight', 'description' => 'Bulk equipment, requires loading bay access'],
            ];
        }

        // More Tab Models
        $departments = Department::with(['headEmployee', 'floors'])
            ->withCount('employees')
            ->orderBy('name')
            ->get();

        $sites = Site::withCount([
            'buildings',
            'employees',
            'kioskDevices',
            'visits as active_visitors_count' => fn($q) => $q->where('status', 'checked_in')
        ])->get();

        $purposes = VisitPurpose::withCount('visits')->orderBy('name')->get();
        $visitorTypes = VisitorType::withCount('visits')->orderBy('name')->get();
        $idTypes = IdType::withCount('visitors')->orderBy('name')->get();
        $shifts = Shift::orderBy('name')->get();
        $floors = Floor::with(['site', 'building', 'areas'])->orderBy('name')->get();
        $buildings = Building::orderBy('name')->get();
        $generalAreas = Area::where('organization_id', $orgId)->select('name')->distinct()->orderBy('name')->pluck('name')->toArray();
        $employees = Employee::whereIn('status', ['active', 'on_leave'])->orderBy('first_name')->get();
        $kioskDevices = KioskDevice::with('site')->get();

        $activeTab = $request->get('tab', 'general');

        return view('settings.index', compact(
            'org',
            'general',
            'branding',
            'kiosk',
            'security',
            'notifications',
            'privacy',
            'vns',
            'vas',
            'meetingRooms',
            'deliveryCarriers',
            'packageSizes',
            'departments',
            'sites',
            'purposes',
            'visitorTypes',
            'idTypes',
            'shifts',
            'floors',
            'buildings',
            'generalAreas',
            'employees',
            'kioskDevices',
            'activeTab'
        ));
    }

    /**
     * Save General Settings.
     */
    public function updateGeneral(Request $request): RedirectResponse
    {
        $orgId = TenantScope::getTenantId() ?? 1;

        $validated = $request->validate([
            'org_name' => ['required', 'string', 'max:255'],
            'trade_license_no' => ['nullable', 'string', 'max:100'],
            'timezone' => ['required', 'string', 'max:100'],
            'date_format' => ['required', 'string', 'max:50'],
            'time_format' => ['required', 'string', 'in:12h,24h'],
            'default_language' => ['required', 'string', 'in:en,ar'],
            'business_type' => ['required', 'string'],
            'business_hours_start' => ['required', 'string'],
            'business_hours_end' => ['required', 'string'],
            'working_days' => ['nullable', 'array'],
            'default_visit_duration' => ['required', 'string'],
        ]);

        $workingDays = $request->input('working_days', ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday']);

        // Update Organization model if present
        $org = Organization::find($orgId);
        if ($org) {
            $org->update([
                'name' => $validated['org_name'],
                'trade_license_no' => $validated['trade_license_no'] ?? $org->trade_license_no,
                'timezone' => $validated['timezone'],
                'default_language' => $validated['default_language'],
            ]);
        }

        OrganizationSetting::updateOrCreate(
            ['organization_id' => $orgId, 'key' => 'general'],
            ['value' => array_merge($validated, [
                'working_days' => $workingDays,
                'after_hours_checkin' => $request->boolean('after_hours_checkin'),
                'auto_checkout_eod' => $request->boolean('auto_checkout_eod'),
            ])]
        );

        return redirect()->route('settings.index', ['tab' => 'general'])->with('success', 'General settings saved successfully.');
    }

    /**
     * Save Branding Settings.
     */
    public function updateBranding(Request $request): RedirectResponse
    {
        $orgId = TenantScope::getTenantId() ?? 1;

        $validated = $request->validate([
            'company_name' => ['required', 'string', 'max:255'],
            'display_name' => ['nullable', 'string', 'max:255'],
            'primary_color' => ['required', 'string', 'max:20'],
            'logo' => ['nullable', 'image', 'max:3072'],
        ]);

        $setting = OrganizationSetting::firstOrNew(['organization_id' => $orgId, 'key' => 'branding']);
        $currentValue = $setting->value ?? [];

        $logoUrl = $currentValue['logo_url'] ?? asset('images/logo.png');
        if ($request->hasFile('logo')) {
            $path = $request->file('logo')->store('branding', 'public');
            $logoUrl = Storage::url($path);
        }

        $setting->value = [
            'company_name' => $validated['company_name'],
            'display_name' => $validated['display_name'] ?? $validated['company_name'],
            'primary_color' => $validated['primary_color'],
            'logo_url' => $logoUrl,
        ];
        $setting->save();

        return redirect()->route('settings.index', ['tab' => 'branding'])->with('success', 'Branding preferences updated.');
    }

    /**
     * Save Kiosk Configuration.
     */
    public function updateKiosk(Request $request): RedirectResponse
    {
        $orgId = TenantScope::getTenantId() ?? 1;

        $validated = $request->validate([
            'idle_timeout_seconds' => ['required', 'string'],
            'language' => ['required', 'string', 'in:en,ar'],
            'badge_printer' => ['required', 'string'],
            'checkin_fields' => ['nullable', 'array'],
        ]);

        OrganizationSetting::updateOrCreate(
            ['organization_id' => $orgId, 'key' => 'kiosk'],
            ['value' => array_merge($validated, [
                'photo_capture' => $request->boolean('photo_capture'),
                'qr_scanning' => $request->boolean('qr_scanning'),
                'id_scanning' => $request->boolean('id_scanning'),
                'auto_print_badge' => $request->boolean('auto_print_badge'),
                'require_agreement' => $request->boolean('require_agreement'),
                'idle_timeout' => $request->boolean('idle_timeout'),
                'checkin_fields' => $request->input('checkin_fields', ['name', 'company', 'phone', 'email', 'idDoc']),
            ])]
        );

        return redirect()->route('settings.index', ['tab' => 'kiosk'])->with('success', 'Kiosk configuration saved.');
    }

    /**
     * Save Security Settings.
     */
    public function updateSecurity(Request $request): RedirectResponse
    {
        $orgId = TenantScope::getTenantId() ?? 1;

        $validated = $request->validate([
            'session_timeout_minutes' => ['required', 'string'],
        ]);

        OrganizationSetting::updateOrCreate(
            ['organization_id' => $orgId, 'key' => 'security'],
            ['value' => [
                'watchlist_screening' => $request->boolean('watchlist_screening'),
                'id_verification_required' => $request->boolean('id_verification_required'),
                'host_approval_required' => $request->boolean('host_approval_required'),
                'badge_mandatory' => $request->boolean('badge_mandatory'),
                'two_factor_admin' => $request->boolean('two_factor_admin'),
                'session_timeout_minutes' => $validated['session_timeout_minutes'],
            ]]
        );

        return redirect()->route('settings.index', ['tab' => 'security'])->with('success', 'Security & Access policies saved.');
    }

    /**
     * Save Notification Preferences.
     */
    public function updateNotifications(Request $request): RedirectResponse
    {
        $orgId = TenantScope::getTenantId() ?? 1;

        $validated = $request->validate([
            'admin_email' => ['required', 'email', 'max:255'],
            'sms_gateway' => ['nullable', 'string', 'max:100'],
        ]);

        OrganizationSetting::updateOrCreate(
            ['organization_id' => $orgId, 'key' => 'notifications'],
            ['value' => [
                'admin_email' => $validated['admin_email'],
                'sms_gateway' => $validated['sms_gateway'] ?? 'Twilio',
                'daily_summary' => $request->boolean('daily_summary'),
                'watchlist_alert' => $request->boolean('watchlist_alert'),
                'system_health_alerts' => $request->boolean('system_health_alerts'),
            ]]
        );

        return redirect()->route('settings.index', ['tab' => 'notifications'])->with('success', 'Notification preferences saved.');
    }

    /**
     * Save Data & Privacy Rules.
     */
    public function updatePrivacy(Request $request): RedirectResponse
    {
        $orgId = TenantScope::getTenantId() ?? 1;

        $validated = $request->validate([
            'retention_days' => ['required', 'string'],
            'photo_retention_days' => ['required', 'string'],
        ]);

        OrganizationSetting::updateOrCreate(
            ['organization_id' => $orgId, 'key' => 'privacy'],
            ['value' => [
                'retention_days' => $validated['retention_days'],
                'photo_retention_days' => $validated['photo_retention_days'],
                'privacy_notice_on_kiosk' => $request->boolean('privacy_notice_on_kiosk'),
                'anonymize_old_records' => $request->boolean('anonymize_old_records'),
                'allow_data_requests' => $request->boolean('allow_data_requests'),
            ]]
        );

        return redirect()->route('settings.index', ['tab' => 'privacy'])->with('success', 'Data & Privacy rules saved.');
    }

    /**
     * Save Host Notification Rules.
     */
        public function updateHostRules(Request $request): RedirectResponse
    {
        $orgId = TenantScope::getTenantId() ?? 1;

        $validated = $request->validate([
            'arrival_reminder_minutes' => ['nullable', 'integer', 'min:0', 'max:1440'],
            'prereg_briefing_time' => ['nullable', 'date_format:H:i A', 'date_format:H:i'],
            'escalation_backup_host' => ['required', \Illuminate\Validation\Rule::in(['department_head', 'front_desk', 'security_team', 'administrator'])],
            'escalate_after_minutes' => ['required', 'integer', 'min:1', 'max:1440'],
            'checkout_overstay_minutes' => ['required', 'integer', 'min:1', 'max:10080'],
        ]);

        $reminderMinutes = $request->input('arrival_reminder_minutes');
        if ($reminderMinutes == 0) {
            $reminderMinutes = null;
        }

        VisitNotificationSetting::updateOrCreate(
            ['organization_id' => $orgId],
            [
                'arrival_email_enabled' => $request->boolean('arrival_email'),
                'arrival_push_enabled' => $request->boolean('arrival_push'),
                'arrival_sms_enabled' => $request->boolean('arrival_sms'),
                'arrival_desktop_enabled' => $request->boolean('arrival_desktop'),
                'arrival_reminder_minutes' => $reminderMinutes,

                'prereg_host_notification_enabled' => $request->boolean('prereg_notify'),
                'morning_briefing_enabled' => $request->boolean('prereg_briefing'),
                'morning_briefing_time' => $request->input('prereg_briefing_time', '08:00'),
                'prereg_visitor_reminder_enabled' => $request->boolean('prereg_reminder_15'),
                'prereg_visitor_reminder_minutes' => 15,

                'escalation_enabled' => true,
                'escalation_recipient_code' => $request->input('escalation_backup_host', 'department_head'),
                'escalation_after_minutes' => $request->input('escalate_after_minutes', 10),

                'checkout_notification_enabled' => $request->boolean('checkout_notify'),
                'overstay_alert_enabled' => $request->boolean('checkout_overstay_alert'),
                'overstay_threshold_minutes' => $request->input('checkout_overstay_minutes', 60),
            ]
        );

        VisitApprovalSetting::updateOrCreate(
            ['organization_id' => $orgId],
            [
                'preregistered_arrival_approval_mode' => $request->boolean('escalation_auto_approve') ? 'automatic' : 'host',
            ]
        );

        return redirect()->route('settings.index', ['tab' => 'hostAlerts'])->with('success', 'Host Notification Rules updated.');
    }

    /**
     * Store Meeting Room.
     */
    public function storeMeetingRoom(Request $request): RedirectResponse
    {
        $orgId = TenantScope::getTenantId() ?? 1;

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'site_id' => ['nullable', 'exists:sites,id'],
            'floor_id' => ['nullable', 'exists:floors,id'],
            'capacity' => ['nullable', 'integer', 'min:1', 'max:500'],
        ]);

        if (Schema::hasTable('meeting_rooms')) {
            MeetingRoom::create([
                'organization_id' => $orgId,
                'name' => $validated['name'],
                'site_id' => $validated['site_id'] ?? null,
                'floor_id' => $validated['floor_id'] ?? null,
                'capacity' => $validated['capacity'] ?? 8,
            ]);
        } else {
            $setting = OrganizationSetting::firstOrNew(['organization_id' => $orgId, 'key' => 'meeting_rooms']);
            $rooms = $setting->value ?? [];
            $site = !empty($validated['site_id']) ? Site::find($validated['site_id']) : null;
            $floor = !empty($validated['floor_id']) ? Floor::find($validated['floor_id']) : null;
            $rooms[] = [
                'id' => 'room_' . time(),
                'name' => $validated['name'],
                'site_id' => $validated['site_id'] ?? null,
                'site_name' => $site?->name ?? 'Main Office',
                'site_code' => $site?->code ?? '',
                'floor_id' => $validated['floor_id'] ?? null,
                'floor_name' => $floor?->name ?? '',
                'capacity' => (int)($validated['capacity'] ?? 8),
                'bookings' => 0,
            ];
            $setting->value = $rooms;
            $setting->save();
        }

        return redirect()->route('settings.index', ['tab' => 'meetingRooms'])->with('success', 'Meeting Room added.');
    }

    /**
     * Delete Meeting Room.
     */
    public function updateMeetingRoom(Request $request, string $id): RedirectResponse
    {
        $orgId = TenantScope::getTenantId() ?? 1;
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'site_id' => 'required',
            'capacity' => 'nullable|integer',
        ]);

        if (Schema::hasTable('meeting_rooms')) {
            MeetingRoom::where('id', $id)->update([
                'name' => $validated['name'],
                'site_id' => $validated['site_id'],
                'capacity' => $validated['capacity'] ?? 8,
            ]);
        }
        return redirect()->route('settings.index', ['tab' => 'meetingRooms'])->with('success', 'Meeting Room updated.');
    }

    public function destroyMeetingRoom(string $id): RedirectResponse
    {
        $orgId = TenantScope::getTenantId() ?? 1;
        if (Schema::hasTable('meeting_rooms')) {
            MeetingRoom::where('id', $id)->delete();
        }
        $setting = OrganizationSetting::where('organization_id', $orgId)->where('key', 'meeting_rooms')->first();
        if ($setting && is_array($setting->value)) {
            $setting->value = array_values(array_filter($setting->value, fn($r) => (string)($r['id'] ?? '') !== (string)$id));
            $setting->save();
        }

        return redirect()->route('settings.index', ['tab' => 'meetingRooms'])->with('success', 'Meeting Room removed.');
    }

    /**
     * Store Delivery Carrier.
     */
    public function storeCarrier(Request $request): RedirectResponse
    {
        $orgId = TenantScope::getTenantId() ?? 1;

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
        ]);

        if (Schema::hasTable('delivery_carriers')) {
            DeliveryCarrier::create([
                'organization_id' => $orgId,
                'name' => $validated['name'],
            ]);
        } else {
            $setting = OrganizationSetting::firstOrNew(['organization_id' => $orgId, 'key' => 'delivery_carriers']);
            $carriers = $setting->value ?? [];
            $carriers[] = [
                'id' => 'carrier_' . time(),
                'name' => $validated['name'],
                'deliveries' => 0,
            ];
            $setting->value = $carriers;
            $setting->save();
        }

        return redirect()->route('settings.index', ['tab' => 'deliveryCarriers'])->with('success', 'Delivery Carrier added.');
    }

    /**
     * Update Delivery Carrier.
     */
    public function updateCarrier(Request $request, string $id): RedirectResponse
    {
        $orgId = TenantScope::getTenantId() ?? 1;

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
        ]);

        if (Schema::hasTable('delivery_carriers')) {
            $carrier = DeliveryCarrier::where('id', $id)->first();
            if ($carrier) {
                $carrier->update(['name' => $validated['name']]);
            }
        } else {
            $setting = OrganizationSetting::where('organization_id', $orgId)->where('key', 'delivery_carriers')->first();
            if ($setting && is_array($setting->value)) {
                $carriers = $setting->value;
                foreach ($carriers as &$c) {
                    if ((string)($c['id'] ?? '') === (string)$id) {
                        $c['name'] = $validated['name'];
                        break;
                    }
                }
                $setting->value = $carriers;
                $setting->save();
            }
        }

        return redirect()->route('settings.index', ['tab' => 'deliveryCarriers'])->with('success', 'Delivery Carrier updated.');
    }

    /**
     * Delete Delivery Carrier.
     */
    public function destroyCarrier(string $id): RedirectResponse
    {
        $orgId = TenantScope::getTenantId() ?? 1;
        if (Schema::hasTable('delivery_carriers')) {
            DeliveryCarrier::where('id', $id)->delete();
        }
        $setting = OrganizationSetting::where('organization_id', $orgId)->where('key', 'delivery_carriers')->first();
        if ($setting && is_array($setting->value)) {
            $setting->value = array_values(array_filter($setting->value, fn($c) => (string)($c['id'] ?? '') !== (string)$id));
            $setting->save();
        }

        return redirect()->route('settings.index', ['tab' => 'deliveryCarriers'])->with('success', 'Delivery Carrier removed.');
    }

    /**
     * Store Package Size.
     */
    public function storePackageSize(Request $request): RedirectResponse
    {
        $orgId = TenantScope::getTenantId() ?? 1;

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
        ]);

        if (Schema::hasTable('package_sizes')) {
            PackageSize::create([
                'organization_id' => $orgId,
                'name' => $validated['name'],
            ]);
        } else {
            $setting = OrganizationSetting::firstOrNew(['organization_id' => $orgId, 'key' => 'package_sizes']);
            $sizes = $setting->value ?? [];
            $sizes[] = [
                'id' => 'size_' . time(),
                'name' => $validated['name'],
                'packages' => 0,
            ];
            $setting->value = $sizes;
            $setting->save();
        }

        return redirect()->route('settings.index', ['tab' => 'packageSizes'])->with('success', 'Package Size added.');
    }

    public function updatePackageSize(Request $request, string $id): RedirectResponse
    {
        $orgId = TenantScope::getTenantId() ?? 1;

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
        ]);

        if (Schema::hasTable('package_sizes')) {
            $size = PackageSize::where('id', $id)->first();
            if ($size) {
                $size->update(['name' => $validated['name']]);
            }
        } else {
            $setting = OrganizationSetting::where('organization_id', $orgId)->where('key', 'package_sizes')->first();
            if ($setting && is_array($setting->value)) {
                $sizes = $setting->value;
                foreach ($sizes as &$s) {
                    if ((string)($s['id'] ?? '') === (string)$id) {
                        $s['name'] = $validated['name'];
                        break;
                    }
                }
                $setting->value = $sizes;
                $setting->save();
            }
        }

        return redirect()->route('settings.index', ['tab' => 'packageSizes'])->with('success', 'Package Size updated.');
    }

    /**
     * Delete Package Size.
     */
    public function destroyPackageSize(string $id): RedirectResponse
    {
        $orgId = TenantScope::getTenantId() ?? 1;
        if (Schema::hasTable('package_sizes')) {
            PackageSize::where('id', $id)->delete();
        }
        $setting = OrganizationSetting::where('organization_id', $orgId)->where('key', 'package_sizes')->first();
        if ($setting && is_array($setting->value)) {
            $setting->value = array_values(array_filter($setting->value, fn($s) => (string)($s['id'] ?? '') !== (string)$id));
            $setting->save();
        }

        return redirect()->route('settings.index', ['tab' => 'packageSizes'])->with('success', 'Package Size removed.');
    }

    /**
     * Store Floor.
     */
    public function storeFloor(Request $request): RedirectResponse
    {
        $orgId = TenantScope::getTenantId() ?? 1;

        $validated = $request->validate([
            'site_id' => ['required', 'exists:sites,id'],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:500'],
            'areas' => ['nullable', 'string'],
        ]);

        $existingFloor = Floor::where('organization_id', $orgId)
            ->where('site_id', $validated['site_id'])
            ->whereRaw('LOWER(TRIM(name)) = ?', [strtolower(trim($validated['name']))])
            ->first();

        if ($existingFloor) {
            return redirect()->route('settings.index', ['tab' => 'floors'])
                ->withErrors(['name' => 'A floor with this name already exists for this branch.'])
                ->withInput();
        }

        $site = Site::find($validated['site_id']);
        $building = Building::firstOrCreate(
            ['organization_id' => $orgId, 'site_id' => $validated['site_id']],
            ['name' => ($site ? $site->name : 'Main') . ' Tower', 'status' => 'active']
        );

        $floor = Floor::create([
            'organization_id' => $orgId,
            'site_id' => $validated['site_id'],
            'building_id' => $building->id,
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'status' => 'active',
        ]);

        if (!empty($validated['areas'])) {
            $areaNames = array_filter(array_map('trim', explode(',', $validated['areas'])));
            foreach ($areaNames as $areaName) {
                Area::create([
                    'organization_id' => $orgId,
                    'floor_id' => $floor->id,
                    'name' => $areaName,
                    'status' => 'active',
                ]);

                if (Schema::hasTable('meeting_rooms')) {
                    MeetingRoom::where('organization_id', $orgId)
                        ->where('site_id', $validated['site_id'])
                        ->where('name', $areaName)
                        ->update(['floor_id' => $floor->id]);
                }
            }
        }

        return redirect()->route('settings.index', ['tab' => 'floors'])->with('success', 'Floor saved.');
    }

    /**
     * Update Floor.
     */
    public function updateFloor(Request $request, string $id): RedirectResponse
    {
        $orgId = TenantScope::getTenantId() ?? 1;

        $validated = $request->validate([
            'site_id' => ['required', 'exists:sites,id'],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:500'],
            'areas' => ['nullable', 'string'],
        ]);

        $existingFloor = Floor::where('organization_id', $orgId)
            ->where('site_id', $validated['site_id'])
            ->where('id', '!=', $id)
            ->whereRaw('LOWER(TRIM(name)) = ?', [strtolower(trim($validated['name']))])
            ->first();

        if ($existingFloor) {
            return redirect()->route('settings.index', ['tab' => 'floors'])
                ->withErrors(['name' => 'A floor with this name already exists for this branch.'])
                ->withInput();
        }

        $floor = Floor::findOrFail($id);
        $site = Site::find($validated['site_id']);
        $building = Building::firstOrCreate(
            ['organization_id' => $orgId, 'site_id' => $validated['site_id']],
            ['name' => ($site ? $site->name : 'Main') . ' Tower', 'status' => 'active']
        );

        $floor->update([
            'site_id' => $validated['site_id'],
            'building_id' => $building->id,
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
        ]);

        Area::where('floor_id', $floor->id)->delete();
        $areaNames = !empty($validated['areas'])
            ? array_filter(array_map('trim', explode(',', $validated['areas'])))
            : [];

        if (Schema::hasTable('meeting_rooms')) {
            // Unlink any meeting rooms previously on this floor that are no longer tagged
            MeetingRoom::where('organization_id', $orgId)
                ->where('floor_id', $floor->id)
                ->whereNotIn('name', $areaNames)
                ->update(['floor_id' => null]);
        }

        foreach ($areaNames as $areaName) {
            Area::create([
                'organization_id' => $orgId,
                'floor_id' => $floor->id,
                'name' => $areaName,
                'status' => 'active',
            ]);

            if (Schema::hasTable('meeting_rooms')) {
                MeetingRoom::where('organization_id', $orgId)
                    ->where('site_id', $validated['site_id'])
                    ->where('name', $areaName)
                    ->update(['floor_id' => $floor->id]);
            }
        }

        return redirect()->route('settings.index', ['tab' => 'floors'])->with('success', 'Floor saved.');
    }

    /**
     * Delete Floor.
     */
    public function destroyFloor(string $id): RedirectResponse
    {
        $floor = Floor::findOrFail($id);
        Area::where('floor_id', $floor->id)->delete();
        if (Schema::hasTable('meeting_rooms')) {
            MeetingRoom::where('floor_id', $floor->id)->update(['floor_id' => null]);
        }
        $floor->delete();

        return redirect()->route('settings.index', ['tab' => 'floors'])->with('success', 'Floor removed.');
    }
}


