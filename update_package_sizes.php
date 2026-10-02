<?php
// 1. Update Routes
$routesFile = 'routes/web.php';
$routesContent = file_get_contents($routesFile);
if (strpos($routesContent, "updatePackageSize") === false) {
    $routeAdd = "Route::post('/settings/package-sizes', [SettingsController::class, 'storePackageSize'])->name('settings.package_sizes.store');\n    Route::put('/settings/package-sizes/{id}', [SettingsController::class, 'updatePackageSize'])->name('settings.package_sizes.update');";
    $routesContent = str_replace("Route::post('/settings/package-sizes', [SettingsController::class, 'storePackageSize'])->name('settings.package_sizes.store');", $routeAdd, $routesContent);
    file_put_contents($routesFile, $routesContent);
}

// 2. Update Controller
$controllerFile = 'app/Http/Controllers/SettingsController.php';
$cContent = file_get_contents($controllerFile);
$storePackageSizeOld = <<<EOF
    public function storePackageSize(Request \$request): RedirectResponse
    {
        \$orgId = TenantScope::getTenantId() ?? 1;

        \$validated = \$request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:500'],
        ]);

        if (Schema::hasTable('package_sizes')) {
            PackageSize::create([
                'organization_id' => \$orgId,
                'name' => \$validated['name'],
                'description' => \$validated['description'] ?? null,
            ]);
        } else {
            \$setting = OrganizationSetting::firstOrNew(['organization_id' => \$orgId, 'key' => 'package_sizes']);
            \$sizes = \$setting->value ?? [];
            \$sizes[] = [
                'id' => 'size_' . time(),
                'name' => \$validated['name'],
                'description' => \$validated['description'] ?? '',
            ];
            \$setting->value = \$sizes;
            \$setting->save();
        }

        return redirect()->route('settings.index', ['tab' => 'packageSizes'])->with('success', 'Package Size added.');
    }
EOF;

$storePackageSizeNew = <<<EOF
    public function storePackageSize(Request \$request): RedirectResponse
    {
        \$orgId = TenantScope::getTenantId() ?? 1;

        \$validated = \$request->validate([
            'name' => ['required', 'string', 'max:255'],
        ]);

        if (Schema::hasTable('package_sizes')) {
            PackageSize::create([
                'organization_id' => \$orgId,
                'name' => \$validated['name'],
            ]);
        } else {
            \$setting = OrganizationSetting::firstOrNew(['organization_id' => \$orgId, 'key' => 'package_sizes']);
            \$sizes = \$setting->value ?? [];
            \$sizes[] = [
                'id' => 'size_' . time(),
                'name' => \$validated['name'],
                'packages' => 0,
            ];
            \$setting->value = \$sizes;
            \$setting->save();
        }

        return redirect()->route('settings.index', ['tab' => 'packageSizes'])->with('success', 'Package Size added.');
    }

    public function updatePackageSize(Request \$request, string \$id): RedirectResponse
    {
        \$orgId = TenantScope::getTenantId() ?? 1;

        \$validated = \$request->validate([
            'name' => ['required', 'string', 'max:255'],
        ]);

        if (Schema::hasTable('package_sizes')) {
            \$size = PackageSize::where('id', \$id)->first();
            if (\$size) {
                \$size->update(['name' => \$validated['name']]);
            }
        } else {
            \$setting = OrganizationSetting::where('organization_id', \$orgId)->where('key', 'package_sizes')->first();
            if (\$setting && is_array(\$setting->value)) {
                \$sizes = \$setting->value;
                foreach (\$sizes as &\$s) {
                    if ((string)(\$s['id'] ?? '') === (string)\$id) {
                        \$s['name'] = \$validated['name'];
                        break;
                    }
                }
                \$setting->value = \$sizes;
                \$setting->save();
            }
        }

        return redirect()->route('settings.index', ['tab' => 'packageSizes'])->with('success', 'Package Size updated.');
    }
EOF;
$cContent = str_replace($storePackageSizeOld, $storePackageSizeNew, $cContent);
file_put_contents($controllerFile, $cContent);

// 3. Update Blade View
$bladeFile = 'resources/views/settings/index.blade.php';
$bContent = file_get_contents($bladeFile);

// 3.1 Replace description text in card with packages logged
$bContent = preg_replace("/\{\{\s*\\\$s\[.description.\]\s*\?\?\s*''.*?\}\}/", "{{ \$s['packages'] ?? 0 }} packages logged", $bContent);

// 3.2 Add Edit Button
$oldForm = <<<EOF
<form method="POST" action="{{ route('settings.package_sizes.destroy', \$s['id']) }}" onsubmit="return confirm('Remove package size?');">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-ghost"><svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 6h18M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg></button>
                                    </form>
EOF;

$newForm = <<<EOF
<div style="display: flex; gap: 4px;">
                                        <button type="button" class="btn btn-sm btn-ghost" onclick="openEditPackageSizeModal('{{ \$s['id'] }}', '{{ addslashes(\$s['name']) }}')" title="Edit">
                                            <svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 20h9M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"/></svg>
                                        </button>
                                        <form method="POST" action="{{ route('settings.package_sizes.destroy', \$s['id']) }}" onsubmit="return confirm('Remove package size?');" style="margin:0;">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-ghost" title="Delete"><svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 6h18M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg></button>
                                        </form>
                                    </div>
