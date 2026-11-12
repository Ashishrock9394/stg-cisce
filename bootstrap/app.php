<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Http\Middleware\NoCacheHeaders;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        
        $middleware->alias([
           'client' => \Laravel\Passport\Http\Middleware\CheckToken::class,
            'auth.auth_student' => \App\Http\Middleware\AuthenticateStudent::class,
            'trainerschool' => \App\Http\Middleware\TrainerSchoolMiddleware::class,
            'role' => \App\Http\Middleware\RoleMiddleware::class,
            'check.questions' => \App\Http\Middleware\CheckSecurityQuestions::class,
            'otp.session' => \App\Http\Middleware\EnsureOtpSession::class,
            'restrict.access' => \App\Http\Middleware\TimeBasedRouteAccess::class,
            'module_access' => \App\Http\Middleware\CheckModuleAccess::class,
        ]);

        $middleware->append(NoCacheHeaders::class);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*') || $request->expectsJson(),
        );
    })->create();
