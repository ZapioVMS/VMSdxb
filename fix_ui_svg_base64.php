<?php
$file = 'resources/views/employees/index.blade.php';
$content = file_get_contents($file);

// Replace the broken data URIs with a clean base64 data URI for the chevron and calendar icons
$chevronSvg = '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#666" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"></polyline></svg>';
$chevronBase64 = 'data:image/svg+xml;base64,' . base64_encode($chevronSvg);

$calendarSvg = '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#666" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>';
$calendarBase64 = 'data:image/svg+xml;base64,' . base64_encode($calendarSvg);

// Find the inputs and replace the background URL
$content = preg_replace("/background:url\('data:image\/svg\+xml;utf8,<svg.*?<\/svg>'\)/is", "background:url('{{REPLACE_ME}}')", $content);

// Replace select dropdowns with chevron
$content = preg_replace("/id=\"shiftStartTime\".*?\{\{REPLACE_ME\}\}/s", "id=\"shiftStartTime\" class=\"form-select\" style=\"width:100%; padding:10px 12px; border:1px solid #dcdcdc; border-radius:8px; font-size:14px; color:#333; appearance:none; background:url('".$chevronBase64."')", $content);
$content = preg_replace("/id=\"shiftEndTime\".*?\{\{REPLACE_ME\}\}/s", "id=\"shiftEndTime\" class=\"form-select\" style=\"width:100%; padding:10px 12px; border:1px solid #dcdcdc; border-radius:8px; font-size:14px; color:#333; appearance:none; background:url('".$chevronBase64."')", $content);

// Replace date inputs with calendar
$content = preg_replace("/id=\"shiftStartDate\".*?\{\{REPLACE_ME\}\}/s", "id=\"shiftStartDate\" class=\"form-control\" style=\"width:100%; padding:10px 12px; border:1px solid #dcdcdc; border-radius:8px; font-size:14px; color:#666; appearance:none; background:url('".$calendarBase64."')", $content);
$content = preg_replace("/id=\"shiftEndDate\".*?\{\{REPLACE_ME\}\}/s", "id=\"shiftEndDate\" class=\"form-control\" style=\"width:100%; padding:10px 12px; border:1px solid #dcdcdc; border-radius:8px; font-size:14px; color:#666; appearance:none; background:url('".$calendarBase64."')", $content);

file_put_contents($file, $content);
echo "Fixed SVG quotes using base64 in index.blade.php\n";
