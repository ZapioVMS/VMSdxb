<?php

namespace App\Http\Controllers;

use App\Models\Site;
use App\Models\Visit;
use App\Models\VisitPurpose;
use App\Models\Scopes\TenantScope;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class VisitorLogController extends Controller
{
    /**
     * Display the filterable Visitor Log data table.
     */
    public function index(Request $request): View
    {
        $query = $this->buildFilterQuery($request);

        $visits = $query->paginate(25)->withQueryString();

        $sites = Site::where('status', 'active')->orderBy('name')->get();
        $purposes = VisitPurpose::where('status', 'active')->orderBy('name')->get();

        return view('visitors.index', compact('visits', 'sites', 'purposes'));
    }

    /**
     * Export the filtered visitor logs to CSV.
     */
    public function export(Request $request): StreamedResponse
    {
        $query = $this->buildFilterQuery($request);
        $visits = $query->get();

        $filename = 'visitor_log_' . date('Y-m-d_H-i') . '.csv';

        return response()->streamDownload(function () use ($visits) {
            $handle = fopen('php://output', 'w');

            // Add UTF-8 BOM for Excel compatibility
            fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));

            // CSV Header
            fputcsv($handle, [
                'Visitor Name',
                'Phone',
                'Email',
                'Company / Vehicle',
                'Host Employee',
                'Department',
                'Purpose of Visit',
                'Site Location',
                'Scheduled Start',
                'Check-In Time',
                'Check-Out Time',
                'Status',
            ]);

            foreach ($visits as $v) {
                fputcsv($handle, [
                    $v->visitor->full_name ?? 'Visitor',
                    $v->visitor->phone ?? '',
                    $v->visitor->email ?? '',
                    $v->visitor->company_name ?? '',
                    $v->hostEmployee->full_name ?? '',
                    $v->department->name ?? '',
                    $v->purpose->name ?? '',
                    $v->site->name ?? '',
                    $v->scheduled_start_at ? $v->scheduled_start_at->format('Y-m-d H:i') : '',
                    $v->check_in_at ? $v->check_in_at->format('Y-m-d H:i') : '',
                    $v->check_out_at ? $v->check_out_at->format('Y-m-d H:i') : '',
                    ucfirst(str_replace('_', ' ', $v->status)),
                ]);
            }

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    /**
     * Build the reusable query filter.
     */
    private function buildFilterQuery(Request $request)
    {
        $query = Visit::with(['visitor', 'site', 'hostEmployee', 'department', 'purpose', 'visitorType', 'badges'])
            ->orderBy('check_in_at', 'desc');

        // Search Filter (Visitor name, phone, company, ID, or host name)
        if ($request->filled('search')) {
            $search = strtolower(trim($request->search));
            $query->where(function ($q) use ($search) {
                $q->whereHas('visitor', function ($vq) use ($search) {
                    $vq->whereRaw('LOWER(first_name) LIKE ?', ["%{$search}%"])
                       ->orWhereRaw('LOWER(last_name) LIKE ?', ["%{$search}%"])
                       ->orWhereRaw('LOWER(company_name) LIKE ?', ["%{$search}%"])
                       ->orWhereRaw('LOWER(id_number) LIKE ?', ["%{$search}%"])
                       ->orWhereRaw('LOWER(phone) LIKE ?', ["%{$search}%"]);
                })->orWhereHas('hostEmployee', function ($hq) use ($search) {
                    $hq->whereRaw('LOWER(first_name) LIKE ?', ["%{$search}%"])
                       ->orWhereRaw('LOWER(last_name) LIKE ?', ["%{$search}%"]);
                });
            });
        }

        // Status Filter (All, Checked in, Expected, Checked out, Overstayed)
        if ($request->filled('status') && $request->status !== 'all') {
            $status = $request->status;
            if ($status === 'overstayed') {
                $query->where(function ($q) {
                    $q->where('status', 'overstayed')
                      ->orWhere(function ($oq) {
                          $oq->where('status', 'checked_in')
                             ->whereNotNull('scheduled_end_at')
                             ->where('scheduled_end_at', '<', now());
                      });
                });
            } else {
                $query->where('status', $status);
            }
        }

        // Site Filter
        if ($request->filled('site_id') && $request->site_id !== 'all') {
            $query->where('site_id', $request->site_id);
        }

        // Purpose Filter
        if ($request->filled('purpose_id') && $request->purpose_id !== 'all') {
            $query->where('purpose_id', $request->purpose_id);
        }

        // Date Range / Time Filter
        if ($request->filled('date_range') && $request->date_range !== 'all') {
            $range = $request->date_range;
            if ($range === 'today') {
                $query->whereDate('check_in_at', now()->today());
            } elseif ($range === 'last_7_days') {
                $query->where('check_in_at', '>=', now()->subDays(7));
            } elseif ($range === 'last_30_days') {
                $query->where('check_in_at', '>=', now()->subDays(30));
            } elseif ($range === 'this_month') {
                $query->whereMonth('check_in_at', now()->month)
                      ->whereYear('check_in_at', now()->year);
            }
        }

        return $query;
    }
}
