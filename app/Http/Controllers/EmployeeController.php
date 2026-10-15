<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\Employee;
use App\Models\Scopes\TenantScope;
use App\Models\Site;
use App\Models\Shift;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EmployeeController extends Controller
{
    /**
     * Display a listing of employees.
     */
    public function index(Request $request): View
    {
        // Treat empty values and the top-bar "all" value as "no filter".
        $hasFilter = fn (string $key) => $request->filled($key) && $request->input($key) !== 'all';

        $query = Employee::with(['site', 'department', 'shift', 'manager', 'leaves', 'attendanceRecords' => function ($q) {
            $q->whereDate('attendance_date', today());
        }])->orderBy('first_name');

        if ($hasFilter('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'ILIKE', "%{$search}%")
                  ->orWhere('last_name', 'ILIKE', "%{$search}%")
                  ->orWhere('email', 'ILIKE', "%{$search}%")
                  ->orWhere('employee_code', 'ILIKE', "%{$search}%");
            });
        }

        if ($hasFilter('site_id')) {
            $query->where('site_id', $request->input('site_id'));
        }

        if ($hasFilter('department_id')) {
            $query->where('department_id', $request->input('department_id'));
        }

        if ($hasFilter('shift_id')) {
            $query->where('shift_id', $request->input('shift_id'));
        }

        $tab = $request->get('tab', 'directory');

        // Work out today's attendance status for every employee matching the filters,
        // so pill counts always reflect the current search/branch/department/shift.
        $filtered = $query->get()->each(function (Employee $emp) {
            $emp->setAttribute('today_status', $this->todayStatus($emp));
        });

        $counts = [
            'all' => $filtered->count(),
            'onsite' => $filtered->where('today_status', 'active')->count(),
            'late' => $filtered->where('today_status', 'late')->count(),
            'early' => $filtered->where('today_status', 'early')->count(),
            'complete' => $filtered->where('today_status', 'complete')->count(),
            'absent' => $filtered->where('today_status', 'absent')->count(),
            'leave' => $filtered->where('today_status', 'on_leave')->count(),
        ];

        $status = $request->input('status', 'all');
        if ($status && $status !== 'all') {
            $filtered = $filtered->where('today_status', $status)->values();
        }

        $perPage = in_array((int) $request->input('per_page'), [25, 50, 100], true) ? (int) $request->input('per_page') : 25;
        $page = LengthAwarePaginator::resolveCurrentPage();
        $employees = new LengthAwarePaginator(
            $filtered->forPage($page, $perPage)->values(),
            $filtered->count(),
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        $sites = Site::orderBy('name')->get();
        $departments = Department::orderBy('name')->get();
        $shifts = Shift::orderBy('name')->get();
        $managers = Employee::where('status', 'active')->orderBy('first_name')->get();
        $allEmployees = Employee::with(['site', 'department', 'shift', 'manager'])->orderBy('first_name')->get();

                $reportRecords = collect();
        $reportKpis = [
            'present' => 0,
            'absent' => 0,
            'leave' => 0,
            'hours' => '0h 0m'
        ];
        $selectedReportEmployee = null;
        $reportEmployeeId = 'all';
        $reportDateRange = 'all_time';

        if ($tab === 'report') {
            $reportEmployeeId = $request->get('report_employee_id', 'all');
            $reportDateRange = $request->get('report_date_range', 'all_time');

            $dateFrom = today()->startOfYear();
            if ($reportDateRange === 'all_time') {
                $dateFrom = \Carbon\Carbon::parse('1970-01-01');
            }
            $dateTo = today()->endOfDay();

            if ($reportDateRange === 'today') {
                $dateFrom = today();
            } elseif ($reportDateRange === 'this_week') {
                $dateFrom = today()->startOfWeek();
            } elseif ($reportDateRange === 'this_month') {
                $dateFrom = today()->startOfMonth();
            }

            $attQuery = \App\Models\AttendanceRecord::with('employee.department', 'employee.site')
                ->whereBetween('attendance_date', [$dateFrom->format('Y-m-d'), $dateTo->format('Y-m-d')]);

            if ($reportEmployeeId !== 'all') {
                $attQuery->where('employee_id', $reportEmployeeId);
                $selectedReportEmployee = $allEmployees->firstWhere('id', $reportEmployeeId);
            }

            $reportRecords = $attQuery->orderBy('attendance_date', 'desc')->get();

            // Calculate KPIs
            $presentDays = $reportRecords->pluck('attendance_date')->unique()->count();
            
            // Very simple absent logic: working days minus present. (5 days a week)
            $workingDays = $dateFrom->diffInDaysFiltered(function (\Carbon\Carbon $date) {
                return $date->isWeekday();
            }, min($dateTo, today()));
            
            $absentDays = max(0, $workingDays - $presentDays);

            // Calculate Total Hours
            $totalMinutes = 0;
            foreach ($reportRecords as $rec) {
                if ($rec->check_in_at && $rec->check_out_at) {
                    $totalMinutes += \Carbon\Carbon::parse($rec->check_in_at)->diffInMinutes(\Carbon\Carbon::parse($rec->check_out_at));
                }
            }
            $hrs = floor($totalMinutes / 60);
            $mins = $totalMinutes % 60;
            $hoursStr = "{$hrs}h {$mins}m";

            $reportKpis = [
                'present' => $presentDays,
                'absent' => $absentDays,
                'leave' => 0,
                'hours' => $hoursStr
            ];
        }

        return view('employees.index', compact('employees', 'allEmployees', 'sites', 'departments', 'shifts', 'managers', 'tab', 'counts', 'reportRecords', 'reportKpis', 'selectedReportEmployee', 'reportDateRange', 'reportEmployeeId'));
    }

    /**
     * Shifts → Manage Employees: save which employees work this shift.
     * Ticked employees are moved to this shift; employees who were on it but are no longer ticked become unassigned.
     * POST /shifts/{shift}/employees
     */
    public function assignShift(Request $request, Shift $shift): RedirectResponse
    {
        $validated = $request->validate([
            'employee_ids' => ['nullable', 'array'],
            'employee_ids.*' => ['integer'],
        ]);

        $ids = collect($validated['employee_ids'] ?? [])->map(fn ($id) => (int) $id)->unique()->values();

        $added = Employee::whereIn('id', $ids)
            ->where(fn ($q) => $q->whereNull('shift_id')->orWhere('shift_id', '!=', $shift->id))
            ->update(['shift_id' => $shift->id]);

        $removed = Employee::where('shift_id', $shift->id)
            ->whereNotIn('id', $ids->all() ?: [0])
            ->update(['shift_id' => null]);

        $total = Employee::where('shift_id', $shift->id)->count();

        $parts = [];
        if ($added) {
            $parts[] = $added . ' added';
        }
        if ($removed) {
            $parts[] = $removed . ' removed';
        }

        return redirect()->route('employees.index', ['tab' => 'shifts'])->with('success',
            $shift->name . ': ' . $total . ' ' . ($total === 1 ? 'employee' : 'employees') . ' assigned'
            . ($parts ? ' (' . implode(', ', $parts) . ').' : '. No changes.'));
    }

    /**
     * Badge Check-In: find employees by Employee ID (exact match first) or name, with today's status.
     * GET /employees/badge/lookup?q=EMP-001
     */
    public function badgeLookup(Request $request)
    {
        $q = trim((string) $request->query('q', ''));
        if ($q === '') {
            return response()->json(['matches' => []]);
        }

        $needle = strtolower($q);
        $employees = Employee::with(['department', 'shift', 'leaves', 'attendanceRecords' => function ($r) {
                $r->whereDate('attendance_date', today())->orderByDesc('check_in_at');
            }])
            ->where(function ($w) use ($needle) {
                $w->whereRaw('LOWER(employee_code) LIKE ?', ['%' . $needle . '%'])
                  ->orWhereRaw("LOWER(first_name || ' ' || COALESCE(last_name, '')) LIKE ?", ['%' . $needle . '%']);
            })
            // exact Employee ID first, then IDs starting with the text, then the rest
            ->orderByRaw('CASE WHEN LOWER(TRIM(employee_code)) = ? THEN 0 WHEN LOWER(employee_code) LIKE ? THEN 1 ELSE 2 END', [$needle, $needle . '%'])
            ->orderBy('first_name')
            ->limit(8)
            ->get();

        return response()->json([
            'matches' => $employees->map(fn (Employee $e) => $this->badgeInfo($e))->values(),
        ]);
    }

    /**
     * Badge Check-In: check an employee in, or out if they are already checked in today.
     * Accepts employee_id (picked from the search) or code (typed / scanned Employee ID),
     * so a badge scanner can later post just the scanned code here.
     * POST /employees/badge/punch
     */
    public function badgePunch(Request $request)
    {
        $request->validate([
            'employee_id' => ['nullable', 'integer'],
            'code' => ['nullable', 'string', 'max:50'],
        ]);

        $employee = null;
        if ($request->filled('employee_id')) {
            $employee = Employee::find($request->input('employee_id'));
        } elseif ($request->filled('code')) {
            $employee = Employee::whereRaw('LOWER(TRIM(employee_code)) = ?', [strtolower(trim($request->input('code')))])->first();
        }

        if (! $employee) {
            return response()->json(['success' => false, 'message' => 'No employee found with that Employee ID.'], 404);
        }

        $employee->load(['department', 'shift', 'leaves', 'attendanceRecords' => function ($r) {
            $r->whereDate('attendance_date', today())->orderByDesc('check_in_at');
        }]);
        $info = $this->badgeInfo($employee);

        if ($info['next_action'] === null) {
            return response()->json(['success' => false, 'message' => $info['blocked_reason'], 'employee' => $info], 422);
        }

        if ($info['next_action'] === 'check_out') {
            $employee->attendanceRecords()
                ->whereDate('attendance_date', today())
                ->whereNull('check_out_at')
                ->orderByDesc('check_in_at')
                ->first()
                ?->update(['check_out_at' => now()]);
            $message = $employee->full_name . ' checked out at ' . now()->format('h:i A') . '.';
        } else {
            $this->checkin($request, $employee);
            $message = $employee->full_name . ' checked in at ' . now()->format('h:i A') . '.';
        }

        $employee->load(['leaves', 'attendanceRecords' => function ($r) {
            $r->whereDate('attendance_date', today())->orderByDesc('check_in_at');
        }]);

        return response()->json(['success' => true, 'message' => $message, 'employee' => $this->badgeInfo($employee)]);
    }

    /**
     * Summary of an employee for the Badge Check-In panel, including what the next action would be.
     */
    private function badgeInfo(Employee $e): array
    {
        $status = $this->todayStatus($e);
        $open = $e->attendanceRecords->first(fn ($r) => $r->check_in_at && ! $r->check_out_at);
        $last = $e->attendanceRecords->first();

        $labels = [
            'on_leave' => 'On leave today',
            'absent' => 'Not checked in',
            'active' => 'On site',
            'late' => 'On site (late)',
            'early' => 'Checked out (early)',
            'complete' => 'Checked out',
        ];

        $next = 'check_in';
        $reason = null;
        if ($open) {
            $next = 'check_out';
        } elseif ($e->status === 'inactive') {
            $next = null;
            $reason = $e->full_name . ' is inactive and cannot be checked in.';
        } elseif ($status === 'on_leave') {
            $next = null;
            $reason = $e->full_name . ' is on leave today. Remove the leave first to check them in.';
        }

        return [
            'id' => $e->id,
            'code' => $e->employee_code,
            'name' => $e->full_name,
            'initials' => strtoupper(mb_substr($e->first_name ?? '', 0, 1) . mb_substr($e->last_name ?? '', 0, 1)),
            'department' => $e->department->name ?? null,
            'job_title' => $e->job_title,
            'shift' => $e->shift->name ?? null,
            'today_status' => $open ? ($open->status === 'late' ? 'late' : 'active') : $status,
            'status_label' => $labels[$open ? ($open->status === 'late' ? 'late' : 'active') : $status] ?? ucfirst($status),
            'check_in_at' => $last?->check_in_at ? Carbon::parse($last->check_in_at)->format('h:i A') : null,
            'check_out_at' => $last?->check_out_at ? Carbon::parse($last->check_out_at)->format('h:i A') : null,
            'next_action' => $next,
            'blocked_reason' => $reason,
        ];
    }

    /**
     * Determine an employee's attendance status for today.
     *
     * Returns one of: on_leave, absent, active (on site), late, early, complete.
     */
    private function todayStatus(Employee $emp): string
    {
        $today = today();

        if ($emp->status === 'on_leave'
            || $emp->leaves->contains(fn ($leave) => $leave->date && Carbon::parse($leave->date)->isSameDay($today))) {
            return 'on_leave';
        }

        $att = $emp->attendanceRecords->first();
        if (! $att || ! $att->check_in_at) {
            return 'absent';
        }

        if ($att->check_out_at) {
            if ($emp->shift && $emp->shift->end_time) {
                $shiftEnd = Carbon::parse($today->format('Y-m-d').' '.Carbon::parse($emp->shift->end_time)->format('H:i:s'));
                if (Carbon::parse($att->check_out_at)->lt($shiftEnd)) {
                    return 'early';
                }
            }

            return 'complete';
        }

        return $att->status === 'late' ? 'late' : 'active';
    }

    /**
     * Employee ID must be unique within the organisation (ignoring case and surrounding spaces).
     * When editing, the employee's own current ID is allowed.
     */
    private function uniqueEmployeeCodeRule(?Employee $current = null): \Closure
    {
        return function (string $attribute, $value, \Closure $fail) use ($current) {
            $code = strtolower(trim((string) $value));
            if ($code === '') {
                return;
            }

            $taken = Employee::whereRaw('LOWER(TRIM(employee_code)) = ?', [$code])
                ->when($current, fn ($q) => $q->where('id', '!=', $current->id))
                ->first();

            if ($taken) {
                $fail('Employee ID "' . trim((string) $value) . '" is already taken by ' . $taken->full_name . '. Please use a different Employee ID.');
            }
        };
    }

    /**
     * Store a newly created employee.
     */
    public function store(Request $request): RedirectResponse
    {
        $orgId = TenantScope::getTenantId();

        $validated = $request->validate([
            'employee_code' => ['required', 'string', 'max:50', $this->uniqueEmployeeCodeRule()],
            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['nullable', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'job_title' => ['nullable', 'string', 'max:150'],
            'site_id' => ['nullable', 'exists:sites,id'],
            'department_id' => ['nullable', 'exists:departments,id'],
            'shift_id' => ['nullable', 'exists:shifts,id'],
            'manager_id' => ['nullable', 'exists:employees,id'],
            'status' => ['nullable', 'string', 'in:active,inactive,on_leave'],
        ]);

        $validated['employee_code'] = trim($validated['employee_code']);
        $validated['status'] = $validated['status'] ?? 'active';

        Employee::create(array_merge($validated, ['organization_id' => $orgId]));

        return back()->with('success', 'Employee successfully registered.');
    }

    /**
     * Update the specified employee.
     */
    public function update(Request $request, Employee $employee): RedirectResponse
    {
        $validated = $request->validate([
            'employee_code' => ['required', 'string', 'max:50', $this->uniqueEmployeeCodeRule($employee)],
            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['nullable', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'job_title' => ['nullable', 'string', 'max:150'],
            'site_id' => ['nullable', 'exists:sites,id'],
            'department_id' => ['nullable', 'exists:departments,id'],
            'shift_id' => ['nullable', 'exists:shifts,id'],
            'manager_id' => ['nullable', 'exists:employees,id'],
            'status' => ['nullable', 'string', 'in:active,inactive,on_leave'],
        ]);

        $validated['employee_code'] = trim($validated['employee_code']);
        if (!isset($validated['status'])) {
            $validated['status'] = $employee->status; // preserve existing status if not provided
        }

        $employee->update($validated);

        return back()->with('success', 'Employee profile updated successfully.');
    }

    /**
     * Toggle leave status for an employee.
     */
    public function toggleLeave(Request $request, Employee $employee): RedirectResponse
    {
        $validated = $request->validate([
            'dates' => 'nullable|string',
            'action' => 'required|in:mark,remove'
        ]);

        $dates = array_filter(explode(',', $validated['dates'] ?? ''));
        $action = $validated['action'];

        if (empty($dates)) {
            return back()->with('error', 'No dates selected.');
        }

        foreach ($dates as $date) {
            if ($action === 'mark') {
                \App\Models\EmployeeLeave::updateOrCreate([
                    'employee_id' => $employee->id,
                    'date' => $date
                ]);
            } else {
                \App\Models\EmployeeLeave::where('employee_id', $employee->id)
                    ->whereDate('date', $date)
                    ->delete();
            }
        }

        // Update overall employee status based on whether they are on leave *today*
        $isOnLeaveToday = $employee->leaves()->whereDate('date', today())->exists();
        $employee->update(['status' => $isOnLeaveToday ? 'on_leave' : 'active']);

        return back()->with('success', 'Employee leave dates updated successfully.');
    }

    /**
     * Remove the specified employee.
     */
        /**
     * Get attendance history for an employee based on a filter.
     */
    public function attendance(Request $request, Employee $employee)
    {
        $filter = $request->get('filter', 'this_week');
        $startDate = null;
        $endDate = null;

        $now = now();
        if ($filter === 'this_week') {
            $startDate = $now->copy()->startOfWeek();
            $endDate = $now->copy()->endOfWeek();
        } elseif ($filter === 'this_month') {
            $startDate = $now->copy()->startOfMonth();
            $endDate = $now->copy()->endOfMonth();
        } elseif ($filter === 'this_year') {
            $startDate = $now->copy()->startOfYear();
            $endDate = $now->copy()->endOfYear();
        } elseif ($filter === 'custom') {
            $startDate = $request->get('start_date') ? \Carbon\Carbon::parse($request->get('start_date'))->startOfDay() : $now->copy()->startOfWeek();
            $endDate = $request->get('end_date') ? \Carbon\Carbon::parse($request->get('end_date'))->endOfDay() : $now->copy()->endOfWeek();
        }

        // Fetch attendance records
        $attendances = \App\Models\AttendanceRecord::where('employee_id', $employee->id)
            ->whereBetween('attendance_date', [$startDate->toDateString(), $endDate->toDateString()])
            ->orderBy('attendance_date', 'desc')
            ->get()
            ->keyBy(function($item) {
                return $item->attendance_date->format('Y-m-d');
            });

        // Fetch leave records
        $leaves = \App\Models\EmployeeLeave::where('employee_id', $employee->id)
            ->whereBetween('date', [$startDate->toDateString(), $endDate->toDateString()])
            ->get()
            ->keyBy(function($item) {
                return $item->date->format('Y-m-d');
            });

        // We want to return a list of dates from $endDate down to $startDate
        $results = [];
        $currentDate = $endDate->copy();
        
        // Prevent massive loops if range is too large (cap at 366 days)
        $maxDays = 366;
        $daysCount = 0;

        while ($currentDate->gte($startDate) && $daysCount < $maxDays) {
            // We only show days up to today, future days are not attendance yet (unless they are leaves)
            // Wait, maybe we show future leaves?
            // Actually, we should probably only generate dates up to min($endDate, today()) 
            // OR if it's a leave, we show it. For simplicity, just output up to min(endDate, today) unless it's a leave.
            $dateString = $currentDate->format('Y-m-d');
            $isFuture = $currentDate->isFuture();

            $att = $attendances->get($dateString);
            $leave = $leaves->get($dateString);

            if (!$isFuture || $leave) {
                $statusHtml = '—';
                $checkIn = '—';
                $checkOut = '—';
                $hours = '—';

                if ($leave) {
                    $statusHtml = '<span class="chip" style="background:#e9e0f3; color:#5b3c88; border-color:#d4c4e4;">
                                     <span style="display:inline-block;width:6px;height:6px;border-radius:50%;background:#5b3c88;margin-right:4px;"></span>On Leave
                                   </span>';
                } elseif ($att) {
                    $checkIn = $att->check_in_at ? $att->check_in_at->format('h:i A') : '—';
                    $checkOut = $att->check_out_at ? $att->check_out_at->format('h:i A') : '—';
                    if ($att->check_in_at && $att->check_out_at) {
                        $diff = $att->check_in_at->diff($att->check_out_at);
                        $hours = $diff->h . 'h ' . $diff->i . 'm';
                    }
                    if ($att->status === 'present') {
                        $statusHtml = '<span class="chip chip-good">Present</span>';
                    } else {
                        $statusHtml = '<span class="chip chip-muted">' . ucfirst($att->status) . '</span>';
                    }
                } else {
                    if ($currentDate->isWeekday()) {
                        // Absent if past weekday
                        $statusHtml = '<span class="chip chip-danger">Absent</span>';
                    } else {
                        $statusHtml = '<span class="chip chip-muted">Weekend</span>';
                    }
                }

                $results[] = [
                    'date_formatted' => $currentDate->format('d M Y'),
                    'check_in' => $checkIn,
                    'check_out' => $checkOut,
                    'hours' => $hours,
                    'status_html' => $statusHtml
                ];
            }
            
            $currentDate->subDay();
            $daysCount++;
        }

        return response()->json($results);
    }

    public function destroy(Employee $employee): RedirectResponse
    {
        if ($employee->hostedVisits()->whereIn('status', ['checked_in', 'expected'])->exists()) {
            return back()->with('error', 'Cannot delete employee — active or expected visitor logs are linked to this host.');
        }

        $employee->delete();

        return back()->with('success', 'Employee deleted successfully.');
    }

    public function checkin(Request $request, Employee $employee)
    {
        $today = today();
        
        // Prevent double checkin
        $existing = $employee->attendanceRecords()->whereDate('attendance_date', $today)->first();
        if ($existing && !$existing->check_out_at) {
            return response()->json(['success' => false, 'message' => 'Already checked in']);
        }

        // Determine if late
        $status = 'present';
        if ($employee->shift && $employee->shift->start_time) {
            $shiftStart = \Carbon\Carbon::parse($employee->shift->start_time);
            $grace = $employee->shift->grace_minutes ?? 0;
            $allowedTime = $shiftStart->copy()->addMinutes($grace);
            
            // Note: Since shift start_time is a time only, we apply it to today
            $shiftStartToday = \Carbon\Carbon::parse($today->format('Y-m-d') . ' ' . $shiftStart->format('H:i:s'));
            $allowedTimeToday = $shiftStartToday->copy()->addMinutes($grace);

            if (now()->greaterThan($allowedTimeToday)) {
                $status = 'late';
            }
        }

        $record = $employee->attendanceRecords()->create([
            'organization_id' => $employee->organization_id,
            'site_id' => $employee->site_id ?? \App\Models\Site::first()->id ?? 1,
            'attendance_date' => $today,
            'check_in_at' => now(),
            'status' => $status,
            'source' => 'web'
        ]);

        return response()->json(['success' => true, 'record' => $record]);
    }

    public function checkout(Request $request, Employee $employee)
    {
        $today = today();
        $record = $employee->attendanceRecords()->whereDate('attendance_date', $today)->whereNull('check_out_at')->first();
        
        if (!$record) {
            return response()->json(['success' => false, 'message' => 'No active check-in found']);
        }

        $record->update([
            'check_out_at' => now()
        ]);

        return response()->json(['success' => true, 'record' => $record]);
    }
}