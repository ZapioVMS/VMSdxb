<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Madkhal — {{ Auth::user()->organization->name ?? 'Zapio VMS' }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=IBM+Plex+Sans:wght@400;500;600;700&family=IBM+Plex+Sans+Arabic:wght@400;500;600;700&display=swap">
    <style>
        :root{
          color-scheme: light;
          --accent:#0c6b64; --accent-2:#0f7a72; --accent-fg:#ffffff; --accent-soft:rgba(12,107,100,.10); --accent-soft-2:rgba(12,107,100,.18);
          --bg:#f4f7f6; --surface:#ffffff; --surface-2:#eef2f1; --surface-3:#e6ebe9;
          --text-primary:#0e1a18; --text-secondary:#4d5b57; --text-muted:#7c8985;
          --border:rgba(14,26,24,.10); --border-strong:rgba(14,26,24,.18);
          --chart-surface:#fcfcfb; --grid:#e1e0d9; --axis:#c3c2b7;
          --good:#0ca30c; --warning:#fab219; --serious:#ec835a; --critical:#d03b3b;
          --s1:#2a78d6; --s2:#eb6834; --s3:#1baf7a; --s4:#eda100; --s5:#e87ba4; --s6:#008300; --s7:#4a3aa7; --s8:#e34948;
          --shadow: 0 1px 2px rgba(14,26,24,.06), 0 8px 24px -12px rgba(14,26,24,.18);
          --radius: 10px; --radius-sm:6px; --radius-lg:14px;
          --font-ui:'IBM Plex Sans','IBM Plex Sans Arabic', system-ui, -apple-system, "Segoe UI", sans-serif;
        }
        *,*::before,*::after{box-sizing:border-box;}
        html,body{height:100%;}
        body{
          margin:0; background:var(--bg); color:var(--text-primary);
          font-family:var(--font-ui); font-size:14px; line-height:1.5;
          -webkit-font-smoothing:antialiased;
        }
        h1,h2,h3,h4{ margin:0; text-wrap:balance; font-weight:600; }
        p{margin:0;}
        button,input,select,textarea,optgroup{ font-family:inherit; }
        a{ color:inherit; }
        ::selection{ background:var(--accent-soft-2); }
        :focus-visible{ outline:2px solid var(--accent); outline-offset:2px; border-radius:4px; }

        .ic{ width:16px; height:16px; flex:none; display:inline-block; vertical-align:-3px; }
        .mono{ font-variant-numeric:tabular-nums; }
        .tabular{ font-variant-numeric:tabular-nums; }
        .hidden{ display:none !important; }
        .scrollx{ overflow-x:auto; }

        #app{ display:grid; grid-template-columns: 248px 1fr; grid-template-rows: 60px 1fr; min-height:100vh; }
        #sidebar{ grid-row:1/3; grid-column:1; background:var(--surface); border-inline-end:1px solid var(--border); display:flex; flex-direction:column; }
        #topbar{ grid-row:1; grid-column:2; background:var(--surface); border-block-end:1px solid var(--border); display:flex; align-items:center; gap:14px; padding-inline:20px; min-width:0; }
        #main{ grid-row:2; grid-column:2; padding:24px 28px 60px; overflow-y:auto; min-width:0; }
        @media (max-width: 860px){
          #app{ grid-template-columns: 0 1fr; }
          #sidebar{ position:fixed; inset-block:0; inset-inline-start:0; width:248px; z-index:40; transform:translateX(-100%); transition:transform .18s ease; box-shadow:var(--shadow); }
          #sidebar.open{ transform:translateX(0); }
          #main{ padding:18px 16px 60px; }
        }
        .brand{ display:flex; align-items:center; gap:10px; padding:16px 18px; border-block-end:1px solid var(--border); }
        .brand-mark{ width:32px; height:32px; border-radius:99px; background:linear-gradient(155deg, var(--accent-2), var(--accent)); display:flex; align-items:center; justify-content:center; color:var(--accent-fg); font-weight:700; font-size:15px; flex:none; }
        .brand-name{ font-weight:700; font-size:16.5px; letter-spacing:.1px; }
        .brand-sub{ font-size:11px; color:var(--text-muted); margin-top:1px; }

        nav.navlist{ padding:10px; display:flex; flex-direction:column; gap:2px; overflow-y:auto; flex:1; }
        .navlabel{ font-size:10.5px; text-transform:uppercase; letter-spacing:.06em; color:var(--text-muted); padding:14px 10px 6px; }
        .navitem{ display:flex; align-items:center; gap:11px; padding:9px 10px; border-radius:var(--radius-sm); color:var(--text-secondary); cursor:pointer; font-size:13.5px; font-weight:500; border:none; background:none; width:100%; text-align:start; text-decoration:none; }
        .navitem:hover{ background:var(--surface-2); color:var(--text-primary); }
        .navitem.active{ background:var(--accent-soft); color:var(--accent); }
        .navitem .ic{ width:18px; height:18px; flex:none; }
        .sidebar-foot{ padding:12px; border-block-start:1px solid var(--border); }

        .icon-btn{ width:34px; height:34px; border-radius:var(--radius-sm); border:1px solid transparent; background:none; color:var(--text-secondary); display:flex; align-items:center; justify-content:center; cursor:pointer; flex:none; position:relative; }
        .icon-btn:hover{ background:var(--surface-2); color:var(--text-primary); }
        .avatar{ width:32px; height:32px; border-radius:99px; background:var(--surface-3); color:var(--text-primary); display:flex; align-items:center; justify-content:center; font-size:12px; font-weight:700; flex:none; }
        .btn{ display:inline-flex; align-items:center; gap:7px; padding:8px 14px; border-radius:var(--radius-sm); border:1px solid var(--border); background:var(--surface); color:var(--text-primary); font-size:13px; font-weight:600; cursor:pointer; white-space:nowrap; text-decoration:none; }
        .btn-primary{ background:var(--accent); border-color:var(--accent); color:var(--accent-fg); }
        .btn-primary:hover{ background:var(--accent); filter:brightness(1.06); }
        .card{ background:var(--surface); border:1px solid var(--border); border-radius:var(--radius-lg); padding:18px; }
        .grid{ display:grid; gap:14px; }
        .grid-4{ grid-template-columns:repeat(4,minmax(0,1fr)); }
        .grid-2{ grid-template-columns:minmax(0,1.3fr) minmax(0,1fr); }
        .stat-tile{ background:var(--surface); border:1px solid var(--border); border-radius:var(--radius-lg); padding:16px 18px; display:flex; flex-direction:column; gap:8px; }
        .stat-label{ font-size:12px; color:var(--text-secondary); font-weight:500; }
        .stat-value{ font-size:26px; font-weight:700; }
        .chip{ display:inline-flex; align-items:center; gap:5px; padding:3px 9px; border-radius:99px; font-size:11.5px; font-weight:600; }
        .chip-good{ background:color-mix(in srgb, var(--good) 14%, transparent); color:var(--good); }
        .hstack{ display:flex; align-items:center; gap:10px; }
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
                <a href="{{ route('visitors.checkin') }}" class="navitem {{ request()->routeIs('visitors.checkin') ? 'active' : '' }}">
                    <svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75"><circle cx="10" cy="8" r="3.2"/><path d="M3 20c0-3.6 3.1-6.3 7-6.3s7 2.7 7 6.3"/><path d="M19 8v5M16.5 10.5h5"/></svg>
                    <span>Check-In</span>
                </a>
            </nav>
            <div class="sidebar-foot">
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
            <div class="hstack" style="gap:10px;">
                <span class="chip chip-good">Local Server Active</span>
                <span style="font-weight:600;font-size:13px;">{{ Auth::user()->organization->name ?? 'Meridian Business Solutions LLC' }}</span>
            </div>
            <div style="flex:1;"></div>
            <div class="hstack">
                <div style="text-align:end;">
                    <div style="font-weight:700;font-size:13px;">{{ Auth::user()->name }}</div>
                    <div style="font-size:11px;color:var(--text-muted);">{{ Auth::user()->email }}</div>
                </div>
                <div class="avatar">{{ strtoupper(substr(Auth::user()->name, 0, 2)) }}</div>
            </div>
        </header>

        <main id="main">
            <div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:20px;">
                <div>
                    <h1>Dashboard</h1>
                    <div style="color:var(--text-secondary);font-size:13px;margin-top:3px;">
                        Live overview of visitor activity for {{ Auth::user()->organization->name ?? 'Meridian Business Solutions LLC' }}.
                    </div>
                </div>
                <a href="{{ route('visitors.checkin') }}" class="btn btn-primary">+ New Check-In</a>
            </div>

            <div class="grid grid-4" style="margin-bottom:16px;">
                <div class="stat-tile">
                    <div class="stat-label">Visitors Today</div>
                    <div class="stat-value">12</div>
                    <div style="font-size:11.5px;color:var(--good);font-weight:600;">+3 vs yesterday</div>
                </div>
                <div class="stat-tile">
                    <div class="stat-label">Currently On-Site</div>
                    <div class="stat-value">5</div>
                    <div style="font-size:11.5px;color:var(--text-muted);">Across 2 active sites</div>
                </div>
                <div class="stat-tile">
                    <div class="stat-label">Pending Approvals</div>
                    <div class="stat-value">2</div>
                    <div style="font-size:11.5px;color:var(--warning);font-weight:600;">Need review</div>
                </div>
                <div class="stat-tile">
                    <div class="stat-label">Watchlist Matches</div>
                    <div class="stat-value" style="color:var(--good);">0</div>
                    <div style="font-size:11.5px;color:var(--text-muted);">All clear</div>
                </div>
            </div>

            <div class="card">
                <div style="font-weight:700;font-size:15px;margin-bottom:6px;">Authenticated Session Verified</div>
                <p style="font-size:13px;color:var(--text-secondary);line-height:1.6;">
                    You are logged in as <strong>{{ Auth::user()->name }}</strong> (<code>{{ Auth::user()->email }}</code>) associated with <strong>{{ Auth::user()->organization->name ?? 'Tenant Organization' }}</strong> (ID: <code>{{ Auth::user()->organization_id }}</code>). Multi-tenant <code>TenantScope</code> query boundaries are automatically active for this session.
                </p>
            </div>
        </main>
    </div>
</body>
</html>
