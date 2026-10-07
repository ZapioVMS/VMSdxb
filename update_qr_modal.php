<?php

$file = 'resources/views/partials/qr_modal_js.blade.php';
if (!file_exists($file)) die("File not found");

$content = file_get_contents($file);

$oldStr = '{{ $employee->full_name }} ({{ $employee->department->name ?? \'General\' }})</option>';
$newStr = 'data-leaves="{{ $employee->leaves ? $employee->leaves->pluck(\'date\')->map(fn($d) => $d->format(\'Y-m-d\'))->join(\',\') : \'\' }}">{{ $employee->full_name }} ({{ $employee->department->name ?? \'General\' }})</option>';

$content = str_replace($oldStr, $newStr, $content);

// Add onchange to date input in qr_modal_js
$oldDateInput = '<input type="date" id="inv_visit_date" name="visit_date" required>';
$newDateInput = '<input type="date" id="inv_visit_date" name="visit_date" required onchange="filterHostEmployeesBySite(document.getElementById(\'inv_site_id\').value);">';

$content = str_replace($oldDateInput, $newDateInput, $content);

file_put_contents($file, $content);
echo "Updated qr_modal_js.blade.php\n";
