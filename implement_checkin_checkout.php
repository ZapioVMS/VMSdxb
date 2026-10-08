<?php

// 1. Update routes/web.php
$webFile = 'routes/web.php';
$webContent = file_get_contents($webFile);
if (strpos($webContent, 'checkin') === false) {
    $webContent = str_replace(
        "Route::get('/employees/{employee}/attendance', [EmployeeController::class, 'attendance'])->name('employees.attendance');",
        "Route::get('/employees/{employee}/attendance', [EmployeeController::class, 'attendance'])->name('employees.attendance');\n    Route::post('/employees/{employee}/checkin', [EmployeeController::class, 'checkin'])->name('employees.checkin');\n    Route::post('/employees/{employee}/checkout', [EmployeeController::class, 'checkout'])->name('employees.checkout');",
        $webContent
    );
    file_put_contents($webFile, $webContent);
}

// 2. Update EmployeeController
$controllerFile = 'app/Http/Controllers/EmployeeController.php';
$controllerContent = file_get_contents($controllerFile);

// First update the index method to include attendanceRecords
$indexFind = <<<'PHP'
        $query = Employee::with(['site', 'department', 'shift', 'manager', 'leaves'])
            ->orderBy('first_name');
PHP;
$indexReplace = <<<'PHP'
        $query = Employee::with(['site', 'department', 'shift', 'manager', 'leaves', 'attendanceRecords' => function ($q) {
            $q->whereDate('attendance_date', today());
        }])->orderBy('first_name');
PHP;
$controllerContent = str_replace($indexFind, $indexReplace, $controllerContent);

// Add the checkin/checkout methods
$checkinCheckoutMethods = <<<'PHP'
    public function checkin(Request $request, Employee $employee)
    {
        $today = today();
        
        // Prevent double checkin
        $existing = $employee->attendanceRecords()->whereDate('attendance_date', $today)->first();
        if ($existing && !$existing->check_out_at) {
            return response()->json(['success' => false, 'message' => 'Already checked in']);
        }

        // Determine if late
        $status = 'present';
        if ($employee->shift && $employee->shift->start_time) {
            $shiftStart = \Carbon\Carbon::parse($employee->shift->start_time);
            $grace = $employee->shift->grace_minutes ?? 0;
            $allowedTime = $shiftStart->copy()->addMinutes($grace);
            
            // Note: Since shift start_time is a time only, we apply it to today
            $shiftStartToday = \Carbon\Carbon::parse($today->format('Y-m-d') . ' ' . $shiftStart->format('H:i:s'));
            $allowedTimeToday = $shiftStartToday->copy()->addMinutes($grace);

            if (now()->greaterThan($allowedTimeToday)) {
                $status = 'late';
            }
        }

        $record = $employee->attendanceRecords()->create([
            'organization_id' => $employee->organization_id,
            'site_id' => $employee->site_id ?? \App\Models\Site::first()->id ?? 1,
            'attendance_date' => $today,
            'check_in_at' => now(),
            'status' => $status,
            'source' => 'web'
        ]);

        return response()->json(['success' => true, 'record' => $record]);
    }

    public function checkout(Request $request, Employee $employee)
    {
        $today = today();
        $record = $employee->attendanceRecords()->whereDate('attendance_date', $today)->whereNull('check_out_at')->first();
        
        if (!$record) {
            return response()->json(['success' => false, 'message' => 'No active check-in found']);
        }

        $record->update([
            'check_out_at' => now()
        ]);

        return response()->json(['success' => true, 'record' => $record]);
    }
PHP;

if (strpos($controllerContent, 'function checkin(') === false) {
    // Inject right before the closing brace of the class
    $controllerContent = preg_replace('/}\s*$/', "\n$checkinCheckoutMethods\n}", $controllerContent);
    file_put_contents($controllerFile, $controllerContent);
}

// 3. Update index.blade.php for the Today's Attendance table
$viewFile = 'resources/views/employees/index.blade.php';
$viewContent = file_get_contents($viewFile);

$tableFind = <<<'HTML'
                            <tbody>
                                @foreach ($employees->take(8) as $emp)
                                    <tr>
                                        <td><div class="cell-name">{{ $emp->full_name }}</div></td>
                                        <td class="mono">{{ $emp->employee_code }}</td>
                                        <td>{{ $emp->department->name ?? '—' }}</td>
                                        <td class="mono">08:15 AM</td>
                                        <td class="mono">—</td>
                                        <td class="mono">3h 45m</td>
                                        <td>
                                            @if ($emp->status === 'on_leave')
                                                <span class="chip chip-leave">On Leave</span>
                                            @else
                                                <span class="chip chip-good">On Site</span>
                                            @endif
                                        </td>
                                        <td style="text-align:end;">
                                            <button type="button" class="btn btn-sm">Check out</button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
