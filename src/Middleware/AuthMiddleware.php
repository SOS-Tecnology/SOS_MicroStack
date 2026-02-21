<?php
namespace App\Middleware;

class AuthMiddleware
{
    public function __invoke($request, $handler)
    {
        // 1. Verificar si hay sesión
        if (!isset($_SESSION['user'])) {
            header('Location: /login');
            exit;
        }

        // 2. Control de inactividad (30 minutos)
        $timeout = 1800; // 30 * 60

        if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY']) > $timeout) {
            session_unset();
            session_destroy();
            header('Location: /login');
            exit;
        }

        // 3. Actualizar actividad
        $_SESSION['LAST_ACTIVITY'] = time();

        return $handler->handle($request);
    }
}
