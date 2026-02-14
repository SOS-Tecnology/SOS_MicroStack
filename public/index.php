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
use App\Controllers\OrdenPedidoController;
use App\Controllers\OrdenProdController;
use App\Models\OprModel;

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


ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

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


// Ruta raíz: redirige según sesión
$app->get('/', function ($request, $response) {
    if (isset($_SESSION['user'])) {
        return $response->withHeader('Location', '/dashboard_home')->withStatus(302);
    } else {
        return $response->withHeader('Location', '/login')->withStatus(302);
    }
});

// Login
$app->get('/login', function ($request, $response) {
    ob_start();
    include __DIR__ . '/../src/Views/Auth/login.php';
    $response->getBody()->write(ob_get_clean());
    return $response;
});

$app->post('/login', function ($request, $response) {

    $data = $request->getParsedBody();

    $email    = trim($data['email'] ?? '');
    $password = trim($data['password'] ?? '');

    if ($email === '' || $password === '') {
        $_SESSION['errors'] = ['Todos los campos son obligatorios'];
        return $response->withHeader('Location', '/login')->withStatus(302);
    }

    $user = $GLOBALS['db']->get('users', '*', [
        'email' => $email
    ]);

    if (!$user || !password_verify($password, $user['password'])) {
        $_SESSION['errors'] = ['Credenciales incorrectas'];
        return $response->withHeader('Location', '/login')->withStatus(302);
    }

    // Guardamos solo lo necesario
    $_SESSION['user'] = [
        'id'    => $user['id'],
        'name'  => $user['name'],
        'email' => $user['email'],
    ];

    return $response->withHeader('Location', '/dashboard_home')->withStatus(302);
});

$app->get('/logout', function ($request, $response) {
    session_destroy();
    return $response->withHeader('Location', '/login')->withStatus(302);
});


$app->get('/dashboard_home', function ($request, $response) {
    if (!isset($_SESSION['user'])) {
        return $response->withHeader('Location', '/login')->withStatus(302);
    }

    return renderView(
        $response,
        __DIR__ . '/../src/Views/dashboard_home.php',
        'Dashboard'
    );
});


// ----------------------------------------------------------
// --- CRUD USUARIOS ---
$app->get('/usuarios', function ($request, $response) use ($database) {
    $usuarios = $database->select("users", "*");
    ob_start();
    include __DIR__ . '/../src/Views/Usuarios/index.php';
    $response->getBody()->write(ob_get_clean());
    return $response;
});

$app->get('/usuarios/create', function ($request, $response) {
    ob_start();
    include __DIR__ . '/../src/Views/Usuarios/create.php';
    $response->getBody()->write(ob_get_clean());
    return $response;
});

$app->post('/usuarios/store', function ($request, $response) use ($database) {
    $data = $request->getParsedBody();
    $database->insert("usuarios", [
        "nombre" => $data['nombre'],
        "email" => $data['email'],
        "password" => password_hash($data['password'], PASSWORD_DEFAULT),
        "rol" => $data['rol']
    ]);
    return $response->withHeader('Location', '/usuarios')->withStatus(302);
});

// ---------------------------------------------------------
// RUTAS: FICHAS TÉCNICAS
// ---------------------------------------------------------

$app->get('/fichas-tecnicas', function ($request, $response) {
    $controller = new FichaTecnicaController($GLOBALS['db']);
    return $controller->index($request, $response);
});

// $app->get('/fichas-tecnicas/create', function ($request, $response) {
//     $data = [
//         'productosBase' => $GLOBALS['db']->select("inrefinv", ["codr", "descr"]),
//         'clientes'      => $GLOBALS['db']->select("geclientes", ["codcli", "nombrecli"]),
//         'referencias'   => $GLOBALS['db']->select("inrefinv", ["codr", "descr"])
//     ];
//     return renderView($response, __DIR__ . '/../src/Views/fichas-tecnicas/create.php', "Nueva Ficha Técnica", $data);
// });
$app->get('/fichas-tecnicas/create', function ($request, $response) {
    $controller = new FichaTecnicaController($GLOBALS['db']);
    return $controller->create($request, $response);
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

// ORDENES DE PRODUCCION
// Rutas Orden de Producción (OPR)
// var_dump(class_exists(\App\Controllers\OrdenProdController::class));
// exit;
$app->get('/orden-produccion', function ($request, $response) {
    // Aquí es donde inyectamos el $GLOBALS['db'] al controlador
    $controller = new App\Controllers\OrdenProdController($GLOBALS['db']);
    return $controller->index($request, $response);
});
$app->get('/orden-produccion/create/{documento}', function ($request, $response, $args) {
    $controller = new App\Controllers\OrdenProdController($GLOBALS['db']);
    return $controller->create($request, $response, $args);
});

$app->post('/orden-produccion/store/{documento}', function ($request, $response, $args) {
    $controller = new App\Controllers\OrdenProdController($GLOBALS['db']);
    return $controller->store($request, $response, $args);
});


$app->get('/seguimiento-opr', function ($request, $response, $args) {
    $controller = new App\Controllers\OrdenProdController($GLOBALS['db']);
    return $controller->seguimiento($request, $response, $args);
});



$app->get('/orden-produccion/ver/{documento}', function ($request, $response, $args) {
    $controller = new App\Controllers\OrdenProdController($GLOBALS['db']);
    return $controller->ver($request, $response, $args);
});


$app->run();
