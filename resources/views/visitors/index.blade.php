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

        .filterbar { display: flex; gap: 10px; align-items: center; margin-bottom: 16px; flex-wrap: wrap; }
        .searchbox { flex: 1; max-width: 280px; background: var(--surface); border: 1px solid var(--border); border-radius: 99px; padding: 7px 12px; }
        .searchbox input { border: none; background: none; outline: none; width: 100%; font-size: 13px; font-family: inherit; }

        .tablewrap { border: 1px solid var(--border); border-radius: var(--radius); overflow: hidden; background: var(--surface); }
        table.dtable { width: 100%; border-collapse: collapse; }
        .dtable thead th { text-align: start; font-size: 11px; text-transform: uppercase; letter-spacing: .05em; color: var(--text-muted); padding: 11px 14px; border-bottom: 1px solid var(--border); background: var(--surface-2); }
        .dtable tbody td { padding: 12px 14px; border-bottom: 1px solid var(--border); font-size: 13px; vertical-align: middle; }
        .cell-name { font-weight: 600; }
        .cell-sub { font-size: 11.5px; color: var(--text-muted); margin-top: 1px; }

        .chip { display: inline-flex; align-items: center; padding: 3px 9px; border-radius: 99px; font-size: 11.5px; font-weight: 600; }
        .chip-good { background: rgba(12, 163, 12, 0.14); color: var(--good); }
        .chip-muted { background: var(--surface-2); color: var(--text-muted); }
        .chip-critical { background: rgba(208, 59, 59, 0.14); color: var(--critical); }

        .alert-success { background: rgba(12, 163, 12, 0.14); border: 1px solid rgba(12, 163, 12, 0.30); color: var(--good); padding: 12px 16px; border-radius: var(--radius-sm); font-size: 13.5px; margin-bottom: 18px; display: flex; align-items: center; justify-content: space-between; }
    </style>
