<?php

use App\Http\Middleware\HandleInertiaRequests;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Middleware\AddLinkHeadersForPreloadedAssets;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->web(append: [
            HandleInertiaRequests::class,
            AddLinkHeadersForPreloadedAssets::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*') || $request->expectsJson(),
        );

        // SQLSTATE 23000 = integrity constraint violation (e.g. deleting a record that is still referenced)
        $exceptions->render(function (QueryException $e, Request $request) {
            if ($request->is('api/*') && (string) $e->getCode() === '23000') {
                return response()->json([
                    'message' => 'La operacion viola una restriccion de integridad: el registro esta referenciado por otros registros o duplica uno existente.',
                ], 409);
            }
        });
    })->create();
