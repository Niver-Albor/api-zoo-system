<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use App\Http\Middleware\CheckPermission;
use Illuminate\Database\Eloquent\ModelNotFoundException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        api: __DIR__.'/../routes/api.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'permission' => CheckPermission::class,
        ]);
        $middleware->statefulApi();
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (NotFoundHttpException $e, $request) {
            if ($request->is('api/*'))
                {
                    return response()->json([
                        'success' => false,
                        'message' => 'El recurso solicitado no existe.',
                        'data' => null
                    ], Response::HTTP_BAD_REQUEST);
                }
        });
        $exceptions->render(function (DomainException $e, $request) {
            if ($request->is('api/*'))
                {
                    return response()->json([
                        'success' => false,
                        'message' => $e->getMessage(),
                        'data' => null
                    ], Response::HTTP_UNPROCESSABLE_ENTITY);
                }
        });
        $exceptions->render(function (ModelNotFoundException $e, $request) {
            if ($request->is('api/*'))
                {
                    return response()->json([
                        'success' => false,
                        'message' => 'El recurso solicitado no existe.',
                        'data' => null
                    ], Response::HTTP_BAD_REQUEST);
                }
        });
    })
    ->withCommands([
        __DIR__.'/../app/Console/Commands',
    ])->create();
