<?php

// 1. Update invite_modal.blade.php
$file = 'resources/views/partials/invite_modal.blade.php';
$content = file_get_contents($file);

$oldOptionStr = <<<'HTML'
                                @foreach ($employees as $employee)
                                    <option value="{{ $employee->id }}" data-site="{{ $employee->site_id }}">{{ $employee->full_name }} ({{ $employee->department->name ?? 'General' }})</option>
                                @endforeach
HTML;

$newOptionStr = <<<'HTML'
                                @foreach ($employees as $employee)
                                    <option value="{{ $employee->id }}" data-site="{{ $employee->site_id }}" data-leaves="{{ $employee->leaves ? $employee->leaves->pluck('date')->map(fn($d) => $d->format('Y-m-d'))->join(',') : '' }}">{{ $employee->full_name }} ({{ $employee->department->name ?? 'General' }})</option>
                                @endforeach
HTML;

if (strpos($content, $oldOptionStr) !== false) {
    $content = str_replace($oldOptionStr, $newOptionStr, $content);
}

// Add onchange to visit_date
$oldDateInput = '<input type="date" id="inv_visit_date" name="visit_date" required>';
$newDateInput = '<input type="date" id="inv_visit_date" name="visit_date" onchange="filterHostEmployeesBySite(document.getElementById(\'inv_site_id\').value)" required>';
$content = str_replace($oldDateInput, $newDateInput, $content);

file_put_contents($file, $content);

// 2. Update invite_modal_js.blade.php
$fileJs = 'resources/views/partials/invite_modal_js.blade.php';
if (file_exists($fileJs)) {
    $contentJs = file_get_contents($fileJs);
    
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
        function filterHostEmployeesBySite(siteId) {
            const hostSelect = document.getElementById('inv_host_employee_id');
            const options = hostSelect.querySelectorAll('option');
            const visitDate = document.getElementById('inv_visit_date') ? document.getElementById('inv_visit_date').value : '';
            
            options.forEach(opt => {
                if (!opt.value) return; // Keep "Select Host Employee"
                const empSite = opt.getAttribute('data-site');
                const leavesStr = opt.getAttribute('data-leaves') || '';
                const leaves = leavesStr ? leavesStr.split(',') : [];
                
                let show = true;
                if (siteId && siteId !== 'all' && empSite !== siteId) {
                    show = false;
                }
                if (visitDate && leaves.includes(visitDate)) {
                    show = false;
                }

                opt.style.display = show ? '' : 'none';
            });
            // If currently selected host is hidden, reset it
            const selectedOpt = hostSelect.options[hostSelect.selectedIndex];
            if (selectedOpt && selectedOpt.style.display === 'none') {
                hostSelect.value = '';
            }
        }
JS;

    if (strpos($contentJs, $oldFilterStr) !== false) {
        $contentJs = str_replace($oldFilterStr, $newFilterStr, $contentJs);
        file_put_contents($fileJs, $contentJs);
        echo "invite_modal_js.blade.php updated.\n";
    } else {
        echo "Could not find JS function in invite_modal_js.blade.php.\n";
    }
}

// 3. Check pre_registrations/index.blade.php (sometimes code is duplicated there)
$fileIndex = 'resources/views/pre_registrations/index.blade.php';
if (file_exists($fileIndex)) {
    $contentIndex = file_get_contents($fileIndex);
    if (strpos($contentIndex, $oldFilterStr) !== false) {
        $contentIndex = str_replace($oldFilterStr, $newFilterStr, $contentIndex);
        file_put_contents($fileIndex, $contentIndex);
        echo "pre_registrations/index.blade.php updated.\n";
    }
}
