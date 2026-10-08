<?php
$file = 'resources/views/visitors/checkin.blade.php';
$content = file_get_contents($file);

// The JS block starts with "function prefillCheckinForm(data)" and ends right before "</body>"
$startString = '        function prefillCheckinForm(data) {';
$endString = "</body>";

$startPos = strrpos($content, $startString);

if ($startPos !== false) {
    $before = substr($content, 0, $startPos);
    $jsContent = substr($content, $startPos, strrpos($content, $endString) - $startPos);
    $after = substr($content, strrpos($content, $endString));
    
    $newContent = $before . "<script>\n" . $jsContent . "</script>\n" . $after;
    file_put_contents($file, $newContent);
    echo "Wrapped JS in script tags successfully.\n";
} else {
    echo "Could not find start string.\n";
}
