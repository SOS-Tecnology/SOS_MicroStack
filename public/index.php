<?php
session_set_cookie_params([
    'lifetime' => 0,
    'path' => '/',
    'httponly' => true,
    'secure' => false, // cámbialo a true cuando uses HTTPS
    'samesite' => 'Lax'
]);

session_start();
// var_dump(class_exists(\App\Controllers\OrdenProdController::class));
// exit;

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../src/dependencies.php';
require_once __DIR__ . '/../src/config.php';

use Slim\Factory\AppFactory;
use Medoo\Medoo;
use Dotenv\Dotenv;
use App\Middleware\AuthMiddleware;
use App\Middleware\ValidationMiddleware;
use App\Controllers\FichaTecnicaController;
use App\Controllers\SateliteController;
use App\Controllers\OrdenPedidoController;
use App\Controllers\OrdenProdController;
use App\Controllers\ProcesosFTController;
use App\Models\OprModel;
use App\Controllers\EppController;

$authMiddleware = new AuthMiddleware();

// 1. Configuración del Entorno
$dotenv = Dotenv::createImmutable(__DIR__ . "/..");
$dotenv->load();

// 2. Conexión Global a la Base de Datos

$GLOBALS['db'] = new Medoo([
    'database_type' => $_ENV['DB_TYPE'],
    'database_name' => $_ENV['DB_NAME'],
    'server'        => $_ENV['DB_HOST'],
    'username'      => $_ENV['DB_USER'],
    'password'      => $_ENV['DB_PASS'],
    'charset'       => 'utf8mb4',    // ← asegurar que es utf8mb4, no utf8
    'collation'     => 'utf8mb4_unicode_ci',  // ← agregar esta línea
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
    // 🔐 regenerar ID de sesión
    session_regenerate_id(true);

    // Guardamos solo lo necesario
    $_SESSION['user'] = [
        'id'    => $user['id'],
        'name'  => $user['name'],
        'email' => $user['email'],
    ];
    // ⏱️ guardar actividad
    $_SESSION['LAST_ACTIVITY'] = time();
    return $response->withHeader('Location', '/dashboard_home')->withStatus(302);
});

$app->get('/logout', function ($request, $response) {

    $_SESSION = [];

    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(
            session_name(),
            '',
            time() - 42000,
            $params["path"],
            $params["domain"],
            $params["secure"],
            $params["httponly"]
        );
    }

    session_destroy();

    return $response->withHeader('Location', '/login')->withStatus(302);
});


