<?php
$webFile = 'routes/web.php';
$webContent = file_get_contents($webFile);

if (strpos($webContent, 'employees/{employee}/checkin') === false) {
    $webContent = str_replace(
        "Route::get('/employees/{employee}/attendance', [EmployeeController::class, 'attendance'])->name('employees.attendance');",
        "Route::get('/employees/{employee}/attendance', [EmployeeController::class, 'attendance'])->name('employees.attendance');\n    Route::post('/employees/{employee}/checkin', [EmployeeController::class, 'checkin'])->name('employees.checkin');\n    Route::post('/employees/{employee}/checkout', [EmployeeController::class, 'checkout'])->name('employees.checkout');",
        $webContent
    );
    file_put_contents($webFile, $webContent);
    echo "Routes added.\n";
} else {
    echo "Routes already exist.\n";
}
