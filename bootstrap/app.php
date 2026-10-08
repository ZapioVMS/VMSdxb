<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'permission' => \App\Http\Middleware\CheckPermission::class,
        ]);
    })
        ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*') || $request->expectsJson(),
        );

        $exceptions->render(function (\Illuminate\Database\QueryException $e, Request $request) {
            $errorCode = $e->errorInfo[1] ?? null;
            $sqlState = $e->getCode();
            
            if ($errorCode == 1062 || $sqlState == '23000' || $sqlState == '23505') {
                if ($request->expectsJson()) {
                    return response()->json(['message' => 'A duplicate record already exists.'], 422);
                }
                return back()->withInput()->with('error', 'A record with this name or code already exists. Please use a unique value.');
            }
        });
    })->create();
