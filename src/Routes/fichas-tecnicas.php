<?php
use Slim\App;
use App\Controllers\FichaTecnicaController;

return function (App $app) {
    $container = $app->getContainer();
    $controller = new FichaTecnicaController($container->get('db'));

    $app->get('/fichas-tecnicas/create', [$controller, 'create']);
    $app->post('/fichas-tecnicas/store', [$controller, 'store']);
};
