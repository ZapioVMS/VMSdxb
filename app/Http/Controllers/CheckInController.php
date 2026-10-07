<?php

namespace App\Http\Controllers;

use App\Models\Invitation;
use Carbon\Carbon;
use App\Models\Badge;
use App\Models\BadgeTemplate;
use App\Models\Country;
use App\Models\Department;
use App\Models\MeetingRoom;
use App\Models\Employee;
use App\Models\IdType;
use App\Models\Site;
use App\Models\Visit;
use App\Models\Visitor;
use App\Models\VisitorType;
use App\Models\VisitPurpose;
use App\Models\WatchlistEntry;
use App\Models\WatchlistMatch;
use App\Models\Scopes\TenantScope;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Throwable;

class CheckInController extends Controller
{
    /**
     * Show the new visitor check-in form.
     */
    public function create(): View
    {
        $orgId = TenantScope::getTenantId();

        $sites = Site::where('status', 'active')->get();
        $idTypes = IdType::where('status', 'active')->get();
        if ($idTypes->isEmpty()) {
            $idTypes = IdType::all();
        }
        $countries = Country::orderBy('name')->get();
        $phoneCountries = Country::whereNotNull('isd_code')->where('isd_code', '!=', '')->orderByRaw("CASE WHEN iso_code = 'AE' THEN 1 ELSE 2 END")->orderBy('name')->get();
        $purposes = VisitPurpose::where('status', 'active')->get();
        $employees = Employee::with(['department', 'site'])
            ->where('status', 'active')
            ->get()
            ->sortBy('first_name')
            ->values();
                        $visitorTypes = VisitorType::where('status', 'active')->get();
        $departments = Department::all();
        $meetingRooms = \Illuminate\Support\Facades\Schema::hasTable('meeting_rooms')
            ? MeetingRoom::with(['site', 'floor'])->orderBy('name')->get()
            : collect([]);

        $expectedToday = Invitation::with([
            'visit.visitor',
            'visit.hostEmployee',
            'visit.purpose'
        ])->whereHas('visit', function ($q) use ($orgId) {
            $q->where('organization_id', $orgId)
              ->whereDate('scheduled_start_at', Carbon::today());
        })->where('status', '!=', 'cancelled')->get();

        return view('visitors.checkin', compact(
            'sites',
            'idTypes',
            'countries',
            'phoneCountries',
            'purposes',
            'employees',
            'visitorTypes',
            'expectedToday',
            'departments',
            'meetingRooms'
        ));
    }

