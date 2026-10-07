<?php

$file = 'resources/views/employees/index.blade.php';
$content = file_get_contents($file);

// 1. Replace the Employee Detail Modal Header
$oldHeader = <<<'HTML'
                                <div>
                                    <div style="font-size:16px;font-weight:700;" id="detEmpName">Employee Name</div>
                                    <div class="cell-sub" id="detEmpTitle">Job Title</div>
                                </div>
                                <div style="margin-inline-start:auto;">
                                    <span class="chip chip-good" id="detEmpStatusChip">● Active</span>
                                </div>
                            </div>
HTML;
$newHeader = <<<'HTML'
                                <div>
                                    <div style="font-size:16px;font-weight:700;" id="detEmpName">Employee Name</div>
                                    <div class="cell-sub" id="detEmpTitle" style="margin-bottom:12px;">Job Title</div>
                                    <div class="hstack" style="gap:8px;">
                                        <button type="button" class="btn btn-sm" id="detEditBtn" style="padding:4px 12px; font-size:12px;">
                                            <svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:14px;height:14px;margin-right:4px;vertical-align:middle;"><path d="M12 20h9M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"/></svg> Edit
                                        </button>
                                        <button type="button" class="btn btn-sm" id="detDeleteBtn" style="padding:4px 12px; font-size:12px; color:#d93025; border-color:#fad2cf; background:#fce8e6;">
                                            <svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:14px;height:14px;margin-right:4px;vertical-align:middle;"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg> Delete
                                        </button>
                                        <form id="detDeleteForm" method="POST" style="display:none;">
                                            @csrf
                                            @method('DELETE')
                                        </form>
                                    </div>
                                </div>
                                <div style="margin-inline-start:auto; align-self:flex-start;">
                                    <span class="chip chip-good" id="detEmpStatusChip">● Active</span>
                                </div>
                            </div>
HTML;
$content = str_replace($oldHeader, $newHeader, $content);

// 2. Replace the Attendance History card
$oldAttendance = <<<'HTML'
                        <div class="card">
                            <div class="section-title">Attendance History</div>
                            <div class="cell-sub" style="margin-bottom:12px;">Past attendance logs and check-in times.</div>
                            <div class="tablewrap scrollx" style="max-height:200px;">
                                <table class="dtable">
                                    <thead><tr><th>Date</th><th>Check-in</th><th>Check-out</th><th>Status</th></tr></thead>
                                    <tbody>
                                        <tr><td class="mono">{{ now()->format('d M Y') }}</td><td class="mono">08:20 AM</td><td class="mono">—</td><td><span class="chip chip-good">Present</span></td></tr>
                                        <tr><td class="mono">{{ now()->subDay()->format('d M Y') }}</td><td class="mono">08:15 AM</td><td class="mono">05:30 PM</td><td><span class="chip chip-muted">Complete</span></td></tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
HTML;
$newAttendance = <<<'HTML'
                        <div class="card">
                            <div class="hstack" style="justify-content:space-between; margin-bottom:12px;">
                                <div class="section-title" style="margin:0;">Attendance History</div>
                                <select id="attendanceFilter" class="form-select" style="width:140px; padding:4px 8px; font-size:13px; border-radius:6px; border:1px solid #dcdcdc; background:#fff;">
                                    <option value="this_week">This Week</option>
                                    <option value="this_month">This Month</option>
                                    <option value="this_year">This Year</option>
                                    <option value="custom">Custom Range</option>
                                </select>
                            </div>
                            
                            <div id="attendanceCustomRange" style="display:none; gap:10px; margin-bottom:12px; align-items:flex-end;">
                                <div style="flex:1;">
                                    <label style="font-size:12px;color:var(--text-muted);display:block;margin-bottom:4px;">From</label>
                                    <input type="date" id="attendanceStartDate" class="form-control" style="width:100%; font-size:13px; padding:4px 8px; border-radius:6px; border:1px solid #dcdcdc; height:32px;">
                                </div>
                                <div style="flex:1;">
                                    <label style="font-size:12px;color:var(--text-muted);display:block;margin-bottom:4px;">To</label>
                                    <input type="date" id="attendanceEndDate" class="form-control" style="width:100%; font-size:13px; padding:4px 8px; border-radius:6px; border:1px solid #dcdcdc; height:32px;">
                                </div>
                                <button type="button" class="btn btn-sm btn-primary" id="attendanceApplyBtn" style="padding:4px 12px; height:32px;">Apply</button>
                            </div>

                            <div class="tablewrap scrollx" style="max-height:200px; overflow-y:auto; border-radius:8px;">
                                <table class="dtable" style="margin:0; border-collapse:collapse;">
                                    <thead style="position:sticky; top:0; background:#f4f7f6; z-index:1; border-bottom:1px solid #dcdcdc;">
                                        <tr>
                                            <th style="font-size:11px;font-weight:700;color:var(--text-muted);padding:8px 12px;text-transform:uppercase;">Date</th>
                                            <th style="font-size:11px;font-weight:700;color:var(--text-muted);padding:8px 12px;text-transform:uppercase;">Check-in</th>
                                            <th style="font-size:11px;font-weight:700;color:var(--text-muted);padding:8px 12px;text-transform:uppercase;">Check-out</th>
                                            <th style="font-size:11px;font-weight:700;color:var(--text-muted);padding:8px 12px;text-transform:uppercase;">Hours</th>
                                            <th style="font-size:11px;font-weight:700;color:var(--text-muted);padding:8px 12px;text-transform:uppercase;">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody id="attendanceTbody">
                                        <tr><td colspan="5" style="text-align:center;color:var(--text-muted);padding:20px;">Select a range to load history</td></tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
