<?php
$file = 'resources/views/employees/index.blade.php';
$content = file_get_contents($file);

$oldModal = <<<'HTML'
    <!-- MODAL 6: SHIFT MODAL -->
    <div id="modalShift" class="modal-root">
        <div class="modal-backdrop" onclick="closeModal('modalShift')"></div>
        <div class="modal-panel">
            <div class="modal-head">
                <h3 id="shiftModalTitle">Add Shift</h3>
                <button type="button" class="icon-btn btn-ghost" onclick="closeModal('modalShift')">✕</button>
            </div>
            <div class="modal-body">
                <div class="field" style="margin-bottom:12px;">
                    <label>Shift Name *</label>
                    <input type="text" id="shiftName" placeholder="e.g. Rotating Shift">
                </div>
                <div class="form-grid">
                    <div class="field">
                        <label>Start Time</label>
                        <input type="time" id="shiftStartTime" value="08:00">
                    </div>
                    <div class="field">
                        <label>End Time</label>
                        <input type="time" id="shiftEndTime" value="17:00">
                    </div>
                </div>
            </div>
            <div class="modal-foot">
                <button type="button" class="btn" onclick="closeModal('modalShift')">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="alert('Shift saved.')">Save</button>
            </div>
        </div>
    </div>
HTML;

$timeOptions = '';
$start = strtotime('00:00');
$end = strtotime('23:45');
for ($i = $start; $i <= $end; $i += 15 * 60) {
    $timeOptions .= '<option value="' . date('H:i:s', $i) . '">' . date('h:i A', $i) . '</option>' . "\n";
}

