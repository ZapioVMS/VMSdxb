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
     * Display a listing of site branches.
     */
    public function index(): View
    {
        $sites = Site::withCount([
            'buildings',
            'employees',
            'kioskDevices',
            'visits as active_visitors_count' => function ($query) {
                $query->where('status', 'checked_in');
            }
        ])->get();

        return view('sites.index', compact('sites'));
    }

    /**
     * Store a newly created site branch.
     */
    public function store(Request $request): RedirectResponse
    {
        $orgId = TenantScope::getTenantId();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'name_ar' => ['nullable', 'string', 'max:255'],
            'city' => ['nullable', 'string', 'max:255'],
            'code' => ['nullable', 'string', 'max:50'],
            'address' => ['nullable', 'string', 'max:500'],
            'capacity' => ['nullable', 'integer', 'min:0'],
            'floors' => ['nullable', 'integer', 'min:0'],
            'status' => ['required', 'string', 'in:active,inactive'],
        ]);

        if (empty($validated['code'])) {
            $validated['code'] = strtoupper(\Illuminate\Support\Str::slug($validated['name'], '_')) ?: ('SITE_' . time());
        }

        Site::create(array_merge($validated, ['organization_id' => $orgId]));

        return back()->with('success', 'Branch successfully created.');
    }

    /**
     * Update the specified site branch.
     */
    public function update(Request $request, Site $site): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'name_ar' => ['nullable', 'string', 'max:255'],
            'city' => ['nullable', 'string', 'max:255'],
            'code' => ['nullable', 'string', 'max:50'],
            'address' => ['nullable', 'string', 'max:500'],
            'capacity' => ['nullable', 'integer', 'min:0'],
            'floors' => ['nullable', 'integer', 'min:0'],
            'status' => ['required', 'string', 'in:active,inactive'],
        ]);

        if (empty($validated['code']) && empty($site->code)) {
            $validated['code'] = strtoupper(\Illuminate\Support\Str::slug($validated['name'], '_')) ?: ('SITE_' . time());
        }

        $site->update($validated);

        return back()->with('success', 'Branch updated successfully.');
    }

    /**
     * Remove the specified site branch.
     */
    public function destroy(Site $site): RedirectResponse
    {
        if ($site->employees()->exists() || $site->visits()->exists()) {
            return back()->with('error', 'Cannot delete branch — staff employees or visitor records are assigned to this branch.');
        }

        $site->delete();

        return back()->with('success', 'Branch deleted successfully.');
    }
}
