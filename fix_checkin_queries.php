<?php

$file = 'app/Http/Controllers/CheckInController.php';
$content = file_get_contents($file);

// Replace the departments and meeting rooms lines
$oldLogic = <<<'PHP'
        $departments = Department::where('status', 'active')->get();
        $meetingRooms = MeetingRoom::with('floor')->where('status', 'active')->get();
PHP;

$newLogic = <<<'PHP'
        $departments = Department::all();
        $meetingRooms = \Illuminate\Support\Facades\Schema::hasTable('meeting_rooms')
            ? MeetingRoom::with(['site', 'floor'])->orderBy('name')->get()
            : collect([]);
PHP;

$content = str_replace($oldLogic, $newLogic, $content);

file_put_contents($file, $content);
echo "CheckInController queries fixed.\n";
