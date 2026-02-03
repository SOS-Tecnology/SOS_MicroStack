<?php

use Slim\App;
use App\Controllers\AuthController;

return function (App $app) {

    $app->get('/login', [AuthController::class, 'showLogin']);
    $app->post('/login', [AuthController::class, 'login']);
    $app->get('/logout', [AuthController::class, 'logout']);

};
