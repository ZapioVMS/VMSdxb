<?php
$file = 'resources/views/employees/index.blade.php';
$content = file_get_contents($file);

$jsToInject = <<<'JS'
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
    </script>
</body>
</html>
JS;

$content = str_replace("    </script>\n</body>\n</html>", $jsToInject, $content);

file_put_contents($file, $content);
echo "Injected saveShift()\n";
