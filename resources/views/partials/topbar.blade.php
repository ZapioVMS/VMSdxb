<header id="topbar" style="grid-row:1; grid-column:2; background:var(--surface); border-block-end:1px solid var(--border); display:flex; align-items:center; gap:16px; padding-inline:20px; height:60px; min-width:0;">
    <!-- Left Section: Site Filter Dropdown + Global Search Bar -->
    <div style="display:flex; align-items:center; gap:12px; flex:1; max-width:650px;">
        <!-- Hamburger Menu Button (Mobile Only) -->
        <button type="button" class="mobile-only-btn" onclick="toggleSidebar()" style="border:none; background:none; padding:8px; cursor:pointer; color:var(--text-primary); margin-left:-10px;">
            <svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="18" x2="21" y2="18"/></svg>
        </button>
        <!-- Site Dropdown -->
        <div style="position:relative; flex:none;">
            <select id="globalSiteSelect" onchange="handleGlobalSiteChange(this.value)" style="font-family:inherit; font-size:13px; font-weight:600; padding:7px 32px 7px 16px; border-radius:99px; border:1.5px solid var(--accent); background:var(--surface); color:var(--text-primary); cursor:pointer; outline:none; appearance:none; -webkit-appearance:none;">
                <option value="all" {{ request('site_id') == 'all' || !request('site_id') ? 'selected' : '' }}>All Sites</option>
                @if(isset($globalSites))
                    @foreach($globalSites as $gSite)
                        <option value="{{ $gSite->id }}" {{ request('site_id') == $gSite->id ? 'selected' : '' }}>{{ $gSite->name }}</option>
                    @endforeach
                @endif
            </select>
            <svg style="position:absolute; right:12px; top:50%; transform:translateY(-50%); pointer-events:none; width:13px; height:13px; color:var(--accent);" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="m6 9 6 6 6-6"/></svg>
        </div>

        <!-- Search Bar: searches the records of the page you are on -->
        @php
            $routeName = optional(request()->route())->getName();
            // Pages with their own server-side search: the box submits to the same page and keeps its filters
            $serverSearch = [
                'visitors.index' => 'Search visitor log: name, phone, company, host...',
                'pre_registrations.index' => 'Search pre-registrations: name, email, phone, company, code...',
                'employees.index' => 'Search employees: name, email, employee ID...',
                'deliveries.index' => 'Search deliveries: package, tracking no., carrier, recipient...',
                'gate_passes.index' => 'Search gate passes: code, name, company / plate...',
            ];
            // Pages whose lists are filtered in place as you type
            $clientSearch = [
                'users.index' => 'Search users: name, email, role...',
                'approvals.index' => 'Search approvals...',
                'settings.index' => 'Search this settings tab...',
                'visitors.checkin' => 'Search today\'s expected visitors...',
            ];
            $isServerSearch = isset($serverSearch[$routeName]);
            $isClientSearch = isset($clientSearch[$routeName]);
            $searchAction = $isServerSearch ? route($routeName) : route('visitors.index');
            $searchPlaceholder = $serverSearch[$routeName] ?? $clientSearch[$routeName] ?? 'Search visitors, ID, company...';
            $searchValue = $isClientSearch ? '' : request('search');
        @endphp
        <form id="topbarSearchForm" action="{{ $searchAction }}" method="GET" style="flex:1; margin:0;" @if ($isClientSearch) onsubmit="event.preventDefault(); topbarFilterRows(this.querySelector('input[name=search]').value);" @endif>
            @if ($isServerSearch)
                @foreach (request()->except(['search', 'page']) as $keepKey => $keepVal)
                    @if (is_string($keepVal) && $keepVal !== '')
                        <input type="hidden" name="{{ $keepKey }}" value="{{ $keepVal }}">
                    @endif
                @endforeach
            @endif
            <div style="display:flex; align-items:center; gap:8px; background:var(--surface-2); border:1px solid var(--border); border-radius:99px; padding:6px 14px; width:100%;">
                <svg class="ic" style="color:var(--text-muted); width:15px; height:15px;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
                <input type="text" name="search" placeholder="{{ $searchPlaceholder }}" value="{{ $searchValue }}" autocomplete="off" @if ($isClientSearch) oninput="topbarFilterRows(this.value)" @endif style="border:none; background:none; outline:none; font-size:13px; font-family:inherit; color:var(--text-primary); width:100%;">
                @if ($isServerSearch && request()->filled('search'))
                    <a href="{{ route($routeName, request()->except(['search', 'page'])) }}" title="Clear search" style="color:var(--text-muted); text-decoration:none; font-size:13px; line-height:1; padding:2px 4px;">✕</a>
                @endif
            </div>
        </form>
    </div>

    <div style="flex:1;"></div>

    <!-- Right Section: Quick Action Lightning Button, EN/AR Language Toggle, Notification Bell, Profile Avatar -->
    <div style="display:flex; align-items:center; gap:12px; flex:none;">
        <!-- Quick Action Lightning Icon Button -->
        <a href="{{ route('visitors.checkin') }}" style="width:34px; height:34px; border-radius:99px; background:var(--accent); color:#ffffff; display:flex; align-items:center; justify-content:center; text-decoration:none; transition:transform 0.15s ease;" title="New Check-In">
            <svg style="width:15px; height:15px;" viewBox="0 0 24 24" fill="currentColor"><path d="M13 2L3 14h9l-1 8 10-12h-9l1-8z"/></svg>
        </a>

        <!-- EN / AR Language Toggle Switch -->
        <div style="display:inline-flex; align-items:center; background:var(--surface-2); border:1px solid var(--border); border-radius:99px; padding:2px; font-size:11px; font-weight:700;">
            <button type="button" style="padding:4px 9px; border-radius:99px; border:none; background:var(--accent); color:#ffffff; cursor:pointer; font-size:11px; font-weight:700;">EN</button>
            <button type="button" style="padding:4px 9px; border-radius:99px; border:none; background:none; color:var(--text-secondary); cursor:pointer; font-size:11px; font-weight:700;">AR</button>
        </div>

        <!-- Notification Bell Icon with Red Count Badge -->
        <div style="position:relative; cursor:pointer;" title="Notifications (4 pending)">
            <div style="width:34px; height:34px; border-radius:99px; background:var(--surface-2); border:1px solid var(--border); display:flex; align-items:center; justify-content:center; color:var(--text-secondary);">
                <svg class="ic" style="width:16px; height:16px;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75"><path d="M6 8a6 6 0 0 1 12 0c0 7 3 9 3 9H3s3-2 3-9"/><path d="M10.3 21a1.94 1.94 0 0 0 3.4 0"/></svg>
            </div>
            <span style="position:absolute; top:-2px; right:-2px; width:16px; height:16px; border-radius:99px; background:var(--critical); color:#ffffff; font-size:9.5px; font-weight:700; display:flex; align-items:center; justify-content:center;">4</span>
        </div>

        <!-- Profile Circle Avatar with User Initials -->
        <div style="display:flex; align-items:center; gap:8px;">
            <div style="width:34px; height:34px; border-radius:99px; background:var(--surface-3); color:var(--text-primary); border:1.5px solid var(--border); font-size:12px; font-weight:700; display:flex; align-items:center; justify-content:center;" title="{{ Auth::user()->name ?? 'User Profile' }}">
                {{ strtoupper(substr(Auth::user()->name ?? 'AM', 0, 2)) }}
            </div>
        </div>
    </div>
