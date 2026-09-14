<?php

namespace App\Http\Controllers;

use App\Models\Building;
use App\Models\Floor;
use App\Models\IdType;
use App\Models\Scopes\TenantScope;
use App\Models\Shift;
use App\Models\VisitPurpose;
use App\Models\VisitorType;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LookupController extends Controller
{
    /**
     * Display tabbed lookup settings.
     */
    public function index(Request $request): View
    {
        $tab = $request->get('tab', 'purposes');

        $visitPurposes = VisitPurpose::orderBy('name')->get();
        $idTypes = IdType::orderBy('name')->get();
        $visitorTypes = VisitorType::orderBy('name')->get();
        $shifts = Shift::orderBy('name')->get();
        $floors = Floor::with('building')->orderBy('name')->get();
        $buildings = Building::orderBy('name')->get();

        return view('lookups.index', compact(
            'tab',
            'visitPurposes',
            'idTypes',
            'visitorTypes',
            'shifts',
            'floors',
            'buildings'
        ));
    }

    // --- VISIT PURPOSES ---
    public function storePurpose(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'status' => ['required', 'string', 'in:active,inactive'],
        ]);

        VisitPurpose::create(array_merge($validated, ['organization_id' => TenantScope::getTenantId()]));

        return redirect()->route('lookups.index', ['tab' => 'purposes'])->with('success', 'Visit purpose created successfully.');
    }

    public function updatePurpose(Request $request, VisitPurpose $purpose): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'status' => ['required', 'string', 'in:active,inactive'],
        ]);

        $purpose->update($validated);

        return redirect()->route('lookups.index', ['tab' => 'purposes'])->with('success', 'Visit purpose updated successfully.');
    }

    // --- ID TYPES ---
    public function storeIdType(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'code' => ['required', 'string', 'max:50'],
            'status' => ['required', 'string', 'in:active,inactive'],
        ]);

        IdType::create($validated);

        return redirect()->route('lookups.index', ['tab' => 'id_types'])->with('success', 'ID Type created successfully.');
    }

    public function updateIdType(Request $request, IdType $idType): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'code' => ['required', 'string', 'max:50'],
            'status' => ['required', 'string', 'in:active,inactive'],
        ]);

        $idType->update($validated);

        return redirect()->route('lookups.index', ['tab' => 'id_types'])->with('success', 'ID Type updated successfully.');
    }

    // --- VISITOR TYPES ---
    public function storeVisitorType(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'status' => ['required', 'string', 'in:active,inactive'],
        ]);

        VisitorType::create([
            'organization_id' => TenantScope::getTenantId(),
            'name' => $validated['name'],
            'status' => $validated['status'],
            'requires_approval' => $request->has('requires_approval'),
            'requires_nda' => $request->has('requires_nda'),
            'requires_escort' => $request->has('requires_escort'),
        ]);

        return redirect()->route('lookups.index', ['tab' => 'visitor_types'])->with('success', 'Visitor category created successfully.');
    }

    public function updateVisitorType(Request $request, VisitorType $visitorType): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'status' => ['required', 'string', 'in:active,inactive'],
        ]);

        $visitorType->update([
            'name' => $validated['name'],
            'status' => $validated['status'],
            'requires_approval' => $request->has('requires_approval'),
            'requires_nda' => $request->has('requires_nda'),
            'requires_escort' => $request->has('requires_escort'),
        ]);

        return redirect()->route('lookups.index', ['tab' => 'visitor_types'])->with('success', 'Visitor category updated successfully.');
    }

    // --- SHIFTS ---
    public function storeShift(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'start_time' => ['required', 'string'],
            'end_time' => ['required', 'string'],
            'grace_minutes' => ['nullable', 'integer', 'min:0'],
            'status' => ['required', 'string', 'in:active,inactive'],
        ]);

        Shift::create(array_merge($validated, ['organization_id' => TenantScope::getTenantId()]));

        return redirect()->route('lookups.index', ['tab' => 'shifts'])->with('success', 'Shift schedule created successfully.');
    }

    public function updateShift(Request $request, Shift $shift): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'start_time' => ['required', 'string'],
            'end_time' => ['required', 'string'],
            'grace_minutes' => ['nullable', 'integer', 'min:0'],
            'status' => ['required', 'string', 'in:active,inactive'],
        ]);

        $shift->update($validated);

        return redirect()->route('lookups.index', ['tab' => 'shifts'])->with('success', 'Shift schedule updated successfully.');
    }

    // --- FLOORS ---
    public function storeFloor(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'building_id' => ['nullable', 'exists:buildings,id'],
            'name' => ['required', 'string', 'max:255'],
            'code' => ['required', 'string', 'max:50'],
            'capacity' => ['nullable', 'integer', 'min:0'],
            'status' => ['required', 'string', 'in:active,inactive'],
        ]);

        Floor::create(array_merge($validated, ['organization_id' => TenantScope::getTenantId()]));

        return redirect()->route('lookups.index', ['tab' => 'floors'])->with('success', 'Building floor created successfully.');
    }

    public function updateFloor(Request $request, Floor $floor): RedirectResponse
    {
        $validated = $request->validate([
            'building_id' => ['nullable', 'exists:buildings,id'],
            'name' => ['required', 'string', 'max:255'],
            'code' => ['required', 'string', 'max:50'],
            'capacity' => ['nullable', 'integer', 'min:0'],
            'status' => ['required', 'string', 'in:active,inactive'],
        ]);

        $floor->update($validated);

        return redirect()->route('lookups.index', ['tab' => 'floors'])->with('success', 'Building floor updated successfully.');
    }
}
