<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$inv = App\Models\Invitation::first();
if (!$inv) {
    echo "No invitation found.\n";
    exit;
}

$oldStart = $inv->visit->scheduled_start_at;

$newStart = \Carbon\Carbon::parse($oldStart)->addHour();

$inv->visit->update(['scheduled_start_at' => $newStart]);

$inv->refresh();
echo "Old start: " . $oldStart . "\n";
echo "New start: " . $inv->visit->scheduled_start_at . "\n";
