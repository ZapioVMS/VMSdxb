<?php

namespace App\Http\Controllers;

use App\Models\Site;
use App\Models\Scopes\TenantScope;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SiteController extends Controller
{
    /**
     * Display a listing of site locations.
     */
    public function index(): View
    {
        $sites = Site::withCount(['buildings', 'employees', 'kioskDevices'])
            ->get();

        return view('sites.index', compact('sites'));
    }

    /**
     * Store a newly created site location.
     */
    public function store(Request $request): RedirectResponse
    {
        $orgId = TenantScope::getTenantId();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'name_ar' => ['nullable', 'string', 'max:255'],
            'city' => ['nullable', 'string', 'max:255'],
            'code' => ['required', 'string', 'max:50'],
            'address' => ['nullable', 'string', 'max:500'],
            'capacity' => ['nullable', 'integer', 'min:0'],
            'status' => ['required', 'string', 'in:active,inactive'],
        ]);

        Site::create(array_merge($validated, ['organization_id' => $orgId]));

        return back()->with('success', 'Site location successfully created.');
    }

    /**
     * Update the specified site location.
     */
    public function update(Request $request, Site $site): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'name_ar' => ['nullable', 'string', 'max:255'],
            'city' => ['nullable', 'string', 'max:255'],
            'code' => ['required', 'string', 'max:50'],
            'address' => ['nullable', 'string', 'max:500'],
            'capacity' => ['nullable', 'integer', 'min:0'],
            'status' => ['required', 'string', 'in:active,inactive'],
        ]);

        $site->update($validated);

        return back()->with('success', 'Site location updated successfully.');
    }

    /**
     * Remove the specified site location.
     */
    public function destroy(Site $site): RedirectResponse
    {
        if ($site->employees()->exists() || $site->visits()->exists()) {
            return back()->with('error', 'Cannot delete site — staff employees or visitor records are assigned to this location.');
        }

        $site->delete();

        return back()->with('success', 'Site location deleted successfully.');
    }
}
