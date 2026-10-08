<?php
// Fix timezone
$appConfig = 'config/app.php';
$content = file_get_contents($appConfig);
$content = preg_replace("/'timezone' => 'UTC'/", "'timezone' => env('APP_TIMEZONE', 'Asia/Dubai')", $content);
file_put_contents($appConfig, $content);

// Fix tab active state
$viewFile = 'resources/views/employees/index.blade.php';
$viewContent = file_get_contents($viewFile);
$jsFind = <<<'JS'
            document.querySelectorAll('.subtabs .subtab').forEach(btn => {
                btn.classList.toggle('active', btn.textContent.toLowerCase().includes(tabKey) || (tabKey === 'directory' && btn.textContent.includes('Directory')));
            });
JS;
$jsReplace = <<<'JS'
            document.querySelectorAll('.subtabs .subtab').forEach(btn => {
                const isMatch = btn.getAttribute('onclick').includes(`switchTab('${tabKey}')`);
                btn.classList.toggle('active', isMatch);
            });
JS;
$viewContent = str_replace($jsFind, $jsReplace, $viewContent);

// Add "All Time" to report date range dropdown
$dropdownFind = <<<'HTML'
                        <option value="today" {{ ($reportDateRange ?? '') === 'today' ? 'selected' : '' }}>Today</option>
HTML;
$dropdownReplace = <<<'HTML'
                        <option value="all_time" {{ ($reportDateRange ?? '') === 'all_time' ? 'selected' : '' }}>All Time</option>
                        <option value="today" {{ ($reportDateRange ?? '') === 'today' ? 'selected' : '' }}>Today</option>
HTML;
$viewContent = str_replace($dropdownFind, $dropdownReplace, $viewContent);
file_put_contents($viewFile, $viewContent);

// Update EmployeeController for 'all_time' default
$controllerFile = 'app/Http/Controllers/EmployeeController.php';
$controllerContent = file_get_contents($controllerFile);

$controllerFind = <<<'PHP'
        $reportDateRange = 'this_year';
PHP;
$controllerReplace = <<<'PHP'
        $reportDateRange = 'all_time';
PHP;
$controllerContent = str_replace($controllerFind, $controllerReplace, $controllerContent);

$controllerFind2 = <<<'PHP'
            $reportDateRange = $request->get('report_date_range', 'this_year');

            $dateFrom = today()->startOfYear();
PHP;
$controllerReplace2 = <<<'PHP'
            $reportDateRange = $request->get('report_date_range', 'all_time');

            $dateFrom = today()->startOfYear();
            if ($reportDateRange === 'all_time') {
                $dateFrom = \Carbon\Carbon::parse('1970-01-01');
            }
PHP;
$controllerContent = str_replace($controllerFind2, $controllerReplace2, $controllerContent);
file_put_contents($controllerFile, $controllerContent);

echo "Fixes applied.\n";
