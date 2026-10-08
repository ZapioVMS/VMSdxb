<?php
$file = 'resources/views/employees/index.blade.php';
$content = file_get_contents($file);

$reportFind = <<<'HTML'
            <div id="tab-report" style="display: {{ $tab === 'report' ? 'block' : 'none' }};">
                <div class="filterbar" style="align-items:center;">
                    <select class="select-filter" style="min-width:200px;">
                        <option value="all">All Employees</option>
                        @foreach ($allEmployees as $e)
                            <option value="{{ $e->id }}">{{ $e->full_name }}</option>
                        @endforeach
                    </select>

                    <select class="select-filter" style="min-width:160px;">
                        <option value="week" selected>This Week</option>
                        <option value="month">This Month</option>
                        <option value="year">This Year</option>
                        <option value="today">Today</option>
                    </select>

                    <select class="select-filter" style="min-width:160px;">
                        <option value="">All Branches</option>
                        @foreach ($sites as $s)
                            <option value="{{ $s->id }}">{{ $s->name }}</option>
                        @endforeach
                    </select>

                    <select class="select-filter" style="min-width:160px;">
                        <option value="">All Departments</option>
                        @foreach ($departments as $d)
                            <option value="{{ $d->id }}">{{ $d->name }}</option>
                        @endforeach
                    </select>

                    <div style="margin-inline-start:auto;">
                        <button type="button" class="btn btn-sm" onclick="exportEmployeesCsv()">
                            <svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                            <span>Export</span>
                        </button>
                    </div>
                </div>

                <div class="card">
                    <div class="tablewrap scrollx">
                        <table class="dtable">
                            <thead>
                                <tr>
                                    <th class="nosort">Name</th>
                                    <th class="nosort">Employee ID</th>
                                    <th class="nosort">Branch</th>
                                    <th class="nosort">Department</th>
                                    <th class="nosort">Days Present</th>
                                    <th class="nosort">Days Absent</th>
                                    <th class="nosort">Days On Leave</th>
                                    <th class="nosort">Total Hours</th>
                                    <th class="nosort">Avg Check-in</th>
                                    <th class="nosort" style="text-align:end;">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($employees->take(10) as $emp)
                                    <tr>
                                        <td><div class="cell-name">{{ $emp->full_name }}</div></td>
                                        <td class="mono">{{ $emp->employee_code }}</td>
                                        <td>{{ $emp->site->name ?? '—' }}</td>
                                        <td>{{ $emp->department->name ?? '—' }}</td>
                                        <td class="mono">5</td>
                                        <td class="mono">0</td>
                                        <td class="mono">{{ $emp->status === 'on_leave' ? 1 : 0 }}</td>
                                        <td class="mono">42h 15m</td>
                                        <td class="mono">08:24 AM</td>
                                        <td style="text-align:end;">
                                            <button type="button" class="btn btn-sm" onclick="openEmployeeDetailModal({{ json_encode($emp) }})">
                                                <svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                                                <span>View</span>
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
HTML;

