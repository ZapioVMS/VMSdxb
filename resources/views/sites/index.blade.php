<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Branches — Madkhal</title>
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

        .card { background: var(--surface); border: 1px solid var(--border); border-radius: var(--radius-lg); padding: 20px; }
        .hstack { display: flex; align-items: center; gap: 10px; }
        .cell-sub { font-size: 11.5px; color: var(--text-muted); }
        .chip { display: inline-flex; align-items: center; padding: 3px 9px; border-radius: 99px; font-size: 11.5px; font-weight: 600; }
        .chip-good { background: rgba(12, 163, 12, 0.14); color: var(--good); }

        .alert-success { background: rgba(12, 163, 12, 0.14); border: 1px solid rgba(12, 163, 12, 0.30); color: var(--good); padding: 12px 16px; border-radius: var(--radius-sm); font-size: 13.5px; margin-bottom: 18px; }
        .alert-danger { background: rgba(208, 59, 59, 0.14); border: 1px solid rgba(208, 59, 59, 0.30); color: var(--critical); padding: 12px 16px; border-radius: var(--radius-sm); font-size: 13.5px; margin-bottom: 18px; }

        .modal-root { display: none; position: fixed; inset: 0; z-index: 100; }
        .modal-root.open { display: block; }
        .modal-backdrop { position: absolute; inset: 0; background: rgba(6,12,11,.5); }
        .modal-panel { position: relative; max-width: 540px; width: calc(100% - 32px); margin: 8vh auto 0; background: var(--surface); border-radius: var(--radius-lg); border: 1px solid var(--border); box-shadow: var(--shadow); overflow: hidden; }
        .modal-head { display: flex; align-items: center; justify-content: space-between; padding: 16px 20px; border-bottom: 1px solid var(--border); }
        .modal-body { padding: 20px; }
        .modal-foot { display: flex; justify-content: flex-end; gap: 8px; padding: 14px 20px; border-top: 1px solid var(--border); }
        .form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }
        .field { display: flex; flex-direction: column; gap: 5px; margin-bottom: 12px; }
        .field label { font-size: 12.5px; font-weight: 600; color: var(--text-secondary); }
        .field input, .field select, .field textarea { font-family: inherit; font-size: 13.5px; padding: 8px 10px; border-radius: var(--radius-sm); border: 1px solid var(--border); background: var(--surface); color: var(--text-primary); width: 100%; }
    </style>
