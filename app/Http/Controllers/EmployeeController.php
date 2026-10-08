<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\Employee;
use App\Models\Scopes\TenantScope;
use App\Models\Site;
use App\Models\Shift;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EmployeeController extends Controller
{
    /**
     * Display a listing of employees.
     */
    public function index(Request $request): View
    {
        $query = Employee::with(['site', 'department', 'shift', 'manager', 'leaves', 'attendanceRecords' => function ($q) {
            $q->whereDate('attendance_date', today());
        }])->orderBy('first_name');

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'ILIKE', "%{$search}%")
                  ->orWhere('last_name', 'ILIKE', "%{$search}%")
                  ->orWhere('email', 'ILIKE', "%{$search}%")
                  ->orWhere('employee_code', 'ILIKE', "%{$search}%");
            });
        }

        if ($request->filled('site_id')) {
            $query->where('site_id', $request->input('site_id'));
        }

        if ($request->filled('department_id')) {
            $query->where('department_id', $request->input('department_id'));
        }

        if ($request->filled('shift_id')) {
            $query->where('shift_id', $request->input('shift_id'));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        $tab = $request->get('tab', 'directory');
        $employees = $query->paginate(25)->withQueryString();

        $sites = Site::orderBy('name')->get();
        $departments = Department::orderBy('name')->get();
        $shifts = Shift::orderBy('name')->get();
        $managers = Employee::where('status', 'active')->orderBy('first_name')->get();
        $allEmployees = Employee::with(['site', 'department', 'shift', 'manager'])->orderBy('first_name')->get();

        $counts = [
            'all' => $allEmployees->count(),
            'onsite' => $allEmployees->where('status', 'active')->count(),
            'late' => 6,
            'early' => 0,
            'complete' => 0,
            'absent' => 5,
            'leave' => $allEmployees->where('status', 'on_leave')->count(),
        ];

        return view('employees.index', compact('employees', 'allEmployees', 'sites', 'departments', 'shifts', 'managers', 'tab', 'counts'));
    }

    /**
     * Store a newly created employee.
     */
    public function store(Request $request): RedirectResponse
    {
        $orgId = TenantScope::getTenantId();

        $validated = $request->validate([
            'employee_code' => ['required', 'string', 'max:50'],
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
            'employee_code' => ['required', 'string', 'max:50'],
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