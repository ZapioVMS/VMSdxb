<?php
$file = 'resources/views/settings/index.blade.php';
$content = file_get_contents($file);

// Generate time options
$options = [];
for ($h = 0; $h < 24; $h++) {
    for ($m = 0; $m < 60; $m += 15) {
        $meridiem = $h < 12 ? 'AM' : 'PM';
        $displayHour = $h == 0 ? 12 : ($h > 12 ? $h - 12 : $h);
        $timeStr = sprintf("%02d:%02d %s", $displayHour, $m, $meridiem);
        $options[] = '<option value="' . $timeStr . '">' . $timeStr . '</option>';
    }
}
$optionsHtml = "\n                            <option value=\"\">Select Time</option>\n                            " . implode("\n                            ", $options) . "\n                        ";

// Replace visiting_hours_from
$fromStart = strpos($content, '<select name="visiting_hours_from" id="branchVisitsFrom">');
$fromEnd = strpos($content, '</select>', $fromStart);
if ($fromStart !== false && $fromEnd !== false) {
    $before = substr($content, 0, $fromStart + strlen('<select name="visiting_hours_from" id="branchVisitsFrom">'));
    $after = substr($content, $fromEnd);
    $content = $before . $optionsHtml . $after;
}

// Replace visiting_hours_to
$toStart = strpos($content, '<select name="visiting_hours_to" id="branchVisitsTo">');
$toEnd = strpos($content, '</select>', $toStart);
if ($toStart !== false && $toEnd !== false) {
    $before = substr($content, 0, $toStart + strlen('<select name="visiting_hours_to" id="branchVisitsTo">'));
    $after = substr($content, $toEnd);
    $content = $before . $optionsHtml . $after;
}

file_put_contents($file, $content);
echo "Updated time dropdowns.\n";
