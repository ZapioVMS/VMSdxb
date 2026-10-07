<?php

$file = 'resources/views/employees/index.blade.php';
$content = file_get_contents($file);

$targetJs = <<<'JS'
        let calCurrentDate = new Date();
        let calLeaves = [];
        let calStagedDates = new Set();
        let currentEmpId = null;

        function renderCalendar() {
            const year = calCurrentDate.getFullYear();
            const month = calCurrentDate.getMonth();
            const label = document.getElementById('calendarMonthLabel');
            label.innerText = calCurrentDate.toLocaleDateString('en-US', { month: 'long', year: 'numeric' });

            const firstDay = new Date(year, month, 1).getDay();
            const daysInMonth = new Date(year, month + 1, 0).getDate();
            const today = new Date();
            
            const grid = document.getElementById('calendarGrid');
            grid.innerHTML = '<div class="dp-dow">Su</div><div class="dp-dow">Mo</div><div class="dp-dow">Tu</div><div class="dp-dow">We</div><div class="dp-dow">Th</div><div class="dp-dow">Fr</div><div class="dp-dow">Sa</div>';

            // blanks
            for (let i = 0; i < firstDay; i++) {
                grid.innerHTML += '<div></div>';
            }

            for (let i = 1; i <= daysInMonth; i++) {
                const dateStr = `${year}-${String(month+1).padStart(2,'0')}-${String(i).padStart(2,'0')}`;
                
                let classes = ['dp-day'];
                if (today.getDate() === i && today.getMonth() === month && today.getFullYear() === year) {
                    classes.push('today');
                }
                
                if (calLeaves.includes(dateStr)) {
                    classes.push('on-leave');
                }
                
                if (calStagedDates.has(dateStr)) {
                    classes.push('staged');
                }

                grid.innerHTML += `<button type="button" class="${classes.join(' ')}" onclick="toggleCalDate('${dateStr}', this)">${i}</button>`;
            }
        }

        function changeMonth(delta) {
            calCurrentDate.setMonth(calCurrentDate.getMonth() + delta);
            renderCalendar();
        }

        function toggleCalDate(dateStr, el) {
            if (calStagedDates.has(dateStr)) {
                calStagedDates.delete(dateStr);
                el.classList.remove('staged');
            } else {
                calStagedDates.add(dateStr);
                el.classList.add('staged');
            }
        }

        function submitLeave(action) {
            if (calStagedDates.size === 0) {
                alert('Please select at least one date.');
                return;
            }
            document.getElementById('leaveDatesInput').value = Array.from(calStagedDates).join(',');
            const form = document.getElementById('leaveToggleForm');
            const actionInput = document.createElement('input');
            actionInput.type = 'hidden';
            actionInput.name = 'action';
            actionInput.value = action;
            form.appendChild(actionInput);
            form.submit();
        }

        function openEmployeeLeaveModal(emp) {
            currentEmpId = emp.id;
            const title = document.getElementById('leaveModalTitle');
            title.innerText = 'Mark Leave — ' + (emp.first_name + ' ' + (emp.last_name || '')).trim();
            document.getElementById('leaveToggleForm').action = '/employees/' + emp.id + '/leave';
            
            calLeaves = emp.leaves ? emp.leaves.map(l => l.date.split('T')[0]) : [];
            calStagedDates.clear();
            calCurrentDate = new Date();
            renderCalendar();
            
            openModal('modalEmployeeLeave');
        }
JS;

$searchStr = <<<'JS'
        function openEmployeeLeaveModal(emp) {
            const title = document.getElementById('leaveModalTitle');
            title.innerText = 'Mark Leave — ' + (emp.first_name + ' ' + (emp.last_name || '')).trim();
            document.getElementById('leaveToggleForm').action = '/employees/' + emp.id + '/leave';
            openModal('modalEmployeeLeave');
        }
JS;

if (strpos($content, $searchStr) !== false) {
    $content = str_replace($searchStr, $targetJs, $content);
    file_put_contents($file, $content);
    echo "JS updated.\n";
} else {
    echo "JS string not found. Please check manually.\n";
}
