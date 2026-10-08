<?php

$file = 'app/Http/Controllers/CheckInController.php';
$content = file_get_contents($file);

// Add missing use statements
$useStatements = [
    'use App\Models\Department;',
    'use App\Models\MeetingRoom;'
];

foreach ($useStatements as $use) {
    if (strpos($content, $use) === false) {
        $content = preg_replace('/use App\\\\Models\\\\Employee;/', $use . "\nuse App\\Models\\Employee;", $content);
    }
}

// Modify create method to fetch departments and meeting rooms
$fetchLogic = <<<'PHP'
        $visitorTypes = VisitorType::where('status', 'active')->get();
        $departments = Department::where('status', 'active')->get();
        $meetingRooms = MeetingRoom::with('floor')->where('status', 'active')->get();
PHP;

$content = preg_replace(
    "/\\\$visitorTypes = VisitorType::where\('status', 'active'\)->get\(\);/s",
    $fetchLogic,
    $content
);

$compactOld = "'employees',
            'visitorTypes',
            'expectedToday'";
$compactNew = "'employees',
            'visitorTypes',
            'expectedToday',
            'departments',
            'meetingRooms'";
$content = str_replace($compactOld, $compactNew, $content);

file_put_contents($file, $content);
echo "CheckInController vars updated.\n";
