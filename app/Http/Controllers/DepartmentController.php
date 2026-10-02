<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\Employee;
use App\Models\Scopes\TenantScope;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DepartmentController extends Controller
{
    /**
     * Display a listing of departments.
     */
    public function index(): View
    {
        $departments = Department::with(['headEmployee', 'floors'])
            ->withCount('employees')
            ->orderBy('name')
            ->get();

        $employees = Employee::whereIn('status', ['active', 'on_leave'])
            ->orderBy('first_name')
            ->get();

        return view('departments.index', compact('departments', 'employees'));
    }

    /**
     * Store a newly created department.
     */
    public function store(Request $request): RedirectResponse
    {
        $orgId = TenantScope::getTenantId();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'code' => ['nullable', 'string', 'max:50'],
            'description' => ['nullable', 'string', 'max:500'],
            'head_employee_id' => ['nullable', 'exists:employees,id'],
            'contact_email' => ['nullable', 'email', 'max:255'],
            'contact_phone' => ['nullable', 'string', 'max:50'],
            'status' => ['required', 'string', 'in:active,inactive'],
        ]);

        $code = $request->input('code') ?: strtoupper(substr(preg_replace('/[^a-zA-Z0-9]/', '', $validated['name']), 0, 5));

        $department = Department::create(array_merge($validated, [
            'organization_id' => $orgId,
            'code' => $code,
            'icon_key' => $request->input('icon_key', 'briefcase'),
            'kiosk_selectable' => $request->has('kiosk_selectable'),
            'require_nda' => $request->has('require_nda'),
            'escort_required' => $request->has('escort_required'),
        ]));

        $syncData = [];
        if ($request->filled('floor_ids')) {
            $validFloorIds = array_filter($request->input('floor_ids'));
            $index = 0;
            foreach ($validFloorIds as $fId) {
                $syncData[$fId] = ['is_primary' => ($index === 0)];
                $index++;
            }
        }
        $department->floors()->sync($syncData);

        return back()->with('success', 'Department created successfully.');
    }

    /**
     * Update the specified department.
     */
    public function update(Request $request, Department $department): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'code' => ['nullable', 'string', 'max:50'],
            'description' => ['nullable', 'string', 'max:500'],
            'head_employee_id' => ['nullable', 'exists:employees,id'],
            'contact_email' => ['nullable', 'email', 'max:255'],
            'contact_phone' => ['nullable', 'string', 'max:50'],
            'status' => ['required', 'string', 'in:active,inactive'],
        ]);
        
        $code = $request->input('code') ?: strtoupper(substr(preg_replace('/[^a-zA-Z0-9]/', '', $validated['name']), 0, 5));

        $department->update(array_merge($validated, [
            'code' => $code,
            'icon_key' => $request->input('icon_key', $department->icon_key ?? 'briefcase'),
            'kiosk_selectable' => $request->has('kiosk_selectable'),
            'require_nda' => $request->has('require_nda'),
            'escort_required' => $request->has('escort_required'),
        ]));

        $syncData = [];
        if ($request->filled('floor_ids')) {
            $validFloorIds = array_filter($request->input('floor_ids'));
            $index = 0;
            foreach ($validFloorIds as $fId) {
                $syncData[$fId] = ['is_primary' => ($index === 0)];
                $index++;
            }
        }
        $department->floors()->sync($syncData);

        return back()->with('success', 'Department updated successfully.');
    }

    /**
     * Remove the specified department.
     */
    public function destroy(Department $department): RedirectResponse
    {
        if ($department->employees()->exists()) {
            return back()->with('error', 'Cannot delete department — active employees are assigned to this department.');
        }

        $department->delete();

        return back()->with('success', 'Department deleted successfully.');
    }
}
