<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Users & Roles — Zapio VMS</title>
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
    </style>
</head>
<body>
    <div id="app">
        @include('partials.sidebar', ['active' => 'users'])
        @include('partials.topbar')

        <main id="main">
            <div class="pagehead">
                <div>
                    <h1>Users & Roles</h1>
                    <div class="sub">Manage system access, create roles, and define permission levels across the platform.</div>
                </div>
                <div class="pagehead-actions">
                    <button type="button" class="btn btn-primary" onclick="openUserModal()">
                        <svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 5v14M5 12h14"/></svg>
                        <span>+ Add User</span>
                    </button>
                    <button type="button" class="btn" onclick="openRoleModal()">
                        <svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 5v14M5 12h14"/></svg>
                        <span>+ Add Role</span>
                    </button>
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
                <a href="{{ route('users.index', ['tab' => 'users']) }}" class="subtab {{ $activeTab === 'users' ? 'active' : '' }}">Users ({{ $users->count() }})</a>
                <a href="{{ route('users.index', ['tab' => 'roles']) }}" class="subtab {{ $activeTab === 'roles' ? 'active' : '' }}">Roles ({{ $roles->count() }})</a>
                <a href="{{ route('users.index', ['tab' => 'matrix']) }}" class="subtab {{ $activeTab === 'matrix' ? 'active' : '' }}">Permissions Matrix</a>
                <a href="{{ route('users.index', ['tab' => 'activity']) }}" class="subtab {{ $activeTab === 'activity' ? 'active' : '' }}">Activity Log</a>
            </div>

            <!-- TAB 1: USERS -->
            @if ($activeTab === 'users')
            <div class="tablewrap scrollx">
                <table class="dtable">
                    <thead>
                        <tr>
                            <th>User Name</th>
                            <th>Role(s)</th>
                            <th>Scope / Domain</th>
                            <th>Status</th>
                            <th>Last Login</th>
                            <th style="text-align:end;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($users as $user)
                        <tr>
                            <td>
                                <div class="cell-name">{{ $user->name }}</div>
                                <div class="cell-sub">{{ $user->email }}</div>
                            </td>
                            <td>
                                @foreach($user->roles as $role)
                                    <span class="chip chip-muted">{{ $role->name }}</span>
                                @endforeach
                            </td>
                            <td>Organization Wide</td>
                            <td>
                                @if($user->status === 'active')
                                    <span class="chip chip-good">Active</span>
                                @else
                                    <span class="chip chip-warning">Inactive</span>
                                @endif
                            </td>
                            <td class="cell-sub">{{ $user->last_login_at ? \Carbon\Carbon::parse($user->last_login_at)->diffForHumans() : 'Never' }}</td>
                            <td style="text-align:end;">
                                <div class="rowactions">
                                    <button class="icon-btn" title="Edit" onclick="openUserModal({{ json_encode($user) }})">
                                        <svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 20h9M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"/></svg>
                                    </button>
                                    <form action="{{ route('users.destroy', $user->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Delete this user?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="icon-btn icon-btn-danger" title="Delete">
                                            <svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg>
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

            <!-- TAB 2: ROLES -->
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
                <h3 id="userModalTitle">Add User</h3>
                <button type="button" class="btn btn-sm btn-ghost" onclick="closeModal('modalUser')">✕</button>
            </div>
            <form id="userForm" method="POST" action="{{ route('users.store') }}">
                @csrf
                <input type="hidden" name="_method" id="userMethod" value="POST">
                <div class="modal-body">
                    <div class="field">
                        <label>Link to Employee Profile (Optional)</label>
                        <select name="employee_id" id="userEmployeeId">
                            <option value="">No linked employee profile</option>
                            @foreach($employees as $emp)
                                <option value="{{ $emp->id }}">{{ $emp->full_name }} ({{ $emp->email }})</option>
                            @endforeach
                        </select>
                        <div class="cell-sub">Linking connects the user account to their directory profile.</div>
                    </div>
                    
                    <div class="form-grid">
                        <div class="field">
                            <label>Full Name *</label>
                            <input type="text" name="name" id="userName" required>
                        </div>
                        <div class="field">
                            <label>Email Address *</label>
                            <input type="email" name="email" id="userEmail" required>
                        </div>
                    </div>
                    
                    <div class="form-grid">
                        <div class="field">
                            <label>Password <span id="userPasswordReq">*</span></label>
                            <input type="password" name="password" id="userPassword" placeholder="Minimum 8 characters">
                            <div class="cell-sub" id="userPasswordHint" style="display:none;">Leave blank to keep current password.</div>
                        </div>
                        <div class="field">
                            <label>Status</label>
                            <select name="status" id="userStatus">
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                        </div>
                    </div>

                    <div class="field" style="margin-top: 10px;">
                        <label>Assigned Roles *</label>
                        <div style="display:flex; flex-wrap:wrap; gap:10px; margin-top:6px;">
                            @foreach($roles as $role)
                                <label style="display:flex; align-items:center; gap:6px; font-weight:normal; color:var(--text-primary); cursor:pointer;">
                                    <input type="checkbox" name="role_ids[]" value="{{ $role->id }}" class="user-role-cb">
                                    {{ $role->name }}
                                </label>
                            @endforeach
                        </div>
                    </div>
                    
                    <div class="field" style="margin-top: 10px; border-top: 1px solid var(--border); padding-top: 14px;">
                        <label>Data Access Scope</label>
                        <select id="userScopeType" onchange="toggleScopeInputs()">
                            <option value="organization">Entire Organization</option>
                            <option value="site">Specific Branches Only</option>
                        </select>
                        
                        <div id="userScopeBranches" style="display:none; margin-top:10px;">
                            <label style="font-size:12px; font-weight:600;">Select Branches:</label>
                            <div style="display:flex; flex-wrap:wrap; gap:10px; margin-top:6px;">
                                @foreach($sites as $site)
                                    <label style="display:flex; align-items:center; gap:6px; font-weight:normal; color:var(--text-primary); cursor:pointer;">
                                        <input type="checkbox" name="scopes[site][]" value="{{ $site->id }}">
                                        {{ $site->name }}
                                    </label>
                                @endforeach
                            </div>
                        </div>
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
    <div id="modalRole" class="modal-root">
        <div class="modal-backdrop" onclick="closeModal('modalRole')"></div>
        <div class="modal-panel wide">
            <div class="modal-head">
                <h3 id="roleModalTitle">Edit Role</h3>
                <button type="button" class="btn btn-sm btn-ghost" onclick="closeModal('modalRole')">✕</button>
            </div>
            <form id="roleForm" method="POST" action="">
                @csrf
                <input type="hidden" name="_method" value="PUT">
                <div class="modal-body">
                    <div class="form-grid">
                        <div class="field">
                            <label>Role Name *</label>
                            <input type="text" name="name" id="roleName" required>
                        </div>
                        <div class="field">
                            <label>Status</label>
                            <select name="status" id="roleStatus">
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                        </div>
                    </div>
                    <div class="field">
                        <label>Description</label>
                        <textarea name="description" id="roleDescription" rows="2"></textarea>
                    </div>

                    <div style="margin-top: 20px; font-weight: 600; font-size:14px; border-bottom:1px solid var(--border); padding-bottom:6px; margin-bottom:10px;">
                        Permissions
                    </div>
                    
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                        @php $currentMod = ''; @endphp
                        @foreach($permissions as $perm)
                            @if($currentMod !== $perm->module)
                                @if($currentMod !== '') </div> @endif
                                <div>
                                    <div style="font-size:12px; font-weight:700; text-transform:uppercase; color:var(--text-muted); margin-bottom:8px;">{{ $perm->module }}</div>
                                @php $currentMod = $perm->module; @endphp
                            @endif
                            <div style="margin-bottom:10px;">
                                <div style="font-size:13px; font-weight:600;">{{ $perm->name }}</div>
                                <div style="display:flex; gap:10px; margin-top:4px;">
                                    <label style="font-size:12px; cursor:pointer;"><input type="radio" name="permissions[{{ $perm->id }}]" value="none" id="perm_{{$perm->id}}_none" checked> None</label>
                                    <label style="font-size:12px; cursor:pointer;"><input type="radio" name="permissions[{{ $perm->id }}]" value="view" id="perm_{{$perm->id}}_view"> View</label>
                                    <label style="font-size:12px; cursor:pointer;"><input type="radio" name="permissions[{{ $perm->id }}]" value="full" id="perm_{{$perm->id}}_full"> Full</label>
                                </div>
                            </div>
                        @endforeach
                        @if($currentMod !== '') </div> @endif
                    </div>
                </div>
                <div class="modal-foot">
                    <button type="button" class="btn" onclick="closeModal('modalRole')">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Role</button>
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
            const pwdReq = document.getElementById('userPasswordReq');
            const pwdHint = document.getElementById('userPasswordHint');
            
            document.querySelectorAll('.user-role-cb').forEach(cb => cb.checked = false);
            
            if (user) {
                document.getElementById('userModalTitle').innerText = 'Edit User';
                form.action = `/users/${user.id}`;
                method.value = 'PUT';
                document.getElementById('userName').value = user.name;
                document.getElementById('userEmail').value = user.email;
                document.getElementById('userEmployeeId').value = user.employee_id || '';
                document.getElementById('userStatus').value = user.status;
                document.getElementById('userPassword').required = false;
                pwdReq.style.display = 'none';
                pwdHint.style.display = 'block';
                
                if (user.roles) {
                    user.roles.forEach(r => {
                        const cb = document.querySelector(`.user-role-cb[value="${r.id}"]`);
                        if(cb) cb.checked = true;
                    });
                }
                
                // TODO: set scopes on edit if we passed scopes in JSON
            } else {
                document.getElementById('userModalTitle').innerText = 'Add User';
                form.action = "{{ route('users.store') }}";
                method.value = 'POST';
                form.reset();
                document.getElementById('userPassword').required = true;
                pwdReq.style.display = 'inline';
                pwdHint.style.display = 'none';
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
        
        function openRoleModal(role) {
            const form = document.getElementById('roleForm');
            document.getElementById('roleModalTitle').innerText = 'Edit Role: ' + role.name;
            form.action = `/users/roles/${role.id}`;
            document.getElementById('roleName').value = role.name;
            document.getElementById('roleName').disabled = role.is_protected ? true : false;
            document.getElementById('roleDescription').value = role.description || '';
            document.getElementById('roleDescription').disabled = role.is_protected ? true : false;
            document.getElementById('roleStatus').value = role.status;
            document.getElementById('roleStatus').disabled = role.is_protected ? true : false;
            
            // reset radios
            document.querySelectorAll('input[type="radio"][value="none"]').forEach(r => r.checked = true);
            
            // map existing perms
            const myPerms = rolePermissions.filter(rp => rp.role_id === role.id);
            myPerms.forEach(rp => {
                const r = document.getElementById(`perm_${rp.permission_id}_${rp.access_level}`);
                if (r) r.checked = true;
            });
            
            openModal('modalRole');
        }
    </script>
</body>
</html>
