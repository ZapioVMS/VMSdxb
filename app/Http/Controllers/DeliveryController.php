<?php

namespace App\Http\Controllers;

use App\Models\Delivery;
use App\Models\DeliveryCarrier;
use App\Models\Employee;
use App\Models\PackageSize;
use App\Models\Scopes\TenantScope;
use App\Models\Site;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DeliveryController extends Controller
{
    /**
     * Display a listing of deliveries with KPI stats, filters, and modals.
     */
    public function index(Request $request): View
    {
        $orgId = TenantScope::getTenantId();
        $today = Carbon::today();

        $query = Delivery::with(['site', 'recipient.department'])->latest('received_at');

        if ($request->filled('search')) {
            $search = trim($request->input('search'));
            $like = '%' . mb_strtolower($search) . '%';
            $query->where(function ($q) use ($like) {
                foreach (['delivery_code', 'description', 'tracking_number', 'carrier', 'recipient_name', 'recipient_email', 'department', 'collected_by'] as $col) {
                    $q->orWhereRaw('LOWER(COALESCE(' . $col . ", '')) LIKE ?", [$like]);
                }
            });
        }

        if ($request->filled('site_id') && $request->input('site_id') !== 'all') {
            $query->where('site_id', $request->input('site_id'));
        }

        $statusFilter = $request->input('status', 'all');
        if ($statusFilter === 'awaiting-pickup') {
            $query->where('status', 'awaiting-pickup');
        } elseif ($statusFilter === 'collected') {
            $query->where('status', 'collected');
        } elseif ($statusFilter === 'unclaimed') {
            $query->where('status', 'awaiting-pickup')
                  ->where('received_at', '<=', Carbon::now()->subDays(3));
        }

        $deliveries = $query->paginate(25)->withQueryString();

        // Calculate KPI Counts
        $allBaseQuery = Delivery::query();
        if ($request->filled('site_id') && $request->input('site_id') !== 'all') {
            $allBaseQuery->where('site_id', $request->input('site_id'));
        }

        $receivedToday = (clone $allBaseQuery)->whereDate('received_at', $today)->count();
        $awaitingPickup = (clone $allBaseQuery)->where('status', 'awaiting-pickup')->count();
        $collectedToday = (clone $allBaseQuery)->where('status', 'collected')->whereDate('collected_at', $today)->count();
        $unclaimedCount = (clone $allBaseQuery)->where('status', 'awaiting-pickup')->where('received_at', '<=', Carbon::now()->subDays(3))->count();
        $collectedTotal = (clone $allBaseQuery)->where('status', 'collected')->count();
        $totalDeliveries = (clone $allBaseQuery)->count();

        $counts = [
            'all' => $totalDeliveries,
            'awaiting-pickup' => $awaitingPickup,
            'collected' => $collectedTotal,
            'unclaimed' => $unclaimedCount,
        ];

        $stats = [
            'receivedToday' => $receivedToday,
            'awaitingPickup' => $awaitingPickup,
            'collectedToday' => $collectedToday,
            'unclaimed' => $unclaimedCount,
        ];

        $sites = Site::orderBy('name')->get();
        $carriers = DeliveryCarrier::orderBy('name')->get();
        $sizes = PackageSize::orderBy('name')->get();
        $employees = Employee::with(['department', 'site'])->orderBy('first_name')->get();

        return view('deliveries.index', compact(
            'deliveries',
            'counts',
            'stats',
            'statusFilter',
            'sites',
            'carriers',
            'sizes',
            'employees'
        ));
    }

    /**
     * Store a newly created delivery log.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'carrier' => ['required', 'string', 'max:100'],
            'size' => ['required', 'string', 'max:100'],
            'tracking_number' => ['nullable', 'string', 'max:100'],
            'description' => ['required', 'string', 'max:255'],
            'recipient_employee_id' => ['required', 'exists:employees,id'],
            'notes' => ['nullable', 'string', 'max:1000'],
            'notify_recipient' => ['nullable', 'boolean'],
            'photo' => ['nullable', 'string'],
        ]);

        $employee = Employee::with(['department', 'site'])->findOrFail($validated['recipient_employee_id']);
        $orgId = TenantScope::getTenantId() ?? $employee->organization_id ?? 1;

        $lastId = Delivery::max('id') ?? 0;
        $deliveryCode = 'DLV' . str_pad($lastId + 1, 3, '0', STR_PAD_LEFT);

        $notify = $request->boolean('notify_recipient', true);

        Delivery::create([
            'organization_id' => $orgId,
            'site_id' => $employee->site_id,
            'delivery_code' => $deliveryCode,
            'carrier' => $validated['carrier'],
            'tracking_number' => $validated['tracking_number'] ?? null,
            'size' => $validated['size'],
            'description' => $validated['description'],
            'recipient_employee_id' => $employee->id,
            'recipient_name' => trim($employee->first_name . ' ' . $employee->last_name),
            'recipient_email' => $employee->email,
            'department' => $employee->department?->name ?? 'General',
            'received_at' => Carbon::now(),
            'received_by' => auth()->user()?->name ?? 'Front Desk',
            'status' => 'awaiting-pickup',
            'notify_recipient' => $notify,
            'notified_at' => $notify ? Carbon::now() : null,
            'notes' => $validated['notes'] ?? null,
            'photo_path' => $this->processPhoto($request->input('photo')),
        ]);

        return redirect()->route('deliveries.index')->with('success', 'Package delivery logged successfully.');
    }

    /**
     * Update an existing delivery log.
     */
    public function update(Request $request, Delivery $delivery): RedirectResponse
    {
        $validated = $request->validate([
            'carrier' => ['required', 'string', 'max:100'],
            'size' => ['required', 'string', 'max:100'],
            'tracking_number' => ['nullable', 'string', 'max:100'],
            'description' => ['required', 'string', 'max:255'],
            'recipient_employee_id' => ['required', 'exists:employees,id'],
            'notes' => ['nullable', 'string', 'max:1000'],
            'notify_recipient' => ['nullable', 'boolean'],
            'photo' => ['nullable', 'string'],
        ]);

        $employee = Employee::with(['department', 'site'])->findOrFail($validated['recipient_employee_id']);

        $updateData = [
            'carrier' => $validated['carrier'],
            'size' => $validated['size'],
            'tracking_number' => $validated['tracking_number'] ?? null,
            'description' => $validated['description'],
            'recipient_employee_id' => $employee->id,
            'recipient_name' => trim($employee->first_name . ' ' . $employee->last_name),
            'recipient_email' => $employee->email,
            'department' => $employee->department?->name ?? $delivery->department,
            'site_id' => $employee->site_id ?? $delivery->site_id,
            'notes' => $validated['notes'] ?? null,
            'notify_recipient' => $request->boolean('notify_recipient', true),
        ];

        if ($request->filled('photo')) {
            $updateData['photo_path'] = $this->processPhoto($request->input('photo'));
        }

        $delivery->update($updateData);

        return redirect()->route('deliveries.index')->with('success', 'Delivery updated successfully.');
    }

    /**
     * Process and store photo if base64 encoded.
     */
    private function processPhoto(?string $photo): ?string
    {
        if (!$photo) {
            return null;
        }

        if (str_starts_with($photo, 'data:image')) {
            try {
                $parts = explode(',', $photo, 2);
                if (count($parts) === 2) {
                    $decoded = base64_decode($parts[1]);
                    $path = 'deliveries/' . Str::uuid() . '.jpg';
                    \Illuminate\Support\Facades\Storage::disk('public')->put($path, $decoded);
                    return 'storage/' . $path;
                }
            } catch (\Throwable $e) {
                return $photo;
            }
        }

        return $photo;
    }

    /**
     * Mark a delivery as collected.
     */
    public function collect(Request $request, Delivery $delivery): RedirectResponse
    {
        $validated = $request->validate([
            'collected_by' => ['required', 'string', 'max:150'],
        ]);

        if ($delivery->status === 'collected' || $delivery->collected_at) {
            return back()->with('error', 'This delivery was already collected'
                . ($delivery->collected_by ? ' by ' . $delivery->collected_by : '')
                . ($delivery->collected_at ? ' on ' . Carbon::parse($delivery->collected_at)->format('d M Y, h:i A') : '') . '.');
        }

        $delivery->update([
            'status' => 'collected',
            'collected_at' => Carbon::now(),
            'collected_by' => trim($validated['collected_by']),
        ]);

        return redirect()->route('deliveries.index')->with('success', "Delivery marked as collected by {$delivery->collected_by}.");
    }

    /**
     * Re-notify the recipient employee.
     */
    public function notify(Delivery $delivery): RedirectResponse
    {
        $delivery->update([
            'notified_at' => Carbon::now(),
        ]);

        return redirect()->route('deliveries.index')->with('success', "Reminder notification sent to {$delivery->recipient_name}.");
    }

    /**
     * Delete a delivery log.
     */
    public function destroy(Delivery $delivery): RedirectResponse
    {
        $delivery->delete();

        return redirect()->route('deliveries.index')->with('success', 'Delivery record removed successfully.');
    }

    /**
     * Export deliveries to CSV.
     */
    public function export(Request $request): StreamedResponse
    {
        $query = Delivery::with(['site'])->latest('received_at');

        if ($request->filled('site_id') && $request->input('site_id') !== 'all') {
            $query->where('site_id', $request->input('site_id'));
        }

        $statusFilter = $request->input('status', 'all');
        if ($statusFilter === 'awaiting-pickup') {
            $query->where('status', 'awaiting-pickup');
        } elseif ($statusFilter === 'collected') {
            $query->where('status', 'collected');
        } elseif ($statusFilter === 'unclaimed') {
            $query->where('status', 'awaiting-pickup')
                  ->where('received_at', '<=', Carbon::now()->subDays(3));
        }

        $deliveries = $query->get();

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="deliveries.csv"',
        ];

        $callback = function () use ($deliveries) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, [
                'Package ID',
                'Description',
                'Carrier',
                'Tracking Number',
                'Size',
                'Recipient',
                'Department',
                'Branch',
                'Received',
                'Status',
                'Collected By',
                'Collected At',
            ]);

            foreach ($deliveries as $d) {
                fputcsv($handle, [
                    $d->delivery_code ?? 'DLV' . str_pad($d->id, 3, '0', STR_PAD_LEFT),
                    $d->description,
                    $d->carrier,
                    $d->tracking_number ?? '',
                    $d->size,
                    $d->recipient_name,
                    $d->department ?? '',
                    $d->site?->name ?? 'Default Site',
                    $d->received_at ? $d->received_at->format('d M Y, h:i A') : '',
                    $d->is_unclaimed ? 'Unclaimed' : ($d->status === 'collected' ? 'Collected' : 'Awaiting Pickup'),
                    $d->collected_by ?? '',
                    $d->collected_at ? $d->collected_at->format('d M Y, h:i A') : '',
                ]);
            }

            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }
}
