<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Pre-Registration — Zapio VMS</title>
    <link rel="icon" href="{{ !empty($globalBranding['favicon_url']) ? $globalBranding['favicon_url'] : asset('favicon.ico') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=IBM+Plex+Sans:wght@400;500;600;700&display=swap">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
    <style>
        :root {
            --accent: #0c6b64;
            --accent-hover: #0f7a72;
            --accent-soft: rgba(12, 107, 100, 0.10);
            --bg: #f4f7f6;
            --surface: #ffffff;
            --surface-2: #eef2f1;
            --surface-3: #e6ebe9;
            --text-primary: #0e1a18;
            --text-secondary: #4d5b57;
            --text-muted: #7c8985;
            --border: rgba(14, 26, 24, 0.12);
            --good: #0ca30c;
            --warning: #fab219;
            --serious: #ec835a;
            --critical: #d03b3b;
            --radius: 12px;
            --radius-sm: 8px;
            --shadow: 0 1px 2px rgba(14,26,24,.06), 0 8px 24px -12px rgba(14,26,24,.18);
        }
        *, *::before, *::after { box-sizing: border-box; }
        body { margin: 0; font-family: 'IBM Plex Sans', system-ui, -apple-system, sans-serif; background: var(--bg); color: var(--text-primary); font-size: 14px; }
        .ic { width: 16px; height: 16px; flex: none; display: inline-block; vertical-align: -3px; }
        #app { display: grid; grid-template-columns: 248px 1fr; grid-template-rows: 60px 1fr; min-height: 100vh; }
        #sidebar { grid-row: 1/3; grid-column: 1; background: var(--surface); border-inline-end: 1px solid var(--border); display: flex; flex-direction: column; }
        #topbar { grid-row: 1; grid-column: 2; background: var(--surface); border-block-end: 1px solid var(--border); display: flex; align-items: center; gap: 14px; padding-inline: 20px; }
        #main { grid-row: 2; grid-column: 2; padding: 24px 28px 60px; overflow-y: auto; }

        .brand { display: flex; align-items: center; gap: 10px; padding: 16px 18px; border-block-end: 1px solid var(--border); }
        .brand-mark { width: 32px; height: 32px; border-radius: 99px; background: linear-gradient(155deg, #0f7a72, var(--accent)); display: flex; align-items: center; justify-content: center; color: #fff; font-weight: 700; font-size: 15px; }
        .brand-name { font-weight: 700; font-size: 16.5px; }
        .brand-sub { font-size: 11px; color: var(--text-muted); }

        nav.navlist { padding: 10px; display: flex; flex-direction: column; gap: 2px; flex: 1; }
        .navlabel { font-size: 10.5px; text-transform: uppercase; letter-spacing: .06em; color: var(--text-muted); padding: 14px 10px 6px; }
        .navitem { display: flex; align-items: center; gap: 11px; padding: 9px 10px; border-radius: var(--radius-sm); color: var(--text-secondary); cursor: pointer; font-size: 13.5px; font-weight: 500; text-decoration: none; }
        .navitem:hover { background: var(--surface-2); color: var(--text-primary); }
        .navitem.active { background: var(--accent-soft); color: var(--accent); font-weight: 600; }
        .sidebar-foot { padding: 12px; border-top: 1px solid var(--border); }

        .pagehead { display: flex; align-items: flex-start; justify-content: space-between; gap: 16px; margin-bottom: 20px; flex-wrap: wrap; }
        .pagehead h1 { font-size: 21px; margin: 0; font-weight: 600; }
        .pagehead .sub { color: var(--text-secondary); font-size: 13px; margin-top: 3px; }
        .pagehead-actions { display: flex; gap: 8px; flex-wrap: wrap; }

        .btn { display: inline-flex; align-items: center; gap: 7px; padding: 8px 14px; border-radius: var(--radius-sm); border: 1px solid var(--border); background: var(--surface); color: var(--text-primary); font-size: 13px; font-weight: 600; cursor: pointer; white-space: nowrap; text-decoration: none; }
        .btn-primary { background: var(--accent); border-color: var(--accent); color: #fff; }
        .btn-primary:hover { background: var(--accent-hover); }
        .btn-sm { padding: 5px 10px; font-size: 12px; }
        .btn-ghost { border-color: transparent; background: transparent; }
        .btn-ghost:hover { background: var(--surface-2); }
        .btn-danger { color: var(--critical); border-color: rgba(208,59,59,0.3); }
        .btn-danger:hover { background: rgba(208,59,59,0.1); }

        .grid-4 { display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 14px; }
        @media (max-width: 1100px) { .grid-4 { grid-template-columns: repeat(2, minmax(0, 1fr)); } }
        @media (max-width: 600px) { .grid-4 { grid-template-columns: 1fr; } }

        .card { background: var(--surface); border: 1px solid var(--border); border-radius: var(--radius); padding: 18px; }
        .stat-tile { background: var(--surface); border: 1px solid var(--border); border-radius: var(--radius); padding: 16px 18px; display: flex; flex-direction: column; gap: 8px; }
        .stat-label { font-size: 12px; color: var(--text-secondary); font-weight: 500; }
        .stat-value { font-size: 26px; font-weight: 700; letter-spacing: -.01em; font-variant-numeric: tabular-nums; }

        .subtabs { display: flex; gap: 22px; border-bottom: 1px solid var(--border); margin-bottom: 18px; overflow-x: auto; }
        .subtab { background: none; border: none; font: inherit; font-size: 13.5px; font-weight: 600; color: var(--text-muted); padding: 9px 1px 12px; cursor: pointer; border-bottom: 2px solid transparent; margin-bottom: -1px; text-decoration: none; white-space: nowrap; }
        .subtab:hover { color: var(--text-primary); }
        .subtab.active { color: var(--accent); border-bottom-color: var(--accent); }

        .filterbar { display: flex; gap: 10px; flex-wrap: wrap; align-items: center; margin-bottom: 16px; }
        .pillrow { display: flex; gap: 8px; flex-wrap: wrap; }
        .statuspill { display: inline-flex; align-items: center; gap: 6px; padding: 7px 14px; border-radius: 99px; border: 1.5px solid var(--border); background: var(--surface); font-size: 12.5px; font-weight: 600; cursor: pointer; color: var(--text-secondary); text-decoration: none; }
        .statuspill:hover { background: var(--surface-2); }
        .statuspill.active { border-color: var(--accent); background: var(--accent-soft); color: var(--accent); }

        .searchbox { display: flex; align-items: center; gap: 8px; background: var(--surface); border: 1px solid var(--border); border-radius: var(--radius-sm); padding: 7px 12px; color: var(--text-muted); min-width: 240px; }
        .searchbox input { border: none; background: none; outline: none; color: var(--text-primary); font-size: 13px; width: 100%; font-family: inherit; }

        select, input[type="text"], input[type="email"], input[type="tel"], input[type="date"], input[type="number"], textarea {
            font-family: inherit; font-size: 13.5px; padding: 8px 11px; border-radius: var(--radius-sm); border: 1px solid var(--border); background: var(--surface); color: var(--text-primary); width: 100%;
        }
        select:focus, input:focus, textarea:focus { outline: none; border-color: var(--accent); box-shadow: 0 0 0 3px var(--accent-soft); }

        .tablewrap { border: 1px solid var(--border); border-radius: var(--radius); overflow-x: auto; background: var(--surface); }
        table.dtable { width: 100%; border-collapse: collapse; min-width: 780px; }
        .dtable thead th { text-align: start; font-size: 11px; text-transform: uppercase; letter-spacing: .05em; color: var(--text-muted); padding: 11px 14px; border-bottom: 1px solid var(--border); background: var(--surface-2); }
        .dtable tbody td { padding: 12px 14px; border-bottom: 1px solid var(--border); font-size: 13px; vertical-align: middle; }
        .cell-name { font-weight: 600; }
        .cell-sub { font-size: 11.5px; color: var(--text-muted); margin-top: 1px; }

        .chip { display: inline-flex; align-items: center; padding: 3px 9px; border-radius: 99px; font-size: 11.5px; font-weight: 600; }
        .chip-good { background: rgba(12, 163, 12, 0.14); color: var(--good); }
        .chip-warning { background: rgba(250, 178, 25, 0.18); color: #8a5a00; }
        .chip-muted { background: var(--surface-2); color: var(--text-muted); }
        .chip-accent { background: var(--accent-soft); color: var(--accent); }
        .chip-critical { background: rgba(208, 59, 59, 0.14); color: var(--critical); }

        .alert-success { background: rgba(12, 163, 12, 0.14); border: 1px solid rgba(12, 163, 12, 0.30); color: var(--good); padding: 12px 16px; border-radius: var(--radius-sm); font-size: 13.5px; margin-bottom: 18px; display: flex; align-items: center; justify-content: space-between; }

        /* Modal styling with visible y-axis scrollbar */
        .modal-root { display: none; position: fixed; inset: 0; z-index: 100; align-items: center; justify-content: center; background: rgba(6, 12, 11, 0.5); padding: 20px 16px; }
        .modal-root.open { display: flex; }
        .modal-backdrop { position: absolute; inset: 0; }
        .modal-panel { position: relative; z-index: 1; max-width: 680px; width: 100%; background: var(--surface); border-radius: var(--radius); border: 1px solid var(--border); box-shadow: var(--shadow); max-height: 88vh; display: flex; flex-direction: column; overflow: hidden; }
        .modal-panel form { display: flex; flex-direction: column; flex: 1; min-height: 0; overflow: hidden; height: 100%; }
        .modal-head { display: flex; align-items: center; justify-content: space-between; padding: 16px 20px; border-bottom: 1px solid var(--border); flex-shrink: 0; background: var(--surface); }
        .modal-head h3 { font-size: 16px; font-weight: 700; margin: 0; }
        .modal-body { padding: 20px 24px; overflow-y: scroll; flex: 1; min-height: 0; max-height: calc(88vh - 130px); scrollbar-width: thin; scrollbar-color: #b0b8b6 transparent; }
        .modal-body::-webkit-scrollbar { width: 8px; }
        .modal-body::-webkit-scrollbar-track { background: transparent; }
        .modal-body::-webkit-scrollbar-thumb { background: #b0b8b6; border-radius: 99px; }
        .modal-body::-webkit-scrollbar-thumb:hover { background: #7c8985; }
        .modal-foot { display: flex; justify-content: flex-end; align-items: center; gap: 10px; padding: 14px 20px; border-top: 1px solid var(--border); flex-shrink: 0; background: var(--surface); }
        .section-label { font-size: 11.5px; font-weight: 700; text-transform: uppercase; letter-spacing: .05em; color: var(--text-muted); margin: 16px 0 10px; border-top: 1px solid var(--border); padding-top: 14px; }
        .section-label:first-child { margin-top: 0; border-top: none; padding-top: 0; }
        .form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; }
        @media (max-width: 640px) { .form-grid { grid-template-columns: 1fr; } }
        .field { display: flex; flex-direction: column; gap: 5px; }
        .field.span2 { grid-column: 1 / -1; }
        .field label { font-size: 12.5px; font-weight: 600; color: var(--text-secondary); }

        /* Switch row styling */
        .switchrow { display: flex; align-items: center; justify-content: space-between; padding: 8px 0; border-bottom: 1px solid var(--border); font-size: 13px; font-weight: 500; }
        .switchrow:last-child { border-bottom: none; }
        .switch { position: relative; display: inline-block; width: 36px; height: 20px; }
        .switch input { opacity: 0; width: 0; height: 0; }
        .slider { position: absolute; cursor: pointer; inset: 0; background-color: var(--surface-3); transition: .2s; border-radius: 20px; }
        .slider:before { position: absolute; content: ""; height: 14px; width: 14px; left: 3px; bottom: 3px; background-color: white; transition: .2s; border-radius: 50%; }
        input:checked + .slider { background-color: var(--accent); }
        input:checked + .slider:before { transform: translateX(16px); }

        .badge-preview { width: 260px; border: 1px solid var(--border); border-radius: 16px; padding: 20px; margin: 0 auto; background: linear-gradient(135deg, var(--accent), #094b46); color: #fff; text-align: center; }
        .badge-preview .bp-top { font-size: 10px; text-transform: uppercase; letter-spacing: .08em; opacity: .8; }
        .badge-preview .bp-name { font-size: 18px; font-weight: 700; margin-top: 10px; }
        .badge-preview .bp-company { font-size: 12px; opacity: .9; margin-top: 2px; }
        .badge-preview .bp-qr { margin: 16px auto; background: #fff; padding: 10px; border-radius: 12px; display: inline-block; }
        .badge-preview .bp-code { font-size: 15px; font-weight: 700; letter-spacing: .08em; background: rgba(255,255,255,0.15); padding: 6px; border-radius: 6px; }
    </style>
</head>
<body>
    <div id="app">
        @include('partials.sidebar', ['active' => 'pre_registrations'])


        @include('partials.topbar')

        <main id="main">
            <div class="pagehead">
                <div>
                    <h1>Pre-Registration</h1>
                    <div class="sub">Invite visitors in advance and share their access code.</div>
                </div>
                <div class="pagehead-actions">
                    <a href="{{ route('pre_registrations.export', request()->query()) }}" class="btn">
                        <svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75"><path d="M12 4v11M7.5 11l4.5 4.5L16.5 11M5 19.5h14"/></svg>
                        <span>Export CSV</span>
                    </a>
                    <button class="btn btn-primary" onclick="openInviteModal()">
                        <svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75"><path d="M12 5v14M5 12h14"/></svg>
                        <span>+ New Pre-registration</span>
                    </button>
                </div>
            </div>

            @if (session('success'))
                <div class="alert-success">
                    <span><strong>{{ session('success') }}</strong></span>
                    <button class="btn btn-sm btn-ghost" onclick="this.parentElement.remove()">✕</button>
                </div>
            @endif

            <!-- 4 Top KPI Stat Cards -->
            <div class="grid-4" style="margin-bottom: 18px;">
                <div class="stat-tile">
                    <div class="stat-label">Total Upcoming</div>
                    <div class="stat-value">{{ $stats['total_upcoming'] }}</div>
                </div>
                <div class="stat-tile">
                    <div class="stat-label">Confirmed</div>
                    <div class="stat-value" style="color: var(--good);">{{ $stats['confirmed'] }}</div>
                </div>
                <div class="stat-tile">
                    <div class="stat-label">Pending</div>
                    <div class="stat-value" style="color: #8a5a00;">{{ $stats['pending'] }}</div>
                </div>
                <div class="stat-tile">
                    <div class="stat-label">Today's Expected</div>
                    <div class="stat-value" style="color: var(--accent);">{{ $stats['today_expected'] }}</div>
                </div>
            </div>

            <!-- Subtabs: All Registrations, Today, Upcoming, Past, Custom range -->
            <div class="subtabs">
                <a href="{{ route('pre_registrations.index', array_merge(request()->query(), ['tab' => 'all'])) }}" class="subtab {{ $tab === 'all' ? 'active' : '' }}">All Registrations</a>
                <a href="{{ route('pre_registrations.index', array_merge(request()->query(), ['tab' => 'today'])) }}" class="subtab {{ $tab === 'today' ? 'active' : '' }}">Today</a>
                <a href="{{ route('pre_registrations.index', array_merge(request()->query(), ['tab' => 'upcoming'])) }}" class="subtab {{ $tab === 'upcoming' ? 'active' : '' }}">Upcoming</a>
                <a href="{{ route('pre_registrations.index', array_merge(request()->query(), ['tab' => 'past'])) }}" class="subtab {{ $tab === 'past' ? 'active' : '' }}">Past</a>
                <a href="{{ route('pre_registrations.index', array_merge(request()->query(), ['tab' => 'custom'])) }}" class="subtab {{ $tab === 'custom' ? 'active' : '' }}">Custom range</a>
            </div>

            <!-- Custom Date Range Filter Bar (Visible when Custom tab active) -->
            @if ($tab === 'custom')
                <form action="{{ route('pre_registrations.index') }}" method="GET" class="filterbar" style="background: var(--surface); padding: 14px; border: 1px solid var(--border); border-radius: var(--radius); margin-bottom: 18px;">
                    <input type="hidden" name="tab" value="custom">
                    <input type="hidden" name="status" value="{{ $statusFilter }}">
                    <div class="field" style="flex: 1; min-width: 160px;">
                        <label>From Date</label>
                        <input type="date" name="custom_from" value="{{ request()->query('custom_from') }}">
                    </div>
                    <div class="field" style="flex: 1; min-width: 160px;">
                        <label>To Date</label>
                        <input type="date" name="custom_to" value="{{ request()->query('custom_to') }}">
                    </div>
                    <button type="submit" class="btn btn-primary" style="margin-top: 18px;">Apply Filter</button>
                </form>
            @endif

            <!-- Filter Bar: Status Pills, Branch, Search -->
            <form action="{{ route('pre_registrations.index') }}" method="GET" id="filterForm" class="filterbar">
                <input type="hidden" name="tab" value="{{ $tab }}">
                <input type="hidden" name="status" id="statusInput" value="{{ $statusFilter }}">
                @if(request()->filled('custom_from'))<input type="hidden" name="custom_from" value="{{ request('custom_from') }}">@endif
                @if(request()->filled('custom_to'))<input type="hidden" name="custom_to" value="{{ request('custom_to') }}">@endif

                <div class="pillrow">
                    <button type="button" class="statuspill {{ $statusFilter === 'all' ? 'active' : '' }}" onclick="setStatusFilter('all')">All</button>
                    <button type="button" class="statuspill {{ $statusFilter === 'approved' ? 'active' : '' }}" onclick="setStatusFilter('approved')" style="--pill-color: var(--good);">Confirmed</button>
                    <button type="button" class="statuspill {{ $statusFilter === 'pending' ? 'active' : '' }}" onclick="setStatusFilter('pending')" style="--pill-color: #8a5a00;">Pending</button>
                    <button type="button" class="statuspill {{ $statusFilter === 'cancelled' ? 'active' : '' }}" onclick="setStatusFilter('cancelled')" style="--pill-color: var(--text-muted);">Cancelled</button>
                </div>

                <div style="margin-left: auto; display: flex; gap: 10px; align-items: center; flex-wrap: wrap;">
                    <select name="site_id" onchange="document.getElementById('filterForm').submit()" style="width: auto; min-width: 160px;">
                        <option value="all">All Branches</option>
                        @foreach ($sites as $site)
                            <option value="{{ $site->id }}" {{ request()->query('site_id') == $site->id ? 'selected' : '' }}>{{ $site->name }}</option>
                        @endforeach
                    </select>

                    <div class="searchbox">
                        <svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75"><circle cx="10.5" cy="10.5" r="6.5"/><path d="M20 20l-4.8-4.8"/></svg>
                        <input type="text" name="search" placeholder="Search visitors, ID, company..." value="{{ request()->query('search') }}" onchange="document.getElementById('filterForm').submit()">
                    </div>
                </div>
            </form>

            <!-- Table of Pre-Registered Visitors -->
            <div class="tablewrap">
                <table class="dtable">
                    <thead>
                        <tr>
                            <th>Visitor</th>
                            <th>Expected Date</th>
                            <th>Time</th>
                            <th>Host Employee</th>
                            <th>Purpose</th>
                            <th>Status</th>
                            <th style="text-align: end;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($invitations as $inv)
                            @php
                                $visit = $inv->visit;
                                $visitor = $visit->visitor ?? null;
                                $host = $visit->hostEmployee ?? null;
                                $scheduledStart = $visit->scheduled_start_at ?? null;
                                $scheduledEnd = $visit->scheduled_end_at ?? null;

                                $isToday = $scheduledStart ? $scheduledStart->isToday() : false;
                                $dateDisplay = $isToday ? 'Today' : ($scheduledStart ? $scheduledStart->format('D, M d, Y') : '—');
                                $timeDisplay = $scheduledStart ? $scheduledStart->format('H:i') . ($scheduledEnd ? ' – ' . $scheduledEnd->format('H:i') : '') : '—';
                            @endphp
                            <tr>
                                <td>
                                    <div class="cell-name">{{ $visitor ? $visitor->full_name : '—' }}</div>
                                    <div class="cell-sub">{{ $visitor->company_name ?? '—' }}</div>
                                </td>
                                <td>
                                    @if ($isToday)
                                        <strong style="color: var(--accent);">Today</strong>
                                    @else
                                        {{ $dateDisplay }}
                                    @endif
                                </td>
                                <td style="font-variant-numeric: tabular-nums;">{{ $timeDisplay }}</td>
                                <td>
                                    <div class="cell-name">{{ $host ? $host->full_name : '—' }}</div>
                                    <div class="cell-sub">{{ $visit->department->name ?? ($host->department->name ?? '') }}</div>
                                </td>
                                <td>
                                    <span class="chip chip-accent">{{ $visit->purpose->name ?? 'Meeting' }}</span>
                                </td>
                                <td>
                                    @if ($inv->status === 'approved')
                                        <span class="chip chip-good">Confirmed</span>
                                    @elseif ($inv->status === 'pending')
                                        <span class="chip chip-warning">Pending</span>
                                    @elseif ($inv->status === 'cancelled')
                                        <span class="chip chip-muted">Cancelled</span>
                                    @else
                                        <span class="chip chip-muted">{{ ucfirst($inv->status) }}</span>
                                    @endif
                                </td>
                                <td>
                                    <div style="display: flex; gap: 4px; justify-content: flex-end;">
                                        <button class="btn btn-sm btn-ghost" onclick="openQrModal({{ json_encode([
                                            'code' => $inv->access_code,
                                            'name' => $visitor ? $visitor->full_name : '',
                                            'company' => $visitor->company_name ?? '',
                                            'host' => $host ? $host->full_name : '',
                                            'site' => $visit->site->name ?? '',
                                            'date' => $dateDisplay,
                                            'time' => $timeDisplay,
                                        ]) }})" title="View Access QR">
                                            <svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
                                        </button>
                                        <button class="btn btn-sm btn-ghost" onclick="openEditInviteModal({{ json_encode([
                                            'id' => $inv->id,
                                            'first_name' => $visitor->first_name ?? '',
                                            'last_name' => $visitor->last_name ?? '',
                                            'company_name' => $visitor->company_name ?? '',
                                            'email' => $visitor->email ?? '',
                                            'phone' => $visitor->phone ?? '',
                                            'id_type_id' => $visitor->id_type_id ?? '',
                                            'id_number' => $visitor->id_number ?? '',
                                            'visit_date' => $scheduledStart ? $scheduledStart->format('Y-m-d') : '',
                                            'expected_arrival' => $scheduledStart ? $scheduledStart->format('H:i') : '10:00',
                                            'expected_departure' => $scheduledEnd ? $scheduledEnd->format('H:i') : '',
                                            'duration' => $inv->duration ?? 60,
                                            'recurring' => $inv->recurring ?? 'none',
                                            'purpose_id' => $visit->purpose_id ?? '',
                                            'visitor_type_id' => $visit->visitor_type_id ?? '',
                                            'site_id' => $visit->site_id ?? '',
                                            'host_employee_id' => $visit->host_employee_id ?? '',
                                            'department_id' => $visit->department_id ?? '',
                                            'meeting_room' => $inv->meeting_room ?? '',
                                            'expected_visitor_count' => $inv->expected_visitor_count ?? 1,
                                            'send_confirmation_email' => $inv->send_confirmation_email,
                                            'notify_host_on_arrival' => $inv->notify_host_on_arrival,
                                            'send_qr_self_checkin' => $inv->send_qr_self_checkin,
                                            'requires_nda' => $inv->requires_nda,
                                            'wifi_guest_access' => $inv->wifi_guest_access,
                                            'parking_required' => $inv->parking_required,
                                            'escort_required' => $inv->escort_required,
                                            'special_instructions' => $inv->special_instructions ?? '',
                                            'items_to_bring' => $inv->items_to_bring ?? '',
                                        ]) }})" title="Edit Pre-registration">
                                            <svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75"><path d="M4 20l1-4.5L15.5 5 19 8.5 8.5 19 4 20z"/></svg>
                                        </button>

                                        @if ($inv->status !== 'cancelled')
                                            <form action="{{ route('pre_registrations.resend', $inv->id) }}" method="POST" style="display:inline;">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-ghost" title="Resend Notification">
                                                    <svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75"><path d="M6 10a6 6 0 0 1 12 0c0 4 1.5 5.5 2 6H4c.5-.5 2-2 2-6z"/><path d="M10 19a2 2 0 0 0 4 0"/></svg>
                                                </button>
                                            </form>
                                            <form action="{{ route('pre_registrations.cancel', $inv->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Cancel this pre-registration?')">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-ghost" style="color: var(--critical);" title="Cancel Pre-registration">
                                                    <svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75"><path d="M6 6l12 12M18 6L6 18"/></svg>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" style="text-align: center; color: var(--text-muted); padding: 36px;">
                                    No pre-registrations found for the selected filters.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </main>
    </div>

    <!-- Add / Edit Pre-Registration Modal -->
    <div id="inviteModal" class="modal-root">
        <div class="modal-backdrop" onclick="closeInviteModal()"></div>
        <div class="modal-panel">
            <div class="modal-head">
                <h3 id="modalTitle">New Pre-registration</h3>
                <button type="button" class="btn btn-ghost btn-sm" onclick="closeInviteModal()">✕</button>
            </div>
            <form id="inviteForm" action="{{ route('pre_registrations.store') }}" method="POST">
                @csrf
                <input type="hidden" id="invite_method" name="_method" value="POST">

                <div class="modal-body">
                    <!-- Section 1: Visitor Information -->
                    <div class="section-label">Visitor Information</div>
                    <div class="field" style="margin-bottom: 12px;">
                        <label for="inv_name">Full Name *</label>
                        <input type="text" id="inv_name" name="name" required placeholder="Full name">
                    </div>
                    <div class="field" style="margin-bottom: 12px;">
                        <label for="inv_company_name">Company / Organization</label>
                        <input type="text" id="inv_company_name" name="company_name" placeholder="Company name">
                    </div>
                    <div class="form-grid">
                        <div class="field">
                            <label for="inv_email">Email (optional) *</label>
                            <input type="email" id="inv_email" name="email" required placeholder="visitor@company.com">
                        </div>
                        <div class="field">
                            <label for="inv_phone">Phone number</label>
                            <div class="phone-group" style="display: flex; gap: 8px;">
                                <select name="phone_code" id="inv_phone_code" style="width: 105px; flex: none;">
                                    <option value="971" selected>+971</option>
                                    <option value="966">+966</option>
                                    <option value="974">+974</option>
                                    <option value="965">+965</option>
                                    <option value="973">+973</option>
                                    <option value="968">+968</option>
                                    <option value="1">+1</option>
                                    <option value="44">+44</option>
                                    <option value="91">+91</option>
                                </select>
                                <input type="tel" id="inv_phone" name="phone" placeholder="50 000 0000" style="flex: 1;">
                            </div>
                        </div>
                        <div class="field">
                            <label for="inv_id_type_id">ID type</label>
                            <select id="inv_id_type_id" name="id_type_id">
                                @foreach ($idTypes as $idType)
                                    <option value="{{ $idType->id }}" {{ str_contains(strtolower($idType->name), 'emirates') ? 'selected' : '' }}>{{ $idType->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="field">
                            <label for="inv_id_number">ID Document Number</label>
                            <input type="text" id="inv_id_number" name="id_number" placeholder="Emirates ID / Passport number">
                        </div>
                    </div>

                    <!-- Section 2: Visit Schedule -->
                    <div class="section-label">Visit Schedule</div>
                    <div class="form-grid">
                        <div class="field">
                            <label for="inv_visit_date">Visit Date *</label>
                            <input type="date" id="inv_visit_date" name="visit_date" required value="{{ date('Y-m-d') }}">
                        </div>
                        <div class="field">
                            <label for="inv_expected_arrival">Expected Arrival *</label>
                            <select id="inv_expected_arrival" name="expected_arrival" required>
                                @for ($h = 7; $h <= 21; $h++)
                                    @foreach (['00', '15', '30', '45'] as $m)
                                        @php
                                            $h12 = $h > 12 ? $h - 12 : ($h == 0 ? 12 : $h);
                                            $ampm = $h >= 12 ? 'PM' : 'AM';
                                            $displayTime = sprintf('%02d:%s %s', $h12, $m, $ampm);
                                            $valTime = sprintf('%02d:%s', $h, $m);
                                        @endphp
                                        <option value="{{ $valTime }}" {{ $valTime === '10:00' ? 'selected' : '' }}>{{ $displayTime }}</option>
                                    @endforeach
                                @endfor
                            </select>
                        </div>
                        <div class="field">
                            <label for="inv_expected_departure">Expected Departure</label>
                            <select id="inv_expected_departure" name="expected_departure">
                                <option value="">Not set</option>
                                @for ($h = 7; $h <= 22; $h++)
                                    @foreach (['00', '15', '30', '45'] as $m)
                                        @php
                                            $h12 = $h > 12 ? $h - 12 : ($h == 0 ? 12 : $h);
                                            $ampm = $h >= 12 ? 'PM' : 'AM';
                                            $displayTime = sprintf('%02d:%s %s', $h12, $m, $ampm);
                                            $valTime = sprintf('%02d:%s', $h, $m);
                                        @endphp
                                        <option value="{{ $valTime }}">{{ $displayTime }}</option>
                                    @endforeach
                                @endfor
                            </select>
                        </div>
                        <div class="field">
                            <label for="inv_duration">Duration</label>
                            <select id="inv_duration" name="duration">
                                <option value="30">30 min</option>
                                <option value="60" selected>1 hour</option>
                                <option value="120">2 hours</option>
                                <option value="240">Half day</option>
                                <option value="480">Full day</option>
                            </select>
                        </div>
                    </div>

                    <!-- Section 3: Recurring Visit -->
                    <div class="section-label">Recurring Visit</div>
                    <div class="field">
                        <label for="inv_recurring">Recurring Visit</label>
                        <select id="inv_recurring" name="recurring">
                            <option value="none">One-time visit</option>
                            <option value="daily">Daily</option>
                            <option value="weekly">Weekly</option>
                            <option value="monthly">Monthly</option>
                        </select>
                    </div>

                    <!-- Section 4: Visit Details -->
                    <div class="section-label">Visit Details</div>
                    <div class="form-grid">
                        <div class="field">
                            <label for="inv_purpose_id">Purpose of Visit *</label>
                            <select id="inv_purpose_id" name="purpose_id" required>
                                @foreach ($purposes as $purpose)
                                    <option value="{{ $purpose->id }}">{{ $purpose->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="field">
                            <label for="inv_visitor_type_id">Visitor Type</label>
                            <select id="inv_visitor_type_id" name="visitor_type_id">
                                @foreach ($visitorTypes as $vType)
                                    <option value="{{ $vType->id }}">{{ $vType->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="field">
                            <label for="inv_site_id">Branch / Location *</label>
                            <select id="inv_site_id" name="site_id" required onchange="filterHostEmployeesBySite(this.value); filterMeetingRoomsBySite(this.value);">
                                @foreach ($sites as $site)
                                    <option value="{{ $site->id }}">{{ $site->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="field">
                            <label for="inv_host_employee_id">Host Employee *</label>
                            <select id="inv_host_employee_id" name="host_employee_id" required>
                                <option value="">Select Host Employee</option>
                                @foreach ($employees as $employee)
                                    <option value="{{ $employee->id }}" data-site="{{ $employee->site_id }}">{{ $employee->full_name }} ({{ $employee->department->name ?? 'General' }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="field">
                            <label for="inv_department_id">Department</label>
                            <select id="inv_department_id" name="department_id">
                                <option value="">Auto-detect from host</option>
                                @foreach ($departments as $dept)
                                    <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="field">
                            <label for="inv_meeting_room">Meeting Room</label>
                            <input type="text" id="inv_meeting_room" name="meeting_room" list="meeting_rooms_list" placeholder="Search or select room..." autocomplete="off">
                            <datalist id="meeting_rooms_list">
                                @foreach ($meetingRooms as $mr)
                                    <option value="{{ $mr->name }}">{{ $mr->name }} ({{ $mr->floor?->name ? $mr->floor->name . ' · ' : '' }}Seats {{ $mr->capacity ?? 8 }})</option>
                                @endforeach
                            </datalist>
                        </div>
                        <div class="field span2">
                            <label for="inv_expected_visitor_count">Number of Visitors</label>
                            <input type="number" id="inv_expected_visitor_count" name="expected_visitor_count" value="1" min="1">
                        </div>
                    </div>

                    <!-- Section 5: Access & Requirements -->
                    <div class="section-label">Access & Requirements</div>
                    <div class="switchrow">
                        <span>Send confirmation email to visitor</span>
                        <label class="switch"><input type="checkbox" id="inv_send_confirmation_email" name="send_confirmation_email" value="1" checked><span class="slider"></span></label>
                    </div>
                    <div class="switchrow">
                        <span>Notify host upon arrival</span>
                        <label class="switch"><input type="checkbox" id="inv_notify_host_on_arrival" name="notify_host_on_arrival" value="1" checked><span class="slider"></span></label>
                    </div>
                    <div class="switchrow">
                        <span>Send QR code for self check-in</span>
                        <label class="switch"><input type="checkbox" id="inv_send_qr_self_checkin" name="send_qr_self_checkin" value="1" checked><span class="slider"></span></label>
                    </div>
                    <div class="switchrow">
                        <span>Requires NDA / Agreement signing</span>
                        <label class="switch"><input type="checkbox" id="inv_requires_nda" name="requires_nda" value="1"><span class="slider"></span></label>
                    </div>
                    <div class="switchrow">
                        <span>Wi-Fi guest access</span>
                        <label class="switch"><input type="checkbox" id="inv_wifi_guest_access" name="wifi_guest_access" value="1"><span class="slider"></span></label>
                    </div>
                    <div class="switchrow">
                        <span>Parking space required</span>
                        <label class="switch"><input type="checkbox" id="inv_parking_required" name="parking_required" value="1"><span class="slider"></span></label>
                    </div>
                    <div class="switchrow">
                        <span>Escort required</span>
                        <label class="switch"><input type="checkbox" id="inv_escort_required" name="escort_required" value="1"><span class="slider"></span></label>
                    </div>

                    <!-- Section 6: Additional Notes -->
                    <div class="section-label">Additional Notes</div>
                    <div class="field">
                        <label for="inv_special_instructions">Special Instructions</label>
                        <textarea id="inv_special_instructions" name="special_instructions" rows="2" placeholder="e.g. Visitor needs wheelchair access, bring laptop for demo, etc."></textarea>
                    </div>
                    <div class="field" style="margin-top: 10px;">
                        <label for="inv_items_to_bring">Items to Bring In</label>
                        <input type="text" id="inv_items_to_bring" name="items_to_bring" placeholder="e.g. Laptop, projector, tools">
                    </div>
                </div>

                <div class="modal-foot">
                    <button type="button" class="btn btn-ghost" onclick="closeInviteModal()">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="btnRegisterNotify" style="display: inline-flex; align-items: center; gap: 8px;">
                        <svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"><path d="M4 11.5 20.5 3.5 12.5 20l-2-6.5-6.5-2z"/><path d="M18.5 5.5 10.5 13.5"/></svg>
                        <span>Register & Notify</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- QR & Access Code Modal -->
    <div id="qrModal" class="modal-root">
        <div class="modal-backdrop" onclick="closeQrModal()"></div>
        <div class="modal-panel" style="max-width: 380px;">
            <div class="modal-head">
                <h3>Visitor Access Code</h3>
                <button type="button" class="btn btn-ghost btn-sm" onclick="closeQrModal()">✕</button>
            </div>
            <div class="modal-body" style="text-align: center;">
                <div class="badge-preview">
                    <div class="bp-top">Zapio VMS — Access Pass</div>
                    <div class="bp-name" id="qrModalVisitorName">John Doe</div>
                    <div class="bp-company" id="qrModalCompany">Acme Corp</div>
                    <div class="bp-qr" id="qrcodeCanvas"></div>
                    <div class="bp-code" id="qrModalCode">ZAP-0000</div>
                </div>
                <div style="font-size: 12px; color: var(--text-muted); margin-top: 14px;">
                    Share this code with the visitor, or have them quote it at reception upon arrival.
                </div>
            </div>
            <div class="modal-foot">
                <button type="button" class="btn" onclick="closeQrModal()">Close</button>
                <button type="button" class="btn btn-primary" onclick="window.print()">
                    <svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75"><path d="M7 8V4h10v4M6 17H4.5A1.5 1.5 0 0 1 3 15.5v-4A1.5 1.5 0 0 1 4.5 10h15a1.5 1.5 0 0 1 1.5 1.5v4a1.5 1.5 0 0 1-1.5 1.5H18M7 14h10v6H7z"/></svg>
                    <span>Print Pass</span>
                </button>
            </div>
        </div>
    </div>

    <script>
        function setStatusFilter(status) {
            document.getElementById('statusInput').value = status;
            document.getElementById('filterForm').submit();
        }

        function filterHostEmployeesBySite(siteId) {
            const hostSelect = document.getElementById('inv_host_employee_id');
            const options = hostSelect.querySelectorAll('option');
            options.forEach(opt => {
                if (!opt.value) return; // Keep "Select Host Employee"
                const empSite = opt.getAttribute('data-site');
                if (!siteId || siteId === 'all' || empSite === siteId || !empSite) {
                    opt.style.display = '';
                    opt.hidden = false;
                    opt.disabled = false;
                } else {
                    opt.style.display = 'none';
                    opt.hidden = true;
                    opt.disabled = true;
                }
            });
            // Reset value if currently selected option is now hidden
            if (hostSelect.selectedOptions[0] && hostSelect.selectedOptions[0].hidden && hostSelect.selectedOptions[0].value) {
                hostSelect.value = '';
            }
        }

        const allMeetingRoomsData = @json($meetingRooms);

        function filterMeetingRoomsBySite(siteId) {
            const dataList = document.getElementById('meeting_rooms_list');
            if (!dataList) return;
            dataList.innerHTML = '';
            
            allMeetingRoomsData.forEach(mr => {
                if (!siteId || siteId === 'all' || String(mr.site_id) === String(siteId)) {
                    const opt = document.createElement('option');
                    opt.value = mr.name;
                    const floorPart = mr.floor ? mr.floor.name + ' · ' : (mr.floor_name ? mr.floor_name + ' · ' : '');
                    opt.label = `${mr.name} (${floorPart}Seats ${mr.capacity || 8})`;
                    opt.textContent = `${mr.name} (${floorPart}Seats ${mr.capacity || 8})`;
                    dataList.appendChild(opt);
                }
            });
        }

        function openInviteModal() {
            const form = document.getElementById('inviteForm');
            const method = document.getElementById('invite_method');
            const title = document.getElementById('modalTitle');

            title.innerText = 'New Pre-registration';
            form.action = "{{ route('pre_registrations.store') }}";
            method.value = 'POST';
            form.reset();

            // Set default date to today and arrival to 10:00
            document.getElementById('inv_name').value = '';
            document.getElementById('inv_company_name').value = '';
            document.getElementById('inv_email').value = '';
            document.getElementById('inv_phone').value = '';
            document.getElementById('inv_phone_code').value = '971';
            document.getElementById('inv_id_number').value = '';
            document.getElementById('inv_visit_date').value = "{{ date('Y-m-d') }}";
            document.getElementById('inv_expected_arrival').value = "10:00";
            document.getElementById('inv_duration').value = "60";

            // Trigger host & room filtering
            const siteId = document.getElementById('inv_site_id').value;
            filterHostEmployeesBySite(siteId);
            filterMeetingRoomsBySite(siteId);

            document.getElementById('inviteModal').classList.add('open');
        }

        function openEditInviteModal(data) {
            const form = document.getElementById('inviteForm');
            const method = document.getElementById('invite_method');
            const title = document.getElementById('modalTitle');

            title.innerText = 'Edit Pre-registration';
            form.action = '/pre-registrations/' + data.id;
            method.value = 'PUT';

            document.getElementById('inv_name').value = data.name || ((data.first_name || '') + ' ' + (data.last_name || '')).trim();
            document.getElementById('inv_company_name').value = data.company_name || '';
            document.getElementById('inv_email').value = data.email || '';
            
            let phoneVal = data.phone || '';
            if (phoneVal.startsWith('+')) {
                let parts = phoneVal.replace('+', '').split(' ', 2);
                document.getElementById('inv_phone_code').value = parts[0] || '971';
                document.getElementById('inv_phone').value = parts[1] || '';
            } else {
                document.getElementById('inv_phone_code').value = '971';
                document.getElementById('inv_phone').value = phoneVal;
            }

            document.getElementById('inv_id_type_id').value = data.id_type_id || '';
            document.getElementById('inv_id_number').value = data.id_number || '';

            document.getElementById('inv_visit_date').value = data.visit_date || '';
            document.getElementById('inv_expected_arrival').value = data.expected_arrival || '10:00';
            document.getElementById('inv_expected_departure').value = data.expected_departure || '';
            document.getElementById('inv_duration').value = data.duration || 60;
            document.getElementById('inv_recurring').value = data.recurring || 'none';

            document.getElementById('inv_purpose_id').value = data.purpose_id || '';
            document.getElementById('inv_visitor_type_id').value = data.visitor_type_id || '';
            document.getElementById('inv_site_id').value = data.site_id || '';
            document.getElementById('inv_department_id').value = data.department_id || '';
            document.getElementById('inv_meeting_room').value = data.meeting_room || '';
            document.getElementById('inv_expected_visitor_count').value = data.expected_visitor_count || 1;

            filterHostEmployeesBySite(data.site_id);
            filterMeetingRoomsBySite(data.site_id);
            document.getElementById('inv_host_employee_id').value = data.host_employee_id || '';

            document.getElementById('inv_send_confirmation_email').checked = !!data.send_confirmation_email;
            document.getElementById('inv_notify_host_on_arrival').checked = !!data.notify_host_on_arrival;
            document.getElementById('inv_send_qr_self_checkin').checked = !!data.send_qr_self_checkin;
            document.getElementById('inv_requires_nda').checked = !!data.requires_nda;
            document.getElementById('inv_wifi_guest_access').checked = !!data.wifi_guest_access;
            document.getElementById('inv_parking_required').checked = !!data.parking_required;
            document.getElementById('inv_escort_required').checked = !!data.escort_required;

            document.getElementById('inv_special_instructions').value = data.special_instructions || '';
            document.getElementById('inv_items_to_bring').value = data.items_to_bring || '';

            document.getElementById('inviteModal').classList.add('open');
        }

        function closeInviteModal() {
            document.getElementById('inviteModal').classList.remove('open');
        }

        function openQrModal(data) {
            document.getElementById('qrModalVisitorName').innerText = data.name || 'Visitor';
            document.getElementById('qrModalCompany').innerText = data.company || '—';
            document.getElementById('qrModalCode').innerText = data.code || 'ZAP-0000';

            const qrContainer = document.getElementById('qrcodeCanvas');
            qrContainer.innerHTML = '';
            new QRCode(qrContainer, {
                text: data.code || 'ZAP-0000',
                width: 120,
                height: 120,
            });

            document.getElementById('qrModal').classList.add('open');
        }

        function closeQrModal() {
            document.getElementById('qrModal').classList.remove('open');
        }
    </script>
</body>
</html>
