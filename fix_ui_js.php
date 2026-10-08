<?php
$file = 'resources/views/employees/index.blade.php';
$content = file_get_contents($file);

$oldJS = <<<'JS'
        function openEmployeeDetailModal(emp) {
            const name = (emp.first_name + ' ' + (emp.last_name || '')).trim();
            document.getElementById('detEmpName').innerText = name;
            document.getElementById('detIdName').innerText = name;
            document.getElementById('detEmpTitle').innerText = emp.job_title || 'Employee';
            document.getElementById('detEmpCode').innerText = emp.employee_code || '—';
            document.getElementById('detIdCode').innerText = emp.employee_code || '—';
            document.getElementById('detEmpSite').innerText = (emp.site && emp.site.name) ? emp.site.name : '—';
            document.getElementById('detEmpDept').innerText = (emp.department && emp.department.name) ? emp.department.name : '—';
            document.getElementById('detEmpShift').innerText = (emp.shift && emp.shift.name) ? emp.shift.name : '—';
            document.getElementById('detEmpEmail').innerText = emp.email || '—';
            document.getElementById('detEmpPhone').innerText = emp.phone || '—';

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
            const name = (emp.first_name + ' ' + (emp.last_name || '')).trim();
            document.getElementById('detEmpName').innerText = name;
            document.getElementById('detIdName').innerText = name;
            document.getElementById('detEmpTitle').innerText = emp.job_title || 'Employee';
            document.getElementById('detEmpCode').innerText = emp.employee_code || '—';
            document.getElementById('detIdCode').innerText = emp.employee_code || '—';
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

        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('attendanceFilter').addEventListener('change', function(e) {
                const val = e.target.value;
                const customRange = document.getElementById('attendanceCustomRange');
                if (val === 'custom') {
                    customRange.style.display = 'flex';
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

// Try to normalize line endings and spaces just in case
$normalizedContent = preg_replace('/\r\n/', "\n", $content);
$normalizedOldJS = preg_replace('/\r\n/', "\n", $oldJS);

$content = str_replace($normalizedOldJS, $newJS, $normalizedContent);

if (strpos($content, 'function fetchEmployeeAttendance') !== false) {
    file_put_contents($file, $content);
    echo "JS updated successfully.";
} else {
    echo "JS replacement failed.";
}
