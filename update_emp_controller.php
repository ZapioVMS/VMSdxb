<?php

$file = 'app/Http/Controllers/EmployeeController.php';
$content = file_get_contents($file);

$newMethod = <<<'PHP'
    /**
     * Get attendance history for an employee based on a filter.
     */
    public function attendance(Request $request, Employee $employee)
    {
        $filter = $request->get('filter', 'this_week');
        $startDate = null;
        $endDate = null;

        $now = now();
        if ($filter === 'this_week') {
            $startDate = $now->copy()->startOfWeek();
            $endDate = $now->copy()->endOfWeek();
        } elseif ($filter === 'this_month') {
            $startDate = $now->copy()->startOfMonth();
            $endDate = $now->copy()->endOfMonth();
        } elseif ($filter === 'this_year') {
            $startDate = $now->copy()->startOfYear();
            $endDate = $now->copy()->endOfYear();
        } elseif ($filter === 'custom') {
            $startDate = $request->get('start_date') ? \Carbon\Carbon::parse($request->get('start_date'))->startOfDay() : $now->copy()->startOfWeek();
            $endDate = $request->get('end_date') ? \Carbon\Carbon::parse($request->get('end_date'))->endOfDay() : $now->copy()->endOfWeek();
        }

        // Fetch attendance records
        $attendances = \App\Models\AttendanceRecord::where('employee_id', $employee->id)
            ->whereBetween('attendance_date', [$startDate->toDateString(), $endDate->toDateString()])
            ->orderBy('attendance_date', 'desc')
            ->get()
            ->keyBy(function($item) {
                return $item->attendance_date->format('Y-m-d');
            });

        // Fetch leave records
        $leaves = \App\Models\EmployeeLeave::where('employee_id', $employee->id)
            ->whereBetween('date', [$startDate->toDateString(), $endDate->toDateString()])
            ->get()
            ->keyBy(function($item) {
                return $item->date->format('Y-m-d');
            });

        // We want to return a list of dates from $endDate down to $startDate
        $results = [];
        $currentDate = $endDate->copy();
        
        // Prevent massive loops if range is too large (cap at 366 days)
        $maxDays = 366;
        $daysCount = 0;

        while ($currentDate->gte($startDate) && $daysCount < $maxDays) {
            // We only show days up to today, future days are not attendance yet (unless they are leaves)
            // Wait, maybe we show future leaves?
            // Actually, we should probably only generate dates up to min($endDate, today()) 
            // OR if it's a leave, we show it. For simplicity, just output up to min(endDate, today) unless it's a leave.
            $dateString = $currentDate->format('Y-m-d');
            $isFuture = $currentDate->isFuture();

            $att = $attendances->get($dateString);
            $leave = $leaves->get($dateString);

            if (!$isFuture || $leave) {
                $statusHtml = '—';
                $checkIn = '—';
                $checkOut = '—';
                $hours = '—';

                if ($leave) {
                    $statusHtml = '<span class="chip" style="background:#e9e0f3; color:#5b3c88; border-color:#d4c4e4;">
                                     <span style="display:inline-block;width:6px;height:6px;border-radius:50%;background:#5b3c88;margin-right:4px;"></span>On Leave
                                   </span>';
                } elseif ($att) {
                    $checkIn = $att->check_in_at ? $att->check_in_at->format('h:i A') : '—';
                    $checkOut = $att->check_out_at ? $att->check_out_at->format('h:i A') : '—';
                    if ($att->check_in_at && $att->check_out_at) {
                        $diff = $att->check_in_at->diff($att->check_out_at);
                        $hours = $diff->h . 'h ' . $diff->i . 'm';
                    }
                    if ($att->status === 'present') {
                        $statusHtml = '<span class="chip chip-good">Present</span>';
                    } else {
                        $statusHtml = '<span class="chip chip-muted">' . ucfirst($att->status) . '</span>';
                    }
                } else {
                    if ($currentDate->isWeekday()) {
                        // Absent if past weekday
                        $statusHtml = '<span class="chip chip-danger">Absent</span>';
                    } else {
                        $statusHtml = '<span class="chip chip-muted">Weekend</span>';
                    }
                }

                $results[] = [
                    'date_formatted' => $currentDate->format('d M Y'),
                    'check_in' => $checkIn,
                    'check_out' => $checkOut,
                    'hours' => $hours,
                    'status_html' => $statusHtml
                ];
            }
            
            $currentDate->subDay();
            $daysCount++;
        }

        return response()->json($results);
    }
PHP;

$insertPos = strpos($content, 'public function destroy(Employee $employee)');
if ($insertPos !== false) {
    $content = substr_replace($content, $newMethod . "\n\n    ", $insertPos, 0);
    file_put_contents($file, $content);
    echo "Added attendance method to EmployeeController.\n";
} else {
    echo "Could not find destroy method.\n";
}
