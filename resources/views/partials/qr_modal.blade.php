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
            .sidebar-overlay { display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.4); z-index: 998; opacity: 0; transition: opacity 0.2s ease; }
        .mobile-only-btn { display: none !important; }
        
        @@media (max-width: 900px) {
            #app { display: flex !important; flex-direction: column !important; min-height: 100vh; overflow-x: hidden; }
            #sidebar { 
                position: fixed !important; left: 0; top: 0; bottom: 0; z-index: 999; 
                width: 260px; transform: translateX(-100%); transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1); 
                box-shadow: 4px 0 24px rgba(0,0,0,0.1); 
            }
            body.sidebar-open #sidebar { transform: translateX(0); }
            body.sidebar-open .sidebar-overlay { display: block; opacity: 1; }
            
            #topbar { width: 100%; grid-column: auto !important; grid-row: auto !important; padding-inline: 12px !important; }
            #main { padding: 16px 14px 60px !important; grid-column: auto !important; grid-row: auto !important; width: 100%; flex: 1; }
            
            .mobile-only-btn { display: inline-flex !important; }
            
            /* Responsive Utilities */
            .grid-4, .grid-3, .form-grid { grid-template-columns: 1fr !important; }
            .grid-2 { grid-template-columns: 1fr !important; }
            .pagehead { flex-direction: column; align-items: flex-start !important; gap: 12px; height: auto !important; padding-bottom: 12px; }
            .pagehead > div:last-child { width: 100%; display: flex; flex-direction: column; gap: 8px; }
            .pagehead button { width: 100%; justify-content: center; }
            .filterbar { flex-direction: column; align-items: stretch !important; gap: 10px; }
            .searchbox { max-width: 100% !important; }
            
            table { display: block; overflow-x: auto; white-space: nowrap; }
            .modal-panel { width: 92% !important; max-height: 90vh !important; margin: auto; }
            .dtable th, .dtable td { padding: 10px 12px !important; }
        }
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
