<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Lookup Settings — Madkhal</title>
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

        .tabs { display: flex; gap: 6px; border-bottom: 1px solid var(--border); margin-bottom: 20px; }
        .tab-btn { padding: 9px 16px; border: none; background: none; font-size: 13.5px; font-weight: 600; color: var(--text-secondary); border-bottom: 2px solid transparent; cursor: pointer; text-decoration: none; }
        .tab-btn.active { color: var(--accent); border-bottom-color: var(--accent); }

        .table-container { background: var(--surface); border: 1px solid var(--border); border-radius: var(--radius); overflow: hidden; }
        table { width: 100%; border-collapse: collapse; font-size: 13.5px; text-align: start; }
        th { background: var(--surface-2); font-weight: 600; color: var(--text-secondary); padding: 11px 14px; border-bottom: 1px solid var(--border); text-transform: uppercase; font-size: 11px; letter-spacing: .04em; }
        td { padding: 12px 14px; border-bottom: 1px solid var(--border); color: var(--text-primary); vertical-align: middle; }
        tr:last-child td { border-bottom: none; }

        .chip { display: inline-flex; align-items: center; padding: 2px 8px; border-radius: 99px; font-size: 11px; font-weight: 600; }
        .chip-good { background: rgba(12, 163, 12, 0.14); color: var(--good); }
        .chip-muted { background: var(--surface-2); color: var(--text-muted); }

        .alert-success { background: rgba(12, 163, 12, 0.14); border: 1px solid rgba(12, 163, 12, 0.30); color: var(--good); padding: 12px 16px; border-radius: var(--radius-sm); font-size: 13.5px; margin-bottom: 18px; }

        .modal-root { display: none; position: fixed; inset: 0; z-index: 100; }
        .modal-root.open { display: block; }
        .modal-backdrop { position: absolute; inset: 0; background: rgba(6,12,11,.5); }
        .modal-panel { position: relative; max-width: 500px; width: calc(100% - 32px); margin: 8vh auto 0; background: var(--surface); border-radius: var(--radius); border: 1px solid var(--border); box-shadow: var(--shadow); overflow: hidden; }
        .modal-head { display: flex; align-items: center; justify-content: space-between; padding: 16px 20px; border-bottom: 1px solid var(--border); }
        .modal-body { padding: 20px; }
        .modal-foot { display: flex; justify-content: flex-end; gap: 8px; padding: 14px 20px; border-top: 1px solid var(--border); }
        .field { display: flex; flex-direction: column; gap: 5px; margin-bottom: 12px; }
        .field label { font-size: 12.5px; font-weight: 600; color: var(--text-secondary); }
        .field input, .field select { font-family: inherit; font-size: 13.5px; padding: 8px 10px; border-radius: var(--radius-sm); border: 1px solid var(--border); background: var(--surface); color: var(--text-primary); width: 100%; }
        .checkbox-item { display: flex; align-items: center; gap: 8px; font-size: 13px; font-weight: 500; cursor: pointer; margin-top: 6px; }
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
                <a href="{{ route('pre_registrations.index') }}" class="navitem {{ request()->routeIs('pre_registrations.*') ? 'active' : '' }}"><svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75"><rect x="3.5" y="5" width="17" height="15" rx="2"/><path d="M3.5 9.5h17M8 3v4M16 3v4"/></svg><span>Pre-Registration</span></a>

                <div class="navlabel">Manage</div>
                <a href="{{ route('sites.index') }}" class="navitem"><svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75"><path d="M3 21h18M5 21V9l7-5 7 5v12M9 21v-5h6v5"/></svg><span>Branches</span></a>
                <a href="{{ route('departments.index') }}" class="navitem"><svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75"><rect x="3" y="7" width="18" height="12.5" rx="2"/><path d="M8.5 7V5.5A1.5 1.5 0 0 1 10 4h4a1.5 1.5 0 0 1 1.5 1.5V7"/></svg><span>Departments</span></a>
                <a href="{{ route('employees.index') }}" class="navitem"><svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75"><circle cx="9" cy="8" r="3.2"/><path d="M2.5 20c0-3.6 3-6.3 6.5-6.3S15.5 16.4 15.5 20"/></svg><span>Employees</span></a>
                <a href="{{ route('lookups.index') }}" class="navitem active"><svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75"><circle cx="12" cy="12" r="3"/><circle cx="12" cy="12" r="7.8"/></svg><span>Lookup Settings</span></a>
            </nav>
        </aside>

        @include('partials.topbar')

        <main id="main">
            <div class="pagehead">
                <div>
                    <h1>Lookup Settings</h1>
                    <div class="sub">Configure system master data catalogs and operational dropdown options.</div>
                </div>
                <button class="btn btn-primary" onclick="openLookupModal()">+ Add New Item</button>
            </div>

            @if (session('success'))
                <div class="alert-success"><strong>{{ session('success') }}</strong></div>
            @endif

            <div class="tabs">
                <a href="{{ route('lookups.index', ['tab' => 'purposes']) }}" class="tab-btn {{ $tab === 'purposes' ? 'active' : '' }}">Visit Purposes</a>
                <a href="{{ route('lookups.index', ['tab' => 'id_types']) }}" class="tab-btn {{ $tab === 'id_types' ? 'active' : '' }}">ID Document Types</a>
                <a href="{{ route('lookups.index', ['tab' => 'visitor_types']) }}" class="tab-btn {{ $tab === 'visitor_types' ? 'active' : '' }}">Visitor Categories</a>
                <a href="{{ route('lookups.index', ['tab' => 'shifts']) }}" class="tab-btn {{ $tab === 'shifts' ? 'active' : '' }}">Shifts & Schedules</a>
                <a href="{{ route('lookups.index', ['tab' => 'floors']) }}" class="tab-btn {{ $tab === 'floors' ? 'active' : '' }}">Building Floors</a>
            </div>

            <div class="table-container">
                @if ($tab === 'purposes')
                    <table>
                        <thead>
                            <tr>
                                <th>Purpose Name</th>
                                <th>Status</th>
                                <th style="text-align:end;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($visitPurposes as $p)
                                <tr>
                                    <td><strong>{{ $p->name }}</strong></td>
                                    <td><span class="chip {{ $p->status === 'active' ? 'chip-good' : 'chip-muted' }}">{{ ucfirst($p->status) }}</span></td>
                                    <td style="text-align:end;"><button class="btn" onclick="openLookupModal('purpose', {{ json_encode($p) }})" style="padding:4px 8px;font-size:12px;">Edit</button></td>
                                </tr>
                            @empty
                                <tr><td colspan="3" style="text-align:center;padding:24px;">No visit purposes defined.</td></tr>
                            @endforelse
                        </tbody>
                    </table>

                @elseif ($tab === 'id_types')
                    <table>
                        <thead>
                            <tr>
                                <th>ID Document Type</th>
                                <th>Code</th>
                                <th>Status</th>
                                <th style="text-align:end;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($idTypes as $idType)
                                <tr>
                                    <td><strong>{{ $idType->name }}</strong></td>
                                    <td><code>{{ $idType->code }}</code></td>
                                    <td><span class="chip {{ $idType->status === 'active' ? 'chip-good' : 'chip-muted' }}">{{ ucfirst($idType->status) }}</span></td>
                                    <td style="text-align:end;"><button class="btn" onclick="openLookupModal('id_type', {{ json_encode($idType) }})" style="padding:4px 8px;font-size:12px;">Edit</button></td>
                                </tr>
                            @empty
                                <tr><td colspan="4" style="text-align:center;padding:24px;">No ID document types defined.</td></tr>
                            @endforelse
                        </tbody>
                    </table>

                @elseif ($tab === 'visitor_types')
                    <table>
                        <thead>
                            <tr>
                                <th>Visitor Category</th>
                                <th>Compliance Policies</th>
                                <th>Status</th>
                                <th style="text-align:end;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($visitorTypes as $vt)
                                <tr>
                                    <td><strong>{{ $vt->name }}</strong></td>
                                    <td>
                                        <div style="display:flex;gap:6px;">
                                            @if($vt->requires_approval)<span class="chip chip-good">Approval Req</span>@endif
                                            @if($vt->requires_nda)<span class="chip chip-good">NDA Req</span>@endif
                                            @if($vt->requires_escort)<span class="chip chip-good">Escort Req</span>@endif
                                        </div>
                                    </td>
                                    <td><span class="chip {{ $vt->status === 'active' ? 'chip-good' : 'chip-muted' }}">{{ ucfirst($vt->status) }}</span></td>
                                    <td style="text-align:end;"><button class="btn" onclick="openLookupModal('visitor_type', {{ json_encode($vt) }})" style="padding:4px 8px;font-size:12px;">Edit</button></td>
                                </tr>
                            @empty
                                <tr><td colspan="4" style="text-align:center;padding:24px;">No visitor categories defined.</td></tr>
                            @endforelse
                        </tbody>
                    </table>

                @elseif ($tab === 'shifts')
                    <table>
                        <thead>
                            <tr>
                                <th>Shift Name</th>
                                <th>Working Hours</th>
                                <th>Grace Time</th>
                                <th>Status</th>
                                <th style="text-align:end;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($shifts as $s)
                                <tr>
                                    <td><strong>{{ $s->name }}</strong></td>
                                    <td>{{ \Carbon\Carbon::parse($s->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($s->end_time)->format('H:i') }}</td>
                                    <td>{{ $s->grace_minutes ?? 0 }} mins</td>
                                    <td><span class="chip {{ $s->status === 'active' ? 'chip-good' : 'chip-muted' }}">{{ ucfirst($s->status) }}</span></td>
                                    <td style="text-align:end;"><button class="btn" onclick="openLookupModal('shift', {{ json_encode($s) }})" style="padding:4px 8px;font-size:12px;">Edit</button></td>
                                </tr>
                            @empty
                                <tr><td colspan="5" style="text-align:center;padding:24px;">No shift schedules defined.</td></tr>
                            @endforelse
                        </tbody>
                    </table>

                @elseif ($tab === 'floors')
                    <table>
                        <thead>
                            <tr>
                                <th>Floor Name</th>
                                <th>Floor Code</th>
                                <th>Building</th>
                                <th>Capacity</th>
                                <th>Status</th>
                                <th style="text-align:end;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($floors as $f)
                                <tr>
                                    <td><strong>{{ $f->name }}</strong></td>
                                    <td><code>{{ $f->code }}</code></td>
                                    <td>{{ $f->building->name ?? 'Main Tower' }}</td>
                                    <td>{{ $f->capacity ?? 'Unlimited' }}</td>
                                    <td><span class="chip {{ $f->status === 'active' ? 'chip-good' : 'chip-muted' }}">{{ ucfirst($f->status) }}</span></td>
                                    <td style="text-align:end;"><button class="btn" onclick="openLookupModal('floor', {{ json_encode($f) }})" style="padding:4px 8px;font-size:12px;">Edit</button></td>
                                </tr>
                            @empty
                                <tr><td colspan="6" style="text-align:center;padding:24px;">No building floors defined.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                @endif
            </div>
        </main>
    </div>

    <!-- Dynamic Modal -->
    <div id="lookupModal" class="modal-root">
        <div class="modal-backdrop" onclick="closeLookupModal()"></div>
        <div class="modal-panel">
            <div class="modal-head">
                <h3 id="modalTitle">Manage Item</h3>
                <button class="btn" onclick="closeLookupModal()">✕</button>
            </div>
            <form id="lookupForm" method="POST" action="">
                @csrf
                <input type="hidden" id="lookup_method" name="_method" value="POST">
                <div class="modal-body" id="modalFormContent">
                    <!-- Dynamic form fields populated via JS based on current tab -->
                </div>
                <div class="modal-foot">
                    <button type="button" class="btn" onclick="closeLookupModal()">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Item</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        const currentTab = "{{ $tab }}";
        const buildings = @json($buildings);

        function openLookupModal(type = currentTab, item = null) {
            const modal = document.getElementById('lookupModal');
            const form = document.getElementById('lookupForm');
            const method = document.getElementById('lookup_method');
            const title = document.getElementById('modalTitle');
            const content = document.getElementById('modalFormContent');

            let html = '';
            if (type === 'purposes' || type === 'purpose') {
                title.innerText = item ? 'Edit Visit Purpose' : 'Add Visit Purpose';
                form.action = item ? '/lookups/purposes/' + item.id : "{{ route('lookups.purposes.store') }}";
                method.value = item ? 'PUT' : 'POST';
                html = `
                    <div class="field">
                        <label>Purpose Name *</label>
                        <input type="text" name="name" required value="${item ? item.name : ''}" placeholder="Business Meeting">
                    </div>
                    <div class="field">
                        <label>Status *</label>
                        <select name="status" required>
                            <option value="active" ${!item || item.status==='active' ? 'selected':''}>Active</option>
                            <option value="inactive" ${item && item.status==='inactive' ? 'selected':''}>Inactive</option>
                        </select>
                    </div>`;
            } else if (type === 'id_types' || type === 'id_type') {
                title.innerText = item ? 'Edit ID Document Type' : 'Add ID Document Type';
                form.action = item ? '/lookups/id-types/' + item.id : "{{ route('lookups.id_types.store') }}";
                method.value = item ? 'PUT' : 'POST';
                html = `
                    <div class="field">
                        <label>Document Name *</label>
                        <input type="text" name="name" required value="${item ? item.name : ''}" placeholder="Emirates ID">
                    </div>
                    <div class="field">
                        <label>Type Code *</label>
                        <input type="text" name="code" required value="${item ? item.code : ''}" placeholder="EID">
                    </div>
                    <div class="field">
                        <label>Status *</label>
                        <select name="status" required>
                            <option value="active" ${!item || item.status==='active' ? 'selected':''}>Active</option>
                            <option value="inactive" ${item && item.status==='inactive' ? 'selected':''}>Inactive</option>
                        </select>
                    </div>`;
            } else if (type === 'visitor_types' || type === 'visitor_type') {
                title.innerText = item ? 'Edit Visitor Category' : 'Add Visitor Category';
                form.action = item ? '/lookups/visitor-types/' + item.id : "{{ route('lookups.visitor_types.store') }}";
                method.value = item ? 'PUT' : 'POST';
                html = `
                    <div class="field">
                        <label>Category Name *</label>
                        <input type="text" name="name" required value="${item ? item.name : ''}" placeholder="Contractor">
                    </div>
                    <div class="field">
                        <label>Status *</label>
                        <select name="status" required>
                            <option value="active" ${!item || item.status==='active' ? 'selected':''}>Active</option>
                            <option value="inactive" ${item && item.status==='inactive' ? 'selected':''}>Inactive</option>
                        </select>
                    </div>
                    <div class="field">
                        <label>Compliance Rules</label>
                        <label class="checkbox-item"><input type="checkbox" name="requires_approval" value="1" ${item && item.requires_approval ? 'checked':''}> Require Approval</label>
                        <label class="checkbox-item"><input type="checkbox" name="requires_nda" value="1" ${item && item.requires_nda ? 'checked':''}> Require NDA</label>
                        <label class="checkbox-item"><input type="checkbox" name="requires_escort" value="1" ${item && item.requires_escort ? 'checked':''}> Require Escort</label>
                    </div>`;
            } else if (type === 'shifts' || type === 'shift') {
                title.innerText = item ? 'Edit Shift Schedule' : 'Add Shift Schedule';
                form.action = item ? '/lookups/shifts/' + item.id : "{{ route('lookups.shifts.store') }}";
                method.value = item ? 'PUT' : 'POST';
                html = `
                    <div class="field">
                        <label>Shift Name *</label>
                        <input type="text" name="name" required value="${item ? item.name : ''}" placeholder="Day Shift">
                    </div>
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;">
                        <div class="field">
                            <label>Start Time *</label>
                            <input type="time" name="start_time" required value="${item ? item.start_time : '08:00'}">
                        </div>
                        <div class="field">
                            <label>End Time *</label>
                            <input type="time" name="end_time" required value="${item ? item.end_time : '17:00'}">
                        </div>
                    </div>
                    <div class="field">
                        <label>Grace Minutes</label>
                        <input type="number" name="grace_minutes" value="${item ? item.grace_minutes : 15}">
                    </div>
                    <div class="field">
                        <label>Status *</label>
                        <select name="status" required>
                            <option value="active" ${!item || item.status==='active' ? 'selected':''}>Active</option>
                            <option value="inactive" ${item && item.status==='inactive' ? 'selected':''}>Inactive</option>
                        </select>
                    </div>`;
            } else if (type === 'floors' || type === 'floor') {
                title.innerText = item ? 'Edit Building Floor' : 'Add Building Floor';
                form.action = item ? '/lookups/floors/' + item.id : "{{ route('lookups.floors.store') }}";
                method.value = item ? 'PUT' : 'POST';
                let bOpts = buildings.map(b => `<option value="${b.id}" ${item && item.building_id==b.id ? 'selected':''}>${b.name}</option>`).join('');
                html = `
                    <div class="field">
                        <label>Floor Name *</label>
                        <input type="text" name="name" required value="${item ? item.name : ''}" placeholder="Floor 12">
                    </div>
                    <div class="field">
                        <label>Floor Code *</label>
                        <input type="text" name="code" required value="${item ? item.code : ''}" placeholder="FL-12">
                    </div>
                    <div class="field">
                        <label>Building Location</label>
                        <select name="building_id"><option value="">-- Main Building --</option>${bOpts}</select>
                    </div>
                    <div class="field">
                        <label>Capacity</label>
                        <input type="number" name="capacity" value="${item ? item.capacity : ''}">
                    </div>
                    <div class="field">
                        <label>Status *</label>
                        <select name="status" required>
                            <option value="active" ${!item || item.status==='active' ? 'selected':''}>Active</option>
                            <option value="inactive" ${item && item.status==='inactive' ? 'selected':''}>Inactive</option>
                        </select>
                    </div>`;
            }

            content.innerHTML = html;
            modal.classList.add('open');
        }
        function closeLookupModal() {
            document.getElementById('lookupModal').classList.remove('open');
        }
    </script>
</body>
</html>
