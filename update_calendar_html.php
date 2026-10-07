<?php

$file = 'resources/views/employees/index.blade.php';
$content = file_get_contents($file);

// Replace the calendar UI
$targetHtml = <<<'HTML'
    <!-- MODAL 3: MARK LEAVE CALENDAR MODAL -->
    <div id="modalEmployeeLeave" class="modal-root">
        <div class="modal-backdrop" onclick="closeModal('modalEmployeeLeave')"></div>
        <div class="modal-panel">
            <div class="modal-head">
                <h3 id="leaveModalTitle">Mark Leave — Employee</h3>
                <button type="button" class="icon-btn btn-ghost" onclick="closeModal('modalEmployeeLeave')">✕</button>
            </div>
            <div class="modal-body">
                <p class="section-sub" style="margin-top:0;">Click any days to select them, then mark or remove leave for all selected days at once.</p>
                <div class="dp-panel">
                    <div class="dp-head">
                        <button type="button" class="icon-btn btn-sm btn-ghost" onclick="changeMonth(-1)">‹</button>
                        <span class="dp-month-label" id="calendarMonthLabel">Month Year</span>
                        <button type="button" class="icon-btn btn-sm btn-ghost" onclick="changeMonth(1)">›</button>
                    </div>
                    <div class="dp-grid" id="calendarGrid">
                        <div class="dp-dow">Su</div><div class="dp-dow">Mo</div><div class="dp-dow">Tu</div><div class="dp-dow">We</div><div class="dp-dow">Th</div><div class="dp-dow">Fr</div><div class="dp-dow">Sa</div>
                        <!-- Days populated via JS -->
                    </div>
                </div>
                <div class="dp-legend">
                    <div class="dp-legend-item"><span class="dp-legend-swatch" style="background:var(--s7);"></span>Already on leave</div>
                    <div class="dp-legend-item"><span class="dp-legend-swatch" style="background:var(--accent);"></span>Selected</div>
                </div>
            </div>
            <div class="modal-foot">
                <button type="button" class="btn" onclick="closeModal('modalEmployeeLeave')">Close</button>
                <form id="leaveToggleForm" method="POST" action="" style="display:inline;">
                    @csrf
                    <input type="hidden" name="dates" id="leaveDatesInput">
                    <button type="button" class="btn" onclick="submitLeave('remove')">Remove Leave</button>
                    <button type="button" class="btn btn-primary" onclick="submitLeave('mark')">Mark as Leave</button>
                </form>
            </div>
        </div>
    </div>
HTML;

// Find the start and end of the original modal
$startStr = '    <!-- MODAL 3: MARK LEAVE CALENDAR MODAL -->';
$endStr = '    <!-- MODAL 4: EMPLOYEE DETAIL & ID CARD MODAL -->';

$startPos = strpos($content, $startStr);
$endPos = strpos($content, $endStr);

$before = substr($content, 0, $startPos);
$after = substr($content, $endPos);

$newContent = $before . $targetHtml . "\n\n" . $after;

file_put_contents($file, $newContent);
echo "Modal HTML replaced.\n";