$newModal = <<<HTML
    <!-- MODAL 6: SHIFT MODAL -->
    <div id="modalShift" class="modal-root">
        <div class="modal-backdrop" onclick="closeModal('modalShift')"></div>
        <div class="modal-panel" style="max-width:560px; padding:24px; border-radius:12px;">
            <div class="modal-head" style="margin-bottom:20px; padding-bottom:16px; border-bottom:1px solid #eaeaea;">
                <h3 id="shiftModalTitle" style="font-size:18px; font-weight:700;">Add Shift</h3>
                <button type="button" class="icon-btn btn-ghost" onclick="closeModal('modalShift')">
                    <svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:20px;height:20px;color:#666;"><path d="M18 6L6 18M6 6l12 12"/></svg>
                </button>
            </div>
            <div class="modal-body">
                <div class="field" style="margin-bottom:20px;">
                    <label style="font-size:13px; font-weight:600; color:#333; margin-bottom:6px; display:block;">Shift Name</label>
                    <input type="text" id="shiftName" class="form-control" placeholder="e.g. Night Shift" style="width:100%; padding:10px 12px; border:1px solid #dcdcdc; border-radius:8px; font-size:14px; color:#333;">
                </div>
                
                <div class="form-grid" style="display:grid; grid-template-columns:1fr 1fr; gap:16px; margin-bottom:24px;">
                    <div class="field">
                        <label style="font-size:13px; font-weight:600; color:#333; margin-bottom:6px; display:block;">Start Time</label>
                        <select id="shiftStartTime" class="form-select" style="width:100%; padding:10px 12px; border:1px solid #dcdcdc; border-radius:8px; font-size:14px; color:#333; appearance:none; background:url('data:image/svg+xml;utf8,<svg xmlns=\"http://www.w3.org/2000/svg\" width=\"16\" height=\"16\" viewBox=\"0 0 24 24\" fill=\"none\" stroke=\"%23666\" stroke-width=\"2\" stroke-linecap=\"round\" stroke-linejoin=\"round\"><polyline points=\"6 9 12 15 18 9\"></polyline></svg>') no-repeat right 12px center; background-color:#fff;">
                            $timeOptions
                        </select>
                    </div>
                    <div class="field">
                        <label style="font-size:13px; font-weight:600; color:#333; margin-bottom:6px; display:block;">End Time</label>
                        <select id="shiftEndTime" class="form-select" style="width:100%; padding:10px 12px; border:1px solid #dcdcdc; border-radius:8px; font-size:14px; color:#333; appearance:none; background:url('data:image/svg+xml;utf8,<svg xmlns=\"http://www.w3.org/2000/svg\" width=\"16\" height=\"16\" viewBox=\"0 0 24 24\" fill=\"none\" stroke=\"%23666\" stroke-width=\"2\" stroke-linecap=\"round\" stroke-linejoin=\"round\"><polyline points=\"6 9 12 15 18 9\"></polyline></svg>') no-repeat right 12px center; background-color:#fff;">
                            $timeOptions
                        </select>
                    </div>
                </div>

                <div style="margin-bottom:16px;">
                    <div style="font-size:13px; font-weight:700; color:#333;">Effective Date Range (optional)</div>
                    <div style="font-size:12px; color:#666; margin-bottom:12px;">Leave blank for an ongoing, recurring shift.</div>
                    
                    <div class="form-grid" style="display:grid; grid-template-columns:1fr 1fr; gap:16px;">
                        <div class="field">
                            <label style="font-size:13px; font-weight:600; color:#333; margin-bottom:6px; display:block;">From</label>
                            <input type="date" id="shiftStartDate" class="form-control" style="width:100%; padding:10px 12px; border:1px solid #dcdcdc; border-radius:8px; font-size:14px; color:#666; appearance:none; background:url('data:image/svg+xml;utf8,<svg xmlns=\"http://www.w3.org/2000/svg\" width=\"16\" height=\"16\" viewBox=\"0 0 24 24\" fill=\"none\" stroke=\"%23666\" stroke-width=\"2\" stroke-linecap=\"round\" stroke-linejoin=\"round\"><rect x=\"3\" y=\"4\" width=\"18\" height=\"18\" rx=\"2\" ry=\"2\"></rect><line x1=\"16\" y1=\"2\" x2=\"16\" y2=\"6\"></line><line x1=\"8\" y1=\"2\" x2=\"8\" y2=\"6\"></line><line x1=\"3\" y1=\"10\" x2=\"21\" y2=\"10\"></line></svg>') no-repeat right 12px center; background-color:#fff; color:#333;">
                        </div>
                        <div class="field">
                            <label style="font-size:13px; font-weight:600; color:#333; margin-bottom:6px; display:block;">To</label>
                            <input type="date" id="shiftEndDate" class="form-control" style="width:100%; padding:10px 12px; border:1px solid #dcdcdc; border-radius:8px; font-size:14px; color:#666; appearance:none; background:url('data:image/svg+xml;utf8,<svg xmlns=\"http://www.w3.org/2000/svg\" width=\"16\" height=\"16\" viewBox=\"0 0 24 24\" fill=\"none\" stroke=\"%23666\" stroke-width=\"2\" stroke-linecap=\"round\" stroke-linejoin=\"round\"><rect x=\"3\" y=\"4\" width=\"18\" height=\"18\" rx=\"2\" ry=\"2\"></rect><line x1=\"16\" y1=\"2\" x2=\"16\" y2=\"6\"></line><line x1=\"8\" y1=\"2\" x2=\"8\" y2=\"6\"></line><line x1=\"3\" y1=\"10\" x2=\"21\" y2=\"10\"></line></svg>') no-repeat right 12px center; background-color:#fff; color:#333;">
                        </div>
                    </div>
                </div>

            </div>
            <div class="modal-foot" style="margin-top:24px; padding-top:20px; border-top:1px solid #eaeaea; justify-content:flex-end; gap:12px;">
                <button type="button" class="btn" onclick="closeModal('modalShift')" style="padding:10px 16px; font-weight:600; border:1px solid #dcdcdc; border-radius:8px; background:#fff; color:#333;">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="alert('Shift saved.')" style="padding:10px 24px; font-weight:600; border-radius:8px; background:#82b3a9; color:#fff; border:none;">Save</button>
            </div>
        </div>
    </div>
HTML;

// Clean up newlines for matching
$normalizedContent = preg_replace('/\r\n/', "\n", $content);
$normalizedOldModal = preg_replace('/\r\n/', "\n", $oldModal);

// Note: The HTML icon "✕" in the actual file is likely a utf8 character or a weird encoding. 
// From the grep it showed "o " or something similar. 
// I will just use regex to replace between "<!-- MODAL 6: SHIFT MODAL -->" and "<!-- MODAL 7: ASSIGN EMPLOYEES TO SHIFT -->"

$startTag = '<!-- MODAL 6: SHIFT MODAL -->';
$endTag = '<!-- MODAL 7: ASSIGN EMPLOYEES TO SHIFT -->';
$startPos = strpos($normalizedContent, $startTag);
$endPos = strpos($normalizedContent, $endTag);

if ($startPos !== false && $endPos !== false) {
    $newContent = substr($normalizedContent, 0, $startPos) . $newModal . "\n\n    " . substr($normalizedContent, $endPos);
    file_put_contents($file, $newContent);
    echo "Shift Modal successfully updated.\n";
} else {
    echo "Could not find modal tags in file.\n";
}
