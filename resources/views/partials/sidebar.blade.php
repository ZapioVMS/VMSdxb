@php
    $current = $active ?? null;
    $isDashboard = $current === 'dashboard' || request()->routeIs('dashboard');
    $isVisitorLog = $current === 'visitors' || (request()->routeIs('visitors.index') && !request()->routeIs('visitors.checkin'));
    $isCheckin = $current === 'checkin' || request()->routeIs('visitors.checkin');
    $isPreReg = $current === 'pre_registrations' || request()->routeIs('pre_registrations.*');
    $isDeliveries = $current === 'deliveries';
    $isApprovals = $current === 'approvals';
    $isWatchlist = $current === 'watchlist';
    $isEvacuation = $current === 'evacuation';
    $isBadges = $current === 'badges';
    $isAgreements = $current === 'agreements';
    $isReports = $current === 'reports';
    $isEmployees = $current === 'employees' || request()->routeIs('employees.*');
    $isUsers = $current === 'users';
    $isNotifications = $current === 'notifications';
    $isSettings = $current === 'settings' || request()->routeIs('settings.*') || request()->routeIs('lookups.*') || request()->routeIs('sites.*') || request()->routeIs('departments.*');
@endphp

<aside id="sidebar">
    <!-- Brand Logo Section -->
    <div class="brand" style="padding: 16px 18px; border-block-end: 1px solid var(--border); display: flex; align-items: center;">
        <a href="{{ route('dashboard') }}" style="display: block; text-decoration: none;">
            <img class="brand-logo" src="{{ asset('images/logo.png') }}" alt="Zapio VMS" style="height: 38px; width: auto; max-width: 100%; display: block;">
        </a>
    </div>

    <!-- Navigation List grouped by Category Submenus -->
    <nav class="navlist">
        <!-- OPERATIONS -->
        <div class="navlabel">OPERATIONS</div>
        <a href="{{ route('dashboard') }}" class="navitem {{ $isDashboard ? 'active' : '' }}">
            <svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"><path d="M3 11.5 12 4l9 7.5"/><path d="M5.5 10v9a1 1 0 0 0 1 1H10v-6h4v6h3.5a1 1 0 0 0 1-1v-9"/></svg>
            <span>Dashboard</span>
        </a>
        <a href="{{ route('visitors.index') }}" class="navitem {{ $isVisitorLog ? 'active' : '' }}">
            <svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"><circle cx="9" cy="8" r="3"/><path d="M2.5 20c0-3.5 3-6 6.5-6s6.5 2.5 6.5 6"/><circle cx="17" cy="9" r="2.5"/><path d="M15.5 14.2c2.6.4 4.5 2.4 4.5 5.3"/></svg>
            <span>Visitor Log</span>
        </a>
        <a href="{{ route('visitors.checkin') }}" class="navitem {{ $isCheckin ? 'active' : '' }}">
            <svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"><circle cx="10" cy="8" r="3.2"/><path d="M3 20c0-3.6 3.1-6.3 7-6.3s7 2.7 7 6.3"/><path d="M19 8v5M16.5 10.5h5"/></svg>
            <span>Check-In</span>
        </a>
        <a href="{{ route('pre_registrations.index') }}" class="navitem {{ $isPreReg ? 'active' : '' }}">
            <svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"><rect x="3.5" y="5" width="17" height="15" rx="2"/><path d="M3.5 9.5h17M8 3v4M16 3v4"/></svg>
            <span>Pre-Registration</span>
        </a>
        <a href="{{ route('visitors.index') }}" class="navitem {{ $isDeliveries ? 'active' : '' }}">
            <svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3 3 8v8l9 5 9-5V8l-9-5z"/><path d="M3 8l9 5 9-5M12 13v8"/></svg>
            <span>Deliveries</span>
        </a>
        <a href="{{ route('dashboard') }}" class="navitem {{ $isApprovals ? 'active' : '' }}">
            <svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3l7 3v5.5c0 4.6-3 8-7 9.5-4-1.5-7-4.9-7-9.5V6l7-3z"/><path d="M9 12l2 2 4-4.5"/></svg>
            <span>Approvals</span>
            <span class="nbadge" style="margin-inline-start: auto; background: var(--critical, #d03b3b); color: #ffffff; font-size: 10.5px; font-weight: 700; border-radius: 99px; padding: 1px 6px; min-width: 16px; text-align: center;">1</span>
        </a>

        <!-- MANAGE -->
        <div class="navlabel">MANAGE</div>
        <a href="{{ route('dashboard') }}" class="navitem {{ $isWatchlist ? 'active' : '' }}">
            <svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3l7 3v5.5c0 4.6-3 8-7 9.5-4-1.5-7-4.9-7-9.5V6l7-3z"/><path d="M12 8.5v4.2M12 15.5h.01"/></svg>
            <span>Watchlist</span>
        </a>
        <a href="{{ route('dashboard') }}" class="navitem {{ $isEvacuation ? 'active' : '' }}">
            <svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3.5 21.5 20h-19L12 3.5z"/><path d="M12 9.5v4.5M12 17h.01"/></svg>
            <span>Evacuation</span>
        </a>
        <a href="{{ route('dashboard') }}" class="navitem {{ $isBadges ? 'active' : '' }}">
            <svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"><rect x="2.5" y="5.5" width="19" height="13" rx="2"/><circle cx="8" cy="12" r="2"/><path d="M6 16.2c0-1.3 1-2 2-2s2 .7 2 2M13.5 10h5M13.5 13h5"/></svg>
            <span>Badges</span>
        </a>
        <a href="{{ route('dashboard') }}" class="navitem {{ $isAgreements ? 'active' : '' }}">
            <svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"><path d="M7 3h7l4 4v14a1 1 0 0 1-1 1H7a1 1 0 0 1-1-1V4a1 1 0 0 1 1-1z"/><path d="M14 3v4h4M9 13h6M9 17h6M9 9h2"/></svg>
            <span>NDA / Agreements</span>
        </a>
        <a href="{{ route('dashboard') }}" class="navitem {{ $isReports ? 'active' : '' }}">
            <svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"><path d="M4 20V10M10 20V4M16 20v-7M4 20h16"/></svg>
            <span>Reports</span>
        </a>
        <a href="{{ route('employees.index') }}" class="navitem {{ $isEmployees ? 'active' : '' }}">
            <svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="7" width="18" height="12.5" rx="2"/><path d="M8.5 7V5.5A1.5 1.5 0 0 1 10 4h4a1.5 1.5 0 0 1 1.5 1.5V7M3 12.5h18"/></svg>
            <span>Employees</span>
        </a>

        <!-- SYSTEM -->
        <div class="navlabel">SYSTEM</div>
        <a href="{{ url('/settings?tab=security') }}" class="navitem {{ $isUsers ? 'active' : '' }}">
            <svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"><circle cx="9" cy="8" r="3.2"/><path d="M2.5 20c0-3.6 3-6.3 6.5-6.3S15.5 16.4 15.5 20"/><circle cx="18" cy="15" r="2.1"/><path d="M18 11.8v1M18 17.2v1M15.7 15h1M19.3 15h1M16.4 13.3l.7.7M18.9 15.7l.7.7M16.4 16.7l.7-.7M18.9 14.3l.7-.7"/></svg>
            <span>Users & Roles</span>
        </a>
        <a href="{{ url('/settings?tab=notifications') }}" class="navitem {{ $isNotifications ? 'active' : '' }}">
            <svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"><path d="M6 10a6 6 0 0 1 12 0c0 4 1.5 5.5 2 6H4c.5-.5 2-2 2-6z"/><path d="M10 19a2 2 0 0 0 4 0"/></svg>
            <span>Notifications</span>
        </a>
        <a href="{{ url('/settings') }}" class="navitem {{ $isSettings ? 'active' : '' }}">
            <svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="3"/><circle cx="12" cy="12" r="7.8"/></svg>
            <span>Settings</span>
        </a>
    </nav>

    <!-- Sidebar Footer / Logout -->
    <div class="sidebar-foot" style="padding: 12px; border-top: 1px solid var(--border);">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="navitem" style="border: none; background: none; width: 100%; cursor: pointer; display: flex; align-items: center; gap: 11px; padding: 9px 10px; border-radius: var(--radius-sm); color: var(--text-secondary); font-size: 13.5px; font-weight: 500;">
                <svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5.5A1.5 1.5 0 0 1 4 19.5v-15A1.5 1.5 0 0 1 5.5 3H9M15 16l5-4-5-4M20 12H9"/></svg>
                <span>Sign Out</span>
            </button>
        </form>
    </div>
</aside>