</head>
<body>
    <div id="app">
        <aside id="sidebar">
            <div class="brand">
                <div class="brand-mark">M</div>
                <div>
                    <div class="brand-name">Madkhal</div>
                    <div class="brand-sub">{{ Auth::user()->organization->name ?? 'Visitor Management' }}</div>
                </div>
            </div>
            <nav class="navlist">
                <div class="navlabel">Operations</div>
                <a href="{{ route('dashboard') }}" class="navitem {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    <svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75"><path d="M3 11.5 12 4l9 7.5"/><path d="M5.5 10v9a1 1 0 0 0 1 1H10v-6h4v6h3.5a1 1 0 0 0 1-1v-9"/></svg>
                    <span>Dashboard</span>
                </a>
                <a href="{{ route('visitors.index') }}" class="navitem {{ request()->routeIs('visitors.index') ? 'active' : '' }}">
                    <svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75"><circle cx="9" cy="8" r="3"/><path d="M2.5 20c0-3.5 3-6 6.5-6s6.5 2.5 6.5 6"/><circle cx="17" cy="9" r="2.5"/><path d="M15.5 14.2c2.6.4 4.5 2.4 4.5 5.3"/></svg>
                    <span>Visitor Log</span>
                </a>
                <div class="navlabel">Manage</div>
                <a href="{{ route('sites.index') }}" class="navitem {{ request()->routeIs('sites.index') ? 'active' : '' }}">
                    <svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75"><path d="M3 21h18M5 21V9l7-5 7 5v12M9 21v-5h6v5"/></svg>
                    <span>Sites & Locations</span>
                </a>
                <a href="{{ route('departments.index') }}" class="navitem {{ request()->routeIs('departments.index') ? 'active' : '' }}">
                    <svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75"><rect x="3" y="7" width="18" height="12.5" rx="2"/><path d="M8.5 7V5.5A1.5 1.5 0 0 1 10 4h4a1.5 1.5 0 0 1 1.5 1.5V7"/></svg>
                    <span>Departments</span>
                </a>
                <a href="{{ route('employees.index') }}" class="navitem {{ request()->routeIs('employees.index') ? 'active' : '' }}">
                    <svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75"><circle cx="9" cy="8" r="3.2"/><path d="M2.5 20c0-3.6 3-6.3 6.5-6.3S15.5 16.4 15.5 20"/></svg>
                    <span>Employees</span>
                </a>
                <a href="{{ route('lookups.index') }}" class="navitem {{ request()->routeIs('lookups.index') ? 'active' : '' }}">
                    <svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75"><circle cx="12" cy="12" r="3"/><circle cx="12" cy="12" r="7.8"/></svg>
                    <span>Lookup Settings</span>
                </a>
            </nav>
            <div style="padding: 12px; border-top: 1px solid var(--border);">
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="navitem">
                        <svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75"><path d="M9 21H5.5A1.5 1.5 0 0 1 4 19.5v-15A1.5 1.5 0 0 1 5.5 3H9M15 16l5-4-5-4M20 12H9"/></svg>
                        <span>Sign Out</span>
                    </button>
                </form>
            </div>
        </aside>

        <header id="topbar">
            <span style="font-weight:700;font-size:15px;">Visitor Log</span>
            <div style="flex:1;"></div>
            <div style="text-align:end;">
                <div style="font-weight:700;font-size:13px;">{{ Auth::user()->name }}</div>
                <div style="font-size:11px;color:var(--text-muted);">{{ Auth::user()->organization->name ?? '' }}</div>
            </div>
        </header>

        <main id="main">
            <div class="pagehead">
                <div>
                    <h1>Visitor Log</h1>
                    <div class="sub">Every visitor check-in across your site locations.</div>
                </div>
                <a href="{{ route('visitors.checkin') }}" class="btn btn-primary">+ New Check-In</a>
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

            <form action="{{ route('visitors.index') }}" method="GET" class="filterbar">
                <div class="searchbox">
                    <input type="text" name="search" placeholder="Search visitor, host, company..." value="{{ request('search') }}">
                </div>
                <select name="status" onchange="this.form.submit()" style="padding:7px 10px;border-radius:6px;border:1px solid var(--border);font-family:inherit;">
                    <option value="all">All Statuses</option>
                    <option value="checked_in" {{ request('status') == 'checked_in' ? 'selected' : '' }}>Checked In</option>
                    <option value="checked_out" {{ request('status') == 'checked_out' ? 'selected' : '' }}>Checked Out</option>
                </select>
                <select name="site_id" onchange="this.form.submit()" style="padding:7px 10px;border-radius:6px;border:1px solid var(--border);font-family:inherit;">
                    <option value="all">All Sites</option>
                    @foreach ($sites as $site)
                        <option value="{{ $site->id }}" {{ request('site_id') == $site->id ? 'selected' : '' }}>{{ $site->name }}</option>
                    @endforeach
                </select>
                <select name="purpose_id" onchange="this.form.submit()" style="padding:7px 10px;border-radius:6px;border:1px solid var(--border);font-family:inherit;">
                    <option value="all">All Purposes</option>
                    @foreach ($purposes as $purpose)
                        <option value="{{ $purpose->id }}" {{ request('purpose_id') == $purpose->id ? 'selected' : '' }}>{{ $purpose->name }}</option>
                    @endforeach
                </select>
            </form>

            <div class="tablewrap scrollx">
                <table class="dtable">
                    <thead>
                        <tr>
                            <th>Visitor</th>
                            <th>Company</th>
                            <th>Host Employee</th>
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
                                    {{ $visit->check_in_at ? $visit->check_in_at->format('M d, g:i A') : '—' }}
                                </td>
                                <td>
                                    @if ($visit->status === 'checked_in')
                                        <span class="chip chip-good">Checked In</span>
                                    @else
                                        <span class="chip chip-muted">Checked Out</span>
                                    @endif
                                </td>
                                <td style="text-align:end;">
                                    @if ($visit->status === 'checked_in')
                                        <form action="{{ route('visits.checkout', $visit->id) }}" method="POST" style="display:inline;">
                                            @csrf
                                            <button type="submit" class="btn" style="padding:4px 8px;font-size:12px;">Check Out</button>
                                        </form>
                                    @else
                                        <span style="font-size:12px;color:var(--text-muted);">{{ $visit->check_out_at ? $visit->check_out_at->format('g:i A') : 'Out' }}</span>
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
</body>
</html>
