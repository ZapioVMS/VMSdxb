<?php

$file = 'resources/views/pre_registrations/index.blade.php';
$content = file_get_contents($file);

// 1. Update the option tag
$oldOptionStr = <<<'HTML'
                                  @foreach ($employees as $employee)
                                      <option value="{{ $employee->id }}" data-site="{{ $employee->site_id }}">{{ $employee->full_name }} ({{ $employee->department->name ?? 'General' }})</option>
                                  @endforeach
HTML;

$newOptionStr = <<<'HTML'
                                  @foreach ($employees as $employee)
                                      <option value="{{ $employee->id }}" data-site="{{ $employee->site_id }}" data-leaves="{{ $employee->leaves->pluck('date')->map(fn($d) => $d->format('Y-m-d'))->join(',') }}">{{ $employee->full_name }} ({{ $employee->department->name ?? 'General' }})</option>
                                  @endforeach
HTML;

if (strpos($content, $oldOptionStr) !== false) {
    $content = str_replace($oldOptionStr, $newOptionStr, $content);
} else {
    echo "Could not find option string.\n";
}

// 2. Update filter function
$oldFilterStr = <<<'JS'
        function filterHostEmployeesBySite(siteId) {
            const hostSelect = document.getElementById('inv_host_employee_id');
            const options = hostSelect.querySelectorAll('option');
            options.forEach(opt => {
                if (!opt.value) return; // Keep "Select Host Employee"
                const empSite = opt.getAttribute('data-site');
                if (!siteId || siteId === 'all' || empSite === siteId) {
                    opt.style.display = '';
                } else {
                    opt.style.display = 'none';
                }
            });
            hostSelect.value = ''; // Reset selection
        }
JS;

$newFilterStr = <<<'JS'
        function filterHostEmployees() {
            const siteSelect = document.getElementById('inv_site_id');
            const siteId = siteSelect ? siteSelect.value : '';
            const dateInput = document.getElementById('inv_visit_date');
            const visitDate = dateInput ? dateInput.value : '';

            const hostSelect = document.getElementById('inv_host_employee_id');
            const options = hostSelect.querySelectorAll('option');
            
            options.forEach(opt => {
                if (!opt.value) return; // Keep "Select Host Employee"
                const empSite = opt.getAttribute('data-site');
                const leaves = opt.getAttribute('data-leaves') ? opt.getAttribute('data-leaves').split(',') : [];
                
                let show = true;
                if (siteId && siteId !== 'all' && empSite !== siteId) {
                    show = false;
                }
                if (visitDate && leaves.includes(visitDate)) {
                    show = false;
                }

                opt.style.display = show ? '' : 'none';
                if (!show && hostSelect.value === opt.value) {
                    hostSelect.value = '';
                }
            });
        }
JS;

if (strpos($content, $oldFilterStr) !== false) {
    $content = str_replace($oldFilterStr, $newFilterStr, $content);
} else {
    echo "Could not find filter JS.\n";
}

// 3. Update calls to filterHostEmployeesBySite to filterHostEmployees
$content = str_replace('filterHostEmployeesBySite(this.value)', 'filterHostEmployees()', $content);
$content = str_replace('filterHostEmployeesBySite(data.site_id)', 'filterHostEmployees()', $content);

// 4. Add onchange to visit_date input
$oldDateInput = '<input type="date" id="inv_visit_date" name="visit_date" required>';
$newDateInput = '<input type="date" id="inv_visit_date" name="visit_date" onchange="filterHostEmployees()" required>';

if (strpos($content, $oldDateInput) !== false) {
    $content = str_replace($oldDateInput, $newDateInput, $content);
} else {
    echo "Could not find date input.\n";
}

file_put_contents($file, $content);
echo "View updated successfully.\n";