$app->group('', function ($app) {

    // DASHBOARD
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
    // ----------------------------------------------------------

    $app->get('/usuarios', function ($request, $response) {
        $usuarios = $GLOBALS['db']->select("users", "*");
        return renderView($response, __DIR__ . '/../src/Views/Usuarios/index.php', "Usuarios", [
            'usuarios' => $usuarios
        ]);
    });

    $app->get('/usuarios/create', function ($request, $response) {
        return renderView($response, __DIR__ . '/../src/Views/Usuarios/create.php', "Nuevo Usuario", []);
    });

    $app->post('/usuarios/store', function ($request, $response) {
        $data = $request->getParsedBody();

        if ($GLOBALS['db']->has("users", ["email" => $data['email']])) {
            $_SESSION['errors'] = ["El correo ya está registrado."];
            return $response->withHeader('Location', '/usuarios/create')->withStatus(302);
        }

        $GLOBALS['db']->insert("users", [
            "name"     => $data['nombre'],
            "email"    => $data['email'],
            "password" => password_hash($data['password'], PASSWORD_DEFAULT),
            "rol"      => $data['rol']
        ]);

        $_SESSION['success'] = "Usuario {$data['nombre']} creado correctamente.";
        return $response->withHeader('Location', '/usuarios')->withStatus(302);
    });

    // Editar usuario
    $app->get('/usuarios/{id}/edit', function ($request, $response, $args) {
        $usuario = $GLOBALS['db']->get("users", "*", ["id" => (int)$args['id']]);
        if (!$usuario) {
            return $response->withHeader('Location', '/usuarios')->withStatus(302);
        }
        return renderView($response, __DIR__ . '/../src/Views/Usuarios/edit.php', "Editar Usuario", [
            'usuario' => $usuario
        ]);
    });

    $app->post('/usuarios/{id}/update', function ($request, $response, $args) {
        $id   = (int)$args['id'];
        $data = $request->getParsedBody();

        // Verificar correo duplicado (excluyendo el propio usuario)
        $existe = $GLOBALS['db']->has("users", [
            "email" => $data['email'],
            "id[!]" => $id
        ]);
        if ($existe) {
            $_SESSION['errors'] = ["El correo ya está en uso por otro usuario."];
            return $response->withHeader('Location', '/usuarios/' . $id . '/edit')->withStatus(302);
        }

        $campos = [
            "name"  => $data['nombre'],
            "email" => $data['email'],
            "rol"   => $data['rol']
        ];
        if (!empty($data['password'])) {
            $campos["password"] = password_hash($data['password'], PASSWORD_DEFAULT);
        }

        $GLOBALS['db']->update("users", $campos, ["id" => $id]);
        $_SESSION['success'] = "Usuario actualizado correctamente.";
        return $response->withHeader('Location', '/usuarios')->withStatus(302);
    });

    // Eliminar usuario
    $app->post('/usuarios/{id}/delete', function ($request, $response, $args) {
        $GLOBALS['db']->delete("users", ["id" => (int)$args['id']]);
        $_SESSION['success'] = "Usuario eliminado.";
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
    });
    $app->get('/orden-pedido/pdf/{id}', function ($request, $response, $args) {
        $controller = new App\Controllers\OrdenPedidoController($GLOBALS['db']);
        return $controller->generarPdf($request, $response, $args);
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
    $app->get('/orden-produccion/avance', function ($request, $response, $args) {
        $controller = new App\Controllers\OrdenProdController($GLOBALS['db']);
        return $controller->avance($request, $response, $args);
    });
    $app->get('/orden-produccion/avance/ver/{documento}', function ($request, $response, $args) {
        $controller = new App\Controllers\OrdenProdController($GLOBALS['db']);
        return $controller->verAvance($request, $response, $args);
    });
    $app->get('/orden-produccion/pdf/{documento}', function ($request, $response, $args) {
        $controller = new App\Controllers\OrdenProdController($GLOBALS['db']);
        return $controller->generarPdf($request, $response, $args);
    });
    // ===============================
    // GESTIÓN PROCESOS OPR (EPP / RPP)
    // ===============================
    $app->get('/orden-produccion/procesos/{documento}/{proceso}', function ($request, $response, $args) {
        $controller = new \App\Controllers\OrdenProdController($GLOBALS['db']);
        return $controller->procesos($request, $response, $args);
    });
    // PROCESOS FT se definirá en un grupo para mantener la organización
    $app->group('/procesos-ft', function ($group) {

        $group->get('', function ($request, $response) {
            $controller = new App\Controllers\ProcesosFTController($GLOBALS['db']);
            return $controller->index($request, $response);
        });

        $group->get('/create', function ($request, $response) {
            $controller = new App\Controllers\ProcesosFTController($GLOBALS['db']);
            return $controller->create($request, $response);
        });

        $group->post('/store', function ($request, $response) {
            $controller = new App\Controllers\ProcesosFTController($GLOBALS['db']);
            return $controller->store($request, $response);
        });

        $group->get('/edit/{id}', function ($request, $response, $args) {
            $controller = new App\Controllers\ProcesosFTController($GLOBALS['db']);
            return $controller->edit($request, $response, $args);
        });

        $group->get('/show/{id}', function ($request, $response, $args) {
            $controller = new App\Controllers\ProcesosFTController($GLOBALS['db']);
            return $controller->show($request, $response, $args);
        });
        $group->post('/update/{id}', function ($request, $response, $args) {
            $controller = new App\Controllers\ProcesosFTController($GLOBALS['db']);
            return $controller->update($request, $response, $args);
        });

        $group->get('/delete/{id}', function ($request, $response, $args) {
            $controller = new App\Controllers\ProcesosFTController($GLOBALS['db']);
            return $controller->delete($request, $response, $args);
        });
    });

    // ==========================
    // EPP - ENVÍO A PROCESO
    // ==========================

    // Listado
    $app->get('/epp', function ($request, $response) {

        $controller = new \App\Controllers\EppController($GLOBALS['db']);
        return $controller->index($request, $response);
    });

    // Formulario crear
    // $app->get('/epp/create/{documento}/{ft_id}/{proceso}', function ($request, $response, $args) {

    //     $controller = new \App\Controllers\EppController($GLOBALS['db']);
    //     return $controller->create($request, $response, $args);
    // });
    // Formulario crear
    $app->get('/epp/create/{documento}/{proceso}', function ($request, $response, $args) {

        $controller = new \App\Controllers\EppController($GLOBALS['db']);
        return $controller->create($request, $response, $args);
    });
    // Guardar
    $app->post('/epp/store', function ($request, $response) {

        $controller = new \App\Controllers\EppController($GLOBALS['db']);
        return $controller->store($request, $response);
    });

    // Ver
    $app->get('/epp/show/{documento}', function ($request, $response, $args) {
        $controller = new \App\Controllers\EppController($GLOBALS['db']);
        return $controller->show($request, $response, $args);
    });

    // Imprimir
    $app->get('/epp/print/{documento}', function ($request, $response, $args) {
        $controller = new \App\Controllers\EppController($GLOBALS['db']);
        return $controller->print($request, $response, $args);
    });
    // Obtener datos de OPR (MP y META)
    $app->get('/epp/opr/{documento}', function ($request, $response, $args) {

        $controller = new \App\Controllers\EppController($GLOBALS['db']);
        return $controller->getOprData($request, $response, $args);
    });

    $app->get('/rpp/print/{documento}', function ($request, $response, $args) {
        $controller = new \App\Controllers\RppController($GLOBALS['db']);
        return $controller->print($request, $response, $args);
    });

    $app->get('/rpp/show/{documento}', function ($request, $response, $args) {
        $controller = new \App\Controllers\RppController($GLOBALS['db']);
        return $controller->show($request, $response, $args);
    });

    $app->get('/rpp/create/{epp}', function ($request, $response, $args) {
        $controller = new \App\Controllers\RppController($GLOBALS['db']);
        return $controller->create($request, $response, $args);
    });

    $app->post('/rpp/store', function ($request, $response) {
        $controller = new \App\Controllers\RppController($GLOBALS['db']);
        return $controller->store($request, $response);
    });
})->add($authMiddleware);

// ---------------------------------------------------------
// RUTAS PÚBLICAS: RECUPERACIÓN DE CONTRASEÑA
// ---------------------------------------------------------

$app->get('/forgot-password', function ($request, $response) {
    ob_start();
    include __DIR__ . '/../src/Views/Auth/forgot-password.php';
    $response->getBody()->write(ob_get_clean());
    return $response;
});

$app->post('/forgot-password', function ($request, $response) {
    $email = trim($request->getParsedBody()['email'] ?? '');

    $user = $GLOBALS['db']->get("users", ["id", "name", "email"], ["email" => $email]);

    // Respuesta genérica para no revelar si el correo existe
    if (!$user) {
        $_SESSION['success'] = "Si el correo está registrado, recibirás un enlace en breve.";
        return $response->withHeader('Location', '/forgot-password')->withStatus(302);
    }

    // Crear tabla de tokens si no existe
    $GLOBALS['db']->query("
        CREATE TABLE IF NOT EXISTS password_resets (
            id INT AUTO_INCREMENT PRIMARY KEY,
            email VARCHAR(255) NOT NULL,
            token VARCHAR(64) NOT NULL,
            expires_at DATETIME NOT NULL,
            INDEX (token),
            INDEX (email)
        )
    ");

    // Generar token seguro
    $token   = bin2hex(random_bytes(32));
    $expires = date('Y-m-d H:i:s', time() + 3600); // 1 hora

    $GLOBALS['db']->delete("password_resets", ["email" => $email]);
    $GLOBALS['db']->insert("password_resets", [
        "email"      => $email,
        "token"      => $token,
        "expires_at" => $expires
    ]);

    $resetUrl = (isset($_SERVER['HTTPS']) ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . '/reset-password/' . $token;
    $nombre   = $user['name'];

    $subject = "Restablecer contraseña - SOS MicroStack";
    $body    = "Hola {$nombre},\n\nHaz clic en el siguiente enlace para restablecer tu contraseña (válido 1 hora):\n\n{$resetUrl}\n\nSi no solicitaste esto, ignora este mensaje.\n\nSOS Technology";
    $headers = "From: noreply@sos-microstack.local\r\nContent-Type: text/plain; charset=UTF-8";

    @mail($email, $subject, $body, $headers);

    $_SESSION['success'] = "Si el correo está registrado, recibirás un enlace en breve.";
    return $response->withHeader('Location', '/forgot-password')->withStatus(302);
});

$app->get('/reset-password/{token}', function ($request, $response, $args) {
    $token = $args['token'];

    $reset = $GLOBALS['db']->get("password_resets", "*", [
        "token"         => $token,
        "expires_at[>]" => date('Y-m-d H:i:s')
    ]);

    if (!$reset) {
        $_SESSION['errors'] = ["El enlace no es válido o ha expirado."];
        return $response->withHeader('Location', '/forgot-password')->withStatus(302);
    }

    ob_start();
    include __DIR__ . '/../src/Views/Auth/reset-password.php';
    $response->getBody()->write(ob_get_clean());
    return $response;
});

$app->post('/reset-password/{token}', function ($request, $response, $args) {
    $token    = $args['token'];
    $data     = $request->getParsedBody();
    $password = $data['password'] ?? '';

    $reset = $GLOBALS['db']->get("password_resets", "*", [
        "token"         => $token,
        "expires_at[>]" => date('Y-m-d H:i:s')
    ]);

    if (!$reset) {
        $_SESSION['errors'] = ["El enlace no es válido o ha expirado."];
        return $response->withHeader('Location', '/forgot-password')->withStatus(302);
    }

    if (strlen($password) < 8) {
        $_SESSION['errors'] = ["La contraseña debe tener al menos 8 caracteres."];
        return $response->withHeader('Location', '/reset-password/' . $token)->withStatus(302);
    }

    $GLOBALS['db']->update("users",
        ["password" => password_hash($password, PASSWORD_DEFAULT)],
        ["email"    => $reset['email']]
    );

    $GLOBALS['db']->delete("password_resets", ["token" => $token]);

    $_SESSION['success'] = "Contraseña actualizada. Ya puedes iniciar sesión.";
    return $response->withHeader('Location', '/login')->withStatus(302);
});

$app->run();