</header>

<script>
    // In-page search for pages without server-side search (Users, Approvals, Settings, Check-In)
    function topbarFilterRows(q) {
        q = (q || '').trim().toLowerCase();
        const main = document.getElementById('main') || document.body;
        // Table rows, plus record cards (cards with Edit/Delete actions, e.g. departments or branches in Settings)
        const candidates = [
            ...main.querySelectorAll('table tbody tr'),
            ...[...main.querySelectorAll('[class*="grid"] > .card')].filter(c =>
                c.querySelector('[title="Edit"], [title="Delete"], [onclick*="edit"], [onclick*="delete"], [onclick*="Edit"], [onclick*="Delete"]')),
        ];
        const rows = candidates.filter(tr =>
            !tr.closest('.modal-root') && (tr.dataset.topbarHidden === '1' || tr.offsetParent !== null));
        let shown = 0, total = 0;
        rows.forEach(tr => {
            if (tr.children.length === 1 && tr.querySelector('td[colspan]')) return; // "no records" rows
            total++;
            const match = !q || tr.innerText.toLowerCase().includes(q);
            tr.style.display = match ? '' : 'none';
            tr.dataset.topbarHidden = match ? '' : '1';
            if (match) shown++;
        });
        let note = document.getElementById('topbarSearchNote');
        if (!q) { if (note) note.remove(); return; }
        if (!note) {
            note = document.createElement('div');
            note.id = 'topbarSearchNote';
            note.style.cssText = 'display:flex;align-items:center;gap:10px;margin:0 0 14px;font-size:13px;color:var(--text-secondary);';
            main.prepend(note);
        }
        const esc = t => t.replace(/[&<>"']/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));
        note.innerHTML = `<span>Showing <strong>${shown}</strong> of ${total} records matching <strong style="color:var(--text-primary);">“${esc(q)}”</strong></span>`
            + `<button type="button" class="btn btn-sm" onclick="const i=document.querySelector('#topbarSearchForm input[name=search]'); i.value=''; topbarFilterRows('');">✕ Clear search</button>`;
    }
</script>
<script>
    function handleGlobalSiteChange(siteId) {
        const currentUrl = new URL(window.location.href);
        currentUrl.searchParams.set('site_id', siteId);
        window.location.href = currentUrl.toString();
    }
</script>
