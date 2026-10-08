<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$countries = \App\Models\Country::orderBy('name')->get();

$code = "<?php\n\nnamespace Database\Seeders;\n\nuse App\Models\Country;\nuse Illuminate\Database\Seeder;\n\nclass CountrySeeder extends Seeder\n{\n    public function run(): void\n    {\n        \$countries = [\n";

foreach ($countries as $c) {
    $name = addslashes($c->name);
    $iso = addslashes($c->iso_code);
    $isd = $c->isd_code ? addslashes($c->isd_code) : '';
    
    $code .= "            ['name' => '{$name}', 'iso_code' => '{$iso}', 'isd_code' => '{$isd}'],\n";
}

$code .= "        ];\n\n        foreach (\$countries as \$c) {\n            Country::updateOrCreate(['iso_code' => \$c['iso_code']], \$c);\n        }\n    }\n}\n";

file_put_contents(__DIR__ . '/database/seeders/CountrySeeder.php', $code);
echo "CountrySeeder.php updated.\n";
