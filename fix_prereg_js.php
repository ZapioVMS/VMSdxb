<?php
$file = 'resources/views/pre_registrations/index.blade.php';
$content = file_get_contents($file);

$content = str_replace(
    'onchange="filterHostEmployees(); filterMeetingRoomsBySite(this.value);"',
    'onchange="filterHostEmployeesBySite(this.value); filterMeetingRoomsBySite(this.value);"',
    $content
);

$content = str_replace(
    "filterHostEmployees();\n            filterMeetingRoomsBySite(data.site_id);",
    "filterHostEmployeesBySite(data.site_id);\n            filterMeetingRoomsBySite(data.site_id);",
    $content
);

file_put_contents($file, $content);
echo "View script fixed.\n";
