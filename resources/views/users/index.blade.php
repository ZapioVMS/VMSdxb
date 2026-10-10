<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Users & Roles — Zapio VMS</title>
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
            --s7: #4a3aa7;
            --radius: 12px;
            --radius-sm: 8px;
            --shadow: 0 1px 2px rgba(14,26,24,.06), 0 8px 24px -12px rgba(14,26,24,.18);
        }
        *, *::before, *::after { box-sizing: border-box; }
        body { margin: 0; font-family: 'IBM Plex Sans', system-ui, -apple-system, sans-serif; background: var(--bg); color: var(--text-primary); font-size: 14px; }
        .ic { width: 16px; height: 16px; flex: none; display: inline-block; vertical-align: -3px; }
        .mono { font-variant-numeric: tabular-nums; font-family: inherit; }
        
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
        .btn-ghost { border-color: transparent; background: transparent; }
        .btn-ghost:hover { background: var(--surface-2); }
        .btn-sm { padding: 5px 10px; font-size: 12px; }

        .subtabs { display: flex; gap: 22px; border-block-end: 1px solid var(--border); margin-bottom: 18px; }
        .subtab { background: none; border: none; font: inherit; font-size: 13.5px; font-weight: 600; color: var(--text-muted); padding: 9px 1px 12px; cursor: pointer; border-block-end: 2px solid transparent; margin-block-end: -1px; text-decoration: none; }
        .subtab:hover { color: var(--text-primary); }
        .subtab.active { color: var(--accent); border-block-end-color: var(--accent); }

        .card { background: var(--surface); border: 1px solid var(--border); border-radius: var(--radius); padding: 18px; margin-bottom: 16px; }
        .tablewrap { border: 1px solid var(--border); border-radius: var(--radius); overflow: hidden; background: var(--surface); }
        .tablewrap.scrollx { overflow-x: auto; }
        table.dtable { width: 100%; border-collapse: collapse; min-width: 720px; }
        .dtable thead th { text-align: start; font-size: 11px; text-transform: uppercase; letter-spacing: .05em; color: var(--text-muted); font-weight: 600; padding: 11px 14px; border-block-end: 1px solid var(--border); background: var(--surface-2); white-space: nowrap; }
        .dtable tbody td { padding: 11px 14px; border-block-end: 1px solid var(--border); font-size: 13px; vertical-align: middle; }
        .dtable tbody tr:last-child td { border-block-end: none; }
        .dtable tbody tr:hover { background: var(--surface-2); }
        .cell-name { font-weight: 600; }
        .cell-sub { font-size: 11.5px; color: var(--text-muted); margin-top: 1px; }

        .rowactions { display: inline-flex; align-items: center; gap: 5px; justify-content: flex-end; }
        .icon-btn { display: inline-flex; align-items: center; justify-content: center; width: 30px; height: 30px; border-radius: var(--radius-sm); border: 1px solid var(--border); background: var(--surface); color: var(--text-secondary); cursor: pointer; transition: all .15s ease; padding: 0; line-height: 1; }
        .icon-btn:hover { background: var(--surface-2); color: var(--text-primary); border-color: var(--accent); }
        .icon-btn.icon-btn-danger:hover { background: rgba(208, 59, 59, 0.1); border-color: var(--critical); color: var(--critical); }

        .chip { display: inline-flex; align-items: center; gap: 5px; padding: 3px 9px; border-radius: 99px; font-size: 11.5px; font-weight: 600; white-space: nowrap; }
        .chip-good { background: color-mix(in srgb, var(--good) 14%, transparent); color: var(--good); }
        .chip-muted { background: var(--surface-3); color: var(--text-secondary); }
        .chip-warning { background: color-mix(in srgb, var(--warning) 18%, transparent); color: #8a5a00; }

        .modal-root { display: none; position: fixed; inset: 0; z-index: 100; }
        .modal-root.open { display: block; }
        .modal-backdrop { position: absolute; inset: 0; background: rgba(6,12,11,.5); backdrop-filter: blur(2px); }
        .modal-panel { position: relative; max-width: 580px; width: calc(100% - 32px); margin: 6vh auto 0; background: var(--surface); border-radius: var(--radius); border: 1px solid var(--border); box-shadow: var(--shadow); overflow: hidden; }
        .modal-head { display: flex; align-items: center; justify-content: space-between; padding: 16px 20px; border-bottom: 1px solid var(--border); }
        .modal-head h3 { margin: 0; font-size: 16px; font-weight: 700; }
        .modal-body { padding: 20px; max-height: 75vh; overflow-y: auto; }
        .modal-foot { display: flex; align-items: center; justify-content: flex-end; gap: 8px; padding: 14px 20px; border-top: 1px solid var(--border); background: var(--surface-2); }
        
        .form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; }
        .field { display: flex; flex-direction: column; gap: 5px; margin-bottom: 14px; }
        .field label { font-size: 12.5px; font-weight: 600; color: var(--text-secondary); }
        .field input, .field select, .field textarea { font-family: inherit; font-size: 13.5px; padding: 9px 11px; border-radius: var(--radius-sm); border: 1px solid var(--border); background: var(--surface); color: var(--text-primary); width: 100%; box-sizing: border-box; }
        
        /* Matrix Styles */
        .matrix-table { width: 100%; border-collapse: collapse; font-size: 12.5px; }
        .matrix-table th { position: sticky; top: 0; background: var(--surface); z-index: 10; border-bottom: 2px solid var(--border); padding: 10px; text-align: left; }
        .matrix-table td { padding: 10px; border-bottom: 1px solid var(--border); vertical-align: middle; }
        .matrix-table td.module-row { background: var(--surface-2); font-weight: 600; text-transform: uppercase; font-size: 11px; letter-spacing: 0.05em; padding: 8px 10px; }
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
            /* Toggle Switch CSS */
        .switch { display: inline-flex; align-items: center; cursor: pointer; user-select: none; margin-left: auto; }
        .switch input { position: absolute; opacity: 0; width: 0; height: 0; }
        .track { width: 36px; height: 20px; background-color: var(--surface-3); border-radius: 20px; position: relative; transition: background-color 0.2s; display: inline-block; }
        .thumb { width: 16px; height: 16px; background-color: #fff; border-radius: 50%; position: absolute; top: 2px; left: 2px; transition: transform 0.2s; box-shadow: 0 1px 3px rgba(0,0,0,0.2); }
        .switch input:checked + .track { background-color: var(--accent); }
        .switch input:checked + .track .thumb { transform: translateX(16px); }
</style>
</head>
<body>
    <div id="app">
        @include('partials.sidebar', ['active' => 'users'])
        @include('partials.topbar')

        <main id="main">
                        <div class="pagehead" style="align-items: center; justify-content: space-between; display: flex; margin-bottom: 24px;">
                <div>
                    <h1>Users & Permissions</h1>
                    <div class="sub">Manage who can access Zapio VMS and what they can do.</div>
                </div>
                <div class="pagehead-actions" style="display: flex; gap: 12px;">
                    @if ($activeTab === 'users')
                        <button type="button" class="btn">
                            <svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4M7 10l5 5 5-5M12 15V3"/></svg>
                            <span>Export</span>
                        </button>
                        <button type="button" class="btn btn-primary" onclick="openUserModal()">
                            <svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2M8.5 7a4 4 0 1 1 8 0 4 4 0 0 1-8 0M20 8v6M23 11h-6"/></svg>
                            <span>Invite User</span>
                        </button>
                    @elseif ($activeTab === 'roles')
                        <button type="button" class="btn btn-primary" onclick="openRoleModal()">
                            <svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 5v14M5 12h14"/></svg>
                            <span>Create Role</span>
                        </button>
                    @endif
                </div>
            </div>

            @if (session('success'))
                <div class="card" style="background: rgba(12, 163, 12, 0.12); border-color: rgba(12, 163, 12, 0.3); color: var(--good); padding: 12px 16px; margin-bottom: 16px; font-weight: 500;">
                    ✓ {{ session('success') }}
                </div>
            @endif
            @if($errors->any())
                <div class="card" style="background: rgba(208, 59, 59, 0.10); border-color: rgba(208, 59, 59, 0.3); color: var(--critical); padding: 12px 16px; margin-bottom: 16px; font-weight: 500;">
                    <ul style="margin:0; padding-left: 20px;">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

                        <div class="subtabs">
                <a href="{{ route('users.index', ['tab' => 'users']) }}" class="subtab {{ $activeTab === 'users' ? 'active' : '' }}">Users</a>
                <a href="{{ route('users.index', ['tab' => 'roles']) }}" class="subtab {{ $activeTab === 'roles' ? 'active' : '' }}">Roles</a>
                <a href="{{ route('users.index', ['tab' => 'matrix']) }}" class="subtab {{ $activeTab === 'matrix' ? 'active' : '' }}">Permission Matrix</a>
                <a href="{{ route('users.index', ['tab' => 'activity']) }}" class="subtab {{ $activeTab === 'activity' ? 'active' : '' }}">Activity Log</a>
            </div>

            <!-- TAB 1: USERS -->
            @if ($activeTab === 'users')
            
            <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom: 16px; margin-top: 24px;">
                <div style="display:flex; align-items:center; gap:12px;">
                    <h3 style="margin:0; font-size:16px; font-weight:700;">All Users</h3>
                    <span class="chip" style="background:#e6f4ea; color:#1e8e3e; font-weight:600; padding: 4px 10px; border-radius: 20px;">{{ $users->count() }} users</span>
                </div>
                <div class="searchbox" style="width:280px; max-width:100%; position: relative;">
                    <svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="position: absolute; left: 10px; top: 10px; width: 16px; height: 16px; color: #9aa0a6;"><circle cx="11" cy="11" r="8"/><path d="M21 21l-4.35-4.35"/></svg>
                    <input type="text" placeholder="Search users..." style="width: 100%; padding: 8px 10px 8px 34px; border: 1px solid var(--border); border-radius: 20px; font-size: 13.5px; background: #f8f9fa;">
                </div>
            </div>

            <div class="tablewrap scrollx" style="border: none; border-top: 1px solid var(--border); border-radius: 0;">
                <table class="dtable" style="font-size: 13.5px; width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr style="background:#f8f9fa;">
                            <th style="font-weight:600; color:var(--text-muted); font-size:11px; letter-spacing:0.05em; padding: 12px 16px; text-align: left; border-bottom: 1px solid var(--border);">USER</th>
                            <th style="font-weight:600; color:var(--text-muted); font-size:11px; letter-spacing:0.05em; padding: 12px 16px; text-align: left; border-bottom: 1px solid var(--border);">ROLE</th>
                            <th style="font-weight:600; color:var(--text-muted); font-size:11px; letter-spacing:0.05em; padding: 12px 16px; text-align: left; border-bottom: 1px solid var(--border);">STATUS</th>
                            <th style="font-weight:600; color:var(--text-muted); font-size:11px; letter-spacing:0.05em; padding: 12px 16px; text-align: left; border-bottom: 1px solid var(--border);">LAST LOGIN</th>
                            <th style="font-weight:600; color:var(--text-muted); font-size:11px; letter-spacing:0.05em; padding: 12px 16px; text-align: left; border-bottom: 1px solid var(--border);">2FA</th>
                            <th style="font-weight:600; color:var(--text-muted); font-size:11px; letter-spacing:0.05em; padding: 12px 16px; text-align: left; border-bottom: 1px solid var(--border);">ADDED</th>
                            <th style="font-weight:600; color:var(--text-muted); font-size:11px; letter-spacing:0.05em; padding: 12px 16px; text-align: right; border-bottom: 1px solid var(--border);">ACTIONS</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($users as $user)
                        <tr>
                            <td style="padding: 14px 16px; border-bottom: 1px solid var(--border);">
                                <div style="font-weight:600; color:var(--text-primary);">{{ $user->name }}</div>
                                <div style="color:var(--text-muted); font-size:12.5px; margin-top: 2px;">{{ $user->email }}</div>
                            </td>
                            <td style="padding: 14px 16px; border-bottom: 1px solid var(--border);">
                                @php
                                    $roleName = $user->roles->first()->name ?? 'None';
                                    $roleColor = 'background:#f1f3f4; color:#5f6368;'; // default grey
                                    if(stripos($roleName, 'Super Admin') !== false) { $roleColor = 'background:#f0e6ff; color:#6b38fb;'; }
                                    elseif(stripos($roleName, 'Admin') !== false) { $roleColor = 'background:#e6f4ea; color:#1e8e3e;'; }
                                    elseif(stripos($roleName, 'Manager') !== false) { $roleColor = 'background:#e8f0fe; color:#1a73e8;'; }
                                    elseif(stripos($roleName, 'Security') !== false) { $roleColor = 'background:#fce8e6; color:#d93025;'; }
                                @endphp
                                <span style="{{ $roleColor }} font-weight:600; padding: 4px 10px; border-radius: 6px; font-size: 12px; display: inline-block;">{{ $roleName }}</span>
                            </td>
                            <td style="padding: 14px 16px; border-bottom: 1px solid var(--border);">
                                @if($user->status === 'active')
                                    <div style="display:flex; align-items:center; gap:6px;">
                                        <div style="width:6px; height:6px; border-radius:50%; background:#1e8e3e;"></div>
                                        <span style="color:var(--text-primary); font-weight: 500;">Online</span>
                                    </div>
                                @else
                                    <div style="display:flex; align-items:center; gap:6px;">
                                        <div style="width:6px; height:6px; border-radius:50%; background:#9aa0a6;"></div>
                                        <span style="color:var(--text-muted); font-weight: 500;">Offline</span>
                                    </div>
                                @endif
                            </td>
                            <td style="padding: 14px 16px; border-bottom: 1px solid var(--border); color:var(--text-secondary);">
                                {{ $user->last_login_at ? \Carbon\Carbon::parse($user->last_login_at)->format('j M Y, h:i A') : 'Never' }}
                            </td>
                            <td style="padding: 14px 16px; border-bottom: 1px solid var(--border);">
                                @if($user->two_factor_enabled)
                                    <span style="color:#1e8e3e; font-weight:600;">Enabled</span>
                                @else
                                    <span style="color:var(--text-muted); font-weight: 500;">Disabled</span>
                                @endif
                            </td>
                            <td style="padding: 14px 16px; border-bottom: 1px solid var(--border); color:var(--text-secondary);">
                                {{ $user->created_at ? $user->created_at->format('j M Y') : '' }}
                            </td>
                            <td style="padding: 14px 16px; border-bottom: 1px solid var(--border); text-align: right;">
                                <div style="display:flex; align-items:center; justify-content:flex-end; gap:8px;">
                                    <button class="icon-btn" onclick="openUserModal({{ json_encode($user) }})" title="Edit User" style="color: #5f6368; background: transparent; border: none; cursor: pointer; padding: 4px;">
                                        <svg style="width: 16px; height: 16px;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 20h9M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"/></svg>
                                    </button>
                                    
                                    <form action="{{ route('users.destroy', $user->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Delete this user?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="icon-btn" title="Delete User" style="color: #5f6368; background: transparent; border: none; cursor: pointer; padding: 4px;">
                                            @if(stripos($roleName, 'Super Admin') !== false)
                                                <svg style="width: 16px; height: 16px;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="1.5"/><circle cx="12" cy="5" r="1.5"/><circle cx="12" cy="19" r="1.5"/></svg>
                                            @else
                                                <svg style="width: 16px; height: 16px;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 6h18M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg>
                                            @endif
                                        </button>
                                    </form>
                                    
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @endif

            <!-- TAB 2: ROLES --><!-- TAB 2: ROLES -->
            @if ($activeTab === 'roles')
            <div class="grid grid-3">
                @foreach($roles as $role)
                <div class="card" style="margin-bottom: 0;">
                    <div style="display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:10px;">
                        <div>
                            <div class="section-title">{{ $role->name }}</div>
                            @if($role->is_protected)
                                <span class="chip chip-muted" style="font-size:10px; margin-top:4px;">Built-in</span>
                            @endif
                        </div>
                        <button class="icon-btn" onclick="openRoleModal({{ json_encode($role) }})">
                            <svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 20h9M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"/></svg>
                        </button>
                    </div>
                    <div class="cell-sub" style="margin-bottom:14px; min-height: 36px;">
                        {{ $role->description ?? 'No description provided.' }}
                    </div>
                    <div style="font-size:12px; font-weight:600; color:var(--text-secondary);">
                        {{ $role->users()->count() }} User(s)
                    </div>
                </div>
                @endforeach
            </div>
            @endif

            <!-- TAB 3: MATRIX -->
            @if ($activeTab === 'matrix')
            <div class="card" style="padding: 0; overflow-x: auto; max-height: 70vh; overflow-y: auto;">
                <table class="matrix-table">
                    <thead>
                        <tr>
                            <th style="min-width: 200px; z-index: 11;">Permission</th>
                            @foreach($roles as $role)
                                <th style="text-align:center;">
                                    {{ $role->name }}<br>
                                    <span style="font-size:10px; font-weight:normal; color:var(--text-muted);">{{ $role->users()->count() }} users</span>
                                </th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $currentModule = '';
                        @endphp
                        @foreach($permissions as $perm)
                            @if($currentModule !== $perm->module)
                                <tr>
                                    <td colspan="{{ count($roles) + 1 }}" class="module-row">{{ $perm->module }}</td>
                                </tr>
                                @php $currentModule = $perm->module; @endphp
                            @endif
                            <tr>
                                <td>
                                    <div class="cell-name">{{ $perm->name }}</div>
                                    <div class="cell-sub">{{ $perm->description }}</div>
                                </td>
                                @foreach($roles as $role)
                                    @php
                                        $rp = $rolePermissions->where('role_id', $role->id)->where('permission_id', $perm->id)->first();
                                        $level = $rp ? $rp->access_level : 'none';
                                    @endphp
                                    <td style="text-align:center;">
                                        @if($level === 'full')
                                            <span class="chip chip-good" title="Full Access">✓ Full</span>
                                        @elseif($level === 'view')
                                            <span class="chip chip-warning" title="View Only">👁 View</span>
                                        @else
                                            <span style="color:var(--border);">—</span>
                                        @endif
                                    </td>
                                @endforeach
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @endif
            
            <!-- TAB 4: ACTIVITY -->
            @if ($activeTab === 'activity')
            <div class="card empty-state">
                <svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" style="width:48px; height:48px; color:var(--border); margin-bottom:10px;"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
                <div class="section-title">Activity Log</div>
                <div class="cell-sub">User session and audit logs will appear here.</div>
            </div>
            @endif

        </main>
    </div>

    <!-- Modals -->
    <!-- ADD/EDIT USER MODAL -->
    <div id="modalUser" class="modal-root">
        <div class="modal-backdrop" onclick="closeModal('modalUser')"></div>
        <div class="modal-panel">
            <div class="modal-head">
                <h3 id="userModalTitle">Invite User</h3>
                <button type="button" class="btn btn-sm btn-ghost" onclick="closeModal('modalUser')">✕</button>
            </div>
            <form id="userForm" method="POST" action="{{ route('users.store') }}">
                @csrf
                <input type="hidden" name="_method" id="userMethod" value="POST">
                <div class="modal-body">
                    <div class="form-grid">
                        <div class="field">
                            <label>Full Name *</label>
                            <input type="text" name="name" id="userName" required>
                        </div>
                        <div class="field">
                            <label>Email *</label>
                            <input type="email" name="email" id="userEmail" required>
                        </div>
                    </div>
                    
                    <div class="field" style="margin-top: 10px;">
                        <label>Role *</label>
                        <select name="role_id" id="userRole" required>
                            <option value="">Select a role...</option>
                            @foreach($roles as $role)
                                <option value="{{ $role->id }}">{{ $role->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div style="margin-top: 16px; display: flex; align-items: center; padding-top: 16px; border-top: 1px solid var(--border);">
                        <div>
                            <label style="margin-bottom: 2px; font-weight: 600;">Require 2FA</label>
                            <div class="cell-sub" style="font-size: 11px;">User must set up two-factor authentication.</div>
                        </div>
                        <label class="switch">
                            <input type="checkbox" name="require_2fa" id="require_2fa" value="1">
                            <span class="track"><span class="thumb"></span></span>
                        </label>
                    </div>

                </div>
                <div class="modal-foot">
                    <button type="button" class="btn" onclick="closeModal('modalUser')">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save User</button>
                </div>
            </form>
        </div>
    </div>

    <!-- EDIT ROLE MODAL -->
        <!-- CREATE / EDIT ROLE MODAL -->
    <div id="modalRole" class="modal-root">
        <div class="modal-backdrop" onclick="closeModal('modalRole')"></div>
        <div class="modal-panel wide">
            <div class="modal-head">
                <h3 id="roleModalTitle">Create Role</h3>
                <button type="button" class="btn btn-sm btn-ghost" onclick="closeModal('modalRole')">✕</button>
            </div>
            <form id="roleForm" method="POST" action="{{ route('users.roles.store') }}">
                @csrf
                <input type="hidden" name="_method" id="roleMethod" value="POST">
                <div class="modal-body">
                    <div class="field">
                        <label>Role Name</label>
                        <input type="text" name="name" id="roleName" required>
                    </div>
                    
                    <div class="field" style="display:none;">
                        <label>Status</label>
                        <select name="status" id="roleStatus">
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>

                    <div class="field">
                        <label>Description</label>
                        <textarea name="description" id="roleDescription" rows="3"></textarea>
                    </div>

                    <div class="field" style="margin-top: 20px;">
                        <label>Permission Highlights</label>
                        <div class="cell-sub" style="margin-bottom:10px;">Shown as tags on the role card. Toggle whether each is granted or denied.</div>
                        
                        <div id="permissionTagsContainer">
                            <!-- Dynamic permission rows will be added here -->
                        </div>

                        <button type="button" class="btn btn-sm" onclick="addPermissionTag()" style="margin-top: 10px;">
                            + Add Tag
                        </button>
                    </div>
                </div>
                <div class="modal-foot">
                    <button type="button" class="btn" onclick="closeModal('modalRole')">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openModal(id) { document.getElementById(id).classList.add('open'); }
        function closeModal(id) { document.getElementById(id).classList.remove('open'); }
        
        function openUserModal(user = null) {
            const form = document.getElementById('userForm');
            const method = document.getElementById('userMethod');
            
            if (user) {
                document.getElementById('userModalTitle').innerText = 'Edit User';
                form.action = `/users/${user.id}`;
                method.value = 'PUT';
                document.getElementById('userName').value = user.name || '';
                document.getElementById('userEmail').value = user.email || '';
                
                if (user.roles && user.roles.length > 0) {
                    document.getElementById('userRole').value = user.roles[0].id;
                }
                document.getElementById('require_2fa').checked = user.two_factor_enabled ? true : false;
                
            } else {
                document.getElementById('userModalTitle').innerText = 'Invite User';
                form.action = "{{ route('users.store') }}";
                method.value = 'POST';
                form.reset();
            }
            openModal('modalUser');
        }
        
        function toggleScopeInputs() {
            const type = document.getElementById('userScopeType').value;
            const branches = document.getElementById('userScopeBranches');
            if (type === 'site') branches.style.display = 'block';
            else branches.style.display = 'none';
        }

        const rolePermissions = @json($rolePermissions);
        const allPermissions = @json($permissions);
        let permRowCounter = 0;

        function renderPermissionRow(permId = '', accessLevel = 'full') {
            permRowCounter++;
            let options = '<option value="">Select a permission...</option>';
            allPermissions.forEach(p => {
                const selected = p.id == permId ? 'selected' : '';
                options += `<option value="${p.id}" ${selected}>${p.name}</option>`;
            });

            const checked = accessLevel !== 'none' ? 'checked' : '';
            const fieldName = permId ? `permissions[${permId}]` : `permissions_temp_${permRowCounter}`;
            
            return `
                <div class="perm-row" id="permRow_${permRowCounter}" style="display:flex; align-items:center; gap:10px; margin-bottom:8px;">
                    <div style="flex:1; border: 1px solid var(--border); border-radius: 4px; padding: 2px 6px;">
                        <select onchange="updatePermName(this, ${permRowCounter})" style="width:100%; border:none; outline:none; background:transparent; font-size:13px; padding:6px 0;">
                            ${options}
                        </select>
                    </div>
                    <label class="switch">
                        <input type="hidden" name="${fieldName}" id="permHidden_${permRowCounter}" value="none">
                        <input type="checkbox" name="${fieldName}" id="permCheck_${permRowCounter}" value="full" ${checked}>
                        <span class="track"><span class="thumb"></span></span>
                    </label>
                    <button type="button" class="btn btn-ghost btn-sm" onclick="removePermRow(${permRowCounter})" style="padding: 4px; border: none; font-size:16px;">✕</button>
                </div>
            `;
        }

        function addPermissionTag() {
            const container = document.getElementById('permissionTagsContainer');
            container.insertAdjacentHTML('beforeend', renderPermissionRow());
        }

        function removePermRow(id) {
            document.getElementById(`permRow_${id}`).remove();
        }

        function updatePermName(selectElem, id) {
            const val = selectElem.value;
            const hidden = document.getElementById(`permHidden_${id}`);
            const check = document.getElementById(`permCheck_${id}`);
            if (val) {
                hidden.name = `permissions[${val}]`;
                check.name = `permissions[${val}]`;
            }
        }
        
        function openRoleModal(role = null) {
            const form = document.getElementById('roleForm');
            const container = document.getElementById('permissionTagsContainer');
            container.innerHTML = ''; // clear tags

            if (role) {
                document.getElementById('roleModalTitle').innerText = 'Edit Role: ' + role.name;
                form.action = `/users/roles/${role.id}`;
                document.getElementById('roleMethod').value = 'PUT';
                
                document.getElementById('roleName').value = role.name;
                document.getElementById('roleName').disabled = role.is_protected ? true : false;
                document.getElementById('roleDescription').value = role.description || '';
                document.getElementById('roleDescription').disabled = role.is_protected ? true : false;
                document.getElementById('roleStatus').value = role.status;
                document.getElementById('roleStatus').disabled = role.is_protected ? true : false;
                
                // load existing perms
                const myPerms = rolePermissions.filter(rp => rp.role_id === role.id);
                myPerms.forEach(rp => {
                    container.insertAdjacentHTML('beforeend', renderPermissionRow(rp.permission_id, rp.access_level));
                });
            } else {
                document.getElementById('roleModalTitle').innerText = 'Create Role';
                form.action = "{{ route('users.roles.store') }}";
                document.getElementById('roleMethod').value = 'POST';
                form.reset();
                
                document.getElementById('roleName').disabled = false;
                document.getElementById('roleDescription').disabled = false;
                document.getElementById('roleStatus').disabled = false;
                document.getElementById('roleStatus').value = 'active';

                // add one blank tag by default
                addPermissionTag();
            }
            
            openModal('modalRole');
        }
    </script>

    <script>
        function toggleSidebar() {
            document.body.classList.toggle('sidebar-open');
        }
    </script>
</body>
</html>




