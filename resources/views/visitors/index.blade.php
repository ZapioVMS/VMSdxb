<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Visitor Log — Madkhal</title>
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

        .tablewrap { border: 1px solid var(--border); border-radius: var(--radius); overflow: hidden; background: var(--surface); }
        table.dtable { width: 100%; border-collapse: collapse; }
        .dtable thead th { text-align: start; font-size: 11px; text-transform: uppercase; letter-spacing: .05em; color: var(--text-muted); padding: 11px 14px; border-bottom: 1px solid var(--border); background: var(--surface-2); }
        .dtable tbody td { padding: 12px 14px; border-bottom: 1px solid var(--border); font-size: 13px; vertical-align: middle; }
        .cell-name { font-weight: 600; }
        .cell-sub { font-size: 11.5px; color: var(--text-muted); margin-top: 1px; }

        .chip { display: inline-flex; align-items: center; padding: 3px 9px; border-radius: 99px; font-size: 11.5px; font-weight: 600; }
        .chip-good { background: rgba(12, 163, 12, 0.14); color: var(--good); }
        .chip-info { background: rgba(12, 107, 100, 0.14); color: var(--accent); }
        .chip-muted { background: var(--surface-2); color: var(--text-muted); }
        .chip-critical { background: rgba(208, 59, 59, 0.14); color: var(--critical); }

        .alert-success { background: rgba(12, 163, 12, 0.14); border: 1px solid rgba(12, 163, 12, 0.30); color: var(--good); padding: 12px 16px; border-radius: var(--radius-sm); font-size: 13.5px; margin-bottom: 18px; display: flex; align-items: center; justify-content: space-between; }
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

            <form action="{{ route('visitors.index') }}" method="GET" id="filterForm">
                <input type="hidden" name="status" id="statusFilterInput" value="{{ request('status', 'all') }}">

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
                        <option value="all">All Sites</option>
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
                        <option value="all" {{ request('date_range') == 'all' ? 'selected' : '' }}>All Time</option>
                        <option value="today" {{ request('date_range') == 'today' ? 'selected' : '' }}>Today</option>
                        <option value="last_7_days" {{ request('date_range', 'last_7_days') == 'last_7_days' ? 'selected' : '' }}>Last 7 days</option>
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
                            <th>Site</th>
                            <th>Check-In</th>
                            <th>Status</th>
                            <th style="text-align:end;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($visits as $visit)
                            <tr>
                                <td>
                                    <div class="cell-name">{{ $visit->visitor->full_name ?? 'Visitor' }}</div>
                                    <div class="cell-sub">{{ $visit->visitor->phone ?? '' }}</div>
                                </td>
                                <td>{{ $visit->visitor->company_name ?? '—' }}</td>
                                <td>
                                    <div>{{ $visit->hostEmployee->full_name ?? '—' }}</div>
                                    <div class="cell-sub">{{ $visit->department->name ?? '' }}</div>
                                </td>
                                <td>{{ $visit->purpose->name ?? '—' }}</td>
                                <td>{{ $visit->site->name ?? '—' }}</td>
                                <td class="mono">
                                    {{ $visit->check_in_at ? $visit->check_in_at->format('d M, h:i A') : ($visit->scheduled_start_at ? $visit->scheduled_start_at->format('d M, h:i A') : '—') }}
                                </td>
                                <td>
                                    @if ($visit->status === 'checked_in')
                                        <span class="chip chip-good">Checked in</span>
                                    @elseif ($visit->status === 'expected')
                                        <span class="chip chip-info">Expected</span>
                                    @elseif ($visit->status === 'overstayed')
                                        <span class="chip chip-critical">Overstayed</span>
                                    @elseif ($visit->status === 'checked_out')
                                        <span class="chip chip-muted">Checked out</span>
                                    @else
                                        <span class="chip chip-muted">{{ ucfirst($visit->status) }}</span>
                                    @endif
                                </td>
                                <td style="text-align:end;">
                                    @if ($visit->status === 'checked_in')
                                        <form action="{{ route('visits.checkout', $visit->id) }}" method="POST" style="display:inline;">
                                            @csrf
                                            <button type="submit" class="btn" style="padding:4px 8px;font-size:12px;">Check Out</button>
                                        </form>
                                    @else
                                        <span style="font-size:12px;color:var(--text-muted);">{{ $visit->check_out_at ? $visit->check_out_at->format('g:i A') : '—' }}</span>
                                    @endif
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
            </div>

            <div style="margin-top:16px;">
                {{ $visits->links() }}
            </div>
        </main>
    </div>

    <script>
        function setStatusFilter(val) {
            document.getElementById('statusFilterInput').value = val;
            document.getElementById('filterForm').submit();
        }
    </script>
</body>
</html>
