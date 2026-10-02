<?php
$file = "resources/views/settings/index.blade.php";
$c = file_get_contents($file);

// 1. Remove Code text
$c = preg_replace("/Code:\s*\{\{\s*\\\$c\[.code.\]\s*\?\?\s*.{2}\s*\}\}.*?\{\{\s*\\\$c\[.deliveries.\]/s", "{{ \$c['deliveries']", $c);

// 2. Add Edit Button
$oldForm = <<<EOF
<form method="POST" action="{{ route('settings.carriers.destroy', \$c['id']) }}" onsubmit="return confirm('Remove carrier?');">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-ghost"><svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 6h18M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg></button>
                                    </form>
EOF;

$newForm = <<<EOF
<div style="display: flex; gap: 4px;">
                                        <button type="button" class="btn btn-sm btn-ghost" onclick="openEditCarrierModal('{{ \$c['id'] }}', '{{ addslashes(\$c['name']) }}')" title="Edit">
                                            <svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 20h9M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"/></svg>
                                        </button>
                                        <form method="POST" action="{{ route('settings.carriers.destroy', \$c['id']) }}" onsubmit="return confirm('Remove carrier?');" style="margin:0;">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-ghost" title="Delete"><svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 6h18M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg></button>
                                        </form>
                                    </div>
EOF;
$c = str_replace($oldForm, $newForm, $c);

// 3. Remove Code Input from Modal
$codeField = <<<EOF
<div class="field" style="margin-top: 14px;">
                    <label>Tracking Code Prefix *</label>
                    <input type="text" name="code" required placeholder="e.g. UPS">
                </div>
EOF;
$c = str_replace($codeField, "", $c);

// 4. Add openEditCarrierModal JS
$js = <<<EOF
function openEditCarrierModal(id, name) {
        const modal = document.getElementById('modalAddCarrier');
        const form = modal.querySelector('form');
        const title = modal.querySelector('.modal-head h3');
        const nameInput = form.querySelector('input[name="name"]');
        
        // Change title
        title.innerText = 'Edit Delivery Carrier';
        
        // Change form action to update
        form.action = '/settings/delivery-carriers/' + id;
        
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

    // Override openModal for modalAddCarrier to reset it to ADD mode
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

// Insert JS before </script>
$c = str_replace("</script>", $js . "\n</script>", $c);

file_put_contents($file, $c);
echo "Changes applied successfully.\n";
