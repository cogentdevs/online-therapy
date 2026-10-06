<?php

use App\Http\Middleware\AdminAccessMiddleware;
use App\Http\Middleware\EnsureFrontendAccountUser;
use App\Http\Middleware\EnsureFrontendUserIsActive;
use App\Http\Middleware\ForceEnglishContentLanguage;
use App\Http\Middleware\SuperAdminMiddleware;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Spatie\Permission\Middleware\PermissionMiddleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->web(append: [EnsureFrontendUserIsActive::class]);
        $middleware->validateCsrfTokens(except: ['webhooks/brevo/newsletter']);
        $middleware->alias([
            'admin-access' => AdminAccessMiddleware::class,
            'front-account' => EnsureFrontendAccountUser::class,
            'fixed-content-language' => ForceEnglishContentLanguage::class,
            'permission' => PermissionMiddleware::class,
            'super-admin' => SuperAdminMiddleware::class,
        ]);

        $middleware->redirectGuestsTo(
            fn (Request $request): string => $request->is('admin/*')
                ? route('admin.login')
                : route('front.login'),
        );
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*') || $request->expectsJson(),
        );
    })->create();
