<?php

namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class AuthController
{
    protected $db;

    public function __construct($container)
    {
        $this->db = $container->get('db');
    }

    public function showLogin(Request $request, Response $response)
    {
        if (isset($_SESSION['user'])) {
            return $response
                ->withHeader('Location', '/dashboard_home')
                ->withStatus(302);
        }

        require __DIR__ . '/../Views/auth/login.php';
        return $response;
    }

    public function login(Request $request, Response $response)
    {
        $data = $request->getParsedBody();

        $email    = trim($data['email'] ?? '');
        $password = trim($data['password'] ?? '');

        $errors = [];

        if ($email === '' || $password === '') {
            $errors[] = 'Todos los campos son obligatorios.';
        }

        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            return $response->withHeader('Location', '/login')->withStatus(302);
        }

        $user = $this->db->get('users', '*', [
            'email' => $email
        ]);

        if (!$user || !password_verify($password, $user['password'])) {
            $_SESSION['errors'] = ['Credenciales incorrectas.'];
            return $response->withHeader('Location', '/login')->withStatus(302);
        }

        $_SESSION['user'] = [
            'id'    => $user['id'],
            'name'  => $user['name'],
            'email' => $user['email'],
        ];

        return $response
            ->withHeader('Location', '/dashboard_home')
            ->withStatus(302);
    }

    public function logout(Request $request, Response $response)
    {
        session_destroy();
        return $response->withHeader('Location', '/login')->withStatus(302);
    }
}
