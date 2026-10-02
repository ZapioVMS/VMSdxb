<?php
$file = "resources/views/settings/index.blade.php";
$c = file_get_contents($file);

$c = preg_replace(
    "/<div class=\"form-grid\" style=\"margin-top: 14px; row-gap: 4px; font-size: 12px;\">.*?<div class=\"cell-sub\">Dept Head<\/div>\s*<\/div>\s*<\/div>/s",
    "<div class=\"form-grid\" style=\"margin-top: 14px; grid-template-columns: 1fr 1fr; row-gap: 16px; font-size: 12px;\">\n                                    <div>\n                                        <div style=\"font-weight: 700; font-size: 15px;\">{{ \$dpt->employees_count ?? 0 }}</div>\n                                        <div class=\"cell-sub\">Members</div>\n                                    </div>\n                                    <div>\n                                        <div style=\"font-weight: 700; font-size: 15px;\">{{ \$dpt->visits_count ?? 0 }}</div>\n                                        <div class=\"cell-sub\">Visits/Week</div>\n                                    </div>\n                                    <div style=\"grid-column: 1 / -1;\">\n                                        <div style=\"font-weight: 700; font-size: 15px;\">Floor {{ \$dpt->floor_id ?? \$loop->iteration }}</div>\n                                        <div class=\"cell-sub\">Floor</div>\n                                    </div>\n                                </div>",
    $c
);

file_put_contents($file, $c);
echo "done";
?>