HTML;

$tableReplace = <<<'HTML'
                            <tbody>
                                @foreach ($employees as $emp)
                                    @php
                                        $att = $emp->attendanceRecords->first();
                                        $isOnLeave = $emp->status === 'on_leave';
                                        
                                        $checkInStr = '—';
                                        $checkOutStr = '—';
                                        $hoursStr = '—';
                                        $statusStr = '<span class="chip chip-muted">Absent</span>';
                                        
                                        if ($isOnLeave) {
                                            $statusStr = '<span class="chip chip-leave" style="background:#e8def8;color:#532397;">On Leave</span>';
                                        } elseif ($att) {
                                            $checkInStr = \Carbon\Carbon::parse($att->check_in_at)->format('h:i A');
                                            
                                            if ($att->check_out_at) {
                                                $checkOutStr = \Carbon\Carbon::parse($att->check_out_at)->format('h:i A');
                                                $diffMins = \Carbon\Carbon::parse($att->check_in_at)->diffInMinutes(\Carbon\Carbon::parse($att->check_out_at));
                                                $hrs = floor($diffMins / 60);
                                                $mins = $diffMins % 60;
                                                $hoursStr = "{$hrs}h {$mins}m";
                                                $statusStr = '<span class="chip" style="background:#f0f0f0;color:#666;">Complete</span>';
                                            } else {
                                                if ($att->status === 'late') {
                                                    $statusStr = '<span class="chip chip-warning" style="background:#fff4e5;color:#b26a00;">Late Check-in</span>';
                                                } else {
                                                    $statusStr = '<span class="chip chip-good" style="background:#e6f4ea;color:#137333;">On Site</span>';
                                                }
                                            }
                                        }
                                    @endphp
                                    <tr>
                                        <td><div class="cell-name">{{ $emp->full_name }}</div></td>
                                        <td class="mono">{{ $emp->employee_code }}</td>
                                        <td>{{ $emp->department->name ?? '—' }}</td>
                                        <td class="mono">{{ $checkInStr }}</td>
                                        <td class="mono">{{ $checkOutStr }}</td>
                                        <td class="mono">{{ $hoursStr }}</td>
                                        <td>{!! $statusStr !!}</td>
                                        <td style="text-align:end;">
                                            @if (!$isOnLeave)
                                                @if (!$att)
                                                    <button type="button" class="btn btn-primary btn-sm" onclick="doCheckIn({{ $emp->id }})" style="background:#1f4e5b; color:white;">
                                                        <svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:14px;height:14px;margin-right:4px;"><path d="M16 21v-2a4 4 0 0 0-4-4H5c-1.1 0-2 .9-2 2v2"/><circle cx="8.5" cy="7" r="4"/><polyline points="17 11 19 13 23 9"/></svg>
                                                        <span>In</span>
                                                    </button>
                                                @elseif (!$att->check_out_at)
                                                    <button type="button" class="btn btn-sm" onclick="doCheckOut({{ $emp->id }})" style="border:1px solid #dcdcdc;">
                                                        <svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:14px;height:14px;margin-right:4px;"><path d="M16 21v-2a4 4 0 0 0-4-4H5c-1.1 0-2 .9-2 2v2"/><circle cx="8.5" cy="7" r="4"/><line x1="23" y1="11" x2="17" y2="11"/></svg>
                                                        <span>Out</span>
                                                    </button>
                                                @endif
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
HTML;

$viewContent = str_replace($tableFind, $tableReplace, $viewContent);

// Add the JS logic for checking in/out
$jsFind = <<<'JS'
        function deleteShift(id) {
JS;
$jsReplace = <<<'JS'
        function doCheckIn(empId) {
            fetch(`/employees/${empId}/checkin`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
                body: JSON.stringify({ _token: '{{ csrf_token() }}' })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    window.location.reload();
                } else {
                    alert(data.message || 'Error checking in');
                }
            })
            .catch(err => {
                console.error(err);
                alert('Server error.');
            });
        }

        function doCheckOut(empId) {
            fetch(`/employees/${empId}/checkout`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
                body: JSON.stringify({ _token: '{{ csrf_token() }}' })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    window.location.reload();
                } else {
                    alert(data.message || 'Error checking out');
                }
            })
            .catch(err => {
                console.error(err);
                alert('Server error.');
            });
        }

        function deleteShift(id) {
JS;

if (strpos($viewContent, 'function doCheckIn(') === false) {
    $viewContent = str_replace($jsFind, $jsReplace, $viewContent);
}

file_put_contents($viewFile, $viewContent);
echo "Checkin/Checkout implemented.\n";
