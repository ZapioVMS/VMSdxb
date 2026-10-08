<?php
// 1. Update routes/web.php
$webFile = 'routes/web.php';
$webContent = file_get_contents($webFile);
if (strpos($webContent, 'destroyShift') === false) {
    $webContent = str_replace(
        "Route::put('/lookups/shifts/{shift}', [LookupController::class, 'updateShift'])->name('lookups.shifts.update');",
        "Route::put('/lookups/shifts/{shift}', [LookupController::class, 'updateShift'])->name('lookups.shifts.update');\n    Route::delete('/lookups/shifts/{shift}', [LookupController::class, 'destroyShift'])->name('lookups.shifts.destroy');",
        $webContent
    );
    file_put_contents($webFile, $webContent);
}

// 2. Update LookupController.php
$controllerFile = 'app/Http/Controllers/LookupController.php';
$controllerContent = file_get_contents($controllerFile);

$updateFind = <<<'PHP'
    public function updateShift(Request $request, Shift $shift): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'start_time' => ['required', 'string'],
            'end_time' => ['required', 'string'],
            'grace_minutes' => ['nullable', 'integer', 'min:0'],
            'status' => ['required', 'string', 'in:active,inactive'],
        ]);

        $shift->update($validated);

        return redirect()->route('settings.index', ['tab' => 'shifts'])->with('success', 'Shift schedule updated successfully.');
    }
PHP;

$updateReplace = <<<'PHP'
    public function updateShift(Request $request, Shift $shift)
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
            $validated['status'] = $shift->status ?? 'active';
        }
        if (!isset($validated['grace_minutes'])) {
            $validated['grace_minutes'] = $shift->grace_minutes ?? 0;
        }

        $shift->update($validated);

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'shift' => $shift]);
        }

        return redirect()->route('settings.index', ['tab' => 'shifts'])->with('success', 'Shift schedule updated successfully.');
    }

    public function destroyShift(Request $request, Shift $shift)
    {
        $shift->delete();
        
        if ($request->wantsJson()) {
            return response()->json(['success' => true]);
        }
        return redirect()->back()->with('success', 'Shift deleted.');
    }
PHP;

if (strpos($controllerContent, 'destroyShift') === false) {
    $controllerContent = str_replace($updateFind, $updateReplace, $controllerContent);
    file_put_contents($controllerFile, $controllerContent);
}

// 3. Update index.blade.php
$viewFile = 'resources/views/employees/index.blade.php';
$viewContent = file_get_contents($viewFile);

// Add hidden id to modal
if (strpos($viewContent, 'id="shiftId"') === false) {
    $viewContent = str_replace(
        '<input type="text" id="shiftName"',
        '<input type="hidden" id="shiftId">'."\n".'                    <input type="text" id="shiftName"',
        $viewContent
    );
}

// Update openShiftModal JS
$openFind = <<<'JS'
        function openShiftModal(sh = null) {
            const title = document.getElementById('shiftModalTitle');
            if (sh) {
                title.innerText = 'Edit Shift';
                document.getElementById('shiftName').value = sh.name || '';
                document.getElementById('shiftStartTime').value = sh.start_time || '08:00';
                document.getElementById('shiftEndTime').value = sh.end_time || '17:00';
            } else {
                title.innerText = 'Add Shift';
                document.getElementById('shiftName').value = '';
                document.getElementById('shiftStartTime').value = '08:00';
                document.getElementById('shiftEndTime').value = '17:00';
            }
            openModal('modalShift');
        }
JS;

$openReplace = <<<'JS'
        function openShiftModal(sh = null) {
            const title = document.getElementById('shiftModalTitle');
            if (sh) {
                title.innerText = 'Edit Shift';
                document.getElementById('shiftId').value = sh.id;
                document.getElementById('shiftName').value = sh.name || '';
                
                // Format times for input
                let start = sh.start_time ? sh.start_time.substring(0,5) + ':00' : '08:00:00';
                let end = sh.end_time ? sh.end_time.substring(0,5) + ':00' : '17:00:00';
                
                document.getElementById('shiftStartTime').value = start;
                document.getElementById('shiftEndTime').value = end;
                document.getElementById('shiftStartDate').value = sh.effective_from ? sh.effective_from.split('T')[0] : '';
                document.getElementById('shiftEndDate').value = sh.effective_to ? sh.effective_to.split('T')[0] : '';
            } else {
                title.innerText = 'Add Shift';
                document.getElementById('shiftId').value = '';
                document.getElementById('shiftName').value = '';
                document.getElementById('shiftStartTime').value = '08:00:00';
                document.getElementById('shiftEndTime').value = '17:00:00';
                document.getElementById('shiftStartDate').value = '';
                document.getElementById('shiftEndDate').value = '';
            }
            openModal('modalShift');
        }
JS;

$viewContent = str_replace($openFind, $openReplace, $viewContent);

// Update saveShift JS
$saveFind = <<<'JS'
            fetch('{{ route("lookups.shifts.store") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify(payload)
            })
JS;

$saveReplace = <<<'JS'
            const shiftId = document.getElementById('shiftId').value;
            let url = '{{ route("lookups.shifts.store") }}';
            let method = 'POST';
            
            if (shiftId) {
                url = `/lookups/shifts/${shiftId}`;
                payload._method = 'PUT'; // Laravel method spoofing
            }

            fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify(payload)
            })
JS;

$viewContent = str_replace($saveFind, $saveReplace, $viewContent);

// Add deleteShift JS
$deleteJs = <<<'JS'
        function deleteShift(id) {
            if (!confirm('Are you sure you want to delete this shift?')) return;
            
            fetch(`/lookups/shifts/${id}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    _method: 'DELETE',
                    _token: '{{ csrf_token() }}'
                })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    window.location.reload();
                } else {
                    alert('Error deleting shift.');
                }
            })
            .catch(err => {
                console.error(err);
                alert('Server error.');
            });
        }
JS;

if (strpos($viewContent, 'function deleteShift') === false) {
    $viewContent = str_replace(
        "        function saveShift() {",
        $deleteJs . "\n\n        function saveShift() {",
        $viewContent
    );
}

// Update the Delete button in the HTML to call deleteShift
$viewContent = preg_replace(
    "/onclick=\"alert\('Delete shift'\)\"/",
    "onclick=\"deleteShift({{ \$sh->id }})\"",
    $viewContent
);

file_put_contents($viewFile, $viewContent);

echo "Update and Delete fixed.\n";
