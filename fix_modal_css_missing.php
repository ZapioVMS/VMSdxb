<?php
$indexFile = 'resources/views/pre_registrations/index.blade.php';
$content = file_get_contents($indexFile);

$start = strpos($content, '/* Modal styling with visible y-axis scrollbar */');
$end = strpos($content, '/* Switch row styling */');

$modalCss = substr($content, $start, $end - $start);

$modalFile = 'resources/views/partials/invite_modal.blade.php';
$modalContent = file_get_contents($modalFile);

$modalContent = str_replace('</style>', "\n" . $modalCss . "\n</style>", $modalContent);

file_put_contents($modalFile, $modalContent);
echo "Added modal CSS to invite_modal.blade.php\n";
