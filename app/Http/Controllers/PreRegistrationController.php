<?php

namespace App\Http\Controllers;

use App\Models\Country;
use App\Models\Department;
use App\Models\Employee;
use App\Models\IdType;
use App\Models\Invitation;
use App\Models\MeetingRoom;
use App\Models\Scopes\TenantScope;
use App\Models\Site;
use App\Models\Visit;
use App\Models\Visitor;
use App\Models\VisitorType;
use App\Models\VisitPurpose;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class PreRegistrationController extends Controller
{
    /**
     * Display a listing of pre-registrations with filters and stats.
     */
    public function index(Request $request): View
    {
        $orgId = TenantScope::getTenantId();
        $today = Carbon::today();

        // Base query for invitations with relationships
        $query = Invitation::with([
            'visit.visitor.idType',
            'visit.site',
            'visit.hostEmployee',
            'visit.department',
            'visit.purpose',
            'visit.visitorType'
        ])->whereHas('visit', function ($q) use ($orgId) {
            $q->where('organization_id', $orgId)
              // Only visitors added through Pre-Registration; walk-ins from the Check-In form never belong here
              ->where(fn ($s) => $s->whereNull('source')->orWhere('source', '!=', 'walkin'));
        });

        // Compute top KPI statistics for upcoming visits (scheduled start >= start of today and not cancelled)
        $upcomingBase = (clone $query)->whereHas('visit', function ($q) use ($today) {
            $q->where('scheduled_start_at', '>=', $today->startOfDay());
        })->where('status', '!=', 'cancelled');

        $totalUpcoming = (clone $upcomingBase)->count();
        $confirmedCount = (clone $upcomingBase)->where('status', 'approved')->count();
        $pendingCount = (clone $upcomingBase)->where('status', 'pending')->count();

        $todayExpectedCount = (clone $query)->whereHas('visit', function ($q) use ($today) {
            $q->whereDate('scheduled_start_at', Carbon::today());
        })->where('status', '!=', 'cancelled')->count();

        $stats = [
            'total_upcoming' => $totalUpcoming,
            'confirmed' => $confirmedCount,
            'pending' => $pendingCount,
            'today_expected' => $todayExpectedCount,
        ];

        // Apply Tab Filter (all, today, upcoming, past, custom)
        $tab = $request->query('tab', 'all');
        if ($tab === 'today') {
            $query->whereHas('visit', function ($q) {
                $q->whereDate('scheduled_start_at', Carbon::today());
            });
        } elseif ($tab === 'upcoming') {
            $query->whereHas('visit', function ($q) {
                $q->where('scheduled_start_at', '>', Carbon::today()->endOfDay());
            });
        } elseif ($tab === 'past') {
            $query->whereHas('visit', function ($q) {
                $q->where('scheduled_start_at', '<', Carbon::today()->startOfDay());
            });
        } elseif ($tab === 'custom') {
            if ($request->filled('custom_from')) {
                $query->whereHas('visit', function ($q) use ($request) {
                    $q->where('scheduled_start_at', '>=', Carbon::parse($request->query('custom_from'))->startOfDay());
                });
            }
            if ($request->filled('custom_to')) {
                $query->whereHas('visit', function ($q) use ($request) {
                    $q->where('scheduled_start_at', '<=', Carbon::parse($request->query('custom_to'))->endOfDay());
                });
            }
        }

        // Apply Status Filter (all, approved, pending, cancelled)
        $statusFilter = $request->query('status', 'all');
        if ($statusFilter !== 'all') {
            $query->where('status', $statusFilter);
        }

        // Latest expected date first (the visit's scheduled start), newest record first for the same time
        $this->orderByExpectedDate($query);

        // Paging: 25 / 50 / 100 / 500 rows per page, or "all" (default 25)
        $perPageOption = (string) $request->query('per_page', '25');
        if (!in_array($perPageOption, ['25', '50', '100', '500', 'all'], true)) {
            $perPageOption = '25';
        }
        $perPage = $perPageOption === 'all' ? max(1, (clone $query)->reorder()->count()) : (int) $perPageOption;

        $invitations = $query->paginate($perPage)->withQueryString();

        // Form Master Data for Add/Edit Modal
        $sites = Site::where('status', 'active')->get();
        $departments = Department::all();
        $employees = Employee::with(['site', 'leaves'])->get();
        $purposes = VisitPurpose::all();
        $visitorTypes = VisitorType::where('status', 'active')->get();
        if ($visitorTypes->isEmpty()) {
            $visitorTypes = VisitorType::all();
        }
        $idTypes = IdType::where('status', 'active')->get();
        if ($idTypes->isEmpty()) {
            $idTypes = IdType::all();
        }

        $meetingRooms = Schema::hasTable('meeting_rooms')
            ? MeetingRoom::with(['site', 'floor'])->orderBy('name')->get()
            : collect([]);

        return view('pre_registrations.index', compact(
            'invitations',
            'perPageOption',
            'stats',
            'tab',
            'statusFilter',
            'sites',
            'departments',
            'employees',
            'purposes',
            'visitorTypes',
            'idTypes',
            'meetingRooms'
        ));
    }

    /**
     * Store a newly created pre-registration.
     */
    public function store(Request $request): RedirectResponse
    {
        $orgId = TenantScope::getTenantId();

        $validated = $request->validate([
            // Section 1: Visitor Info
            'name' => ['nullable', 'string', 'max:255'],
            'first_name' => ['nullable', 'string', 'max:255'],
            'last_name' => ['nullable', 'string', 'max:255'],
            'company_name' => ['nullable', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:254'],
            'phone' => ['nullable', 'string', 'max:32'],
            'phone_code' => ['nullable', 'string', 'max:10'],
            'id_type_id' => ['nullable', 'exists:id_types,id'],
            'id_number' => ['nullable', 'string', 'max:255'],

            // Section 2: Visit Schedule
            'visit_date' => ['required', 'date'],
            'expected_arrival' => ['required', 'string'],
            'expected_departure' => ['nullable', 'string'],
            'duration' => ['nullable', 'integer', 'min:15'],

            // Section 3: Recurring Visit
            'recurring' => ['nullable', 'string', 'in:none,daily,weekly,monthly'],

            // Section 4: Visit Details
            'purpose_id' => ['required', 'exists:visit_purposes,id'],
            'visitor_type_id' => ['nullable', 'exists:visitor_types,id'],
            'site_id' => ['nullable', 'exists:sites,id'],
            'host_employee_id' => ['required', 'exists:employees,id'],
            'department_id' => ['nullable', 'exists:departments,id'],
            'meeting_room' => ['nullable', 'string', 'max:255'],
            'expected_visitor_count' => ['nullable', 'integer', 'min:1'],

            // Section 5: Access & Requirements
            'send_confirmation_email' => ['nullable', 'boolean'],
            'notify_host_on_arrival' => ['nullable', 'boolean'],
            'send_qr_self_checkin' => ['nullable', 'boolean'],
            'requires_nda' => ['nullable', 'boolean'],
            'wifi_guest_access' => ['nullable', 'boolean'],
            'parking_required' => ['nullable', 'boolean'],
            'escort_required' => ['nullable', 'boolean'],

            // Section 6: Additional Notes
            'special_instructions' => ['nullable', 'string', 'max:1000'],
            'items_to_bring' => ['nullable', 'string', 'max:1000'],
        ]);

        $rawName = trim($request->input('name', ''));
        if (!empty($rawName)) {
            $nameParts = explode(' ', $rawName, 2);
            $firstName = $nameParts[0];
            $lastName = $nameParts[1] ?? null;
        } else {
            $firstName = $request->input('first_name', 'Visitor');
            $lastName = $request->input('last_name', null);
        }

        $phoneNum = trim($request->input('phone', ''));
        $phoneCode = trim($request->input('phone_code', '971'));
        $fullPhone = $phoneNum ? ('+' . ltrim($phoneCode, '+') . ' ' . $phoneNum) : null;

        // Find or create Visitor
        $visitor = Visitor::where('organization_id', $orgId)
            ->where('email', $validated['email'])
            ->first();

        if (!$visitor) {
            $visitor = Visitor::create([
                'organization_id' => $orgId,
                'first_name' => $firstName,
                'last_name' => $lastName,
                'email' => $validated['email'],
                'phone' => $fullPhone,
                'id_type_id' => $validated['id_type_id'] ?? null,
                'id_number' => $validated['id_number'] ?? null,
                'company_name' => $validated['company_name'] ?? null,
            ]);
        } else {
            $visitor->update([
                'first_name' => $firstName,
                'last_name' => $lastName ?? $visitor->last_name,
                'phone' => $fullPhone ?? $visitor->phone,
                'id_type_id' => $validated['id_type_id'] ?? $visitor->id_type_id,
                'id_number' => $validated['id_number'] ?? $visitor->id_number,
                'company_name' => $validated['company_name'] ?? $visitor->company_name,
            ]);
        }

        // Calculate start and end timestamps
        $visitDate = Carbon::parse($validated['visit_date'])->format('Y-m-d');
        $startTimeStr = $validated['expected_arrival'];
        $scheduledStart = Carbon::parse("{$visitDate} {$startTimeStr}");

        $durationMinutes = !empty($validated['duration']) ? (int) $validated['duration'] : 60;
        if (!empty($validated['expected_departure'])) {
            $scheduledEnd = Carbon::parse("{$visitDate} {$validated['expected_departure']}");
        } else {
            $scheduledEnd = (clone $scheduledStart)->addMinutes($durationMinutes);
        }

        // Branch comes from the host employee; department defaults to the host's department
        $hostEmp = Employee::find($validated['host_employee_id']);
        $deptId = $validated['department_id'] ?? $hostEmp?->department_id;
        $siteId = $hostEmp?->site_id ?? $validated['site_id'] ?? Site::query()->value('id');

        // Create Visit Record
        $visit = Visit::create([
            'organization_id' => $orgId,
            'visitor_id' => $visitor->id,
            'site_id' => $siteId,
            'host_employee_id' => $validated['host_employee_id'],
            'department_id' => $deptId,
            'purpose_id' => $validated['purpose_id'],
            'visitor_type_id' => $validated['visitor_type_id'] ?? null,
            'scheduled_start_at' => $scheduledStart,
            'scheduled_end_at' => $scheduledEnd,
            'status' => 'approved',
            'source' => 'preregistered',
            'notes' => $validated['special_instructions'] ?? null,
        ]);

        // Generate unique access code
        $accessCode = 'ZAP-' . strtoupper(Str::random(5));

        // Create Invitation Record
        Invitation::create([
            'organization_id' => $orgId,
            'visit_id' => $visit->id,
            'access_code' => $accessCode,
            'expected_visitor_count' => $validated['expected_visitor_count'] ?? 1,
            'items_to_bring' => $validated['items_to_bring'] ?? null,
            'special_instructions' => $validated['special_instructions'] ?? null,
            'meeting_room' => $validated['meeting_room'] ?? null,
            'recurring' => $validated['recurring'] ?? 'none',
            'duration' => $durationMinutes,
            'send_confirmation_email' => $request->has('send_confirmation_email'),
            'notify_host_on_arrival' => $request->has('notify_host_on_arrival'),
            'send_qr_self_checkin' => $request->has('send_qr_self_checkin'),
            'requires_nda' => $request->has('requires_nda'),
            'wifi_guest_access' => $request->has('wifi_guest_access'),
            'parking_required' => $request->has('parking_required'),
            'escort_required' => $request->has('escort_required'),
            'sent_at' => Carbon::now(),
            'expires_at' => $scheduledEnd,
            'status' => 'approved',
        ]);

        return back()->with('success', 'Pre-registration created successfully. Access code: ' . $accessCode);
    }

    /**
     * Update the specified pre-registration.
     */
        public function update(Request $request, Invitation $invitation): RedirectResponse
    {
        \Log::info('Update Request Data:', $request->all());
        // Once the visitor has checked in, the arrival time (schedule) and visit details are locked:
        // those fields are neither validated nor saved, even if they are sent in the request.
        $isCheckedIn = $invitation->status === 'checked_in'
            || in_array($invitation->visit?->status, ['checked_in', 'checked_out'], true);
        $lockedFields = [
            'visit_date', 'expected_arrival', 'expected_departure', 'duration', 'recurring',
            'purpose_id', 'visitor_type_id', 'site_id', 'host_employee_id', 'department_id',
            'meeting_room', 'expected_visitor_count',
        ];

        $rules = [
            'name' => ['nullable', 'string', 'max:255'],
            'first_name' => ['nullable', 'string', 'max:255'],
            'last_name' => ['nullable', 'string', 'max:255'],
            'company_name' => ['nullable', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:254'],
            'phone' => ['nullable', 'string', 'max:32'],
            'phone_code' => ['nullable', 'string', 'max:10'],
            'id_type_id' => ['nullable', 'exists:id_types,id'],
            'id_number' => ['nullable', 'string', 'max:255'],

            'visit_date' => ['required', 'date'],
            'expected_arrival' => ['required', 'string'],
            'expected_departure' => ['nullable', 'string'],
            'duration' => ['nullable', 'integer', 'min:15'],

            'recurring' => ['nullable', 'string', 'in:none,daily,weekly,monthly'],
            'purpose_id' => ['required', 'exists:visit_purposes,id'],
            'visitor_type_id' => ['nullable', 'exists:visitor_types,id'],
            'site_id' => ['nullable', 'exists:sites,id'],
            'host_employee_id' => ['required', 'exists:employees,id'],
            'department_id' => ['nullable', 'exists:departments,id'],
            'meeting_room' => ['nullable', 'string', 'max:255'],
            'expected_visitor_count' => ['nullable', 'integer', 'min:1'],

            'special_instructions' => ['nullable', 'string', 'max:1000'],
            'items_to_bring' => ['nullable', 'string', 'max:1000'],
        ];

        if ($isCheckedIn) {
            $rules = array_diff_key($rules, array_flip($lockedFields));
        }

        $validated = $request->validate($rules);

        $rawName = trim($request->input('name', ''));
        if (!empty($rawName)) {
            $nameParts = explode(' ', $rawName, 2);
            $firstName = $nameParts[0];
            $lastName = $nameParts[1] ?? null;
        } else {
            $firstName = $request->input('first_name', 'Visitor');
            $lastName = $request->input('last_name', null);
        }

        $phoneNum = trim($request->input('phone', ''));
        $phoneCode = trim($request->input('phone_code', '971'));
        $fullPhone = $phoneNum ? ('+' . ltrim($phoneCode, '+') . ' ' . $phoneNum) : null;

        $visit = $invitation->visit;
        $visitor = $visit->visitor;

        // Update Visitor
        $visitor->update([
            'first_name' => $firstName,
            'last_name' => $lastName ?? $visitor->last_name,
            'email' => $validated['email'],
            'phone' => $fullPhone ?? $visitor->phone,
            'id_type_id' => $validated['id_type_id'] ?? null,
            'id_number' => $validated['id_number'] ?? null,
            'company_name' => $validated['company_name'] ?? null,
        ]);

        $accessFlags = [
            'send_confirmation_email' => $request->has('send_confirmation_email'),
            'notify_host_on_arrival' => $request->has('notify_host_on_arrival'),
            'send_qr_self_checkin' => $request->has('send_qr_self_checkin'),
            'requires_nda' => $request->has('requires_nda'),
            'wifi_guest_access' => $request->has('wifi_guest_access'),
            'parking_required' => $request->has('parking_required'),
            'escort_required' => $request->has('escort_required'),
        ];

        if ($isCheckedIn) {
            // Only visitor information, access options and notes can still change.
            $visit->update(['notes' => $validated['special_instructions'] ?? null]);

            $invitation->update(array_merge([
                'items_to_bring' => $validated['items_to_bring'] ?? null,
                'special_instructions' => $validated['special_instructions'] ?? null,
            ], $accessFlags));

            return back()->with('success', 'Pre-registration updated. Arrival time and visit details are locked because the visitor has checked in.');
        }

        // Calculate schedule times
        $visitDate = Carbon::parse($validated['visit_date'])->format('Y-m-d');
        $scheduledStart = Carbon::parse("{$visitDate} {$validated['expected_arrival']}");

        $durationMinutes = !empty($validated['duration']) ? (int) $validated['duration'] : 60;
        if (!empty($validated['expected_departure'])) {
            $scheduledEnd = Carbon::parse("{$visitDate} {$validated['expected_departure']}");
        } else {
            $scheduledEnd = (clone $scheduledStart)->addMinutes($durationMinutes);
        }

        // Branch comes from the host employee; department defaults to the host's department
        $hostEmp = Employee::find($validated['host_employee_id']);
        $deptId = $validated['department_id'] ?? $hostEmp?->department_id;
        $siteId = $hostEmp?->site_id ?? $validated['site_id'] ?? Site::query()->value('id');

        // Update Visit
        $visit->update([
            'site_id' => $siteId,
            'host_employee_id' => $validated['host_employee_id'],
            'department_id' => $deptId,
            'purpose_id' => $validated['purpose_id'],
            'visitor_type_id' => $validated['visitor_type_id'] ?? null,
            'scheduled_start_at' => $scheduledStart,
            'scheduled_end_at' => $scheduledEnd,
            'notes' => $validated['special_instructions'] ?? null,
        ]);

        // Update Invitation
        $invitation->update(array_merge([
            'expected_visitor_count' => $validated['expected_visitor_count'] ?? 1,
            'items_to_bring' => $validated['items_to_bring'] ?? null,
            'special_instructions' => $validated['special_instructions'] ?? null,
            'meeting_room' => $validated['meeting_room'] ?? null,
            'recurring' => $validated['recurring'] ?? 'none',
            'duration' => $durationMinutes,
            'expires_at' => $scheduledEnd,
        ], $accessFlags));

        return back()->with('success', 'Pre-registration updated successfully.');
    }

    /**
     * Cancel the specified pre-registration.
     */
    public function cancel(Invitation $invitation): RedirectResponse
    {
        $invitation->update(['status' => 'cancelled']);
        if ($invitation->visit) {
            $invitation->visit->update(['status' => 'cancelled']);
        }

        return back()->with('success', 'Pre-registration has been cancelled.');
    }

    /**
     * Resend access code or notification for the specified pre-registration.
     */
    public function resend(Invitation $invitation): RedirectResponse
    {
        $invitation->update(['sent_at' => Carbon::now()]);

        return back()->with('success', 'Invitation notification resent to visitor.');
    }

    /**
     * Export pre-registrations list to CSV.
     */
    public function export(Request $request): StreamedResponse
    {
        $orgId = TenantScope::getTenantId();

        $query = Invitation::with([
            'visit.visitor',
            'visit.site',
            'visit.hostEmployee',
            'visit.purpose'
        ])->whereHas('visit', function ($q) use ($orgId) {
            $q->where('organization_id', $orgId)
              // Only visitors added through Pre-Registration; walk-ins from the Check-In form never belong here
              ->where(fn ($s) => $s->whereNull('source')->orWhere('source', '!=', 'walkin'));
        });

        $tab = $request->query('tab', 'all');
        if ($tab === 'today') {
            $query->whereHas('visit', function ($q) {
                $q->whereDate('scheduled_start_at', Carbon::today());
            });
        } elseif ($tab === 'upcoming') {
            $query->whereHas('visit', function ($q) {
                $q->where('scheduled_start_at', '>', Carbon::today()->endOfDay());
            });
        } elseif ($tab === 'past') {
            $query->whereHas('visit', function ($q) {
                $q->where('scheduled_start_at', '<', Carbon::today()->startOfDay());
            });
        }

        $this->orderByExpectedDate($query);

        $invitations = $query->get();

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="pre_registrations_' . date('Y-m-d') . '.csv"',
        ];

        $callback = function () use ($invitations) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['Access Code', 'Visitor Name', 'Company', 'Email', 'Phone', 'Host Employee', 'Purpose', 'Site', 'Scheduled Date', 'Expected Arrival', 'Status']);

            foreach ($invitations as $inv) {
                $v = $inv->visit;
                $visitor = $v ? $v->visitor : null;
                $host = $v ? $v->hostEmployee : null;

                fputcsv($file, [
                    $inv->access_code,
                    $visitor ? $visitor->full_name : '',
                    $visitor ? $visitor->company_name : '',
                    $visitor ? $visitor->email : '',
                    $visitor ? $visitor->phone : '',
                    $host ? $host->full_name : '',
                    $v && $v->purpose ? $v->purpose->name : '',
                    $v && $v->site ? $v->site->name : '',
                    $v && $v->scheduled_start_at ? $v->scheduled_start_at->format('Y-m-d') : '',
                    $v && $v->scheduled_start_at ? $v->scheduled_start_at->format('H:i') : '',
                    ucfirst($inv->status),
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Sort pre-registrations by expected date (visit scheduled start), latest first.
     */
    private function orderByExpectedDate($query): void
    {
        $query->orderByRaw('(SELECT visits.scheduled_start_at FROM visits WHERE visits.id = invitations.visit_id) DESC NULLS LAST')
              ->orderByDesc('invitations.id');
    }
}
