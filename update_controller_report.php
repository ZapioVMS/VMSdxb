<?php
$file = 'app/Http/Controllers/EmployeeController.php';
$content = file_get_contents($file);

$insertLogic = <<<'PHP'
        $reportRecords = collect();
        $reportKpis = [
            'present' => 0,
            'absent' => 0,
            'leave' => 0,
            'hours' => '0h 0m'
        ];
        $selectedReportEmployee = null;

        if ($tab === 'report') {
            $reportEmployeeId = $request->get('report_employee_id', 'all');
            $reportDateRange = $request->get('report_date_range', 'this_year');

            $dateFrom = today()->startOfYear();
            $dateTo = today()->endOfDay();

            if ($reportDateRange === 'today') {
                $dateFrom = today();
            } elseif ($reportDateRange === 'this_week') {
                $dateFrom = today()->startOfWeek();
            } elseif ($reportDateRange === 'this_month') {
                $dateFrom = today()->startOfMonth();
            }

            $attQuery = \App\Models\AttendanceRecord::with('employee.department', 'employee.site')
                ->whereBetween('attendance_date', [$dateFrom->format('Y-m-d'), $dateTo->format('Y-m-d')]);

            if ($reportEmployeeId !== 'all') {
                $attQuery->where('employee_id', $reportEmployeeId);
                $selectedReportEmployee = $allEmployees->firstWhere('id', $reportEmployeeId);
            }

            $reportRecords = $attQuery->orderBy('attendance_date', 'desc')->get();

            // Calculate KPIs
            $presentDays = $reportRecords->pluck('attendance_date')->unique()->count();
            
            // Very simple absent logic: working days minus present. (5 days a week)
            $workingDays = $dateFrom->diffInDaysFiltered(function (\Carbon\Carbon $date) {
                return $date->isWeekday();
            }, min($dateTo, today()));
            
            $absentDays = max(0, $workingDays - $presentDays);

            // Calculate Total Hours
            $totalMinutes = 0;
            foreach ($reportRecords as $rec) {
                if ($rec->check_in_at && $rec->check_out_at) {
                    $totalMinutes += \Carbon\Carbon::parse($rec->check_in_at)->diffInMinutes(\Carbon\Carbon::parse($rec->check_out_at));
                }
            }
            $hrs = floor($totalMinutes / 60);
            $mins = $totalMinutes % 60;
            $hoursStr = "{$hrs}h {$mins}m";

            $reportKpis = [
                'present' => $presentDays,
                'absent' => $absentDays,
                'leave' => 0,
                'hours' => $hoursStr
            ];
        }

        return view('employees.index', compact('employees', 'allEmployees', 'sites', 'departments', 'shifts', 'managers', 'tab', 'counts', 'reportRecords', 'reportKpis', 'selectedReportEmployee', 'reportDateRange', 'reportEmployeeId'));
PHP;

$content = str_replace("return view('employees.index', compact('employees', 'allEmployees', 'sites', 'departments', 'shifts', \n'managers', 'tab', 'counts'));", $insertLogic, $content);
$content = str_replace("return view('employees.index', compact('employees', 'allEmployees', 'sites', 'departments', 'shifts', 'managers', 'tab', 'counts'));", $insertLogic, $content);

file_put_contents($file, $content);
echo "Controller updated.\n";
