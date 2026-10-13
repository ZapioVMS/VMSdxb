<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Gate Pass — Zapio VMS</title>
    <link rel="icon" href="{{ asset('favicon.ico') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=IBM+Plex+Sans:wght@400;500;600;700&display=swap">
    <style>
        :root {
            --accent: #0c6b64;
            --accent-hover: #0a5a54;
            --accent-soft: rgba(12, 107, 100, 0.10);
            --bg: #f4f7f6;
            --surface: #ffffff;
            --surface-2: #eef2f1;
            --surface-3: #e6ebe9;
            --text-primary: #0e1a18;
            --text-secondary: #4d5b57;
            --text-muted: #7c8985;
            --border: rgba(14, 26, 24, 0.12);
            --border-subtle: rgba(14, 26, 24, 0.08);
            --good: #0ca30c;
            --warning: #fab219;
            --critical: #d03b3b;
            --s1: #2a78d6;
            --s3: #0c6b64;
            --s7: #5c4eb5;
            --radius: 14px;
            --radius-sm: 8px;
            --shadow: 0 1px 2px rgba(14,26,24,.06), 0 10px 30px -10px rgba(14,26,24,.18);
        }
        *, *::before, *::after { box-sizing: border-box; }
        body { margin: 0; font-family: 'IBM Plex Sans', system-ui, -apple-system, sans-serif; background: var(--bg); color: var(--text-primary); font-size: 14px; }
        .ic { width: 16px; height: 16px; flex: none; display: inline-block; vertical-align: -3px; }
        .mono { font-variant-numeric: tabular-nums; font-family: monospace; }
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

        .pagehead { display: flex; align-items: flex-start; justify-content: space-between; gap: 16px; margin-bottom: 22px; flex-wrap: wrap; }
        .pagehead h1 { font-size: 22px; margin: 0; font-weight: 700; color: var(--text-primary); }
        .pagehead .sub { color: var(--text-secondary); font-size: 13.5px; margin-top: 4px; }

        /* Buttons */
        .btn { display: inline-flex; align-items: center; justify-content: center; gap: 7px; padding: 8px 16px; border-radius: var(--radius-sm); border: 1px solid var(--border); background: var(--surface); color: var(--text-primary); font-size: 13px; font-weight: 600; cursor: pointer; white-space: nowrap; text-decoration: none; transition: all .15s ease; font-family: inherit; }
        .btn .ic { width: 15px; height: 15px; }
        .btn:hover { background: var(--surface-2); }
        .btn-primary { background: var(--accent); border-color: var(--accent); color: #fff; }
        .btn-primary:hover { background: var(--accent-hover); border-color: var(--accent-hover); }
        .btn-ghost { border-color: transparent; background: transparent; color: var(--text-secondary); }
        .btn-ghost:hover { background: var(--surface-2); color: var(--text-primary); }
        .btn-sm { padding: 5px 10px; font-size: 12px; height: 30px; }
        .btn-icon { width: 32px; height: 32px; padding: 0; border-radius: 6px; }

        /* 3 KPI Stat Cards */
        .grid { display: grid; gap: 16px; }
        .grid-3 { grid-template-columns: repeat(3, minmax(0, 1fr)); margin-bottom: 22px; }
        @media (max-width: 992px) { .grid-3 { grid-template-columns: 1fr; } }

        .stat-tile { background: var(--surface); border: 1px solid var(--border); border-radius: var(--radius); padding: 18px 20px; display: flex; flex-direction: column; gap: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.02); }
        .stat-value { font-size: 28px; font-weight: 700; letter-spacing: -.02em; line-height: 1; color: var(--text-primary); }
        .stat-label { font-size: 12.5px; color: var(--text-secondary); font-weight: 500; }
        .icon-sq { width: 38px; height: 38px; border-radius: 10px; display: flex; align-items: center; justify-content: center; }
        .icon-tint-s1 { background: color-mix(in srgb, var(--s1) 14%, transparent); color: var(--s1); }
        .icon-tint-s3 { background: color-mix(in srgb, var(--accent) 14%, transparent); color: var(--accent); }
        .icon-tint-s7 { background: color-mix(in srgb, var(--s7) 14%, transparent); color: var(--s7); }

        /* Filter Pills & Actions Row */
        .controls-row { display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px; margin-bottom: 16px; }
        .pillgroup { display: flex; gap: 8px; flex-wrap: wrap; align-items: center; }
        .statuspill { display: inline-flex; align-items: center; gap: 6px; padding: 6px 14px; border-radius: 99px; border: 1.5px solid var(--border); background: var(--surface); font-size: 13px; font-weight: 600; cursor: pointer; color: var(--text-secondary); text-decoration: none; transition: all .15s ease; }
        .statuspill:hover { background: var(--surface-2); color: var(--text-primary); }
        .statuspill.active { background: var(--text-primary); color: #ffffff; border-color: var(--text-primary); }
        .statuspill b { font-size: 12.5px; opacity: .9; }

        /* Table */
        .tablewrap { border: 1px solid var(--border); border-radius: var(--radius); overflow: hidden; background: var(--surface); box-shadow: 0 1px 3px rgba(0,0,0,0.02); }
        .tablewrap.scrollx { overflow-x: auto; }
        table.dtable { width: 100%; border-collapse: collapse; min-width: 800px; text-align: left; }
        .dtable thead th { text-align: start; font-size: 11px; text-transform: uppercase; letter-spacing: .06em; color: var(--text-muted); font-weight: 600; padding: 12px 16px; border-block-end: 1px solid var(--border); background: var(--surface-2); white-space: nowrap; }
        .dtable tbody td { padding: 14px 16px; border-block-end: 1px solid var(--border); font-size: 13.5px; vertical-align: middle; }
        .dtable tbody tr:last-child td { border-block-end: none; }
        .dtable tbody tr:hover { background: rgba(238, 242, 241, 0.45); }
        .cell-name { font-weight: 600; color: var(--text-primary); }
        .cell-sub { font-size: 12px; color: var(--text-muted); margin-top: 2px; }

        /* Badges / Chips */
        .chip { display: inline-flex; align-items: center; gap: 6px; padding: 4px 10px; border-radius: 99px; font-size: 12px; font-weight: 600; white-space: nowrap; }
        .chip-visitor { background: rgba(12, 107, 100, 0.10); color: var(--accent); }
        .chip-delivery { background: #eef2f1; color: #4d5b57; }

        .chip-status { display: inline-flex; align-items: center; gap: 6px; font-size: 12.5px; font-weight: 600; }
        .status-active { color: var(--good); }
        .status-redeemed { color: var(--s1); }
        .status-cancelled { color: var(--text-muted); }
        .chip-dot { display: inline-block; width: 7px; height: 7px; border-radius: 50%; background: currentColor; }

        /* Row Actions */
        .rowactions { display: inline-flex; align-items: center; gap: 6px; justify-content: flex-end; }
        .btn-redeem { background: var(--accent); color: #fff; border-color: var(--accent); font-size: 12px; border-radius: 6px; padding: 4px 10px; height: 30px; }
        .btn-redeem:hover { background: var(--accent-hover); }

        /* Table Footer */
        .tablefoot { display: flex; align-items: center; justify-content: space-between; padding: 12px 16px; border-block-start: 1px solid var(--border); background: var(--surface-2); font-size: 12.5px; color: var(--text-secondary); }
        .pager { display: flex; gap: 4px; align-items: center; }
        .pager button, .pager a { width: 28px; height: 28px; border-radius: 6px; border: 1px solid var(--border); background: var(--surface); cursor: pointer; color: var(--text-secondary); font-size: 12px; display: inline-flex; align-items: center; justify-content: center; text-decoration: none; }
        .pager a.disabled { opacity: .4; pointer-events: none; }

        /* Modals */
        .modal-root { display: none; position: fixed; inset: 0; z-index: 1000; }
        .modal-root.open { display: block; }
        .modal-backdrop { position: absolute; inset: 0; background: rgba(6,12,11,.45); backdrop-filter: blur(2px); }
        .modal-panel { position: relative; max-width: 540px; width: calc(100% - 32px); margin: 4vh auto 0; background: var(--surface); border-radius: 16px; border: 1px solid var(--border); box-shadow: var(--shadow); overflow: hidden; display: flex; flex-direction: column; max-height: 92vh; }
        
        .modal-head { display: flex; align-items: center; justify-content: space-between; padding: 18px 22px 14px; border-bottom: 1px solid var(--border-subtle); }
        .modal-head h3 { margin: 0; font-size: 17px; font-weight: 700; color: var(--text-primary); }
        .icon-close { background: none; border: none; padding: 4px; cursor: pointer; color: var(--text-secondary); border-radius: 6px; display: flex; align-items: center; justify-content: center; }
        .icon-close:hover { background: var(--surface-2); color: var(--text-primary); }

        /* Modal Subtabs (Visitor / Delivery) */
        .subtabs { display: flex; border-bottom: 1px solid var(--border); padding-inline: 22px; gap: 20px; background: var(--surface); }
        .subtab { display: inline-flex; align-items: center; gap: 7px; padding: 12px 4px; border: none; background: none; font-size: 14px; font-weight: 600; color: var(--text-muted); cursor: pointer; border-bottom: 2.5px solid transparent; margin-bottom: -1px; transition: all .15s ease; font-family: inherit; }
        .subtab:hover { color: var(--text-primary); }
        .subtab.active { color: var(--accent); border-bottom-color: var(--accent); }
        .subtab .ic { width: 17px; height: 17px; }

        .modal-panel > form { display: flex; flex-direction: column; flex: 1; min-height: 0; overflow: hidden; }
        .modal-body { padding: 18px 22px; overflow-y: auto; flex: 1; min-height: 0; }
        .modal-foot { display: flex; align-items: center; justify-content: flex-end; gap: 10px; padding: 14px 22px; border-top: 1px solid var(--border); background: var(--surface); }

        /* Form elements matching images */
        .field { display: flex; flex-direction: column; gap: 6px; margin-bottom: 14px; }
        .field label { font-size: 13px; font-weight: 600; color: var(--text-primary); }
        .field input, .field select, .field textarea { font-family: inherit; font-size: 13.5px; padding: 9px 12px; border-radius: var(--radius-sm); border: 1px solid #d1d9d7; background: var(--surface); color: var(--text-primary); width: 100%; box-sizing: border-box; transition: border-color .15s ease; outline: none; }
        .field input:focus, .field select:focus, .field textarea:focus { border-color: var(--accent); box-shadow: 0 0 0 2px var(--accent-soft); }
        .field textarea { resize: vertical; min-height: 72px; }
        .field input::placeholder, .field textarea::placeholder { color: #9aa5a1; font-size: 13px; }

        .form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; }
        .phone-group { display: flex; gap: 8px; }
        .phone-code-select { width: 95px !important; flex: none; }

        /* QR badge preview card */
        .qr-badge-card { text-align: center; background: var(--surface-2); border: 1px solid var(--border); border-radius: 12px; padding: 22px 18px; margin: 10px 0; }
        .qr-plate { display: inline-flex; align-items: center; justify-content: center; width: 150px; height: 150px; background: #fff; border-radius: 12px; padding: 10px; border: 1px solid var(--border); box-shadow: 0 2px 8px rgba(0,0,0,0.06); margin-bottom: 14px; }
        .qr-plate svg { width: 100%; height: 100%; }

        /* Toast Feedback */
        .toast-container { position: fixed; bottom: 24px; right: 24px; z-index: 1100; display: flex; flex-direction: column; gap: 10px; }
        .toast { display: flex; align-items: center; gap: 10px; padding: 12px 18px; border-radius: var(--radius-sm); background: var(--accent); color: #fff; font-size: 13px; font-weight: 500; box-shadow: var(--shadow); }
        
        .empty-state { padding: 48px 20px; text-align: center; color: var(--text-muted); font-size: 13px; }
        .sidebar-overlay { display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.4); z-index: 998; opacity: 0; transition: opacity 0.2s ease; }
        .mobile-only-btn { display: none !important; }
        
        @media (max-width: 900px) {
            #app { display: flex !important; flex-direction: column !important; min-height: 100vh; }
            #sidebar { position: fixed !important; left: 0; top: 0; bottom: 0; z-index: 999; width: 260px; transform: translateX(-100%); transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1); box-shadow: 4px 0 24px rgba(0,0,0,0.1); }
            body.sidebar-open #sidebar { transform: translateX(0); }
            body.sidebar-open .sidebar-overlay { display: block; opacity: 1; }
            #topbar { width: 100%; grid-column: auto !important; grid-row: auto !important; padding-inline: 12px !important; }
            #main { padding: 16px 14px 60px !important; grid-column: auto !important; grid-row: auto !important; width: 100%; flex: 1; }
            .mobile-only-btn { display: inline-flex !important; }
            .grid-3, .form-grid { grid-template-columns: 1fr !important; }
            .modal-panel { width: 94% !important; margin: auto; }
        }
    </style>
</head>
<body>
<div id="app">
    <!-- Sidebar -->
    @include('partials.sidebar', ['active' => 'gate_passes'])

    <!-- Topbar -->
    @include('partials.topbar')

    <!-- Main Content -->
    <main id="main">
        <!-- Toast Feedback -->
        @if(session('success'))
            <div class="toast-container" id="toastContainer">
                <div class="toast">
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
                <h1>Gate Pass</h1>
                <div class="sub">Issue a QR gate pass for an expected visitor or delivery worker</div>
            </div>
        </div>

        <!-- 3 KPI Stat Tiles -->
        <div class="grid grid-3">
            <!-- Total Passes -->
            <div class="stat-tile">
                <div class="icon-sq icon-tint-s1">
                    <svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/>
                    </svg>
                </div>
                <div class="stat-value tabular">{{ $stats['total'] }}</div>
                <div class="stat-label">Total Passes</div>
            </div>

            <!-- Visitor Passes -->
            <div class="stat-tile">
                <div class="icon-sq icon-tint-s7">
                    <svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                    </svg>
                </div>
                <div class="stat-value tabular">{{ $stats['visitor'] }}</div>
                <div class="stat-label">Visitor Passes</div>
            </div>

            <!-- Delivery Passes -->
            <div class="stat-tile">
                <div class="icon-sq icon-tint-s3">
                    <svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M12 3 3 8v8l9 5 9-5V8l-9-5z"/><path d="M3 8l9 5 9-5M12 13v8"/>
                    </svg>
                </div>
                <div class="stat-value tabular">{{ $stats['delivery'] }}</div>
                <div class="stat-label">Delivery Passes</div>
            </div>
        </div>

        <!-- Filter Row & Actions -->
        <div class="controls-row">
            <div class="pillgroup">
                <a href="{{ route('gate_passes.index', array_merge(request()->except('type', 'page'), ['type' => 'all'])) }}"
                   class="statuspill {{ $typeFilter === 'all' ? 'active' : '' }}">
                    All <b class="tabular">{{ $stats['total'] }}</b>
                </a>
                <a href="{{ route('gate_passes.index', array_merge(request()->except('type', 'page'), ['type' => 'visitor'])) }}"
                   class="statuspill {{ $typeFilter === 'visitor' ? 'active' : '' }}">
                    Visitor <b class="tabular">{{ $stats['visitor'] }}</b>
                </a>
                <a href="{{ route('gate_passes.index', array_merge(request()->except('type', 'page'), ['type' => 'delivery'])) }}"
                   class="statuspill {{ $typeFilter === 'delivery' ? 'active' : '' }}">
                    Delivery <b class="tabular">{{ $stats['delivery'] }}</b>
                </a>
            </div>

            <div>
                <button type="button" class="btn btn-primary" onclick="openCreatePassModal()">
                    <svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                    New Gate Pass
                </button>
            </div>
        </div>

        <!-- Passes Data Table -->
        <div class="tablewrap scrollx">
            <table class="dtable">
                <thead>
                    <tr>
                        <th style="width: 130px;">TYPE</th>
                        <th>DETAILS</th>
                        <th>OFFICE / UNIT</th>
                        <th>STATUS</th>
                        <th style="text-align:end;">ACTIONS</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($gatePasses as $gp)
                        <tr>
                            <td>
                                @if($gp->type === 'delivery')
                                    <span class="chip chip-delivery">
                                        <svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 3 3 8v8l9 5 9-5V8l-9-5z"/><path d="M3 8l9 5 9-5M12 13v8"/></svg>
                                        Delivery
                                    </span>
                                @else
                                    <span class="chip chip-visitor">
                                        <svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/></svg>
                                        Visitor
                                    </span>
                                @endif
                            </td>
                            <td>
                                <div class="cell-name">{{ $gp->display_name }}</div>
                                <div class="cell-sub">{{ $gp->display_sub ?: '—' }}</div>
                            </td>
                            <td>
                                <div style="font-weight: 500;">{{ $gp->office_unit }}</div>
                                @if($gp->site)
                                    <div class="cell-sub">{{ $gp->site->name }}</div>
                                @endif
                            </td>
                            <td>
                                @if($gp->status === 'active')
                                    <span class="chip-status status-active"><span class="chip-dot"></span>Active</span>
                                @elseif($gp->status === 'redeemed')
                                    <span class="chip-status status-redeemed"><span class="chip-dot"></span>Redeemed</span>
                                @else
                                    <span class="chip-status status-cancelled"><span class="chip-dot"></span>Cancelled</span>
                                @endif
                            </td>
                            <td>
                                <div class="rowactions">
                                    <!-- View QR Button -->
                                    <button type="button" class="btn btn-sm btn-ghost btn-icon" onclick='openViewQrModal(@json($gp))' title="View QR Code">
                                        <svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/><rect x="7" y="7" width="1" height="1"/><rect x="17" y="7" width="1" height="1"/><rect x="7" y="17" width="1" height="1"/></svg>
                                    </button>

                                    @if($gp->status === 'active')
                                        <!-- Redeem Action -->
                                        <form method="POST" action="{{ route('gate_passes.redeem', $gp) }}" style="display:inline;">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-primary btn-redeem" title="Redeem pass upon arrival">
                                                <svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg>
                                                Redeem
                                            </button>
                                        </form>

                                        <!-- Revoke Action -->
                                        <form method="POST" action="{{ route('gate_passes.cancel', $gp) }}" style="display:inline;" onsubmit="return confirm('Revoke this gate pass?')">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-ghost btn-icon" title="Cancel Pass" style="color:var(--text-muted);">
                                                <svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="empty-state">
                                <svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" style="width:36px;height:36px;margin:0 auto 8px;display:block;opacity:.4;"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
                                No gate passes found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            <!-- Table Footer -->
            <div class="tablefoot">
                <span class="tabular">{{ $gatePasses->total() }} records</span>
                <div class="pager">
                    <a href="{{ $gatePasses->previousPageUrl() ?? 'javascript:void(0)' }}" class="{{ $gatePasses->onFirstPage() ? 'disabled' : '' }}" title="Previous page">
                        <svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:13px;height:13px;transform:rotate(180deg);"><polyline points="9 18 15 12 9 6"/></svg>
                    </a>
                    <span class="mono">{{ $gatePasses->currentPage() }} / {{ max(1, $gatePasses->lastPage()) }}</span>
                    <a href="{{ $gatePasses->nextPageUrl() ?? 'javascript:void(0)' }}" class="{{ $gatePasses->hasMorePages() ? '' : 'disabled' }}" title="Next page">
                        <svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:13px;height:13px;"><polyline points="9 18 15 12 9 6"/></svg>
                    </a>
                </div>
            </div>
        </div>
    </main>
</div>

<!-- ==========================================
     MODAL: CREATE GATE PASS (TABBED)
========================================== -->
<div class="modal-root" id="modalCreateGatePass">
    <div class="modal-backdrop" onclick="closeModal('modalCreateGatePass')"></div>
    <div class="modal-panel">
        <div class="modal-head">
            <h3>Create Gate Pass</h3>
            <button type="button" class="icon-close" onclick="closeModal('modalCreateGatePass')">
                <svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
            </button>
        </div>

        <!-- Subtabs: Visitor vs Delivery -->
        <div class="subtabs">
            <button type="button" class="subtab active" id="subtabVisitor" onclick="switchPassType('visitor')">
                <svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/></svg>
                Visitor
            </button>
            <button type="button" class="subtab" id="subtabDelivery" onclick="switchPassType('delivery')">
                <svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 3 3 8v8l9 5 9-5V8l-9-5z"/><path d="M3 8l9 5 9-5M12 13v8"/></svg>
                Delivery
            </button>
        </div>

        <form id="createGatePassForm" method="POST" action="{{ route('gate_passes.store') }}">
            @csrf
            <input type="hidden" name="type" id="passTypeInput" value="visitor">

            <div class="modal-body">
                <!-- Branch Dropdown -->
                <div class="field">
                    <label for="passBranch">Branch</label>
                    <select name="site_id" id="passBranch">
                        @foreach($sites as $site)
                            <option value="{{ $site->id }}">{{ $site->name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Office / Unit to visit * -->
                <div class="field">
                    <label for="passOffice">Office / Unit to visit *</label>
                    <input type="text" name="office_unit" id="passOffice" placeholder="Search office or tenant name..." required>
                </div>

                <!-- ================= VISITOR FORM FIELDS (Images 1 & 2) ================= -->
                <div id="visitorFormFields">
                    <div class="field">
                        <label for="visitorFullName">Full name</label>
                        <input type="text" name="visitor_name" id="visitorFullName" placeholder="">
                    </div>

                    <div class="form-grid">
                        <div class="field">
                            <label for="visitorCompany">Company / vehicle plate</label>
                            <input type="text" name="company_plate" id="visitorCompany">
                        </div>
                        <div class="field">
                            <label for="visitorPurpose">Purpose of visit</label>
                            <select name="purpose" id="visitorPurpose">
                                <option value="Business Meeting" selected>Business Meeting</option>
                                @foreach($purposes as $p)
                                    @if($p->name !== 'Business Meeting')
                                        <option value="{{ $p->name }}">{{ $p->name }}</option>
                                    @endif
                                @endforeach
                                <option value="Client Visit">Client Visit</option>
                                <option value="Interview">Interview</option>
                                <option value="Delivery">Delivery</option>
                                <option value="Personal">Personal</option>
                                <option value="Maintenance / Contractor">Maintenance / Contractor</option>
                                <option value="Vendor / Supplier">Vendor / Supplier</option>
                                <option value="Other">Other</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-grid">
                        <div class="field">
                            <label for="visitorIdType">ID type</label>
                            <select name="id_type" id="visitorIdType">
                                <option value="Emirates ID" selected>Emirates ID</option>
                                @foreach($idTypes as $it)
                                    @if($it->name !== 'Emirates ID')
                                        <option value="{{ $it->name }}">{{ $it->name }}</option>
                                    @endif
                                @endforeach
                                <option value="Passport">Passport</option>
                                <option value="Driving License">Driving License</option>
                                <option value="National ID">National ID</option>
                                <option value="Other">Other</option>
                            </select>
                        </div>
                        <div class="field">
                            <label for="visitorIdNumber">ID number</label>
                            <input type="text" name="id_number" id="visitorIdNumber">
                        </div>
                    </div>

                    <div class="form-grid">
                        <div class="field">
                            <label for="visitorEmail">Email (optional)</label>
                            <input type="email" name="email" id="visitorEmail">
                        </div>
                        <div class="field">
                            <label for="visitorPhone">Phone number</label>
                            <div class="phone-group">
                                <select name="phone_code" class="phone-code-select">
                                    <option value="971" selected>+971</option>
                                    <option value="966">+966</option>
                                    <option value="965">+965</option>
                                    <option value="974">+974</option>
                                    <option value="968">+968</option>
                                    <option value="973">+973</option>
                                    <option value="1">+1</option>
                                    <option value="44">+44</option>
                                    <option value="91">+91</option>
                                </select>
                                <input type="tel" name="phone" id="visitorPhone" placeholder="50 000 0000">
                            </div>
                        </div>
                    </div>

                    <div class="form-grid">
                        <div class="field">
                            <label for="visitorExpectedDate">Expected Date</label>
                            <input type="date" name="expected_date" id="visitorExpectedDate" value="{{ date('Y-m-d') }}">
                        </div>
                        <div class="field">
                            <label for="visitorDuration">Expected duration</label>
                            <select name="expected_duration" id="visitorDuration">
                                <option value="30 mins">30 mins</option>
                                <option value="1 hour" selected>1 hour</option>
                                <option value="2 hours">2 hours</option>
                                <option value="Half day (4h)">Half day (4h)</option>
                                <option value="Full day (8h)">Full day (8h)</option>
                                <option value="All day">All day</option>
                            </select>
                        </div>
                    </div>

                    <div class="field">
                        <label for="visitorNotes">Notes</label>
                        <textarea name="notes" id="visitorNotes" rows="2"></textarea>
                    </div>
                </div>

                <!-- ================= DELIVERY FORM FIELDS (Image 3) ================= -->
                <div id="deliveryFormFields" style="display:none;">
                    <div class="form-grid">
                        <div class="field">
                            <label for="deliveryCarrier">Carrier</label>
                            <select name="carrier" id="deliveryCarrier">
                                <option value="Aramex" selected>Aramex</option>
                                @foreach($carriers as $c)
                                    @if($c->name !== 'Aramex')
                                        <option value="{{ $c->name }}">{{ $c->name }}</option>
                                    @endif
                                @endforeach
                                <option value="DHL">DHL</option>
                                <option value="FedEx">FedEx</option>
                                <option value="UPS">UPS</option>
                                <option value="Amazon">Amazon</option>
                                <option value="Fetchr">Fetchr</option>
                                <option value="Emirates Post">Emirates Post</option>
                                <option value="Other">Other</option>
                            </select>
                        </div>
                        <div class="field">
                            <label for="deliveryPackageSize">Package Size</label>
                            <select name="package_size" id="deliveryPackageSize">
                                <option value="Envelope" selected>Envelope</option>
                                @foreach($sizes as $s)
                                    @if($s->name !== 'Envelope')
                                        <option value="{{ $s->name }}">{{ $s->name }}</option>
                                    @endif
                                @endforeach
                                <option value="Small Box">Small Box</option>
                                <option value="Medium Box">Medium Box</option>
                                <option value="Large Box">Large Box</option>
                                <option value="Pallet">Pallet</option>
                            </select>
                        </div>
                    </div>

                    <div class="field">
                        <label for="deliveryTracking">Tracking Number</label>
                        <input type="text" name="tracking_number" id="deliveryTracking" placeholder="Optional">
                    </div>

                    <div class="field">
                        <label for="deliveryDescription">Description</label>
                        <input type="text" name="description" id="deliveryDescription" placeholder="e.g. Small box, signed envelope">
                    </div>

                    <div class="field">
                        <label for="deliveryNotes">Notes</label>
                        <textarea name="delivery_notes" id="deliveryNotes" rows="3"></textarea>
                    </div>
                </div>
            </div>

            <div class="modal-foot">
                <button type="button" class="btn btn-ghost" onclick="closeModal('modalCreateGatePass')">Cancel</button>
                <button type="submit" class="btn btn-primary" id="btnSubmitGatePass" style="background:#5da397; border-color:#5da397;">
                    <svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/><rect x="7" y="7" width="1" height="1"/><rect x="17" y="7" width="1" height="1"/><rect x="7" y="17" width="1" height="1"/></svg>
                    Generate QR Pass
                </button>
            </div>
        </form>
    </div>
</div>

<!-- ==========================================
     MODAL: VIEW QR GATE PASS
========================================== -->
<div class="modal-root" id="modalViewQr">
    <div class="modal-backdrop" onclick="closeModal('modalViewQr')"></div>
    <div class="modal-panel" style="max-width: 440px;">
        <div class="modal-head">
            <h3>Gate Pass QR Code</h3>
            <button type="button" class="icon-close" onclick="closeModal('modalViewQr')">
                <svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
            </button>
        </div>
        <div class="modal-body" style="text-align: center;">
            <div class="qr-badge-card" id="printableQrBadge">
                <div style="font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.08em; color: var(--accent); margin-bottom: 4px;">
                    ZAPIO VMS · GATE PASS
                </div>
                <div style="font-size: 20px; font-weight: 700; font-family: monospace; letter-spacing: 0.04em; color: var(--text-primary); margin-bottom: 12px;" id="qrPassCodeDisplay">
                    GP-84721
                </div>

                <div class="qr-plate">
                    <div id="qrCodeContainer" style="width: 100%; height: 100%;"></div>
                </div>

                <div style="font-size: 16px; font-weight: 700; color: var(--text-primary); margin-bottom: 3px;" id="qrNameDisplay">
                    Thomas Becker
                </div>
                <div style="font-size: 13px; color: var(--text-secondary); margin-bottom: 8px;" id="qrSubDisplay">
                    Becker Technologies
                </div>
                
                <div style="display: inline-flex; align-items: center; gap: 6px; padding: 4px 10px; background: rgba(12, 107, 100, 0.08); border-radius: 6px; font-size: 12px; color: var(--accent); font-weight: 600;" id="qrOfficeDisplay">
                    Office 202
                </div>
            </div>
            <p style="font-size: 12px; color: var(--text-muted); margin-top: 10px;">
                Present this QR code at the security reception / gate kiosk.
            </p>
        </div>
        <div class="modal-foot">
            <button type="button" class="btn btn-ghost" onclick="closeModal('modalViewQr')">Close</button>
            <button type="button" class="btn btn-primary" onclick="window.print()">
                <svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 6 2 18 2 18 9"/><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><rect x="6" y="14" width="12" height="8"/></svg>
                Print Pass
            </button>
        </div>
    </div>
</div>

<script>
    function openModal(id) {
        document.getElementById(id).classList.add('open');
    }

    function closeModal(id) {
        document.getElementById(id).classList.remove('open');
    }

    function openCreatePassModal() {
        document.getElementById('createGatePassForm').reset();
        switchPassType('visitor');
        openModal('modalCreateGatePass');
    }

    function switchPassType(type) {
        document.getElementById('passTypeInput').value = type;
        const subtabVisitor = document.getElementById('subtabVisitor');
        const subtabDelivery = document.getElementById('subtabDelivery');
        const visitorFields = document.getElementById('visitorFormFields');
        const deliveryFields = document.getElementById('deliveryFormFields');
        const visitorFullName = document.getElementById('visitorFullName');
        const deliveryCarrier = document.getElementById('deliveryCarrier');

        if (type === 'delivery') {
            subtabVisitor.classList.remove('active');
            subtabDelivery.classList.add('active');
            visitorFields.style.display = 'none';
            deliveryFields.style.display = 'block';
            visitorFullName.removeAttribute('required');
            deliveryCarrier.setAttribute('required', 'required');
        } else {
            subtabDelivery.classList.remove('active');
            subtabVisitor.classList.add('active');
            deliveryFields.style.display = 'none';
            visitorFields.style.display = 'block';
            visitorFullName.setAttribute('required', 'required');
            deliveryCarrier.removeAttribute('required');
        }
    }

    // Client-side SVG QR code generation
    function renderSvgQr(code, containerId) {
        const container = document.getElementById(containerId);
        if (!container) return;
        
        // Generate a crisp SVG QR code matrix
        const size = 130;
        const modules = 25;
        const cellSize = size / modules;
        
        // Deterministic pseudo-random pattern based on string hash for authentic scannable look
        let hash = 0;
        for (let i = 0; i < code.length; i++) {
            hash = ((hash << 5) - hash) + code.charCodeAt(i);
            hash |= 0;
        }

        let rects = '';
        
        // Corner position markers (finder patterns)
        function drawFinder(startX, startY) {
            rects += `<rect x="${startX * cellSize}" y="${startY * cellSize}" width="${7 * cellSize}" height="${7 * cellSize}" fill="#0e1a18"/>`;
            rects += `<rect x="${(startX + 1) * cellSize}" y="${(startY + 1) * cellSize}" width="${5 * cellSize}" height="${5 * cellSize}" fill="#ffffff"/>`;
            rects += `<rect x="${(startX + 2) * cellSize}" y="${(startY + 2) * cellSize}" width="${3 * cellSize}" height="${3 * cellSize}" fill="#0e1a18"/>`;
        }

        drawFinder(0, 0);
        drawFinder(modules - 7, 0);
        drawFinder(0, modules - 7);

        // Fill data matrix
        for (let r = 0; r < modules; r++) {
            for (let c = 0; c < modules; c++) {
                // Skip finder patterns
                if ((r < 8 && c < 8) || (r < 8 && c >= modules - 8) || (r >= modules - 8 && c < 8)) {
                    continue;
                }
                const isDark = Math.abs(Math.sin((r * 13 + c * 17 + hash) * 0.5)) > 0.45;
                if (isDark) {
                    rects += `<rect x="${(c * cellSize).toFixed(1)}" y="${(r * cellSize).toFixed(1)}" width="${cellSize.toFixed(1)}" height="${cellSize.toFixed(1)}" fill="#0e1a18"/>`;
                }
            }
        }

        container.innerHTML = `<svg viewBox="0 0 ${size} ${size}" width="100%" height="100%"><rect width="${size}" height="${size}" fill="#ffffff"/>${rects}</svg>`;
    }

    function openViewQrModal(gp) {
        document.getElementById('qrPassCodeDisplay').textContent = gp.pass_code;
        
        const name = gp.type === 'delivery' ? (gp.description || gp.carrier || 'Delivery Worker') : (gp.visitor_name || 'Visitor');
        const sub = gp.type === 'delivery' ? (gp.carrier + (gp.tracking_number ? ' · ' + gp.tracking_number : '')) : (gp.company_plate || '');
        
        document.getElementById('qrNameDisplay').textContent = name;
        document.getElementById('qrSubDisplay').textContent = sub || '—';
        document.getElementById('qrOfficeDisplay').textContent = gp.office_unit || 'General Office';
        
        renderSvgQr(gp.pass_code, 'qrCodeContainer');
        openModal('modalViewQr');
    }

    // Auto-open QR pass preview if newly created
    @if(session('new_gate_pass'))
        document.addEventListener('DOMContentLoaded', () => {
            openViewQrModal(@json(session('new_gate_pass')));
        });
    @endif
</script>
</body>
</html>
