<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Employee Directory — Madkhal</title>
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
            --warn: #d97706;
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

        .card { background: var(--surface); border: 1px solid var(--border); border-radius: var(--radius); padding: 20px; }
        .filterbar { display: flex; flex-wrap: wrap; gap: 10px; margin-bottom: 20px; background: var(--surface); border: 1px solid var(--border); border-radius: var(--radius-sm); padding: 12px 14px; align-items: center; }
        .filterbar input, .filterbar select { font-family: inherit; font-size: 13px; padding: 7px 10px; border-radius: var(--radius-sm); border: 1px solid var(--border); background: var(--surface); }

        .table-container { background: var(--surface); border: 1px solid var(--border); border-radius: var(--radius); overflow: hidden; }
        table { width: 100%; border-collapse: collapse; font-size: 13.5px; text-align: start; }
        th { background: var(--surface-2); font-weight: 600; color: var(--text-secondary); padding: 11px 14px; border-bottom: 1px solid var(--border); text-transform: uppercase; font-size: 11px; letter-spacing: .04em; }
        td { padding: 12px 14px; border-bottom: 1px solid var(--border); color: var(--text-primary); vertical-align: middle; }
        tr:last-child td { border-bottom: none; }
        tr:hover td { background: rgba(14,26,24,.015); }

        .avatar { width: 34px; height: 34px; border-radius: 99px; background: var(--accent-soft); color: var(--accent); font-weight: 700; font-size: 13px; display: flex; align-items: center; justify-content: center; flex: none; }
        .cell-sub { font-size: 11.5px; color: var(--text-muted); }
        .chip { display: inline-flex; align-items: center; padding: 2px 8px; border-radius: 99px; font-size: 11px; font-weight: 600; }
        .chip-active { background: rgba(12, 163, 12, 0.14); color: var(--good); }
        .chip-leave { background: rgba(217, 119, 6, 0.14); color: var(--warn); }
        .chip-inactive { background: rgba(208, 59, 59, 0.14); color: var(--critical); }

        .alert-success { background: rgba(12, 163, 12, 0.14); border: 1px solid rgba(12, 163, 12, 0.30); color: var(--good); padding: 12px 16px; border-radius: var(--radius-sm); font-size: 13.5px; margin-bottom: 18px; }
        .alert-danger { background: rgba(208, 59, 59, 0.14); border: 1px solid rgba(208, 59, 59, 0.30); color: var(--critical); padding: 12px 16px; border-radius: var(--radius-sm); font-size: 13.5px; margin-bottom: 18px; }

        .modal-root { display: none; position: fixed; inset: 0; z-index: 100; }
        .modal-root.open { display: block; }
        .modal-backdrop { position: absolute; inset: 0; background: rgba(6,12,11,.5); }
        .modal-panel { position: relative; max-width: 620px; width: calc(100% - 32px); margin: 5vh auto 0; background: var(--surface); border-radius: var(--radius); border: 1px solid var(--border); box-shadow: var(--shadow); overflow: hidden; }
        .modal-head { display: flex; align-items: center; justify-content: space-between; padding: 16px 20px; border-bottom: 1px solid var(--border); }
        .modal-body { padding: 20px; max-height: 75vh; overflow-y: auto; }
        .modal-foot { display: flex; justify-content: flex-end; gap: 8px; padding: 14px 20px; border-top: 1px solid var(--border); }
        .form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }
        .field { display: flex; flex-direction: column; gap: 5px; margin-bottom: 12px; }
        .field label { font-size: 12.5px; font-weight: 600; color: var(--text-secondary); }
        .field input, .field select, .field textarea { font-family: inherit; font-size: 13.5px; padding: 8px 10px; border-radius: var(--radius-sm); border: 1px solid var(--border); background: var(--surface); color: var(--text-primary); width: 100%; }

        .pagination { display: flex; gap: 4px; margin-top: 16px; justify-content: flex-end; }
        .pagination a, .pagination span { padding: 6px 12px; border-radius: 6px; border: 1px solid var(--border); background: var(--surface); text-decoration: none; color: var(--text-primary); font-size: 12.5px; }
        .pagination .active { background: var(--accent); color: #fff; border-color: var(--accent); }
    </style>
</head>
<body>
    <div id="app">
        @include('partials.sidebar', ['active' => 'employees'])


        @include('partials.topbar')

        <main id="main">
            <div class="pagehead">
                <div>
                    <h1>Employee Directory</h1>
                    <div class="sub">Manage staff members, hosts, department assignments, and leave statuses.</div>
                </div>
                <button class="btn btn-primary" onclick="openEmployeeModal()">+ Add Employee</button>
            </div>

            @if (session('success'))
                <div class="alert-success"><strong>{{ session('success') }}</strong></div>
            @endif
            @if (session('error'))
                <div class="alert-danger"><strong>{{ session('error') }}</strong></div>
            @endif

            <form method="GET" action="{{ route('employees.index') }}" class="filterbar">
                <input type="text" name="search" placeholder="Search by name, email, or code..." value="{{ request('search') }}" style="min-width:240px;flex:1;">
                
                <select name="site_id" onchange="this.form.submit()">
                    <option value="">-- All Sites --</option>
                    @foreach ($sites as $site)
                        <option value="{{ $site->id }}" {{ request('site_id') == $site->id ? 'selected' : '' }}>{{ $site->name }}</option>
                    @endforeach
                </select>

                <select name="department_id" onchange="this.form.submit()">
                    <option value="">-- All Departments --</option>
                    @foreach ($departments as $dept)
                        <option value="{{ $dept->id }}" {{ request('department_id') == $dept->id ? 'selected' : '' }}>{{ $dept->name }}</option>
                    @endforeach
                </select>

                <select name="status" onchange="this.form.submit()">
                    <option value="">-- All Statuses --</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                    <option value="on_leave" {{ request('status') == 'on_leave' ? 'selected' : '' }}>On Leave</option>
                    <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                </select>

                <button type="submit" class="btn">Filter</button>
                @if (request()->hasAny(['search', 'site_id', 'department_id', 'status']))
                    <a href="{{ route('employees.index') }}" class="btn" style="color:var(--text-muted);">Clear</a>
                @endif
            </form>

            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>Employee</th>
                            <th>Code</th>
                            <th>Department & Site</th>
                            <th>Shift / Schedule</th>
                            <th>Status</th>
                            <th style="text-align:end;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($employees as $emp)
                            <tr>
                                <td>
                                    <div style="display:flex;align-items:center;gap:10px;">
                                        <div class="avatar">
                                            {{ strtoupper(substr($emp->first_name, 0, 1)) }}{{ strtoupper(substr($emp->last_name ?? '', 0, 1)) }}
                                        </div>
                                        <div>
                                            <div style="font-weight:600;">{{ $emp->full_name }}</div>
                                            <div class="cell-sub">{{ $emp->job_title ?? 'Employee' }} · {{ $emp->email }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td><code>{{ $emp->employee_code }}</code></td>
                                <td>
                                    <div><strong>{{ $emp->department->name ?? 'Unassigned' }}</strong></div>
                                    <div class="cell-sub">{{ $emp->site->name ?? 'All Locations' }}</div>
                                </td>
                                <td>
                                    <div>{{ $emp->shift->name ?? 'Standard Shift' }}</div>
                                    @if ($emp->shift)
                                        <div class="cell-sub">{{ \Carbon\Carbon::parse($emp->shift->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($emp->shift->end_time)->format('H:i') }}</div>
                                    @endif
                                </td>
                                <td>
                                    @if ($emp->status === 'active')
                                        <span class="chip chip-active">Active</span>
                                    @elseif ($emp->status === 'on_leave')
                                        <span class="chip chip-leave">On Leave</span>
                                    @else
                                        <span class="chip chip-inactive">Inactive</span>
                                    @endif
                                </td>
                                <td style="text-align:end;">
                                    <div style="display:inline-flex;gap:6px;">
                                        <form action="{{ route('employees.leave', $emp->id) }}" method="POST" style="display:inline;">
                                            @csrf
                                            <button type="submit" class="btn" style="padding:4px 8px;font-size:12px;" title="Toggle leave status">
                                                {{ $emp->status === 'on_leave' ? 'Return' : 'Leave' }}
                                            </button>
                                        </form>
                                        <button class="btn" onclick="openEmployeeModal({{ json_encode($emp) }})" style="padding:4px 8px;font-size:12px;">Edit</button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" style="text-align:center;padding:30px;color:var(--text-muted);">No employees found matching the filters.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div style="margin-top:16px;">
                {{ $employees->links() }}
            </div>
        </main>
    </div>

    <!-- Employee Modal -->
    <div id="empModal" class="modal-root">
        <div class="modal-backdrop" onclick="closeEmployeeModal()"></div>
        <div class="modal-panel">
            <div class="modal-head">
                <h3 id="modalTitle">Add Employee</h3>
                <button class="btn" onclick="closeEmployeeModal()">✕</button>
            </div>
            <form id="empForm" action="{{ route('employees.store') }}" method="POST">
                @csrf
                <input type="hidden" id="emp_method" name="_method" value="POST">
                <div class="modal-body">
                    <div class="form-grid">
                        <div class="field">
                            <label for="emp_code">Employee Code *</label>
                            <input type="text" id="emp_code" name="employee_code" required placeholder="EMP-10020">
                        </div>
                        <div class="field">
                            <label for="emp_status">Status *</label>
                            <select id="emp_status" name="status" required>
                                <option value="active">Active</option>
                                <option value="on_leave">On Leave</option>
                                <option value="inactive">Inactive</option>
                            </select>
                        </div>
                        <div class="field">
                            <label for="emp_fname">First Name *</label>
                            <input type="text" id="emp_fname" name="first_name" required placeholder="Allan">
                        </div>
                        <div class="field">
                            <label for="emp_lname">Last Name</label>
                            <input type="text" id="emp_lname" name="last_name" placeholder="Jacob">
                        </div>
                        <div class="field">
                            <label for="emp_email">Email Address *</label>
                            <input type="email" id="emp_email" name="email" required placeholder="allan@zapio.ae">
                        </div>
                        <div class="field">
                            <label for="emp_phone">Phone Number</label>
                            <input type="text" id="emp_phone" name="phone" placeholder="+971 50 123 4567">
                        </div>
                        <div class="field">
                            <label for="emp_title">Job Title</label>
                            <input type="text" id="emp_title" name="job_title" placeholder="Operations Manager">
                        </div>
                        <div class="field">
                            <label for="emp_site">Site Location</label>
                            <select id="emp_site" name="site_id">
                                <option value="">-- All Sites --</option>
                                @foreach ($sites as $site)
                                    <option value="{{ $site->id }}">{{ $site->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="field">
                            <label for="emp_dept">Department</label>
                            <select id="emp_dept" name="department_id">
                                <option value="">-- Unassigned --</option>
                                @foreach ($departments as $dept)
                                    <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="field">
                            <label for="emp_shift">Shift Schedule</label>
                            <select id="emp_shift" name="shift_id">
                                <option value="">-- Standard Shift --</option>
                                @foreach ($shifts as $s)
                                    <option value="{{ $s->id }}">{{ $s->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="field" style="grid-column: 1 / -1;">
                            <label for="emp_manager">Reporting Manager</label>
                            <select id="emp_manager" name="manager_id">
                                <option value="">-- None --</option>
                                @foreach ($managers as $mgr)
                                    <option value="{{ $mgr->id }}">{{ $mgr->full_name }} ({{ $mgr->job_title ?? 'Staff' }})</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-foot">
                    <button type="button" class="btn" onclick="closeEmployeeModal()">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Employee</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openEmployeeModal(emp) {
            const modal = document.getElementById('empModal');
            const form = document.getElementById('empForm');
            const method = document.getElementById('emp_method');
            const title = document.getElementById('modalTitle');

            if (emp) {
                title.innerText = 'Edit Employee Profile';
                form.action = '/employees/' + emp.id;
                method.value = 'PUT';
                document.getElementById('emp_code').value = emp.employee_code || '';
                document.getElementById('emp_status').value = emp.status || 'active';
                document.getElementById('emp_fname').value = emp.first_name || '';
                document.getElementById('emp_lname').value = emp.last_name || '';
                document.getElementById('emp_email').value = emp.email || '';
                document.getElementById('emp_phone').value = emp.phone || '';
                document.getElementById('emp_title').value = emp.job_title || '';
                document.getElementById('emp_site').value = emp.site_id || '';
                document.getElementById('emp_dept').value = emp.department_id || '';
                document.getElementById('emp_shift').value = emp.shift_id || '';
                document.getElementById('emp_manager').value = emp.manager_id || '';
            } else {
                title.innerText = 'Add Employee';
                form.action = "{{ route('employees.store') }}";
                method.value = 'POST';
                form.reset();
            }
            modal.classList.add('open');
        }
        function closeEmployeeModal() {
            document.getElementById('empModal').classList.remove('open');
        }
    </script>
</body>
</html>
