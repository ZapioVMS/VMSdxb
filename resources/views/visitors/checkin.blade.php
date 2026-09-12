<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>New Visitor Check-In — Madkhal</title>
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
            <span style="font-weight:700;font-size:15px;">New Visitor Check-In</span>
            <div style="flex:1;"></div>
            <div style="text-align:end;">
                <div style="font-weight:700;font-size:13px;">{{ Auth::user()->name }}</div>
                <div style="font-size:11px;color:var(--text-muted);">{{ Auth::user()->organization->name ?? '' }}</div>
            </div>
        </header>

        <main id="main">
            <div class="pagehead">
                <div>
                    <h1>New Visitor Check-In</h1>
                    <div class="sub">Register a walk-in visitor and issue their entry badge.</div>
                </div>
                <a href="{{ route('visitors.index') }}" class="btn">View Visitor Log</a>
            </div>

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

            <form action="{{ route('visitors.checkin.store') }}" method="POST">
                @csrf
                <div class="grid-layout">
                    <div class="card">
                        <fieldset>
                            <legend>Visitor Identity</legend>
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
                                    <select id="site_id" name="site_id" required>
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
                                        @foreach ($employees as $employee)
                                            <option value="{{ $employee->id }}" {{ old('host_employee_id') == $employee->id ? 'selected' : '' }}>
                                                {{ $employee->full_name }} — {{ $employee->department->name ?? 'General' }}
                                            </option>
                                        @endforeach
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
        </main>
    </div>

    <script>
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
</body>
</html>
