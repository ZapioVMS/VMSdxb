<?php

namespace App\Http\Controllers;

use App\Models\DeliveryCarrier;
use App\Models\GatePass;
use App\Models\IdType;
use App\Models\PackageSize;
use App\Models\Scopes\TenantScope;
use App\Models\Site;
use App\Models\VisitPurpose;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class GatePassController extends Controller
{
    /**
     * Display a listing of gate passes with filters, stats, and modals.
     */
    public function index(Request $request): View
    {
        $orgId = TenantScope::getTenantId();

        $query = GatePass::with('site')->latest();

        // Site Filter
        if ($request->filled('site_id') && $request->input('site_id') !== 'all') {
            $query->where('site_id', $request->input('site_id'));
        }

        // Type Filter ('all', 'visitor', 'delivery')
        $typeFilter = $request->input('type', 'all');
        if (in_array($typeFilter, ['visitor', 'delivery'])) {
            $query->where('type', $typeFilter);
        }

        // Status Filter ('all', 'active', 'redeemed', 'cancelled')
        $statusFilter = $request->input('status', 'all');
        if (in_array($statusFilter, ['active', 'redeemed', 'cancelled'])) {
            $query->where('status', $statusFilter);
        }

        // Search Filter
        if ($request->filled('search')) {
            $search = trim($request->input('search'));
            $like = '%' . mb_strtolower($search) . '%';
            $query->where(function ($q) use ($like) {
                foreach (['pass_code', 'visitor_name', 'company_plate', 'carrier', 'tracking_number', 'description', 'office_unit'] as $col) {
                    $q->orWhereRaw('LOWER(COALESCE(' . $col . ", '')) LIKE ?", [$like]);
                }
            });
        }

        $gatePasses = $query->paginate(25)->withQueryString();

        // KPI Counts
        $baseCountsQuery = GatePass::query();
        if ($request->filled('site_id') && $request->input('site_id') !== 'all') {
            $baseCountsQuery->where('site_id', $request->input('site_id'));
        }

        $totalPasses = (clone $baseCountsQuery)->count();
        $visitorPasses = (clone $baseCountsQuery)->where('type', 'visitor')->count();
        $deliveryPasses = (clone $baseCountsQuery)->where('type', 'delivery')->count();
        $activePasses = (clone $baseCountsQuery)->where('status', 'active')->count();
        $redeemedPasses = (clone $baseCountsQuery)->where('status', 'redeemed')->count();

        $stats = [
            'total' => $totalPasses,
            'visitor' => $visitorPasses,
            'delivery' => $deliveryPasses,
            'active' => $activePasses,
            'redeemed' => $redeemedPasses,
        ];

        // Master Data for Modals
        $sites = Site::orderBy('name')->get();
        if ($sites->isEmpty()) {
            $sites = collect([
                (object)['id' => 1, 'name' => 'Business Bay Tower'],
                (object)['id' => 2, 'name' => 'Downtown HQ'],
            ]);
        }

        $purposes = VisitPurpose::orderBy('name')->get();
        $idTypes = IdType::orderBy('name')->get();
        $carriers = DeliveryCarrier::orderBy('name')->get();
        $sizes = PackageSize::orderBy('name')->get();

        return view('gate_passes.index', compact(
            'gatePasses',
            'stats',
            'typeFilter',
            'statusFilter',
            'sites',
            'purposes',
            'idTypes',
            'carriers',
            'sizes'
        ));
    }

    /**
     * Store a newly created gate pass.
     */
    public function store(Request $request): RedirectResponse
    {
        $type = $request->input('type', 'visitor');

        $rules = [
            'type' => ['required', 'in:visitor,delivery'],
            'site_id' => ['nullable'],
            'office_unit' => ['required', 'string', 'max:255'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ];

        if ($type === 'delivery') {
            $rules['carrier'] = ['required', 'string', 'max:100'];
            $rules['package_size'] = ['nullable', 'string', 'max:100'];
            $rules['tracking_number'] = ['nullable', 'string', 'max:100'];
            $rules['description'] = ['nullable', 'string', 'max:255'];
        } else {
            $rules['visitor_name'] = ['required', 'string', 'max:255'];
            $rules['company_plate'] = ['nullable', 'string', 'max:255'];
            $rules['purpose'] = ['nullable', 'string', 'max:100'];
            $rules['id_type'] = ['nullable', 'string', 'max:100'];
            $rules['id_number'] = ['nullable', 'string', 'max:100'];
            $rules['email'] = ['nullable', 'email', 'max:255'];
            $rules['phone'] = ['nullable', 'string', 'max:50'];
            $rules['phone_code'] = ['nullable', 'string', 'max:10'];
            $rules['expected_date'] = ['nullable', 'date'];
            $rules['expected_duration'] = ['nullable', 'string', 'max:50'];
        }

        $validated = $request->validate($rules);

        // Format phone with country code if provided
        $fullPhone = null;
        if (!empty($validated['phone'])) {
            $code = trim($request->input('phone_code', '971'));
            $fullPhone = '+' . ltrim($code, '+') . ' ' . trim($validated['phone']);
        }

        // Generate unique pass code like GP-84729
        $passCode = 'GP-' . strtoupper(Str::random(5));
        while (GatePass::where('pass_code', $passCode)->exists()) {
            $passCode = 'GP-' . strtoupper(Str::random(5));
        }

        $orgId = TenantScope::getTenantId() ?? 1;

        $gatePass = GatePass::create([
            'organization_id' => $orgId,
            'site_id' => $validated['site_id'] ?: null,
            'pass_code' => $passCode,
            'type' => $type,
            'office_unit' => $validated['office_unit'],
            // Visitor fields
            'visitor_name' => $validated['visitor_name'] ?? null,
            'company_plate' => $validated['company_plate'] ?? null,
            'purpose' => $validated['purpose'] ?? 'Business Meeting',
            'id_type' => $validated['id_type'] ?? 'Emirates ID',
            'id_number' => $validated['id_number'] ?? null,
            'email' => $validated['email'] ?? null,
            'phone' => $fullPhone,
            'expected_date' => !empty($validated['expected_date']) ? Carbon::parse($validated['expected_date']) : Carbon::today(),
            'expected_duration' => $validated['expected_duration'] ?? '1 hour',
            // Delivery fields
            'carrier' => $validated['carrier'] ?? null,
            'package_size' => $validated['package_size'] ?? 'Envelope',
            'tracking_number' => $validated['tracking_number'] ?? null,
            'description' => $validated['description'] ?? null,
            // Common fields
            'notes' => $type === 'delivery'
                ? ($request->input('delivery_notes') ?: null)
                : ($validated['notes'] ?? null),
            'status' => 'active',
        ]);

        return redirect()->route('gate_passes.index')
            ->with('success', "Gate pass {$passCode} created successfully.")
            ->with('new_gate_pass', $gatePass);
    }

    /**
     * Mark a gate pass as redeemed.
     */
    public function redeem(GatePass $gatePass): RedirectResponse
    {
        $gatePass->update([
            'status' => 'redeemed',
            'redeemed_at' => Carbon::now(),
        ]);

        return redirect()->route('gate_passes.index')
            ->with('success', "Gate pass {$gatePass->pass_code} has been redeemed.");
    }

    /**
     * Cancel / revoke a gate pass.
     */
    public function cancel(GatePass $gatePass): RedirectResponse
    {
        $gatePass->update([
            'status' => 'cancelled',
            'cancelled_at' => Carbon::now(),
        ]);

        return redirect()->route('gate_passes.index')
            ->with('success', "Gate pass {$gatePass->pass_code} has been cancelled.");
    }
}
