<?php
$file = 'resources/views/visitors/checkin.blade.php';
$content = file_get_contents($file);

// Find the first prefillCheckinForm
$startPos = strpos($content, 'function prefillCheckinForm(data) {');
if ($startPos !== false) {
    // We will just rewrite the bottom part of the file completely to be safe.
    
    // Find the end of the script before the first prefillCheckinForm
    $includeModalJsPos = strpos($content, '@include(\'partials.invite_modal_js\')');
    
    if ($includeModalJsPos !== false) {
        $before = substr($content, 0, $includeModalJsPos + strlen('@include(\'partials.invite_modal_js\')'));
        
        $newScript = <<<'HTML'

        function prefillCheckinForm(data) {
            if(data.first_name) document.getElementById('first_name').value = data.first_name;
            if(data.last_name) document.getElementById('last_name').value = data.last_name;
            if(data.id_type_id) document.getElementById('id_type_id').value = data.id_type_id;
            if(data.id_number) document.getElementById('id_number').value = data.id_number;
            if(data.nationality_id) document.getElementById('nationality_id').value = data.nationality_id;
            
            const phoneCodeSelect = document.querySelector('select[name="phone_code"]');
            if(data.phone_code && phoneCodeSelect) phoneCodeSelect.value = data.phone_code;
            
            const phoneInput = document.querySelector('input[name="phone"]') || document.getElementById('phone');
            if(data.phone && phoneInput) phoneInput.value = data.phone;
            
            if(data.email) document.getElementById('email').value = data.email;
            
            const companyInput = document.querySelector('input[name="company_name"]') || document.getElementById('company_name');
            if(data.company_name && companyInput) companyInput.value = data.company_name;
            
            if(data.site_id) {
                const siteEl = document.getElementById('site_id');
                if(siteEl) {
                    siteEl.value = data.site_id;
                    if(typeof filterEmployeesBySite === 'function') filterEmployeesBySite();
                }
            }
            if(data.host_employee_id) {
                setTimeout(() => {
                    const hostEl = document.getElementById('host_employee_id');
                    if(hostEl) hostEl.value = data.host_employee_id;
                }, 50);
            }
            if(data.purpose_id) {
                const purposeEl = document.getElementById('purpose_id');
                if(purposeEl) purposeEl.value = data.purpose_id;
            }
            
            const durationSelect = document.querySelector('select[name="duration"]');
            if(data.duration && durationSelect) durationSelect.value = data.duration;

            if(typeof takePhoto === 'function') takePhoto();
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }

        // Initialize auto-suggest logic immediately
        (function() {
            const searchInput = document.getElementById('visitor_search');
            const suggestionsBox = document.getElementById('visitor_suggestions');
            let debounceTimer;

            if (searchInput) {
                searchInput.addEventListener('input', function() {
                    clearTimeout(debounceTimer);
                    const query = this.value.trim();
                    
                    if (query.length < 2) {
                        suggestionsBox.style.display = 'none';
                        return;
                    }

                    debounceTimer = setTimeout(() => {
                        fetch(`/visitors/search?q=${encodeURIComponent(query)}`)
                            .then(response => response.json())
                            .then(data => {
                                suggestionsBox.innerHTML = '';
                                if (data.length === 0) {
                                    suggestionsBox.innerHTML = '<div style="padding: 10px 14px; color: var(--text-muted); font-size: 13px;">No matching visitors found.</div>';
                                } else {
                                    data.forEach(visitor => {
                                        const div = document.createElement('div');
                                        div.style.cssText = 'padding: 10px 14px; border-bottom: 1px solid var(--surface-2); cursor: pointer; transition: background 0.15s;';
                                        div.onmouseover = () => div.style.background = 'var(--surface-2)';
                                        div.onmouseout = () => div.style.background = 'transparent';
                                        
                                        const compText = visitor.company ? ` — ${visitor.company}` : '';
                                        div.innerHTML = `
                                            <div style="font-weight: 600; color: var(--text-primary); font-size: 13.5px;">${visitor.name}</div>
                                            <div style="font-size: 12px; color: var(--text-muted); margin-top: 2px;">${visitor.phone} ${visitor.email ? '• ' + visitor.email : ''}${compText}</div>
                                        `;
                                        
                                        div.addEventListener('click', function() {
                                            prefillCheckinForm(visitor.data);
                                            searchInput.value = visitor.name;
                                            suggestionsBox.style.display = 'none';
                                        });
                                        
                                        suggestionsBox.appendChild(div);
                                    });
                                }
                                suggestionsBox.style.display = 'block';
                            })
                            .catch(err => console.error('Search error:', err));
                    }, 300);
                });

                // Hide suggestions when clicking outside
                document.addEventListener('click', function(e) {
                    if (!searchInput.contains(e.target) && !suggestionsBox.contains(e.target)) {
                        suggestionsBox.style.display = 'none';
                    }
                });
            }
        })();
    </script>
</body>
</html>
HTML;
        
        $newContent = $before . "\n" . $newScript;
        file_put_contents($file, $newContent);
        echo "Cleaned up JS.\n";
    }
}
