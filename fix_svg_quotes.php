<?php
$file = 'resources/views/employees/index.blade.php';
$content = file_get_contents($file);

// We need to replace the double quotes inside the SVG data URI with single quotes so they don't break the style attribute.
// The SVG strings start with `background:url('data:image/svg+xml;utf8,<svg ` and end with `</svg>')`

$pattern = "/(background:url\('data:image\/svg\+xml;utf8,<svg)(.*?)(<\/svg>'\))/is";
$content = preg_replace_callback($pattern, function($matches) {
    // Replace double quotes with single quotes inside the SVG content
    $svgInner = str_replace('"', "'", $matches[2]);
    return $matches[1] . $svgInner . $matches[3];
}, $content);

file_put_contents($file, $content);
echo "Fixed SVG quotes in index.blade.php\n";
