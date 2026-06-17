<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'verified' => \App\Http\Middleware\EnsureEmailIsVerified::class,
            'admin' => \App\Http\Middleware\AdminMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*'),
        );

        $exceptions->render(function (NotFoundHttpException $e, Request $request) {
            if (! $request->is('api/*')) {
                return null;
            }

            $previous = $e->getPrevious();

            if ($previous instanceof ModelNotFoundException) {
                $model = class_basename($previous->getModel());

                $messages = [
                    'Article' => 'Article is not available.',
                    'Meme' => 'Meme is not available.',
                    'Poll' => 'Poll is not available.',
                    'PollVote' => 'Poll vote is not available.',
                    'PollOption' => 'Poll option is not available.',
                    'Source' => 'Source is not available.',
                    'Tag' => 'Tag is not available.',
                ];

                return response()->json([
                    'message' => $messages[$model] ?? 'Resource is not available.',
                ], 404);
            }

            return response()->json([
                'message' => 'Endpoint is not available.',
            ], 404);
        });

        $exceptions->render(function (NotFoundHttpException $e, Request $request) {
            if ($request->is('api/*')) {
                return response()->json([
                    'message' => 'Endpoint is not available.',
                ], 404);
            }

            return null;
        });
    })
    ->create();
