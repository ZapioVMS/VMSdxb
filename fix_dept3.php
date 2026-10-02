<?php
$file = "resources/views/settings/index.blade.php";
$c = file_get_contents($file);

$old = <<<EOF
<div class="form-grid" style="margin-top: 14px; row-gap: 4px; font-size: 12px;">
                                    <div>
                                        <div style="font-weight: 700; font-size: 15px;">{{ \$dpt->employees_count }}</div>
                                        <div class="cell-sub">Members</div>
                                    </div>
                                    <div>
                                        <div style="font-weight: 700; font-size: 15px;">{{ \$dpt->code }}</div>
                                        <div class="cell-sub">Code</div>
                                    </div>
                                    <div>
                                        <div style="font-weight: 700; font-size: 15px;">{{ \$dpt->headEmployee ? \$dpt->headEmployee->first_name : '-' }}</div>
                                        <div class="cell-sub">Dept Head</div>
                                    </div>
                                </div>
EOF;

$new = <<<EOF
<div class="form-grid" style="margin-top: 14px; grid-template-columns: 1fr 1fr; row-gap: 16px; font-size: 12px;">
                                    <div>
                                        <div style="font-weight: 700; font-size: 15px;">{{ \$dpt->employees_count ?? 0 }}</div>
                                        <div class="cell-sub">Members</div>
                                    </div>
                                    <div>
                                        <div style="font-weight: 700; font-size: 15px;">0</div>
                                        <div class="cell-sub">Visits/Week</div>
                                    </div>
                                    <div style="grid-column: 1 / -1;">
                                        <div style="font-weight: 700; font-size: 15px;">Floor {{ \$dpt->floor_id ?? \$loop->iteration }}</div>
                                        <div class="cell-sub">Floor</div>
                                    </div>
                                </div>
EOF;

$c = str_replace($old, $new, $c);
file_put_contents($file, $c);
echo "done";
?>
