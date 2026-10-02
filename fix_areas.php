<?php
$file = "resources/views/settings/index.blade.php";
$c = file_get_contents($file);

// 1. Replace HTML
$oldHtml = <<<EOF
<input type="hidden" name="areas" id="floorAreasHidden">
                    <div class="tags-input-container" onclick="document.getElementById('floorTagInput').focus()">
                        <div id="floorTagsList" style="display: contents;"></div>
                        <input type="text" id="floorTagInput" placeholder="Type area or select meeting room, press Enter..." autocomplete="off">
                    </div>
                    <div id="floorRoomSuggestions" class="suggestions-dropdown" style="display: none;"></div>
                    <div class="cell-sub" style="margin-top: 4px;">Comma-separated list, e.g. Reception, Meeting Room A, Open Office</div>
EOF;

$newHtml = <<<EOF
<input type="text" name="areas" id="floorAreasInput" placeholder="e.g. Reception, Meeting Room A, Open Office">
                    <div class="cell-sub" style="margin-top: 4px;">Comma-separated list</div>
EOF;

$c = str_replace($oldHtml, $newHtml, $c);

// 2. Fix JS editFloor()
$oldEditJs = <<<EOF
// Populate existing areas as tags
        currentFloorTags = [];
        if (fl.areas && Array.isArray(fl.areas)) {
            currentFloorTags = fl.areas.map(a => a.name).filter(Boolean);
        } else if (fl.areas_text) {
            currentFloorTags = fl.areas_text.split(',').map(s => s.trim()).filter(Boolean);
        }
        renderFloorTags();
EOF;

$newEditJs = <<<EOF
// Populate existing areas text
        let areasStr = '';
        if (fl.areas && Array.isArray(fl.areas)) {
            areasStr = fl.areas.map(a => a.name).filter(Boolean).join(', ');
        } else if (fl.areas_text) {
            areasStr = fl.areas_text;
        }
        if (document.getElementById('floorAreasInput')) {
            document.getElementById('floorAreasInput').value = areasStr;
        }
EOF;

$c = str_replace($oldEditJs, $newEditJs, $c);

// 3. Fix JS reset in openAddFloorModal
$oldReset = <<<EOF
currentFloorTags = [];
        renderFloorTags();
EOF;
$newReset = <<<EOF
if (document.getElementById('floorAreasInput')) document.getElementById('floorAreasInput').value = '';
EOF;
$c = str_replace($oldReset, $newReset, $c);

file_put_contents($file, $c);
echo "done";
?>
