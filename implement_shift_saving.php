<?php

// 1. Update Migration
$migrationFile = 'database/migrations/2026_10_08_110030_add_effective_dates_to_shifts_table.php';
$migrationContent = file_get_contents($migrationFile);
$migrationContent = str_replace(
    'public function up(): void
    {
        Schema::table(\'shifts\', function (Blueprint $table) {
            //
        });
    }',
    'public function up(): void
    {
        Schema::table(\'shifts\', function (Blueprint $table) {
            $table->date(\'effective_from\')->nullable()->after(\'end_time\');
            $table->date(\'effective_to\')->nullable()->after(\'effective_from\');
        });
    }',
    $migrationContent
);
$migrationContent = str_replace(
    'public function down(): void
    {
        Schema::table(\'shifts\', function (Blueprint $table) {
            //
        });
    }',
    'public function down(): void
    {
        Schema::table(\'shifts\', function (Blueprint $table) {
            $table->dropColumn([\'effective_from\', \'effective_to\']);
        });
    }',
    $migrationContent
);
file_put_contents($migrationFile, $migrationContent);

// 2. Update Model
$modelFile = 'app/Models/Shift.php';
$modelContent = file_get_contents($modelFile);
$modelContent = str_replace(
    "'end_time',",
    "'end_time',\n        'effective_from',\n        'effective_to',",
    $modelContent
);
$modelContent = str_replace(
    "'start_time' => 'datetime:H:i',",
    "'start_time' => 'datetime:H:i',\n        'effective_from' => 'date',\n        'effective_to' => 'date',",
    $modelContent
);
file_put_contents($modelFile, $modelContent);

// 3. Update LookupController (to handle AJAX requests)
$controllerFile = 'app/Http/Controllers/LookupController.php';
$controllerContent = file_get_contents($controllerFile);

// Update storeShift validation and response
$storeShiftFind = <<<'PHP'
    public function storeShift(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'start_time' => ['required', 'string'],
            'end_time' => ['required', 'string'],
            'grace_minutes' => ['nullable', 'integer', 'min:0'],
            'status' => ['required', 'string', 'in:active,inactive'],
        ]);

        Shift::create(array_merge($validated, ['organization_id' => TenantScope::getTenantId()]));

        return redirect()->route('settings.index', ['tab' => 'shifts'])->with('success', 'Shift schedule created successfully.');
    }
PHP;

$storeShiftReplace = <<<'PHP'
    public function storeShift(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'start_time' => ['required', 'string'],
            'end_time' => ['required', 'string'],
            'effective_from' => ['nullable', 'date'],
            'effective_to' => ['nullable', 'date'],
            'grace_minutes' => ['nullable', 'integer', 'min:0'],
            'status' => ['nullable', 'string', 'in:active,inactive'],
        ]);

        if (!isset($validated['status'])) {
            $validated['status'] = 'active';
        }
        if (!isset($validated['grace_minutes'])) {
            $validated['grace_minutes'] = 0;
        }

        $shift = Shift::create(array_merge($validated, ['organization_id' => TenantScope::getTenantId()]));

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'shift' => $shift]);
        }

        return redirect()->route('settings.index', ['tab' => 'shifts'])->with('success', 'Shift schedule created successfully.');
    }
PHP;

$controllerContent = str_replace($storeShiftFind, $storeShiftReplace, $controllerContent);
file_put_contents($controllerFile, $controllerContent);

// 4. Update index.blade.php JS and Button
$viewFile = 'resources/views/employees/index.blade.php';
$viewContent = file_get_contents($viewFile);

// Fix button styles and onclick
$viewContent = str_replace(
    'onclick="alert(\'Shift saved.\')" style="padding:10px 24px; font-weight:600; border-radius:8px; background:#82b3a9; color:#fff; border:none;"',
    'onclick="saveShift()" class="btn btn-primary"',
    $viewContent
);

// Add the JS function for saveShift()
$jsFind = <<<'JS'
        function closeModal(id) {
            document.getElementById(id).style.display = 'none';
        }
JS;

$jsReplace = <<<'JS'
        function closeModal(id) {
            document.getElementById(id).style.display = 'none';
        }

        function saveShift() {
            const name = document.getElementById('shiftName').value;
            const startTime = document.getElementById('shiftStartTime').value;
            const endTime = document.getElementById('shiftEndTime').value;
            const startDate = document.getElementById('shiftStartDate').value;
            const endDate = document.getElementById('shiftEndDate').value;

            if (!name) {
                alert('Shift Name is required.');
                return;
            }

            const payload = {
                name: name,
                start_time: startTime,
                end_time: endTime,
                effective_from: startDate || null,
                effective_to: endDate || null,
                _token: '{{ csrf_token() }}'
            };

            fetch('{{ route("lookups.shifts.store") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify(payload)
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    closeModal('modalShift');
                    window.location.reload(); // Reload to show new shift
                } else {
                    alert('Error saving shift.');
                }
            })
            .catch(err => {
                console.error(err);
                alert('Server error.');
            });
        }
JS;

$viewContent = str_replace($jsFind, $jsReplace, $viewContent);
file_put_contents($viewFile, $viewContent);

echo "Backend submission implementation complete.\n";
