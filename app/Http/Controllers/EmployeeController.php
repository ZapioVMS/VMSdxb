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
        $query = Employee::with(['site', 'department', 'shift', 'manager'])
            ->orderBy('first_name');

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

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        $employees = $query->paginate(15)->withQueryString();

        $sites = Site::orderBy('name')->get();
        $departments = Department::orderBy('name')->get();
        $shifts = Shift::orderBy('name')->get();
        $managers = Employee::where('status', 'active')->orderBy('first_name')->get();

        return view('employees.index', compact('employees', 'sites', 'departments', 'shifts', 'managers'));
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
            'status' => ['required', 'string', 'in:active,inactive,on_leave'],
        ]);

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
            'status' => ['required', 'string', 'in:active,inactive,on_leave'],
        ]);

        $employee->update($validated);

        return back()->with('success', 'Employee profile updated successfully.');
    }

    /**
     * Toggle leave status for an employee.
     */
    public function toggleLeave(Employee $employee): RedirectResponse
    {
        $newStatus = $employee->status === 'on_leave' ? 'active' : 'on_leave';
        $employee->update(['status' => $newStatus]);

        $msg = $newStatus === 'on_leave' ? 'Employee marked as On Leave.' : 'Employee returned to Active status.';
        return back()->with('success', $msg);
    }

    /**
     * Remove the specified employee.
     */
    public function destroy(Employee $employee): RedirectResponse
    {
        if ($employee->hostedVisits()->whereIn('status', ['checked_in', 'expected'])->exists()) {
            return back()->with('error', 'Cannot delete employee — active or expected visitor logs are linked to this host.');
        }

        $employee->delete();

        return back()->with('success', 'Employee deleted successfully.');
    }
}
