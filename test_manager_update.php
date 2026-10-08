<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$emp = App\Models\Employee::first();
$emp2 = App\Models\Employee::where('id', '!=', $emp->id)->first();

echo "Before update, manager_id = " . ($emp->manager_id ?? 'NULL') . "\n";
$emp->update(['manager_id' => $emp2->id]);
$emp->refresh();
echo "After update, manager_id = " . ($emp->manager_id ?? 'NULL') . "\n";
