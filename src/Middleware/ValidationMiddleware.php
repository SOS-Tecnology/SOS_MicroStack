<?php
// src/Middleware/ValidationMiddleware.php
namespace App\Middleware;

class ValidationMiddleware {
    public function __invoke($request, $handler) {
        $data = $request->getParsedBody();
        $errors = [];

        if (empty($data['nombre_ficha'])) {
            $errors[] = "El nombre de la ficha es obligatorio.";
        }
        if (empty($data['id_cliente'])) {
            $errors[] = "Debe seleccionar un cliente.";
        }
        if (empty($data['id_producto_base'])) {
            $errors[] = "Debe seleccionar un producto base.";
        }

        if (!empty($errors)) {
            // Guardar errores en sesión
            $_SESSION['errors'] = $errors;
            return (new \Slim\Psr7\Response())
                ->withHeader('Location', '/fichas-tecnicas/create')
                ->withStatus(302);
        }

        return $handler->handle($request);
    }
}
