<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Deliveries — Zapio VMS</title>
    <link rel="icon" href="{{ !empty($globalBranding['favicon_url']) ? $globalBranding['favicon_url'] : asset('favicon.ico') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=IBM+Plex+Sans:wght@400;500;600;700&display=swap">
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
            --s1: #2a78d6;
            --s3: #1baf7a;
            --s4: #eda100;
            --s7: #4a3aa7;
            --radius: 12px;
            --radius-sm: 8px;
            --shadow: 0 1px 2px rgba(14,26,24,.06), 0 8px 24px -12px rgba(14,26,24,.18);
        }
        *, *::before, *::after { box-sizing: border-box; }
        body { margin: 0; font-family: 'IBM Plex Sans', system-ui, -apple-system, sans-serif; background: var(--bg); color: var(--text-primary); font-size: 14px; }
        .ic { width: 16px; height: 16px; flex: none; display: inline-block; vertical-align: -3px; }
        .mono { font-variant-numeric: tabular-nums; font-family: inherit; }
        .tabular { font-variant-numeric: tabular-nums; }
        
        #app { display: grid; grid-template-columns: 248px 1fr; grid-template-rows: 60px 1fr; min-height: 100vh; }
        #sidebar { grid-row: 1/3; grid-column: 1; background: var(--surface); border-inline-end: 1px solid var(--border); display: flex; flex-direction: column; }
        #topbar { grid-row: 1; grid-column: 2; background: var(--surface); border-block-end: 1px solid var(--border); display: flex; align-items: center; gap: 14px; padding-inline: 20px; }
        #main { grid-row: 2; grid-column: 2; padding: 24px 28px 60px; overflow-y: auto; }

        .brand { display: flex; align-items: center; gap: 10px; padding: 16px 18px; border-block-end: 1px solid var(--border); }
        .brand-logo { max-height: 38px; width: auto; }

        nav.navlist { padding: 10px; display: flex; flex-direction: column; gap: 2px; flex: 1; }
        .navlabel { font-size: 10.5px; text-transform: uppercase; letter-spacing: .06em; color: var(--text-muted); padding: 14px 10px 6px; }
        .navitem { display: flex; align-items: center; gap: 11px; padding: 9px 10px; border-radius: var(--radius-sm); color: var(--text-secondary); cursor: pointer; font-size: 13.5px; font-weight: 500; border: none; background: none; width: 100%; text-align: start; text-decoration: none; }
        .navitem:hover { background: var(--surface-2); color: var(--text-primary); }
        .navitem.active { background: var(--accent-soft); color: var(--accent); }
        .navitem .ic { width: 18px; height: 18px; flex: none; }

        .pagehead { display: flex; align-items: flex-start; justify-content: space-between; gap: 16px; margin-bottom: 20px; flex-wrap: wrap; }
        .pagehead h1 { font-size: 21px; margin: 0; font-weight: 700; }
        .pagehead .sub { color: var(--text-secondary); font-size: 13px; margin-top: 4px; }
        .pagehead-actions { display: flex; gap: 8px; flex-wrap: wrap; }

        .btn { display: inline-flex; align-items: center; gap: 7px; padding: 8px 14px; border-radius: var(--radius-sm); border: 1px solid var(--border); background: var(--surface); color: var(--text-primary); font-size: 13px; font-weight: 600; cursor: pointer; white-space: nowrap; text-decoration: none; transition: background .12s ease; }
        .btn .ic { width: 15px; height: 15px; }
        .btn:hover { background: var(--surface-2); }
        .btn-primary { background: var(--accent); border-color: var(--accent); color: #fff; }
        .btn-primary:hover { background: var(--accent-hover); }
        .btn-danger { background: transparent; border-color: var(--critical); color: var(--critical); }
        .btn-danger:hover { background: rgba(208, 59, 59, 0.10); }
        .btn-ghost { border-color: transparent; background: transparent; }
        .btn-ghost:hover { background: var(--surface-2); }
        .btn-sm { padding: 5px 10px; font-size: 12px; }

        /* Stat Grid (4 KPI Cards) */
        .grid { display: grid; gap: 14px; }
        .grid-4 { grid-template-columns: repeat(4, minmax(0, 1fr)); margin-bottom: 16px; }
        @media (max-width: 1100px) { .grid-4 { grid-template-columns: repeat(2, minmax(0, 1fr)); } }
        @media (max-width: 620px) { .grid-4 { grid-template-columns: minmax(0, 1fr); } }

        .stat-tile { background: var(--surface); border: 1px solid var(--border); border-radius: var(--radius); padding: 16px 18px; display: flex; flex-direction: column; gap: 8px; }
        .stat-value { font-size: 26px; font-weight: 700; letter-spacing: -.01em; line-height: 1; }
        .stat-label { font-size: 12px; color: var(--text-secondary); font-weight: 500; }
        .icon-sq { width: 36px; height: 36px; border-radius: 10px; display: flex; align-items: center; justify-content: center; }
        .icon-tint-s1 { background: color-mix(in srgb, var(--s1) 16%, transparent); color: var(--s1); }
        .icon-tint-s4 { background: color-mix(in srgb, var(--s4) 18%, transparent); color: #8a5a00; }
        .icon-tint-s3 { background: color-mix(in srgb, var(--s3) 16%, transparent); color: var(--s3); }
        .icon-tint-s7 { background: color-mix(in srgb, var(--s7) 16%, transparent); color: var(--s7); }

        /* Filter bar & Status Pills */
        .pillrow-wrapper { display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 10px; margin-bottom: 14px; }
        .pillrow { display: flex; gap: 8px; flex-wrap: wrap; }
        .statuspill { display: inline-flex; align-items: center; gap: 6px; padding: 7px 14px; border-radius: 99px; border: 1.5px solid var(--border); background: var(--surface); font-size: 12.5px; font-weight: 600; cursor: pointer; color: var(--pill-color, var(--text-secondary)); font-family: inherit; text-decoration: none; }
        .statuspill:hover { background: var(--surface-2); }
        .statuspill.active { border-color: var(--pill-color, var(--accent)); background: color-mix(in srgb, var(--pill-color, var(--accent)) 12%, var(--surface)); color: var(--pill-color, var(--accent)); }
        .statuspill.pill-all.active { background: var(--text-primary); color: #fff; border-color: var(--text-primary); }

        .filterbar { display: flex; gap: 10px; flex-wrap: wrap; align-items: center; margin-bottom: 14px; }
        .searchbox { flex: 1; max-width: 300px; display: flex; align-items: center; gap: 8px; background: var(--surface); border: 1px solid var(--border); border-radius: 99px; padding: 7px 14px; color: var(--text-muted); }
        .searchbox input { border: none; background: none; outline: none; color: var(--text-primary); font-size: 13px; width: 100%; font-family: inherit; }
        .select-filter { font-family: inherit; font-size: 13px; padding: 7px 14px; border-radius: 99px; border: 1px solid var(--border); background: var(--surface); color: var(--text-primary); font-weight: 500; cursor: pointer; }

        /* Table */
        .tablewrap { border: 1px solid var(--border); border-radius: var(--radius); overflow: hidden; background: var(--surface); }
        .tablewrap.scrollx { overflow-x: auto; }
        table.dtable { width: 100%; border-collapse: collapse; min-width: 820px; }
        .dtable thead th { text-align: start; font-size: 11px; text-transform: uppercase; letter-spacing: .05em; color: var(--text-muted); font-weight: 600; padding: 11px 14px; border-block-end: 1px solid var(--border); background: var(--surface-2); white-space: nowrap; }
        .dtable thead th.nosort { cursor: default; }
        .dtable tbody td { padding: 12px 14px; border-block-end: 1px solid var(--border); font-size: 13px; vertical-align: middle; }
        .dtable tbody tr:last-child td { border-block-end: none; }
        .dtable tbody tr:hover { background: var(--surface-2); }
        .cell-name { font-weight: 600; }
        .cell-sub { font-size: 11.5px; color: var(--text-muted); margin-top: 1px; }

        /* Status Chips */
        .chip { display: inline-flex; align-items: center; gap: 5px; padding: 3px 9px; border-radius: 99px; font-size: 11.5px; font-weight: 600; white-space: nowrap; }
        .chip-good { background: color-mix(in srgb, var(--good) 14%, transparent); color: var(--good); }
        .chip-warning { background: color-mix(in srgb, var(--warning) 18%, transparent); color: #8a5a00; }
        .chip-critical { background: color-mix(in srgb, var(--critical) 14%, transparent); color: var(--critical); }
        .chip-dot { display: inline-block; width: 6px; height: 6px; border-radius: 50%; background: currentColor; }

        /* Row Actions */
        .rowactions { display: inline-flex; align-items: center; gap: 4px; justify-content: flex-end; }
        .rowactions .btn-sm { width: 28px; height: 28px; padding: 0; display: inline-flex; align-items: center; justify-content: center; border-radius: var(--radius-sm); border: 1px solid var(--border); }
        .rowactions .btn-primary { background: var(--accent); border-color: var(--accent); color: #fff; }
        .rowactions .btn-primary:hover { background: var(--accent-hover); }
        .rowactions .btn-danger { color: var(--critical); border-color: var(--critical); }
        .rowactions .btn-danger:hover { background: rgba(208, 59, 59, 0.1); }
        .rowactions .btn-ghost { border-color: transparent; color: var(--text-secondary); }
        .rowactions .btn-ghost:hover { background: var(--surface-3); color: var(--text-primary); }

        /* Table Footer */
        .tablefoot { display: flex; align-items: center; justify-content: space-between; padding: 10px 14px; border-block-start: 1px solid var(--border); background: var(--surface-2); font-size: 12px; color: var(--text-secondary); }
        .pager { display: flex; gap: 4px; align-items: center; }
        .pager button, .pager a { width: 26px; height: 26px; border-radius: 6px; border: 1px solid var(--border); background: var(--surface); cursor: pointer; color: var(--text-secondary); font-size: 12px; display: inline-flex; align-items: center; justify-content: center; text-decoration: none; }
        .pager button:hover:not(:disabled), .pager a:hover:not(.disabled) { background: var(--surface-3); }
        .pager a.disabled, .pager button:disabled { opacity: .4; pointer-events: none; cursor: default; }
        .pagesize { display: flex; align-items: center; gap: 6px; white-space: nowrap; }

        /* Modals */
        .modal-root { display: none; position: fixed; inset: 0; z-index: 100; }
        .modal-root.open { display: block; }
        .modal-backdrop { position: absolute; inset: 0; background: rgba(6,12,11,.5); backdrop-filter: blur(2px); }
        .modal-panel { position: relative; max-width: 580px; width: calc(100% - 32px); margin: 6vh auto 0; background: var(--surface); border-radius: var(--radius); border: 1px solid var(--border); box-shadow: var(--shadow); overflow: hidden; }
        .modal-head { display: flex; align-items: center; justify-content: space-between; padding: 16px 20px; border-bottom: 1px solid var(--border); }
        .modal-head h3 { margin: 0; font-size: 16px; font-weight: 700; }
        .modal-body { padding: 20px; max-height: calc(88vh - 130px); overflow-y: auto; }
        .modal-foot { display: flex; align-items: center; justify-content: flex-end; gap: 8px; padding: 14px 20px; border-top: 1px solid var(--border); background: var(--surface-2); }
        
        .form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; margin-bottom: 12px; }
        .field { display: flex; flex-direction: column; gap: 5px; margin-bottom: 12px; }
        .field label { font-size: 12.5px; font-weight: 600; color: var(--text-secondary); }
        .field input, .field select, .field textarea { font-family: inherit; font-size: 13.5px; padding: 9px 11px; border-radius: var(--radius-sm); border: 1px solid var(--border); background: var(--surface); color: var(--text-primary); width: 100%; box-sizing: border-box; }
        .field input:focus, .field select:focus, .field textarea:focus { outline: none; border-color: var(--accent); }
        
        .switchrow { display: flex; align-items: center; justify-content: space-between; gap: 12px; padding: 11px 0; border-block-start: 1px solid var(--border); margin-top: 6px; }
        .switchrow .sr-label { font-size: 13px; font-weight: 600; }
        .switchrow .sr-sub { font-size: 11.5px; color: var(--text-muted); margin-top: 1px; }

        .empty-state { padding: 48px 20px; text-align: center; color: var(--text-muted); font-size: 13px; }

        /* Notification Toast */
        .toast-container { position: fixed; bottom: 24px; right: 24px; z-index: 1000; display: flex; flex-direction: column; gap: 10px; }
        .toast { display: flex; align-items: center; gap: 10px; padding: 12px 18px; border-radius: var(--radius-sm); background: var(--text-primary); color: #fff; font-size: 13px; font-weight: 500; box-shadow: var(--shadow); }
        .toast-success { background: #0c6b64; }
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
    <!-- Sidebar -->
    @include('partials.sidebar', ['active' => 'deliveries'])

    <!-- Topbar -->
    @include('partials.topbar')

    <!-- Main Content -->
    <main id="main">
        <!-- Toast feedback -->
        @if(session('error'))
            <div style="position: fixed; top: 76px; right: 24px; z-index: 1000; max-width: 460px; background: #fdecec; border: 1px solid rgba(208, 59, 59, 0.35); color: #b42318; padding: 12px 16px; border-radius: 10px; box-shadow: 0 8px 24px -12px rgba(0,0,0,.25); font-size: 13.5px; font-weight: 500; display: flex; gap: 10px; align-items: flex-start;" id="errorToast">
                <span>⚠ {{ session('error') }}</span>
                <button type="button" onclick="this.parentElement.remove()" style="border: none; background: none; color: inherit; cursor: pointer; font-size: 14px;">✕</button>
            </div>
        @endif
        @if(session('success'))
            <div class="toast-container" id="toastContainer">
                <div class="toast toast-success">
                    <svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>
                    <span>{{ session('success') }}</span>
                </div>
            </div>
            <script>
                setTimeout(() => {
                    const el = document.getElementById('toastContainer');
                    if (el) el.style.display = 'none';
                }, 4000);
            </script>
        @endif

        <!-- Page Header -->
        <div class="pagehead">
            <div>
                <h1>Deliveries</h1>
                <div class="sub">Log incoming packages and track them from arrival to pickup.</div>
            </div>
        </div>

        <!-- 4 KPI Stat Cards -->
        <div class="grid grid-4">
            <!-- Received Today -->
            <div class="stat-tile">
                <div class="icon-sq icon-tint-s1">
                    <svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 3 3 8v8l9 5 9-5V8l-9-5z"/><path d="M3 8l9 5 9-5M12 13v8"/></svg>
                </div>
                <div class="stat-value tabular">{{ $stats['receivedToday'] }}</div>
                <div class="stat-label">Received Today</div>
            </div>

            <!-- Awaiting Pickup -->
            <div class="stat-tile">
                <div class="icon-sq icon-tint-s4">
                    <svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="9"/><polyline points="12 7 12 12 15 15"/></svg>
                </div>
                <div class="stat-value tabular">{{ $stats['awaitingPickup'] }}</div>
                <div class="stat-label">Awaiting Pickup</div>
            </div>

            <!-- Collected Today -->
            <div class="stat-tile">
                <div class="icon-sq icon-tint-s3">
                    <svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                </div>
                <div class="stat-value tabular">{{ $stats['collectedToday'] }}</div>
                <div class="stat-label">Collected Today</div>
            </div>

            <!-- Unclaimed 3+ Days -->
            <div class="stat-tile">
                <div class="icon-sq icon-tint-s7">
                    <svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3Z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
                </div>
                <div class="stat-value tabular">{{ $stats['unclaimed'] }}</div>
                <div class="stat-label">Unclaimed 3+ Days</div>
            </div>
        </div>

        <!-- Status Pills & Actions Row -->
        <div class="pillrow-wrapper">
            <div class="pillrow">
                <a href="{{ route('deliveries.index', array_merge(request()->except('status', 'page'), ['status' => 'all'])) }}"
                   class="statuspill pill-all {{ $statusFilter === 'all' ? 'active' : '' }}">
                    All <b class="tabular">{{ $counts['all'] }}</b>
                </a>
                <a href="{{ route('deliveries.index', array_merge(request()->except('status', 'page'), ['status' => 'awaiting-pickup'])) }}"
                   class="statuspill {{ $statusFilter === 'awaiting-pickup' ? 'active' : '' }}" style="--pill-color: var(--warning);">
                    Awaiting Pickup <b class="tabular">{{ $counts['awaiting-pickup'] }}</b>
                </a>
                <a href="{{ route('deliveries.index', array_merge(request()->except('status', 'page'), ['status' => 'collected'])) }}"
                   class="statuspill {{ $statusFilter === 'collected' ? 'active' : '' }}" style="--pill-color: var(--good);">
                    Collected <b class="tabular">{{ $counts['collected'] }}</b>
                </a>
                <a href="{{ route('deliveries.index', array_merge(request()->except('status', 'page'), ['status' => 'unclaimed'])) }}"
                   class="statuspill {{ $statusFilter === 'unclaimed' ? 'active' : '' }}" style="--pill-color: var(--critical);">
                    Unclaimed <b class="tabular">{{ $counts['unclaimed'] }}</b>
                </a>
            </div>

            <div class="pagehead-actions">
                <a href="{{ route('deliveries.export', request()->query()) }}" class="btn">
                    <svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                    Export
                </a>
                <button type="button" class="btn btn-primary" onclick="openAddDeliveryModal()">
                    <svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                    Log Delivery
                </button>
            </div>
        </div>

        <!-- Filter Bar -->
        <div class="filterbar">
            <form id="filterForm" method="GET" action="{{ route('deliveries.index') }}" style="display:flex; gap:10px; flex-wrap:wrap; width:100%; align-items:center;">
                <input type="hidden" name="status" value="{{ $statusFilter }}">

                <div class="searchbox">
                    <svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search visitors, ID, company..." onkeydown="if(event.key==='Enter') this.form.submit()">
                </div>

                <select name="site_id" class="select-filter" onchange="this.form.submit()">
                    <option value="all">All Branches</option>
                    @foreach($sites as $s)
                        <option value="{{ $s->id }}" {{ request('site_id') == $s->id ? 'selected' : '' }}>{{ $s->name }}</option>
                    @endforeach
                </select>
            </form>
        </div>

        <!-- Deliveries Data Table -->
        <div class="tablewrap scrollx">
            <table class="dtable">
                <thead>
                    <tr>
                        <th class="nosort">Package</th>
                        <th class="nosort">Recipient</th>
                        <th class="nosort">Carrier</th>
                        <th class="nosort">Received</th>
                        <th class="nosort">Status</th>
                        <th class="nosort" style="text-align:end;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($deliveries as $d)
                        @php
                            $isUnclaimed = $d->is_unclaimed;
                        @endphp
                        <tr>
                            <td>
                                <div style="min-width:0; display:flex; align-items:center; gap:10px;">
                                    @if($d->photo_path)
                                        <a href="{{ asset($d->photo_path) }}" target="_blank" title="View Package Photo" style="display:inline-block; flex:none;">
                                            <img src="{{ asset($d->photo_path) }}" alt="Package" style="width:34px; height:34px; object-fit:cover; border-radius:6px; border:1px solid var(--border);">
                                        </a>
                                    @endif
                                    <div>
                                        <div class="cell-name">{{ $d->description }}</div>
                                        <div class="cell-sub">
                                            @if($d->tracking_number){{ $d->tracking_number }} · @endif{{ $d->size }}
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="cell-name">{{ $d->recipient_name }}</div>
                                <div class="cell-sub">{{ $d->department ?? 'General' }}</div>
                            </td>
                            <td>{{ $d->carrier }}</td>
                            <td>
                                <div class="mono">{{ $d->received_at->format('d M, h:i A') }}</div>
                                @if($isUnclaimed)
                                    <div class="cell-sub" style="color:var(--critical); font-weight: 500;">Sitting {{ $d->age_days }}d</div>
                                @endif
                            </td>
                            <td>
                                @if($isUnclaimed)
                                    <span class="chip chip-critical"><span class="chip-dot"></span>Unclaimed</span>
                                @elseif($d->status === 'collected')
                                    <span class="chip chip-good"><span class="chip-dot"></span>Collected</span>
                                @else
                                    <span class="chip chip-warning"><span class="chip-dot"></span>Awaiting Pickup</span>
                                @endif
                            </td>
                            <td>
                                <div class="rowactions">
                                    @if($d->status === 'awaiting-pickup')
                                        <!-- Notify Recipient button -->
                                        <form method="POST" action="{{ route('deliveries.notify', $d) }}" style="display:inline;">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-ghost" title="Notify Recipient">
                                                <svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/></svg>
                                            </button>
                                        </form>

                                        <!-- Mark as Collected button -->
                                        <button type="button" class="btn btn-sm btn-primary" onclick="openCollectModal('{{ $d->id }}', '{{ addslashes($d->description) }}', '{{ addslashes($d->carrier) }}', '{{ addslashes($d->tracking_number ?? '') }}', '{{ addslashes($d->recipient_name) }}')" title="Mark as Collected">
                                            <svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>
                                        </button>
                                    @endif

                                    <!-- Edit Delivery button -->
                                    <button type="button" class="btn btn-sm btn-ghost" onclick="openEditDeliveryModal({{ json_encode($d) }})" title="Edit">
                                        <svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                                    </button>

                                    <!-- Delete Delivery button -->
                                    <button type="button" class="btn btn-sm btn-danger" onclick="openDeleteModal('{{ $d->id }}', '{{ addslashes($d->description) }}')" title="Delete">
                                        <svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/><line x1="10" y1="11" x2="10" y2="17"/><line x1="14" y1="11" x2="14" y2="17"/></svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="empty-state">
                                <svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" style="width:36px;height:36px;margin:0 auto 8px;display:block;opacity:.4;"><path d="M12 3 3 8v8l9 5 9-5V8l-9-5z"/><path d="M3 8l9 5 9-5M12 13v8"/></svg>
                                No matching delivery packages found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            <!-- Table Footer -->
            <div class="tablefoot">
                <span class="tabular">{{ $deliveries->total() }} records</span>
                <div class="hstack" style="display:flex; align-items:center; gap:16px;">
                    <div class="pagesize">
                        <span>Rows per page</span>
                        <select class="select-filter" style="padding:4px 8px;font-size:12px;" onchange="location.href='?per_page='+this.value">
                            <option value="25" {{ request('per_page', 25) == 25 ? 'selected' : '' }}>25</option>
                            <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50</option>
                            <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100</option>
                        </select>
                    </div>
                    <div class="pager">
                        <a href="{{ $deliveries->previousPageUrl() ?? 'javascript:void(0)' }}" class="{{ $deliveries->onFirstPage() ? 'disabled' : '' }}" {{ $deliveries->onFirstPage() ? 'tabindex=-1' : '' }} title="Previous page">
                            <svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:13px;height:13px;transform:rotate(180deg);"><polyline points="9 18 15 12 9 6"/></svg>
                        </a>
                        <span class="mono">{{ $deliveries->currentPage() }} / {{ max(1, $deliveries->lastPage()) }}</span>
                        <a href="{{ $deliveries->nextPageUrl() ?? 'javascript:void(0)' }}" class="{{ $deliveries->hasMorePages() ? '' : 'disabled' }}" {{ $deliveries->hasMorePages() ? '' : 'tabindex=-1' }} title="Next page">
                            <svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:13px;height:13px;"><polyline points="9 18 15 12 9 6"/></svg>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>

<!-- ==========================================
     MODAL 1: ADD / EDIT DELIVERY
========================================== -->
<div class="modal-root" id="modalDelivery">
    <div class="modal-backdrop" onclick="closeModal('modalDelivery')"></div>
    <div class="modal-panel">
        <div class="modal-head">
            <h3 id="modalDeliveryTitle">Log Delivery</h3>
            <button type="button" class="btn btn-ghost" style="padding:4px;" onclick="closeModal('modalDelivery')">
                <svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
            </button>
        </div>
        <form id="deliveryForm" method="POST" action="{{ route('deliveries.store') }}">
            @csrf
            <input type="hidden" name="_method" id="deliveryFormMethod" value="POST">

            <div class="modal-body">
                <div class="form-grid">
                    <div class="field">
                        <label for="deliveryCarrier">Carrier *</label>
                        <select name="carrier" id="deliveryCarrier" required>
                            @foreach($carriers as $c)
                                <option value="{{ $c->name }}">{{ $c->name }}</option>
                            @endforeach
                            @if($carriers->isEmpty())
                                <option value="DHL Express">DHL Express</option>
                                <option value="FedEx">FedEx</option>
                                <option value="Aramex">Aramex</option>
                                <option value="Emirates Post">Emirates Post</option>
                                <option value="UPS">UPS</option>
                                <option value="Amazon Logistics">Amazon Logistics</option>
                            @endif
                        </select>
                    </div>
                    <div class="field">
                        <label for="deliverySize">Package Size *</label>
                        <select name="size" id="deliverySize" required>
                            @foreach($sizes as $s)
                                <option value="{{ $s->name }}">{{ $s->name }}</option>
                            @endforeach
                            @if($sizes->isEmpty())
                                <option value="Small Box">Small Box</option>
                                <option value="Envelope / Document">Envelope / Document</option>
                                <option value="Medium Box">Medium Box</option>
                                <option value="Large Box">Large Box</option>
                                <option value="Pallet / Freight">Pallet / Freight</option>
                            @endif
                        </select>
                    </div>
                </div>

                <div class="field">
                    <label for="deliveryTracking">Tracking Number</label>
                    <input type="text" name="tracking_number" id="deliveryTracking" placeholder="e.g. ARAMEX-123456">
                </div>

                <div class="field">
                    <label for="deliveryDescription">Description *</label>
                    <input type="text" name="description" id="deliveryDescription" placeholder="e.g. Small box with office stationery" required>
                </div>

                <div class="field">
                    <label for="deliveryRecipient">Recipient Employee *</label>
                    <select name="recipient_employee_id" id="deliveryRecipient" required>
                        <option value="">Select recipient...</option>
                        @foreach($employees as $e)
                            <option value="{{ $e->id }}">
                                {{ $e->first_name }} {{ $e->last_name }} ({{ $e->department?->name ?? 'General' }} · {{ $e->site?->name ?? 'Branch' }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="field">
                    <label for="deliveryNotes">Notes</label>
                    <textarea name="notes" id="deliveryNotes" rows="2" placeholder="Fragile, storage locker location, etc..."></textarea>
                </div>

                <div class="switchrow">
                    <div>
                        <div class="sr-label">Notify Recipient</div>
                        <div class="sr-sub">Send email/SMS notification to recipient immediately.</div>
                    </div>
                    <input type="checkbox" name="notify_recipient" id="deliveryNotify" value="1" checked style="width: 18px; height: 18px; accent-color: var(--accent); cursor: pointer;">
                </div>

                <!-- Package Photo Capture Area -->
                <div class="card" style="text-align:center; margin-top:14px; padding:14px; border:1px solid var(--border); border-radius:var(--radius-sm); background:var(--surface);">
                    <div id="photoPlaceholder" style="width:72px; height:72px; border-radius:12px; background:var(--surface-2); border:1.5px dashed var(--border); display:flex; align-items:center; justify-content:center; margin:0 auto 8px; color:var(--accent);">
                        <svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:28px;height:28px;"><path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"/><circle cx="12" cy="13" r="4"/></svg>
                    </div>
                    <input type="file" id="packagePhotoInput" accept="image/*" style="display:none;" onchange="handlePhotoUpload(event)">
                    <button type="button" class="btn btn-sm" onclick="document.getElementById('packagePhotoInput').click()">
                        <svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"/><circle cx="12" cy="13" r="4"/></svg>
                        <span id="photoBtnLabel">Capture Package Photo</span>
                    </button>
                    <input type="hidden" name="photo" id="deliveryPhotoHidden">
                </div>
            </div>
            <div class="modal-foot">
                <button type="button" class="btn" onclick="closeModal('modalDelivery')">Cancel</button>
                <button type="submit" class="btn btn-primary">
                    <svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>
                    Save
                </button>
            </div>
        </form>
    </div>
</div>

<!-- ==========================================
     MODAL 2: MARK AS COLLECTED
========================================== -->
<div class="modal-root" id="modalCollect">
    <div class="modal-backdrop" onclick="closeModal('modalCollect')"></div>
    <div class="modal-panel">
        <div class="modal-head">
            <h3>Mark as Collected</h3>
            <button type="button" class="btn btn-ghost" style="padding:4px;" onclick="closeModal('modalCollect')">
                <svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
            </button>
        </div>
        <form id="collectForm" method="POST" action="">
            @csrf
            <div class="modal-body">
                <div class="cell-sub" id="collectSummary" style="margin-bottom:14px; font-size:13px; color:var(--text-secondary); font-weight:500;">
                    Small box · Aramex · ARA-758461
                </div>
                <div class="field">
                    <label for="collectBy">Collected By *</label>
                    <input type="text" name="collected_by" id="collectBy" required placeholder="Person collecting package">
                </div>
            </div>
            <div class="modal-foot">
                <button type="button" class="btn" onclick="closeModal('modalCollect')">Cancel</button>
                <button type="submit" class="btn btn-primary">
                    <svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>
                    Confirm Collection
                </button>
            </div>
        </form>
    </div>
</div>

<!-- ==========================================
     MODAL 3: DELETE CONFIRMATION
========================================== -->
<div class="modal-root" id="modalDelete">
    <div class="modal-backdrop" onclick="closeModal('modalDelete')"></div>
    <div class="modal-panel" style="max-width:440px;">
        <div class="modal-head">
            <h3>Remove Delivery Log</h3>
            <button type="button" class="btn btn-ghost" style="padding:4px;" onclick="closeModal('modalDelete')">
                <svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
            </button>
        </div>
        <form id="deleteForm" method="POST" action="">
            @csrf
            @method('DELETE')
            <div class="modal-body">
                <p style="margin:0 0 10px; font-size:13.5px; color:var(--text-primary);">
                    Are you sure you want to remove <b id="deleteItemDesc">this delivery record</b>?
                </p>
                <p style="margin:0; font-size:12px; color:var(--text-muted);">
                    This action cannot be undone.
                </p>
            </div>
            <div class="modal-foot">
                <button type="button" class="btn" onclick="closeModal('modalDelete')">Cancel</button>
                <button type="submit" class="btn btn-danger" style="background:var(--critical); color:#fff; border-color:var(--critical);">
                    Delete Record
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function openModal(id) {
        document.getElementById(id).classList.add('open');
    }

    function closeModal(id) {
        document.getElementById(id).classList.remove('open');
    }

    function openAddDeliveryModal() {
        document.getElementById('modalDeliveryTitle').textContent = 'Log Delivery';
        const form = document.getElementById('deliveryForm');
        form.action = "{{ route('deliveries.store') }}";
        document.getElementById('deliveryFormMethod').value = 'POST';
        form.reset();
        document.getElementById('photoBtnLabel').textContent = 'Capture Package Photo';
        document.getElementById('photoPlaceholder').innerHTML = '<svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:28px;height:28px;"><path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"/><circle cx="12" cy="13" r="4"/></svg>';
        document.getElementById('deliveryPhotoHidden').value = '';
        openModal('modalDelivery');
    }

    function openEditDeliveryModal(d) {
        document.getElementById('modalDeliveryTitle').textContent = 'Edit Delivery';
        const form = document.getElementById('deliveryForm');
        form.action = '/deliveries/' + d.id;
        document.getElementById('deliveryFormMethod').value = 'PUT';
        
        document.getElementById('deliveryCarrier').value = d.carrier || '';
        document.getElementById('deliverySize').value = d.size || '';
        document.getElementById('deliveryTracking').value = d.tracking_number || '';
        document.getElementById('deliveryDescription').value = d.description || '';
        document.getElementById('deliveryRecipient').value = d.recipient_employee_id || '';
        document.getElementById('deliveryNotes').value = d.notes || '';
        document.getElementById('deliveryNotify').checked = d.notify_recipient ? true : false;
        
        if (d.photo_path) {
            const src = d.photo_path.startsWith('http') || d.photo_path.startsWith('data:') ? d.photo_path : ('/' + d.photo_path.replace(/^\//, ''));
            document.getElementById('photoPlaceholder').innerHTML = '<img src="' + src + '" style="width:100%;height:100%;object-fit:cover;border-radius:10px;">';
            document.getElementById('photoBtnLabel').textContent = 'Retake Photo';
        } else {
            document.getElementById('photoBtnLabel').textContent = 'Capture Package Photo';
            document.getElementById('photoPlaceholder').innerHTML = '<svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:28px;height:28px;"><path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"/><circle cx="12" cy="13" r="4"/></svg>';
        }
        document.getElementById('deliveryPhotoHidden').value = '';
        
        openModal('modalDelivery');
    }

    function openCollectModal(id, desc, carrier, tracking, recipient) {
        const form = document.getElementById('collectForm');
        form.action = '/deliveries/' + id + '/collect';
        document.getElementById('collectSummary').textContent = desc + ' · ' + carrier + (tracking ? ' · ' + tracking : '');
        document.getElementById('collectBy').value = recipient || '';
        openModal('modalCollect');
    }

    function openDeleteModal(id, desc) {
        const form = document.getElementById('deleteForm');
        form.action = '/deliveries/' + id;
        document.getElementById('deleteItemDesc').textContent = desc ? '"' + desc + '"' : 'this delivery record';
        openModal('modalDelete');
    }

    function handlePhotoUpload(event) {
        const file = event.target.files[0];
        if (!file) return;

        const reader = new FileReader();
        reader.onload = function(e) {
            const dataUrl = e.target.result;
            document.getElementById('deliveryPhotoHidden').value = dataUrl;
            document.getElementById('photoPlaceholder').innerHTML = '<img src="' + dataUrl + '" style="width:100%;height:100%;object-fit:cover;border-radius:10px;">';
            document.getElementById('photoBtnLabel').textContent = 'Retake Photo';
        };
        reader.readAsDataURL(file);
    }
</script>
    <script>
        function toggleSidebar() {
            document.body.classList.toggle('sidebar-open');
        }
    </script>
</body>
</html>
