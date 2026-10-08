<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$html = file_get_contents(__DIR__ . '/template/vms-admin.html.html');
preg_match('/const COUNTRY_LIST = \[\s*(.*?)\s*\];/s', $html, $matches);

if (!empty($matches[1])) {
    $listStr = $matches[1];
    preg_match_all("/\['([^']+)'\s*,\s*'([^']+)'\]/", $listStr, $countryMatches, PREG_SET_ORDER);
    
    // Disable foreign key checks temporarily if needed, but safer to just updateOrCreate
    foreach ($countryMatches as $match) {
        $name = $match[1];
        $iso = $match[2];
        
        \App\Models\Country::updateOrCreate(
            ['iso_code' => $iso],
            [
                'name' => $name,
                'isd_code' => '', // Default empty if not available
            ]
        );
    }
    
    echo "Inserted " . count($countryMatches) . " countries successfully.\n";
} else {
    echo "Could not find COUNTRY_LIST in HTML.\n";
}
