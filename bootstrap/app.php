<?php

use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Configation des routes de l'application.
 */
return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
        apiPrefix: 'battleship-ia',
    )
    ->withMiddleware(function (Middleware $middleware) {
    })
    /**
     * Gestion des erreurs.
     */
    ->withExceptions(function (Exceptions $exceptions) {

        /**
         * Erreur 404. Ressource inéxistante.
         */
        $exceptions->render(function (NotFoundHttpException $e, Request $request) {
            if ($request->is('battleship-ia/*')) {
                return response()->json([
                    'message' => 'La ressource n’existe pas.'
                ], 404);
            }
        });

        /**
         * Erreur 401. Non authentifié.
         */
        $exceptions->render(function (AuthenticationException $e, Request $request) {
            if ($request->is('battleship-ia/*')) {
                return response()->json([
                    'message' => 'Non authentifié.'
                ], 401);
            }
        });

        /**
         * Erreur 403. Accèss non authorisé.
         */
        $exceptions->render(function (AccessDeniedHttpException $e, Request $request) {
            if ($request->is('battleship-ia/*')) {
                return response()->json([
                    'message' => 'Cette action n’est pas autorisée.'
                ], 403);
            }
        });
    })->create();
