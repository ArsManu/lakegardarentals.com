<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Exceptions\PostTooLargeException;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'admin' => \App\Http\Middleware\EnsureUserIsAdmin::class,
            'noindex' => \App\Http\Middleware\AddNoIndexHeader::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (PostTooLargeException $e, Request $request) {
            if (! $request->is('admin/*') || $request->expectsJson()) {
                return null;
            }

            return redirect()
                ->back(fallback: route('admin.dashboard'))
                ->withErrors([
                    'images' => __('The total upload exceeds the PHP post_max_size limit. For local development use: composer run serve — or raise post_max_size and upload_max_filesize in php.ini (see php-dev.ini in the project root).'),
                ]);
        });
    })->create();
