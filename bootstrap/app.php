<?php


use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use App\Traits\ApiExceptionTrait;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        //
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->shouldRenderJsonWhen(function (Request $request, \Throwable $e) {
            // Force JSON for routes starting with 'api/'
            if ($request->is('api/*')) {
                return true;
            }
            // Fall back to default behavior
            return $request->expectsJson();
        });


    /*
    * This code registers a global exception renderer for your application.
    * It uses the `$exceptions->render()` method to intercept all exceptions
    * and pass them through a custom handler defined in `ApiExceptionTrait`.
    */
    $exceptions->render(function (\Throwable $e, Request $request) {
            $trait = new class {
                use ApiExceptionTrait;
            };
            return $trait->renderApi($request, $e);
        });
    })
    ->create();
