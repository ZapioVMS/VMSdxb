<?php
$file = 'app/Http/Controllers/CheckInController.php';
$content = file_get_contents($file);

$targetValidate = <<<'PHP'
        $validated = $request->validate([
            'first_name' => ['required', 'string', 'max:255', 'min:2'],
            'last_name' => ['nullable', 'string', 'max:255'],
PHP;

$replaceValidate = <<<'PHP'
        $validated = $request->validate([
            'full_name' => ['required', 'string', 'max:255', 'min:2'],
PHP;

$content = str_replace($targetValidate, $replaceValidate, $content);

$targetCreateVisitor = <<<'PHP'
            if (!$visitor) {
                $visitor = Visitor::create([
                    'organization_id' => $orgId,
                    'first_name' => $validated['first_name'],
                    'last_name' => $validated['last_name'] ?? null,
PHP;

$replaceCreateVisitor = <<<'PHP'
            $nameParts = explode(' ', trim($validated['full_name']), 2);
            $firstName = $nameParts[0];
            $lastName = $nameParts[1] ?? null;

            if (!$visitor) {
                $visitor = Visitor::create([
                    'organization_id' => $orgId,
                    'first_name' => $firstName,
                    'last_name' => $lastName,
PHP;

$content = str_replace($targetCreateVisitor, $replaceCreateVisitor, $content);

$targetUpdateVisitor = <<<'PHP'
            } else {
                $visitor->update([
                    'first_name' => $validated['first_name'],
                    'last_name' => $validated['last_name'] ?? $visitor->last_name,
PHP;

$replaceUpdateVisitor = <<<'PHP'
            } else {
                $visitor->update([
                    'first_name' => $firstName,
                    'last_name' => $lastName ?? $visitor->last_name,
PHP;

$content = str_replace($targetUpdateVisitor, $replaceUpdateVisitor, $content);

file_put_contents($file, $content);
echo "Updated CheckInController to handle full_name.\n";
