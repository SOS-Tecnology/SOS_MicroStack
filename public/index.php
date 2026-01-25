<?php
session_start();

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../src/dependencies.php'; // aquí cargas la conexión

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

// Aquí van tus rutas...
$app->post('/fichas-tecnicas/store', function ($request, $response) {
    $controller = new \App\Controllers\FichaTecnicaController($GLOBALS['db']);
    return $controller->store($request, $response);
})->add(new ValidationMiddleware());

// Cargar variables de entorno
$dotenv = Dotenv::createImmutable(__DIR__ . "/..");
$dotenv->load();

// Ruta raíz de prueba
$app->get('/', function ($request, $response, $args) {
    $response->getBody()->write("¡Hola SOS-MicroStack! Slim está funcionando 🚀");
    return $response;
});


$app->get('/fichas-tecnicas/create', function ($request, $response) {
    // Aquí deberías pasar $productosBase, $clientes y $referencias desde la DB
    $productosBase = $GLOBALS['db']->select("inrefinv", ["codr","descr"]);
    $clientes      = $GLOBALS['db']->select("geclientes", ["codcli","nombrecli"]);
    $referencias   = $GLOBALS['db']->select("inrefinv", ["codr","descr"]);

    require __DIR__ . '/../src/Views/fichas-tecnicas/create.php';
    return $response;
});

$app->get('/fichas-tecnicas', function ($request, $response) {
    // Traer fichas técnicas
    $fichas = $GLOBALS['db']->select("fichas_tecnicas", "*");

    // Para cada ficha, traer fotos y referencias
    foreach ($fichas as &$ficha) {
        $ficha['fotos'] = $GLOBALS['db']->select("ficha_tecnica_fotos", ["ruta_imagen"], [
            "id_ficha_tecnica" => $ficha['id']
        ]);

        $ficha['referencias'] = $GLOBALS['db']->query("
            SELECT d.codr, i.descr, d.cantidad, d.talla, d.color
            FROM ficha_tecnica_detalles d
            JOIN inrefinv i ON d.codr = i.codr
            WHERE d.id_ficha_tecnica = {$ficha['id']}
        ")->fetchAll();
    }

    require __DIR__ . '/../src/Views/fichas-tecnicas/index.php';
    return $response;
});

$app->get('/test-db', function ($request, $response) {
    try {
        $clientes = $GLOBALS['db']->select("geclientes", ["codcli","nombrecli"]);
        $response->getBody()->write(json_encode($clientes));
    } catch (Exception $e) {
        $response->getBody()->write("Error: " . $e->getMessage());
    }
    return $response->withHeader('Content-Type', 'application/json');
});


$app->run();