</head>
<body>
    <div id="app">
        @include('partials.sidebar', ['active' => 'sites'])


        @include('partials.topbar')

        <main id="main">
            <div class="pagehead">
                <div>
                    <h1>Branches</h1>
                    <div class="sub">Manage the buildings and floors covered by this system.</div>
                </div>
                <button class="btn btn-primary" onclick="openSiteModal()">+ Add Site</button>
            </div>

            @if (session('success'))
                <div class="alert-success"><strong>{{ session('success') }}</strong></div>
            @endif
            @if (session('error'))
                <div class="alert-danger"><strong>{{ session('error') }}</strong></div>
            @endif

            <div class="grid-3">
                @forelse ($sites as $site)
                    <div class="card" style="padding:20px;">
                        <div style="display:flex; align-items:flex-start; justify-content:space-between; margin-bottom:14px;">
                            <div style="display:flex; align-items:center; gap:12px;">
                                <div style="width:38px; height:38px; border-radius:10px; background:var(--accent-soft); color:var(--accent); display:flex; align-items:center; justify-content:center; font-size:17px; font-weight:700;">🏢</div>
                                <div>
                                    <div style="font-weight:700; font-size:15.5px;">{{ $site->name }}</div>
                                    <div class="cell-sub" style="font-size:12px; color:var(--text-muted);">{{ $site->city ?? 'Dubai' }}</div>
                                </div>
                            </div>
                            <button onclick="openSiteModal({{ json_encode($site) }})" title="Edit Branch" style="background:none; border:none; color:var(--text-muted); cursor:pointer; padding:4px;">
                                <svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75"><path d="M12 20h9"/><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"/></svg>
                            </button>
                        </div>

                        <div style="display:grid; grid-template-columns:1fr 1fr; gap:14px 10px; font-size:13px; margin-top:14px;">
                            <div>
                                <div style="font-size:11.5px; color:var(--text-muted); font-weight:500;">Floors</div>
                                <div style="font-weight:700; font-size:15px; margin-top:2px;">{{ $site->floors ?? ($site->buildings_count > 0 ? $site->buildings_count * 12 : ($site->id == 1 ? 32 : ($site->id == 2 ? 12 : 8))) }}</div>
                            </div>
                            <div>
                                <div style="font-size:11.5px; color:var(--text-muted); font-weight:500;">Capacity</div>
                                <div style="font-weight:700; font-size:15px; margin-top:2px;">{{ $site->capacity ?? 1200 }}</div>
                            </div>
                            <div>
                                <div style="font-size:11.5px; color:var(--text-muted); font-weight:500;">Visiting hours</div>
                                <div style="font-weight:700; font-size:13px; margin-top:2px;">{{ $site->id == 2 ? '07:30–18:30' : ($site->id == 3 ? '08:00–18:00' : '08:00–19:00') }}</div>
                            </div>
                            <div>
                                <div style="font-size:11.5px; color:var(--text-muted); font-weight:500;">on-site now</div>
                                <div style="font-weight:700; font-size:15px; color:var(--good); margin-top:2px;">{{ $site->active_visitors_count ?? 0 }}</div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div style="grid-column: 1 / -1;" class="card">No branches found for this organization.</div>
                @endforelse
            </div>
        </main>
    </div>

    <!-- Site Modal -->
    <div id="siteModal" class="modal-root">
        <div class="modal-backdrop" onclick="closeSiteModal()"></div>
        <div class="modal-panel">
            <div class="modal-head">
                <h3 id="modalTitle">Add Site Location</h3>
                <button class="btn" onclick="closeSiteModal()">✕</button>
            </div>
            <form id="siteForm" action="{{ route('sites.store') }}" method="POST">
                @csrf
                <input type="hidden" id="site_method" name="_method" value="POST">
                <div class="modal-body">
                    <div class="form-grid">
                        <div class="field">
                            <label for="site_name">Site Name *</label>
                            <input type="text" id="site_name" name="name" required placeholder="Business Bay Tower" oninput="debouncedTranslate()">
                        </div>
                        <div class="field">
                            <div style="display:flex;justify-content:space-between;align-items:center;">
                                <label for="site_name_ar">Arabic Name</label>
                                <button type="button" id="btnTranslate" onclick="translateSiteName()" style="background:none;border:none;color:var(--accent);font-size:11.5px;font-weight:600;cursor:pointer;padding:0;" title="Translate English name to Arabic">🌐 Auto Translate</button>
                            </div>
                            <input type="text" id="site_name_ar" name="name_ar" placeholder="برج الخليج التجاري" dir="rtl">
                        </div>
                        <div class="field">
                            <label for="site_floors">Floor nos</label>
                            <input type="number" id="site_floors" name="floors" placeholder="32" min="0">
                        </div>
                        <div class="field">
                            <label for="site_city">City / Emirate</label>
                            <input type="text" id="site_city" name="city" placeholder="Dubai">
                        </div>
                        <div class="field">
                            <label for="site_capacity">Capacity</label>
                            <input type="number" id="site_capacity" name="capacity" placeholder="1200">
                        </div>
                        <div class="field">
                            <label for="site_status">Status *</label>
                            <select id="site_status" name="status" required>
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                        </div>
                        <div class="field" style="grid-column: 1 / -1;">
                            <label for="site_address">Street Address</label>
                            <textarea id="site_address" name="address" rows="2" placeholder="Business Bay, Dubai, UAE"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-foot">
                    <button type="button" class="btn" onclick="closeSiteModal()">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Site</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        let translateTimer = null;

        function openSiteModal(site) {
            const modal = document.getElementById('siteModal');
            const form = document.getElementById('siteForm');
            const method = document.getElementById('site_method');
            const title = document.getElementById('modalTitle');

            if (site) {
                title.innerText = 'Edit Site Location';
                form.action = '/sites/' + site.id;
                method.value = 'PUT';
                document.getElementById('site_name').value = site.name || '';
                document.getElementById('site_name_ar').value = site.name_ar || '';
                document.getElementById('site_floors').value = site.floors !== null && site.floors !== undefined ? site.floors : '';
                document.getElementById('site_city').value = site.city || '';
                document.getElementById('site_capacity').value = site.capacity || '';
                document.getElementById('site_status').value = site.status || 'active';
                document.getElementById('site_address').value = site.address || '';
            } else {
                title.innerText = 'Add Site Location';
                form.action = "{{ route('sites.store') }}";
                method.value = 'POST';
                form.reset();
            }
            modal.classList.add('open');
        }

        function closeSiteModal() {
            document.getElementById('siteModal').classList.remove('open');
        }

        function debouncedTranslate() {
            clearTimeout(translateTimer);
            translateTimer = setTimeout(() => {
                const arInput = document.getElementById('site_name_ar');
                if (arInput && !arInput.value.trim()) {
                    translateSiteName();
                }
            }, 600);
        }

        async function translateSiteName() {
            const nameInput = document.getElementById('site_name');
            const arInput = document.getElementById('site_name_ar');
            const btn = document.getElementById('btnTranslate');
            const text = nameInput ? nameInput.value.trim() : '';

            if (!text) return;

            if (btn) btn.innerText = '⏳ Translating...';

            try {
                const res = await fetch(`https://api.mymemory.translated.net/get?q=${encodeURIComponent(text)}&langpair=en|ar`);
                const data = await res.json();
                if (data && data.responseData && data.responseData.translatedText) {
                    arInput.value = data.responseData.translatedText;
                }
            } catch (e) {
                console.error('Translation failed:', e);
            } finally {
                if (btn) btn.innerText = '🌐 Auto Translate';
            }
        }
    </script>
</body>
</html>
