<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Employees — Zapio VMS</title>
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

        /* Search suggestions */
        .searchbox { position: relative; }
        .suggest-list { position: absolute; top: calc(100% + 6px); left: 0; right: 0; z-index: 50; background: var(--surface); border: 1px solid var(--border); border-radius: var(--radius-sm); box-shadow: var(--shadow); max-height: 280px; overflow-y: auto; display: none; padding: 4px; }
        .suggest-list.open { display: block; }
        .suggest-item { display: flex; align-items: center; justify-content: space-between; gap: 10px; padding: 8px 10px; border-radius: 6px; cursor: pointer; font-size: 13px; color: var(--text-primary); }
        .suggest-item:hover, .suggest-item.active { background: var(--accent-soft); }
        .suggest-item .s-name { font-weight: 600; }
        .suggest-item .s-sub { font-size: 11.5px; color: var(--text-muted); }
        .suggest-item .s-code { font-size: 11.5px; font-weight: 600; color: var(--accent); white-space: nowrap; }
        .suggest-item mark { background: color-mix(in srgb, var(--warning) 35%, transparent); color: inherit; border-radius: 2px; }
        .suggest-empty { padding: 10px; font-size: 12.5px; color: var(--text-muted); }
        
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
                            <input type="text" name="search" id="empSearchInput" placeholder="Search by name or employee ID..." value="{{ request('search') }}" autocomplete="off" role="combobox" aria-autocomplete="list" aria-controls="empSuggestList" aria-expanded="false">
                            <div class="suggest-list" id="empSuggestList" role="listbox"></div>
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

                <script>
                    (function () {
                        const employees = @json($allEmployees->map(fn ($e) => ['name' => $e->full_name, 'code' => $e->employee_code, 'email' => $e->email])->values());
                        const input = document.getElementById('empSearchInput');
                        const list = document.getElementById('empSuggestList');
                        const form = document.getElementById('directoryFilterForm');
                        let activeIndex = -1;
                        let current = [];

                        const escapeHtml = s => String(s ?? '').replace(/[&<>"']/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));
                        const highlight = (text, q) => {
                            const safe = escapeHtml(text);
                            if (!q) return safe;
                            const re = new RegExp('(' + q.replace(/[.*+?^${}()|[\]\\]/g, '\\$&') + ')', 'ig');
                            return safe.replace(re, '<mark>$1</mark>');
                        };

                        function close() {
                            list.classList.remove('open');
                            input.setAttribute('aria-expanded', 'false');
                            activeIndex = -1;
                        }

                        function render(q) {
                            const term = q.trim().toLowerCase();
                            if (!term) { close(); return; }

                            current = employees.filter(e =>
                                (e.name || '').toLowerCase().includes(term) ||
                                (e.code || '').toLowerCase().includes(term)
                            ).slice(0, 8);

                            list.innerHTML = current.length
                                ? current.map((e, i) => `
                                    <div class="suggest-item" role="option" data-index="${i}">
                                        <div>
                                            <div class="s-name">${highlight(e.name, q.trim())}</div>
                                            <div class="s-sub">${escapeHtml(e.email || '')}</div>
                                        </div>
                                        <span class="s-code">${highlight(e.code, q.trim())}</span>
                                    </div>`).join('')
                                : '<div class="suggest-empty">No employees match “' + escapeHtml(q.trim()) + '”</div>';

                            activeIndex = -1;
                            list.classList.add('open');
                            input.setAttribute('aria-expanded', 'true');
                        }

                        function choose(i) {
                            const e = current[i];
                            if (!e) return;
                            input.value = e.code || e.name;
                            close();
                            form.submit();
                        }

                        function setActive(i) {
                            const items = list.querySelectorAll('.suggest-item');
                            if (!items.length) return;
                            activeIndex = (i + items.length) % items.length;
                            items.forEach((el, idx) => el.classList.toggle('active', idx === activeIndex));
                            items[activeIndex].scrollIntoView({ block: 'nearest' });
                        }

                        input.addEventListener('input', () => render(input.value));
                        input.addEventListener('focus', () => { if (input.value.trim()) render(input.value); });
                        input.addEventListener('keydown', ev => {
                            const open = list.classList.contains('open');
                            if (ev.key === 'ArrowDown' && open) { ev.preventDefault(); setActive(activeIndex + 1); }
                            else if (ev.key === 'ArrowUp' && open) { ev.preventDefault(); setActive(activeIndex - 1); }
                            else if (ev.key === 'Enter') {
                                if (open && activeIndex >= 0) { ev.preventDefault(); choose(activeIndex); }
                                // otherwise the form submits with the typed text
                            }
                            else if (ev.key === 'Escape') { close(); }
                        });
                        list.addEventListener('mousedown', ev => {
                            const item = ev.target.closest('.suggest-item');
                            if (item) { ev.preventDefault(); choose(+item.dataset.index); }
                        });
                        document.addEventListener('click', ev => {
                            if (!ev.target.closest('#empSuggestList') && ev.target !== input) close();
                        });
                    })();
                </script>

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
                            <select class="select-filter" style="padding:4px 8px;font-size:12px;" onchange="const u = new URL(window.location.href); u.searchParams.set('per_page', this.value); u.searchParams.delete('page'); location.href = u.toString();">
                                @foreach ([25, 50, 100] as $size)
                                    <option value="{{ $size }}" {{ $employees->perPage() == $size ? 'selected' : '' }}>{{ $size }}</option>
                                @endforeach
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
                                @foreach ($employees as $emp)
                                    @php
                                        $att = $emp->attendanceRecords->first();
                                        $isOnLeave = $emp->status === 'on_leave';
                                        
                                        $checkInStr = '—';
                                        $checkOutStr = '—';
                                        $hoursStr = '—';
                                        $statusStr = '<span class="chip chip-muted">Absent</span>';
                                        
                                        if ($isOnLeave) {
                                            $statusStr = '<span class="chip chip-leave" style="background:#e8def8;color:#532397;">On Leave</span>';
                                        } elseif ($att) {
                                            $checkInStr = \Carbon\Carbon::parse($att->check_in_at)->format('h:i A');
                                            
                                            if ($att->check_out_at) {
                                                $checkOutStr = \Carbon\Carbon::parse($att->check_out_at)->format('h:i A');
                                                $diffMins = \Carbon\Carbon::parse($att->check_in_at)->diffInMinutes(\Carbon\Carbon::parse($att->check_out_at));
                                                $hrs = floor($diffMins / 60);
                                                $mins = $diffMins % 60;
                                                $hoursStr = "{$hrs}h {$mins}m";
                                                $statusStr = ($emp->today_status ?? null) === 'early'
                                                    ? '<span class="chip chip-serious">Early Checkout</span>'
                                                    : '<span class="chip" style="background:#f0f0f0;color:#666;">Complete</span>';
                                            } else {
                                                if ($att->status === 'late') {
                                                    $statusStr = '<span class="chip chip-warning" style="background:#fff4e5;color:#b26a00;">Late Check-in</span>';
                                                } else {
                                                    $statusStr = '<span class="chip chip-good" style="background:#e6f4ea;color:#137333;">On Site</span>';
                                                }
                                            }
                                        }
                                    @endphp
                                    <tr>
                                        <td><div class="cell-name">{{ $emp->full_name }}</div></td>
                                        <td class="mono">{{ $emp->employee_code }}</td>
                                        <td>{{ $emp->department->name ?? '—' }}</td>
                                        <td class="mono">{{ $checkInStr }}</td>
                                        <td class="mono">{{ $checkOutStr }}</td>
                                        <td class="mono">{{ $hoursStr }}</td>
                                        <td>{!! $statusStr !!}</td>
                                        <td style="text-align:end;">
                                            @if (!$isOnLeave)
                                                @if (!$att)
                                                    <button type="button" class="btn btn-primary btn-sm" onclick="doCheckIn({{ $emp->id }})" style="background:#1f4e5b; color:white;">
                                                        <svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:14px;height:14px;margin-right:4px;"><path d="M16 21v-2a4 4 0 0 0-4-4H5c-1.1 0-2 .9-2 2v2"/><circle cx="8.5" cy="7" r="4"/><polyline points="17 11 19 13 23 9"/></svg>
                                                        <span>In</span>
                                                    </button>
                                                @elseif (!$att->check_out_at)
                                                    <button type="button" class="btn btn-sm" onclick="doCheckOut({{ $emp->id }})" style="border:1px solid #dcdcdc;">
                                                        <svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:14px;height:14px;margin-right:4px;"><path d="M16 21v-2a4 4 0 0 0-4-4H5c-1.1 0-2 .9-2 2v2"/><circle cx="8.5" cy="7" r="4"/><line x1="23" y1="11" x2="17" y2="11"/></svg>
                                                        <span>Out</span>
                                                    </button>
                                                @endif
                                            @endif
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
                <form method="GET" action="{{ route('employees.index') }}" style="display: flex; gap: 10px; align-items: center; margin-bottom: 20px;">
                    <input type="hidden" name="tab" value="report">
                    <select name="report_employee_id" class="select-filter" style="min-width:200px;" onchange="this.form.submit()">
                        <option value="all" {{ ($reportEmployeeId ?? 'all') === 'all' ? 'selected' : '' }}>All Employees</option>
                        @foreach ($allEmployees as $e)
                            <option value="{{ $e->id }}" {{ ($reportEmployeeId ?? '') == $e->id ? 'selected' : '' }}>{{ $e->full_name }}</option>
                        @endforeach
                    </select>

                    <select name="report_date_range" class="select-filter" style="min-width:160px;" onchange="this.form.submit()">
                        <option value="all_time" {{ ($reportDateRange ?? '') === 'all_time' ? 'selected' : '' }}>All Time</option>
                        <option value="today" {{ ($reportDateRange ?? '') === 'today' ? 'selected' : '' }}>Today</option>
                        <option value="this_week" {{ ($reportDateRange ?? '') === 'this_week' ? 'selected' : '' }}>This Week</option>
                        <option value="this_month" {{ ($reportDateRange ?? '') === 'this_month' ? 'selected' : '' }}>This Month</option>
                        <option value="this_year" {{ ($reportDateRange ?? '') === 'this_year' ? 'selected' : '' }}>This Year</option>
                        <option value="custom" {{ ($reportDateRange ?? '') === 'custom' ? 'selected' : '' }}>Custom Range</option>
                    </select>

                    <div style="margin-inline-start:auto;">
                        <button type="button" class="btn btn-sm" onclick="exportEmployeesCsv()">
                            <svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                            <span>Export CSV</span>
                        </button>
                    </div>
                </form>

                @if(isset($selectedReportEmployee))
                    <div style="margin-bottom: 20px;">
                        <span style="font-weight: 600; color: #333; margin-right: 8px;">All Employees</span>
                        <span style="font-weight: 700; font-size: 15px;">{{ $selectedReportEmployee->full_name }}</span>
                        <span style="color: #888; font-size: 13px; margin-left: 8px;">{{ $selectedReportEmployee->employee_code }} - {{ $selectedReportEmployee->department->name ?? 'N/A' }}</span>
                    </div>
                @endif

                <!-- KPI Cards -->
                <div class="grid-4" style="margin-bottom: 24px;">
                    <div class="stat-tile" style="background: #fff;">
                        <div class="stat-label" style="font-size:13px; color:#555;">Days Present</div>
                        <div class="stat-value-row"><span class="stat-value tabular" style="font-size:28px;">{{ $reportKpis['present'] ?? 0 }}</span></div>
                    </div>
                    <div class="stat-tile" style="background: #fff;">
                        <div class="stat-label" style="font-size:13px; color:#555;">Days Absent</div>
                        <div class="stat-value-row"><span class="stat-value tabular" style="font-size:28px;">{{ $reportKpis['absent'] ?? 0 }}</span></div>
                    </div>
                    <div class="stat-tile" style="background: #fff;">
                        <div class="stat-label" style="font-size:13px; color:#555;">Days On Leave</div>
                        <div class="stat-value-row"><span class="stat-value tabular" style="font-size:28px;">{{ $reportKpis['leave'] ?? 0 }}</span></div>
                    </div>
                    <div class="stat-tile" style="background: #fff;">
                        <div class="stat-label" style="font-size:13px; color:#555;">Total Hours</div>
                        <div class="stat-value-row"><span class="stat-value tabular" style="font-size:28px;">{{ $reportKpis['hours'] ?? '0h 0m' }}</span></div>
                    </div>
                </div>

                <div class="card">
                    <div class="tablewrap scrollx">
                        <table class="dtable">
                            <thead>
                                <tr>
                                    @if(!isset($selectedReportEmployee))
                                        <th class="nosort">Name</th>
                                    @endif
                                    <th class="nosort">DATE</th>
                                    <th class="nosort">CHECK-IN</th>
                                    <th class="nosort">CHECK-OUT</th>
                                    <th class="nosort">HOURS</th>
                                    <th class="nosort">STATUS</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if(isset($reportRecords) && $reportRecords->count() > 0)
                                    @foreach ($reportRecords as $rec)
                                        @php
                                            $checkInStr = $rec->check_in_at ? \Carbon\Carbon::parse($rec->check_in_at)->format('h:i A') : '—';
                                            $checkOutStr = $rec->check_out_at ? \Carbon\Carbon::parse($rec->check_out_at)->format('h:i A') : '—';
                                            $hoursStr = '—';
                                            $statusStr = '<span class="chip chip-muted">Absent</span>';
                                            
                                            if ($rec->check_in_at && $rec->check_out_at) {
                                                $diffMins = \Carbon\Carbon::parse($rec->check_in_at)->diffInMinutes(\Carbon\Carbon::parse($rec->check_out_at));
                                                $hrs = floor($diffMins / 60);
                                                $mins = $diffMins % 60;
                                                $hoursStr = "{$hrs}h {$mins}m";
                                            }

                                            if ($rec->status === 'late') {
                                                $statusStr = '<span class="chip chip-warning" style="background:#fff4e5;color:#b26a00;">Late Check-in</span>';
                                            } elseif ($rec->status === 'early') {
                                                $statusStr = '<span class="chip chip-warning" style="background:#fff4e5;color:#b26a00;">Early Checkout</span>';
                                            } elseif ($rec->check_out_at) {
                                                $statusStr = '<span class="chip chip-good" style="background:#e6f4ea;color:#137333;">Complete</span>';
                                            } elseif ($rec->check_in_at) {
                                                $statusStr = '<span class="chip chip-good" style="background:#e6f4ea;color:#137333;">On Site</span>';
                                            }
                                        @endphp
                                        <tr>
                                            @if(!isset($selectedReportEmployee))
                                                <td><div class="cell-name">{{ $rec->employee->full_name }}</div></td>
                                            @endif
                                            <td class="mono" style="font-weight:500;">{{ \Carbon\Carbon::parse($rec->attendance_date)->format('j M Y') }}</td>
                                            <td class="mono">{{ $checkInStr }}</td>
                                            <td class="mono">{{ $checkOutStr }}</td>
                                            <td class="mono">{{ $hoursStr }}</td>
                                            <td>{!! $statusStr !!}</td>
                                        </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="{{ !isset($selectedReportEmployee) ? '6' : '5' }}" style="text-align: center; padding: 20px; color: #888;">No attendance records found for this period.</td>
                                    </tr>
                                @endif
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
                                <button type="button" class="icon-btn btn-ghost icon-btn-danger" onclick="deleteShift({{ $sh->id }})">
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
                        <button type="button" class="icon-btn btn-sm btn-ghost" onclick="changeMonth(-1)">‹</button>
                        <span class="dp-month-label" id="calendarMonthLabel">Month Year</span>
                        <button type="button" class="icon-btn btn-sm btn-ghost" onclick="changeMonth(1)">›</button>
                    </div>
                    <div class="dp-grid" id="calendarGrid">
                        <div class="dp-dow">Su</div><div class="dp-dow">Mo</div><div class="dp-dow">Tu</div><div class="dp-dow">We</div><div class="dp-dow">Th</div><div class="dp-dow">Fr</div><div class="dp-dow">Sa</div>
                        <!-- Days populated via JS -->
                    </div>
                </div>
                <div class="dp-legend">
                    <div class="dp-legend-item"><span class="dp-legend-swatch" style="background:var(--s7);"></span>Already on leave</div>
                    <div class="dp-legend-item"><span class="dp-legend-swatch" style="background:var(--accent);"></span>Selected</div>
                </div>
            </div>
            <div class="modal-foot">
                <button type="button" class="btn" onclick="closeModal('modalEmployeeLeave')">Close</button>
                <form id="leaveToggleForm" method="POST" action="" style="display:inline;">
                    @csrf
                    <input type="hidden" name="dates" id="leaveDatesInput">
                    <button type="button" class="btn" onclick="submitLeave('remove')">Remove Leave</button>
                    <button type="button" class="btn btn-primary" onclick="submitLeave('mark')">Mark as Leave</button>
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
                                    <div class="cell-sub" id="detEmpTitle" style="margin-bottom:12px;">Job Title</div>
                                    <div class="hstack" style="gap:8px;">
                                        <button type="button" class="btn btn-sm" id="detEditBtn" style="padding:4px 12px; font-size:12px;">
                                            <svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:14px;height:14px;margin-right:4px;vertical-align:middle;"><path d="M12 20h9M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"/></svg> Edit
                                        </button>
                                        <button type="button" class="btn btn-sm" id="detDeleteBtn" style="padding:4px 12px; font-size:12px; color:#d93025; border-color:#fad2cf; background:#fce8e6;">
                                            <svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:14px;height:14px;margin-right:4px;vertical-align:middle;"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg> Delete
                                        </button>
                                        <form id="detDeleteForm" method="POST" style="display:none;">
                                            @csrf
                                            @method('DELETE')
                                        </form>
                                    </div>
                                </div>
                                <div style="margin-inline-start:auto; align-self:flex-start;">
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
                            <div class="hstack" style="justify-content:space-between; margin-bottom:12px;">
                                <div class="section-title" style="margin:0;">Attendance History</div>
                                <select id="attendanceFilter" class="form-select" style="width:140px; padding:4px 8px; font-size:13px; border-radius:6px; border:1px solid #dcdcdc; background:#fff;">
                                    <option value="this_week">This Week</option>
                                    <option value="this_month">This Month</option>
                                    <option value="this_year">This Year</option>
                                    <option value="custom">Custom Range</option>
                                </select>
                            </div>
                            
                            <div id="attendanceCustomRange" style="display:none; gap:10px; margin-bottom:12px; align-items:flex-end;">
                                <div style="flex:1;">
                                    <label style="font-size:12px;color:var(--text-muted);display:block;margin-bottom:4px;">From</label>
                                    <input type="date" id="attendanceStartDate" class="form-control" style="width:100%; font-size:13px; padding:4px 8px; border-radius:6px; border:1px solid #dcdcdc; height:32px;">
                                </div>
                                <div style="flex:1;">
                                    <label style="font-size:12px;color:var(--text-muted);display:block;margin-bottom:4px;">To</label>
                                    <input type="date" id="attendanceEndDate" class="form-control" style="width:100%; font-size:13px; padding:4px 8px; border-radius:6px; border:1px solid #dcdcdc; height:32px;">
                                </div>
                                <button type="button" class="btn btn-sm btn-primary" id="attendanceApplyBtn" style="padding:4px 12px; height:32px;">Apply</button>
                            </div>

                            <div class="tablewrap scrollx" style="max-height:200px; overflow-y:auto; border-radius:8px;">
                                <table class="dtable" style="margin:0; border-collapse:collapse;">
                                    <thead style="position:sticky; top:0; background:#f4f7f6; z-index:1; border-bottom:1px solid #dcdcdc;">
                                        <tr>
                                            <th style="font-size:11px;font-weight:700;color:var(--text-muted);padding:8px 12px;text-transform:uppercase;">Date</th>
                                            <th style="font-size:11px;font-weight:700;color:var(--text-muted);padding:8px 12px;text-transform:uppercase;">Check-in</th>
                                            <th style="font-size:11px;font-weight:700;color:var(--text-muted);padding:8px 12px;text-transform:uppercase;">Check-out</th>
                                            <th style="font-size:11px;font-weight:700;color:var(--text-muted);padding:8px 12px;text-transform:uppercase;">Hours</th>
                                            <th style="font-size:11px;font-weight:700;color:var(--text-muted);padding:8px 12px;text-transform:uppercase;">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody id="attendanceTbody">
                                        <tr><td colspan="5" style="text-align:center;color:var(--text-muted);padding:20px;">Select a range to load history</td></tr>
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
        <div class="modal-panel" style="max-width:560px; padding:24px; border-radius:12px;">
            <div class="modal-head" style="margin-bottom:20px; padding-bottom:16px; border-bottom:1px solid #eaeaea;">
                <h3 id="shiftModalTitle" style="font-size:18px; font-weight:700;">Add Shift</h3>
                <button type="button" class="icon-btn btn-ghost" onclick="closeModal('modalShift')">
                    <svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:20px;height:20px;color:#666;"><path d="M18 6L6 18M6 6l12 12"/></svg>
                </button>
            </div>
            <div class="modal-body">
                <div class="field" style="margin-bottom:20px;">
                    <label style="font-size:13px; font-weight:600; color:#333; margin-bottom:6px; display:block;">Shift Name</label>
                    <input type="hidden" id="shiftId">
                    <input type="text" id="shiftName" class="form-control" placeholder="e.g. Night Shift" style="width:100%; padding:10px 12px; border:1px solid #dcdcdc; border-radius:8px; font-size:14px; color:#333;">
                </div>
                
                <div class="form-grid" style="display:grid; grid-template-columns:1fr 1fr; gap:16px; margin-bottom:24px;">
                    <div class="field">
                        <label style="font-size:13px; font-weight:600; color:#333; margin-bottom:6px; display:block;">Start Time</label>
                        <select id="shiftStartTime" class="form-select" style="width:100%; padding:10px 12px; border:1px solid #dcdcdc; border-radius:8px; font-size:14px; color:#333; appearance:none; background:url('data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSIxNiIgaGVpZ2h0PSIxNiIgdmlld0JveD0iMCAwIDI0IDI0IiBmaWxsPSJub25lIiBzdHJva2U9IiM2NjYiIHN0cm9rZS13aWR0aD0iMiIgc3Ryb2tlLWxpbmVjYXA9InJvdW5kIiBzdHJva2UtbGluZWpvaW49InJvdW5kIj48cG9seWxpbmUgcG9pbnRzPSI2IDkgMTIgMTUgMTggOSI+PC9wb2x5bGluZT48L3N2Zz4=')') no-repeat right 12px center; background-color:#fff;">
                            <option value="00:00:00">12:00 AM</option>
<option value="00:15:00">12:15 AM</option>
<option value="00:30:00">12:30 AM</option>
<option value="00:45:00">12:45 AM</option>
<option value="01:00:00">01:00 AM</option>
<option value="01:15:00">01:15 AM</option>
<option value="01:30:00">01:30 AM</option>
<option value="01:45:00">01:45 AM</option>
<option value="02:00:00">02:00 AM</option>
<option value="02:15:00">02:15 AM</option>
<option value="02:30:00">02:30 AM</option>
<option value="02:45:00">02:45 AM</option>
<option value="03:00:00">03:00 AM</option>
<option value="03:15:00">03:15 AM</option>
<option value="03:30:00">03:30 AM</option>
<option value="03:45:00">03:45 AM</option>
<option value="04:00:00">04:00 AM</option>
<option value="04:15:00">04:15 AM</option>
<option value="04:30:00">04:30 AM</option>
<option value="04:45:00">04:45 AM</option>
<option value="05:00:00">05:00 AM</option>
<option value="05:15:00">05:15 AM</option>
<option value="05:30:00">05:30 AM</option>
<option value="05:45:00">05:45 AM</option>
<option value="06:00:00">06:00 AM</option>
<option value="06:15:00">06:15 AM</option>
<option value="06:30:00">06:30 AM</option>
<option value="06:45:00">06:45 AM</option>
<option value="07:00:00">07:00 AM</option>
<option value="07:15:00">07:15 AM</option>
<option value="07:30:00">07:30 AM</option>
<option value="07:45:00">07:45 AM</option>
<option value="08:00:00">08:00 AM</option>
<option value="08:15:00">08:15 AM</option>
<option value="08:30:00">08:30 AM</option>
<option value="08:45:00">08:45 AM</option>
<option value="09:00:00">09:00 AM</option>
<option value="09:15:00">09:15 AM</option>
<option value="09:30:00">09:30 AM</option>
<option value="09:45:00">09:45 AM</option>
<option value="10:00:00">10:00 AM</option>
<option value="10:15:00">10:15 AM</option>
<option value="10:30:00">10:30 AM</option>
<option value="10:45:00">10:45 AM</option>
<option value="11:00:00">11:00 AM</option>
<option value="11:15:00">11:15 AM</option>
<option value="11:30:00">11:30 AM</option>
<option value="11:45:00">11:45 AM</option>
<option value="12:00:00">12:00 PM</option>
<option value="12:15:00">12:15 PM</option>
<option value="12:30:00">12:30 PM</option>
<option value="12:45:00">12:45 PM</option>
<option value="13:00:00">01:00 PM</option>
<option value="13:15:00">01:15 PM</option>
<option value="13:30:00">01:30 PM</option>
<option value="13:45:00">01:45 PM</option>
<option value="14:00:00">02:00 PM</option>
<option value="14:15:00">02:15 PM</option>
<option value="14:30:00">02:30 PM</option>
<option value="14:45:00">02:45 PM</option>
<option value="15:00:00">03:00 PM</option>
<option value="15:15:00">03:15 PM</option>
<option value="15:30:00">03:30 PM</option>
<option value="15:45:00">03:45 PM</option>
<option value="16:00:00">04:00 PM</option>
<option value="16:15:00">04:15 PM</option>
<option value="16:30:00">04:30 PM</option>
<option value="16:45:00">04:45 PM</option>
<option value="17:00:00">05:00 PM</option>
<option value="17:15:00">05:15 PM</option>
<option value="17:30:00">05:30 PM</option>
<option value="17:45:00">05:45 PM</option>
<option value="18:00:00">06:00 PM</option>
<option value="18:15:00">06:15 PM</option>
<option value="18:30:00">06:30 PM</option>
<option value="18:45:00">06:45 PM</option>
<option value="19:00:00">07:00 PM</option>
<option value="19:15:00">07:15 PM</option>
<option value="19:30:00">07:30 PM</option>
<option value="19:45:00">07:45 PM</option>
<option value="20:00:00">08:00 PM</option>
<option value="20:15:00">08:15 PM</option>
<option value="20:30:00">08:30 PM</option>
<option value="20:45:00">08:45 PM</option>
<option value="21:00:00">09:00 PM</option>
<option value="21:15:00">09:15 PM</option>
<option value="21:30:00">09:30 PM</option>
<option value="21:45:00">09:45 PM</option>
<option value="22:00:00">10:00 PM</option>
<option value="22:15:00">10:15 PM</option>
<option value="22:30:00">10:30 PM</option>
<option value="22:45:00">10:45 PM</option>
<option value="23:00:00">11:00 PM</option>
<option value="23:15:00">11:15 PM</option>
<option value="23:30:00">11:30 PM</option>
<option value="23:45:00">11:45 PM</option>

                        </select>
                    </div>
                    <div class="field">
                        <label style="font-size:13px; font-weight:600; color:#333; margin-bottom:6px; display:block;">End Time</label>
                        <select id="shiftEndTime" class="form-select" style="width:100%; padding:10px 12px; border:1px solid #dcdcdc; border-radius:8px; font-size:14px; color:#333; appearance:none; background:url('data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSIxNiIgaGVpZ2h0PSIxNiIgdmlld0JveD0iMCAwIDI0IDI0IiBmaWxsPSJub25lIiBzdHJva2U9IiM2NjYiIHN0cm9rZS13aWR0aD0iMiIgc3Ryb2tlLWxpbmVjYXA9InJvdW5kIiBzdHJva2UtbGluZWpvaW49InJvdW5kIj48cG9seWxpbmUgcG9pbnRzPSI2IDkgMTIgMTUgMTggOSI+PC9wb2x5bGluZT48L3N2Zz4=')') no-repeat right 12px center; background-color:#fff;">
                            <option value="00:00:00">12:00 AM</option>
<option value="00:15:00">12:15 AM</option>
<option value="00:30:00">12:30 AM</option>
<option value="00:45:00">12:45 AM</option>
<option value="01:00:00">01:00 AM</option>
<option value="01:15:00">01:15 AM</option>
<option value="01:30:00">01:30 AM</option>
<option value="01:45:00">01:45 AM</option>
<option value="02:00:00">02:00 AM</option>
<option value="02:15:00">02:15 AM</option>
<option value="02:30:00">02:30 AM</option>
<option value="02:45:00">02:45 AM</option>
<option value="03:00:00">03:00 AM</option>
<option value="03:15:00">03:15 AM</option>
<option value="03:30:00">03:30 AM</option>
<option value="03:45:00">03:45 AM</option>
<option value="04:00:00">04:00 AM</option>
<option value="04:15:00">04:15 AM</option>
<option value="04:30:00">04:30 AM</option>
<option value="04:45:00">04:45 AM</option>
<option value="05:00:00">05:00 AM</option>
<option value="05:15:00">05:15 AM</option>
<option value="05:30:00">05:30 AM</option>
<option value="05:45:00">05:45 AM</option>
<option value="06:00:00">06:00 AM</option>
<option value="06:15:00">06:15 AM</option>
<option value="06:30:00">06:30 AM</option>
<option value="06:45:00">06:45 AM</option>
<option value="07:00:00">07:00 AM</option>
<option value="07:15:00">07:15 AM</option>
<option value="07:30:00">07:30 AM</option>
<option value="07:45:00">07:45 AM</option>
<option value="08:00:00">08:00 AM</option>
<option value="08:15:00">08:15 AM</option>
<option value="08:30:00">08:30 AM</option>
<option value="08:45:00">08:45 AM</option>
<option value="09:00:00">09:00 AM</option>
<option value="09:15:00">09:15 AM</option>
<option value="09:30:00">09:30 AM</option>
<option value="09:45:00">09:45 AM</option>
<option value="10:00:00">10:00 AM</option>
<option value="10:15:00">10:15 AM</option>
<option value="10:30:00">10:30 AM</option>
<option value="10:45:00">10:45 AM</option>
<option value="11:00:00">11:00 AM</option>
<option value="11:15:00">11:15 AM</option>
<option value="11:30:00">11:30 AM</option>
<option value="11:45:00">11:45 AM</option>
<option value="12:00:00">12:00 PM</option>
<option value="12:15:00">12:15 PM</option>
<option value="12:30:00">12:30 PM</option>
<option value="12:45:00">12:45 PM</option>
<option value="13:00:00">01:00 PM</option>
<option value="13:15:00">01:15 PM</option>
<option value="13:30:00">01:30 PM</option>
<option value="13:45:00">01:45 PM</option>
<option value="14:00:00">02:00 PM</option>
<option value="14:15:00">02:15 PM</option>
<option value="14:30:00">02:30 PM</option>
<option value="14:45:00">02:45 PM</option>
<option value="15:00:00">03:00 PM</option>
<option value="15:15:00">03:15 PM</option>
<option value="15:30:00">03:30 PM</option>
<option value="15:45:00">03:45 PM</option>
<option value="16:00:00">04:00 PM</option>
<option value="16:15:00">04:15 PM</option>
<option value="16:30:00">04:30 PM</option>
<option value="16:45:00">04:45 PM</option>
<option value="17:00:00">05:00 PM</option>
<option value="17:15:00">05:15 PM</option>
<option value="17:30:00">05:30 PM</option>
<option value="17:45:00">05:45 PM</option>
<option value="18:00:00">06:00 PM</option>
<option value="18:15:00">06:15 PM</option>
<option value="18:30:00">06:30 PM</option>
<option value="18:45:00">06:45 PM</option>
<option value="19:00:00">07:00 PM</option>
<option value="19:15:00">07:15 PM</option>
<option value="19:30:00">07:30 PM</option>
<option value="19:45:00">07:45 PM</option>
<option value="20:00:00">08:00 PM</option>
<option value="20:15:00">08:15 PM</option>
<option value="20:30:00">08:30 PM</option>
<option value="20:45:00">08:45 PM</option>
<option value="21:00:00">09:00 PM</option>
<option value="21:15:00">09:15 PM</option>
<option value="21:30:00">09:30 PM</option>
<option value="21:45:00">09:45 PM</option>
<option value="22:00:00">10:00 PM</option>
<option value="22:15:00">10:15 PM</option>
<option value="22:30:00">10:30 PM</option>
<option value="22:45:00">10:45 PM</option>
<option value="23:00:00">11:00 PM</option>
<option value="23:15:00">11:15 PM</option>
<option value="23:30:00">11:30 PM</option>
<option value="23:45:00">11:45 PM</option>

                        </select>
                    </div>
                </div>

                <div style="margin-bottom:16px;">
                    <div style="font-size:13px; font-weight:700; color:#333;">Effective Date Range (optional)</div>
                    <div style="font-size:12px; color:#666; margin-bottom:12px;">Leave blank for an ongoing, recurring shift.</div>
                    
                    <div class="form-grid" style="display:grid; grid-template-columns:1fr 1fr; gap:16px;">
                        <div class="field">
                            <label style="font-size:13px; font-weight:600; color:#333; margin-bottom:6px; display:block;">From</label>
                            <input type="date" id="shiftStartDate" class="form-control" style="width:100%; padding:10px 12px; border:1px solid #dcdcdc; border-radius:8px; font-size:14px; color:#666; appearance:none; background:url('data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSIxNiIgaGVpZ2h0PSIxNiIgdmlld0JveD0iMCAwIDI0IDI0IiBmaWxsPSJub25lIiBzdHJva2U9IiM2NjYiIHN0cm9rZS13aWR0aD0iMiIgc3Ryb2tlLWxpbmVjYXA9InJvdW5kIiBzdHJva2UtbGluZWpvaW49InJvdW5kIj48cmVjdCB4PSIzIiB5PSI0IiB3aWR0aD0iMTgiIGhlaWdodD0iMTgiIHJ4PSIyIiByeT0iMiI+PC9yZWN0PjxsaW5lIHgxPSIxNiIgeTE9IjIiIHgyPSIxNiIgeTI9IjYiPjwvbGluZT48bGluZSB4MT0iOCIgeTE9IjIiIHgyPSI4IiB5Mj0iNiI+PC9saW5lPjxsaW5lIHgxPSIzIiB5MT0iMTAiIHgyPSIyMSIgeTI9IjEwIj48L2xpbmU+PC9zdmc+')') no-repeat right 12px center; background-color:#fff; color:#333;">
                        </div>
                        <div class="field">
                            <label style="font-size:13px; font-weight:600; color:#333; margin-bottom:6px; display:block;">To</label>
                            <input type="date" id="shiftEndDate" class="form-control" style="width:100%; padding:10px 12px; border:1px solid #dcdcdc; border-radius:8px; font-size:14px; color:#666; appearance:none; background:url('data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSIxNiIgaGVpZ2h0PSIxNiIgdmlld0JveD0iMCAwIDI0IDI0IiBmaWxsPSJub25lIiBzdHJva2U9IiM2NjYiIHN0cm9rZS13aWR0aD0iMiIgc3Ryb2tlLWxpbmVjYXA9InJvdW5kIiBzdHJva2UtbGluZWpvaW49InJvdW5kIj48cmVjdCB4PSIzIiB5PSI0IiB3aWR0aD0iMTgiIGhlaWdodD0iMTgiIHJ4PSIyIiByeT0iMiI+PC9yZWN0PjxsaW5lIHgxPSIxNiIgeTE9IjIiIHgyPSIxNiIgeTI9IjYiPjwvbGluZT48bGluZSB4MT0iOCIgeTE9IjIiIHgyPSI4IiB5Mj0iNiI+PC9saW5lPjxsaW5lIHgxPSIzIiB5MT0iMTAiIHgyPSIyMSIgeTI9IjEwIj48L2xpbmU+PC9zdmc+')') no-repeat right 12px center; background-color:#fff; color:#333;">
                        </div>
                    </div>
                </div>

            </div>
            <div class="modal-foot" style="margin-top:24px; padding-top:20px; border-top:1px solid #eaeaea; justify-content:flex-end; gap:12px;">
                <button type="button" class="btn" onclick="closeModal('modalShift')" style="padding:10px 16px; font-weight:600; border:1px solid #dcdcdc; border-radius:8px; background:#fff; color:#333;">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="saveShift()" class="btn btn-primary">Save</button>
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
                const isMatch = btn.getAttribute('onclick').includes(`switchTab('${tabKey}')`);
                btn.classList.toggle('active', isMatch);
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

        let calCurrentDate = new Date();
        let calLeaves = [];
        let calStagedDates = new Set();
        let currentEmpId = null;

        function renderCalendar() {
            const year = calCurrentDate.getFullYear();
            const month = calCurrentDate.getMonth();
            const label = document.getElementById('calendarMonthLabel');
            label.innerText = calCurrentDate.toLocaleDateString('en-US', { month: 'long', year: 'numeric' });

            const firstDay = new Date(year, month, 1).getDay();
            const daysInMonth = new Date(year, month + 1, 0).getDate();
            const today = new Date();
            
            const grid = document.getElementById('calendarGrid');
            grid.innerHTML = '<div class="dp-dow">Su</div><div class="dp-dow">Mo</div><div class="dp-dow">Tu</div><div class="dp-dow">We</div><div class="dp-dow">Th</div><div class="dp-dow">Fr</div><div class="dp-dow">Sa</div>';

            // blanks
            for (let i = 0; i < firstDay; i++) {
                grid.innerHTML += '<div></div>';
            }

            for (let i = 1; i <= daysInMonth; i++) {
                const dateStr = `${year}-${String(month+1).padStart(2,'0')}-${String(i).padStart(2,'0')}`;
                
                let classes = ['dp-day'];
                if (today.getDate() === i && today.getMonth() === month && today.getFullYear() === year) {
                    classes.push('today');
                }
                
                if (calLeaves.includes(dateStr)) {
                    classes.push('on-leave');
                }
                
                if (calStagedDates.has(dateStr)) {
                    classes.push('staged');
                }

                grid.innerHTML += `<button type="button" class="${classes.join(' ')}" onclick="toggleCalDate('${dateStr}', this)">${i}</button>`;
            }
        }

        function changeMonth(delta) {
            calCurrentDate.setMonth(calCurrentDate.getMonth() + delta);
            renderCalendar();
        }

        function toggleCalDate(dateStr, el) {
            if (calStagedDates.has(dateStr)) {
                calStagedDates.delete(dateStr);
                el.classList.remove('staged');
            } else {
                calStagedDates.add(dateStr);
                el.classList.add('staged');
            }
        }

        function submitLeave(action) {
            if (calStagedDates.size === 0) {
                alert('Please select at least one date.');
                return;
            }
            document.getElementById('leaveDatesInput').value = Array.from(calStagedDates).join(',');
            const form = document.getElementById('leaveToggleForm');
            const actionInput = document.createElement('input');
            actionInput.type = 'hidden';
            actionInput.name = 'action';
            actionInput.value = action;
            form.appendChild(actionInput);
            form.submit();
        }

        function openEmployeeLeaveModal(emp) {
            currentEmpId = emp.id;
            const title = document.getElementById('leaveModalTitle');
            title.innerText = 'Mark Leave — ' + (emp.first_name + ' ' + (emp.last_name || '')).trim();
            document.getElementById('leaveToggleForm').action = '/employees/' + emp.id + '/leave';
            
            calLeaves = emp.leaves ? emp.leaves.map(l => l.date.split('T')[0]) : [];
            calStagedDates.clear();
            calCurrentDate = new Date();
            renderCalendar();
            
            openModal('modalEmployeeLeave');
        }

        let currentDetailEmp = null;

        function openEmployeeDetailModal(emp) {
            currentDetailEmp = emp;
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

            const statusChip = document.getElementById('detEmpStatusChip');
            if(emp.status === 'active') {
                statusChip.className = 'chip chip-good';
                statusChip.innerHTML = '● Active';
            } else if(emp.status === 'on_leave') {
                statusChip.className = 'chip chip-warning';
                statusChip.innerHTML = '● On Leave';
            } else {
                statusChip.className = 'chip chip-muted';
                statusChip.innerHTML = '● Inactive';
            }

            // Edit and Delete buttons logic
            document.getElementById('detEditBtn').onclick = () => {
                closeModal('modalEmployeeDetail');
                openEmployeeModal(emp);
            };
            
            document.getElementById('detDeleteBtn').onclick = () => {
                if(confirm('Are you sure you want to delete this employee?')) {
                    const form = document.getElementById('detDeleteForm');
                    form.action = '/employees/' + emp.id;
                    form.submit();
                }
            };

            const qrContainer = document.getElementById('detQrCanvas');
            qrContainer.innerHTML = '';
            if (typeof QRCode !== 'undefined') {
                new QRCode(qrContainer, {
                    text: emp.employee_code || 'EMP-001',
                    width: 140,
                    height: 140,
                });
            }
            
            // Reset filter and fetch attendance
            const filterEl = document.getElementById('attendanceFilter');
            filterEl.value = 'this_week';
            document.getElementById('attendanceCustomRange').style.display = 'none';
            fetchEmployeeAttendance(emp.id, 'this_week');

            openModal('modalEmployeeDetail');
        }

        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('attendanceFilter').addEventListener('change', function(e) {
                const val = e.target.value;
                const customRange = document.getElementById('attendanceCustomRange');
                if (val === 'custom') {
                    customRange.style.display = 'flex';
                } else {
                    customRange.style.display = 'none';
                    if (currentDetailEmp) {
                        fetchEmployeeAttendance(currentDetailEmp.id, val);
                    }
                }
            });

            document.getElementById('attendanceApplyBtn').addEventListener('click', function() {
                const start = document.getElementById('attendanceStartDate').value;
                const end = document.getElementById('attendanceEndDate').value;
                if (currentDetailEmp) {
                    fetchEmployeeAttendance(currentDetailEmp.id, 'custom', start, end);
                }
            });
        });

        function fetchEmployeeAttendance(empId, filter, start = '', end = '') {
            const tbody = document.getElementById('attendanceTbody');
            tbody.innerHTML = '<tr><td colspan="5" style="text-align:center;color:var(--text-muted);padding:20px;">Loading...</td></tr>';
            
            let url = `/employees/${empId}/attendance?filter=${filter}`;
            if (filter === 'custom') {
                if (!start || !end) {
                    tbody.innerHTML = '<tr><td colspan="5" style="text-align:center;color:var(--text-muted);padding:20px;">Please select both start and end dates.</td></tr>';
                    return;
                }
                url += `&start_date=${start}&end_date=${end}`;
            }

            fetch(url)
                .then(res => res.json())
                .then(data => {
                    if (!data || data.length === 0) {
                        tbody.innerHTML = '<tr><td colspan="5" style="text-align:center;color:var(--text-muted);padding:20px;">No attendance records found for this period.</td></tr>';
                        return;
                    }
                    tbody.innerHTML = '';
                    data.forEach(row => {
                        const tr = document.createElement('tr');
                        tr.innerHTML = `
                            <td class="mono" style="padding:10px 12px;border-bottom:1px solid #f0f0f0;">${row.date_formatted}</td>
                            <td class="mono" style="padding:10px 12px;border-bottom:1px solid #f0f0f0;">${row.check_in}</td>
                            <td class="mono" style="padding:10px 12px;border-bottom:1px solid #f0f0f0;">${row.check_out}</td>
                            <td class="mono" style="padding:10px 12px;border-bottom:1px solid #f0f0f0;">${row.hours}</td>
                            <td style="padding:10px 12px;border-bottom:1px solid #f0f0f0;">${row.status_html}</td>
                        `;
                        tbody.appendChild(tr);
                    });
                })
                .catch(err => {
                    console.error('Failed to fetch attendance:', err);
                    tbody.innerHTML = '<tr><td colspan="5" style="text-align:center;color:#d93025;padding:20px;">Error loading attendance.</td></tr>';
                });
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
                document.getElementById('shiftId').value = sh.id;
                document.getElementById('shiftName').value = sh.name || '';
                
                // Format times for input
                let start = sh.start_time ? sh.start_time.substring(0,5) + ':00' : '08:00:00';
                let end = sh.end_time ? sh.end_time.substring(0,5) + ':00' : '17:00:00';
                
                document.getElementById('shiftStartTime').value = start;
                document.getElementById('shiftEndTime').value = end;
                document.getElementById('shiftStartDate').value = sh.effective_from ? sh.effective_from.split('T')[0] : '';
                document.getElementById('shiftEndDate').value = sh.effective_to ? sh.effective_to.split('T')[0] : '';
            } else {
                title.innerText = 'Add Shift';
                document.getElementById('shiftId').value = '';
                document.getElementById('shiftName').value = '';
                document.getElementById('shiftStartTime').value = '08:00:00';
                document.getElementById('shiftEndTime').value = '17:00:00';
                document.getElementById('shiftStartDate').value = '';
                document.getElementById('shiftEndDate').value = '';
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
        function doCheckIn(empId) {
            fetch(`/employees/${empId}/checkin`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
                body: JSON.stringify({ _token: '{{ csrf_token() }}' })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    window.location.reload();
                } else {
                    alert(data.message || 'Error checking in');
                }
            })
            .catch(err => {
                console.error(err);
                alert('Server error.');
            });
        }

        function doCheckOut(empId) {
            fetch(`/employees/${empId}/checkout`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
                body: JSON.stringify({ _token: '{{ csrf_token() }}' })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    window.location.reload();
                } else {
                    alert(data.message || 'Error checking out');
                }
            })
            .catch(err => {
                console.error(err);
                alert('Server error.');
            });
        }

        function deleteShift(id) {
            if (!confirm('Are you sure you want to delete this shift?')) return;
            
            fetch(`/lookups/shifts/${id}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    _method: 'DELETE',
                    _token: '{{ csrf_token() }}'
                })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    window.location.reload();
                } else {
                    alert('Error deleting shift.');
                }
            })
            .catch(err => {
                console.error(err);
                alert('Server error.');
            });
        }

        function saveShift() {
            const name = document.getElementById('shiftName').value;
            const startTime = document.getElementById('shiftStartTime').value;
            const endTime = document.getElementById('shiftEndTime').value;
            const startDate = document.getElementById('shiftStartDate').value;
            const endDate = document.getElementById('shiftEndDate').value;

            if (!name) {
                alert('Shift Name is required.');
                return;
            }

            const payload = {
                name: name,
                start_time: startTime,
                end_time: endTime,
                effective_from: startDate || null,
                effective_to: endDate || null,
                _token: '{{ csrf_token() }}'
            };

            const shiftId = document.getElementById('shiftId').value;
            let url = '{{ route("lookups.shifts.store") }}';
            let method = 'POST';
            
            if (shiftId) {
                url = `/lookups/shifts/${shiftId}`;
                payload._method = 'PUT'; // Laravel method spoofing
            }

            fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify(payload)
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    closeModal('modalShift');
                    window.location.reload(); // Reload to show new shift
                } else {
                    alert('Error saving shift.');
                }
            })
            .catch(err => {
                console.error(err);
                alert('Server error.');
            });
        }
    </script>
    <script>
        function toggleSidebar() {
            document.body.classList.toggle('sidebar-open');
        }
    </script>
</body>
</html>
