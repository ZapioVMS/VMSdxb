<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

$inv = App\Models\Invitation::first();
if (!$inv) {
    echo "No invitation found.\n";
    exit;
}

echo "BEFORE:\n";
echo "Name: " . $inv->visit->visitor->first_name . "\n";
echo "Arrival: " . $inv->visit->scheduled_start_at . "\n";
echo "Duration: " . $inv->duration . "\n";

$request = Illuminate\Http\Request::create('/pre-registrations/' . $inv->id, 'PUT', [
    '_token' => csrf_token(),
    '_method' => 'PUT',
    'name' => 'John Edited',
    'email' => $inv->visit->visitor->email,
    'visit_date' => '2026-10-10',
    'expected_arrival' => '08:00',
    'duration' => '120',
    'purpose_id' => $inv->visit->purpose_id,
    'site_id' => $inv->visit->site_id,
    'host_employee_id' => $inv->visit->host_employee_id,
]);

$response = $kernel->handle($request);
echo "\nResponse Status: " . $response->getStatusCode() . "\n";

$inv->refresh();
$inv->visit->refresh();
$inv->visit->visitor->refresh();

echo "\nAFTER:\n";
echo "Name: " . $inv->visit->visitor->first_name . "\n";
echo "Arrival: " . $inv->visit->scheduled_start_at . "\n";
echo "Duration: " . $inv->duration . "\n";

if ($response->isRedirect()) {
    echo "Redirect Location: " . $response->headers->get('Location') . "\n";
}

$kernel->terminate($request, $response);
