<?php
$file = 'resources/views/visitors/checkin.blade.php';
$content = file_get_contents($file);

$expectedHtml = <<<'HTML'

            <div class="card" style="margin-top: 32px; padding: 24px; padding-bottom: 0;">
                <div style="margin-bottom: 16px;">
                    <h2 style="font-size: 16.5px; font-weight: 700; margin: 0 0 4px 0;">Pre-Registered Visitors Expected Today</h2>
                    <div style="font-size: 13px; color: var(--text-muted);">{{ $expectedToday->count() }} pre-registered visitor{{ $expectedToday->count() !== 1 ? 's' : '' }} expected today.</div>
                </div>

                @if($expectedToday->count() > 0)
                    <div style="margin: 0 -24px;">
                        <style>
                            .tablewrap { border: none; border-radius: 0; }
                            .dtable { min-width: 100%; border-top: 1px solid var(--border); }
                            .dtable thead th { border-bottom: 1px solid var(--border); background: var(--surface-2); font-size: 10.5px; padding: 12px 24px; }
                            .dtable tbody td { padding: 14px 24px; border-bottom: 1px solid var(--border); }
                            .dtable tbody tr:last-child td { border-bottom: none; }
                        </style>
                        @include('partials.pre_registrations_table', ['invitations' => $expectedToday, 'showCheckInButton' => true])
                    </div>
                @else
                    <div style="padding: 32px; text-align: center; color: var(--text-muted); font-size: 14px; margin-bottom: 24px; border: 1px dashed var(--border); border-radius: var(--radius-sm);">
                        No pre-registered visitors are expected today.
                    </div>
                @endif
            </div>

HTML;

$content = str_replace('</main>', $expectedHtml . "\n        </main>", $content);

$modalHtml = <<<'HTML'

    @include('partials.invite_modal')
    <script>
        @include('partials.invite_modal_js')
        
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
                filterEmployeesBySite();
            }
            if(data.host_employee_id) {
                setTimeout(() => {
                    document.getElementById('host_employee_id').value = data.host_employee_id;
                }, 50);
            }
            if(data.purpose_id) document.getElementById('purpose_id').value = data.purpose_id;
            
            const durationSelect = document.querySelector('select[name="duration"]');
            if(data.duration && durationSelect) durationSelect.value = data.duration;

            takePhoto();
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }
    </script>
HTML;

$content = str_replace('</body>', $modalHtml . "\n</body>", $content);

file_put_contents($file, $content);
echo "Restored exact layout and injected Expected Today section.\n";

