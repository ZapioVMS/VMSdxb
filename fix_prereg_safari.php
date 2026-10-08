<?php
$file = 'resources/views/pre_registrations/index.blade.php';
$content = file_get_contents($file);

$jsFind = <<<'JS'
        function filterHostEmployeesBySite(siteId) {
            const hostSelect = document.getElementById('inv_host_employee_id');
            const options = hostSelect.querySelectorAll('option');
            options.forEach(opt => {
                if (!opt.value) return; // Keep "Select Host Employee"
                const empSite = opt.getAttribute('data-site');
                if (!siteId || siteId === 'all' || empSite === siteId) {
                    opt.style.display = '';
                } else {
                    opt.style.display = 'none';
                }
            });
        }
JS;

$jsReplace = <<<'JS'
        function filterHostEmployeesBySite(siteId) {
            const hostSelect = document.getElementById('inv_host_employee_id');
            const options = hostSelect.querySelectorAll('option');
            options.forEach(opt => {
                if (!opt.value) return; // Keep "Select Host Employee"
                const empSite = opt.getAttribute('data-site');
                if (!siteId || siteId === 'all' || empSite === siteId || !empSite) {
                    opt.style.display = '';
                    opt.hidden = false;
                    opt.disabled = false;
                } else {
                    opt.style.display = 'none';
                    opt.hidden = true;
                    opt.disabled = true;
                }
            });
            // Reset value if currently selected option is now hidden
            if (hostSelect.selectedOptions[0] && hostSelect.selectedOptions[0].hidden && hostSelect.selectedOptions[0].value) {
                hostSelect.value = '';
            }
        }
JS;

$content = str_replace($jsFind, $jsReplace, $content);
file_put_contents($file, $content);
echo "Robust filterHostEmployeesBySite applied.\n";
