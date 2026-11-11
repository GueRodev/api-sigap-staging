<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Activa la sesión por cookies (Sanctum SPA) en las rutas /api.
        // Sin esto, /api/user no reconoce al usuario logueado aunque
        // el navegador envíe la cookie de sesión.
        $middleware->statefulApi();

        // En staging/producción Traefik recibe el HTTPS y reenvía la
        // petición a este contenedor por HTTP interno, agregando las
        // cabeceras X-Forwarded-* (IP real del usuario y protocolo).
        // Confiar en el proxy permite que Laravel sepa que la conexión
        // original era HTTPS (cookies Secure, URLs https) y registre
        // la IP real (rate limit del login, auditoría).
        //
        // '*' = confiar en cualquier proxy. Es seguro SOLO porque este
        // contenedor no publica puertos: únicamente Traefik le llega
        // por la red interna de Docker. Si algún día se expone un
        // puerto, cualquiera podría falsificar su IP con estas
        // cabeceras. Pendiente: evaluar acotarlo a la subred de
        // Docker en el servidor del CI.
        $middleware->trustProxies(at: '*');
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*') || $request->expectsJson(),
        );
    })->create();