$reportReplace = <<<'HTML'
            <div id="tab-report" style="display: {{ $tab === 'report' ? 'block' : 'none' }};">
                <form method="GET" action="{{ route('employees.index') }}" style="display: flex; gap: 10px; align-items: center; margin-bottom: 20px;">
                    <input type="hidden" name="tab" value="report">
                    <select name="report_employee_id" class="select-filter" style="min-width:200px;" onchange="this.form.submit()">
                        <option value="all" {{ ($reportEmployeeId ?? 'all') === 'all' ? 'selected' : '' }}>All Employees</option>
                        @foreach ($allEmployees as $e)
                            <option value="{{ $e->id }}" {{ ($reportEmployeeId ?? '') == $e->id ? 'selected' : '' }}>{{ $e->full_name }}</option>
                        @endforeach
                    </select>

                    <select name="report_date_range" class="select-filter" style="min-width:160px;" onchange="this.form.submit()">
                        <option value="today" {{ ($reportDateRange ?? '') === 'today' ? 'selected' : '' }}>Today</option>
                        <option value="this_week" {{ ($reportDateRange ?? '') === 'this_week' ? 'selected' : '' }}>This Week</option>
                        <option value="this_month" {{ ($reportDateRange ?? '') === 'this_month' ? 'selected' : '' }}>This Month</option>
                        <option value="this_year" {{ ($reportDateRange ?? '') === 'this_year' ? 'selected' : '' }}>This Year</option>
                        <option value="custom" {{ ($reportDateRange ?? '') === 'custom' ? 'selected' : '' }}>Custom Range</option>
                    </select>

                    <div style="margin-inline-start:auto;">
                        <button type="button" class="btn btn-sm" onclick="exportEmployeesCsv()">
                            <svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                            <span>Export CSV</span>
                        </button>
                    </div>
                </form>

                @if(isset($selectedReportEmployee))
                    <div style="margin-bottom: 20px;">
                        <span style="font-weight: 600; color: #333; margin-right: 8px;">All Employees</span>
                        <span style="font-weight: 700; font-size: 15px;">{{ $selectedReportEmployee->full_name }}</span>
                        <span style="color: #888; font-size: 13px; margin-left: 8px;">{{ $selectedReportEmployee->employee_code }} - {{ $selectedReportEmployee->department->name ?? 'N/A' }}</span>
                    </div>
                @endif

                <!-- KPI Cards -->
                <div class="grid-4" style="margin-bottom: 24px;">
                    <div class="stat-tile" style="background: #fff;">
                        <div class="stat-label" style="font-size:13px; color:#555;">Days Present</div>
                        <div class="stat-value-row"><span class="stat-value tabular" style="font-size:28px;">{{ $reportKpis['present'] ?? 0 }}</span></div>
                    </div>
                    <div class="stat-tile" style="background: #fff;">
                        <div class="stat-label" style="font-size:13px; color:#555;">Days Absent</div>
                        <div class="stat-value-row"><span class="stat-value tabular" style="font-size:28px;">{{ $reportKpis['absent'] ?? 0 }}</span></div>
                    </div>
                    <div class="stat-tile" style="background: #fff;">
                        <div class="stat-label" style="font-size:13px; color:#555;">Days On Leave</div>
                        <div class="stat-value-row"><span class="stat-value tabular" style="font-size:28px;">{{ $reportKpis['leave'] ?? 0 }}</span></div>
                    </div>
                    <div class="stat-tile" style="background: #fff;">
                        <div class="stat-label" style="font-size:13px; color:#555;">Total Hours</div>
                        <div class="stat-value-row"><span class="stat-value tabular" style="font-size:28px;">{{ $reportKpis['hours'] ?? '0h 0m' }}</span></div>
                    </div>
                </div>

                <div class="card">
                    <div class="tablewrap scrollx">
                        <table class="dtable">
                            <thead>
                                <tr>
                                    @if(!isset($selectedReportEmployee))
                                        <th class="nosort">Name</th>
                                    @endif
                                    <th class="nosort">DATE</th>
                                    <th class="nosort">CHECK-IN</th>
                                    <th class="nosort">CHECK-OUT</th>
                                    <th class="nosort">HOURS</th>
                                    <th class="nosort">STATUS</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if(isset($reportRecords) && $reportRecords->count() > 0)
                                    @foreach ($reportRecords as $rec)
                                        @php
                                            $checkInStr = $rec->check_in_at ? \Carbon\Carbon::parse($rec->check_in_at)->format('h:i A') : '—';
                                            $checkOutStr = $rec->check_out_at ? \Carbon\Carbon::parse($rec->check_out_at)->format('h:i A') : '—';
                                            $hoursStr = '—';
                                            $statusStr = '<span class="chip chip-muted">Absent</span>';
                                            
                                            if ($rec->check_in_at && $rec->check_out_at) {
                                                $diffMins = \Carbon\Carbon::parse($rec->check_in_at)->diffInMinutes(\Carbon\Carbon::parse($rec->check_out_at));
                                                $hrs = floor($diffMins / 60);
                                                $mins = $diffMins % 60;
                                                $hoursStr = "{$hrs}h {$mins}m";
                                            }

                                            if ($rec->status === 'late') {
                                                $statusStr = '<span class="chip chip-warning" style="background:#fff4e5;color:#b26a00;">Late Check-in</span>';
                                            } elseif ($rec->status === 'early') {
                                                $statusStr = '<span class="chip chip-warning" style="background:#fff4e5;color:#b26a00;">Early Checkout</span>';
                                            } elseif ($rec->check_out_at) {
                                                $statusStr = '<span class="chip chip-good" style="background:#e6f4ea;color:#137333;">Complete</span>';
                                            } elseif ($rec->check_in_at) {
                                                $statusStr = '<span class="chip chip-good" style="background:#e6f4ea;color:#137333;">On Site</span>';
                                            }
                                        @endphp
                                        <tr>
                                            @if(!isset($selectedReportEmployee))
                                                <td><div class="cell-name">{{ $rec->employee->full_name }}</div></td>
                                            @endif
                                            <td class="mono" style="font-weight:500;">{{ \Carbon\Carbon::parse($rec->attendance_date)->format('j M Y') }}</td>
                                            <td class="mono">{{ $checkInStr }}</td>
                                            <td class="mono">{{ $checkOutStr }}</td>
                                            <td class="mono">{{ $hoursStr }}</td>
                                            <td>{!! $statusStr !!}</td>
                                        </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="{{ !isset($selectedReportEmployee) ? '6' : '5' }}" style="text-align: center; padding: 20px; color: #888;">No attendance records found for this period.</td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
HTML;

$content = str_replace($reportFind, $reportReplace, $content);
file_put_contents($file, $content);
echo "View updated.\n";
