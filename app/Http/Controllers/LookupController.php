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
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
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
            'name' => [
                'required', 'string', 'max:255',
                Rule::unique('visit_purposes', 'name')->where('organization_id', TenantScope::getTenantId())
            ],
            'status' => ['required', 'string', 'in:active,inactive'],
        ]);

        VisitPurpose::create(array_merge($validated, ['organization_id' => TenantScope::getTenantId()]));

        return redirect()->route('settings.index', ['tab' => 'purposes'])->with('success', 'Visit purpose created successfully.');
    }

    public function updatePurpose(Request $request, VisitPurpose $purpose): RedirectResponse
    {
        $validated = $request->validate([
            'name' => [
                'required', 'string', 'max:255',
                Rule::unique('visit_purposes', 'name')
                    ->where('organization_id', TenantScope::getTenantId())
                    ->ignore($purpose->id)
            ],
            'status' => ['required', 'string', 'in:active,inactive'],
        ]);

        $purpose->update($validated);

        return redirect()->route('settings.index', ['tab' => 'purposes'])->with('success', 'Visit purpose updated successfully.');
    }

    public function destroyPurpose(VisitPurpose $purpose): RedirectResponse
    {
        if ($purpose->visits()->exists()) {
            return back()->with('error', 'Cannot delete purpose — visitor records exist for this purpose.');
        }

        $purpose->delete();

        return redirect()->route('settings.index', ['tab' => 'purposes'])->with('success', 'Visit purpose deleted successfully.');
    }

    // --- ID TYPES ---
    public function storeIdType(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('id_types', 'name')],
            'status' => ['required', 'string', 'in:active,inactive'],
        ]);

        $validated['code'] = Str::slug($validated['name']);

        IdType::create($validated);

        return redirect()->route('settings.index', ['tab' => 'idTypes'])->with('success', 'ID Type created successfully.');
    }

    public function updateIdType(Request $request, IdType $idType): RedirectResponse
    {
        $validated = $request->request->all();
        $validated = $request->validate([
            'name' => [
                'required', 'string', 'max:255',
                Rule::unique('id_types', 'name')->ignore($idType->id)
            ],
            'status' => ['required', 'string', 'in:active,inactive'],
        ]);
        
        $validated['code'] = Str::slug($validated['name']);

        $idType->update($validated);

        return redirect()->route('settings.index', ['tab' => 'idTypes'])->with('success', 'ID Type updated successfully.');
    }

    public function destroyIdType(IdType $idType): RedirectResponse
    {
        if ($idType->visitors()->exists()) {
            return redirect()->route('settings.index', ['tab' => 'idTypes'])->with('error', 'Cannot delete ID type because it is being used by existing visitors.');
        }

        $idType->delete();

        return redirect()->route('settings.index', ['tab' => 'idTypes'])->with('success', 'ID Type deleted successfully.');
    }

    // --- VISITOR TYPES ---
    public function storeVisitorType(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => [
                'required', 'string', 'max:255',
                Rule::unique('visitor_types', 'name')->where('organization_id', TenantScope::getTenantId())
            ],
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

        return redirect()->route('settings.index', ['tab' => 'visitorTypes'])->with('success', 'Visitor category created successfully.');
    }

    public function updateVisitorType(Request $request, VisitorType $visitorType): RedirectResponse
    {
        $validated = $request->validate([
            'name' => [
                'required', 'string', 'max:255',
                Rule::unique('visitor_types', 'name')
                    ->where('organization_id', TenantScope::getTenantId())
                    ->ignore($visitorType->id)
            ],
            'status' => ['required', 'string', 'in:active,inactive'],
        ]);

        $visitorType->update([
            'name' => $validated['name'],
            'status' => $validated['status'],
            'requires_approval' => $request->has('requires_approval'),
            'requires_nda' => $request->has('requires_nda'),
            'requires_escort' => $request->has('requires_escort'),
        ]);

        return redirect()->route('settings.index', ['tab' => 'visitorTypes'])->with('success', 'Visitor category updated successfully.');
    }

    public function destroyVisitorType(VisitorType $visitorType): RedirectResponse
    {
        if ($visitorType->visits()->exists()) {
            return back()->with('error', 'Cannot delete visitor type — visitor records exist for this category.');
        }

        $visitorType->delete();

        return redirect()->route('settings.index', ['tab' => 'visitorTypes'])->with('success', 'Visitor category deleted successfully.');
    }

    // --- SHIFTS ---
    public function storeShift(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'start_time' => ['required', 'string'],
            'end_time' => ['required', 'string'],
            'effective_from' => ['nullable', 'date'],
            'effective_to' => ['nullable', 'date'],
            'grace_minutes' => ['nullable', 'integer', 'min:0'],
            'status' => ['nullable', 'string', 'in:active,inactive'],
        ]);

        if (!isset($validated['status'])) {
            $validated['status'] = 'active';
        }
        if (!isset($validated['grace_minutes'])) {
            $validated['grace_minutes'] = 0;
        }

        $shift = Shift::create(array_merge($validated, ['organization_id' => TenantScope::getTenantId()]));

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'shift' => $shift]);
        }

        return redirect()->route('settings.index', ['tab' => 'shifts'])->with('success', 'Shift schedule created successfully.');
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

        return redirect()->route('settings.index', ['tab' => 'shifts'])->with('success', 'Shift schedule updated successfully.');
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

        return redirect()->route('settings.index', ['tab' => 'floors'])->with('success', 'Building floor created successfully.');
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

        return redirect()->route('settings.index', ['tab' => 'floors'])->with('success', 'Building floor updated successfully.');
    }
}
