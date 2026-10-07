<?php
$file = 'bootstrap/app.php';
$content = file_get_contents($file);

$newExceptionBlock = <<<'PHP'
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*') || $request->expectsJson(),
        );

        $exceptions->render(function (\Illuminate\Database\QueryException $e, Request $request) {
            if ($e->errorInfo[1] == 1062 || $e->getCode() == 23000) {
                if ($request->expectsJson()) {
                    return response()->json(['message' => 'A duplicate record already exists.'], 422);
                }
                return back()->withInput()->with('error', 'A record with this name or code already exists. Please use a unique value.');
            }
        });
    })->create();
PHP;

// Find the withExceptions block
$startPos = strpos($content, '->withExceptions(function (Exceptions $exceptions): void {');
$endPos = strpos($content, '})->create();', $startPos);

if ($startPos !== false && $endPos !== false) {
    $before = substr($content, 0, $startPos);
    $after = substr($content, $endPos + strlen('})->create();'));
    
    $newContent = $before . $newExceptionBlock . $after;
    file_put_contents($file, $newContent);
    echo "Updated app.php with QueryException handler.\n";
} else {
    echo "Could not find exception block.\n";
}
