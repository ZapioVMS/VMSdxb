<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$html = file_get_contents(__DIR__ . '/template/vms-admin.html.html');
preg_match('/const PHONE_DIAL_CODES = \{([^}]+)\}/s', $html, $matches);

if (!empty($matches[1])) {
    $listStr = $matches[1];
    // Match something like AE:'971'
    preg_match_all("/([A-Z]{2})\s*:\s*'(\d+)'/", $listStr, $phoneMatches, PREG_SET_ORDER);
    
    $updated = 0;
    foreach ($phoneMatches as $match) {
        $iso = $match[1];
        $isd = $match[2];
        
        $updated += \App\Models\Country::where('iso_code', $iso)->update(['isd_code' => $isd]);
    }
    
    echo "Updated " . $updated . " countries with ISD codes.\n";
} else {
    echo "Could not find PHONE_DIAL_CODES in HTML.\n";
}
