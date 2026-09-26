<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Employees — Zapio VMS</title>
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
        .brand-mark { width: 32px; height: 32px; border-radius: 99px; background: linear-gradient(155deg, #0f7a72, var(--accent)); display: flex; align-items: center; justify-content: center; color: #fff; font-weight: 700; font-size: 15px; }
        .brand-name { font-weight: 700; font-size: 16.5px; }
        .brand-sub { font-size: 11px; color: var(--text-muted); }

        nav.navlist { padding: 10px; display: flex; flex-direction: column; gap: 2px; flex: 1; }
        .navlabel { font-size: 10.5px; text-transform: uppercase; letter-spacing: .06em; color: var(--text-muted); padding: 14px 10px 6px; }
        .navitem { display: flex; align-items: center; gap: 11px; padding: 9px 10px; border-radius: var(--radius-sm); color: var(--text-secondary); cursor: pointer; font-size: 13.5px; font-weight: 500; border: none; background: none; width: 100%; text-align: start; text-decoration: none; }
        .navitem:hover { background: var(--surface-2); color: var(--text-primary); }
        .navitem.active { background: var(--accent-soft); color: var(--accent); }
        .navitem .ic { width: 18px; height: 18px; flex: none; }

        .pagehead { display: flex; align-items: flex-start; justify-content: space-between; gap: 16px; margin-bottom: 20px; flex-wrap: wrap; }
        .pagehead h1 { font-size: 21px; margin: 0; }
        .pagehead .sub { color: var(--text-secondary); font-size: 13px; margin-top: 4px; }
        .pagehead-actions { display: flex; gap: 8px; flex-wrap: wrap; }

        .btn { display: inline-flex; align-items: center; gap: 7px; padding: 8px 14px; border-radius: var(--radius-sm); border: 1px solid var(--border); background: var(--surface); color: var(--text-primary); font-size: 13px; font-weight: 600; cursor: pointer; white-space: nowrap; text-decoration: none; }
        .btn .ic { width: 15px; height: 15px; }
        .btn:hover { background: var(--surface-2); }
        .btn-primary { background: var(--accent); border-color: var(--accent); color: #fff; }
        .btn-primary:hover { background: var(--accent-hover); }
        .btn-danger { background: transparent; border-color: var(--critical); color: var(--critical); }
        .btn-danger:hover { background: rgba(208, 59, 59, 0.10); }
        .btn-leave-active { background: color-mix(in srgb, var(--s7, #4a3aa7) 14%, transparent); border-color: var(--s7, #4a3aa7); color: var(--s7, #4a3aa7); }
        .btn-leave-active:hover { background: color-mix(in srgb, var(--s7, #4a3aa7) 24%, transparent); }
        .btn-ghost { border-color: transparent; background: transparent; }
        .btn-ghost:hover { background: var(--surface-2); }
        .btn-sm { padding: 5px 10px; font-size: 12px; }

        /* Sub-tabs */
        .subtabs { display: flex; gap: 22px; border-block-end: 1px solid var(--border); margin-bottom: 18px; }
        .subtab { background: none; border: none; font: inherit; font-size: 13.5px; font-weight: 600; color: var(--text-muted); padding: 9px 1px 12px; cursor: pointer; border-block-end: 2px solid transparent; margin-block-end: -1px; text-decoration: none; }
        .subtab:hover { color: var(--text-primary); }
        .subtab.active { color: var(--accent); border-block-end-color: var(--accent); }

        /* Filter bar & Pills */
        .filterbar { display: flex; gap: 10px; flex-wrap: wrap; align-items: center; margin-bottom: 14px; }
        .searchbox { flex: 1; min-width: 240px; display: flex; align-items: center; gap: 8px; background: var(--surface); border: 1px solid var(--border); border-radius: 99px; padding: 7px 14px; color: var(--text-muted); }
        .searchbox input { border: none; background: none; outline: none; color: var(--text-primary); font-size: 13px; width: 100%; font-family: inherit; }
        .select-filter { font-family: inherit; font-size: 13px; padding: 7px 14px; border-radius: 99px; border: 1px solid var(--border); background: var(--surface); color: var(--text-primary); font-weight: 500; cursor: pointer; }
        
        .pillrow { display: flex; gap: 8px; flex-wrap: wrap; margin-bottom: 16px; }
        .statuspill { display: inline-flex; align-items: center; gap: 6px; padding: 7px 14px; border-radius: 99px; border: 1.5px solid var(--border); background: var(--surface); font-size: 12.5px; font-weight: 600; cursor: pointer; color: var(--pill-color, var(--text-secondary)); font-family: inherit; text-decoration: none; }
        .statuspill:hover { background: var(--surface-2); }
        .statuspill.active { border-color: var(--pill-color, var(--accent)); background: color-mix(in srgb, var(--pill-color, var(--accent)) 12%, var(--surface)); color: var(--pill-color, var(--accent)); }
        .statuspill.pill-all.active { background: var(--text-primary); color: #fff; border-color: var(--text-primary); }

        /* Bulk Selection Bar */
        .bulkbar { display: flex; align-items: center; justify-content: space-between; gap: 12px; background: var(--accent-soft); border: 1px solid color-mix(in srgb, var(--accent) 40%, transparent); border-radius: var(--radius-sm); padding: 9px 14px; margin-bottom: 12px; font-size: 13px; font-weight: 600; color: var(--accent); }

        /* Tables */
        .tablewrap { border: 1px solid var(--border); border-radius: var(--radius); overflow: hidden; background: var(--surface); }
        .tablewrap.scrollx { overflow-x: auto; }
        table.dtable { width: 100%; border-collapse: collapse; min-width: 720px; }
        .dtable thead th { text-align: start; font-size: 11px; text-transform: uppercase; letter-spacing: .05em; color: var(--text-muted); font-weight: 600; padding: 11px 14px; border-block-end: 1px solid var(--border); background: var(--surface-2); white-space: nowrap; }
        .dtable thead th.nosort { cursor: default; }
        .dtable tbody td { padding: 11px 14px; border-block-end: 1px solid var(--border); font-size: 13px; vertical-align: middle; }
        .dtable tbody tr:last-child td { border-block-end: none; }
        .dtable tbody tr.clickable { cursor: pointer; transition: background .12s ease; }
        .dtable tbody tr.clickable:hover { background: var(--surface-2); }
        .cell-name { font-weight: 600; }
        .cell-sub { font-size: 11.5px; color: var(--text-muted); margin-top: 1px; }

        /* Row Actions */
        .rowactions { display: inline-flex; align-items: center; gap: 5px; justify-content: flex-end; }
        .icon-btn { display: inline-flex; align-items: center; justify-content: center; width: 30px; height: 30px; border-radius: var(--radius-sm); border: 1px solid var(--border); background: var(--surface); color: var(--text-secondary); cursor: pointer; transition: all .15s ease; padding: 0; line-height: 1; }
        .icon-btn:hover { background: var(--surface-2); color: var(--text-primary); border-color: var(--accent); }
        .icon-btn.icon-btn-danger:hover { background: rgba(208, 59, 59, 0.1); border-color: var(--critical); color: var(--critical); }
        .icon-btn .ic { width: 15px; height: 15px; }

        /* Table Footer */
        .tablefoot { display: flex; align-items: center; justify-content: space-between; padding: 10px 14px; border-block-start: 1px solid var(--border); background: var(--surface-2); font-size: 12px; color: var(--text-secondary); }
        .pager { display: flex; gap: 4px; align-items: center; }
        .pager button, .pager a { width: 26px; height: 26px; border-radius: 6px; border: 1px solid var(--border); background: var(--surface); cursor: pointer; color: var(--text-secondary); font-size: 12px; display: inline-flex; align-items: center; justify-content: center; text-decoration: none; }
        .pager button:hover:not(:disabled), .pager a:hover:not(.disabled) { background: var(--surface-3); }
        .pager a.disabled, .pager button:disabled { opacity: .4; pointer-events: none; cursor: default; }
        .pagesize { display: flex; align-items: center; gap: 6px; white-space: nowrap; }

        /* Chips & Badges */
        .chip { display: inline-flex; align-items: center; gap: 5px; padding: 3px 9px; border-radius: 99px; font-size: 11.5px; font-weight: 600; white-space: nowrap; }
        .chip-good { background: color-mix(in srgb, var(--good) 14%, transparent); color: var(--good); }
        .chip-warning { background: color-mix(in srgb, var(--warning) 18%, transparent); color: #8a5a00; }
        .chip-serious { background: color-mix(in srgb, var(--serious) 16%, transparent); color: #a34a26; }
        .chip-critical { background: color-mix(in srgb, var(--critical) 14%, transparent); color: var(--critical); }
        .chip-muted { background: var(--surface-3); color: var(--text-secondary); }
        .chip-leave { background: color-mix(in srgb, var(--s7) 16%, transparent); color: var(--s7); }
        .countpill { display: inline-flex; align-items: center; padding: 3px 10px; border-radius: 99px; font-size: 12px; font-weight: 600; background: color-mix(in srgb, var(--good) 14%, transparent); color: var(--good); }

        /* Cards & Grids */
        .card { background: var(--surface); border: 1px solid var(--border); border-radius: var(--radius); padding: 18px; margin-bottom: 16px; }
        .grid { display: grid; gap: 14px; }
        .grid-2 { grid-template-columns: minmax(0,1.3fr) minmax(0,1fr); }
        .grid-3 { grid-template-columns: repeat(3, minmax(0, 1fr)); }
        .grid-4 { display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 14px; margin-bottom: 16px; }
        .stat-tile { background: var(--surface); border: 1px solid var(--border); border-radius: var(--radius); padding: 16px 18px; display: flex; flex-direction: column; gap: 8px; }
        .stat-label { font-size: 12px; color: var(--text-secondary); font-weight: 500; }
        .stat-value-row { display: flex; align-items: baseline; gap: 8px; }
        .stat-value { font-size: 26px; font-weight: 700; letter-spacing: -.01em; }
        .avatar-sq { width: 38px; height: 38px; border-radius: 10px; background: var(--accent-soft); color: var(--accent); display: flex; align-items: center; justify-content: center; font-size: 12px; font-weight: 700; flex: none; }
        .icon-tint-s1 { background: color-mix(in srgb, #2a78d6 16%, transparent); color: #2a78d6; }
        .icon-tint-s3 { background: color-mix(in srgb, #1baf7a 16%, transparent); color: #1baf7a; }
        .icon-tint-s4 { background: color-mix(in srgb, #eda100 16%, transparent); color: #eda100; }
        .icon-tint-s7 { background: color-mix(in srgb, #4a3aa7 16%, transparent); color: #4a3aa7; }

        .section-title { font-size: 14.5px; font-weight: 700; margin-bottom: 2px; }
        .section-sub { font-size: 12px; color: var(--text-muted); margin-bottom: 14px; }
        .empty-state { padding: 48px 20px; text-align: center; color: var(--text-muted); font-size: 13px; }

        /* Modals */
        .modal-root { display: none; position: fixed; inset: 0; z-index: 100; }
        .modal-root.open { display: block; }
        .modal-backdrop { position: absolute; inset: 0; background: rgba(6,12,11,.5); backdrop-filter: blur(2px); }
        .modal-panel { position: relative; max-width: 580px; width: calc(100% - 32px); margin: 6vh auto 0; background: var(--surface); border-radius: var(--radius); border: 1px solid var(--border); box-shadow: var(--shadow); overflow: hidden; }
        .modal-panel.wide { max-width: 760px; }
        .modal-panel.xwide { max-width: 940px; }
        .modal-head { display: flex; align-items: center; justify-content: space-between; padding: 16px 20px; border-bottom: 1px solid var(--border); }
        .modal-head h3 { margin: 0; font-size: 16px; font-weight: 700; }
        .modal-body { padding: 20px; max-height: 75vh; overflow-y: auto; }
        .modal-foot { display: flex; align-items: center; justify-content: flex-end; gap: 8px; padding: 14px 20px; border-top: 1px solid var(--border); background: var(--surface-2); }
        
        .form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; }
        .field { display: flex; flex-direction: column; gap: 5px; }
        .field label { font-size: 12.5px; font-weight: 600; color: var(--text-secondary); }
        .field input, .field select, .field textarea { font-family: inherit; font-size: 13.5px; padding: 9px 11px; border-radius: var(--radius-sm); border: 1px solid var(--border); background: var(--surface); color: var(--text-primary); width: 100%; box-sizing: border-box; }
        .switchrow { display: flex; align-items: center; justify-content: space-between; gap: 12px; padding: 11px 0; border-block-end: 1px solid var(--border); }
        .switchrow:last-child { border-block-end: none; }
        .switchrow .sr-label { font-size: 13px; font-weight: 600; }
        .switchrow .sr-sub { font-size: 11.5px; color: var(--text-muted); margin-top: 1px; }
        .switch { position: relative; width: 36px; height: 20px; flex: none; }
        .switch input { opacity: 0; width: 0; height: 0; position: absolute; }
        .switch .track { position: absolute; inset: 0; background: var(--surface-3); border-radius: 99px; border: 1px solid var(--border); transition: background .15s; cursor: pointer; }
        .switch .thumb { position: absolute; top: 2px; left: 2px; width: 14px; height: 14px; border-radius: 99px; background: #fff; box-shadow: 0 1px 2px rgba(0,0,0,.3); transition: transform .15s; }
        .switch input:checked + .track { background: var(--accent); border-color: var(--accent); }
        .switch input:checked + .track .thumb { transform: translateX(16px); }

        /* Calendar Leave View */
        .dp-panel { width: 100%; background: var(--surface); border: 1px solid var(--border); border-radius: var(--radius); padding: 14px; box-sizing: border-box; }
        .dp-head { display: flex; align-items: center; justify-content: space-between; margin-bottom: 12px; }
        .dp-month-label { font-weight: 700; font-size: 14px; }
        .dp-grid { display: grid; grid-template-columns: repeat(7, 1fr); gap: 4px; text-align: center; }
        .dp-dow { font-size: 11px; font-weight: 700; color: var(--text-muted); padding: 4px 0; text-transform: uppercase; }
        .dp-day { width: 100%; aspect-ratio: 1; display: flex; align-items: center; justify-content: center; border-radius: 8px; font-size: 12.5px; cursor: pointer; border: none; background: transparent; color: var(--text-primary); transition: background .12s; }
        .dp-day:hover { background: var(--surface-2); }
        .dp-day.outside { color: var(--text-muted); opacity: 0.4; }
        .dp-day.today { box-shadow: inset 0 0 0 1.5px var(--accent); font-weight: 700; }
        .dp-day.on-leave { background: color-mix(in srgb, var(--s7) 16%, transparent); color: var(--s7); font-weight: 700; }
        .dp-day.staged { background: var(--accent); color: #fff; font-weight: 700; }
        .dp-legend { display: flex; gap: 14px; flex-wrap: wrap; font-size: 11.5px; color: var(--text-muted); margin-top: 12px; }
        .dp-legend-item { display: flex; align-items: center; gap: 6px; }
        .dp-legend-swatch { width: 11px; height: 11px; border-radius: 3px; }

        .upload-box { border: 1.5px dashed var(--border); border-radius: var(--radius); padding: 24px; display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 10px; text-align: center; }
        .hstack { display: flex; align-items: center; gap: 10px; }
    </style>
</head>
<body>
    <div id="app">
        @include('partials.sidebar', ['active' => 'employees'])

        @include('partials.topbar')

        <main id="main">
            <!-- 1. Page Header with Title, Date Badge & Actions -->
            <div class="pagehead">
                <div>
                    <div style="display:flex;align-items:center;gap:10px;">
                        <h1>Employees</h1>
                        <span class="chip chip-muted" style="font-weight:600;font-size:11.5px;padding:3px 9px;">
                            {{ now()->format('D, d M Y') }}
                        </span>
                    </div>
                    <div class="sub">Maintain your staff directory — used as visit hosts during check-in and pre-registration.</div>
                </div>
                <div class="pagehead-actions">
                    <button type="button" class="btn" onclick="openCsvImportModal('employees')">
                        <svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
                        <span>Import CSV</span>
                    </button>
                    <button type="button" class="btn" onclick="exportEmployeesCsv()">
                        <svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                        <span>Export CSV</span>
                    </button>
                    <button type="button" class="btn btn-primary" onclick="openEmployeeModal()">
                        <svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 5v14M5 12h14"/></svg>
                        <span>+ Add Employee</span>
                    </button>
                </div>
            </div>

            <!-- Flash Alerts -->
            @if (session('success'))
                <div class="card" style="background: rgba(12, 163, 12, 0.12); border-color: rgba(12, 163, 12, 0.3); color: var(--good); padding: 12px 16px; margin-bottom: 16px; font-weight: 500;">
                    ✓ {{ session('success') }}
                </div>
            @endif

            <!-- 2. Subtabs Navigation -->
            <div class="subtabs">
                <button type="button" class="subtab {{ $tab === 'directory' ? 'active' : '' }}" onclick="switchTab('directory')">Employee Directory</button>
                <button type="button" class="subtab {{ $tab === 'attendance' ? 'active' : '' }}" onclick="switchTab('attendance')">Attendance</button>
                <button type="button" class="subtab {{ $tab === 'report' ? 'active' : '' }}" onclick="switchTab('report')">Attendance Report</button>
                <button type="button" class="subtab {{ $tab === 'shifts' ? 'active' : '' }}" onclick="switchTab('shifts')">Shifts</button>
            </div>

            <!-- TAB 1: EMPLOYEE DIRECTORY -->
            <div id="tab-directory" style="display: {{ $tab === 'directory' ? 'block' : 'none' }};">
                <div class="hstack" style="margin-bottom:14px;gap:10px;">
                    <div style="font-size:14.5px;font-weight:700;">All Employees</div>
                    <span class="chip chip-good">● {{ $allEmployees->count() }} employees</span>
                </div>

                <form method="GET" action="{{ route('employees.index') }}" id="directoryFilterForm">
                    <input type="hidden" name="tab" value="directory">
                    <input type="hidden" name="status" id="filterStatus" value="{{ request('status', 'all') }}">

                    <!-- Row 1: Filters -->
                    <div class="filterbar" style="align-items:center;">
                        <div class="searchbox grow" style="max-width:320px;">
                            <svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                            <input type="text" name="search" placeholder="Search employees..." value="{{ request('search') }}" onchange="this.form.submit()">
                        </div>

                        <select name="site_id" onchange="this.form.submit()" class="select-filter">
                            <option value="">All Branches</option>
                            @foreach ($sites as $s)
                                <option value="{{ $s->id }}" {{ request('site_id') == $s->id ? 'selected' : '' }}>{{ $s->name }}</option>
                            @endforeach
                        </select>

                        <select name="department_id" onchange="this.form.submit()" class="select-filter">
                            <option value="">All Departments</option>
                            @foreach ($departments as $d)
                                <option value="{{ $d->id }}" {{ request('department_id') == $d->id ? 'selected' : '' }}>{{ $d->name }}</option>
                            @endforeach
                        </select>

                        <select name="shift_id" onchange="this.form.submit()" class="select-filter">
                            <option value="">All Shifts</option>
                            @foreach ($shifts as $sh)
                                <option value="{{ $sh->id }}" {{ request('shift_id') == $sh->id ? 'selected' : '' }}>{{ $sh->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Row 2: Status Pills with Live Counts -->
                    <div class="pillrow">
                        @php $st = request('status', 'all'); @endphp
                        <button type="button" class="statuspill pill-all {{ $st === 'all' ? 'active' : '' }}" onclick="setStatusPill('all')">
                            All <b class="tabular">{{ $counts['all'] }}</b>
                        </button>
                        <button type="button" class="statuspill {{ $st === 'active' ? 'active' : '' }}" style="--pill-color:var(--good);" onclick="setStatusPill('active')">
                            On Site <b class="tabular">{{ $counts['onsite'] }}</b>
                        </button>
                        <button type="button" class="statuspill {{ $st === 'late' ? 'active' : '' }}" style="--pill-color:var(--warning);" onclick="setStatusPill('late')">
                            Late Check-in <b class="tabular">{{ $counts['late'] }}</b>
                        </button>
                        <button type="button" class="statuspill {{ $st === 'early' ? 'active' : '' }}" style="--pill-color:var(--serious);" onclick="setStatusPill('early')">
                            Early Checkout <b class="tabular">{{ $counts['early'] }}</b>
                        </button>
                        <button type="button" class="statuspill {{ $st === 'complete' ? 'active' : '' }}" style="--pill-color:var(--text-muted);" onclick="setStatusPill('complete')">
                            Complete <b class="tabular">{{ $counts['complete'] }}</b>
                        </button>
                        <button type="button" class="statuspill {{ $st === 'absent' ? 'active' : '' }}" style="--pill-color:var(--text-muted);" onclick="setStatusPill('absent')">
                            Absent <b class="tabular">{{ $counts['absent'] }}</b>
                        </button>
                        <button type="button" class="statuspill {{ $st === 'on_leave' ? 'active' : '' }}" style="--pill-color:var(--s7);" onclick="setStatusPill('on_leave')">
                            On Leave <b class="tabular">{{ $counts['leave'] }}</b>
                        </button>
                    </div>
                </form>

                <!-- Bulk Selection Bar -->
                <div id="bulkActionBar" class="bulkbar" style="display:none;">
                    <span id="selectedCountText">0 selected</span>
                    <div class="pagehead-actions">
                        <button type="button" class="btn btn-sm" onclick="exportEmployeesCsv()">
                            <svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                            <span>Export</span>
                        </button>
                        <button type="button" class="btn btn-sm btn-danger" onclick="bulkDeleteEmployees()">
                            <svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 6h18M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg>
                            <span>Delete Selected</span>
                        </button>
                    </div>
                </div>

                <!-- Employees Table -->
                <div class="tablewrap scrollx">
                    <table class="dtable">
                        <thead>
                            <tr>
                                <th class="nosort" style="width:36px;"><input type="checkbox" id="selectAllCheckbox" onchange="toggleSelectAll(this)"></th>
                                <th class="nosort">Name</th>
                                <th class="nosort">Employee ID</th>
                                <th class="nosort">Branch</th>
                                <th class="nosort">Department</th>
                                <th class="nosort">Shift</th>
                                <th class="nosort">Reporting To</th>
                                <th class="nosort" style="text-align:end;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($employees as $emp)
                                @php
                                    $managerName = $emp->manager ? $emp->manager->full_name : '—';
                                    $isOnLeave = $emp->status === 'on_leave';
                                @endphp
                                <tr class="clickable" onclick="handleEmployeeRowClick(event, {{ json_encode($emp) }})">
                                    <td onclick="event.stopPropagation();">
                                        <input type="checkbox" class="emp-select-check" value="{{ $emp->id }}" onchange="updateSelectedCount()">
                                    </td>
                                    <td>
                                        <div class="cell-name">{{ $emp->full_name }}</div>
                                        <div class="cell-sub">{{ $emp->email ?? '—' }}</div>
                                    </td>
                                    <td class="mono">{{ $emp->employee_code }}</td>
                                    <td>{{ $emp->site->name ?? '—' }}</td>
                                    <td>{{ $emp->department->name ?? '—' }}</td>
                                    <td>{{ $emp->shift->name ?? '—' }}</td>
                                    <td>{{ $managerName }}</td>
                                    <td style="text-align:end;" onclick="event.stopPropagation();">
                                        <div class="rowactions">
                                            <button type="button" class="icon-btn" title="View QR Badge" onclick="openEmployeeQrModal({{ json_encode($emp) }})">
                                                <svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
                                            </button>
                                            <button type="button" class="icon-btn" title="Edit" onclick="openEmployeeModal({{ json_encode($emp) }})">
                                                <svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 20h9M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"/></svg>
                                            </button>
                                            <button type="button" class="icon-btn {{ $isOnLeave ? 'btn-leave-active' : '' }}" title="{{ $isOnLeave ? 'Manage leave' : 'Mark on leave' }}" onclick="openEmployeeLeaveModal({{ json_encode($emp) }})">
                                                <svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                                            </button>
                                            <form action="{{ route('employees.destroy', $emp->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Delete this employee?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="icon-btn icon-btn-danger" title="Delete">
                                                    <svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="empty-state">No matching employees found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Table Footer -->
                <div class="tablefoot">
                    <span class="tabular">{{ $employees->total() }} records</span>
                    <div class="hstack" style="gap:16px;">
                        <div class="pagesize">
                            <span>Rows per page</span>
                            <select class="select-filter" style="padding:4px 8px;font-size:12px;" onchange="location.href='?per_page='+this.value">
                                <option value="25" selected>25</option>
                                <option value="50">50</option>
                                <option value="100">100</option>
                            </select>
                        </div>
                        <div class="pager">
                            <a href="{{ $employees->previousPageUrl() ?? 'javascript:void(0)' }}" class="{{ $employees->onFirstPage() ? 'disabled' : '' }}" {{ $employees->onFirstPage() ? 'tabindex=-1' : '' }} title="Previous page">
                                <svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:13px;height:13px;transform:rotate(180deg);"><polyline points="9 18 15 12 9 6"/></svg>
                            </a>
                            <span class="mono">{{ $employees->currentPage() }} / {{ max(1, $employees->lastPage()) }}</span>
                            <a href="{{ $employees->nextPageUrl() ?? 'javascript:void(0)' }}" class="{{ $employees->hasMorePages() ? '' : 'disabled' }}" {{ $employees->hasMorePages() ? '' : 'tabindex=-1' }} title="Next page">
                                <svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:13px;height:13px;"><polyline points="9 18 15 12 9 6"/></svg>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- TAB 2: ATTENDANCE -->
            <div id="tab-attendance" style="display: {{ $tab === 'attendance' ? 'block' : 'none' }};">
                <div class="card">
                    <div class="section-title">Badge Check-In</div>
                    <div class="section-sub">Scan an employee’s QR badge (or type their code) to check them in or out.</div>
                    <div class="hstack" style="gap:8px;flex-wrap:wrap;">
                        <div class="searchbox grow" style="max-width:360px;">
                            <svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
                            <input type="text" id="kioskScanInput" placeholder="Scan or enter badge code..." autocomplete="off">
                        </div>
                        <button type="button" class="btn btn-primary" onclick="alert('Badge scan simulated.')">
                            <svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>
                            <span>Submit</span>
                        </button>
                    </div>
                </div>

                <!-- 4 KPI Stat Tiles -->
                <div class="grid-4">
                    <div class="stat-tile">
                        <div class="stat-label">Present Today</div>
                        <div class="stat-value-row"><span class="stat-value tabular">{{ $counts['onsite'] }}</span></div>
                    </div>
                    <div class="stat-tile">
                        <div class="stat-label">Absent</div>
                        <div class="stat-value-row"><span class="stat-value tabular">{{ $counts['absent'] }}</span></div>
                    </div>
                    <div class="stat-tile">
                        <div class="stat-label">On Leave</div>
                        <div class="stat-value-row"><span class="stat-value tabular" style="color:var(--s7);">{{ $counts['leave'] }}</span></div>
                    </div>
                    <div class="stat-tile">
                        <div class="stat-label">Avg Check-in</div>
                        <div class="stat-value-row"><span class="stat-value tabular">08:35 AM</span></div>
                    </div>
                </div>

                <!-- Today's Attendance Card -->
                <div class="card">
                    <div class="hstack" style="margin-bottom:14px;flex-wrap:wrap;gap:10px;">
                        <div style="flex:1;min-width:200px;">
                            <div class="section-title" style="margin-bottom:2px;">Today’s Attendance</div>
                            <span class="chip chip-muted">{{ now()->format('d M Y') }}</span>
                        </div>
                        <div class="pagehead-actions">
                            <button type="button" class="btn btn-sm" onclick="alert('Date selection.')">
                                <svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                                <span>Change Date</span>
                            </button>
                            <button type="button" class="btn btn-sm" onclick="exportEmployeesCsv()">
                                <svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                                <span>Export CSV</span>
                            </button>
                        </div>
                    </div>

                    <div class="tablewrap scrollx">
                        <table class="dtable">
                            <thead>
                                <tr>
                                    <th class="nosort">Name</th>
                                    <th class="nosort">Employee ID</th>
                                    <th class="nosort">Department</th>
                                    <th class="nosort">Check-in</th>
                                    <th class="nosort">Check-out</th>
                                    <th class="nosort">Hours</th>
                                    <th class="nosort">Status</th>
                                    <th class="nosort" style="text-align:end;">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($employees->take(8) as $emp)
                                    <tr>
                                        <td><div class="cell-name">{{ $emp->full_name }}</div></td>
                                        <td class="mono">{{ $emp->employee_code }}</td>
                                        <td>{{ $emp->department->name ?? '—' }}</td>
                                        <td class="mono">08:15 AM</td>
                                        <td class="mono">—</td>
                                        <td class="mono">3h 45m</td>
                                        <td>
                                            @if ($emp->status === 'on_leave')
                                                <span class="chip chip-leave">On Leave</span>
                                            @else
                                                <span class="chip chip-good">On Site</span>
                                            @endif
                                        </td>
                                        <td style="text-align:end;">
                                            <button type="button" class="btn btn-sm">Check out</button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- TAB 3: ATTENDANCE REPORT -->
            <div id="tab-report" style="display: {{ $tab === 'report' ? 'block' : 'none' }};">
                <div class="filterbar" style="align-items:center;">
                    <select class="select-filter" style="min-width:200px;">
                        <option value="all">All Employees</option>
                        @foreach ($allEmployees as $e)
                            <option value="{{ $e->id }}">{{ $e->full_name }}</option>
                        @endforeach
                    </select>

                    <select class="select-filter" style="min-width:160px;">
                        <option value="week" selected>This Week</option>
                        <option value="month">This Month</option>
                        <option value="year">This Year</option>
                        <option value="today">Today</option>
                    </select>

                    <select class="select-filter" style="min-width:160px;">
                        <option value="">All Branches</option>
                        @foreach ($sites as $s)
                            <option value="{{ $s->id }}">{{ $s->name }}</option>
                        @endforeach
                    </select>

                    <select class="select-filter" style="min-width:160px;">
                        <option value="">All Departments</option>
                        @foreach ($departments as $d)
                            <option value="{{ $d->id }}">{{ $d->name }}</option>
                        @endforeach
                    </select>

                    <div style="margin-inline-start:auto;">
                        <button type="button" class="btn btn-sm" onclick="exportEmployeesCsv()">
                            <svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                            <span>Export</span>
                        </button>
                    </div>
                </div>

                <div class="card">
                    <div class="tablewrap scrollx">
                        <table class="dtable">
                            <thead>
                                <tr>
                                    <th class="nosort">Name</th>
                                    <th class="nosort">Employee ID</th>
                                    <th class="nosort">Branch</th>
                                    <th class="nosort">Department</th>
                                    <th class="nosort">Days Present</th>
                                    <th class="nosort">Days Absent</th>
                                    <th class="nosort">Days On Leave</th>
                                    <th class="nosort">Total Hours</th>
                                    <th class="nosort">Avg Check-in</th>
                                    <th class="nosort" style="text-align:end;">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($employees->take(10) as $emp)
                                    <tr>
                                        <td><div class="cell-name">{{ $emp->full_name }}</div></td>
                                        <td class="mono">{{ $emp->employee_code }}</td>
                                        <td>{{ $emp->site->name ?? '—' }}</td>
                                        <td>{{ $emp->department->name ?? '—' }}</td>
                                        <td class="mono">5</td>
                                        <td class="mono">0</td>
                                        <td class="mono">{{ $emp->status === 'on_leave' ? 1 : 0 }}</td>
                                        <td class="mono">42h 15m</td>
                                        <td class="mono">08:24 AM</td>
                                        <td style="text-align:end;">
                                            <button type="button" class="btn btn-sm" onclick="openEmployeeDetailModal({{ json_encode($emp) }})">
                                                <svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                                                <span>View</span>
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- TAB 4: SHIFTS -->
            <div id="tab-shifts" style="display: {{ $tab === 'shifts' ? 'block' : 'none' }};">
                <div class="hstack" style="margin-bottom:14px;justify-content:space-between;">
                    <div style="font-size:14.5px;font-weight:700;">Shifts</div>
                    <button type="button" class="btn btn-primary btn-sm" onclick="openShiftModal()">
                        <svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 5v14M5 12h14"/></svg>
                        <span>Add Shift</span>
                    </button>
                </div>

                <div class="grid grid-3">
                    @forelse ($shifts as $sh)
                        @php $empCount = $allEmployees->where('shift_id', $sh->id)->count(); @endphp
                        <div class="card">
                            <div class="hstack" style="margin-bottom:12px;">
                                <div class="avatar-sq">
                                    <svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                                </div>
                                <div style="flex:1;min-width:0;">
                                    <div style="font-weight:600;font-size:14.5px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">{{ $sh->name }}</div>
                                    <div class="cell-sub">{{ $empCount }} employees</div>
                                </div>
                                <button type="button" class="icon-btn btn-ghost" onclick="openShiftModal({{ json_encode($sh) }})">
                                    <svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 20h9M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"/></svg>
                                </button>
                                <button type="button" class="icon-btn btn-ghost icon-btn-danger" onclick="alert('Delete shift')">
                                    <svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg>
                                </button>
                            </div>
                            <div class="form-grid" style="row-gap:6px;font-size:12px;margin-bottom:12px;">
                                <div>
                                    <div class="cell-sub">Hours</div>
                                    <div class="mono" style="font-weight:600;">{{ $sh->start_time ?? '08:00' }} – {{ $sh->end_time ?? '17:00' }}</div>
                                </div>
                            </div>
                            <button type="button" class="btn btn-sm" style="width:100%;justify-content:center;" onclick="openShiftAssignModal({{ json_encode($sh) }})">
                                <svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                                <span>Manage Employees</span>
                            </button>
                        </div>
                    @empty
                        <div class="card" style="grid-column: 1 / -1;">
                            <div class="empty-state">No shifts configured yet.</div>
                        </div>
                    @endforelse
                </div>
            </div>
        </main>
    </div>

    <!-- MODAL 1: ADD / EDIT EMPLOYEE -->
    <div id="modalEmployee" class="modal-root">
        <div class="modal-backdrop" onclick="closeModal('modalEmployee')"></div>
        <div class="modal-panel wide">
            <div class="modal-head">
                <h3 id="employeeModalTitle">Add Employee</h3>
                <button type="button" class="icon-btn btn-ghost" onclick="closeModal('modalEmployee')">✕</button>
            </div>
            <form id="employeeForm" method="POST" action="{{ route('employees.store') }}">
                @csrf
                <input type="hidden" name="_method" id="employeeMethod" value="POST">
                <div class="modal-body">
                    <div class="form-grid">
                        <div class="field">
                            <label>First Name *</label>
                            <input type="text" name="first_name" id="empFirstName" required placeholder="First name">
                        </div>
                        <div class="field">
                            <label>Last Name *</label>
                            <input type="text" name="last_name" id="empLastName" required placeholder="Last name">
                        </div>
                        <div class="field">
                            <label>Email *</label>
                            <input type="email" name="email" id="empEmail" required placeholder="employee@zapio.ae">
                        </div>
                        <div class="field">
                            <label>Phone</label>
                            <input type="tel" name="phone" id="empPhone" placeholder="+971 50 000 0000">
                        </div>
                        <div class="field">
                            <label>Employee ID</label>
                            <input type="text" name="employee_code" id="empCode" placeholder="EMP-001">
                        </div>
                        <div class="field">
                            <label>Branch *</label>
                            <select name="site_id" id="empSiteId" required>
                                @foreach ($sites as $s)
                                    <option value="{{ $s->id }}">{{ $s->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="field">
                            <label>Department *</label>
                            <select name="department_id" id="empDeptId" required>
                                @foreach ($departments as $d)
                                    <option value="{{ $d->id }}">{{ $d->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="field">
                            <label>Job Title *</label>
                            <input type="text" name="job_title" id="empJobTitle" required placeholder="e.g. Software Engineer">
                        </div>
                        <div class="field">
                            <label>Shift</label>
                            <select name="shift_id" id="empShiftId">
                                <option value="">Unassigned</option>
                                @foreach ($shifts as $sh)
                                    <option value="{{ $sh->id }}">{{ $sh->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="field">
                            <label>Reporting To</label>
                            <select name="manager_id" id="empManagerId">
                                <option value="">No manager</option>
                                @foreach ($managers as $m)
                                    <option value="{{ $m->id }}">{{ $m->full_name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div style="font-size:12px;font-weight:700;text-transform:uppercase;letter-spacing:.04em;color:var(--text-muted);margin:18px 0 6px;">Notifications & Access</div>
                    <div class="switchrow">
                        <div>
                            <div class="sr-label">Receive email notifications</div>
                            <div class="sr-sub">Notified by email when a visitor or delivery arrives for them.</div>
                        </div>
                        <label class="switch">
                            <input type="checkbox" name="receive_emails" value="1" checked>
                            <span class="track"><span class="thumb"></span></span>
                        </label>
                    </div>
                    <div class="switchrow">
                        <div>
                            <div class="sr-label">Login to admin panel</div>
                            <div class="sr-sub">Creates a linked sign-in with Host-level access to Zapio VMS.</div>
                        </div>
                        <label class="switch">
                            <input type="checkbox" name="admin_access" value="1">
                            <span class="track"><span class="thumb"></span></span>
                        </label>
                    </div>
                </div>
                <div class="modal-foot">
                    <button type="button" class="btn" onclick="closeModal('modalEmployee')">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="employeeSubmitBtn">
                        <svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>
                        <span>Save Employee</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL 2: QR BADGE MODAL -->
    <div id="modalEmployeeQr" class="modal-root">
        <div class="modal-backdrop" onclick="closeModal('modalEmployeeQr')"></div>
        <div class="modal-panel" style="max-width:380px;">
            <div class="modal-head">
                <h3>Staff Check-In Badge</h3>
                <button type="button" class="icon-btn btn-ghost" onclick="closeModal('modalEmployeeQr')">✕</button>
            </div>
            <div class="modal-body" style="text-align:center;">
                <div id="employeeQrCanvas" style="display:inline-block;padding:12px;background:#fff;border-radius:12px;box-shadow:var(--shadow);"></div>
                <div class="mono" id="qrEmpCode" style="font-size:20px;font-weight:700;letter-spacing:.08em;margin:14px 0 4px;">EMP-001</div>
                <div class="cell-name" id="qrEmpName">Employee Name</div>
                <div class="cell-sub" id="qrEmpDept">Department</div>
                <p style="font-size:12px;color:var(--text-muted);margin-top:14px;">Scan this code at the reception kiosk to check in or out.</p>
            </div>
            <div class="modal-foot" style="justify-content:flex-end;">
                <button type="button" class="btn" onclick="closeModal('modalEmployeeQr')">Close</button>
                <button type="button" class="btn btn-primary" onclick="window.print()">
                    <svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 6 2 18 2 18 9"/><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><rect x="6" y="14" width="12" height="8"/></svg>
                    <span>Print Badge</span>
                </button>
            </div>
        </div>
    </div>

    <!-- MODAL 3: MARK LEAVE CALENDAR MODAL -->
    <div id="modalEmployeeLeave" class="modal-root">
        <div class="modal-backdrop" onclick="closeModal('modalEmployeeLeave')"></div>
        <div class="modal-panel">
            <div class="modal-head">
                <h3 id="leaveModalTitle">Mark Leave — Employee</h3>
                <button type="button" class="icon-btn btn-ghost" onclick="closeModal('modalEmployeeLeave')">✕</button>
            </div>
            <div class="modal-body">
                <p class="section-sub" style="margin-top:0;">Click any days to select them, then mark or remove leave for all selected days at once.</p>
                <div class="dp-panel">
                    <div class="dp-head">
                        <button type="button" class="icon-btn btn-sm btn-ghost" onclick="alert('Previous month')">‹</button>
                        <span class="dp-month-label">{{ now()->format('F Y') }}</span>
                        <button type="button" class="icon-btn btn-sm btn-ghost" onclick="alert('Next month')">›</button>
                    </div>
                    <div class="dp-grid">
                        <div class="dp-dow">Su</div><div class="dp-dow">Mo</div><div class="dp-dow">Tu</div><div class="dp-dow">We</div><div class="dp-dow">Th</div><div class="dp-dow">Fr</div><div class="dp-dow">Sa</div>
                        <!-- Month sample days -->
                        @for ($i = 1; $i <= 30; $i++)
                            <button type="button" class="dp-day {{ $i === (int)now()->format('d') ? 'today' : '' }} {{ $i === 15 ? 'on-leave' : '' }}" onclick="this.classList.toggle('staged')">
                                {{ $i }}
                            </button>
                        @endfor
                    </div>
                </div>
                <div class="dp-legend">
                    <div class="dp-legend-item"><span class="dp-legend-swatch" style="background:var(--s7);"></span>Already on leave</div>
                    <div class="dp-legend-item"><span class="dp-legend-swatch" style="background:var(--accent);"></span>Selected</div>
                </div>
            </div>
            <div class="modal-foot">
                <button type="button" class="btn" onclick="closeModal('modalEmployeeLeave')">Close</button>
                <button type="button" class="btn" onclick="alert('Leave removed.')">Remove Leave</button>
                <form id="leaveToggleForm" method="POST" action="" style="display:inline;">
                    @csrf
                    <button type="submit" class="btn btn-primary">Mark as Leave</button>
                </form>
            </div>
        </div>
    </div>

    <!-- MODAL 4: EMPLOYEE DETAIL & ID CARD MODAL -->
    <div id="modalEmployeeDetail" class="modal-root">
        <div class="modal-backdrop" onclick="closeModal('modalEmployeeDetail')"></div>
        <div class="modal-panel xwide">
            <div class="modal-head">
                <h3>Employee Detail</h3>
                <button type="button" class="icon-btn btn-ghost" onclick="closeModal('modalEmployeeDetail')">✕</button>
            </div>
            <div class="modal-body">
                <div class="grid grid-2" style="gap:16px;align-items:start;">
                    <div>
                        <div class="card">
                            <div class="hstack" style="margin-bottom:14px;">
                                <div>
                                    <div style="font-size:16px;font-weight:700;" id="detEmpName">Employee Name</div>
                                    <div class="cell-sub" id="detEmpTitle">Job Title</div>
                                </div>
                                <div style="margin-inline-start:auto;">
                                    <span class="chip chip-good" id="detEmpStatusChip">● Active</span>
                                </div>
                            </div>
                            <div class="form-grid" style="row-gap:10px;font-size:13px;">
                                <div><div class="cell-sub">Employee ID</div><div class="mono" style="font-weight:600;" id="detEmpCode">—</div></div>
                                <div><div class="cell-sub">Branch</div><div style="font-weight:600;" id="detEmpSite">—</div></div>
                                <div><div class="cell-sub">Department</div><div style="font-weight:600;" id="detEmpDept">—</div></div>
                                <div><div class="cell-sub">Shift</div><div style="font-weight:600;" id="detEmpShift">—</div></div>
                                <div><div class="cell-sub">Email</div><div style="font-weight:600;" id="detEmpEmail">—</div></div>
                                <div><div class="cell-sub">Phone</div><div style="font-weight:600;" id="detEmpPhone">—</div></div>
                            </div>
                        </div>

                        <div class="card">
                            <div class="section-title">Attendance History</div>
                            <div class="cell-sub" style="margin-bottom:12px;">Past attendance logs and check-in times.</div>
                            <div class="tablewrap scrollx" style="max-height:200px;">
                                <table class="dtable">
                                    <thead><tr><th>Date</th><th>Check-in</th><th>Check-out</th><th>Status</th></tr></thead>
                                    <tbody>
                                        <tr><td class="mono">{{ now()->format('d M Y') }}</td><td class="mono">08:20 AM</td><td class="mono">—</td><td><span class="chip chip-good">Present</span></td></tr>
                                        <tr><td class="mono">{{ now()->subDay()->format('d M Y') }}</td><td class="mono">08:15 AM</td><td class="mono">05:30 PM</td><td><span class="chip chip-muted">Complete</span></td></tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div>
                        <div class="card" style="text-align:center;">
                            <div style="font-size:11px;font-weight:700;letter-spacing:.08em;color:var(--text-muted);text-transform:uppercase;margin-bottom:12px;">Employee ID Card</div>
                            <div id="detQrCanvas" style="display:inline-block;padding:12px;background:#fff;border-radius:12px;box-shadow:var(--shadow);"></div>
                            <div class="cell-name" style="margin-top:12px;" id="detIdName">Employee Name</div>
                            <div class="cell-sub" id="detIdCode">EMP-001</div>
                            <div class="hstack" style="gap:8px;margin-top:16px;">
                                <button type="button" class="btn btn-sm" style="flex:1;" onclick="window.print()">Print</button>
                                <button type="button" class="btn btn-sm" style="flex:1;" onclick="alert('Download card')">Download</button>
                            </div>
                        </div>
                        <div class="card">
                            <div class="section-title" style="margin-bottom:10px;">Quick Actions</div>
                            <div style="display:flex;flex-direction:column;gap:8px;">
                                <button type="button" class="btn btn-primary" onclick="alert('Checked in')">Manual Check-in</button>
                                <button type="button" class="btn" onclick="alert('Checked out')">Manual Check-out</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-foot">
                <button type="button" class="btn" onclick="closeModal('modalEmployeeDetail')">Close</button>
            </div>
        </div>
    </div>

    <!-- MODAL 5: CSV IMPORT MODAL -->
    <div id="modalCsvImport" class="modal-root">
        <div class="modal-backdrop" onclick="closeModal('modalCsvImport')"></div>
        <div class="modal-panel wide">
            <div class="modal-head">
                <h3>Import Employees</h3>
                <button type="button" class="icon-btn btn-ghost" onclick="closeModal('modalCsvImport')">✕</button>
            </div>
            <div class="modal-body">
                <p class="section-sub">Upload a CSV to add or update multiple employees at once. Matching by email updates an existing employee instead of creating a duplicate.</p>
                <div class="upload-box">
                    <svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:28px;height:28px;color:var(--text-muted);"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
                    <button type="button" class="btn btn-sm" onclick="document.getElementById('csvFileInput').click()">Choose CSV File</button>
                    <input type="file" id="csvFileInput" accept=".csv" style="display:none;" onchange="handleCsvFileSelected(this)">
                </div>
                <div class="cell-sub" style="margin-top:10px;">Accepts .csv files exported from Excel, Google Sheets, or another VMS.</div>
                <button type="button" class="btn btn-sm" style="margin-top:12px;" onclick="downloadSampleCsv()">
                    <svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                    <span>Download Template</span>
                </button>
            </div>
            <div class="modal-foot">
                <button type="button" class="btn" onclick="closeModal('modalCsvImport')">Cancel</button>
                <button type="button" class="btn btn-primary" disabled id="csvImportSubmitBtn">Import</button>
            </div>
        </div>
    </div>

    <!-- MODAL 6: SHIFT MODAL -->
    <div id="modalShift" class="modal-root">
        <div class="modal-backdrop" onclick="closeModal('modalShift')"></div>
        <div class="modal-panel">
            <div class="modal-head">
                <h3 id="shiftModalTitle">Add Shift</h3>
                <button type="button" class="icon-btn btn-ghost" onclick="closeModal('modalShift')">✕</button>
            </div>
            <div class="modal-body">
                <div class="field" style="margin-bottom:12px;">
                    <label>Shift Name *</label>
                    <input type="text" id="shiftName" placeholder="e.g. Rotating Shift">
                </div>
                <div class="form-grid">
                    <div class="field">
                        <label>Start Time</label>
                        <input type="time" id="shiftStartTime" value="08:00">
                    </div>
                    <div class="field">
                        <label>End Time</label>
                        <input type="time" id="shiftEndTime" value="17:00">
                    </div>
                </div>
            </div>
            <div class="modal-foot">
                <button type="button" class="btn" onclick="closeModal('modalShift')">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="alert('Shift saved.')">Save</button>
            </div>
        </div>
    </div>

    <!-- MODAL 7: ASSIGN EMPLOYEES TO SHIFT -->
    <div id="modalShiftAssign" class="modal-root">
        <div class="modal-backdrop" onclick="closeModal('modalShiftAssign')"></div>
        <div class="modal-panel wide">
            <div class="modal-head">
                <h3 id="shiftAssignTitle">Assign Employees — Shift</h3>
                <button type="button" class="icon-btn btn-ghost" onclick="closeModal('modalShiftAssign')">✕</button>
            </div>
            <div class="modal-body">
                <div class="filterbar" style="align-items:center;">
                    <div class="searchbox grow">
                        <svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                        <input type="text" placeholder="Search employees..." oninput="filterAssignList(this.value)">
                    </div>
                    <select class="select-filter">
                        <option value="">All Branches</option>
                        @foreach ($sites as $s)
                            <option value="{{ $s->id }}">{{ $s->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="hstack" style="margin-bottom:10px;justify-content:space-between;">
                    <span class="cell-sub" id="assignSelectedCount">0 selected</span>
                    <div class="hstack" style="gap:8px;">
                        <button type="button" class="btn btn-sm" onclick="selectAllAssign(true)">Select all</button>
                        <button type="button" class="btn btn-sm" onclick="selectAllAssign(false)">Clear</button>
                    </div>
                </div>
                <div class="tablewrap scrollx" style="max-height:300px;">
                    <table class="dtable" id="assignEmpTable">
                        <tbody>
                            @foreach ($allEmployees as $e)
                                <tr>
                                    <td style="width:36px;"><input type="checkbox" class="assign-check" value="{{ $e->id }}"></td>
                                    <td><div class="cell-name">{{ $e->full_name }}</div><div class="cell-sub">{{ $e->department->name ?? '—' }}</div></td>
                                    <td class="cell-sub">{{ $e->site->name ?? '—' }}</td>
                                    <td class="cell-sub">{{ $e->shift->name ?? 'Unassigned' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-foot">
                <button type="button" class="btn" onclick="closeModal('modalShiftAssign')">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="alert('Assignments updated.'); closeModal('modalShiftAssign');">Save</button>
            </div>
        </div>
    </div>

    <!-- JavaScript Handlers -->
    <script>
        function switchTab(tabKey) {
            ['directory', 'attendance', 'report', 'shifts'].forEach(k => {
                const el = document.getElementById('tab-' + k);
                if (el) el.style.display = (k === tabKey ? 'block' : 'none');
            });
            document.querySelectorAll('.subtabs .subtab').forEach(btn => {
                btn.classList.toggle('active', btn.textContent.toLowerCase().includes(tabKey) || (tabKey === 'directory' && btn.textContent.includes('Directory')));
            });
            const url = new URL(window.location);
            url.searchParams.set('tab', tabKey);
            window.history.replaceState({}, '', url);
        }

        function setStatusPill(status) {
            document.getElementById('filterStatus').value = status;
            document.getElementById('directoryFilterForm').submit();
        }

        function openModal(id) {
            const el = document.getElementById(id);
            if (el) el.classList.add('open');
        }

        function closeModal(id) {
            const el = document.getElementById(id);
            if (el) el.classList.remove('open');
        }

        function openEmployeeModal(emp = null) {
            const form = document.getElementById('employeeForm');
            const title = document.getElementById('employeeModalTitle');
            const method = document.getElementById('employeeMethod');

            if (emp) {
                title.innerText = 'Edit Employee';
                form.action = '/employees/' + emp.id;
                method.value = 'PUT';
                document.getElementById('empFirstName').value = emp.first_name || '';
                document.getElementById('empLastName').value = emp.last_name || '';
                document.getElementById('empEmail').value = emp.email || '';
                document.getElementById('empPhone').value = emp.phone || '';
                document.getElementById('empCode').value = emp.employee_code || '';
                document.getElementById('empSiteId').value = emp.site_id || '';
                document.getElementById('empDeptId').value = emp.department_id || '';
                document.getElementById('empJobTitle').value = emp.job_title || '';
                document.getElementById('empShiftId').value = emp.shift_id || '';
                document.getElementById('empManagerId').value = emp.manager_id || '';
            } else {
                title.innerText = 'Add Employee';
                form.action = '{{ route("employees.store") }}';
                method.value = 'POST';
                form.reset();
            }
            openModal('modalEmployee');
        }

        function openEmployeeQrModal(emp) {
            document.getElementById('qrEmpCode').innerText = emp.employee_code || 'EMP-001';
            document.getElementById('qrEmpName').innerText = (emp.first_name + ' ' + (emp.last_name || '')).trim();
            document.getElementById('qrEmpDept').innerText = (emp.department && emp.department.name) ? emp.department.name : 'Staff';

            const qrContainer = document.getElementById('employeeQrCanvas');
            qrContainer.innerHTML = '';
            if (typeof QRCode !== 'undefined') {
                new QRCode(qrContainer, {
                    text: emp.employee_code || 'EMP-001',
                    width: 156,
                    height: 156,
                });
            }
            openModal('modalEmployeeQr');
        }

        function openEmployeeLeaveModal(emp) {
            const title = document.getElementById('leaveModalTitle');
            title.innerText = 'Mark Leave — ' + (emp.first_name + ' ' + (emp.last_name || '')).trim();
            document.getElementById('leaveToggleForm').action = '/employees/' + emp.id + '/leave';
            openModal('modalEmployeeLeave');
        }

        function openEmployeeDetailModal(emp) {
            const name = (emp.first_name + ' ' + (emp.last_name || '')).trim();
            document.getElementById('detEmpName').innerText = name;
            document.getElementById('detIdName').innerText = name;
            document.getElementById('detEmpTitle').innerText = emp.job_title || 'Employee';
            document.getElementById('detEmpCode').innerText = emp.employee_code || '—';
            document.getElementById('detIdCode').innerText = emp.employee_code || '—';
            document.getElementById('detEmpSite').innerText = (emp.site && emp.site.name) ? emp.site.name : '—';
            document.getElementById('detEmpDept').innerText = (emp.department && emp.department.name) ? emp.department.name : '—';
            document.getElementById('detEmpShift').innerText = (emp.shift && emp.shift.name) ? emp.shift.name : '—';
            document.getElementById('detEmpEmail').innerText = emp.email || '—';
            document.getElementById('detEmpPhone').innerText = emp.phone || '—';

            const qrContainer = document.getElementById('detQrCanvas');
            qrContainer.innerHTML = '';
            if (typeof QRCode !== 'undefined') {
                new QRCode(qrContainer, {
                    text: emp.employee_code || 'EMP-001',
                    width: 140,
                    height: 140,
                });
            }
            openModal('modalEmployeeDetail');
        }

        function handleEmployeeRowClick(event, emp) {
            if (event.target.closest('button, a, form, input, select')) return;
            openEmployeeDetailModal(emp);
        }

        function openCsvImportModal(kind) {
            openModal('modalCsvImport');
        }

        function handleCsvFileSelected(input) {
            if (input.files && input.files.length) {
                document.getElementById('csvImportSubmitBtn').disabled = false;
            }
        }

        function downloadSampleCsv() {
            const csv = "Employee ID,First Name,Last Name,Email,Phone,Branch,Department,Job Title,Shift\nEMP-101,Jamie,Rivera,jamie.rivera@zapio.ae,+971 50 1234567,Business Bay Tower,Executive Office,Software Engineer,Sat-Wed (8AM-5PM)";
            const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
            const url = URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = 'employee-import-template.csv';
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
        }

        function openShiftModal(sh = null) {
            const title = document.getElementById('shiftModalTitle');
            if (sh) {
                title.innerText = 'Edit Shift';
                document.getElementById('shiftName').value = sh.name || '';
                document.getElementById('shiftStartTime').value = sh.start_time || '08:00';
                document.getElementById('shiftEndTime').value = sh.end_time || '17:00';
            } else {
                title.innerText = 'Add Shift';
                document.getElementById('shiftName').value = '';
                document.getElementById('shiftStartTime').value = '08:00';
                document.getElementById('shiftEndTime').value = '17:00';
            }
            openModal('modalShift');
        }

        function openShiftAssignModal(sh) {
            document.getElementById('shiftAssignTitle').innerText = 'Assign Employees — ' + (sh.name || 'Shift');
            openModal('modalShiftAssign');
        }

        function toggleSelectAll(master) {
            const checks = document.querySelectorAll('.emp-select-check');
            checks.forEach(c => c.checked = master.checked);
            updateSelectedCount();
        }

        function updateSelectedCount() {
            const checked = document.querySelectorAll('.emp-select-check:checked');
            const bar = document.getElementById('bulkActionBar');
            const countText = document.getElementById('selectedCountText');
            if (checked.length > 0) {
                bar.style.display = 'flex';
                countText.innerText = checked.length + ' selected';
            } else {
                bar.style.display = 'none';
                document.getElementById('selectAllCheckbox').checked = false;
            }
        }

        function bulkDeleteEmployees() {
            const checked = document.querySelectorAll('.emp-select-check:checked');
            if (!checked.length) return;
            if (confirm('Delete ' + checked.length + ' selected employees?')) {
                alert('Bulk delete simulated.');
            }
        }

        function exportEmployeesCsv() {
            window.location.href = "{{ route('employees.index', array_merge(request()->query(), ['export' => 'csv'])) }}";
        }
    </script>
</body>
</html>