EOF;
$bContent = str_replace($oldForm, $newForm, $bContent);

// 3.3 Remove Description field from modal
$descField = <<<EOF
<div class="field" style="margin-top: 14px;">
                    <label>Description</label>
                    <textarea name="description" rows="2" placeholder="e.g. For small letters and documents"></textarea>
                </div>
EOF;
$bContent = str_replace($descField, "", $bContent);

// 3.4 Add JS
$js = <<<EOF
function openEditPackageSizeModal(id, name) {
        const modal = document.getElementById('modalAddSize');
        const form = modal.querySelector('form');
        const title = modal.querySelector('.modal-head h3');
        const nameInput = form.querySelector('input[name="name"]');
        
        // Change title
        title.innerText = 'Edit Package Size';
        
        // Change form action to update
        form.action = '/settings/package-sizes/' + id;
        
        // Add or update _method input for PUT
        let methodInput = form.querySelector('input[name="_method"]');
        if (!methodInput) {
            methodInput = document.createElement('input');
            methodInput.type = 'hidden';
            methodInput.name = '_method';
            form.appendChild(methodInput);
        }
        methodInput.value = 'PUT';
        
        // Set values
        nameInput.value = name;
        
        modal.classList.add('open');
    }

    // Override openModal for modalAddSize to reset it to ADD mode
    const originalOpenModalForSize = openModal;
    openModal = function(id) {
        if (id === 'modalAddSize') {
            const modal = document.getElementById(id);
            const form = modal.querySelector('form');
            const title = modal.querySelector('.modal-head h3');
            title.innerText = 'Add Package Size';
            form.action = "{{ route('settings.package_sizes.store') }}";
            form.reset();
            const methodInput = form.querySelector('input[name="_method"]');
            if (methodInput) methodInput.remove();
        } else if (id === 'modalAddCarrier') {
            const modal = document.getElementById(id);
            const form = modal.querySelector('form');
            const title = modal.querySelector('.modal-head h3');
            title.innerText = 'Add Delivery Carrier';
            form.action = "{{ route('settings.carriers.store') }}";
            form.reset();
            const methodInput = form.querySelector('input[name="_method"]');
            if (methodInput) methodInput.remove();
        } else {
            // we have overridden it in the other snippet too, so be careful.
            // But this will just wrap again if needed. Wait, we are doing it in the same file.
            // Let's just do it directly.
        }
        originalOpenModalForSize(id); // Wait, this might cause double execution or infinite loop if we do it poorly. Let's just use originalOpenModal.
    };
EOF;

// Since we already overrode `openModal` in the previous step, we shouldn't wrap it again with `const originalOpenModal = openModal;` because it could cause issues.
// Instead, let's just REPLACE the previous override block in the file to include both.

$previousOverride = <<<EOF
const originalOpenModal = openModal;
    openModal = function(id) {
        if (id === 'modalAddCarrier') {
            const modal = document.getElementById(id);
            const form = modal.querySelector('form');
            const title = modal.querySelector('.modal-head h3');
            title.innerText = 'Add Delivery Carrier';
            form.action = "{{ route('settings.carriers.store') }}";
            form.reset();
            const methodInput = form.querySelector('input[name="_method"]');
            if (methodInput) methodInput.remove();
        }
        originalOpenModal(id);
    };
EOF;

$newOverride = <<<EOF
const originalOpenModal = openModal;
    openModal = function(id) {
        if (id === 'modalAddCarrier') {
            const modal = document.getElementById(id);
            const form = modal.querySelector('form');
            const title = modal.querySelector('.modal-head h3');
            title.innerText = 'Add Delivery Carrier';
            form.action = "{{ route('settings.carriers.store') }}";
            form.reset();
            const methodInput = form.querySelector('input[name="_method"]');
            if (methodInput) methodInput.remove();
        }
        if (id === 'modalAddSize') {
            const modal = document.getElementById(id);
            const form = modal.querySelector('form');
            const title = modal.querySelector('.modal-head h3');
            title.innerText = 'Add Package Size';
            form.action = "{{ route('settings.package_sizes.store') }}";
            form.reset();
            const methodInput = form.querySelector('input[name="_method"]');
            if (methodInput) methodInput.remove();
        }
        originalOpenModal(id);
    };

    function openEditPackageSizeModal(id, name) {
        const modal = document.getElementById('modalAddSize');
        const form = modal.querySelector('form');
        const title = modal.querySelector('.modal-head h3');
        const nameInput = form.querySelector('input[name="name"]');
        
        // Change title
        title.innerText = 'Edit Package Size';
        
        // Change form action to update
        form.action = '/settings/package-sizes/' + id;
        
        // Add or update _method input for PUT
        let methodInput = form.querySelector('input[name="_method"]');
        if (!methodInput) {
            methodInput = document.createElement('input');
            methodInput.type = 'hidden';
            methodInput.name = '_method';
            form.appendChild(methodInput);
        }
        methodInput.value = 'PUT';
        
        // Set values
        nameInput.value = name;
        
        modal.classList.add('open');
    }
EOF;

$bContent = str_replace($previousOverride, $newOverride, $bContent);

file_put_contents($bladeFile, $bContent);
echo "Changes applied successfully.\n";
?>
