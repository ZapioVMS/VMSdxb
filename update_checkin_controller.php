<?php

$file = 'app/Http/Controllers/CheckInController.php';
$content = file_get_contents($file);

// Add missing use statements if needed
$useStatements = [
    'use App\Models\Invitation;',
    'use Carbon\Carbon;'
];

foreach ($useStatements as $use) {
    if (strpos($content, $use) === false) {
        $content = preg_replace('/use App\\\\Models\\\\Badge;/', $use . "\nuse App\\Models\\Badge;", $content);
    }
}

// Modify create method to fetch expected today
$fetchLogic = <<<'PHP'
        $visitorTypes = VisitorType::where('status', 'active')->get();

        $expectedToday = Invitation::with([
            'visit.visitor',
            'visit.hostEmployee',
            'visit.purpose'
        ])->whereHas('visit', function ($q) use ($orgId) {
            $q->where('organization_id', $orgId)
              ->whereDate('scheduled_start_at', Carbon::today());
        })->where('status', '!=', 'cancelled')->get();

        return view('visitors.checkin', compact(
            'sites',
            'idTypes',
            'countries',
            'purposes',
            'employees',
            'visitorTypes',
            'expectedToday'
        ));
PHP;

$content = preg_replace(
    "/\\\$visitorTypes = VisitorType::where\('status', 'active'\)->get\(\);\s*return view\('visitors\.checkin', compact\(\s*'sites',\s*'idTypes',\s*'countries',\s*'purposes',\s*'employees',\s*'visitorTypes'\s*\)\);/s",
    $fetchLogic,
    $content
);

file_put_contents($file, $content);
echo "CheckInController updated.\n";
