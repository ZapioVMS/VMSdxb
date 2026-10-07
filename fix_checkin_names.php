<?php
$file = 'resources/views/visitors/checkin.blade.php';
$content = file_get_contents($file);

// 1. Replace First/Last name with Fullname robustly using regex
$pattern = '/<div class="field">\s*<label for="first_name">First Name \*<\/label>\s*<input type="text" id="first_name" name="first_name" value="\{\{ old\(\'first_name\'\) \}\}" required autofocus oninput="checkWatchlistName\(this\.value\)['"]?>\s*<\/div>\s*<div class="field">\s*<label for="last_name">Last Name<\/label>\s*<input type="text" id="last_name" name="last_name" value="\{\{ old\(\'last_name\'\) \}\}">\s*<\/div>/is';

$replacement = <<<'HTML'
                                  <div class="field" style="grid-column: span 2;">
                                      <label for="full_name">Fullname *</label>
                                      <input type="text" id="full_name" name="full_name" value="{{ old('full_name') }}" required autofocus oninput="checkWatchlistName(this.value)">
                                  </div>
HTML;

$content = preg_replace($pattern, $replacement, $content);

// 2. Add Select2 CDN and initialize for Nationality
if (strpos($content, 'select2.min.css') === false) {
    $headTarget = '</style>';
    $headReplacement = "</style>\n    <link href=\"https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css\" rel=\"stylesheet\" />\n    <style>\n        .select2-container--default .select2-selection--single {\n            height: 42px;\n            border: 1px solid var(--border);\n            border-radius: var(--radius-sm);\n            background: var(--surface);\n            font-family: 'IBM Plex Sans', sans-serif;\n        }\n        .select2-container--default .select2-selection--single .select2-selection__rendered {\n            line-height: 42px;\n            padding-left: 14px;\n            color: var(--text-primary);\n        }\n        .select2-container--default .select2-selection--single .select2-selection__arrow {\n            height: 40px;\n            right: 8px;\n        }\n    </style>";
    $content = str_replace($headTarget, $headReplacement, $content);
}

if (strpos($content, 'select2.min.js') === false) {
    $bodyTarget = '</body>';
    $bodyReplacement = "    <script src=\"https://code.jquery.com/jquery-3.6.0.min.js\"></script>\n    <script src=\"https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js\"></script>\n    <script>\n        $(document).ready(function() {\n            $('#nationality_id').select2({\n                placeholder: 'Search country...',\n                allowClear: true\n            });\n        });\n    </script>\n</body>";
    $content = str_replace($bodyTarget, $bodyReplacement, $content);
}

file_put_contents($file, $content);
echo "Robustly updated names and added Select2 styling.\n";
