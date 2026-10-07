<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$emps = App\Models\Employee::all();
foreach ($emps as $e) {
    echo $e->first_name . " (site: " . $e->site_id . ", status: " . $e->status . ")\n";
}
