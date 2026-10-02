<?php
$file = "resources/views/settings/index.blade.php";
$c = file_get_contents($file);

$badHTML = <<<EOF
<div class="field" style="margin-top: 14px;">
                    <label>Description</label>
                    <input type="text" name="description" placeholder="e.g. Up to 30x20x15 cm (fits in small locker)">
                </div>
EOF;

$c = str_replace($badHTML, "", $c);
file_put_contents($file, $c);
echo "done";
?>
