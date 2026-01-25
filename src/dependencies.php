<?php
use Medoo\Medoo;

// Configuración de conexión
$database = new Medoo([
    'database_type' => 'mysql',
    'database_name' => 'sosdyd',
    'server'        => 'localhost',
    'username'      => 'root',
    'password'      => '1234',
    'charset'       => 'utf8mb4'
]);

// Guardar en variable global (simple) o en contenedor Slim
$GLOBALS['db'] = $database;
