<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Visitor Log — Zapio VMS</title>
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
            --critical: #d03b3b;
            --warning: #fab219;
            --radius: 12px;
            --radius-sm: 8px;
            --shadow: 0 1px 2px rgba(14,26,24,.06), 0 8px 24px -12px rgba(14,26,24,.18);
        }
        *, *::before, *::after { box-sizing: border-box; }
        body { margin: 0; font-family: 'IBM Plex Sans', system-ui, -apple-system, sans-serif; background: var(--bg); color: var(--text-primary); font-size: 14px; }
        
        .ic { width: 16px; height: 16px; flex: none; display: inline-block; vertical-align: -3px; }
        .mono { font-variant-numeric: tabular-nums; }
        
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
        .navitem { display: flex; align-items: center; gap: 11px; padding: 9px 10px; border-radius: var(--radius-sm); color: var(--text-secondary); cursor: pointer; font-size: 13.5px; font-weight: 500; border: none; background: none; width: 100%; text-align: start; text-decoration: none; }
        .navitem:hover { background: var(--surface-2); color: var(--text-primary); }
        .navitem.active { background: var(--accent-soft); color: var(--accent); }
        .navitem .ic { width: 18px; height: 18px; flex: none; }

        .pagehead { display: flex; align-items: flex-start; justify-content: space-between; gap: 16px; margin-bottom: 20px; }
        .pagehead h1 { font-size: 21px; margin: 0; }
        .pagehead .sub { color: var(--text-secondary); font-size: 13px; margin-top: 3px; }

        .btn { display: inline-flex; align-items: center; gap: 7px; padding: 8px 14px; border-radius: var(--radius-sm); border: 1px solid var(--border); background: var(--surface); color: var(--text-primary); font-size: 13px; font-weight: 600; cursor: pointer; white-space: nowrap; text-decoration: none; }
        .btn-primary { background: var(--accent); border-color: var(--accent); color: #fff; }
        .btn-primary:hover { background: var(--accent-hover); }
        .btn-outline-danger { border: 1.5px solid rgba(208, 59, 59, 0.4); color: var(--critical); background: transparent; }
        .btn-outline-danger:hover { background: rgba(208, 59, 59, 0.08); border-color: var(--critical); }

        /* 4 Top KPI Stat Cards */
        .grid-4 { display: grid; grid-template-columns: repeat(4, 1fr); gap: 14px; margin-bottom: 20px; }
        .stat-tile { background: var(--surface); border: 1px solid var(--border); border-radius: var(--radius); padding: 14px 18px; display: flex; flex-direction: column; gap: 4px; box-shadow: 0 1px 2px rgba(14,26,24,.03); }
        .stat-tile-top { display: flex; align-items: center; justify-content: space-between; margin-bottom: 4px; }
        .stat-icon { width: 34px; height: 34px; border-radius: 8px; display: flex; align-items: center; justify-content: center; }
        .stat-value { font-size: 26px; font-weight: 700; line-height: 1.1; color: var(--text-primary); }
        .stat-label { font-size: 12px; color: var(--text-secondary); font-weight: 500; margin-top: 2px; }

        .filterbar { display: flex; gap: 12px; align-items: center; margin-bottom: 20px; flex-wrap: wrap; }
        .searchbox { display: flex; align-items: center; gap: 8px; flex: 1; min-width: 240px; background: var(--surface); border: 1px solid var(--border); border-radius: 99px; padding: 7px 14px; }
        .searchbox input { border: none; background: none; outline: none; width: 100%; font-size: 13px; font-family: inherit; color: var(--text-primary); }

        .pill-group { display: flex; align-items: center; gap: 6px; flex-wrap: wrap; }
        .pill-btn {
            display: inline-flex; align-items: center; justify-content: center;
            padding: 6px 14px; border-radius: 99px; font-size: 12.5px; font-weight: 600;
            border: 1.5px solid var(--border); background: var(--surface); color: var(--text-secondary);
            cursor: pointer; text-decoration: none; transition: all 0.15s ease;
        }
        .pill-btn:hover { background: var(--surface-2); color: var(--text-primary); }
        .pill-btn.pill-all.active { background: var(--text-primary); color: #ffffff; border-color: var(--text-primary); }
        .pill-btn.pill-checked-in { color: #0ca30c; border-color: rgba(12, 163, 12, 0.4); }
        .pill-btn.pill-checked-in.active { background: #0ca30c; color: #ffffff; border-color: #0ca30c; }
        .pill-btn.pill-expected { color: #0c6b64; border-color: rgba(12, 107, 100, 0.4); }
        .pill-btn.pill-expected.active { background: #0c6b64; color: #ffffff; border-color: #0c6b64; }
        .pill-btn.pill-checked-out { color: #7c8985; border-color: rgba(124, 137, 133, 0.4); }
        .pill-btn.pill-checked-out.active { background: #7c8985; color: #ffffff; border-color: #7c8985; }
        .pill-btn.pill-overstayed { color: #d03b3b; border-color: rgba(208, 59, 59, 0.4); }
        .pill-btn.pill-overstayed.active { background: #d03b3b; color: #ffffff; border-color: #d03b3b; }

        .select-filter { font-family: inherit; font-size: 13px; padding: 7px 12px; border-radius: 99px; border: 1px solid var(--border); background: var(--surface); color: var(--text-primary); font-weight: 500; cursor: pointer; }

        .tablefoot { display: flex; align-items: center; justify-content: space-between; padding: 10px 14px; border-block-start: 1px solid var(--border); background: var(--surface-2); font-size: 12px; color: var(--text-secondary); }
        .pager { display: flex; gap: 4px; align-items: center; }
        .pager a { width: 26px; height: 26px; border-radius: 6px; border: 1px solid var(--border); background: var(--surface); cursor: pointer; color: var(--text-secondary); font-size: 12px; display: inline-flex; align-items: center; justify-content: center; text-decoration: none; }
        .pager a:hover:not(.disabled) { background: var(--surface-3); }
        .pager a.disabled { opacity: .4; pointer-events: none; cursor: default; }
        .pagesize { display: flex; align-items: center; gap: 6px; white-space: nowrap; }
        .pagesize select { padding: 4px 8px; font-size: 12px; width: auto; border-radius: 6px; }
        .tablewrap { border: 1px solid var(--border); border-radius: var(--radius); overflow: hidden; background: var(--surface); }
        table.dtable { width: 100%; border-collapse: collapse; }
        .dtable thead th { text-align: start; font-size: 11px; text-transform: uppercase; letter-spacing: .05em; color: var(--text-muted); padding: 11px 14px; border-bottom: 1px solid var(--border); background: var(--surface-2); }
        .dtable tbody td { padding: 12px 14px; border-bottom: 1px solid var(--border); font-size: 13px; vertical-align: middle; }
        .cell-name { font-weight: 600; }
        .cell-sub { font-size: 11.5px; color: var(--text-muted); margin-top: 1px; }

        .clickable-row { cursor: pointer; transition: background 0.12s ease; }
        .clickable-row:hover { background: var(--surface-2); }

        /* Row Actions & Icon Buttons */
        .rowactions { display: inline-flex; align-items: center; gap: 5px; justify-content: flex-end; }
        .icon-btn {
            display: inline-flex; align-items: center; justify-content: center;
            width: 30px; height: 30px; border-radius: var(--radius-sm);
            border: 1px solid var(--border); background: var(--surface);
            color: var(--text-secondary); cursor: pointer; transition: all 0.15s ease;
            padding: 0; line-height: 1;
        }
        .icon-btn:hover { background: var(--surface-2); color: var(--text-primary); border-color: var(--accent); }
        .icon-btn .ic { width: 15px; height: 15px; }

        .chip { display: inline-flex; align-items: center; padding: 3px 9px; border-radius: 99px; font-size: 11.5px; font-weight: 600; gap: 4px; }
        .chip-good { background: rgba(12, 163, 12, 0.14); color: var(--good); }
        .chip-info { background: rgba(12, 107, 100, 0.14); color: var(--accent); }
        .chip-muted { background: var(--surface-2); color: var(--text-muted); }
        .chip-critical { background: rgba(208, 59, 59, 0.14); color: var(--critical); }

        .alert-success { background: rgba(12, 163, 12, 0.14); border: 1px solid rgba(12, 163, 12, 0.30); color: var(--good); padding: 12px 16px; border-radius: var(--radius-sm); font-size: 13.5px; margin-bottom: 18px; display: flex; align-items: center; justify-content: space-between; }

        /* Modals */
        .modal-root { display: none; position: fixed; inset: 0; z-index: 100; }
        .modal-root.open { display: block; }
        .modal-backdrop { position: absolute; inset: 0; background: rgba(6,12,11,.5); backdrop-filter: blur(2px); }
        .modal-panel { position: relative; max-width: 540px; width: calc(100% - 32px); margin: 6vh auto 0; background: var(--surface); border-radius: var(--radius); border: 1px solid var(--border); box-shadow: var(--shadow); overflow: hidden; }
        .modal-head { display: flex; align-items: center; justify-content: space-between; padding: 16px 20px; border-bottom: 1px solid var(--border); }
        .modal-head h3 { margin: 0; font-size: 16px; font-weight: 700; }
        .modal-body { padding: 20px 22px; max-height: 75vh; overflow-y: auto; }
        .modal-foot { display: flex; align-items: center; justify-content: space-between; gap: 8px; padding: 14px 20px; border-top: 1px solid var(--border); background: var(--surface-2); flex-wrap: wrap; }

        /* Visit Details Grid */
        .vd-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 16px 24px; margin-top: 18px; }
        .vd-item { display: flex; flex-direction: column; gap: 3px; }
        .vd-label { font-size: 11.5px; color: var(--text-muted); font-weight: 500; }
        .vd-value { font-size: 13.5px; font-weight: 600; color: var(--text-primary); word-break: break-word; }

        /* Badge Preview */
        .badge-preview { width: 280px; border: 1px solid var(--border); border-radius: 16px; padding: 20px; margin: 0 auto; background: linear-gradient(135deg, var(--accent), #094b46); color: #fff; text-align: center; box-shadow: 0 4px 14px rgba(0,0,0,0.12); }
        .badge-preview .bp-top { font-size: 10px; text-transform: uppercase; letter-spacing: .08em; opacity: .8; }
        .badge-preview .bp-name { font-size: 18px; font-weight: 700; margin-top: 10px; }
        .badge-preview .bp-company { font-size: 12px; opacity: .9; margin-top: 2px; }
        .badge-preview .bp-qr { margin: 16px auto; background: #fff; padding: 10px; border-radius: 12px; display: inline-block; }
        .badge-preview .bp-code { font-size: 15px; font-weight: 700; letter-spacing: .08em; background: rgba(255,255,255,0.18); padding: 6px; border-radius: 6px; }
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
        @include('partials.sidebar', ['active' => 'visitors'])

        @include('partials.topbar')

        <main id="main">
            <div class="pagehead">
                <div>
                    <h1>Visitor Log</h1>
                    <div class="sub">Every visitor check-in across your sites.</div>
                </div>
                <div style="display:flex;gap:10px;">
                    <a href="{{ route('visitors.export', request()->query()) }}" class="btn">
                        <svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                        <span>Export CSV</span>
                    </a>
                    <a href="{{ route('visitors.checkin') }}" class="btn btn-primary">+ New Check-In</a>
                </div>
            </div>

            @if (session('success'))
                <div class="alert-success">
                    <div>
                        <strong>{{ session('success') }}</strong>
                        @if (session('badge_number'))
                            — Issued Badge Code: <code style="background:#fff;padding:2px 6px;border-radius:4px;">{{ session('badge_number') }}</code> (Visitor: {{ session('visitor_name') }})
                        @endif
                    </div>
                </div>
            @endif

            <!-- 4 Top KPI Stat Cards -->
            <div class="grid-4">
                <div class="stat-tile">
                    <div class="stat-tile-top">
                        <div class="stat-icon" style="background: rgba(12, 107, 100, 0.10); color: var(--accent);">
                            <svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                        </div>
                    </div>
                    <div class="stat-value">{{ $stats['total_visitors'] ?? 0 }}</div>
                    <div class="stat-label">Total Visitors</div>
                </div>
                <div class="stat-tile">
                    <div class="stat-tile-top">
                        <div class="stat-icon" style="background: rgba(12, 163, 12, 0.12); color: var(--good);">
                            <svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"/><polyline points="10 17 15 12 10 7"/><line x1="15" y1="12" x2="3" y2="12"/></svg>
                        </div>
                    </div>
                    <div class="stat-value" style="color: var(--good);">{{ $stats['checked_in'] ?? 0 }}</div>
                    <div class="stat-label">Checked in</div>
                </div>
                <div class="stat-tile">
                    <div class="stat-tile-top">
                        <div class="stat-icon" style="background: rgba(12, 107, 100, 0.12); color: var(--accent);">
                            <svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                        </div>
                    </div>
                    <div class="stat-value" style="color: var(--accent);">{{ $stats['expected'] ?? 0 }}</div>
                    <div class="stat-label">Expected</div>
                </div>
                <div class="stat-tile">
                    <div class="stat-tile-top">
                        <div class="stat-icon" style="background: rgba(208, 59, 59, 0.12); color: var(--critical);">
                            <svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
                        </div>
                    </div>
                    <div class="stat-value" style="color: var(--critical);">{{ $stats['overstayed'] ?? 0 }}</div>
                    <div class="stat-label">Overstayed</div>
                </div>
            </div>

            <!-- Filter Bar -->
            <form action="{{ route('visitors.index') }}" method="GET" id="filterForm">
                <input type="hidden" name="status" id="statusFilterInput" value="{{ request('status', 'all') }}">
                <input type="hidden" name="per_page" value="{{ $perPageOption }}">

                <div class="filterbar">
                    <div class="searchbox">
                        <svg class="ic" style="color:var(--text-muted);" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
                        <input type="text" name="search" placeholder="Search visitors, ID, company..." value="{{ request('search') }}" onchange="this.form.submit()">
                    </div>

                    <div class="pill-group">
                        @php $currStatus = request('status', 'all'); @endphp
                        <button type="button" onclick="setStatusFilter('all')" class="pill-btn pill-all {{ $currStatus === 'all' ? 'active' : '' }}">All</button>
                        <button type="button" onclick="setStatusFilter('checked_in')" class="pill-btn pill-checked-in {{ $currStatus === 'checked_in' ? 'active' : '' }}">Checked in</button>
                        <button type="button" onclick="setStatusFilter('expected')" class="pill-btn pill-expected {{ $currStatus === 'expected' ? 'active' : '' }}">Expected</button>
                        <button type="button" onclick="setStatusFilter('checked_out')" class="pill-btn pill-checked-out {{ $currStatus === 'checked_out' ? 'active' : '' }}">Checked out</button>
                        <button type="button" onclick="setStatusFilter('overstayed')" class="pill-btn pill-overstayed {{ $currStatus === 'overstayed' ? 'active' : '' }}">Overstayed</button>
                    </div>

                    <select name="site_id" onchange="this.form.submit()" class="select-filter">
                        <option value="all">All Branches</option>
                        @foreach ($sites as $site)
                            <option value="{{ $site->id }}" {{ request('site_id') == $site->id ? 'selected' : '' }}>{{ $site->name }}</option>
                        @endforeach
                    </select>

                    <select name="purpose_id" onchange="this.form.submit()" class="select-filter">
                        <option value="all">All Purposes</option>
                        @foreach ($purposes as $purpose)
                            <option value="{{ $purpose->id }}" {{ request('purpose_id') == $purpose->id ? 'selected' : '' }}>{{ $purpose->name }}</option>
                        @endforeach
                    </select>

                    <select name="date_range" onchange="this.form.submit()" class="select-filter">
                        <option value="all" {{ request('date_range') == 'all' ? 'selected' : '' }}>All time</option>
                        <option value="today" {{ request('date_range') == 'today' ? 'selected' : '' }}>Today</option>
                        <option value="last_7_days" {{ request('date_range', 'all') == 'last_7_days' ? 'selected' : '' }}>Last 7 days</option>
                        <option value="last_30_days" {{ request('date_range') == 'last_30_days' ? 'selected' : '' }}>Last 30 days</option>
                        <option value="this_month" {{ request('date_range') == 'this_month' ? 'selected' : '' }}>This Month</option>
                    </select>
                </div>
            </form>

            <div class="tablewrap scrollx">
                <table class="dtable">
                    <thead>
                        <tr>
                            <th>Visitor</th>
                            <th>Company</th>
                            <th>Host</th>
                            <th>Purpose</th>
                            <th>Branch</th>
                            <th>Check-In</th>
                            <th>Status</th>
                            <th style="text-align:end;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($visits as $visit)
                            @php
                                $visitor = $visit->visitor;
                                $durationText = '—';
                                if ($visit->check_in_at) {
                                    $endTime = $visit->check_out_at ?? now();
                                    $diffMinutes = max(0, $visit->check_in_at->diffInMinutes($endTime));
                                    $hours = floor($diffMinutes / 60);
                                    $minutes = $diffMinutes % 60;
                                    $durationText = $hours > 0 ? "{$hours}h {$minutes}m" : "{$minutes}m";
                                }

                                $passCode = $visit->badges->first()?->badge_number ?? ($visit->invitation?->invitation_code ?? 'MDK-' . str_pad($visit->id, 5, '0', STR_PAD_LEFT));

                                $hostDepartment = $visit->department?->name ?? ($visit->hostEmployee?->department?->name ?? '');
                                $hostText = ($visit->hostEmployee->full_name ?? '—') . ($hostDepartment ? ' (' . $hostDepartment . ')' : '');

                                $detailsData = [
                                    'id' => $visit->id,
                                    'name' => $visitor->full_name ?? 'Visitor',
                                    'nationality' => $visitor->nationality?->name ?? '—',
                                    'id_type' => $visitor->idType?->name ?? 'National ID',
                                    'id_number' => $visitor->id_number ?? '—',
                                    'status' => $visit->status,
                                    'company' => $visitor->company_name ?? '—',
                                    'host' => $hostText,
                                    'purpose' => $visit->purpose->name ?? '—',
                                    'branch' => $visit->site->name ?? '—',
                                    'phone' => $visitor->phone ?? '—',
                                    'duration' => $durationText,
                                    'checked_in_at' => $visit->check_in_at ? $visit->check_in_at->format('d M, h:i A') : ($visit->scheduled_start_at ? $visit->scheduled_start_at->format('d M, h:i A') . ' (Expected)' : '—'),
                                    'checked_out_at' => $visit->check_out_at ? $visit->check_out_at->format('d M, h:i A') : (in_array($visit->status, ['checked_in', 'overstayed']) ? 'Still on premises' : '—'),
                                    'pass_code' => $passCode,
                                ];
                            @endphp
                            <tr class="clickable-row" onclick="handleRowClick(event, {{ json_encode($detailsData) }})">
                                <td>
                                    <div class="cell-name">{{ $visitor->full_name ?? 'Visitor' }}</div>
                                    <div class="cell-sub mono">{{ $visitor->phone ?? ($visitor->id_number ?? '') }}</div>
                                </td>
                                <td>{{ $visitor->company_name ?? '—' }}</td>
                                <td>
                                    <div>{{ $visit->hostEmployee->full_name ?? '—' }}</div>
                                    <div class="cell-sub">{{ $hostDepartment }}</div>
                                </td>
                                <td>{{ $visit->purpose->name ?? '—' }}</td>
                                <td>{{ $visit->site->name ?? '—' }}</td>
                                <td class="mono">
                                    {{ $visit->check_in_at ? $visit->check_in_at->format('d M, h:i A') : ($visit->scheduled_start_at ? $visit->scheduled_start_at->format('d M, h:i A') : '—') }}
                                </td>
                                <td>
                                    @if ($visit->status === 'checked_in')
                                        <span class="chip chip-good">● Checked in</span>
                                    @elseif ($visit->status === 'expected')
                                        <span class="chip chip-info">Expected</span>
                                    @elseif ($visit->status === 'overstayed')
                                        <span class="chip chip-critical">● Overstayed</span>
                                    @elseif ($visit->status === 'checked_out')
                                        <span class="chip chip-muted">Checked out</span>
                                    @else
                                        <span class="chip chip-muted">{{ ucfirst($visit->status) }}</span>
                                    @endif
                                </td>
                                <td style="text-align:end;" onclick="event.stopPropagation();">
                                    <div class="rowactions">
                                        @if ($visit->status === 'expected')
                                            <button type="button" class="icon-btn" title="View Pass / Barcode" onclick="openPassModal({{ json_encode($detailsData) }})">
                                                <svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
                                            </button>
                                        @else
                                            @if (in_array($visit->status, ['checked_in', 'overstayed']))
                                                <form action="{{ route('visits.checkout', $visit->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Check out {{ addslashes($visitor->full_name ?? 'this visitor') }}?')">
                                                    @csrf
                                                    <button type="submit" class="icon-btn" title="Check Out">
                                                        <svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
                                                    </button>
                                                </form>
                                            @endif
                                            <button type="button" class="icon-btn" title="View Pass / Barcode" onclick="openPassModal({{ json_encode($detailsData) }})">
                                                <svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
                                            </button>
                                            <button type="button" class="icon-btn" title="View Visit Details" onclick="openVisitDetailsModal({{ json_encode($detailsData) }})">
                                                <svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" style="text-align:center;padding:40px;color:var(--text-muted);">
                                    No visitor logs match your search.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

                <!-- Table Footer: record count, rows per page, pager -->
                <div class="tablefoot">
                    <span class="tabular">{{ $visits->total() }} records</span>
                    <div style="display:flex; align-items:center; gap:16px;">
                        <div class="pagesize">
                            <span>Rows per page</span>
                            <select class="select-filter" onchange="setPerPage(this.value)">
                                @foreach (['25', '50', '100', '500', 'all'] as $opt)
                                    <option value="{{ $opt }}" {{ $perPageOption === $opt ? 'selected' : '' }}>{{ $opt === 'all' ? 'All' : $opt }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="pager">
                            <a href="{{ $visits->previousPageUrl() ?? 'javascript:void(0)' }}" class="{{ $visits->onFirstPage() ? 'disabled' : '' }}" {{ $visits->onFirstPage() ? 'tabindex=-1' : '' }} title="Previous page">
                                <svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:13px;height:13px;transform:rotate(180deg);"><polyline points="9 18 15 12 9 6"/></svg>
                            </a>
                            <span class="mono">{{ $visits->currentPage() }} / {{ max(1, $visits->lastPage()) }}</span>
                            <a href="{{ $visits->nextPageUrl() ?? 'javascript:void(0)' }}" class="{{ $visits->hasMorePages() ? '' : 'disabled' }}" {{ $visits->hasMorePages() ? '' : 'tabindex=-1' }} title="Next page">
                                <svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:13px;height:13px;"><polyline points="9 18 15 12 9 6"/></svg>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <!-- 1. Visit Details Modal (Exact replica of Image 2) -->
    <div id="modalVisitDetails" class="modal-root">
        <div class="modal-backdrop" onclick="closeVisitDetailsModal()"></div>
        <div class="modal-panel">
            <div class="modal-head">
                <h3>Visit Details</h3>
                <button type="button" class="icon-btn" onclick="closeVisitDetailsModal()" style="border:none;background:none;font-size:16px;">✕</button>
            </div>
            <div class="modal-body">
                <div style="display: flex; align-items: flex-start; justify-content: space-between; gap: 12px; margin-bottom: 18px;">
                    <div>
                        <div id="vdName" style="font-size: 17px; font-weight: 700; color: var(--text-primary);">—</div>
                        <div id="vdSubtitle" style="font-size: 12.5px; color: var(--text-muted); margin-top: 3px;">—</div>
                    </div>
                    <div id="vdStatusWrap">
                        <span id="vdStatusChip" class="chip chip-good">● Checked in</span>
                    </div>
                </div>

                <div class="vd-grid">
                    <div class="vd-item">
                        <div class="vd-label">Company</div>
                        <div class="vd-value" id="vdCompany">—</div>
                    </div>
                    <div class="vd-item">
                        <div class="vd-label">Host</div>
                        <div class="vd-value" id="vdHost">—</div>
                    </div>
                    <div class="vd-item">
                        <div class="vd-label">Purpose</div>
                        <div class="vd-value" id="vdPurpose">—</div>
                    </div>
                    <div class="vd-item">
                        <div class="vd-label">Branch</div>
                        <div class="vd-value" id="vdBranch">—</div>
                    </div>
                    <div class="vd-item">
                        <div class="vd-label">Phone number</div>
                        <div class="vd-value mono" id="vdPhone">—</div>
                    </div>
                    <div class="vd-item">
                        <div class="vd-label">Duration</div>
                        <div class="vd-value mono" id="vdDuration">—</div>
                    </div>
                    <div class="vd-item">
                        <div class="vd-label">Checked in at</div>
                        <div class="vd-value mono" id="vdCheckedInAt">—</div>
                    </div>
                    <div class="vd-item">
                        <div class="vd-label">Checked out at</div>
                        <div class="vd-value mono" id="vdCheckedOutAt">—</div>
                    </div>
                </div>
            </div>
            <div class="modal-foot">
                <form id="vdWatchlistForm" method="POST" action="" style="margin: 0;">
                    @csrf
                    <button type="submit" class="btn btn-outline-danger" onclick="return confirm('Add this visitor to the security watchlist?')">
                        <svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                        <span>Add to watchlist</span>
                    </button>
                </form>
                <div style="display: flex; gap: 8px; align-items: center; margin-inline-start: auto;">
                    <button type="button" class="btn" onclick="openPassFromDetails()">
                        <svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
                        <span>View code</span>
                    </button>
                    <button type="button" class="btn" onclick="reprintBadgeFromDetails()">
                        <svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 6 2 18 2 18 9"/><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><rect x="6" y="14" width="12" height="8"/></svg>
                        <span>Reprint badge</span>
                    </button>
                    <form id="vdCheckoutForm" method="POST" action="" style="margin: 0; display: inline;">
                        @csrf
                        <button type="submit" class="btn btn-primary" id="vdCheckoutBtn" style="background: var(--accent); color: #fff;" onclick="return confirm('Check out this visitor?')">
                            <svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
                            <span>Check out</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- 2. Pass / Barcode Modal -->
    <div id="modalPass" class="modal-root">
        <div class="modal-backdrop" onclick="closePassModal()"></div>
        <div class="modal-panel" style="max-width: 380px;">
            <div class="modal-head">
                <h3>Visitor Access Pass</h3>
                <button type="button" class="icon-btn" onclick="closePassModal()" style="border:none;background:none;font-size:16px;">✕</button>
            </div>
            <div class="modal-body" style="text-align: center;">
                <div class="badge-preview">
                    <div class="bp-top">Zapio VMS — Access Pass</div>
                    <div class="bp-name" id="passModalVisitorName">John Doe</div>
                    <div class="bp-company" id="passModalCompany">Acme Corp</div>
                    <div class="bp-qr" id="passQrCanvas"></div>
                    <div class="bp-code mono" id="passModalCode">MDK-00000</div>
                </div>
                <div style="font-size: 12px; color: var(--text-muted); margin-top: 14px;">
                    Scan this QR code at security gates, reception kiosks, or quote it upon arrival.
                </div>
            </div>
            <div class="modal-foot" style="justify-content: flex-end;">
                <button type="button" class="btn" onclick="closePassModal()">Close</button>
                <button type="button" class="btn btn-primary" onclick="window.print()">
                    <svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75"><path d="M7 8V4h10v4M6 17H4.5A1.5 1.5 0 0 1 3 15.5v-4A1.5 1.5 0 0 1 4.5 10h15a1.5 1.5 0 0 1 1.5 1.5v4a1.5 1.5 0 0 1-1.5 1.5H18M7 14h10v6H7z"/></svg>
                    <span>Print Pass</span>
                </button>
            </div>
        </div>
    </div>

    <script>
        function setPerPage(value) {
            const url = new URL(window.location.href);
            url.searchParams.set('per_page', value);
            url.searchParams.delete('page');
            window.location.href = url.toString();
        }

        function setStatusFilter(val) {
            document.getElementById('statusFilterInput').value = val;
            document.getElementById('filterForm').submit();
        }

        let currentVisitDetailData = null;

        function openVisitDetailsModal(data) {
            currentVisitDetailData = data;
            document.getElementById('vdName').innerText = data.name || 'Visitor';
            
            // Subtitle: Nationality · ID Type ID Number
            const parts = [];
            if (data.nationality && data.nationality !== '—') {
                parts.push(data.nationality);
            }
            const idType = data.id_type || 'ID';
            const idNum = (data.id_number && data.id_number !== '—') ? data.id_number : '';
            if (idNum) {
                parts.push(`${idType} ${idNum}`);
            } else if (data.id_type) {
                parts.push(data.id_type);
            }
            document.getElementById('vdSubtitle').innerText = parts.join(' · ') || '—';

            // Status chip
            const statusWrap = document.getElementById('vdStatusWrap');
            let chipHtml = '';
            if (data.status === 'checked_in') {
                chipHtml = '<span class="chip chip-good">● Checked in</span>';
            } else if (data.status === 'overstayed') {
                chipHtml = '<span class="chip chip-critical">● Overstayed</span>';
            } else if (data.status === 'expected') {
                chipHtml = '<span class="chip chip-info">Expected</span>';
            } else if (data.status === 'checked_out') {
                chipHtml = '<span class="chip chip-muted">Checked out</span>';
            } else {
                chipHtml = `<span class="chip chip-muted">${data.status}</span>`;
            }
            statusWrap.innerHTML = chipHtml;

            // Grid fields
            document.getElementById('vdCompany').innerText = data.company || '—';
            document.getElementById('vdHost').innerText = data.host || '—';
            document.getElementById('vdPurpose').innerText = data.purpose || '—';
            document.getElementById('vdBranch').innerText = data.branch || '—';
            document.getElementById('vdPhone').innerText = data.phone || '—';
            document.getElementById('vdDuration').innerText = data.duration || '—';
            document.getElementById('vdCheckedInAt').innerText = data.checked_in_at || '—';
            document.getElementById('vdCheckedOutAt').innerText = data.checked_out_at || '—';

            // Forms setup
            document.getElementById('vdWatchlistForm').action = '/visits/' + data.id + '/watchlist';
            
            const checkoutForm = document.getElementById('vdCheckoutForm');
            if (data.status === 'checked_in' || data.status === 'overstayed') {
                checkoutForm.action = '/visits/' + data.id + '/checkout';
                checkoutForm.style.display = 'inline';
            } else {
                checkoutForm.style.display = 'none';
            }

            document.getElementById('modalVisitDetails').classList.add('open');
        }

        function closeVisitDetailsModal() {
            document.getElementById('modalVisitDetails').classList.remove('open');
        }

        function openPassModal(data) {
            document.getElementById('passModalVisitorName').innerText = data.name || 'Visitor';
            document.getElementById('passModalCompany').innerText = data.company || '—';
            document.getElementById('passModalCode').innerText = data.pass_code || 'MDK-00000';

            const qrContainer = document.getElementById('passQrCanvas');
            qrContainer.innerHTML = '';
            if (typeof QRCode !== 'undefined') {
                new QRCode(qrContainer, {
                    text: data.pass_code || 'MDK-00000',
                    width: 120,
                    height: 120,
                    colorDark : "#0e1a18",
                    colorLight : "#ffffff",
                    correctLevel : QRCode.CorrectLevel.M
                });
            }

            document.getElementById('modalPass').classList.add('open');
        }

        function closePassModal() {
            document.getElementById('modalPass').classList.remove('open');
        }

        function openPassFromDetails() {
            if (!currentVisitDetailData) return;
            closeVisitDetailsModal();
            openPassModal(currentVisitDetailData);
        }

        function reprintBadgeFromDetails() {
            if (!currentVisitDetailData) return;
            closeVisitDetailsModal();
            openPassModal(currentVisitDetailData);
            setTimeout(() => {
                window.print();
            }, 350);
        }

        function handleRowClick(event, data) {
            if (event.target.closest('button, a, form, input, select')) return;
            openVisitDetailsModal(data);
        }

        // Close modals on Escape key
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                closeVisitDetailsModal();
                closePassModal();
            }
        });
    </script>
    <script>
        function toggleSidebar() {
            document.body.classList.toggle('sidebar-open');
        }
    </script>
</body>
</html>