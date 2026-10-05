<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Approvals — Zapio VMS</title>
    <link rel="icon" href="{{ !empty($globalBranding['favicon_url']) ? $globalBranding['favicon_url'] : asset('favicon.ico') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=IBM+Plex+Sans:wght@400;500;600;700&display=swap">
    <style>
        :root {
            --accent: #0c6b64;
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
        .navlist { padding: 10px; display: flex; flex-direction: column; gap: 2px; flex: 1; }
        .navlabel { font-size: 10.5px; text-transform: uppercase; letter-spacing: .06em; color: var(--text-muted); padding: 14px 10px 6px; }
        .navitem { display: flex; align-items: center; gap: 11px; padding: 9px 10px; border-radius: var(--radius-sm); color: var(--text-secondary); cursor: pointer; font-size: 13.5px; font-weight: 500; text-decoration: none; }
        .navitem:hover { background: var(--surface-2); color: var(--text-primary); }
        .navitem.active { background: rgba(12, 107, 100, 0.10); color: var(--accent); }

        .pagehead { display: flex; align-items: flex-start; justify-content: space-between; gap: 16px; margin-bottom: 20px; }
        .pagehead h1 { font-size: 21px; margin: 0; }
        .pagehead .sub { color: var(--text-secondary); font-size: 13px; margin-top: 4px; }

        .subtabs { display: flex; gap: 22px; border-block-end: 1px solid var(--border); margin-bottom: 18px; }
        .subtab { background: none; border: none; font: inherit; font-size: 13.5px; font-weight: 600; color: var(--text-muted); padding: 9px 1px 12px; cursor: pointer; border-block-end: 2px solid transparent; margin-block-end: -1px; text-decoration: none; }
        .subtab:hover { color: var(--text-primary); }
        .subtab.active { color: var(--accent); border-block-end-color: var(--accent); }

        .card { background: var(--surface); border: 1px solid var(--border); border-radius: var(--radius); padding: 18px; margin-bottom: 16px; }
        .tablewrap { border: 1px solid var(--border); border-radius: var(--radius); overflow: hidden; background: var(--surface); }
        table.dtable { width: 100%; border-collapse: collapse; }
        .dtable th { text-align: start; font-size: 11px; text-transform: uppercase; color: var(--text-muted); padding: 11px 14px; border-bottom: 1px solid var(--border); background: var(--surface-2); }
        .dtable td { padding: 11px 14px; border-bottom: 1px solid var(--border); font-size: 13px; }
        .dtable tr:hover { background: var(--surface-2); }
        
        .chip { display: inline-flex; align-items: center; padding: 3px 9px; border-radius: 99px; font-size: 11.5px; font-weight: 600; white-space: nowrap; }
        .chip-good { background: rgba(12, 163, 12, 0.14); color: var(--good); }
        .chip-warning { background: rgba(250, 178, 25, 0.18); color: #8a5a00; }
        .chip-muted { background: var(--surface-3); color: var(--text-secondary); }

        .btn { display: inline-flex; align-items: center; gap: 7px; padding: 8px 14px; border-radius: var(--radius-sm); border: 1px solid var(--border); background: var(--surface); color: var(--text-primary); font-size: 13px; font-weight: 600; cursor: pointer; }
        .btn-primary { background: var(--accent); color: white; border-color: var(--accent); }
        .btn-danger { color: var(--critical); border-color: var(--critical); background: transparent; }

        .modal-root { display: none; position: fixed; inset: 0; z-index: 100; }
        .modal-root.open { display: block; }
        .modal-backdrop { position: absolute; inset: 0; background: rgba(6,12,11,.5); backdrop-filter: blur(2px); }
        .modal-panel { position: relative; max-width: 500px; margin: 10vh auto; background: var(--surface); border-radius: var(--radius); box-shadow: var(--shadow); }
        .modal-head { padding: 16px 20px; border-bottom: 1px solid var(--border); display: flex; justify-content: space-between; }
        .modal-body { padding: 20px; }
        .modal-foot { padding: 16px 20px; border-top: 1px solid var(--border); display: flex; justify-content: flex-end; gap: 10px; background: var(--surface-2); }
    </style>
</head>
<body>
    <div id="app">
        @include('partials.sidebar', ['active' => 'approvals'])
        @include('partials.topbar')

        <main id="main">
            <div class="pagehead">
                <div>
                    <h1>Approvals</h1>
                    <div class="sub">Review and action pending requests requiring your authorization.</div>
                </div>
            </div>

            @if (session('success'))
                <div class="card" style="background: rgba(12, 163, 12, 0.12); border-color: rgba(12, 163, 12, 0.3); color: var(--good); padding: 12px 16px; margin-bottom: 16px; font-weight: 500;">
                    ✓ {{ session('success') }}
                </div>
            @endif

            <div class="subtabs">
                <a href="?tab=pending" class="subtab {{ request('tab', 'pending') === 'pending' ? 'active' : '' }}">Pending Approvals ({{ $pendingApprovals->count() }})</a>
                <a href="?tab=history" class="subtab {{ request('tab') === 'history' ? 'active' : '' }}">History</a>
            </div>

            @if(request('tab', 'pending') === 'pending')
                <div class="tablewrap">
                    <table class="dtable">
                        <thead>
                            <tr>
                                <th>Request Type</th>
                                <th>Requester</th>
                                <th>Status</th>
                                <th>Created</th>
                                <th style="text-align:end;">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($pendingApprovals as $req)
                                <tr>
                                    <td>
                                        <div style="font-weight:600;">{{ $req->request_type }}</div>
                                        <div style="font-size:11.5px; color:var(--text-muted);">{{ Str::afterLast($req->approvable_type, '\\') }} #{{ $req->approvable_id }}</div>
                                    </td>
                                    <td>{{ $req->requester->name ?? 'System' }}</td>
                                    <td>
                                        @if($req->status === 'pending_l1')
                                            <span class="chip chip-warning">Pending L1</span>
                                        @elseif($req->status === 'pending_l2')
                                            <span class="chip chip-warning">Pending L2</span>
                                        @else
                                            <span class="chip chip-muted">{{ ucwords(str_replace('_', ' ', $req->status)) }}</span>
                                        @endif
                                    </td>
                                    <td style="color:var(--text-muted); font-size:12px;">{{ $req->created_at->diffForHumans() }}</td>
                                    <td style="text-align:end;">
                                        <button class="btn btn-sm btn-primary" onclick="openActionModal({{ $req->id }}, '{{ $req->request_type }}')">Review</button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" style="text-align:center; padding: 40px; color:var(--text-muted);">No pending approvals.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            @else
                <div class="tablewrap">
                    <table class="dtable">
                        <thead>
                            <tr>
                                <th>Request Type</th>
                                <th>Requester</th>
                                <th>Outcome</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($history as $req)
                                <tr>
                                    <td>
                                        <div style="font-weight:600;">{{ $req->request_type }}</div>
                                    </td>
                                    <td>{{ $req->requester->name ?? 'System' }}</td>
                                    <td>
                                        @if($req->status === 'approved')
                                            <span class="chip chip-good">Approved</span>
                                        @else
                                            <span class="chip chip-critical" style="background: rgba(208, 59, 59, 0.14); color: var(--critical);">Rejected</span>
                                        @endif
                                    </td>
                                    <td style="color:var(--text-muted); font-size:12px;">{{ $req->decided_at ? \Carbon\Carbon::parse($req->decided_at)->format('d M Y, H:i') : '' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" style="text-align:center; padding: 40px; color:var(--text-muted);">No history found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            @endif

        </main>
    </div>

    <!-- ACTION MODAL -->
    <div id="modalAction" class="modal-root">
        <div class="modal-backdrop" onclick="document.getElementById('modalAction').classList.remove('open')"></div>
        <div class="modal-panel">
            <div class="modal-head">
                <h3 id="actionModalTitle">Review Request</h3>
                <button type="button" class="btn btn-sm" style="border:none;background:none;" onclick="document.getElementById('modalAction').classList.remove('open')">✕</button>
            </div>
            <form id="actionForm" method="POST" action="">
                @csrf
                <div class="modal-body">
                    <div style="margin-bottom:14px;">
                        <label style="font-size:12px; font-weight:600; color:var(--text-secondary);">Action</label>
                        <select name="action" style="width:100%; padding:8px; margin-top:4px; border-radius:4px; border:1px solid var(--border);">
                            <option value="approve">Approve</option>
                            <option value="reject">Reject</option>
                            <option value="request_more_info">Request More Info</option>
                        </select>
                    </div>
                    <div>
                        <label style="font-size:12px; font-weight:600; color:var(--text-secondary);">Comments</label>
                        <textarea name="comments" rows="3" style="width:100%; padding:8px; margin-top:4px; border-radius:4px; border:1px solid var(--border);" placeholder="Optional reasoning..."></textarea>
                    </div>
                </div>
                <div class="modal-foot">
                    <button type="submit" class="btn btn-primary">Submit Decision</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openActionModal(id, title) {
            document.getElementById('actionModalTitle').innerText = 'Review: ' + title;
            document.getElementById('actionForm').action = '/approvals/' + id + '/action';
            document.getElementById('modalAction').classList.add('open');
        }
    </script>
</body>
</html>
