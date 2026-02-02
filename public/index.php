<?php
session_start();

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../src/dependencies.php';

use Slim\Factory\AppFactory;
use Medoo\Medoo;
use Dotenv\Dotenv;
use App\Middleware\ValidationMiddleware;
use App\Controllers\FichaTecnicaController;
use App\Controllers\SateliteController;
use App\Http\Controllers\OrdenPedidoController;
// 1. Configuración del Entorno
$dotenv = Dotenv::createImmutable(__DIR__ . "/..");
$dotenv->load();

// 2. Conexión Global a la Base de Datos
$GLOBALS['db'] = new Medoo([
    'database_type' => $_ENV['DB_TYPE'],
    'database_name' => $_ENV['DB_NAME'],
    'server'         => $_ENV['DB_HOST'],
    'username'       => $_ENV['DB_USER'],
    'password'       => $_ENV['DB_PASS'],
    'charset'        => $_ENV['DB_CHARSET']
]);

$app = AppFactory::create();
$app->addErrorMiddleware(true, true, true);

/**
 * Función auxiliar para renderizar vistas con layout
 */
function renderView($response, $viewPath, $title, $data = [])
{
    extract($data);
    ob_start();
    include $viewPath;
    $content = ob_get_clean();
    include __DIR__ . '/../src/Views/layouts/dashboard.php';
    return $response;
}

// ---------------------------------------------------------
// RUTAS: GENERALES
// ---------------------------------------------------------

$app->get('/', function ($request, $response) {
    return renderView($response, __DIR__ . '/../src/Views/dashboard_home.php', "Dashboard");
});

// ---------------------------------------------------------
// RUTAS: FICHAS TÉCNICAS
// ---------------------------------------------------------

$app->get('/fichas-tecnicas', function ($request, $response) {
    $controller = new FichaTecnicaController($GLOBALS['db']);
    return $controller->index($request, $response);
});

$app->get('/fichas-tecnicas/create', function ($request, $response) {
    $data = [
        'productosBase' => $GLOBALS['db']->select("inrefinv", ["codr", "descr"]),
        'clientes'      => $GLOBALS['db']->select("geclientes", ["codcli", "nombrecli"]),
        'referencias'   => $GLOBALS['db']->select("inrefinv", ["codr", "descr"])
    ];
    return renderView($response, __DIR__ . '/../src/Views/fichas-tecnicas/create.php', "Nueva Ficha Técnica", $data);
});

$app->post('/fichas-tecnicas/store', function ($request, $response) {
    $controller = new FichaTecnicaController($GLOBALS['db']);
    return $controller->store($request, $response);
})->add(new ValidationMiddleware());

$app->get('/fichas-tecnicas/show/{id}', function ($request, $response, $args) {
    $controller = new FichaTecnicaController($GLOBALS['db']);
    return $controller->show($request, $response, $args);
});

$app->get('/fichas-tecnicas/edit/{id}', function ($request, $response, $args) {
    $controller = new FichaTecnicaController($GLOBALS['db']);
    return $controller->edit($request, $response, $args);
});

$app->post('/fichas-tecnicas/update/{id}', function ($request, $response, $args) {
    $controller = new FichaTecnicaController($GLOBALS['db']);
    return $controller->update($request, $response, $args);
});

$app->post('/fichas-tecnicas/delete/{id}', function ($request, $response, $args) {
    $controller = new FichaTecnicaController($GLOBALS['db']);
    return $controller->delete($request, $response, $args);
});

// ---------------------------------------------------------
// RUTAS: CONTROL DE SATELITES
// ---------------------------------------------------------

$app->get('/satelites', function ($request, $response) {
    $controller = new SateliteController($GLOBALS['db']);
    return $controller->index($request, $response);
});

$app->get('/satelites/create', function ($request, $response) {
    $controller = new SateliteController($GLOBALS['db']);
    return $controller->create($request, $response);
});

