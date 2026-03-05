<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->web(append: [
            \Modules\Core\Http\Middleware\CenterContextMiddleware::class,
        ]);
        
        $middleware->api(append: [
            \Modules\Core\Http\Middleware\CenterContextMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->render(function (\Illuminate\Session\TokenMismatchException $e, \Illuminate\Http\Request $request) {
            return redirect()->back()
                ->withInput($request->except('password', '_token'))
                ->withErrors(['error' => 'Phiên đăng nhập đã hết hạn. Vui lòng thử lại.']);
        });
    })->create();