    /**
     * Store a newly checked-in visitor and issue badge.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'full_name' => ['required', 'string', 'max:255', 'min:2'],
            'email' => ['nullable', 'email', 'max:254'],
            'phone' => ['nullable', 'string', 'max:32'],
            'phone_code' => ['nullable', 'string', 'max:8'],
            'nationality_id' => ['nullable', 'exists:countries,id'],
            'id_type_id' => ['required', 'exists:id_types,id'],
            'id_number' => ['required', 'string', 'max:255'],
            'company_name' => ['nullable', 'string', 'max:255'],
            'vehicle_plate' => ['nullable', 'string', 'max:255'],
            'site_id' => ['required', 'exists:sites,id'],
            'host_employee_id' => ['required', 'exists:employees,id'],
            'purpose_id' => ['required', 'exists:visit_purposes,id'],
            'duration' => ['required', 'integer', 'min:15'],
            'health_safety_ack' => ['required', 'accepted'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $orgId = TenantScope::getTenantId();

        try {
            // Format full phone number with dial code if provided
            $fullPhone = $request->phone;
            if ($request->phone && $request->phone_code) {
                $fullPhone = '+' . preg_replace('/[^0-9]/', '', $request->phone_code) . ' ' . $request->phone;
            }

            // Fetch Host Employee & Site Validation
            $hostEmployee = Employee::with(['department', 'site'])->findOrFail($validated['host_employee_id']);
            $site = Site::findOrFail($validated['site_id']);

            // Validate that the host employee belongs to the selected site location if employee has a site assigned
            if ($hostEmployee->site_id && (int) $hostEmployee->site_id !== (int) $validated['site_id']) {
                $hostSiteName = $hostEmployee->site ? $hostEmployee->site->name : 'another site';
                return back()->withInput()->with('error', "Selected host employee ({$hostEmployee->full_name}) is assigned to {$hostSiteName}. Please select a host employee belonging to {$site->name}.");
            }

            // 1. Resolve or Create Visitor Profile
            $visitor = null;
            if (!empty($validated['email']) || !empty($fullPhone)) {
                $visitor = Visitor::where('organization_id', $orgId)
                    ->where(function ($q) use ($validated, $fullPhone) {
                        if (!empty($validated['email'])) {
                            $q->where('email', $validated['email']);
                        }
                        if (!empty($fullPhone)) {
                            $q->orWhere('phone', $fullPhone);
                        }
                    })
                    ->first();
            }

            $nameParts = explode(' ', trim($validated['full_name']), 2);
            $firstName = $nameParts[0];
            $lastName = $nameParts[1] ?? null;

            if (!$visitor) {
                $visitor = Visitor::create([
                    'organization_id' => $orgId,
                    'first_name' => $firstName,
                    'last_name' => $lastName,
                    'email' => $validated['email'] ?? null,
                    'phone' => $fullPhone,
                    'nationality_id' => $validated['nationality_id'] ?? null,
                    'id_type_id' => $validated['id_type_id'],
                    'id_number' => $validated['id_number'],
                    'company_name' => $validated['company_name'] ?? null,
                    'vehicle_plate' => $validated['vehicle_plate'] ?? null,
                ]);
            } else {
                $visitor->update([
                    'first_name' => $firstName,
                    'last_name' => $lastName ?? $visitor->last_name,
                    'company_name' => $validated['company_name'] ?? $visitor->company_name,
                    'id_type_id' => $validated['id_type_id'],
                    'id_number' => $validated['id_number'],
                ]);
            }

            $departmentId = $hostEmployee->department_id;
            $defaultVisitorType = VisitorType::where('organization_id', $orgId)->first();
            if (!$defaultVisitorType) {
                $defaultVisitorType = VisitorType::create([
                    'organization_id' => $orgId,
                    'name' => 'General Visitor',
                    'status' => 'active',
                ]);
            }

            $durationMinutes = (int) $validated['duration'];
            $checkInAt = now();
            $scheduledEndAt = (clone $checkInAt)->addMinutes($durationMinutes);

            // 3. Create Visit Record
            $visit = Visit::create([
                'organization_id' => $orgId,
                'visitor_id' => $visitor->id,
                'site_id' => $validated['site_id'],
                'host_employee_id' => $hostEmployee->id,
                'department_id' => $departmentId,
                'purpose_id' => $validated['purpose_id'],
                'visitor_type_id' => $defaultVisitorType ? $defaultVisitorType->id : null,
                'scheduled_start_at' => $checkInAt,
                'scheduled_end_at' => $scheduledEndAt,
                'check_in_at' => $checkInAt,
                'status' => 'checked_in',
                'source' => 'walkin',
                'notes' => $validated['notes'] ?? null,
            ]);

            // 4. Watchlist Match Screening
            $idHash = hash('sha256', strtolower(trim($validated['id_number'])));
            $fullName = strtolower(trim($visitor->full_name));

            $watchlistEntry = WatchlistEntry::where('organization_id', $orgId)
                ->where(function ($q) use ($idHash, $fullName) {
                    $q->where('id_number_hash', $idHash)
                      ->orWhereRaw('LOWER(name) LIKE ?', ['%' . $fullName . '%']);
                })
                ->first();

            if ($watchlistEntry) {
                WatchlistMatch::create([
                    'organization_id' => $orgId,
                    'visit_id' => $visit->id,
                    'watchlist_entry_id' => $watchlistEntry->id,
                    'match_type' => $watchlistEntry->id_number_hash === $idHash ? 'exact_id' : 'fuzzy_name',
                    'score' => 0.9500,
                    'decision' => 'pending',
                ]);
            }

            // 5. Issue Badge
            $badgeTemplate = BadgeTemplate::where('organization_id', $orgId)->first();
            if (!$badgeTemplate) {
                $badgeTemplate = BadgeTemplate::create([
                    'organization_id' => $orgId,
                    'name' => 'Default Badge Template',
                    'layout_json' => ['style' => 'gradient', 'color' => '#0c6b64'],
                    'status' => 'active',
                ]);
            }

            $badgeNumber = 'MDK-' . strtoupper(Str::random(5));
            $badge = Badge::create([
                'organization_id' => $orgId,
                'visit_id' => $visit->id,
                'badge_number' => $badgeNumber,
                'template_id' => $badgeTemplate->id,
                'issued_at' => $checkInAt,
                'expires_at' => $scheduledEndAt,
                'status' => 'issued',
            ]);

            return redirect()->route('visitors.index')->with([
                'success' => 'Visitor successfully checked in!',
                'badge_id' => $badge->id,
                'badge_number' => $badgeNumber,
                'visitor_name' => $visitor->full_name,
                'host_name' => $hostEmployee->full_name,
            ]);
        } catch (Throwable $e) {
            return back()->withInput()->with('error', 'Unable to complete check-in: ' . $e->getMessage());
        }
    }

    /**
     * Check out a visitor.
     */
    public function checkout(Visit $visit): RedirectResponse
    {
        $visit->update([
            'status' => 'checked_out',
            'check_out_at' => now(),
        ]);

        return back()->with('success', 'Visitor successfully checked out.');
    }
}
