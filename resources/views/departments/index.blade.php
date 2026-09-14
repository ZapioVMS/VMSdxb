<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Departments — Madkhal</title>
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
            --radius: 12px;
            --radius-sm: 8px;
            --shadow: 0 1px 2px rgba(14,26,24,.06), 0 8px 24px -12px rgba(14,26,24,.18);
        }
        *, *::before, *::after { box-sizing: border-box; }
        body { margin: 0; font-family: 'IBM Plex Sans', system-ui, -apple-system, sans-serif; background: var(--bg); color: var(--text-primary); font-size: 14px; }
        .ic { width: 16px; height: 16px; flex: none; display: inline-block; vertical-align: -3px; }
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

        .grid-3 { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 16px; }
        @media (max-width: 1100px) { .grid-3 { grid-template-columns: 1fr; } }

        .card { background: var(--surface); border: 1px solid var(--border); border-radius: var(--radius); padding: 20px; }
        .hstack { display: flex; align-items: center; gap: 10px; }
        .cell-sub { font-size: 11.5px; color: var(--text-muted); }
        .chip { display: inline-flex; align-items: center; padding: 2px 8px; border-radius: 99px; font-size: 11px; font-weight: 600; }
        .chip-good { background: rgba(12, 163, 12, 0.14); color: var(--good); }
        .chip-muted { background: var(--surface-2); color: var(--text-muted); }

        .alert-success { background: rgba(12, 163, 12, 0.14); border: 1px solid rgba(12, 163, 12, 0.30); color: var(--good); padding: 12px 16px; border-radius: var(--radius-sm); font-size: 13.5px; margin-bottom: 18px; }
        .alert-danger { background: rgba(208, 59, 59, 0.14); border: 1px solid rgba(208, 59, 59, 0.30); color: var(--critical); padding: 12px 16px; border-radius: var(--radius-sm); font-size: 13.5px; margin-bottom: 18px; }

        .modal-root { display: none; position: fixed; inset: 0; z-index: 100; }
        .modal-root.open { display: block; }
        .modal-backdrop { position: absolute; inset: 0; background: rgba(6,12,11,.5); }
        .modal-panel { position: relative; max-width: 560px; width: calc(100% - 32px); margin: 6vh auto 0; background: var(--surface); border-radius: var(--radius); border: 1px solid var(--border); box-shadow: var(--shadow); overflow: hidden; }
        .modal-head { display: flex; align-items: center; justify-content: space-between; padding: 16px 20px; border-bottom: 1px solid var(--border); }
        .modal-body { padding: 20px; }
        .modal-foot { display: flex; justify-content: flex-end; gap: 8px; padding: 14px 20px; border-top: 1px solid var(--border); }
        .form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }
        .field { display: flex; flex-direction: column; gap: 5px; margin-bottom: 12px; }
        .field label { font-size: 12.5px; font-weight: 600; color: var(--text-secondary); }
        .field input, .field select, .field textarea { font-family: inherit; font-size: 13.5px; padding: 8px 10px; border-radius: var(--radius-sm); border: 1px solid var(--border); background: var(--surface); color: var(--text-primary); width: 100%; }
        .checkbox-group { display: flex; flex-direction: column; gap: 8px; margin-top: 4px; }
        .checkbox-item { display: flex; align-items: center; gap: 8px; font-size: 13px; font-weight: 500; cursor: pointer; }
        .checkbox-item input[type="checkbox"] { width: 16px; height: 16px; accent-color: var(--accent); }
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
                <a href="{{ route('dashboard') }}" class="navitem"><svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75"><path d="M3 11.5 12 4l9 7.5"/><path d="M5.5 10v9a1 1 0 0 0 1 1H10v-6h4v6h3.5a1 1 0 0 0 1-1v-9"/></svg><span>Dashboard</span></a>
                <a href="{{ route('visitors.index') }}" class="navitem"><svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75"><circle cx="9" cy="8" r="3"/><path d="M2.5 20c0-3.5 3-6 6.5-6s6.5 2.5 6.5 6"/><circle cx="17" cy="9" r="2.5"/><path d="M15.5 14.2c2.6.4 4.5 2.4 4.5 5.3"/></svg><span>Visitor Log</span></a>
                <a href="{{ route('visitors.checkin') }}" class="navitem"><svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75"><circle cx="10" cy="8" r="3.2"/><path d="M3 20c0-3.6 3.1-6.3 7-6.3s7 2.7 7 6.3"/><path d="M19 8v5M16.5 10.5h5"/></svg><span>Check-In</span></a>

                <div class="navlabel">Manage</div>
                <a href="{{ route('sites.index') }}" class="navitem"><svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75"><path d="M3 21h18M5 21V9l7-5 7 5v12M9 21v-5h6v5"/></svg><span>Sites & Locations</span></a>
                <a href="{{ route('departments.index') }}" class="navitem active"><svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75"><rect x="3" y="7" width="18" height="12.5" rx="2"/><path d="M8.5 7V5.5A1.5 1.5 0 0 1 10 4h4a1.5 1.5 0 0 1 1.5 1.5V7"/></svg><span>Departments</span></a>
                <a href="{{ route('employees.index') }}" class="navitem"><svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75"><circle cx="9" cy="8" r="3.2"/><path d="M2.5 20c0-3.6 3-6.3 6.5-6.3S15.5 16.4 15.5 20"/></svg><span>Employees</span></a>
                <a href="{{ route('lookups.index') }}" class="navitem"><svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75"><circle cx="12" cy="12" r="3"/><circle cx="12" cy="12" r="7.8"/></svg><span>Lookup Settings</span></a>
            </nav>
        </aside>

        <header id="topbar">
            <span style="font-weight:700;font-size:15px;">Departments</span>
            <div style="flex:1;"></div>
            <div style="text-align:end;">
                <div style="font-weight:700;font-size:13px;">{{ Auth::user()->name }}</div>
                <div style="font-size:11px;color:var(--text-muted);">{{ Auth::user()->organization->name ?? '' }}</div>
            </div>
        </header>

        <main id="main">
            <div class="pagehead">
                <div>
                    <h1>Departments Directory</h1>
                    <div class="sub">Manage organization departments, head contacts, and kiosk / NDA policy settings.</div>
                </div>
                <button class="btn btn-primary" onclick="openDepartmentModal()">+ Add Department</button>
            </div>

            @if (session('success'))
                <div class="alert-success"><strong>{{ session('success') }}</strong></div>
            @endif
            @if (session('error'))
                <div class="alert-danger"><strong>{{ session('error') }}</strong></div>
            @endif

            <div class="grid-3">
                @forelse ($departments as $dept)
                    <div class="card">
                        <div class="hstack" style="margin-bottom: 12px; justify-content: space-between;">
                            <div class="hstack" style="gap: 10px;">
                                <div style="width:36px;height:36px;border-radius:10px;background:var(--accent-soft);color:var(--accent);display:flex;align-items:center;justify-content:center;font-weight:700;">📂</div>
                                <div>
                                    <div style="font-weight: 700; font-size: 15px;">{{ $dept->name }}</div>
                                    <div class="cell-sub">Code: {{ $dept->code }}</div>
                                </div>
                            </div>
                            <button class="btn" onclick="openDepartmentModal({{ json_encode($dept) }})" style="padding:4px 8px;font-size:12px;">Edit</button>
                        </div>
                        
                        @if ($dept->description)
                            <p style="font-size:12.5px;color:var(--text-secondary);margin:0 0 12px 0;">{{ $dept->description }}</p>
                        @endif

                        <div style="display:flex;flex-wrap:wrap;gap:6px;margin-bottom:14px;">
                            @if ($dept->kiosk_selectable)
                                <span class="chip chip-good">Kiosk Visible</span>
                            @else
                                <span class="chip chip-muted">Kiosk Hidden</span>
                            @endif

                            @if ($dept->require_nda)
                                <span class="chip chip-good">NDA Required</span>
                            @endif

                            @if ($dept->escort_required)
                                <span class="chip chip-good">Escort Required</span>
                            @endif
                        </div>

                        <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;font-size:12.5px;background:var(--surface-2);padding:10px;border-radius:8px;">
                            <div><div class="cell-sub">Department Head</div><strong>{{ $dept->headEmployee->full_name ?? 'Unassigned' }}</strong></div>
                            <div><div class="cell-sub">Total Staff</div><strong>{{ $dept->employees_count ?? 0 }} employees</strong></div>
                            @if($dept->contact_email)
                                <div style="grid-column: 1 / -1;"><div class="cell-sub">Contact</div><strong>{{ $dept->contact_email }}</strong></div>
                            @endif
                        </div>
                    </div>
                @empty
                    <div style="grid-column: 1 / -1;" class="card">No departments found for this organization.</div>
                @endforelse
            </div>
        </main>
    </div>

    <!-- Department Modal -->
    <div id="deptModal" class="modal-root">
        <div class="modal-backdrop" onclick="closeDepartmentModal()"></div>
        <div class="modal-panel">
            <div class="modal-head">
                <h3 id="modalTitle">Add Department</h3>
                <button class="btn" onclick="closeDepartmentModal()">✕</button>
            </div>
            <form id="deptForm" action="{{ route('departments.store') }}" method="POST">
                @csrf
                <input type="hidden" id="dept_method" name="_method" value="POST">
                <div class="modal-body">
                    <div class="form-grid">
                        <div class="field">
                            <label for="dept_name">Department Name *</label>
                            <input type="text" id="dept_name" name="name" required placeholder="Human Resources">
                        </div>
                        <div class="field">
                            <label for="dept_code">Department Code *</label>
                            <input type="text" id="dept_code" name="code" required placeholder="HR">
                        </div>
                        <div class="field">
                            <label for="dept_head">Department Head</label>
                            <select id="dept_head" name="head_employee_id">
                                <option value="">-- Unassigned --</option>
                                @foreach ($employees as $emp)
                                    <option value="{{ $emp->id }}">{{ $emp->full_name }} ({{ $emp->job_title ?? 'Employee' }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="field">
                            <label for="dept_status">Status *</label>
                            <select id="dept_status" name="status" required>
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                        </div>
                        <div class="field">
                            <label for="dept_email">Contact Email</label>
                            <input type="email" id="dept_email" name="contact_email" placeholder="hr@company.com">
                        </div>
                        <div class="field">
                            <label for="dept_phone">Contact Phone</label>
                            <input type="text" id="dept_phone" name="contact_phone" placeholder="+971 4 123 4567">
                        </div>
                        <div class="field" style="grid-column: 1 / -1;">
                            <label for="dept_desc">Description</label>
                            <textarea id="dept_desc" name="description" rows="2" placeholder="Brief description of department scope..."></textarea>
                        </div>
                        <div class="field" style="grid-column: 1 / -1;">
                            <label>Security & Kiosk Policies</label>
                            <div class="checkbox-group">
                                <label class="checkbox-item">
                                    <input type="checkbox" id="dept_kiosk" name="kiosk_selectable" value="1" checked>
                                    <span>Show this department in Kiosk check-in directory</span>
                                </label>
                                <label class="checkbox-item">
                                    <input type="checkbox" id="dept_nda" name="require_nda" value="1">
                                    <span>Require Non-Disclosure Agreement (NDA) for visitors</span>
                                </label>
                                <label class="checkbox-item">
                                    <input type="checkbox" id="dept_escort" name="escort_required" value="1">
                                    <span>Require Security/Staff Escort for all visitors</span>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-foot">
                    <button type="button" class="btn" onclick="closeDepartmentModal()">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Department</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openDepartmentModal(dept) {
            const modal = document.getElementById('deptModal');
            const form = document.getElementById('deptForm');
            const method = document.getElementById('dept_method');
            const title = document.getElementById('modalTitle');

            if (dept) {
                title.innerText = 'Edit Department';
                form.action = '/departments/' + dept.id;
                method.value = 'PUT';
                document.getElementById('dept_name').value = dept.name || '';
                document.getElementById('dept_code').value = dept.code || '';
                document.getElementById('dept_head').value = dept.head_employee_id || '';
                document.getElementById('dept_status').value = dept.status || 'active';
                document.getElementById('dept_email').value = dept.contact_email || '';
                document.getElementById('dept_phone').value = dept.contact_phone || '';
                document.getElementById('dept_desc').value = dept.description || '';
                document.getElementById('dept_kiosk').checked = Boolean(dept.kiosk_selectable);
                document.getElementById('dept_nda').checked = Boolean(dept.require_nda);
                document.getElementById('dept_escort').checked = Boolean(dept.escort_required);
            } else {
                title.innerText = 'Add Department';
                form.action = "{{ route('departments.store') }}";
                method.value = 'POST';
                form.reset();
                document.getElementById('dept_kiosk').checked = true;
            }
            modal.classList.add('open');
        }
        function closeDepartmentModal() {
            document.getElementById('deptModal').classList.remove('open');
        }
    </script>
</body>
</html>
