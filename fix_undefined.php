<?php
$file = 'app/Http/Controllers/EmployeeController.php';
$content = file_get_contents($file);

$find = <<<'PHP'
        $reportRecords = collect();
        $reportKpis = [
            'present' => 0,
            'absent' => 0,
            'leave' => 0,
            'hours' => '0h 0m'
        ];
        $selectedReportEmployee = null;

        if ($tab === 'report') {
PHP;

$replace = <<<'PHP'
        $reportRecords = collect();
        $reportKpis = [
            'present' => 0,
            'absent' => 0,
            'leave' => 0,
            'hours' => '0h 0m'
        ];
        $selectedReportEmployee = null;
        $reportEmployeeId = 'all';
        $reportDateRange = 'this_year';

        if ($tab === 'report') {
PHP;

$content = str_replace($find, $replace, $content);
file_put_contents($file, $content);
echo "Fixed undefined variables.\n";
