<?php
$file = "resources/views/settings/index.blade.php";
$c = file_get_contents($file);

$c = preg_replace(
    "/<input type=\"hidden\" name=\"areas\" id=\"floorAreasHidden\">.*?<div class=\"cell-sub\" style=\"margin-top: 4px;\">Comma-separated list.*?<\/div>/s",
    "<input type=\"text\" name=\"areas\" id=\"floorAreasInput\" placeholder=\"e.g. Reception, Meeting Room A, Open Office\">\n                    <div class=\"cell-sub\" style=\"margin-top: 4px;\">Comma-separated list</div>",
    $c
);

file_put_contents($file, $c);
echo "done";
?>
