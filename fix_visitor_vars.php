<?php

$file = 'resources/views/partials/pre_registrations_table.blade.php';
$content = file_get_contents($file);

$content = str_replace(
    "'phone_code' => \$inv->phone_code ?? '971',",
    "'phone_code' => '971', // Extracting country code in JS if needed",
    $content
);

$content = str_replace(
    "'phone' => str_replace('+', '', \$inv->phone ?? ''),",
    "'phone' => str_replace('+', '', \$visitor->phone ?? ''),",
    $content
);

$content = str_replace(
    "'email' => \$inv->email ?? '',",
    "'email' => \$visitor->email ?? '',",
    $content
);

$content = str_replace(
    "'company_name' => \$inv->company ?? '',",
    "'company_name' => \$visitor->company_name ?? '',",
    $content
);

file_put_contents($file, $content);
echo "Fixed visitor variables in check-in button payload.\n";
