<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

$inv = App\Models\Invitation::first();
if (!$inv) {
    echo "No invitation found.\n";
    exit;
}

$request = Illuminate\Http\Request::create('/pre-registrations/' . $inv->id, 'PUT', [
    '_token' => csrf_token(),
    'name' => 'John Doe Changed',
    'email' => 'john.doe@example.com',
    'visit_date' => '2026-10-06',
    'expected_arrival' => '14:00',
    'duration' => '120',
    'purpose_id' => $inv->visit->purpose_id,
    'site_id' => $inv->visit->site_id,
    'host_employee_id' => $inv->visit->host_employee_id,
]);

$response = $kernel->handle($request);
echo "Status: " . $response->getStatusCode() . "\n";
$kernel->terminate($request, $response);
