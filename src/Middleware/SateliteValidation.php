<?php
namespace App\Middleware;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as Handler;
use Slim\Psr7\Response;

class SateliteValidation
{
    public function __invoke(Request $request, Handler $handler): Response
    {
        $data = $request->getParsedBody();
        $errors = [];

        // Validar campos obligatorios
        if (empty($data['id_proveedor'])) $errors[] = "Debe seleccionar un proveedor.";
        if (empty($data['tipo'])) $errors[] = "El tipo de servicio es obligatorio.";
        
        // Validar rango de calificación
        if (isset($data['calificacion']) && ($data['calificacion'] < 0 || $data['calificacion'] > 5)) {
            $errors[] = "La calificación debe estar entre 0 y 5.";
        }

        if (!empty($errors)) {
            $response = new Response();
            $response->getBody()->write(json_encode(['errors' => $errors]));
            return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
        }

        return $handler->handle($request);
    }
}