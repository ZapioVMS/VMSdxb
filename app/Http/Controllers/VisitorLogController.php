<?php

namespace App\Http\Controllers;

use App\Models\Site;
use App\Models\Visit;
use App\Models\VisitPurpose;
use App\Models\Scopes\TenantScope;
use Illuminate\Http\Request;
use Illuminate\View\View;

class VisitorLogController extends Controller
{
    /**
     * Display the filterable Visitor Log data table.
     */
    public function index(Request $request): View
    {
        $orgId = TenantScope::getTenantId();

        $query = Visit::with(['visitor', 'site', 'hostEmployee', 'department', 'purpose', 'visitorType', 'badges'])
            ->orderBy('check_in_at', 'desc');

        // Search Filter
        if ($request->filled('search')) {
            $search = strtolower(trim($request->search));
            $query->where(function ($q) use ($search) {
                $q->whereHas('visitor', function ($vq) use ($search) {
                    $vq->whereRaw('LOWER(first_name) LIKE ?', ["%{$search}%"])
                       ->orWhereRaw('LOWER(last_name) LIKE ?', ["%{$search}%"])
                       ->orWhereRaw('LOWER(company_name) LIKE ?', ["%{$search}%"]);
                })->orWhereHas('hostEmployee', function ($hq) use ($search) {
                    $hq->whereRaw('LOWER(first_name) LIKE ?', ["%{$search}%"])
                       ->orWhereRaw('LOWER(last_name) LIKE ?', ["%{$search}%"]);
                });
            });
        }

        // Status Filter
        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        // Site Filter
        if ($request->filled('site_id') && $request->site_id !== 'all') {
            $query->where('site_id', $request->site_id);
        }

        // Purpose Filter
        if ($request->filled('purpose_id') && $request->purpose_id !== 'all') {
            $query->where('purpose_id', $request->purpose_id);
        }

        $visits = $query->paginate(25)->withQueryString();

        $sites = Site::where('status', 'active')->get();
        $purposes = VisitPurpose::where('status', 'active')->get();

        return view('visitors.index', compact('visits', 'sites', 'purposes'));
    }
}
