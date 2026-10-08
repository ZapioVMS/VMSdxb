<?php

$file = 'resources/views/visitors/checkin.blade.php';
$content = file_get_contents($file);

// Replace the Edit button with an onclick handler
$oldEdit = <<<'HTML'
                                        <!-- Edit button redirects to pre-registrations page -->
                                        <a href="{{ route('pre_registrations.index') }}" class="btn btn-sm btn-ghost" title="Edit" style="padding: 4px 8px;">
                                            <svg class="ic" viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                                        </a>
HTML;

$newEdit = <<<'HTML'
                                        <!-- Edit button populates the checkin form -->
                                        <button type="button" class="btn btn-sm btn-ghost" title="Fill Check-In Form" style="padding: 4px 8px;" onclick="prefillCheckinForm({{ json_encode([
                                            'first_name' => $invite->visit->visitor->first_name ?? '',
                                            'last_name' => $invite->visit->visitor->last_name ?? '',
                                            'id_type_id' => $invite->visit->visitor->id_type_id ?? '',
                                            'id_number' => $invite->visit->visitor->id_number ?? '',
                                            'nationality_id' => $invite->visit->visitor->nationality_id ?? '',
                                            'phone_code' => $invite->phone_code ?? '971',
                                            'phone' => str_replace('+', '', $invite->phone ?? ''),
                                            'email' => $invite->email ?? '',
                                            'company_name' => $invite->company ?? '',
                                            'site_id' => $invite->visit->site_id ?? '',
                                            'host_employee_id' => $invite->visit->host_employee_id ?? '',
                                            'purpose_id' => $invite->visit->purpose_id ?? '',
                                            'duration' => $invite->duration ?? 60
                                        ]) }})">
                                            <svg class="ic" viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                                        </button>
HTML;

$content = str_replace($oldEdit, $newEdit, $content);

// Add the JS function
$jsFunction = <<<'HTML'
        function prefillCheckinForm(data) {
            if(data.first_name) document.getElementById('first_name').value = data.first_name;
            if(data.last_name) document.getElementById('last_name').value = data.last_name;
            if(data.id_type_id) document.getElementById('id_type_id').value = data.id_type_id;
            if(data.id_number) document.getElementById('id_number').value = data.id_number;
            if(data.nationality_id) document.getElementById('nationality_id').value = data.nationality_id;
            
            const phoneCodeSelect = document.querySelector('select[name="phone_code"]');
            if(data.phone_code && phoneCodeSelect) phoneCodeSelect.value = data.phone_code;
            
            const phoneInput = document.querySelector('input[name="phone"]');
            if(data.phone && phoneInput) phoneInput.value = data.phone;
            
            if(data.email) document.getElementById('email').value = data.email;
            
            const companyInput = document.querySelector('input[name="company_name"]');
            if(data.company_name && companyInput) companyInput.value = data.company_name;
            
            if(data.site_id) {
                document.getElementById('site_id').value = data.site_id;
                filterEmployeesBySite(); // Refilter employees based on site
            }
            if(data.host_employee_id) document.getElementById('host_employee_id').value = data.host_employee_id;
            if(data.purpose_id) document.getElementById('purpose_id').value = data.purpose_id;
            
            const durationSelect = document.querySelector('select[name="duration"]');
            if(data.duration && durationSelect) durationSelect.value = data.duration;

            // Scroll up to the form
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }
HTML;

$content = str_replace('function takePhoto() {', $jsFunction . "\n\n        function takePhoto() {", $content);

file_put_contents($file, $content);
echo "Checkin JS prefill updated.\n";
