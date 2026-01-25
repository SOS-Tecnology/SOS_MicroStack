<?php
session_start();

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../src/dependencies.php'; // conexión y dependencias


use Slim\Factory\AppFactory;
use Medoo\Medoo;
use Dotenv\Dotenv;
use App\Middleware\ValidationMiddleware;
use App\Controllers\FichaTecnicaController;

// Cargar variables de entorno
$dotenv = Dotenv::createImmutable(__DIR__ . "/..");
$dotenv->load();

// Inicializar conexión con Medoo usando .env
$GLOBALS['db'] = new Medoo([
    'database_type' => $_ENV['DB_TYPE'],
    'database_name' => $_ENV['DB_NAME'],
    'server'        => $_ENV['DB_HOST'],
    'username'      => $_ENV['DB_USER'],
    'password'      => $_ENV['DB_PASS'],
    'charset'       => $_ENV['DB_CHARSET']
]);

$app = AppFactory::create();

// Middleware de errores
$app->addErrorMiddleware(true, true, true);

/**
 * Función auxiliar para renderizar vistas con layout
 */
function renderView($response, $viewPath, $title) {
    ob_start();
    include $viewPath;
    $content = ob_get_clean();
    include __DIR__ . '/../src/Views/layouts/dashboard.php';
    return $response;
}

// ------------------- RUTAS ------------------- //

// Ruta principal → Dashboard
$app->get('/', function ($request, $response, $args) {
    return renderView(
        $response,
        __DIR__ . '/../src/Views/dashboard_home.php',
        "Dashboard"
    );
});

// Fichas Técnicas
$app->get('/fichas-tecnicas', function ($request, $response, $args) {
    return renderView(
        $response,
        __DIR__ . '/../src/Views/fichas-tecnicas/index.php',
        "Fichas Técnicas"
    );
});

$app->get('/fichas-tecnicas/create', function ($request, $response) {
    // Pasar datos desde la DB
    $productosBase = $GLOBALS['db']->select("inrefinv", ["codr","descr"]);
    $clientes      = $GLOBALS['db']->select("geclientes", ["codcli","nombrecli"]);
    $referencias   = $GLOBALS['db']->select("inrefinv", ["codr","descr"]);

    require __DIR__ . '/../src/Views/fichas-tecnicas/create.php';
    return $response;
});

$app->post('/fichas-tecnicas/store', function ($request, $response) {
    $controller = new FichaTecnicaController($GLOBALS['db']);
    return $controller->store($request, $response);
})->add(new ValidationMiddleware());

/*
// Clientes
$app->get('/clientes', function ($request, $response, $args) {
    return renderView(
        $response,
        __DIR__ . '/../src/Views/clientes/index.php',
        "Clientes"
    );
});

// Satélites
$app->get('/satelites', function ($request, $response, $args) {
    return renderView(
        $response,
        __DIR__ . '/../src/Views/satelites/index.php',
        "Manejo de Satélites"
    );
});

// Orden de Pedido
$app->get('/orden-pedido', function ($request, $response, $args) {
    return renderView(
        $response,
        __DIR__ . '/../src/Views/orden-pedido/index.php',
        "Orden de Pedido"
    );
});

// Orden de Producción
$app->get('/orden-produccion', function ($request, $response, $args) {
    return renderView(
        $response,
        __DIR__ . '/../src/Views/orden-produccion/index.php',
        "Orden de Producción"
    );
});

// Seguimiento a OPs
$app->get('/seguimiento-op', function ($request, $response, $args) {
    return renderView(
        $response,
        __DIR__ . '/../src/Views/seguimiento-op/index.php',
        "Seguimiento a OPs"
    );
});
*/
// Test DB
$app->get('/test-db', function ($request, $response) {
    try {
        $clientes = $GLOBALS['db']->select("geclientes", ["codcli","nombrecli"]);
        $response->getBody()->write(json_encode($clientes));
    } catch (Exception $e) {
        $response->getBody()->write("Error: " . $e->getMessage());
    }
    return $response->withHeader('Content-Type', 'application/json');
});

// Logout
$app->get('/logout', function ($request, $response, $args) {
    session_destroy();
    $response->getBody()->write("Sesión cerrada");
    return $response;
});

$app->run();
