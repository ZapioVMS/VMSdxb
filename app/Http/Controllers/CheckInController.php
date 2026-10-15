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
use Illuminate\Support\Facades\DB;
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

            // A visitor can check in again on the same day only after checking out of the open visit
            $openVisit = $this->findOpenVisitToday(
                $visitor?->id,
                $validated['email'] ?? null,
                $fullPhone,
                $validated['id_number']
            );
            if ($openVisit) {
                return back()->withInput()->with('error', $this->alreadyCheckedInMessage($openVisit));
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
     * Check in a pre-registered visitor straight from their invitation (one click).
     * Used by the "Check In" button on the Pre-Registration list and on the Check-In page's
     * "Pre-Registered Visitors Expected Today" table.
     */
    public function checkInInvitation(Invitation $invitation): RedirectResponse
    {
        $invitation->load(['visit.visitor', 'visit.hostEmployee']);
        $visit = $invitation->visit;
        $visitor = $visit?->visitor;

        if (!$visit || !$visitor) {
            return back()->with('error', 'Unable to check in: the visit record for this pre-registration was not found.');
        }

        if ($invitation->status === 'cancelled' || $visit->status === 'cancelled') {
            return back()->with('error', 'This pre-registration has been cancelled and cannot be checked in.');
        }

        if ($invitation->status === 'checked_in' || in_array($visit->status, ['checked_in', 'checked_out'], true)) {
            return back()->with('error', $visitor->full_name . ' has already been checked in.');
        }

        if (!$visit->scheduled_start_at || !$visit->scheduled_start_at->isToday()) {
            return back()->with('error', 'Only pre-registrations scheduled for today can be checked in.');
        }

        $openVisit = $this->findOpenVisitToday($visitor->id, $visitor->email, $visitor->phone, $visitor->id_number, $visit->id);
        if ($openVisit) {
            return back()->with('error', $this->alreadyCheckedInMessage($openVisit));
        }

        try {
            $badgeNumber = DB::transaction(function () use ($invitation, $visit, $visitor) {
                $orgId = $visit->organization_id;
                $checkInAt = now();

                // Keep the planned end time if it is still in the future; otherwise (late arrival)
                // give the visitor their booked duration from now so they are not flagged as overstayed.
                $scheduledEndAt = ($visit->scheduled_end_at && $visit->scheduled_end_at->gt($checkInAt))
                    ? $visit->scheduled_end_at
                    : (clone $checkInAt)->addMinutes((int) ($invitation->duration ?: 60));

                $visit->update([
                    'status' => 'checked_in',
                    'check_in_at' => $checkInAt,
                    'scheduled_end_at' => $scheduledEndAt,
                ]);

                $invitation->update(['status' => 'checked_in']);

                // Watchlist screening (same rules as walk-in check-in)
                $idHash = $visitor->id_number ? hash('sha256', strtolower(trim($visitor->id_number))) : null;
                $fullName = strtolower(trim($visitor->full_name));

                $watchlistEntry = WatchlistEntry::where('organization_id', $orgId)
                    ->where(function ($q) use ($idHash, $fullName) {
                        if ($idHash) {
                            $q->where('id_number_hash', $idHash);
                        }
                        $q->orWhereRaw('LOWER(name) LIKE ?', ['%' . $fullName . '%']);
                    })
                    ->first();

                if ($watchlistEntry) {
                    WatchlistMatch::create([
                        'organization_id' => $orgId,
                        'visit_id' => $visit->id,
                        'watchlist_entry_id' => $watchlistEntry->id,
                        'match_type' => ($idHash && $watchlistEntry->id_number_hash === $idHash) ? 'exact_id' : 'fuzzy_name',
                        'score' => 0.9500,
                        'decision' => 'pending',
                    ]);
                }

                // Issue badge (skip if one was already issued for this visit)
                $existingBadge = Badge::where('visit_id', $visit->id)->first();
                if ($existingBadge) {
                    return $existingBadge->badge_number;
                }

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
                Badge::create([
                    'organization_id' => $orgId,
                    'visit_id' => $visit->id,
                    'badge_number' => $badgeNumber,
                    'template_id' => $badgeTemplate->id,
                    'issued_at' => $checkInAt,
                    'expires_at' => $scheduledEndAt,
                    'status' => 'issued',
                ]);

                return $badgeNumber;
            });
        } catch (Throwable $e) {
            return back()->with('error', 'Unable to complete check-in: ' . $e->getMessage());
        }

        return back()->with('success', $visitor->full_name . ' checked in successfully. Badge ' . $badgeNumber . ' issued.');
    }

    /**
     * Find a visit checked in today that has not been checked out yet for the same person.
     * Same person = same visitor record, or same email, phone or ID number (ID numbers are
     * stored encrypted, so they are compared in PHP on the small set of open visits).
     */
    private function findOpenVisitToday(?int $visitorId, ?string $email, ?string $phone, ?string $idNumber, ?int $excludeVisitId = null): ?Visit
    {
        $normEmail = fn ($v) => strtolower(trim((string) $v));
        $normPhone = fn ($v) => preg_replace('/\D+/', '', (string) $v);          // digits only
        $normId = fn ($v) => strtolower(preg_replace('/[^A-Za-z0-9]+/', '', (string) $v)); // ignore dashes/spaces
        $email = $normEmail($email);
        $phone = $normPhone($phone);
        $idNumber = $normId($idNumber);

        $openVisits = Visit::with(['visitor', 'badges'])
            ->where('status', 'checked_in')
            ->whereNull('check_out_at')
            ->whereDate('check_in_at', Carbon::today())
            ->when($excludeVisitId, fn ($q) => $q->where('id', '!=', $excludeVisitId))
            ->get();

        return $openVisits->first(function (Visit $open) use ($visitorId, $email, $phone, $idNumber, $normEmail, $normPhone, $normId) {
            $v = $open->visitor;
            if (!$v) {
                return false;
            }
            return ($visitorId && (int) $v->id === (int) $visitorId)
                || ($email !== '' && $normEmail($v->email) === $email)
                || (strlen($phone) >= 6 && $normPhone($v->phone) === $phone)
                || ($idNumber !== '' && $normId($v->id_number) === $idNumber);
        });
    }

    private function alreadyCheckedInMessage(Visit $openVisit): string
    {
        $name = $openVisit->visitor?->full_name ?? 'This visitor';
        $since = $openVisit->check_in_at ? ' since ' . $openVisit->check_in_at->format('h:i A') : '';
        $badge = $openVisit->badges->first()?->badge_number;

        return $name . ' is already checked in today' . $since . ($badge ? ' (badge ' . $badge . ')' : '')
            . '. Please check them out before checking them in again.';
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