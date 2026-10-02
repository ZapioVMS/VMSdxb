<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Settings — Zapio VMS</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=IBM+Plex+Sans:wght@400;500;600;700&display=swap">
    <style>
        :root {
            --accent: #2E9E5B;
            --accent-hover: #26894e;
            --accent-soft: rgba(46, 158, 91, 0.12);
            --bg: #f4f7f6;
            --surface: #ffffff;
            --surface-2: #f8faf9;
            --surface-3: #eef2f1;
            --text-primary: #0e1a18;
            --text-secondary: #4d5b57;
            --text-muted: #7c8985;
            --border: rgba(14, 26, 24, 0.12);
            --good: #22a35a;
            --critical: #d03b3b;
            --radius: 12px;
            --radius-sm: 8px;
            --radius-lg: 14px;
            --shadow: 0 1px 2px rgba(14,26,24,.06), 0 8px 24px -12px rgba(14,26,24,.18);
        }

        .toggle-switch { position: relative; display: inline-block; width: 44px; height: 24px; flex-shrink: 0; }
        .toggle-switch input { opacity: 0; width: 0; height: 0; }
        .toggle-slider { position: absolute; cursor: pointer; top: 0; left: 0; right: 0; bottom: 0; background-color: #cbd5e1; transition: .3s; border-radius: 24px; }
        .toggle-slider:before { position: absolute; content: ""; height: 18px; width: 18px; left: 3px; bottom: 3px; background-color: white; transition: .3s; border-radius: 50%; }
        .toggle-switch input:checked + .toggle-slider { background-color: var(--accent); }
        .toggle-switch input:checked + .toggle-slider:before { transform: translateX(20px); }
        *, *::before, *::after { box-sizing: border-box; }
        body { margin: 0; font-family: 'IBM Plex Sans', system-ui, -apple-system, sans-serif; background: var(--bg); color: var(--text-primary); font-size: 14px; }
        .ic { width: 16px; height: 16px; flex: none; display: inline-block; vertical-align: -3px; }
        #app { display: grid; grid-template-columns: 248px 1fr; grid-template-rows: 60px 1fr; min-height: 100vh; }
        #sidebar { grid-row: 1/3; grid-column: 1; background: var(--surface); border-inline-end: 1px solid var(--border); display: flex; flex-direction: column; }
        #topbar { grid-row: 1; grid-column: 2; background: var(--surface); border-block-end: 1px solid var(--border); display: flex; align-items: center; gap: 14px; padding-inline: 20px; }
        #main { grid-row: 2; grid-column: 2; padding: 24px 28px 60px; overflow-y: auto; }

        .brand { display: flex; align-items: center; gap: 10px; padding: 16px 18px; border-block-end: 1px solid var(--border); }
        .brand-logo { height: 38px; width: auto; max-width: 100%; display: block; }
        
        nav.navlist { padding: 10px; display: flex; flex-direction: column; gap: 2px; flex: 1; }
        .navlabel { font-size: 10.5px; text-transform: uppercase; letter-spacing: .06em; color: var(--text-muted); padding: 14px 10px 6px; }
        .navitem { display: flex; align-items: center; gap: 11px; padding: 9px 10px; border-radius: var(--radius-sm); color: var(--text-secondary); cursor: pointer; font-size: 13.5px; font-weight: 500; border: none; background: none; width: 100%; text-align: start; text-decoration: none; }
        .navitem:hover { background: var(--surface-2); color: var(--text-primary); }
        .navitem.active { background: var(--accent-soft); color: var(--accent); font-weight: 600; }
        .navitem .ic { width: 18px; height: 18px; flex: none; }

        .pagehead { display: flex; align-items: center; justify-content: space-between; gap: 16px; margin-bottom: 22px; }
        .pagehead h1 { font-size: 22px; font-weight: 700; margin: 0; }
        .pagehead-actions { display: flex; align-items: center; gap: 10px; }

        .btn { display: inline-flex; align-items: center; justify-content: center; gap: 7px; padding: 8px 16px; border-radius: var(--radius-sm); border: 1px solid var(--border); background: var(--surface); color: var(--text-primary); font-size: 13.5px; font-weight: 600; cursor: pointer; text-decoration: none; transition: all .15s ease; font-family: inherit; }
        .btn:hover { background: var(--surface-2); }
        .btn-primary { background: var(--accent); border-color: var(--accent); color: #fff; }
        .btn-primary:hover { background: var(--accent-hover); border-color: var(--accent-hover); }
        .btn-sm { padding: 5px 11px; font-size: 12.5px; border-radius: 6px; }
        .btn-ghost { border-color: transparent; background: transparent; color: var(--text-muted); }
        .btn-ghost:hover { background: var(--surface-2); color: var(--text-primary); }
        .btn-danger { background: rgba(208, 59, 59, 0.1); border-color: rgba(208, 59, 59, 0.25); color: var(--critical); }
        .btn-danger:hover { background: var(--critical); color: #fff; }

        /* Settings layout & left nav */
        .settings-layout { display: grid; grid-template-columns: 235px minmax(0, 1fr); gap: 22px; align-items: start; }
        @media (max-width: 960px) { .settings-layout { grid-template-columns: 1fr; } }
        .settings-nav-card { background: var(--surface); border: 1px solid var(--border); border-radius: var(--radius-lg); padding: 8px; box-shadow: var(--shadow); position: sticky; top: 16px; }
        .settings-nav-card .navlist { padding: 0; overflow: visible; display: flex; flex-direction: column; gap: 2px; }
        .settings-nav-card .navlabel { font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: .06em; color: var(--text-muted); padding: 12px 10px 5px; }
        .settings-nav-card .navitem { display: flex; align-items: center; gap: 10px; padding: 9px 12px; border-radius: var(--radius-sm); color: var(--text-secondary); font-size: 13.5px; font-weight: 500; cursor: pointer; border: none; background: transparent; width: 100%; text-align: start; text-decoration: none; transition: all .15s; }
        .settings-nav-card .navitem:hover { background: var(--surface-2); color: var(--text-primary); }
        .settings-nav-card .navitem.active { background: var(--accent-soft); color: var(--accent); font-weight: 600; }
        .settings-nav-card .navitem .ic { width: 17px; height: 17px; flex: none; }

        /* Cards & Form fields */
        .card { background: var(--surface); border: 1px solid var(--border); border-radius: var(--radius); padding: 22px; margin-bottom: 18px; box-shadow: var(--shadow); }
        .section-title { font-size: 15px; font-weight: 700; color: var(--text-primary); margin-bottom: 4px; }
        .section-sub, .cell-sub { font-size: 12px; color: var(--text-muted); margin-bottom: 8px; }
        .form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-top: 12px; }
        @media (max-width: 800px) { .form-grid { grid-template-columns: 1fr; } }
        .field { display: flex; flex-direction: column; gap: 6px; margin-bottom: 14px; }
        .field label { font-size: 12.5px; font-weight: 600; color: var(--text-secondary); }
        .field input, .field select, .field textarea { font-family: inherit; font-size: 13.5px; padding: 8px 12px; border-radius: var(--radius-sm); border: 1px solid var(--border); background: var(--surface); color: var(--text-primary); width: 100%; transition: border-color .15s; }
        .field input:focus, .field select:focus, .field textarea:focus { outline: none; border-color: var(--accent); box-shadow: 0 0 0 3px var(--accent-soft); }

        /* Grid for cards (departments, branches, etc.) */
        .grid { display: grid; gap: 16px; }
        .grid-2 { grid-template-columns: repeat(2, minmax(0, 1fr)); }
        .grid-3 { grid-template-columns: repeat(3, minmax(0, 1fr)); }
        @media (max-width: 1100px) { .grid-3 { grid-template-columns: repeat(2, minmax(0, 1fr)); } }
        @media (max-width: 750px) { .grid-3, .grid-2 { grid-template-columns: 1fr; } }
        .countpill { display: inline-flex; align-items: center; padding: 2px 9px; border-radius: 99px; font-size: 11.5px; font-weight: 700; background: var(--accent-soft); color: var(--accent); }
        .tagchips { display: flex; gap: 6px; flex-wrap: wrap; }
        .tagchip { display: inline-flex; align-items: center; padding: 4px 10px; border-radius: 6px; font-size: 11.5px; font-weight: 600; background: var(--surface-2); border: 1px solid var(--border); color: var(--text-secondary); }

        /* Tag Input for Floor Areas */
        .tag-input-container { display: flex; flex-wrap: wrap; align-items: center; gap: 6px; padding: 6px 10px; border-radius: var(--radius-sm); border: 1px solid var(--border); background: var(--surface); min-height: 42px; cursor: text; transition: border-color .15s; }
        .tag-input-container:focus-within { border-color: var(--accent); box-shadow: 0 0 0 3px var(--accent-soft); }
        .tag-input-container input { border: none !important; outline: none !important; box-shadow: none !important; background: transparent !important; padding: 3px 0 !important; font-size: 13px !important; min-width: 140px; flex: 1; color: var(--text-primary); }
        .tagchip-item { display: inline-flex; align-items: center; gap: 5px; background: var(--surface-2); border: 1px solid var(--border); color: var(--text-primary); padding: 3px 8px; border-radius: 6px; font-size: 12px; font-weight: 600; }
        .tagchip-item.is-meeting-room { background: var(--accent-soft); border-color: rgba(16, 185, 129, 0.3); color: var(--accent); }
        .tagchip-remove { cursor: pointer; font-size: 13px; opacity: 0.6; line-height: 1; margin-left: 2px; }
        .tagchip-remove:hover { opacity: 1; color: #ef4444; }
        .suggestions-dropdown { position: relative; margin-top: 4px; max-height: 160px; overflow-y: auto; border: 1px solid var(--border); border-radius: var(--radius-sm); background: var(--surface); box-shadow: 0 6px 16px rgba(0,0,0,.08); z-index: 50; }
        .suggestion-item { padding: 8px 12px; font-size: 12.5px; cursor: pointer; display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid var(--border); }
        .suggestion-item:last-child { border-bottom: none; }
        .suggestion-item:hover { background: var(--surface-2); }

        /* Switches */
        .switchrow { display: flex; align-items: center; justify-content: space-between; gap: 12px; padding: 12px 0; border-block-end: 1px solid var(--border); }
        .switchrow:last-child { border-block-end: none; }
        .switchrow .sr-label { font-size: 13px; font-weight: 600; }
        .switchrow .sr-sub { font-size: 11.5px; color: var(--text-muted); margin-top: 2px; }
        .switch { position: relative; width: 38px; height: 21px; flex: none; }
        .switch input { opacity: 0; width: 0; height: 0; position: absolute; }
        .switch .track { position: absolute; inset: 0; background: var(--surface-3); border-radius: 99px; border: 1px solid var(--border); transition: background .15s; cursor: pointer; }
        .switch .thumb { position: absolute; top: 2.5px; left: 3px; width: 14px; height: 14px; border-radius: 99px; background: #fff; box-shadow: 0 1px 2px rgba(0,0,0,.3); transition: transform .15s; }
        .switch input:checked + .track { background: var(--accent); border-color: var(--accent); }
        .switch input:checked + .track .thumb { transform: translateX(17px); }

        /* Chip toggles for days / multi-select */
        .chipset { display: flex; gap: 8px; flex-wrap: wrap; margin-top: 8px; }
        .chip-toggle { display: inline-flex; align-items: center; gap: 6px; padding: 7px 14px; border-radius: 99px; border: 1.5px solid var(--border); background: var(--surface); font-size: 12.5px; font-weight: 600; cursor: pointer; color: var(--text-secondary); font-family: inherit; transition: all .15s; user-select: none; }
        .chip-toggle:hover { background: var(--surface-2); border-color: var(--text-muted); }
        .chip-toggle.active, .chip-toggle:has(input:checked) { border-color: var(--accent); background: var(--accent-soft); color: var(--accent); }
        .chip-toggle .ic { width: 13px; height: 13px; }
        .chip-toggle input { display: none; }

        /* Chips */
        .chip { display: inline-flex; align-items: center; padding: 3px 9px; border-radius: 99px; font-size: 11.5px; font-weight: 600; }
        .chip-good { background: rgba(34, 163, 90, 0.14); color: var(--good); }
        .chip-muted { background: var(--surface-3); color: var(--text-muted); }

        /* Avatar squares */
        .avatar-sq { width: 38px; height: 38px; border-radius: 10px; background: var(--surface-3); display: flex; align-items: center; justify-content: center; color: var(--text-secondary); flex: none; }
        .icon-tint-s1 { background: rgba(46, 158, 91, 0.12); color: #2E9E5B; }
        .icon-tint-s2 { background: rgba(59, 130, 246, 0.12); color: #3B82F6; }
        .icon-tint-s3 { background: rgba(139, 92, 246, 0.12); color: #8B5CF6; }
        .icon-tint-s4 { background: rgba(239, 68, 68, 0.12); color: #EF4444; }
        .icon-tint-s5 { background: rgba(245, 158, 11, 0.12); color: #F59E0B; }

        /* Integration & Device cards */
        .integration-card { display: flex; align-items: flex-start; gap: 14px; padding: 16px; border: 1px solid var(--border); border-radius: var(--radius); background: var(--surface); }
        .integration-status { font-size: 11.5px; font-weight: 600; margin-top: 3px; }
        .integration-status.connected { color: var(--good); }
        .integration-status.not-connected { color: var(--text-muted); }
        .device-card { display: flex; align-items: center; gap: 12px; padding: 12px 14px; border: 1px solid var(--border); border-radius: var(--radius-sm); margin-bottom: 10px; background: var(--surface); }
        .device-dot { width: 8px; height: 8px; border-radius: 99px; background: var(--text-muted); }
        .device-dot.online { background: var(--good); }

        /* Upload box */
        .upload-box { border: 1.5px dashed var(--border); border-radius: var(--radius); padding: 22px; display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 10px; text-align: center; }
        .upload-logo-preview { max-height: 48px; max-width: 200px; object-fit: contain; }
        .color-swatches { display: flex; align-items: center; gap: 8px; flex-wrap: wrap; margin-top: 8px; }
        .color-swatch { width: 32px; height: 32px; border-radius: 8px; border: 2px solid transparent; cursor: pointer; transition: transform .1s; }
        .color-swatch.active { border-color: var(--text-primary); transform: scale(1.1); }

        /* Tab Panes */
        .tab-pane { display: none; }
        .tab-pane.active { display: block; }

        /* Alerts */
        .alert-success { background: rgba(34, 163, 90, 0.14); border: 1px solid rgba(34, 163, 90, 0.3); color: var(--good); padding: 12px 16px; border-radius: var(--radius-sm); font-size: 13.5px; margin-bottom: 18px; display: flex; align-items: center; gap: 8px; }
        .alert-danger { background: rgba(208, 59, 59, 0.14); border: 1px solid rgba(208, 59, 59, 0.3); color: var(--critical); padding: 12px 16px; border-radius: var(--radius-sm); font-size: 13.5px; margin-bottom: 18px; display: flex; align-items: center; gap: 8px; }

        /* Modals */
        .modal-root { display: none; position: fixed; inset: 0; z-index: 100; }
        .modal-root.open { display: block; }
        .modal-backdrop { position: absolute; inset: 0; background: rgba(6,12,11,.5); }
        .modal-panel { position: relative; max-width: 580px; width: calc(100% - 32px); margin: 6vh auto 0; background: var(--surface); border-radius: var(--radius); border: 1px solid var(--border); box-shadow: var(--shadow); overflow: hidden; }
        .modal-head { display: flex; align-items: center; justify-content: space-between; padding: 16px 20px; border-bottom: 1px solid var(--border); }
        .modal-head h3 { margin: 0; font-size: 16px; font-weight: 700; }
        .modal-body { padding: 20px; max-height: 70vh; overflow-y: auto; }
        .modal-foot { display: flex; justify-content: flex-end; gap: 8px; padding: 14px 20px; border-top: 1px solid var(--border); background: var(--surface-2); }
    </style>
</head>
<body>
<div id="app">
    <!-- Main Left Sidebar -->
    @include('partials.sidebar', ['active' => 'settings'])

    <!-- Top Navigation Bar -->
    @include('partials.topbar')

    <main id="main">
        <!-- Success & Error Flash Messages -->
        @if (session('success'))
            <div class="alert-success">
                <svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 6L9 17l-5-5"/></svg>
                <span>{{ session('success') }}</span>
            </div>
        @endif
        @if (session('error'))
            <div class="alert-danger">
                <svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M12 8v4M12 16h.01"/></svg>
                <span>{{ session('error') }}</span>
            </div>
        @endif
        
        @if ($errors->any())
            <div class="alert-danger" style="display: flex; flex-direction: column; align-items: flex-start; gap: 4px;">
                <div style="display: flex; align-items: center; gap: 8px;">
                    <svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M12 8v4M12 16h.01"/></svg>
                    <strong>Please fix the following errors:</strong>
                </div>
                <ul style="margin: 0; padding-left: 28px; list-style-type: disc;">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Page Header with Save and Reset Actions -->
        <div class="pagehead">
            <div>
                <h1>Settings</h1>
            </div>
            <div class="pagehead-actions">
                <button type="button" class="btn" onclick="location.reload();">Reset Defaults</button>
                <button type="button" class="btn btn-primary" onclick="submitActiveTabForm();">
                    <svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 6L9 17l-5-5"/></svg>
                    <span>Save All Changes</span>
                </button>
            </div>
        </div>

        <!-- 2-Column Settings Layout -->
        <div class="settings-layout">
            <!-- Left Submenu Tab Navigation -->
            <div class="settings-nav-card">
                <nav class="navlist">
                    <!-- Top Core Settings -->
                    <button type="button" class="navitem {{ $activeTab === 'branding' ? 'active' : '' }}" onclick="switchTab('branding')">
                        <svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75"><circle cx="13.5" cy="6.5" r=".5"/><circle cx="17.5" cy="10.5" r=".5"/><circle cx="8.5" cy="7.5" r=".5"/><circle cx="6.5" cy="12.5" r=".5"/><path d="M12 2C6.5 2 2 6.5 2 12s4.5 10 10 10c.926 0 1.648-.746 1.648-1.688 0-.437-.18-.835-.437-1.125-.29-.289-.438-.652-.438-1.125a1.64 1.64 0 0 1 1.668-1.668h1.996c3.051 0 5.563-2.512 5.563-5.563C22 6.5 17.5 2 12 2z"/></svg>
                        <span>Branding & Appearance</span>
                    </button>
                    <button type="button" class="navitem {{ $activeTab === 'kiosk' ? 'active' : '' }}" onclick="switchTab('kiosk')">
                        <svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75"><rect width="20" height="14" x="2" y="3" rx="2"/><path d="M12 17v4M8 21h8"/></svg>
                        <span>Kiosk Configuration</span>
                    </button>
                    <button type="button" class="navitem {{ $activeTab === 'general' ? 'active' : '' }}" onclick="switchTab('general')">
                        <svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"/></svg>
                        <span>General</span>
                    </button>
                    <button type="button" class="navitem {{ $activeTab === 'security' ? 'active' : '' }}" onclick="switchTab('security')">
                        <svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                        <span>Security & Access</span>
                    </button>
                    <button type="button" class="navitem {{ $activeTab === 'notifications' ? 'active' : '' }}" onclick="switchTab('notifications')">
                        <svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75"><path d="M6 8a6 6 0 0 1 12 0c0 7 3 9 3 9H3s3-2 3-9"/><path d="M10.3 21a1.94 1.94 0 0 0 3.4 0"/></svg>
                        <span>Notifications</span>
                    </button>
                    <button type="button" class="navitem {{ $activeTab === 'integrations' ? 'active' : '' }}" onclick="switchTab('integrations')">
                        <svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75"><path d="M12 2v6M9 4l3 3 3-3M2 12h6M4 9l3 3-3 3M22 12h-6M20 9l-3 3 3 3M12 22v-6M9 20l3-3 3 3"/></svg>
                        <span>Integrations</span>
                    </button>
                    <button type="button" class="navitem {{ $activeTab === 'privacy' ? 'active' : '' }}" onclick="switchTab('privacy')">
                        <svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75"><rect width="18" height="11" x="3" y="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                        <span>Data & Privacy</span>
                    </button>

                    <!-- MORE Section -->
                    <div class="navlabel">MORE</div>
                    <button type="button" class="navitem {{ $activeTab === 'departments' ? 'active' : '' }}" onclick="switchTab('departments')">
                        <svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75"><path d="M3 21h18M5 21V9l7-5 7 5v12M9 21v-5h6v5M9 12h.01M13 12h.01M9 16h.01M13 16h.01"/></svg>
                        <span>Departments</span>
                    </button>
                    <button type="button" class="navitem {{ $activeTab === 'branches' ? 'active' : '' }}" onclick="switchTab('branches')">
                        <svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75"><path d="M3 21h18M5 21V9l7-5 7 5v12M9 21v-5h6v5"/></svg>
                        <span>Branches</span>
                    </button>
                    <button type="button" class="navitem {{ $activeTab === 'purposes' ? 'active' : '' }}" onclick="switchTab('purposes')">
                        <svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75"><path d="M12 2H2v10l9.29 9.29c.94.94 2.48.94 3.42 0l6.58-6.58c.94-.94.94-2.48 0-3.42L12 2Z"/><circle cx="7" cy="7" r=".5" fill="currentColor"/></svg>
                        <span>Purpose of Visit</span>
                    </button>
                    <button type="button" class="navitem {{ $activeTab === 'visitorTypes' ? 'active' : '' }}" onclick="switchTab('visitorTypes')">
                        <svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                        <span>Visitor Type</span>
                    </button>
                    <button type="button" class="navitem {{ $activeTab === 'idTypes' ? 'active' : '' }}" onclick="switchTab('idTypes')">
                        <svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75"><rect width="20" height="14" x="2" y="5" rx="2"/><line x1="2" x2="22" y1="10" y2="10"/></svg>
                        <span>ID Types</span>
                    </button>
                    <button type="button" class="navitem {{ $activeTab === 'meetingRooms' ? 'active' : '' }}" onclick="switchTab('meetingRooms')">
                        <svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75"><rect width="20" height="14" x="2" y="7" rx="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/></svg>
                        <span>Meeting Rooms</span>
                    </button>
                    <button type="button" class="navitem {{ $activeTab === 'deliveryCarriers' ? 'active' : '' }}" onclick="switchTab('deliveryCarriers')">
                        <svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75"><path d="m7.5 4.27 9 5.15M21 8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16Z"/><path d="m3.3 7 8.7 5 8.7-5M12 22V12"/></svg>
                        <span>Delivery Carriers</span>
                    </button>
                    <button type="button" class="navitem {{ $activeTab === 'packageSizes' ? 'active' : '' }}" onclick="switchTab('packageSizes')">
                        <svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75"><path d="m21 21-6-6m6 6v-4.8m0 4.8h-4.8M3 16.2V21m0 0h4.8M3 21l6-6M21 7.8V3m0 0h-4.8M21 3l-6 6M3 7.8V3m0 0h4.8M3 3l6 6"/></svg>
                        <span>Package Sizes</span>
                    </button>
                    <button type="button" class="navitem {{ $activeTab === 'floors' ? 'active' : '' }}" onclick="switchTab('floors')">
                        <svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75"><path d="m12.83 2.18a2 2 0 0 0-1.66 0L2.6 6.08a1 1 0 0 0 0 1.83l8.58 3.91a2 2 0 0 0 1.66 0l8.58-3.9a1 1 0 0 0 0-1.83Z"/><path d="m22 12.5-8.58 3.91a2 2 0 0 1-1.66 0L2.6 12.5M22 17.5l-8.58 3.91a2 2 0 0 1-1.66 0L2.6 17.5"/></svg>
                        <span>Floors & Locations</span>
                    </button>
                    <button type="button" class="navitem {{ $activeTab === 'hostAlerts' ? 'active' : '' }}" onclick="switchTab('hostAlerts')">
                        <svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/></svg>
                        <span>Host Notification Rules</span>
                    </button>
                </nav>
            </div>

            <!-- Right Content Panes -->
            <div class="settings-content">
                <!-- ================= TAB: GENERAL ================= -->
                <div id="tab-general" class="tab-pane {{ $activeTab === 'general' ? 'active' : '' }}">
                    <form id="form-general" method="POST" action="{{ route('settings.general') }}">
                        @csrf
                        <!-- Organization Card -->
                        <div class="card">
                            <div class="section-title">Organization</div>
                            <div class="form-grid">
                                <div class="field">
                                    <label>Organization name</label>
                                    <input type="text" name="org_name" value="{{ old('org_name', $general['org_name'] ?? 'Meridian Business Solutions LLC') }}" required>
                                </div>
                                <div class="field">
                                    <label>Trade license number</label>
                                    <input type="text" name="trade_license_no" value="{{ old('trade_license_no', $general['trade_license_no'] ?? 'CN-1487293') }}">
                                </div>
                                <div class="field">
                                    <label>Timezone</label>
                                    <select name="timezone">
                                        <option value="Asia/Dubai" {{ ($general['timezone'] ?? '') === 'Asia/Dubai' ? 'selected' : '' }}>Asia/Dubai (GST +04:00)</option>
                                        <option value="Asia/Riyadh" {{ ($general['timezone'] ?? '') === 'Asia/Riyadh' ? 'selected' : '' }}>Asia/Riyadh (AST +03:00)</option>
                                        <option value="Europe/London" {{ ($general['timezone'] ?? '') === 'Europe/London' ? 'selected' : '' }}>Europe/London (GMT/BST)</option>
                                        <option value="America/New_York" {{ ($general['timezone'] ?? '') === 'America/New_York' ? 'selected' : '' }}>America/New_York (EST/EDT)</option>
                                    </select>
                                </div>
                                <div class="field">
                                    <label>Date Format</label>
                                    <select name="date_format">
                                        <option value="MMM DD, YYYY" {{ ($general['date_format'] ?? '') === 'MMM DD, YYYY' ? 'selected' : '' }}>MMM DD, YYYY</option>
                                        <option value="DD/MM/YYYY" {{ ($general['date_format'] ?? '') === 'DD/MM/YYYY' ? 'selected' : '' }}>DD/MM/YYYY</option>
                                        <option value="MM/DD/YYYY" {{ ($general['date_format'] ?? '') === 'MM/DD/YYYY' ? 'selected' : '' }}>MM/DD/YYYY</option>
                                        <option value="YYYY-MM-DD" {{ ($general['date_format'] ?? '') === 'YYYY-MM-DD' ? 'selected' : '' }}>YYYY-MM-DD</option>
                                    </select>
                                </div>
                                <div class="field">
                                    <label>Time Format</label>
                                    <select name="time_format">
                                        <option value="12h" {{ ($general['time_format'] ?? '') === '12h' ? 'selected' : '' }}>12 Hour (e.g. 02:30 PM)</option>
                                        <option value="24h" {{ ($general['time_format'] ?? '') === '24h' ? 'selected' : '' }}>24 Hour (e.g. 14:30)</option>
                                    </select>
                                </div>
                                <div class="field">
                                    <label>Default language</label>
                                    <select name="default_language">
                                        <option value="en" {{ ($general['default_language'] ?? '') === 'en' ? 'selected' : '' }}>English</option>
                                        <option value="ar" {{ ($general['default_language'] ?? '') === 'ar' ? 'selected' : '' }}>العربية (Arabic)</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Business Type Card -->
                        <div class="card">
                            <div class="section-title">Business Type</div>
                            <div class="cell-sub">Choose whether this system manages employees & departments, or a multi-tenant building with floors and offices. Switching modes changes the admin menu and the Check-In / Pre-Registration destination field.</div>
                            <div class="form-grid" style="margin-top: 14px;">
                                <div class="field" style="grid-column: 1 / -1;">
                                    <label>This system manages</label>
                                    <select name="business_type">
                                        <option value="employees" {{ ($general['business_type'] ?? '') === 'employees' ? 'selected' : '' }}>Employees / Company</option>
                                        <option value="tower" {{ ($general['business_type'] ?? '') === 'tower' ? 'selected' : '' }}>Multi-Tenant Commercial Tower (Floors & Offices)</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Business Hours Card -->
                        <div class="card">
                            <div class="section-title">Business Hours</div>
                            <div class="form-grid">
                                <div class="field">
                                    <label>Business Hours Start</label>
                                    <select name="business_hours_start">
                                        @foreach (['06:00 AM','07:00 AM','08:00 AM','08:30 AM','09:00 AM','09:30 AM','10:00 AM'] as $t)
                                            <option value="{{ $t }}" {{ ($general['business_hours_start'] ?? '08:00 AM') === $t ? 'selected' : '' }}>{{ $t }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="field">
                                    <label>Business Hours End</label>
                                    <select name="business_hours_end">
                                        @foreach (['04:00 PM','05:00 PM','05:30 PM','06:00 PM','06:30 PM','07:00 PM','08:00 PM'] as $t)
                                            <option value="{{ $t }}" {{ ($general['business_hours_end'] ?? '06:00 PM') === $t ? 'selected' : '' }}>{{ $t }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="field" style="margin-top: 8px;">
                                <label>Working Days</label>
                                <div class="cell-sub">Click a day to toggle it on or off. Select as many as apply.</div>
                                <div class="chipset">
                                    @php
                                        $selectedDays = $general['working_days'] ?? ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday'];
                                        $daysOfWeek = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
                                    @endphp
                                    @foreach ($daysOfWeek as $day)
                                        @php $isActive = in_array($day, $selectedDays); @endphp
                                        <label class="chip-toggle {{ $isActive ? 'active' : '' }}" onclick="toggleDayChip(this)">
                                            <input type="checkbox" name="working_days[]" value="{{ $day }}" {{ $isActive ? 'checked' : '' }}>
                                            <svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="display: {{ $isActive ? 'inline-block' : 'none' }};"><path d="M20 6L9 17l-5-5"/></svg>
                                            <span>{{ $day }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>

                            <div class="field" style="margin-top: 14px;">
                                <label>Default Visit Duration</label>
                                <select name="default_visit_duration">
                                    <option value="30" {{ ($general['default_visit_duration'] ?? '') == '30' ? 'selected' : '' }}>30 minutes</option>
                                    <option value="60" {{ ($general['default_visit_duration'] ?? '') == '60' ? 'selected' : '' }}>1 hour</option>
                                    <option value="120" {{ ($general['default_visit_duration'] ?? '') == '120' ? 'selected' : '' }}>2 hours</option>
                                    <option value="240" {{ ($general['default_visit_duration'] ?? '') == '240' ? 'selected' : '' }}>4 hours</option>
                                    <option value="480" {{ ($general['default_visit_duration'] ?? '') == '480' ? 'selected' : '' }}>Full Working Day</option>
                                </select>
                            </div>

                            <div class="switchrow">
                                <div>
                                    <div class="sr-label">After-Hours Check-In</div>
                                    <div class="sr-sub">Allow visitors to check in outside configured operational business hours with host notification.</div>
                                </div>
                                <label class="switch">
                                    <input type="checkbox" name="after_hours_checkin" value="1" {{ !empty($general['after_hours_checkin']) ? 'checked' : '' }}>
                                    <span class="track"><span class="thumb"></span></span>
                                </label>
                            </div>
                            <div class="switchrow">
                                <div>
                                    <div class="sr-label">Auto-Checkout at End of Day</div>
                                    <div class="sr-sub">Automatically check out any remaining active visitors at 11:59 PM to maintain clean daily logs.</div>
                                </div>
                                <label class="switch">
                                    <input type="checkbox" name="auto_checkout_eod" value="1" {{ !empty($general['auto_checkout_eod']) ? 'checked' : '' }}>
                                    <span class="track"><span class="thumb"></span></span>
                                </label>
                            </div>
                        </div>

                        <!-- ID Policy Card -->
                        <div class="card">
                            <div style="display: flex; justify-content: space-between; align-items: flex-start;">
                                <div>
                                    <div class="section-title">ID Verification Policy</div>
                                    <div class="cell-sub">Accepted identity document formats for visitor check-in across all branches.</div>
                                </div>
                                <button type="button" class="btn btn-sm" onclick="switchTab('idTypes')">
                                    <svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75"><rect width="20" height="14" x="2" y="5" rx="2"/><line x1="2" x2="22" y1="10" y2="10"/></svg>
                                    <span>Manage ID Types</span>
                                </button>
                            </div>
                            <div style="display: flex; gap: 8px; flex-wrap: wrap; margin-top: 14px;">
                                @forelse ($idTypes as $it)
                                    <span class="chip {{ $it->status === 'active' ? 'chip-good' : 'chip-muted' }}">{{ $it->name }}</span>
                                @empty
                                    <span class="chip chip-good">Emirates ID</span>
                                    <span class="chip chip-good">Passport</span>
                                    <span class="chip chip-good">UAE Driving License</span>
                                    <span class="chip chip-good">GCC National ID</span>
                                @endforelse
                            </div>
                        </div>

                        <div style="display: flex; justify-content: flex-end; gap: 8px; margin-top: 18px;">
                            <button type="submit" class="btn btn-primary">
                                <svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 6L9 17l-5-5"/></svg>
                                <span>Save Changes</span>
                            </button>
                        </div>
                    </form>
                </div>

                <!-- ================= TAB: BRANDING ================= -->
                <div id="tab-branding" class="tab-pane {{ $activeTab === 'branding' ? 'active' : '' }}">
                    <form id="form-branding" method="POST" action="{{ route('settings.branding') }}" enctype="multipart/form-data">
                        @csrf
                        <div class="card">
                            <div class="section-title">Company Logo</div>
                            <div class="upload-box" style="margin-top: 12px;">
                                <img id="logoPreview" class="upload-logo-preview" src="{{ $branding['logo_url'] ?? asset('images/logo.png') }}" alt="Logo">
                                <input type="file" name="logo" id="logoFileInput" accept="image/*" style="display: none;" onchange="previewUploadLogo(this)">
                                <button type="button" class="btn btn-sm" onclick="document.getElementById('logoFileInput').click();">
                                    <svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4M17 8l-5-5-5 5M12 3v12"/></svg>
                                    <span>Upload New Logo</span>
                                </button>
                                <div class="cell-sub">Recommended format: PNG or SVG with transparent background, max 3MB.</div>
                            </div>
                        </div>

                        <div class="card">
                            <div class="form-grid">
                                <div class="field">
                                    <label>Company Name</label>
                                    <input type="text" name="company_name" value="{{ old('company_name', $branding['company_name'] ?? 'Zapio VMS') }}" required>
                                </div>
                                <div class="field">
                                    <label>Display Name (Kiosk & Passes)</label>
                                    <input type="text" name="display_name" value="{{ old('display_name', $branding['display_name'] ?? 'Zapio Reception Kiosk') }}">
                                </div>
                            </div>
                        </div>

                        <div class="card">
                            <div class="section-title">Primary Brand Color</div>
                            <div class="cell-sub">Theme color applied across guest passes, kiosk terminals, buttons, and notifications.</div>
                            <div class="color-swatches">
                                @foreach (['#2E9E5B','#3B82F6','#8B5CF6','#EF4444','#F59E0B','#14B8A6'] as $clr)
                                    <button type="button" class="color-swatch {{ strtolower($branding['primary_color'] ?? '#2E9E5B') === strtolower($clr) ? 'active' : '' }}" style="background: {{ $clr }};" onclick="setBrandColor('{{ $clr }}')"></button>
                                @endforeach
                                <input type="color" id="brandColorPicker" value="{{ $branding['primary_color'] ?? '#2E9E5B' }}" oninput="setBrandColor(this.value)" style="width: 38px; height: 34px; padding: 0; border: none; border-radius: 6px; cursor: pointer;">
                                <input type="text" name="primary_color" id="brandColorHex" value="{{ $branding['primary_color'] ?? '#2E9E5B' }}" style="width: 100px; font-family: monospace; padding: 7px 10px; border-radius: 6px; border: 1px solid var(--border);">
                            </div>
                        </div>

                        <div style="display: flex; justify-content: flex-end; margin-top: 18px;">
                            <button type="submit" class="btn btn-primary">
                                <svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 6L9 17l-5-5"/></svg>
                                <span>Save Branding</span>
                            </button>
                        </div>
                    </form>
                </div>

                <!-- ================= TAB: KIOSK ================= -->
                <div id="tab-kiosk" class="tab-pane {{ $activeTab === 'kiosk' ? 'active' : '' }}">
                    <div class="grid grid-2" style="margin-bottom: 18px; align-items: start;">
                        <div class="card">
                            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px;">
                                <div class="section-title" style="margin-bottom: 0;">Registered Kiosk Terminals</div>
                                <button type="button" class="btn btn-sm btn-primary" onclick="openModal('modalAddKiosk')">+ Add Device</button>
                            </div>
                            @forelse ($kioskDevices as $dev)
                                <div class="device-card">
                                    <div class="avatar-sq"><svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75"><rect width="20" height="14" x="2" y="3" rx="2"/><path d="M12 17v4M8 21h8"/></svg></div>
                                    <div style="flex: 1; min-width: 0;">
                                        <div style="font-weight: 700; font-size: 13.5px;">{{ $dev->name }}</div>
                                        <div class="cell-sub" style="display: flex; align-items: center; gap: 6px; margin-top: 2px;">
                                            <span class="device-dot {{ $dev->status === 'online' ? 'online' : '' }}"></span>
                                            <span>{{ ucfirst($dev->status ?? 'online') }} · {{ $dev->site ? $dev->site->name : 'Main Lobby' }}</span>
                                        </div>
                                    </div>
                                    <form method="POST" action="{{ route('settings.kiosk_devices.destroy', $dev->id) }}" onsubmit="return confirm('Remove kiosk device?');">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-ghost" title="Delete Device"><svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 6h18M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg></button>
                                    </form>
                                </div>
                            @empty
                                <div class="device-card">
                                    <div class="avatar-sq"><svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75"><rect width="20" height="14" x="2" y="3" rx="2"/><path d="M12 17v4M8 21h8"/></svg></div>
                                    <div style="flex: 1;">
                                        <div style="font-weight: 700; font-size: 13.5px;">Main Reception Terminal (iPad #1)</div>
                                        <div class="cell-sub" style="display: flex; align-items: center; gap: 6px; margin-top: 2px;">
                                            <span class="device-dot online"></span>
                                            <span>Online · Business Bay Tower</span>
                                        </div>
                                    </div>
                                </div>
                            @endforelse
                        </div>

                        <!-- Kiosk Preview Widget -->
                        <div class="card" style="text-align: center;">
                            <div class="section-title">Kiosk Self Check-In Screen</div>
                            <div class="cell-sub">Live visual preview of guest interface</div>
                            <div style="background: #1b1e24; border-radius: var(--radius); padding: 24px; display: flex; align-items: center; justify-content: center; min-height: 220px; margin-top: 12px;">
                                <div style="background: #fff; border-radius: 12px; padding: 18px; width: 100%; max-width: 220px; text-align: center; box-shadow: 0 10px 25px rgba(0,0,0,.35);">
                                    <div style="font-weight: 800; font-size: 15px; color: var(--accent);">{{ $branding['company_name'] ?? 'Zapio VMS' }}</div>
                                    <div style="font-size: 11px; color: var(--text-muted); margin-top: 2px;">Welcome, please register</div>
                                    <div style="margin-top: 12px; display: flex; flex-direction: column; gap: 6px;">
                                        <button type="button" class="btn btn-sm btn-primary" style="width: 100%; font-size: 11.5px; border-radius: 6px;">Check In Here</button>
                                        <button type="button" class="btn btn-sm" style="width: 100%; font-size: 11.5px; border-radius: 6px;">Pre-Registered QR</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <form id="form-kiosk" method="POST" action="{{ route('settings.kiosk') }}">
                        @csrf
                        <div class="card">
                            <div class="section-title">Kiosk Behavior & Workflows</div>
                            <div class="switchrow">
                                <div><div class="sr-label">Camera Photo Capture</div><div class="sr-sub">Require live visitor facial photo during check-in.</div></div>
                                <label class="switch"><input type="checkbox" name="photo_capture" value="1" {{ !empty($kiosk['photo_capture']) ? 'checked' : '' }}><span class="track"><span class="thumb"></span></span></label>
                            </div>
                            <div class="switchrow">
                                <div><div class="sr-label">QR Access Pass Scanning</div><div class="sr-sub">Allow scanning email/SMS QR codes for 5-second fast check-in.</div></div>
                                <label class="switch"><input type="checkbox" name="qr_scanning" value="1" {{ !empty($kiosk['qr_scanning']) ? 'checked' : '' }}><span class="track"><span class="thumb"></span></span></label>
                            </div>
                            <div class="switchrow">
                                <div><div class="sr-label">Emirates ID / Document OCR Scanning</div><div class="sr-sub">Enable barcode scanner or optical reader for government IDs.</div></div>
                                <label class="switch"><input type="checkbox" name="id_scanning" value="1" {{ !empty($kiosk['id_scanning']) ? 'checked' : '' }}><span class="track"><span class="thumb"></span></span></label>
                            </div>
                            <div class="switchrow">
                                <div><div class="sr-label">Automatic Badge Printing</div><div class="sr-sub">Trigger thermal visitor badge printer immediately on check-in completion.</div></div>
                                <label class="switch"><input type="checkbox" name="auto_print_badge" value="1" {{ !empty($kiosk['auto_print_badge']) ? 'checked' : '' }}><span class="track"><span class="thumb"></span></span></label>
                            </div>
                            <div class="switchrow">
                                <div><div class="sr-label">Require NDA / Health & Safety Agreement</div><div class="sr-sub">Prompt visitor to review and sign digital legal agreement on-screen.</div></div>
                                <label class="switch"><input type="checkbox" name="require_agreement" value="1" {{ !empty($kiosk['require_agreement']) ? 'checked' : '' }}><span class="track"><span class="thumb"></span></span></label>
                            </div>
                        </div>

                        <div class="card">
                            <div class="form-grid" style="grid-template-columns: 1fr 1fr 1fr;">
                                <div class="field">
                                    <label>Idle Screen Timeout</label>
                                    <select name="idle_timeout_seconds">
                                        <option value="30" {{ ($kiosk['idle_timeout_seconds'] ?? '') == '30' ? 'selected' : '' }}>30 seconds</option>
                                        <option value="60" {{ ($kiosk['idle_timeout_seconds'] ?? '') == '60' ? 'selected' : '' }}>60 seconds</option>
                                        <option value="120" {{ ($kiosk['idle_timeout_seconds'] ?? '') == '120' ? 'selected' : '' }}>2 minutes</option>
                                    </select>
                                </div>
                                <div class="field">
                                    <label>Kiosk Language</label>
                                    <select name="language">
                                        <option value="en" {{ ($kiosk['language'] ?? '') == 'en' ? 'selected' : '' }}>English</option>
                                        <option value="ar" {{ ($kiosk['language'] ?? '') == 'ar' ? 'selected' : '' }}>العربية (Arabic)</option>
                                    </select>
                                </div>
                                <div class="field">
                                    <label>Badge Printer</label>
                                    <select name="badge_printer">
                                        <option value="Brother QL-820NWB" {{ ($kiosk['badge_printer'] ?? '') == 'Brother QL-820NWB' ? 'selected' : '' }}>Brother QL-820NWB</option>
                                        <option value="Zebra ZD420" {{ ($kiosk['badge_printer'] ?? '') == 'Zebra ZD420' ? 'selected' : '' }}>Zebra ZD420</option>
                                        <option value="DYMO LabelWriter 450" {{ ($kiosk['badge_printer'] ?? '') == 'DYMO LabelWriter 450' ? 'selected' : '' }}>DYMO LabelWriter 450</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div style="display: flex; justify-content: flex-end; margin-top: 18px;">
                            <button type="submit" class="btn btn-primary">
                                <svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 6L9 17l-5-5"/></svg>
                                <span>Save Kiosk Configuration</span>
                            </button>
                        </div>
                    </form>
                </div>

                <!-- ================= TAB: SECURITY ================= -->
                <div id="tab-security" class="tab-pane {{ $activeTab === 'security' ? 'active' : '' }}">
                    <form id="form-security" method="POST" action="{{ route('settings.security') }}">
                        @csrf
                        <div class="card">
                            <div class="section-title">Physical Security & Verification</div>
                            <div class="switchrow">
                                <div><div class="sr-label">Live Watchlist Screening</div><div class="sr-sub">Screen every incoming visitor against security watchlist and flagged individuals.</div></div>
                                <label class="switch"><input type="checkbox" name="watchlist_screening" value="1" {{ !empty($security['watchlist_screening']) ? 'checked' : '' }}><span class="track"><span class="thumb"></span></span></label>
                            </div>
                            <div class="switchrow">
                                <div><div class="sr-label">Mandatory ID Document Verification</div><div class="sr-sub">Block registration unless a valid Emirates ID or Passport number is captured.</div></div>
                                <label class="switch"><input type="checkbox" name="id_verification_required" value="1" {{ !empty($security['id_verification_required']) ? 'checked' : '' }}><span class="track"><span class="thumb"></span></span></label>
                            </div>
                            <div class="switchrow">
                                <div><div class="sr-label">Host Approval Required for Walk-Ins</div><div class="sr-sub">Notify host employee and require digital acceptance before visitor badge is issued.</div></div>
                                <label class="switch"><input type="checkbox" name="host_approval_required" value="1" {{ !empty($security['host_approval_required']) ? 'checked' : '' }}><span class="track"><span class="thumb"></span></span></label>
                            </div>
                            <div class="switchrow">
                                <div><div class="sr-label">Badge Mandatory for Physical Access</div><div class="sr-sub">Assign a barcode/RFID badge ID to every visitor on the premises.</div></div>
                                <label class="switch"><input type="checkbox" name="badge_mandatory" value="1" {{ !empty($security['badge_mandatory']) ? 'checked' : '' }}><span class="track"><span class="thumb"></span></span></label>
                            </div>
                            <div class="switchrow">
                                <div><div class="sr-label">Enforce Two-Factor Authentication (2FA) for Admins</div><div class="sr-sub">Require OTP verification on login for users with Admin and Security roles.</div></div>
                                <label class="switch"><input type="checkbox" name="two_factor_admin" value="1" {{ !empty($security['two_factor_admin']) ? 'checked' : '' }}><span class="track"><span class="thumb"></span></span></label>
                            </div>
                        </div>

                        <div class="card">
                            <div class="form-grid">
                                <div class="field">
                                    <label>Session Inactivity Timeout</label>
                                    <select name="session_timeout_minutes">
                                        <option value="15" {{ ($security['session_timeout_minutes'] ?? '') == '15' ? 'selected' : '' }}>15 minutes</option>
                                        <option value="30" {{ ($security['session_timeout_minutes'] ?? '') == '30' ? 'selected' : '' }}>30 minutes</option>
                                        <option value="60" {{ ($security['session_timeout_minutes'] ?? '') == '60' ? 'selected' : '' }}>60 minutes</option>
                                        <option value="120" {{ ($security['session_timeout_minutes'] ?? '') == '120' ? 'selected' : '' }}>2 hours</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div style="display: flex; justify-content: flex-end; margin-top: 18px;">
                            <button type="submit" class="btn btn-primary">
                                <svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 6L9 17l-5-5"/></svg>
                                <span>Save Security Policies</span>
                            </button>
                        </div>
                    </form>
                </div>

                <!-- ================= TAB: NOTIFICATIONS ================= -->
                <div id="tab-notifications" class="tab-pane {{ $activeTab === 'notifications' ? 'active' : '' }}">
                    <form id="form-notifications" method="POST" action="{{ route('settings.notifications') }}">
                        @csrf
                        <div class="card">
                            <div class="section-title">Notification Delivery Channels</div>
                            <div class="form-grid">
                                <div class="field">
                                    <label>System Admin Notification Email</label>
                                    <input type="email" name="admin_email" value="{{ old('admin_email', $notifications['admin_email'] ?? 'allanjacob03@gmail.com') }}" required>
                                </div>
                                <div class="field">
                                    <label>SMS Gateway Provider</label>
                                    <input type="text" name="sms_gateway" value="{{ old('sms_gateway', $notifications['sms_gateway'] ?? 'Twilio / UAE Telecom') }}">
                                </div>
                            </div>
                        </div>

                        <div class="card">
                            <div class="section-title">Automated Alerts & Reports</div>
                            <div class="switchrow">
                                <div><div class="sr-label">Daily Visitor Summary Digest</div><div class="sr-sub">Send automated daily visitor volume & overstay log report to administrators at 6:00 PM.</div></div>
                                <label class="switch"><input type="checkbox" name="daily_summary" value="1" {{ !empty($notifications['daily_summary']) ? 'checked' : '' }}><span class="track"><span class="thumb"></span></span></label>
                            </div>
                            <div class="switchrow">
                                <div><div class="sr-label">Real-Time Security Watchlist Match Alerts</div><div class="sr-sub">Trigger immediate high-priority SMS and email alert to security staff upon watchlist hit.</div></div>
                                <label class="switch"><input type="checkbox" name="watchlist_alert" value="1" {{ !empty($notifications['watchlist_alert']) ? 'checked' : '' }}><span class="track"><span class="thumb"></span></span></label>
                            </div>
                            <div class="switchrow">
                                <div><div class="sr-label">Kiosk Offline & Hardware Alerts</div><div class="sr-sub">Notify IT team if a check-in kiosk terminal loses network connection or runs out of paper.</div></div>
                                <label class="switch"><input type="checkbox" name="system_health_alerts" value="1" {{ !empty($notifications['system_health_alerts']) ? 'checked' : '' }}><span class="track"><span class="thumb"></span></span></label>
                            </div>
                        </div>

                        <div style="display: flex; justify-content: flex-end; margin-top: 18px;">
                            <button type="submit" class="btn btn-primary">
                                <svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 6L9 17l-5-5"/></svg>
                                <span>Save Notification Preferences</span>
                            </button>
                        </div>
                    </form>
                </div>

                <!-- ================= TAB: INTEGRATIONS ================= -->
                <div id="tab-integrations" class="tab-pane {{ $activeTab === 'integrations' ? 'active' : '' }}">
                    <div class="grid grid-2">
                        <!-- Email SMTP -->
                        <div class="integration-card">
                            <div class="avatar-sq icon-tint-s1"><svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg></div>
                            <div style="flex: 1; min-width: 0;">
                                <div style="font-weight: 700; font-size: 14px;">Email (SMTP / Amazon SES)</div>
                                <div class="cell-sub">Send automated invitation QR passes and arrival notifications via email.</div>
                                <div class="integration-status connected">● Connected</div>
                            </div>
                            <button type="button" class="btn btn-sm" onclick="openModal('modalSmtpConfig')">Configure</button>
                        </div>

                        <!-- Microsoft 365 / Azure AD -->
                        <div class="integration-card">
                            <div class="avatar-sq icon-tint-s2"><svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75"><path d="M16.5 9.4 7.55 4.24a1.79 1.79 0 0 0-2.55 1.56v12.4a1.79 1.79 0 0 0 2.55 1.56L16.5 14.6a1.78 1.78 0 0 0 0-3.2z"/></svg></div>
                            <div style="flex: 1; min-width: 0;">
                                <div style="font-weight: 700; font-size: 14px;">Microsoft Entra ID / Azure AD</div>
                                <div class="cell-sub">Automated employee directory synchronization and single sign-on (SSO).</div>
                                <div class="integration-status not-connected">○ Not Connected</div>
                            </div>
                            <button type="button" class="btn btn-sm btn-primary" onclick="alert('Redirecting to Microsoft 365 OAuth...')">Connect</button>
                        </div>

                        <!-- Slack -->
                        <div class="integration-card">
                            <div class="avatar-sq icon-tint-s3"><svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75"><path d="M14.5 10c-.83 0-1.5-.67-1.5-1.5v-5c0-.83.67-1.5 1.5-1.5s1.5.67 1.5 1.5v5c0 .83-.67 1.5-1.5 1.5z"/></svg></div>
                            <div style="flex: 1; min-width: 0;">
                                <div style="font-weight: 700; font-size: 14px;">Slack Direct Host Bot</div>
                                <div class="cell-sub">Send instant visitor arrival notifications and 1-click check-in approvals in Slack.</div>
                                <div class="integration-status not-connected">○ Not Connected</div>
                            </div>
                            <button type="button" class="btn btn-sm btn-primary" onclick="alert('Connect Slack Workspace')">Connect</button>
                        </div>

                        <!-- Webhooks -->
                        <div class="integration-card">
                            <div class="avatar-sq icon-tint-s4"><svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/><polyline points="15 3 21 3 21 9"/><line x1="10" x2="21" y1="14" y2="3"/></svg></div>
                            <div style="flex: 1; min-width: 0;">
                                <div style="font-weight: 700; font-size: 14px;">Webhooks & REST API</div>
                                <div class="cell-sub">Stream live visitor check-in events to external SIEM or physical turnstiles.</div>
                                <div class="integration-status connected">● 3 Active Endpoints</div>
                            </div>
                            <button type="button" class="btn btn-sm" onclick="alert('Webhooks API Keys active')">Manage</button>
                        </div>
                    </div>
                </div>

                <!-- ================= TAB: PRIVACY ================= -->
                <div id="tab-privacy" class="tab-pane {{ $activeTab === 'privacy' ? 'active' : '' }}">
                    <form id="form-privacy" method="POST" action="{{ route('settings.privacy') }}">
                        @csrf
                        <div class="card">
                            <div class="section-title">Data Retention & UAE PDPL Compliance</div>
                            <div class="form-grid">
                                <div class="field">
                                    <label>Visitor Log Data Retention</label>
                                    <select name="retention_days">
                                        <option value="30" {{ ($privacy['retention_days'] ?? '') == '30' ? 'selected' : '' }}>30 Days</option>
                                        <option value="90" {{ ($privacy['retention_days'] ?? '') == '90' ? 'selected' : '' }}>90 Days (Recommended)</option>
                                        <option value="180" {{ ($privacy['retention_days'] ?? '') == '180' ? 'selected' : '' }}>180 Days</option>
                                        <option value="365" {{ ($privacy['retention_days'] ?? '') == '365' ? 'selected' : '' }}>365 Days (1 Year)</option>
                                    </select>
                                    <div class="cell-sub">Historical visits older than this window will be archived.</div>
                                </div>
                                <div class="field">
                                    <label>Visitor Photo Retention</label>
                                    <select name="photo_retention_days">
                                        <option value="7" {{ ($privacy['photo_retention_days'] ?? '') == '7' ? 'selected' : '' }}>7 Days</option>
                                        <option value="30" {{ ($privacy['photo_retention_days'] ?? '') == '30' ? 'selected' : '' }}>30 Days</option>
                                        <option value="60" {{ ($privacy['photo_retention_days'] ?? '') == '60' ? 'selected' : '' }}>60 Days</option>
                                        <option value="90" {{ ($privacy['photo_retention_days'] ?? '') == '90' ? 'selected' : '' }}>90 Days</option>
                                    </select>
                                    <div class="cell-sub">Captured facial photos automatically purged per privacy law.</div>
                                </div>
                            </div>
                        </div>

                        <div class="card">
                            <div class="switchrow">
                                <div><div class="sr-label">Display Privacy Notice on Kiosk</div><div class="sr-sub">Show formal consent text and terms before guest enters identifying data.</div></div>
                                <label class="switch"><input type="checkbox" name="privacy_notice_on_kiosk" value="1" {{ !empty($privacy['privacy_notice_on_kiosk']) ? 'checked' : '' }}><span class="track"><span class="thumb"></span></span></label>
                            </div>
                            <div class="switchrow">
                                <div><div class="sr-label">Anonymize Expired Visitor Records</div><div class="sr-sub">Replace phone numbers and ID document numbers with salted hashes after visit expiry.</div></div>
                                <label class="switch"><input type="checkbox" name="anonymize_old_records" value="1" {{ !empty($privacy['anonymize_old_records']) ? 'checked' : '' }}><span class="track"><span class="thumb"></span></span></label>
                            </div>
                            <div class="switchrow">
                                <div><div class="sr-label">Support Right to Erasure / Data Portability</div><div class="sr-sub">Enable single-click visitor GDPR/PDPL deletion upon verified guest request.</div></div>
                                <label class="switch"><input type="checkbox" name="allow_data_requests" value="1" {{ !empty($privacy['allow_data_requests']) ? 'checked' : '' }}><span class="track"><span class="thumb"></span></span></label>
                            </div>
                        </div>

                        <div style="display: flex; justify-content: flex-end; gap: 8px; margin-top: 18px;">
                            <a href="{{ route('visitors.export') }}" class="btn">
                                <svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4M7 10l5 5 5-5M12 15V3"/></svg>
                                <span>Export All Tenant Data</span>
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 6L9 17l-5-5"/></svg>
                                <span>Save Privacy Policies</span>
                            </button>
                        </div>
                    </form>
                </div>

                <!-- ================= TAB: DEPARTMENTS ================= -->
                <div id="tab-departments" class="tab-pane {{ $activeTab === 'departments' ? 'active' : '' }}">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
                        <div>
                            <div style="font-size: 16px; font-weight: 700;">Departments</div>
                            <div class="cell-sub">{{ $departments->count() }} configured departments across organization</div>
                        </div>
                        <button type="button" class="btn btn-primary" onclick="openModal('modalAddDepartment')">
                            <svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                            <span>Add Department</span>
                        </button>
                    </div>

                    <div class="grid grid-3">
                        @forelse ($departments as $dpt)
                            <div class="card">
                                <div style="display: flex; justify-content: space-between; align-items: flex-start;">
                                    <div class="avatar-sq icon-tint-s1">
                                        <svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            @switch($dpt->icon_key)
                                                @case('sun') <circle cx="12" cy="12" r="4"/><path d="M12 2v2M12 20v2M4.93 4.93l1.41 1.41M17.66 17.66l1.41 1.41M2 12h2M20 12h2M4.93 19.07l1.41-1.41M17.66 6.34l1.41-1.41"/> @break
                                                @case('code') <path d="M16 18l6-6-6-6M8 6l-6 6 6 6"/> @break
                                                @case('heart') <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/> @break
                                                @case('dollar') <line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/> @break
                                                @case('scale') <path d="M12 2v20M17 5H9.5M19 12h-4l2-4zM9 12H5l2-4z"/> @break
                                                @case('speaker') <polygon points="11 5 6 9 2 9 2 15 6 15 11 19 11 5"/><path d="M15.54 8.46a5 5 0 0 1 0 7.07M19.07 4.93a10 10 0 0 1 0 14.14"/> @break
                                                @case('home') <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/> @break
                                                @case('shield') <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/> @break
                                                @case('layers') <polygon points="12 2 2 7 12 12 22 7 12 2"/><polyline points="2 17 12 22 22 17"/><polyline points="2 12 12 17 22 12"/> @break
                                                @default <rect x="2" y="7" width="20" height="14" rx="2" ry="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/>
                                            @endswitch
                                        </svg>
                                    </div>
                                    <div style="display: flex; gap: 4px;">
                                        <button type="button" class="btn btn-sm btn-ghost" title="Edit" onclick="editDepartment({{ json_encode($dpt) }})">
                                            <svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 3a2.828 2.828 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5L17 3z"/></svg>
                                        </button>
                                        <form method="POST" action="{{ route('departments.destroy', $dpt->id) }}" onsubmit="return confirm('Delete department {{ $dpt->name }}?');">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-ghost" title="Delete">
                                                <svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 6h18M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                                <div style="font-weight: 700; font-size: 15px; margin-top: 12px;">{{ $dpt->name }}</div>
                                @if ($dpt->description)
                                    <div class="cell-sub" style="margin-top: 2px;">{{ $dpt->description }}</div>
                                @endif
                                <div class="form-grid" style="margin-top: 14px; row-gap: 4px; font-size: 12px;">
                                    <div>
                                        <div style="font-weight: 700; font-size: 15px;">{{ $dpt->employees_count }}</div>
                                        <div class="cell-sub">Members</div>
                                    </div>
                                    <div>
                                        <div style="font-weight: 700; font-size: 15px;">{{ $dpt->code }}</div>
                                        <div class="cell-sub">Code</div>
                                    </div>
                                    <div>
                                        <div style="font-weight: 700; font-size: 15px;">{{ $dpt->headEmployee ? $dpt->headEmployee->first_name : '—' }}</div>
                                        <div class="cell-sub">Dept Head</div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="card" style="grid-column: 1 / -1; text-align: center; padding: 40px;">No departments found. Click "+ Add Department" to create one.</div>
                        @endforelse
                    </div>
                </div>

                <!-- ================= TAB: BRANCHES ================= -->
                <div id="tab-branches" class="tab-pane {{ $activeTab === 'branches' ? 'active' : '' }}">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
                        <div>
                            <div style="font-size: 16px; font-weight: 700;">Branches</div>
                            <div class="cell-sub">Sites, facility locations, and regional offices</div>
                        </div>
                        <button type="button" class="btn btn-primary" onclick="openModal('modalAddBranch')">
                            <svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                            <span>Add Branch</span>
                        </button>
                    </div>

                    <div class="grid grid-3">
                        @forelse ($sites as $site)
                            <div class="card">
                                <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 10px;">
                                    <div class="avatar-sq icon-tint-s2">
                                        <svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75"><path d="M3 21h18M5 21V9l7-5 7 5v12M9 21v-5h6v5"/></svg>
                                    </div>
                                    <button type="button" class="btn btn-sm btn-ghost" title="Edit" onclick="editBranch({{ json_encode($site) }})">
                                        <svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 3a2.828 2.828 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5L17 3z"/></svg>
                                    </button>
                                </div>
                                <div style="font-weight: 700; font-size: 15px;">{{ $site->name }}</div>
                                <div class="cell-sub">{{ $site->city ?? 'Dubai' }} · {{ $site->code }}</div>
                                <div class="form-grid" style="margin-top: 14px; row-gap: 6px; font-size: 12px;">
                                    <div>
                                        <div class="cell-sub">Floors</div>
                                        <div style="font-weight: 700;">{{ $site->floors ?? 1 }}</div>
                                    </div>
                                    <div>
                                        <div class="cell-sub">Capacity</div>
                                        <div style="font-weight: 700;">{{ $site->capacity ?? 'Unlimited' }}</div>
                                    </div>
                                    <div>
                                        <div class="cell-sub">Staff</div>
                                        <div style="font-weight: 700;">{{ $site->employees_count }}</div>
                                    </div>
                                    <div>
                                        <div class="cell-sub">On-site Now</div>
                                        <div style="font-weight: 700; color: var(--accent);">{{ $site->active_visitors_count ?? 0 }}</div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="card" style="grid-column: 1 / -1; text-align: center; padding: 40px;">No branches created yet.</div>
                        @endforelse
                    </div>
                </div>

                <!-- ================= TAB: PURPOSES ================= -->
                <div id="tab-purposes" class="tab-pane {{ $activeTab === 'purposes' ? 'active' : '' }}">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
                        <div>
                            <div style="font-size: 16px; font-weight: 700;">Purpose of Visit</div>
                            <div class="cell-sub">Visit categories presented to guests at registration</div>
                        </div>
                        <button type="button" class="btn btn-primary" onclick="openModal('modalAddPurpose')">
                            <svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                            <span>Add Purpose</span>
                        </button>
                    </div>

                    <div class="grid grid-3">
                        @foreach ($purposes as $p)
                            <div class="card">
                                <div style="display: flex; align-items: center; justify-content: space-between;">
                                    <div style="display: flex; align-items: center; gap: 10px;">
                                        <div class="avatar-sq icon-tint-s5"><svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75"><path d="M12 2H2v10l9.29 9.29c.94.94 2.48.94 3.42 0l6.58-6.58c.94-.94.94-2.48 0-3.42L12 2Z"/><circle cx="7" cy="7" r=".5" fill="currentColor"/></svg></div>
                                        <div>
                                            <div style="font-weight: 700; font-size: 14px;">{{ $p->name }}</div>
                                        </div>
                                    </div>
                                    <div style="display: flex; gap: 4px; align-items: center;">
                                        <button type="button" class="btn btn-sm btn-ghost" onclick="editPurpose({{ json_encode($p) }})"><svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 3a2.828 2.828 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5L17 3z"/></svg></button>
                                        <form action="{{ route('lookups.purposes.destroy', $p->id) }}" method="POST" onsubmit="return confirm('Delete this purpose?');" style="margin: 0;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-ghost" style="color: #ef4444;"><svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 6h18"/><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/><path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"/></svg></button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- ================= TAB: VISITOR TYPES ================= -->
                <div id="tab-visitorTypes" class="tab-pane {{ $activeTab === 'visitorTypes' ? 'active' : '' }}">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
                        <div>
                            <div style="font-size: 16px; font-weight: 700;">Visitor Types</div>
                            <div class="cell-sub">Manage the visitor type categories used in pre-registration and NDA/agreement rules.</div>
                        </div>
                        <button type="button" class="btn btn-primary" onclick="openModal('modalAddVisitorType')">+ Add Visitor Type</button>
                    </div>

                    <div class="grid grid-3">
                        @foreach ($visitorTypes as $vt)
                            <div class="card">
                                <div style="display: flex; align-items: flex-start; justify-content: space-between;">
                                    <div class="avatar-sq icon-tint-s3"><svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"/></svg></div>
                                    <div style="display: flex; gap: 4px; align-items: center;">
                                        <button type="button" class="btn btn-sm btn-ghost" onclick="editVisitorType({{ json_encode($vt) }})"><svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 3a2.828 2.828 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5L17 3z"/></svg></button>
                                        <form action="{{ route('lookups.visitor_types.destroy', $vt->id) }}" method="POST" onsubmit="return confirm('Delete this visitor type?');" style="margin: 0;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-ghost" style="color: #ef4444;"><svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 6h18"/><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/><path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"/></svg></button>
                                        </form>
                                    </div>
                                </div>
                                <div style="font-weight: 700; font-size: 14.5px; margin-top: 10px;">
                                    {{ $vt->name }}
                                    @if($vt->status === 'active')
                                        <span style="color: var(--accent); font-size: 11px; font-weight: 600; margin-left: 6px;">&bull; Active</span>
                                    @endif
                                </div>
                                <div style="display: flex; gap: 6px; flex-wrap: wrap; margin-top: 10px;">
                                    @if ($vt->requires_approval) <span class="chip chip-good">Requires Approval</span> @endif
                                    @if ($vt->requires_nda) <span class="chip chip-good">Requires NDA</span> @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- ================= TAB: ID TYPES ================= -->
                <div id="tab-idTypes" class="tab-pane {{ $activeTab === 'idTypes' ? 'active' : '' }}">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
                        <div>
                            <div style="font-size: 16px; font-weight: 700;">ID Document Types</div>
                            <div class="cell-sub">Official identity cards accepted by front desk and kiosk</div>
                        </div>
                        <button type="button" class="btn btn-primary" onclick="openModal('modalAddIdType')">+ Add ID Type</button>
                    </div>

                    <div class="grid grid-3">
                        @foreach ($idTypes as $it)
                            <div class="card">
                                <div style="display: flex; align-items: center; justify-content: space-between;">
                                    <div style="display: flex; align-items: center; gap: 10px;">
                                        <div class="avatar-sq icon-tint-s2"><svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75"><rect width="20" height="14" x="2" y="5" rx="2"/><line x1="2" x2="22" y1="10" y2="10"/></svg></div>
                                        <div>
                                            <div style="font-weight: 700; font-size: 14px;">{{ $it->name }}</div>
                                            <div class="cell-sub">Code: {{ $it->code }}</div>
                                        </div>
                                    </div>
                                    <button type="button" class="btn btn-sm btn-ghost" onclick="editIdType({{ json_encode($it) }})"><svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 3a2.828 2.828 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5L17 3z"/></svg></button>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- ================= TAB: MEETING ROOMS ================= -->
                <div id="tab-meetingRooms" class="tab-pane {{ $activeTab === 'meetingRooms' ? 'active' : '' }}">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
                        <div>
                            <div style="font-size: 16px; font-weight: 700;">Meeting Rooms</div>
                            <div class="cell-sub">Conference rooms available for host invitations and schedule reservations</div>
                        </div>
                        <button type="button" class="btn btn-primary" onclick="openModal('modalAddRoom')">+ Add Meeting Room</button>
                    </div>

                    <div class="grid grid-3">
                        @foreach ($meetingRooms as $room)
                            <div class="card">
                                <div style="display: flex; justify-content: space-between; align-items: flex-start;">
                                    <div style="display: flex; align-items: flex-start; gap: 10px;">
                                        <div class="avatar-sq icon-tint-s3"><svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75"><rect width="20" height="14" x="2" y="7" rx="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/></svg></div>
                                        <div>
                                            <div style="font-weight: 700; font-size: 15px;">{{ $room['name'] }}</div>
                                            <div class="cell-sub" style="margin-top: 2px;">{{ $room['site_name'] ?? 'Main Office' }}@if(!empty($room['floor_name'])) · {{ $room['floor_name'] }}@endif · Seats {{ $room['capacity'] ?? 8 }} · {{ $room['bookings'] ?? 0 }} bookings</div>
                                        </div>
                                    </div>
                                    <form method="POST" action="{{ route('settings.meeting_rooms.destroy', $room['id']) }}" onsubmit="return confirm('Remove meeting room?');">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-ghost"><svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 6h18M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg></button>
                                    </form>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- ================= TAB: DELIVERY CARRIERS ================= -->
                <div id="tab-deliveryCarriers" class="tab-pane {{ $activeTab === 'deliveryCarriers' ? 'active' : '' }}">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
                        <div>
                            <div style="font-size: 16px; font-weight: 700;">Delivery Carriers</div>
                            <div class="cell-sub">Couriers, postal providers, and freight services</div>
                        </div>
                        <button type="button" class="btn btn-primary" onclick="openModal('modalAddCarrier')">+ Add Carrier</button>
                    </div>

                    <div class="grid grid-3">
                        @foreach ($deliveryCarriers as $c)
                            <div class="card">
                                <div style="display: flex; justify-content: space-between; align-items: flex-start;">
                                    <div style="display: flex; align-items: flex-start; gap: 10px;">
                                        <div class="avatar-sq icon-tint-s4"><svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75"><path d="m7.5 4.27 9 5.15M21 8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16Z"/></svg></div>
                                        <div>
                                            <div style="font-weight: 700; font-size: 14.5px;">{{ $c['name'] }}</div>
                                            <div class="cell-sub" style="margin-top: 2px;">Code: {{ $c['code'] ?? '' }} · {{ $c['deliveries'] ?? 0 }} packages logged</div>
                                        </div>
                                    </div>
                                    <form method="POST" action="{{ route('settings.carriers.destroy', $c['id']) }}" onsubmit="return confirm('Remove carrier?');">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-ghost"><svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 6h18M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg></button>
                                    </form>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- ================= TAB: PACKAGE SIZES ================= -->
                <div id="tab-packageSizes" class="tab-pane {{ $activeTab === 'packageSizes' ? 'active' : '' }}">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
                        <div>
                            <div style="font-size: 16px; font-weight: 700;">Package Sizes</div>
                            <div class="cell-sub">Standard parcel classifications for front-desk mailroom intake</div>
                        </div>
                        <button type="button" class="btn btn-primary" onclick="openModal('modalAddSize')">+ Add Package Size</button>
                    </div>

                    <div class="grid grid-3">
                        @foreach ($packageSizes as $s)
                            <div class="card">
                                <div style="display: flex; justify-content: space-between; align-items: flex-start;">
                                    <div style="display: flex; align-items: flex-start; gap: 10px;">
                                        <div class="avatar-sq icon-tint-s5"><svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75"><path d="m21 21-6-6m6 6v-4.8m0 4.8h-4.8M3 16.2V21m0 0h4.8M3 21l6-6M21 7.8V3m0 0h-4.8M21 3l-6 6M3 7.8V3m0 0h4.8M3 3l6 6"/></svg></div>
                                        <div>
                                            <div style="font-weight: 700; font-size: 14.5px;">{{ $s['name'] }}</div>
                                            @if (!empty($s['description']))
                                                <div class="cell-sub" style="margin-top: 2px;">{{ $s['description'] }}</div>
                                            @endif
                                        </div>
                                    </div>
                                    <form method="POST" action="{{ route('settings.package_sizes.destroy', $s['id']) }}" onsubmit="return confirm('Remove size?');">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-ghost"><svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 6h18M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg></button>
                                    </form>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- ================= TAB: FLOORS ================= -->
                <div id="tab-floors" class="tab-pane {{ $activeTab === 'floors' ? 'active' : '' }}">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
                        <div>
                            <div style="font-size: 16px; font-weight: 700;">Floors & Locations</div>
                            <div class="cell-sub">Manage floors for each branch. A branch can have as many floors as it needs.</div>
                        </div>
                        <button type="button" class="btn btn-primary" onclick="openFloorModal()">+ Add Floor</button>
                    </div>

                    @foreach ($sites as $s)
                        @php
                            $branchFloors = $floors->filter(fn($f) => $f->site_id == $s->id || ($f->building && $f->building->site_id == $s->id));
                        @endphp
                        <div style="margin-bottom: 22px;">
                            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
                                <div style="display: flex; align-items: center; gap: 8px;">
                                    <div style="font-weight: 700; font-size: 14.5px;">{{ $s->name }}</div>
                                    <span class="countpill">{{ $branchFloors->count() }}</span>
                                </div>
                                <button type="button" class="btn btn-sm btn-ghost" onclick="openFloorModal(null, '{{ $s->id }}')">
                                    <svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 5v14M5 12h14"/></svg>
                                    <span>Add Floor</span>
                                </button>
                            </div>

                            @if ($branchFloors->count())
                                <div class="grid grid-2">
                                    @foreach ($branchFloors as $fl)
                                        <div class="card">
                                            <div style="display: flex; align-items: flex-start; justify-content: space-between;">
                                                <div style="flex: 1; min-width: 0;">
                                                    <div style="font-weight: 700; font-size: 14.5px;">{{ $fl->name }}</div>
                                                    @if ($fl->description)
                                                        <div class="cell-sub" style="margin-top: 2px;">{{ $fl->description }}</div>
                                                    @endif
                                                </div>
                                                <div style="display: flex; gap: 4px;">
                                                    <button type="button" class="btn btn-sm btn-ghost" onclick="editFloor({{ json_encode($fl) }})" title="Edit">
                                                        <svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 3a2.828 2.828 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5L17 3z"/></svg>
                                                    </button>
                                                    <form method="POST" action="{{ route('settings.floors.destroy', $fl->id) }}" onsubmit="return confirm('Remove floor?');" style="display: inline;">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-ghost" title="Delete">
                                                            <svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 6h18M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg>
                                                        </button>
                                                    </form>
                                                </div>
                                            </div>
                                            <div class="tagchips" style="margin-top: 12px;">
                                                @forelse ($fl->areas as $area)
                                                    <span class="tagchip">{{ $area->name }}</span>
                                                @empty
                                                    <span class="cell-sub">No areas specified</span>
                                                @endforelse
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="card" style="margin-bottom: 0;">
                                    <div style="text-align: center; padding: 24px; color: var(--text-muted); font-size: 13px;">
                                        No floors added for this branch yet.
                                    </div>
                                </div>
                            @endif
                        </div>
                    @endforeach

                    @php
                        $orphanedFloors = $floors->filter(fn($f) => !$f->site_id && (!$f->building || !$f->building->site_id));
                    @endphp
                    @if ($orphanedFloors->count())
                        <div style="margin-bottom: 22px;">
                            <div style="font-weight: 700; font-size: 14.5px; margin-bottom: 10px;">Unassigned Floors</div>
                            <div class="grid grid-2">
                                @foreach ($orphanedFloors as $fl)
                                    <div class="card">
                                        <div style="display: flex; align-items: flex-start; justify-content: space-between;">
                                            <div style="flex: 1; min-width: 0;">
                                                <div style="font-weight: 700; font-size: 14.5px;">{{ $fl->name }}</div>
                                                @if ($fl->description)
                                                    <div class="cell-sub" style="margin-top: 2px;">{{ $fl->description }}</div>
                                                @endif
                                            </div>
                                            <div style="display: flex; gap: 4px;">
                                                <button type="button" class="btn btn-sm btn-ghost" onclick="editFloor({{ json_encode($fl) }})" title="Edit">
                                                    <svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 3a2.828 2.828 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5L17 3z"/></svg>
                                                </button>
                                                <form method="POST" action="{{ route('settings.floors.destroy', $fl->id) }}" onsubmit="return confirm('Remove floor?');" style="display: inline;">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-ghost" title="Delete">
                                                        <svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 6h18M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg>
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                        <div class="tagchips" style="margin-top: 12px;">
                                            @forelse ($fl->areas as $area)
                                                <span class="tagchip">{{ $area->name }}</span>
                                            @empty
                                                <span class="cell-sub">No areas specified</span>
                                            @endforelse
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>

                <!-- ================= TAB: HOST ALERTS ================= -->
                <div id="tab-hostAlerts" class="tab-pane {{ $activeTab === 'hostAlerts' ? 'active' : '' }}">
                    <form id="form-hostAlerts" method="POST" action="{{ route('settings.host_rules') }}">
                        @csrf
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
                            <div>
                                <div style="font-size: 16px; font-weight: 700;">Host Notification Rules</div>
                                <div class="cell-sub">Configure how and when employees are alerted about guest arrivals and overstays</div>
                            </div>
                            <button type="submit" class="btn btn-primary">
                                <svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 6L9 17l-5-5"/></svg>
                                <span>Save Rules</span>
                            </button>
                        </div>

                        <div class="grid grid-2">
                            <!-- Guest Arrival Alerts -->
                            <div class="card">
                                <div class="section-title">Visitor Arrival Alerts</div>
                                <div class="cell-sub">Triggered the moment a guest checks in at reception or kiosk.</div>
                                <div class="switchrow"><div><div class="sr-label">Email Notification</div></div><label class="switch"><input type="checkbox" name="arrival_email" value="1" {{ !empty($hostAlerts['arrival']['email']) ? 'checked' : '' }}><span class="track"><span class="thumb"></span></span></label></div>
                                <div class="switchrow"><div><div class="sr-label">Push Notification</div></div><label class="switch"><input type="checkbox" name="arrival_push" value="1" {{ !empty($hostAlerts['arrival']['push']) ? 'checked' : '' }}><span class="track"><span class="thumb"></span></span></label></div>
                                <div class="switchrow"><div><div class="sr-label">SMS Alert to Host Mobile</div></div><label class="switch"><input type="checkbox" name="arrival_sms" value="1" {{ !empty($hostAlerts['arrival']['sms']) ? 'checked' : '' }}><span class="track"><span class="thumb"></span></span></label></div>
                                <div class="switchrow"><div><div class="sr-label">Desktop Browser Popup</div></div><label class="switch"><input type="checkbox" name="arrival_desktop" value="1" {{ !empty($hostAlerts['arrival']['desktop']) ? 'checked' : '' }}><span class="track"><span class="thumb"></span></span></label></div>
                                <div class="field" style="margin-top: 12px;">
                                    <label>Reminder if Host Does Not Acknowledge</label>
                                    <select name="arrival_reminder_minutes">
                                        <option value="0" {{ ($hostAlerts['arrival']['reminder_minutes'] ?? '') == '0' ? 'selected' : '' }}>Do not repeat</option>
                                        <option value="5" {{ ($hostAlerts['arrival']['reminder_minutes'] ?? '') == '5' ? 'selected' : '' }}>Repeat after 5 minutes</option>
                                        <option value="10" {{ ($hostAlerts['arrival']['reminder_minutes'] ?? '') == '10' ? 'selected' : '' }}>Repeat after 10 minutes</option>
                                    </select>
                                </div>
                            </div>

                            <!-- Pre-Registration Reminders -->
                            <div class="card">
                                <div class="section-title">Pre-Registration Reminders</div>
                                <div class="cell-sub">Upcoming visit schedules and morning agendas.</div>
                                <div class="switchrow"><div><div class="sr-label">Notify on Invite Creation</div></div><label class="switch"><input type="checkbox" name="prereg_notify" value="1" {{ !empty($hostAlerts['pre_reg']['notify']) ? 'checked' : '' }}><span class="track"><span class="thumb"></span></span></label></div>
                                <div class="switchrow"><div><div class="sr-label">Morning Briefing Email</div></div><label class="switch"><input type="checkbox" name="prereg_briefing" value="1" {{ !empty($hostAlerts['pre_reg']['morning_briefing']) ? 'checked' : '' }}><span class="track"><span class="thumb"></span></span></label></div>
                                <div class="switchrow"><div><div class="sr-label">15-Minute Pre-Arrival Alert</div></div><label class="switch"><input type="checkbox" name="prereg_reminder_15" value="1" {{ !empty($hostAlerts['pre_reg']['reminder_15']) ? 'checked' : '' }}><span class="track"><span class="thumb"></span></span></label></div>
                            </div>

                            <!-- Escalation Rules -->
                            <div class="card">
                                <div class="section-title">Unattended Visitor Escalation</div>
                                <div class="cell-sub">Action when host does not receive or acknowledge visitor</div>
                                <div class="field">
                                    <label>Escalate To</label>
                                    <select name="escalation_backup_host">
                                        <option value="Department Head">Department Head</option>
                                        <option value="Front Desk">Front Desk Receptionist</option>
                                        <option value="Security Team">Security Operations Center</option>
                                    </select>
                                </div>
                                <div class="field">
                                    <label>Escalate After</label>
                                    <select name="escalate_after_minutes">
                                        <option value="5">5 minutes</option>
                                        <option value="10" selected>10 minutes</option>
                                        <option value="15">15 minutes</option>
                                    </select>
                                </div>
                            </div>

                            <!-- Checkout & Overstay -->
                            <div class="card">
                                <div class="section-title">Checkout & Overstay Rules</div>
                                <div class="cell-sub">Automatic duration monitoring</div>
                                <div class="switchrow"><div><div class="sr-label">Notify Host on Checkout</div></div><label class="switch"><input type="checkbox" name="checkout_notify" value="1" {{ !empty($hostAlerts['checkout']['notify_on_checkout']) ? 'checked' : '' }}><span class="track"><span class="thumb"></span></span></label></div>
                                <div class="switchrow"><div><div class="sr-label">Alert Security on Overstay</div></div><label class="switch"><input type="checkbox" name="checkout_overstay_alert" value="1" {{ !empty($hostAlerts['checkout']['alert_on_overstay']) ? 'checked' : '' }}><span class="track"><span class="thumb"></span></span></label></div>
                                <div class="field" style="margin-top: 12px;">
                                    <label>Overstay Grace Period</label>
                                    <select name="checkout_overstay_minutes">
                                        <option value="30">30 minutes past scheduled end</option>
                                        <option value="60" selected>60 minutes past scheduled end</option>
                                        <option value="120">120 minutes past scheduled end</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </main>
</div>

<!-- ================= MODALS ================= -->

<!-- 1. Add/Edit Department Modal -->
<div id="modalAddDepartment" class="modal-root">
    <div class="modal-backdrop" onclick="closeModal('modalAddDepartment')"></div>
    <div class="modal-panel" style="max-width: 520px;">
        <div class="modal-head">
            <h3 id="deptModalTitle">Add Department</h3>
            <button type="button" class="btn btn-sm btn-ghost" onclick="closeModal('modalAddDepartment')">✕</button>
        </div>
        <form id="deptForm" method="POST" action="{{ route('departments.store') }}">
            @csrf
            <input type="hidden" name="_method" id="deptMethod" value="POST">
            <!-- Keeping code as a hidden field that will be auto-generated in the backend if empty, since it's required by the schema but not in the UI image -->
            <input type="hidden" name="code" id="deptCode" value="">
            <input type="hidden" name="status" id="deptStatus" value="active">
            
            <div class="modal-body">
                <div class="form-grid">
                    <div class="field" style="grid-column: 1 / -1;">
                        <label>Department Name *</label>
                        <input type="text" name="name" id="deptName" required placeholder="e.g. Marketing">
                    </div>
                    
                    <div class="field" style="grid-column: 1 / -1;">
                        <label>Description</label>
                        <textarea name="description" id="deptDesc" rows="3" placeholder=""></textarea>
                    </div>

                    <div class="field" style="grid-column: 1 / -1;">
                        <label>Icon</label>
                        <div class="tagchips" style="gap: 8px;">
                            <label class="chip-toggle"><input type="radio" name="icon_key" value="sun" checked> <svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="4"/><path d="M12 2v2M12 20v2M4.93 4.93l1.41 1.41M17.66 17.66l1.41 1.41M2 12h2M20 12h2M4.93 19.07l1.41-1.41M17.66 6.34l1.41-1.41"/></svg></label>
                            <label class="chip-toggle"><input type="radio" name="icon_key" value="code"> <svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M16 18l6-6-6-6M8 6l-6 6 6 6"/></svg></label>
                            <label class="chip-toggle"><input type="radio" name="icon_key" value="heart"> <svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/></svg></label>
                            <label class="chip-toggle"><input type="radio" name="icon_key" value="dollar"> <svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg></label>
                            <label class="chip-toggle"><input type="radio" name="icon_key" value="scale"> <svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2v20M17 5H9.5M19 12h-4l2-4zM9 12H5l2-4z"/></svg></label>
                            <label class="chip-toggle"><input type="radio" name="icon_key" value="speaker"> <svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="11 5 6 9 2 9 2 15 6 15 11 19 11 5"/><path d="M15.54 8.46a5 5 0 0 1 0 7.07M19.07 4.93a10 10 0 0 1 0 14.14"/></svg></label>
                            <label class="chip-toggle"><input type="radio" name="icon_key" value="briefcase"> <svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="7" width="20" height="14" rx="2" ry="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/></svg></label>
                            <label class="chip-toggle"><input type="radio" name="icon_key" value="home"> <svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg></label>
                            <label class="chip-toggle"><input type="radio" name="icon_key" value="shield"> <svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg></label>
                            <label class="chip-toggle"><input type="radio" name="icon_key" value="layers"> <svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="12 2 2 7 12 12 22 7 12 2"/><polyline points="2 17 12 22 22 17"/><polyline points="2 12 12 17 22 12"/></svg></label>
                        </div>
                    </div>

                    <div class="field">
                        <label>Floor</label>
                        <select name="floor_ids[]" id="deptFloor" @if($floors->isEmpty()) disabled @endif>
                            <option value="">Unassigned</option>
                            @foreach ($floors->groupBy(fn($f) => $f->site->name ?? 'Main Office') as $siteName => $siteFloors)
                                <optgroup label="{{ $siteName }}">
                                    @foreach ($siteFloors as $floor)
                                        <option value="{{ $floor->id }}">{{ $siteName }} — {{ $floor->name }}</option>
                                    @endforeach
                                </optgroup>
                            @endforeach
                        </select>
                        @if($floors->isEmpty())
                            <div style="font-size: 11px; color: var(--critical); margin-top: 4px;">No floors are configured. Add floors under Floors & Locations first.</div>
                        @endif
                    </div>
                    
                    <div class="field">
                        <label>Department Head</label>
                        <select name="head_employee_id" id="deptHead">
                            <option value="">No manager</option>
                            @foreach ($employees as $emp)
                                <option value="{{ $emp->id }}">{{ $emp->full_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="field">
                        <label>Contact Email</label>
                        <input type="email" name="contact_email" id="deptContactEmail" placeholder="">
                    </div>
                    
                    <div class="field">
                        <label>Contact Phone</label>
                        <input type="text" name="contact_phone" id="deptContactPhone" placeholder="">
                    </div>
                </div>
                
                <div style="margin-top: 24px;">
                    <div class="section-title" style="font-size: 11px; text-transform: uppercase; letter-spacing: 0.05em; color: var(--text-muted); margin-bottom: 8px;">ACCESS SETTINGS</div>
                    
                    <div class="switchrow">
                        <div>
                            <div class="sr-label">Selectable at kiosk check-in</div>
                        </div>
                        <label class="switch">
                            <input type="checkbox" name="kiosk_selectable" id="deptKioskSelectable" value="1" checked>
                            <span class="track"><span class="thumb"></span></span>
                        </label>
                    </div>
                    
                    <div class="switchrow">
                        <div>
                            <div class="sr-label">Require NDA for visitors</div>
                        </div>
                        <label class="switch">
                            <input type="checkbox" name="require_nda" id="deptRequireNda" value="1">
                            <span class="track"><span class="thumb"></span></span>
                        </label>
                    </div>
                    
                    <div class="switchrow">
                        <div>
                            <div class="sr-label">Escort required at all times</div>
                        </div>
                        <label class="switch">
                            <input type="checkbox" name="escort_required" id="deptEscortRequired" value="1">
                            <span class="track"><span class="thumb"></span></span>
                        </label>
                    </div>
                </div>
            </div>
            <div class="modal-foot">
                <button type="button" class="btn" onclick="closeModal('modalAddDepartment')">Cancel</button>
                <button type="submit" class="btn btn-primary">Save</button>
            </div>
        </form>
    </div>
</div>

<!-- 2. Add/Edit Branch Modal -->
<div id="modalAddBranch" class="modal-root">
    <div class="modal-backdrop" onclick="closeModal('modalAddBranch')"></div>
    <div class="modal-panel">
        <div class="modal-head">
            <h3 id="branchModalTitle">Add Branch</h3>
            <button type="button" class="btn btn-sm btn-ghost" onclick="closeModal('modalAddBranch')">✕</button>
        </div>
        <form id="branchForm" method="POST" action="{{ route('sites.store') }}">
            @csrf
            <input type="hidden" name="_method" id="branchMethod" value="POST">
            <input type="hidden" name="code" id="branchCode" value="">
            <input type="hidden" name="address" id="branchAddress" value="">
            <input type="hidden" name="status" id="branchStatus" value="active">
            <div class="modal-body">
                <div class="form-grid">
                    <div class="field">
                        <label>Branch name</label>
                        <input type="text" name="name" id="branchName" required placeholder="">
                    </div>
                    <div class="field">
                        <label>اسم الفرع (Arabic)</label>
                        <input type="text" name="name_ar" id="branchNameAr" dir="rtl" placeholder="">
                    </div>
                    <div class="field">
                        <label>City</label>
                        <input type="text" name="city" id="branchCity" value="" required>
                    </div>
                    <div class="field">
                        <label>Floors</label>
                        <input type="number" name="floors" id="branchFloors" value="" min="1">
                    </div>
                    <div class="field">
                        <label>Visiting hours (from)</label>
                        <select name="visiting_hours_from" id="branchVisitsFrom">
                            <option value="">Select Time</option>
                            <option value="07:00 AM">07:00 AM</option>
                            <option value="08:00 AM">08:00 AM</option>
                            <option value="09:00 AM">09:00 AM</option>
                            <option value="10:00 AM">10:00 AM</option>
                            <option value="11:00 AM">11:00 AM</option>
                        </select>
                    </div>
                    <div class="field">
                        <label>Visiting hours (to)</label>
                        <select name="visiting_hours_to" id="branchVisitsTo">
                            <option value="">Select Time</option>
                            <option value="04:00 PM">04:00 PM</option>
                            <option value="05:00 PM">05:00 PM</option>
                            <option value="06:00 PM">06:00 PM</option>
                            <option value="07:00 PM">07:00 PM</option>
                            <option value="08:00 PM">08:00 PM</option>
                        </select>
                    </div>
                    <div class="field">
                        <label>Capacity</label>
                        <input type="number" name="capacity" id="branchCapacity" value="" min="1">
                    </div>
                </div>
            </div>
            <div class="modal-foot">
                <button type="button" class="btn" onclick="closeModal('modalAddBranch')">Cancel</button>
                <button type="submit" class="btn btn-primary">Save Branch</button>
            </div>
        </form>
    </div>
</div>

<!-- 3. Add/Edit Purpose Modal -->
<div id="modalAddPurpose" class="modal-root">
    <div class="modal-backdrop" onclick="closeModal('modalAddPurpose')"></div>
    <div class="modal-panel" style="max-width: 440px;">
        <div class="modal-head">
            <h3 id="purposeModalTitle">Add Visit Purpose</h3>
            <button type="button" class="btn btn-sm btn-ghost" onclick="closeModal('modalAddPurpose')">✕</button>
        </div>
        <form id="purposeForm" method="POST" action="{{ route('lookups.purposes.store') }}">
            @csrf
            <input type="hidden" name="_method" id="purposeMethod" value="POST">
            <div class="modal-body">
                <div class="field">
                    <label>Purpose Name *</label>
                    <input type="text" name="name" id="purposeName" required placeholder="e.g. Client Meeting">
                </div>
                <input type="hidden" name="status" id="purposeStatus" value="active">
            </div>
            <div class="modal-foot">
                <button type="button" class="btn" onclick="closeModal('modalAddPurpose')">Cancel</button>
                <button type="submit" class="btn btn-primary">Save Purpose</button>
            </div>
        </form>
    </div>
</div>

<!-- 4. Add/Edit Visitor Type Modal -->
<div id="modalAddVisitorType" class="modal-root">
    <div class="modal-backdrop" onclick="closeModal('modalAddVisitorType')"></div>
    <div class="modal-panel" style="max-width: 440px;">
        <div class="modal-head">
            <h3 id="vtModalTitle">Add Visitor Type</h3>
            <button type="button" class="btn btn-sm btn-ghost" onclick="closeModal('modalAddVisitorType')">✕</button>
        </div>
        <form id="vtForm" method="POST" action="{{ route('lookups.visitor_types.store') }}">
            @csrf
            <input type="hidden" name="_method" id="vtMethod" value="POST">
            <div class="modal-body">
                <div class="field">
                    <label>Visitor Type Name</label>
                    <input type="text" name="name" id="vtName" required placeholder="">
                </div>
                
                <input type="hidden" name="status" id="vtStatusHidden" value="active">
                <div class="field" style="display: flex; justify-content: space-between; align-items: center; margin-top: 16px;">
                    <div style="padding-right: 16px;">
                        <label style="margin-bottom: 4px;">Active</label>
                        <div style="font-size: 12.5px; color: var(--text-muted);">Add, edit, or disable visitor type categories. Only active types appear on the Pre-Registration form.</div>
                    </div>
                    <label class="toggle-switch">
                        <input type="checkbox" id="vtStatusToggle" onchange="document.getElementById('vtStatusHidden').value = this.checked ? 'active' : 'inactive'">
                        <span class="toggle-slider"></span>
                    </label>
                </div>
            </div>
            <div class="modal-foot">
                <button type="button" class="btn" onclick="closeModal('modalAddVisitorType')">Cancel</button>
                <button type="submit" class="btn btn-primary"><svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="width: 14px; height: 14px;"><path d="M20 6L9 17l-5-5"/></svg> Save</button>
            </div>
        </form>
    </div>
</div>

<!-- 5. Add/Edit ID Type Modal -->
<div id="modalAddIdType" class="modal-root">
    <div class="modal-backdrop" onclick="closeModal('modalAddIdType')"></div>
    <div class="modal-panel" style="max-width: 440px;">
        <div class="modal-head">
            <h3 id="idTypeModalTitle">Add ID Type</h3>
            <button type="button" class="btn btn-sm btn-ghost" onclick="closeModal('modalAddIdType')">✕</button>
        </div>
        <form id="idTypeForm" method="POST" action="{{ route('lookups.id_types.store') }}">
            @csrf
            <input type="hidden" name="_method" id="idTypeMethod" value="POST">
            <div class="modal-body">
                <div class="field">
                    <label>Document Name *</label>
                    <input type="text" name="name" id="idTypeName" required placeholder="e.g. Emirates ID">
                </div>
                <div class="field">
                    <label>Document Code *</label>
                    <input type="text" name="code" id="idTypeCode" required placeholder="e.g. EID">
                </div>
                <div class="field">
                    <label>Status</label>
                    <select name="status" id="idTypeStatus">
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                    </select>
                </div>
            </div>
            <div class="modal-foot">
                <button type="button" class="btn" onclick="closeModal('modalAddIdType')">Cancel</button>
                <button type="submit" class="btn btn-primary">Save ID Type</button>
            </div>
        </form>
    </div>
</div>

<!-- 6. Add Meeting Room Modal -->
<div id="modalAddRoom" class="modal-root">
    <div class="modal-backdrop" onclick="closeModal('modalAddRoom')"></div>
    <div class="modal-panel" style="max-width: 480px;">
        <div class="modal-head">
            <h3>Add Meeting Room</h3>
            <button type="button" class="btn btn-sm btn-ghost" onclick="closeModal('modalAddRoom')">✕</button>
        </div>
        <form method="POST" action="{{ route('settings.meeting_rooms.store') }}" id="formAddRoom">
            @csrf
            <div class="modal-body">
                <div class="form-grid" style="margin-top: 0;">
                    <div class="field">
                        <label>Branch / Site *</label>
                        <select name="site_id" id="roomSiteId" required onchange="onRoomBranchChange()">
                            <option value="">Select Branch / Site</option>
                            @foreach ($sites as $s)
                                <option value="{{ $s->id }}" data-code="{{ $s->code ?? strtoupper(substr($s->name, 0, 3)) }}">{{ $s->name }} ({{ $s->code ?? strtoupper(substr($s->name, 0, 3)) }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="field">
                        <label>Floor <span style="font-weight: 400; color: var(--text-muted);">(Optional)</span></label>
                        <select name="floor_id" id="roomFloorId" onchange="updateRoomNameSample()">
                            <option value="">Select Floor</option>
                            @foreach ($floors as $fl)
                                <option value="{{ $fl->id }}" data-site-id="{{ $fl->site_id }}" data-name="{{ $fl->name }}">{{ $fl->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="field" style="margin-top: 14px;">
                    <label>Room Name *</label>
                    <input type="text" name="name" id="roomName" required placeholder="Branch code + Room name + floor no. & code">
                    <div class="cell-sub" id="roomNameSample" style="margin-top: 4px; font-size: 11.5px; color: var(--text-muted);">
                        Sample: <strong>Branch code[unique for each branch] + Roomname + floorno.&code</strong> (e.g. <span id="sampleCodePreview">BBT-Boardroom-FL02</span>)
                    </div>
                </div>

                <div class="field" style="margin-top: 14px;">
                    <label>Seating Capacity</label>
                    <input type="number" name="capacity" id="roomCapacity" value="8" min="1" max="500">
                </div>
            </div>
            <div class="modal-foot">
                <button type="button" class="btn" onclick="closeModal('modalAddRoom')">Cancel</button>
                <button type="submit" class="btn btn-primary">Save Room</button>
            </div>
        </form>
    </div>
</div>

<!-- 7. Add Delivery Carrier Modal -->
<div id="modalAddCarrier" class="modal-root">
    <div class="modal-backdrop" onclick="closeModal('modalAddCarrier')"></div>
    <div class="modal-panel" style="max-width: 440px;">
        <div class="modal-head">
            <h3>Add Delivery Carrier</h3>
            <button type="button" class="btn btn-sm btn-ghost" onclick="closeModal('modalAddCarrier')">✕</button>
        </div>
        <form method="POST" action="{{ route('settings.carriers.store') }}">
            @csrf
            <div class="modal-body">
                <div class="field">
                    <label>Carrier Name *</label>
                    <input type="text" name="name" required placeholder="e.g. UPS Express">
                </div>
                <div class="field" style="margin-top: 14px;">
                    <label>Tracking Code Prefix *</label>
                    <input type="text" name="code" required placeholder="e.g. UPS">
                </div>
            </div>
            <div class="modal-foot">
                <button type="button" class="btn" onclick="closeModal('modalAddCarrier')">Cancel</button>
                <button type="submit" class="btn btn-primary">Save Carrier</button>
            </div>
        </form>
    </div>
</div>

<!-- 8. Add Package Size Modal -->
<div id="modalAddSize" class="modal-root">
    <div class="modal-backdrop" onclick="closeModal('modalAddSize')"></div>
    <div class="modal-panel" style="max-width: 440px;">
        <div class="modal-head">
            <h3>Add Package Size</h3>
            <button type="button" class="btn btn-sm btn-ghost" onclick="closeModal('modalAddSize')">✕</button>
        </div>
        <form method="POST" action="{{ route('settings.package_sizes.store') }}">
            @csrf
            <div class="modal-body">
                <div class="field">
                    <label>Package Size Name *</label>
                    <input type="text" name="name" required placeholder="e.g. Small Box">
                </div>
                <div class="field" style="margin-top: 14px;">
                    <label>Description</label>
                    <input type="text" name="description" placeholder="e.g. Up to 30x20x15 cm (fits in small locker)">
                </div>
            </div>
            <div class="modal-foot">
                <button type="button" class="btn" onclick="closeModal('modalAddSize')">Cancel</button>
                <button type="submit" class="btn btn-primary">Save Size</button>
            </div>
        </form>
    </div>
</div>

<!-- 9. Add Floor Modal -->
<div id="modalAddFloor" class="modal-root">
    <div class="modal-backdrop" onclick="closeModal('modalAddFloor')"></div>
    <div class="modal-panel" style="max-width: 480px;">
        <div class="modal-head">
            <h3 id="floorModalTitle">Add Floor</h3>
            <button type="button" class="btn btn-sm btn-ghost" onclick="closeModal('modalAddFloor')">✕</button>
        </div>
        <form id="floorForm" method="POST" action="{{ route('settings.floors.store') }}">
            @csrf
            <input type="hidden" name="_method" id="floorMethod" value="POST">
            <div class="modal-body">
                <div class="field">
                    <label>Branch *</label>
                    <select name="site_id" id="floorSiteId" required onchange="onFloorBranchChange()">
                        @foreach ($sites as $s)
                            <option value="{{ $s->id }}">{{ $s->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="field">
                    <label>Floor Name *</label>
                    <div style="position: relative;">
                        <input type="text" name="name" id="floorName" required placeholder="e.g. Floor 6" autocomplete="off" oninput="onFloorNameInput()" onfocus="showFloorNameSuggestions()" onchange="validateFloorName()">
                        <div id="floorNameSuggestions" class="suggestions-dropdown" style="display: none; position: absolute; top: calc(100% + 4px); left: 0; right: 0; z-index: 60; background: var(--surface); max-height: 220px; overflow-y: auto;"></div>
                    </div>
                    <div id="floorNameError" style="display: none; color: #ef4444; font-size: 12px; margin-top: 5px; font-weight: 500;">
                        <svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width: 14px; height: 14px; vertical-align: -2px; margin-right: 3px;"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                        <span id="floorNameErrorMsg">This floor already exists for this branch.</span>
                    </div>
                    <div class="cell-sub" style="margin-top: 2px;">Click or type to view existing floors in this branch for reference</div>
                </div>
                <div class="field">
                    <label>Description</label>
                    <input type="text" name="description" id="floorDescription" placeholder="e.g. Engineering & development">
                </div>
                <div class="field">
                    <label>Areas / Rooms</label>
                    <input type="hidden" name="areas" id="floorAreas">
                    <div id="floorTagsWrapper" class="tag-input-container" onclick="document.getElementById('floorTagInput').focus()">
                        <div id="floorTagsList" style="display: contents;"></div>
                        <input type="text" id="floorTagInput" placeholder="Type area or select meeting room, press Enter..." autocomplete="off">
                    </div>
                    <div id="floorRoomSuggestions" class="suggestions-dropdown" style="display: none;"></div>
                    <div class="cell-sub" style="margin-top: 4px;">Press Enter to add custom area, or pick from branch meeting rooms</div>
                </div>
            </div>
            <div class="modal-foot">
                <button type="button" class="btn" onclick="closeModal('modalAddFloor')">Cancel</button>
                <button type="submit" class="btn btn-primary" id="floorSubmitBtn">
                    <svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 6L9 17l-5-5"/></svg>
                    <span>Save Floor</span>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- 10. SMTP Configuration Modal -->
<div id="modalSmtpConfig" class="modal-root">
    <div class="modal-backdrop" onclick="closeModal('modalSmtpConfig')"></div>
    <div class="modal-panel" style="max-width: 520px;">
        <div class="modal-head">
            <h3>SMTP Mail Server Configuration</h3>
            <button type="button" class="btn btn-sm btn-ghost" onclick="closeModal('modalSmtpConfig')">✕</button>
        </div>
        <form onsubmit="alert('SMTP configuration saved.'); closeModal('modalSmtpConfig'); return false;">
            <div class="modal-body">
                <div class="form-grid">
                    <div class="field"><label>SMTP Host *</label><input type="text" value="email-smtp.eu-west-1.amazonaws.com" required></div>
                    <div class="field"><label>Port</label><input type="number" value="587"></div>
                </div>
                <div class="form-grid">
                    <div class="field"><label>Encryption</label><select><option>TLS</option><option>SSL</option><option>None</option></select></div>
                    <div class="field"><label>Username</label><input type="text" value="AKIAIOSFODNN7EXAMPLE"></div>
                </div>
                <div class="field"><label>From Email *</label><input type="email" value="noreply@zapio.ae" required></div>
            </div>
            <div class="modal-foot">
                <button type="button" class="btn" onclick="closeModal('modalSmtpConfig')">Cancel</button>
                <button type="submit" class="btn btn-primary">Save SMTP Config</button>
            </div>
        </form>
    </div>
</div>

<script>
    // Tab switching engine
    function switchTab(tabKey) {
        // Toggle navitem active state
        document.querySelectorAll('.settings-nav-card .navitem').forEach(el => {
            el.classList.remove('active');
            if (el.getAttribute('onclick') && el.getAttribute('onclick').includes("'" + tabKey + "'")) {
                el.classList.add('active');
            }
        });

        // Toggle content pane
        document.querySelectorAll('.tab-pane').forEach(el => el.classList.remove('active'));
        const activePane = document.getElementById('tab-' + tabKey);
        if (activePane) {
            activePane.classList.add('active');
        }

        // Update URL query string without reloading page
        const newUrl = new URL(window.location);
        newUrl.searchParams.set('tab', tabKey);
        window.history.replaceState({}, '', newUrl);
    }

    // Submit active tab form
    function submitActiveTabForm() {
        const activePane = document.querySelector('.tab-pane.active');
        if (activePane) {
            const form = activePane.querySelector('form');
            if (form) {
                form.submit();
                return;
            }
        }
        alert('All changes on current tab saved successfully.');
    }

    // Working day chip click toggle
    function toggleDayChip(label) {
        const input = label.querySelector('input');
        const icon = label.querySelector('.ic');
        setTimeout(() => {
            if (input.checked) {
                label.classList.add('active');
                if (icon) icon.style.display = 'inline-block';
            } else {
                label.classList.remove('active');
                if (icon) icon.style.display = 'none';
            }
        }, 10);
    }

    // Logo preview on upload
    function previewUploadLogo(input) {
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('logoPreview').src = e.target.result;
            }
            reader.readAsDataURL(input.files[0]);
        }
    }

    // Set brand color
    function setBrandColor(color) {
        document.getElementById('brandColorHex').value = color;
        document.getElementById('brandColorPicker').value = color;
        document.querySelectorAll('.color-swatch').forEach(sw => {
            sw.classList.toggle('active', sw.style.backgroundColor.toLowerCase() === color.toLowerCase() || sw.title === color);
        });
    }

    // Modals open/close
    function openModal(id) {
        const modal = document.getElementById(id);
        if (modal) modal.classList.add('open');
    }
    function closeModal(id) {
        const modal = document.getElementById(id);
        if (modal) modal.classList.remove('open');
    }

    // Edit Department helper
    function editDepartment(dept) {
        document.getElementById('deptModalTitle').innerText = 'Edit Department';
        document.getElementById('deptForm').action = '/departments/' + dept.id;
        document.getElementById('deptMethod').value = 'PUT';
        document.getElementById('deptName').value = dept.name || '';
        document.getElementById('deptCode').value = dept.code || '';
        document.getElementById('deptHead').value = dept.head_employee_id || '';
        document.getElementById('deptDesc').value = dept.description || '';
        document.getElementById('deptStatus').value = dept.status || 'active';
        
        document.getElementById('deptContactEmail').value = dept.contact_email || '';
        document.getElementById('deptContactPhone').value = dept.contact_phone || '';
        document.getElementById('deptKioskSelectable').checked = !!dept.kiosk_selectable;
        document.getElementById('deptRequireNda').checked = !!dept.require_nda;
        document.getElementById('deptEscortRequired').checked = !!dept.escort_required;

        // Set Icon
        const iconKey = dept.icon_key || 'briefcase';
        const iconRadio = document.querySelector(`input[name="icon_key"][value="${iconKey}"]`);
        if (iconRadio) iconRadio.checked = true;

        // Set Floors
        const floorSelect = document.getElementById('deptFloor');
        if (dept.floors && dept.floors.length > 0) {
            floorSelect.value = dept.floors[0].id;
        } else {
            floorSelect.value = "";
        }
        
        openModal('modalAddDepartment');
    }

    // Edit Branch helper
    function editBranch(site) {
        document.getElementById('branchModalTitle').innerText = 'Edit Branch';
        document.getElementById('branchForm').action = '/sites/' + site.id;
        document.getElementById('branchMethod').value = 'PUT';
        document.getElementById('branchName').value = site.name || '';
        document.getElementById('branchNameAr').value = site.name_ar || '';
        document.getElementById('branchCity').value = site.city || 'Dubai';
        document.getElementById('branchCode').value = site.code || '';
        document.getElementById('branchFloors').value = site.floors || 1;
        document.getElementById('branchCapacity').value = site.capacity || 500;
        document.getElementById('branchAddress').value = site.address || '';
        document.getElementById('branchStatus').value = site.status || 'active';
        
        let vFrom = '';
        let vTo = '';
        if (site.visiting_hours) {
            const parts = site.visiting_hours.split(/–|-/); // handle en-dash or hyphen
            if (parts.length === 2) {
                vFrom = parts[0].trim();
                vTo = parts[1].trim();
            }
        }
        document.getElementById('branchVisitsFrom').value = vFrom;
        document.getElementById('branchVisitsTo').value = vTo;

        openModal('modalAddBranch');
    }

    // Edit Purpose helper
    function editPurpose(p) {
        document.getElementById('purposeModalTitle').innerText = 'Edit Purpose';
        document.getElementById('purposeForm').action = '/lookups/purposes/' + p.id;
        document.getElementById('purposeMethod').value = 'PUT';
        document.getElementById('purposeName').value = p.name || '';
        document.getElementById('purposeStatus').value = p.status || 'active';
        openModal('modalAddPurpose');
    }

    // Edit Visitor Type helper
    function editVisitorType(vt) {
        document.getElementById('vtModalTitle').innerText = 'Edit Visitor Type';
        document.getElementById('vtForm').action = '/lookups/visitor-types/' + vt.id;
        document.getElementById('vtMethod').value = 'PUT';
        document.getElementById('vtName').value = vt.name || '';
        
        const status = vt.status || 'active';
        document.getElementById('vtStatusHidden').value = status;
        document.getElementById('vtStatusToggle').checked = (status === 'active');
        
        openModal('modalAddVisitorType');
    }

    // Edit ID Type helper
    function editIdType(it) {
        document.getElementById('idTypeModalTitle').innerText = 'Edit ID Type';
        document.getElementById('idTypeForm').action = '/lookups/id-types/' + it.id;
        document.getElementById('idTypeMethod').value = 'PUT';
        document.getElementById('idTypeName').value = it.name || '';
        document.getElementById('idTypeCode').value = it.code || '';
        document.getElementById('idTypeStatus').value = it.status || 'active';
        openModal('modalAddIdType');
    }

    // Master data in JS
    window.settingsMeetingRooms = @json($meetingRooms);
    window.settingsFloors = @json($floors);
    window.settingsGeneralAreas = @json($generalAreas);

    // Update sample preview text below Room Name
    function updateRoomNameSample() {
        const siteSelect = document.getElementById('roomSiteId');
        const selectedOption = siteSelect ? siteSelect.options[siteSelect.selectedIndex] : null;
        const siteCode = (selectedOption && selectedOption.value) ? (selectedOption.getAttribute('data-code') || 'BBT') : 'BBT';

        const floorSelect = document.getElementById('roomFloorId');
        const floorOption = (floorSelect && floorSelect.selectedIndex > 0) ? floorSelect.options[floorSelect.selectedIndex] : null;
        let floorPart = 'FL02';
        if (floorOption && floorOption.value) {
            const rawFloorName = floorOption.getAttribute('data-name') || floorOption.textContent;
            const matchNum = rawFloorName.match(/\d+/);
            floorPart = matchNum ? 'FL' + matchNum[0].padStart(2, '0') : rawFloorName.replace(/[^A-Za-z0-9]/g, '').toUpperCase();
        }

        const previewSpan = document.getElementById('sampleCodePreview');
        if (previewSpan) {
            previewSpan.textContent = `${siteCode}-Boardroom-${floorPart}`;
        }
    }

    // Meeting Room Branch onchange handler
    function onRoomBranchChange() {
        const siteSelect = document.getElementById('roomSiteId');
        const selectedSiteId = siteSelect.value;
        const selectedOption = siteSelect.options[siteSelect.selectedIndex];
        const siteCode = selectedOption && selectedOption.value ? (selectedOption.getAttribute('data-code') || '') : '';
        const roomNameInput = document.getElementById('roomName');
        const floorSelect = document.getElementById('roomFloorId');

        // Clean reset prefix whenever branch changes (prevents mixing up multiple codes)
        roomNameInput.value = siteCode ? siteCode + '-' : '';

        // Filter floor dropdown by branch
        if (floorSelect) {
            const floorOptions = floorSelect.querySelectorAll('option');
            floorOptions.forEach(opt => {
                if (!opt.value) return; // Keep "Select Floor"
                const optSiteId = opt.getAttribute('data-site-id');
                if (!selectedSiteId || optSiteId === selectedSiteId) {
                    opt.style.display = '';
                } else {
                    opt.style.display = 'none';
                }
            });
            floorSelect.value = '';
        }

        updateRoomNameSample();
    }

    // Interactive Tag Input for Floor Areas
    let currentFloorTags = [];
    let currentEditingFloorId = null;

    function validateFloorName() {
        const nameInput = document.getElementById('floorName');
        const errorDiv = document.getElementById('floorNameError');
        const errorMsg = document.getElementById('floorNameErrorMsg');
        const submitBtn = document.getElementById('floorSubmitBtn');
        const siteSelect = document.getElementById('floorSiteId');
        if (!nameInput || !siteSelect) return true;

        const floorName = nameInput.value.trim().toLowerCase();
        const siteId = siteSelect.value;

        if (!floorName) {
            if (errorDiv) errorDiv.style.display = 'none';
            nameInput.style.borderColor = '';
            if (submitBtn) {
                submitBtn.disabled = false;
                submitBtn.style.opacity = '';
                submitBtn.style.cursor = '';
            }
            return true;
        }

        // Check if floor name already exists in this site
        const exists = (window.settingsFloors || []).some(f => {
            const fSiteId = f.site_id || (f.building ? f.building.site_id : '');
            if (String(fSiteId) !== String(siteId)) return false;
            if (currentEditingFloorId && String(f.id) === String(currentEditingFloorId)) return false;
            return String(f.name || '').trim().toLowerCase() === floorName;
        });

        if (exists) {
            if (errorDiv && errorMsg) {
                const siteName = siteSelect.options[siteSelect.selectedIndex]?.textContent || 'this branch';
                errorMsg.textContent = `Floor "${nameInput.value.trim()}" already exists in ${siteName}.`;
                errorDiv.style.display = 'block';
            }
            nameInput.style.borderColor = '#ef4444';
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.style.opacity = '0.5';
                submitBtn.style.cursor = 'not-allowed';
            }
            return false;
        } else {
            if (errorDiv) errorDiv.style.display = 'none';
            nameInput.style.borderColor = '';
            if (submitBtn) {
                submitBtn.disabled = false;
                submitBtn.style.opacity = '';
                submitBtn.style.cursor = '';
            }
            return true;
        }
    }

    // Autocomplete Suggestions for Floor Name (Only existing floors for the selected branch)
    function showFloorNameSuggestions(filterText = null) {
        const input = document.getElementById('floorName');
        const dropdown = document.getElementById('floorNameSuggestions');
        const siteSelect = document.getElementById('floorSiteId');
        if (!input || !dropdown || !siteSelect) return;

        const siteId = siteSelect.value;
        const siteName = siteSelect.options[siteSelect.selectedIndex]?.textContent || 'Selected Branch';
        const query = (filterText !== null ? filterText : input.value).trim().toLowerCase();

        // Existing Floors in this site only
        const branchFloors = (window.settingsFloors || []).filter(f => {
            const fSiteId = f.site_id || (f.building ? f.building.site_id : '');
            return String(fSiteId) === String(siteId);
        });

        const matchedFloors = branchFloors.filter(f => !query || String(f.name || '').toLowerCase().includes(query));

        if (matchedFloors.length === 0) {
            if (!query) {
                dropdown.innerHTML = `
                    <div style="padding: 10px 14px; font-size: 12px; color: var(--text-muted); text-align: center;">
                        No floors created yet in <strong>${escapeHtml(siteName)}</strong>.<br>You can name this floor e.g. <em>Floor 1</em> or <em>Level 1</em>.
                    </div>
                `;
                dropdown.style.display = 'block';
            } else {
                dropdown.style.display = 'none';
                dropdown.innerHTML = '';
            }
            return;
        }

        let html = '';
        html += `<div style="padding: 6px 12px; font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; color: var(--text-muted); background: var(--surface-2); border-bottom: 1px solid var(--border);">Existing floors in ${escapeHtml(siteName)}</div>`;

        matchedFloors.forEach(f => {
            const areaCount = (f.areas && f.areas.length) ? `${f.areas.length} areas` : (f.areas_text ? `${f.areas_text.split(',').length} areas` : 'No areas');
            html += `
                <div class="suggestion-item" onmousedown="selectFloorNameSuggestion('${escapeJs(f.name)}')">
                    <div style="display: flex; align-items: center; gap: 8px;">
                        <span style="font-size: 14px;">🏢</span>
                        <div>
                            <div style="font-weight: 600; color: var(--text-primary); font-size: 13px;">${escapeHtml(f.name)}</div>
                            <div style="font-size: 11px; color: var(--text-muted);">Existing Floor (${escapeHtml(areaCount)})</div>
                        </div>
                    </div>
                    <span style="font-size: 11px; font-weight: 600; color: var(--accent); background: var(--accent-soft); padding: 2px 7px; border-radius: 4px;">Use format</span>
                </div>
            `;
        });

        dropdown.innerHTML = html;
        dropdown.style.display = 'block';
    }

    function hideFloorNameSuggestions() {
        const dropdown = document.getElementById('floorNameSuggestions');
        if (dropdown) dropdown.style.display = 'none';
    }

    function onFloorNameInput() {
        const input = document.getElementById('floorName');
        if (input) {
            showFloorNameSuggestions(input.value);
        }
        validateFloorName();
    }

    function selectFloorNameSuggestion(value) {
        const input = document.getElementById('floorName');
        if (!input) return;
        input.value = value;
        hideFloorNameSuggestions();
        validateFloorName();
        input.focus();
        // Select trailing digits for quick editing (e.g. "Floor 2" -> selects "2")
        const match = value.match(/\d+$/);
        if (match) {
            const start = value.lastIndexOf(match[0]);
            input.setSelectionRange(start, value.length);
        } else {
            input.setSelectionRange(value.length, value.length);
        }
    }

    function renderFloorTags() {
        const listContainer = document.getElementById('floorTagsList');
        const hiddenInput = document.getElementById('floorAreas');
        if (!listContainer || !hiddenInput) return;

        listContainer.innerHTML = '';
        const currentSiteId = document.getElementById('floorSiteId')?.value || '';

        currentFloorTags.forEach(tagName => {
            const isMeetingRoom = window.settingsMeetingRooms.some(mr => 
                String(mr.name).toLowerCase() === String(tagName).toLowerCase() && 
                (!currentSiteId || String(mr.site_id) === String(currentSiteId))
            );

            const chip = document.createElement('span');
            chip.className = 'tagchip-item' + (isMeetingRoom ? ' is-meeting-room' : '');
            chip.innerHTML = `${escapeHtml(tagName)} <span class="tagchip-remove" onclick="removeFloorTag('${escapeJs(tagName)}')">&times;</span>`;
            listContainer.appendChild(chip);
        });

        hiddenInput.value = currentFloorTags.join(', ');
    }

    function addFloorTag(name) {
        const cleanName = (name || '').trim();
        if (!cleanName) return;
        // Avoid duplicate tags
        if (!currentFloorTags.some(t => t.toLowerCase() === cleanName.toLowerCase())) {
            currentFloorTags.push(cleanName);
            renderFloorTags();
        }
        const tagInput = document.getElementById('floorTagInput');
        if (tagInput) {
            tagInput.value = '';
        }
        hideFloorRoomSuggestions();
    }

    function removeFloorTag(name) {
        currentFloorTags = currentFloorTags.filter(t => t.toLowerCase() !== (name || '').toLowerCase());
        renderFloorTags();
    }

    function showFloorRoomSuggestions(query) {
        const dropdown = document.getElementById('floorRoomSuggestions');
        const siteId = document.getElementById('floorSiteId')?.value || '';
        if (!dropdown) return;

        const q = (query || '').toLowerCase();
        
        // 1. Filter meeting rooms for this branch not already tagged
        const mrMatches = (window.settingsMeetingRooms || []).filter(mr => {
            if (siteId && String(mr.site_id) !== String(siteId)) return false;
            if (currentFloorTags.some(t => t.toLowerCase() === String(mr.name).toLowerCase())) return false;
            return !q || String(mr.name).toLowerCase().includes(q);
        }).map(mr => ({ type: 'meeting_room', name: mr.name, seats: mr.capacity }));

        // 2. Filter general areas not already tagged
        const areaMatches = (window.settingsGeneralAreas || []).filter(areaName => {
            if (currentFloorTags.some(t => t.toLowerCase() === String(areaName).toLowerCase())) return false;
            // Only suggest if it's not also a meeting room name to avoid duplicates
            if ((window.settingsMeetingRooms || []).some(mr => String(mr.name).toLowerCase() === String(areaName).toLowerCase())) return false;
            return !q || String(areaName).toLowerCase().includes(q);
        }).map(areaName => ({ type: 'area', name: areaName, seats: null }));

        // Merge and show up to 10 suggestions
        const matches = [...mrMatches, ...areaMatches].slice(0, 10);

        if (matches.length === 0) {
            dropdown.style.display = 'none';
            dropdown.innerHTML = '';
            return;
        }

        dropdown.innerHTML = matches.map(item => {
            const isMr = item.type === 'meeting_room';
            const iconHtml = isMr ? `<svg class="ic" viewBox="0 0 24 24" fill="none" stroke="#e11d48" stroke-width="2" style="width:13px; height:13px; vertical-align:-2px; margin-right:4px;"><path d="M16 21v-2a4 4 0 00-8 0v2"/><circle cx="12" cy="7" r="4"/></svg>` : `<svg class="ic" viewBox="0 0 24 24" fill="none" stroke="#6b7280" stroke-width="2" style="width:13px; height:13px; vertical-align:-2px; margin-right:4px;"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>`;
            return `
            <div class="suggestion-item" onclick="addFloorTag('${escapeJs(item.name)}')">
                <span>${iconHtml}<strong>${escapeHtml(item.name)}</strong> <span style="color: var(--text-muted); font-size: 11px;">${isMr ? `(Seats ${item.seats || 8})` : '(General Area)'}</span></span>
                <span style="font-size: 11.5px; color: var(--accent); font-weight: 600;">+ Add</span>
            </div>
            `;
        }).join('');
        dropdown.style.display = 'block';
    }

    function hideFloorRoomSuggestions() {
        const dropdown = document.getElementById('floorRoomSuggestions');
        if (dropdown) dropdown.style.display = 'none';
    }

    function onFloorBranchChange() {
        const tagInput = document.getElementById('floorTagInput');
        if (tagInput) {
            showFloorRoomSuggestions(tagInput.value.trim());
        }
        renderFloorTags();
        validateFloorName();

        const floorDropdown = document.getElementById('floorNameSuggestions');
        if (floorDropdown && floorDropdown.style.display !== 'none') {
            showFloorNameSuggestions();
        }
    }

    function escapeHtml(str) {
        return (str || '').replace(/[&<>"']/g, m => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' })[m]);
    }

    function escapeJs(str) {
        return (str || '').replace(/'/g, "\\'").replace(/"/g, '\\"');
    }

    // Floor helpers
    function openFloorModal(id, siteId) {
        currentEditingFloorId = null;
        document.getElementById('floorModalTitle').innerText = 'Add Floor';
        document.getElementById('floorForm').action = "{{ route('settings.floors.store') }}";
        document.getElementById('floorMethod').value = 'POST';
        document.getElementById('floorName').value = '';
        document.getElementById('floorDescription').value = '';
        currentFloorTags = [];
        renderFloorTags();
        if (siteId) {
            document.getElementById('floorSiteId').value = siteId;
        }
        hideFloorNameSuggestions();
        validateFloorName();
        openModal('modalAddFloor');
    }

    function editFloor(fl) {
        currentEditingFloorId = fl.id;
        document.getElementById('floorModalTitle').innerText = 'Edit Floor';
        document.getElementById('floorForm').action = '/settings/floors/' + fl.id;
        document.getElementById('floorMethod').value = 'PUT';
        document.getElementById('floorName').value = fl.name || '';
        const siteId = fl.site_id || (fl.building ? fl.building.site_id : '');
        if (siteId) {
            document.getElementById('floorSiteId').value = siteId;
        }
        document.getElementById('floorDescription').value = fl.description || '';
        
        // Populate existing areas as tags
        currentFloorTags = [];
        if (fl.areas && Array.isArray(fl.areas)) {
            currentFloorTags = fl.areas.map(a => a.name).filter(Boolean);
        } else if (fl.areas_text) {
            currentFloorTags = fl.areas_text.split(',').map(s => s.trim()).filter(Boolean);
        }
        renderFloorTags();
        hideFloorNameSuggestions();
        validateFloorName();

        openModal('modalAddFloor');
    }

    // Check query string on page load and attach listeners
    window.addEventListener('DOMContentLoaded', () => {
        const urlParams = new URLSearchParams(window.location.search);
        const tab = urlParams.get('tab');
        if (tab) {
            switchTab(tab);
        }

        // Setup Floor Tag Input event listeners
        const tagInput = document.getElementById('floorTagInput');
        if (tagInput) {
            tagInput.addEventListener('keydown', (e) => {
                if (e.key === 'Enter' || e.key === ',') {
                    e.preventDefault();
                    addFloorTag(tagInput.value);
                } else if (e.key === 'Backspace' && !tagInput.value && currentFloorTags.length > 0) {
                    currentFloorTags.pop();
                    renderFloorTags();
                }
            });

            tagInput.addEventListener('input', () => {
                showFloorRoomSuggestions(tagInput.value.trim());
            });

            tagInput.addEventListener('focus', () => {
                showFloorRoomSuggestions(tagInput.value.trim());
            });
        }

        // Setup Floor Name input key listeners
        const floorNameInput = document.getElementById('floorName');
        if (floorNameInput) {
            floorNameInput.addEventListener('keydown', (e) => {
                if (e.key === 'Escape') {
                    hideFloorNameSuggestions();
                }
            });
        }

        // Prevent floorForm submit if floor name is duplicate
        const floorForm = document.getElementById('floorForm');
        if (floorForm) {
            floorForm.addEventListener('submit', (e) => {
                if (!validateFloorName()) {
                    e.preventDefault();
                    return false;
                }
            });
        }

        // Close suggestion dropdown when clicking outside
        document.addEventListener('click', (e) => {
            const wrapper = document.getElementById('floorTagsWrapper');
            const dropdown = document.getElementById('floorRoomSuggestions');
            if (dropdown && wrapper && !wrapper.contains(e.target) && !dropdown.contains(e.target)) {
                hideFloorRoomSuggestions();
            }

            const floorNameInput = document.getElementById('floorName');
            const floorNameDropdown = document.getElementById('floorNameSuggestions');
            if (floorNameDropdown && floorNameInput && !floorNameInput.contains(e.target) && !floorNameDropdown.contains(e.target)) {
                hideFloorNameSuggestions();
            }
        });
    });

    // Auto-translate Branch Name to Arabic
    let branchNameDebounceTimer;
    const branchNameInput = document.getElementById('branchName');
    const branchNameArInput = document.getElementById('branchNameAr');
    
    if (branchNameInput && branchNameArInput) {
        branchNameInput.addEventListener('input', function() {
            clearTimeout(branchNameDebounceTimer);
            const text = this.value.trim();
            branchNameDebounceTimer = setTimeout(async () => {
                if (!text) {
                    branchNameArInput.value = '';
                    return;
                }
                try {
                    const res = await fetch(`https://api.mymemory.translated.net/get?q=${encodeURIComponent(text)}&langpair=en|ar`);
                    const data = await res.json();
                    if (data && data.responseData && data.responseData.translatedText) {
                        branchNameArInput.value = data.responseData.translatedText;
                    }
                } catch (e) {
                    console.error('Translation failed', e);
                }
            }, 600);
        });
    }
    
    // Auto fade-out alerts after 5 seconds
    document.addEventListener('DOMContentLoaded', function() {
        const alerts = document.querySelectorAll('.alert-success, .alert-danger');
        alerts.forEach(alert => {
            setTimeout(() => {
                alert.style.transition = 'opacity 0.5s ease';
                alert.style.opacity = '0';
                setTimeout(() => alert.remove(), 500); // Remove from DOM after fade completes
            }, 5000);
        });
    });
</script>
</body>
</html>