HTML;
$content = str_replace($oldAttendance, $newAttendance, $content);

// 3. JS Logic
$oldJS = <<<'JS'
        function openEmployeeDetailModal(emp) {
            document.getElementById('detEmpName').innerText = (emp.first_name + ' ' + (emp.last_name || '')).trim();
            document.getElementById('detEmpTitle').innerText = emp.job_title || 'Employee';
            document.getElementById('detEmpCode').innerText = emp.employee_code || '—';
            document.getElementById('detEmpSite').innerText = (emp.site && emp.site.name) ? emp.site.name : '—';
            document.getElementById('detEmpDept').innerText = (emp.department && emp.department.name) ? emp.department.name : '—';
            document.getElementById('detEmpShift').innerText = (emp.shift && emp.shift.name) ? emp.shift.name : '—';
            document.getElementById('detEmpEmail').innerText = emp.email || '—';
            document.getElementById('detEmpPhone').innerText = emp.phone || '—';
            
            const statusChip = document.getElementById('detEmpStatusChip');
            if(emp.status === 'active') {
                statusChip.className = 'chip chip-good';
                statusChip.innerHTML = '● Active';
            } else if(emp.status === 'on_leave') {
                statusChip.className = 'chip chip-warning';
                statusChip.innerHTML = '● On Leave';
            } else {
                statusChip.className = 'chip chip-muted';
                statusChip.innerHTML = '● Inactive';
            }

            document.getElementById('detIdName').innerText = (emp.first_name + ' ' + (emp.last_name || '')).trim();
            document.getElementById('detIdCode').innerText = emp.employee_code || 'EMP-001';
            
            const qrContainer = document.getElementById('detQrCanvas');
            qrContainer.innerHTML = '';
            if (typeof QRCode !== 'undefined') {
                new QRCode(qrContainer, {
                    text: emp.employee_code || 'EMP-001',
                    width: 140,
                    height: 140,
                });
            }
            openModal('modalEmployeeDetail');
        }
JS;