$app->post('/satelites/store', function ($request, $response) {
    $controller = new App\Controllers\SateliteController($GLOBALS['db']);
    return $controller->store($request, $response);
})->add(new App\Middleware\SateliteValidation()); // Agregamos la validación aquí

$app->get('/satelites/show/{id}', function ($request, $response, $args) {
    $controller = new SateliteController($GLOBALS['db']);
    return $controller->show($request, $response, $args);
});

$app->get('/satelites/edit/{id}', function ($request, $response, $args) {
    $controller = new SateliteController($GLOBALS['db']);
    return $controller->edit($request, $response, $args);
});

$app->post('/satelites/update/{id}', function ($request, $response, $args) {
    $controller = new SateliteController($GLOBALS['db']);
    return $controller->update($request, $response, $args);
});

$app->post('/satelites/anular/{id}', function ($request, $response, $args) {
    $controller = new SateliteController($GLOBALS['db']);
    return $controller->anular($request, $response, $args);
});

// ---------------------------------------------------------
// RUTAS: SISTEMA / ORDEN PEDIDO
// ---------------------------------------------------------
// Rutas Orden de Pedido

$app->get('/orden-pedido', function ($request, $response) {
    // Aquí es donde inyectamos el $GLOBALS['db'] al controlador
    $controller = new App\Controllers\OrdenPedidoController($GLOBALS['db']);
    return $controller->index($request, $response);
});

$app->get('/orden-pedido/create', function ($request, $response) {
    $controller = new App\Controllers\OrdenPedidoController($GLOBALS['db']);
    return $controller->create($request, $response);
});

$app->post('/orden-pedido/store', function ($request, $response) {
    $controller = new App\Controllers\OrdenPedidoController($GLOBALS['db']);
    return $controller->store($request, $response);
});
$app->get('/orden-pedido/show/{id}', function ($request, $response, $args) {
    $controller = new App\Controllers\OrdenPedidoController($GLOBALS['db']);
    return $controller->show($request, $response, $args);
});

$app->get('/orden-pedido/edit/{id}', function ($request, $response, $args) {
    $controller = new App\Controllers\OrdenPedidoController($GLOBALS['db']);
    return $controller->edit($request, $response, $args);
});

$app->post('/orden-pedido/update/{id}', function ($request, $response, $args) {
    $controller = new App\Controllers\OrdenPedidoController($GLOBALS['db']);
    return $controller->update($request, $response, $args);

$app->get('/orden-pedido/pdf/{id}', [\App\Controllers\OrdenPedidoController::class, 'generarPdf']);
});
// ---------------------------------------------------------
// RUTAS: SISTEMA / TEST
// ---------------------------------------------------------

$app->get('/test-db', function ($request, $response) {
    try {
        $clientes = $GLOBALS['db']->select("geclientes", ["codcli", "nombrecli"], ["LIMIT" => 10]);
        $response->getBody()->write(json_encode($clientes));
    } catch (Exception $e) {
        $response->getBody()->write("Error: " . $e->getMessage());
    }
    return $response->withHeader('Content-Type', 'application/json');
});

$app->get('/logout', function ($request, $response) {
    session_destroy();
    return $response->withHeader('Location', '/')->withStatus(302);
});
// Sucursales por cliente
$app->get('/orden-pedido/sucursales/{codcli}', function ($request, $response, $args) {
    $codcli = $args['codcli'];

    try {
        // Filtrar sucursales por cliente
        $sucursales = $GLOBALS['db']->select("geclientesaux", [
            "codsuc",
            "nombresuc"
        ], [
            "codcli" => $codcli
        ]);

        $response->getBody()->write(json_encode($sucursales));
        return $response->withHeader('Content-Type', 'application/json');
    } catch (Exception $e) {
        $response->getBody()->write(json_encode(["error" => $e->getMessage()]));
        return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
    }
});


$app->run();
