<?php

$file = 'app/Http/Controllers/PreRegistrationController.php';
$content = file_get_contents($file);

$logCode = <<<'PHP'
    public function update(Request $request, Invitation $invitation): RedirectResponse
    {
        \Log::info('Update Request Data:', $request->all());
PHP;

$content = str_replace('public function update(Request $request, Invitation $invitation): RedirectResponse
    {', $logCode, $content);

file_put_contents($file, $content);
echo "Added logging to PreRegistrationController update method.\n";
