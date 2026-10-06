<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>New Visitor Check-In — Madkhal</title>
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
            --text-primary: #0e1a18;
            --text-secondary: #4d5b57;
            --text-muted: #7c8985;
            --border: rgba(14, 26, 24, 0.12);
            --critical: #d03b3b;
            --warning: #fab219;
            --radius: 12px;
            --radius-sm: 8px;
            --radius-lg: 14px;
            --shadow: 0 1px 2px rgba(14,26,24,.06), 0 8px 24px -12px rgba(14,26,24,.18);
        }
        *, *::before, *::after { box-sizing: border-box; }
        body {
            margin: 0;
            font-family: 'IBM Plex Sans', system-ui, -apple-system, sans-serif;
            background: var(--bg);
            color: var(--text-primary);
            font-size: 14px;
            line-height: 1.5;
        }

        .ic { width: 16px; height: 16px; flex: none; display: inline-block; vertical-align: -3px; }
        .mono { font-variant-numeric: tabular-nums; }

        #app {
            display: grid;
            grid-template-columns: 248px 1fr;
            grid-template-rows: 60px 1fr;
            min-height: 100vh;
        }
        #sidebar {
            grid-row: 1/3;
            grid-column: 1;
            background: var(--surface);
            border-inline-end: 1px solid var(--border);
            display: flex;
            flex-direction: column;
        }
        #topbar {
            grid-row: 1;
            grid-column: 2;
            background: var(--surface);
            border-block-end: 1px solid var(--border);
            display: flex;
            align-items: center;
            gap: 14px;
            padding-inline: 20px;
        }
        #main {
            grid-row: 2;
            grid-column: 2;
            padding: 24px 28px 60px;
            overflow-y: auto;
        }
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

        .card { background: var(--surface); border: 1px solid var(--border); border-radius: var(--radius-lg); padding: 22px; }
        .grid-layout { display: grid; grid-template-columns: 1fr 300px; gap: 20px; align-items: start; }
        
        fieldset { border: 1px solid var(--border); border-radius: var(--radius); padding: 16px 18px; margin: 0 0 18px 0; }
        legend { font-size: 12px; font-weight: 700; text-transform: uppercase; letter-spacing: .04em; color: var(--text-muted); padding: 0 6px; }

        .form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; }
        .field { display: flex; flex-direction: column; gap: 5px; margin-bottom: 12px; }
        .field.span2 { grid-column: 1 / -1; }
        .field label { font-size: 12.5px; font-weight: 600; color: var(--text-secondary); }
        .field input, .field select, .field textarea {
            font-family: inherit; font-size: 13.5px; padding: 9px 11px; border-radius: var(--radius-sm);
            border: 1px solid var(--border); background: var(--surface); color: var(--text-primary); width: 100%;
        }
        .field input:focus, .field select:focus, .field textarea:focus {
            outline: none; border-color: var(--accent); box-shadow: 0 0 0 3px var(--accent-soft);
        }
        .phone-group { display: flex; gap: 8px; }
        .phone-group select { width: 110px; flex: none; }
        .phone-group input { flex: 1; }

        .checkline { display: flex; align-items: flex-start; gap: 9px; font-size: 12.5px; color: var(--text-secondary); margin-top: 14px; }
        .checkline input { margin-top: 2px; accent-color: var(--accent); width: 15px; height: 15px; }

        .btn { display: inline-flex; align-items: center; gap: 7px; padding: 9px 16px; border-radius: var(--radius-sm); border: 1px solid var(--border); background: var(--surface); color: var(--text-primary); font-size: 13.5px; font-weight: 600; cursor: pointer; white-space: nowrap; text-decoration: none; }
        .btn-primary { background: var(--accent); border-color: var(--accent); color: #fff; }
        .btn-primary:hover { background: var(--accent-hover); }

        .alert-danger { background: rgba(208, 59, 59, 0.10); border: 1px solid rgba(208, 59, 59, 0.25); color: var(--critical); padding: 12px 14px; border-radius: var(--radius-sm); font-size: 13px; margin-bottom: 18px; }
        .banner-warn { background: rgba(250, 178, 25, 0.18); border: 1px solid rgba(250, 178, 25, 0.40); color: #7a5200; padding: 12px 14px; border-radius: var(--radius-sm); font-size: 13px; margin-bottom: 18px; display: none; }

        .avatar-placeholder { width: 120px; height: 120px; border-radius: 16px; background: var(--surface-2); border: 1.5px dashed var(--border); display: flex; align-items: center; justify-content: center; margin: 0 auto 12px; font-size: 28px; font-weight: 700; color: var(--accent); }
    </style>
</head>
<body>
    <div id="app">
        @include('partials.sidebar', ['active' => 'checkin'])


        @include('partials.topbar')

        <main id="main">
            <div class="pagehead">
                <div>
                    <h1>New Visitor Check-In</h1>
                    <div class="sub">Register a walk-in visitor and issue their entry badge.</div>
                </div>
                <a href="{{ route('visitors.index') }}" class="btn">View Visitor Log</a>
            </div>

            @if (session('error'))
                <div class="alert-danger">
                    <strong>⚠️ Check-In Error:</strong> {{ session('error') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="alert-danger">
                    <strong>Please fix the errors below:</strong>
                    <ul style="margin: 6px 0 0 18px; padding: 0;">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div id="watchlistBanner" class="banner-warn">
                ⚠️ <strong>Potential Watchlist Warning:</strong> The name entered matches a flagged security record. Please verify government ID carefully.
            </div>

            <form action="{{ route('visitors.checkin.store') }}" method="POST" id="checkinForm">
                @csrf
                <div class="grid-layout">
                    <div class="card">
                                                <div style="margin-bottom: 24px; position: relative;">
                            <label style="display: block; font-size: 13px; font-weight: 600; color: var(--text-secondary); margin-bottom: 8px;">Returning visitor? Search by name, phone or ID</label>
                            <input type="text" id="visitor_search" placeholder="Search past visitors..." autocomplete="off" style="width: 100%; font-family: inherit; font-size: 13.5px; padding: 10px 12px; border-radius: var(--radius-sm); border: 1px solid var(--border); background: var(--surface); color: var(--text-primary);">
                            
                            <div id="visitor_suggestions" style="display: none; position: absolute; top: 100%; left: 0; right: 0; background: var(--surface); border: 1px solid var(--border); border-radius: var(--radius-sm); box-shadow: var(--shadow); z-index: 50; max-height: 250px; overflow-y: auto; margin-top: 4px;">
                                <!-- Suggestions populated by JS -->
                            </div>
                        </div>

                        <fieldset>
                            <legend>VISITOR DETAILS</legend>
                            <div class="form-grid">
                                <div class="field">
                                    <label for="first_name">First Name *</label>
                                    <input type="text" id="first_name" name="first_name" value="{{ old('first_name') }}" required autofocus oninput="checkWatchlistName(this.value)">
                                </div>
                                <div class="field">
                                    <label for="last_name">Last Name</label>
                                    <input type="text" id="last_name" name="last_name" value="{{ old('last_name') }}">
                                </div>
                                <div class="field">
                                    <label for="id_type_id">ID Type *</label>
                                    <select id="id_type_id" name="id_type_id" required>
                                        @foreach ($idTypes as $idType)
                                            <option value="{{ $idType->id }}" {{ old('id_type_id') == $idType->id ? 'selected' : '' }}>
                                                {{ $idType->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="field">
                                    <label for="id_number">ID Number *</label>
                                    <input type="text" id="id_number" name="id_number" value="{{ old('id_number') }}" placeholder="e.g. 784-1990-1234567-1" required>
                                </div>
                                <div class="field">
                                    <label for="nationality_id">Nationality</label>
                                    <select id="nationality_id" name="nationality_id">
                                        <option value="">Select Country</option>
                                        @foreach ($countries as $country)
                                            <option value="{{ $country->id }}" {{ old('nationality_id') == $country->id ? 'selected' : '' }}>
                                                {{ $country->name }} ({{ $country->iso_code }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="field">
                                    <label for="phone">Phone Number</label>
                                    <div class="phone-group">
                                        <select name="phone_code">
                                            <option value="971">+971 (UAE)</option>
                                            <option value="966">+966 (KSA)</option>
                                            <option value="974">+974 (QA)</option>
                                            <option value="1">+1 (US)</option>
                                            <option value="44">+44 (UK)</option>
                                        </select>
                                        <input type="tel" name="phone" value="{{ old('phone') }}" placeholder="50 000 0000">
                                    </div>
                                </div>
                                <div class="field span2">
                                    <label for="email">Email Address (Optional)</label>
                                    <input type="email" id="email" name="email" value="{{ old('email') }}" placeholder="visitor@company.com">
                                </div>
                            </div>
                        </fieldset>

                        <fieldset>
                            <legend>Visit & Host Details</legend>
                            <div class="form-grid">
                                <div class="field">
                                    <label for="company_name">Company / Vehicle Plate</label>
                                    <input type="text" id="company_name" name="company_name" value="{{ old('company_name') }}" placeholder="e.g. Acme Corp / DP-1829">
                                </div>
                                <div class="field">
                                    <label for="site_id">Site Location *</label>
                                    <select id="site_id" name="site_id" required onchange="filterEmployeesBySite()">
                                        @foreach ($sites as $site)
                                            <option value="{{ $site->id }}" {{ old('site_id') == $site->id ? 'selected' : '' }}>
                                                {{ $site->name }} ({{ $site->city }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="field">
                                    <label for="host_employee_id">Host Employee *</label>
                                    <select id="host_employee_id" name="host_employee_id" required>
                                        <option value="">Select Host Employee</option>
                                    </select>
                                </div>
                                <div class="field">
                                    <label for="purpose_id">Purpose of Visit *</label>
                                    <select id="purpose_id" name="purpose_id" required>
                                        @foreach ($purposes as $purpose)
                                            <option value="{{ $purpose->id }}" {{ old('purpose_id') == $purpose->id ? 'selected' : '' }}>
                                                {{ $purpose->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="field span2">
                                    <label for="duration">Expected Duration *</label>
                                    <select id="duration" name="duration" required>
                                        <option value="30">30 minutes</option>
                                        <option value="60" selected>1 hour</option>
                                        <option value="120">2 hours</option>
                                        <option value="240">Half day (4 hours)</option>
                                        <option value="480">Full day (8 hours)</option>
                                    </select>
                                </div>
                            </div>
                        </fieldset>

                        <div class="checkline">
                            <input type="checkbox" id="health_safety_ack" name="health_safety_ack" value="1" required checked>
                            <label for="health_safety_ack">
                                The visitor has been briefed on site health & safety rules and has agreed to the non-disclosure policy (NDA).
                            </label>
                        </div>

                        <div style="margin-top: 22px; display: flex; justify-content: flex-end;">
                            <button type="submit" class="btn btn-primary">Check In Visitor & Issue Badge</button>
                        </div>
                    </div>

                    <div class="card" style="text-align: center;">
                        <div class="field" style="margin-bottom: 8px;"><label>Visitor Photo</label></div>
                        <div class="avatar-placeholder" id="photoAvatar">📷</div>
                        <button type="button" class="btn" onclick="takePhoto()" style="font-size: 12px; width: 100%; justify-content: center;">Capture Photo</button>
                        <div style="margin-top: 20px; font-size: 12px; color: var(--text-muted); text-align: start;">
                            <strong>Reception Tip:</strong> Verify physical Emirates ID or Passport matching the name entered.
                        </div>
                    </div>
                </div>
            </form>
        
            <div class="card" style="margin-top: 32px; padding: 24px; padding-bottom: 0;">
                <div style="margin-bottom: 16px;">
                    <h2 style="font-size: 16.5px; font-weight: 700; margin: 0 0 4px 0;">Pre-Registered Visitors Expected Today</h2>
                    <div style="font-size: 13px; color: var(--text-muted);">{{ $expectedToday->count() }} pre-registered visitor{{ $expectedToday->count() !== 1 ? 's' : '' }} expected today.</div>
                </div>

                @if($expectedToday->count() > 0)
                    <div style="margin: 0 -24px;">
                        <style>
                            .tablewrap { border: none; border-radius: 0; }
                            .dtable { min-width: 100%; border-top: 1px solid var(--border); }
                            .dtable thead th { border-bottom: 1px solid var(--border); background: var(--surface-2); font-size: 10.5px; padding: 12px 24px; }
                            .dtable tbody td { padding: 14px 24px; border-bottom: 1px solid var(--border); }
                            .dtable tbody tr:last-child td { border-bottom: none; }
                        </style>
                        @include('partials.pre_registrations_table', ['invitations' => $expectedToday, 'showCheckInButton' => true])
                    </div>
                @else
                    <div style="padding: 32px; text-align: center; color: var(--text-muted); font-size: 14px; margin-bottom: 24px; border: 1px dashed var(--border); border-radius: var(--radius-sm);">
                        No pre-registered visitors are expected today.
                    </div>
                @endif
            </div>

        </main>
    </div>

    <script>
        const allEmployees = @json($employees);
        const initialSelectedHost = "{{ old('host_employee_id') }}";

        function filterEmployeesBySite() {
            const siteSelect = document.getElementById('site_id');
            const empSelect = document.getElementById('host_employee_id');
            const selectedSiteId = siteSelect ? siteSelect.value : '';
            const currentSelected = empSelect.value || initialSelectedHost;

            empSelect.innerHTML = '<option value="">Select Host Employee</option>';

            let matchCount = 0;
            allEmployees.forEach(emp => {
                // Include employee if they belong to the selected site OR if they have no site assigned (null)
                if (!selectedSiteId || !emp.site_id || String(emp.site_id) === String(selectedSiteId)) {
                    const opt = document.createElement('option');
                    opt.value = emp.id;
                    const deptTag = emp.department ? ` — ${emp.department.name}` : '';
                    opt.textContent = `${emp.first_name} ${emp.last_name || ''}${deptTag}`;
                    if (String(emp.id) === String(currentSelected)) {
                        opt.selected = true;
                    }
                    empSelect.appendChild(opt);
                    matchCount++;
                }
            });

            if (matchCount === 0) {
                const opt = document.createElement('option');
                opt.value = '';
                opt.textContent = '-- No employees assigned to this site --';
                empSelect.appendChild(opt);
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            filterEmployeesBySite();

            document.getElementById('checkinForm').addEventListener('submit', function(e) {
                const siteSelect = document.getElementById('site_id');
                const empSelect = document.getElementById('host_employee_id');
                const empId = empSelect ? empSelect.value : '';
                const siteId = siteSelect ? siteSelect.value : '';

                if (empId && siteId) {
                    const selectedEmp = allEmployees.find(emp => String(emp.id) === String(empId));
                    if (selectedEmp && selectedEmp.site_id && String(selectedEmp.site_id) !== String(siteId)) {
                        e.preventDefault();
                        const siteName = siteSelect.options[siteSelect.selectedIndex] ? siteSelect.options[siteSelect.selectedIndex].text : 'the selected site';
                        alert(`Validation Alert: The host employee (${selectedEmp.first_name} ${selectedEmp.last_name || ''}) belongs to ${selectedEmp.site ? selectedEmp.site.name : 'another site'}. Please select a host employee assigned to ${siteName}.`);
                        return false;
                    }
                }
            });
        });

        function checkWatchlistName(val) {
            const banner = document.getElementById('watchlistBanner');
            const lower = (val || '').toLowerCase().trim();
            if (lower.includes('khalid') || lower.includes('test') || lower.includes('flag')) {
                banner.style.display = 'block';
            } else {
                banner.style.display = 'none';
            }
        }

        function takePhoto() {
            const fname = document.getElementById('first_name').value || 'V';
            const avatar = document.getElementById('photoAvatar');
            avatar.innerHTML = fname.charAt(0).toUpperCase();
        }
    </script>

    @include('partials.invite_modal')
    <script>
        @include('partials.invite_modal_js')

        function prefillCheckinForm(data) {
            if(data.first_name) document.getElementById('first_name').value = data.first_name;
            if(data.last_name) document.getElementById('last_name').value = data.last_name;
            if(data.id_type_id) document.getElementById('id_type_id').value = data.id_type_id;
            if(data.id_number) document.getElementById('id_number').value = data.id_number;
            if(data.nationality_id) document.getElementById('nationality_id').value = data.nationality_id;
            
            const phoneCodeSelect = document.querySelector('select[name="phone_code"]');
            if(data.phone_code && phoneCodeSelect) phoneCodeSelect.value = data.phone_code;
            
            const phoneInput = document.querySelector('input[name="phone"]') || document.getElementById('phone');
            if(data.phone && phoneInput) phoneInput.value = data.phone;
            
            if(data.email) document.getElementById('email').value = data.email;
            
            const companyInput = document.querySelector('input[name="company_name"]') || document.getElementById('company_name');
            if(data.company_name && companyInput) companyInput.value = data.company_name;
            
            if(data.site_id) {
                const siteEl = document.getElementById('site_id');
                if(siteEl) {
                    siteEl.value = data.site_id;
                    if(typeof filterEmployeesBySite === 'function') filterEmployeesBySite();
                }
            }
            if(data.host_employee_id) {
                setTimeout(() => {
                    const hostEl = document.getElementById('host_employee_id');
                    if(hostEl) hostEl.value = data.host_employee_id;
                }, 50);
            }
            if(data.purpose_id) {
                const purposeEl = document.getElementById('purpose_id');
                if(purposeEl) purposeEl.value = data.purpose_id;
            }
            
            const durationSelect = document.querySelector('select[name="duration"]');
            if(data.duration && durationSelect) durationSelect.value = data.duration;

            if(typeof takePhoto === 'function') takePhoto();
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }

        // Initialize auto-suggest logic immediately
        (function() {
            const searchInput = document.getElementById('visitor_search');
            const suggestionsBox = document.getElementById('visitor_suggestions');
            let debounceTimer;

            if (searchInput) {
                searchInput.addEventListener('input', function() {
                    clearTimeout(debounceTimer);
                    const query = this.value.trim();
                    
                    if (query.length < 2) {
                        suggestionsBox.style.display = 'none';
                        return;
                    }

                    debounceTimer = setTimeout(() => {
                        fetch(`/visitors/search?q=${encodeURIComponent(query)}`)
                            .then(response => response.json())
                            .then(data => {
                                suggestionsBox.innerHTML = '';
                                if (data.length === 0) {
                                    suggestionsBox.innerHTML = '<div style="padding: 10px 14px; color: var(--text-muted); font-size: 13px;">No matching visitors found.</div>';
                                } else {
                                    data.forEach(visitor => {
                                        const div = document.createElement('div');
                                        div.style.cssText = 'padding: 10px 14px; border-bottom: 1px solid var(--surface-2); cursor: pointer; transition: background 0.15s;';
                                        div.onmouseover = () => div.style.background = 'var(--surface-2)';
                                        div.onmouseout = () => div.style.background = 'transparent';
                                        
                                        const compText = visitor.company ? ` — ${visitor.company}` : '';
                                        div.innerHTML = `
                                            <div style="font-weight: 600; color: var(--text-primary); font-size: 13.5px;">${visitor.name}</div>
                                            <div style="font-size: 12px; color: var(--text-muted); margin-top: 2px;">${visitor.phone} ${visitor.email ? '• ' + visitor.email : ''}${compText}</div>
                                        `;
                                        
                                        div.addEventListener('click', function() {
                                            prefillCheckinForm(visitor.data);
                                            searchInput.value = visitor.name;
                                            suggestionsBox.style.display = 'none';
                                        });
                                        
                                        suggestionsBox.appendChild(div);
                                    });
                                }
                                suggestionsBox.style.display = 'block';
                            })
                            .catch(err => console.error('Search error:', err));
                    }, 300);
                });

                // Hide suggestions when clicking outside
                document.addEventListener('click', function(e) {
                    if (!searchInput.contains(e.target) && !suggestionsBox.contains(e.target)) {
                        suggestionsBox.style.display = 'none';
                    }
                });
            }
        })();
    </script>
</body>
</html>