$newJS = <<<'JS'
        let currentDetailEmp = null;

        function openEmployeeDetailModal(emp) {
            currentDetailEmp = emp;
            document.getElementById('detEmpName').innerText = (emp.first_name + ' ' + (emp.last_name || '')).trim();
            document.getElementById('detEmpTitle').innerText = emp.job_title || 'Employee';
            document.getElementById('detEmpCode').innerText = emp.employee_code || '—';
            document.getElementById('detEmpSite').innerText = (emp.site && emp.site.name) ? emp.site.name : '—';
            document.getElementById('detEmpDept').innerText = (emp.department && emp.department.name) ? emp.department.name : '—';
            document.getElementById('detEmpShift').innerText = (emp.shift && emp.shift.name) ? emp.shift.name : '—';
            document.getElementById('detEmpEmail').innerText = emp.email || '—';
            document.getElementById('detEmpPhone').innerText = emp.phone || '—';
            
            const statusChip = document.getElementById('detEmpStatusChip');
            if(emp.status === 'active') {
                statusChip.className = 'chip chip-good';
                statusChip.innerHTML = '● Active';
            } else if(emp.status === 'on_leave') {
                statusChip.className = 'chip chip-warning';
                statusChip.innerHTML = '● On Leave';
            } else {
                statusChip.className = 'chip chip-muted';
                statusChip.innerHTML = '● Inactive';
            }

            // Edit and Delete buttons logic
            document.getElementById('detEditBtn').onclick = () => {
                closeModal('modalEmployeeDetail');
                openEmployeeModal(emp);
            };
            
            document.getElementById('detDeleteBtn').onclick = () => {
                if(confirm('Are you sure you want to delete this employee?')) {
                    const form = document.getElementById('detDeleteForm');
                    form.action = '/employees/' + emp.id;
                    form.submit();
                }
            };

            document.getElementById('detIdName').innerText = (emp.first_name + ' ' + (emp.last_name || '')).trim();
            document.getElementById('detIdCode').innerText = emp.employee_code || 'EMP-001';
            
            const qrContainer = document.getElementById('detQrCanvas');
            qrContainer.innerHTML = '';
            if (typeof QRCode !== 'undefined') {
                new QRCode(qrContainer, {
                    text: emp.employee_code || 'EMP-001',
                    width: 140,
                    height: 140,
                });
            }
            
            // Reset filter and fetch attendance
            const filterEl = document.getElementById('attendanceFilter');
            filterEl.value = 'this_week';
            document.getElementById('attendanceCustomRange').style.display = 'none';
            fetchEmployeeAttendance(emp.id, 'this_week');

            openModal('modalEmployeeDetail');
        }

        document.getElementById('attendanceFilter').addEventListener('change', function(e) {
            const val = e.target.value;
            const customRange = document.getElementById('attendanceCustomRange');
            if (val === 'custom') {
                customRange.style.display = 'flex';
                // Don't fetch automatically, wait for Apply
            } else {
                customRange.style.display = 'none';
                if (currentDetailEmp) {
                    fetchEmployeeAttendance(currentDetailEmp.id, val);
                }
            }
        });

        document.getElementById('attendanceApplyBtn').addEventListener('click', function() {
            const start = document.getElementById('attendanceStartDate').value;
            const end = document.getElementById('attendanceEndDate').value;
            if (currentDetailEmp) {
                fetchEmployeeAttendance(currentDetailEmp.id, 'custom', start, end);
            }
        });

        function fetchEmployeeAttendance(empId, filter, start = '', end = '') {
            const tbody = document.getElementById('attendanceTbody');
            tbody.innerHTML = '<tr><td colspan="5" style="text-align:center;color:var(--text-muted);padding:20px;">Loading...</td></tr>';
            
            let url = `/employees/${empId}/attendance?filter=${filter}`;
            if (filter === 'custom') {
                if (!start || !end) {
                    tbody.innerHTML = '<tr><td colspan="5" style="text-align:center;color:var(--text-muted);padding:20px;">Please select both start and end dates.</td></tr>';
                    return;
                }
                url += `&start_date=${start}&end_date=${end}`;
            }

            fetch(url)
                .then(res => res.json())
                .then(data => {
                    if (!data || data.length === 0) {
                        tbody.innerHTML = '<tr><td colspan="5" style="text-align:center;color:var(--text-muted);padding:20px;">No attendance records found for this period.</td></tr>';
                        return;
                    }
                    tbody.innerHTML = '';
                    data.forEach(row => {
                        const tr = document.createElement('tr');
                        tr.innerHTML = `
                            <td class="mono" style="padding:10px 12px;border-bottom:1px solid #f0f0f0;">${row.date_formatted}</td>
                            <td class="mono" style="padding:10px 12px;border-bottom:1px solid #f0f0f0;">${row.check_in}</td>
                            <td class="mono" style="padding:10px 12px;border-bottom:1px solid #f0f0f0;">${row.check_out}</td>
                            <td class="mono" style="padding:10px 12px;border-bottom:1px solid #f0f0f0;">${row.hours}</td>
                            <td style="padding:10px 12px;border-bottom:1px solid #f0f0f0;">${row.status_html}</td>
                        `;
                        tbody.appendChild(tr);
                    });
                })
                .catch(err => {
                    console.error('Failed to fetch attendance:', err);
                    tbody.innerHTML = '<tr><td colspan="5" style="text-align:center;color:#d93025;padding:20px;">Error loading attendance.</td></tr>';
                });
        }
JS;
$content = str_replace($oldJS, $newJS, $content);

file_put_contents($file, $content);
echo "Updated index.blade.php with attendance logic and buttons.\n";
