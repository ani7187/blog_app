<?php

use Php\Blog\Controllers\AuthController;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

$viewPath = __DIR__ . '/Views';

$app->get('/', function (Request $request, Response $response, $args) use ($viewPath) {
    ob_start();
    include $viewPath . '/home.php';
    $content = ob_get_clean();

    ob_start();
    include $viewPath . '/layout.php';
    $html = ob_get_clean();

    $response->getBody()->write($html);
    return $response;
});

$app->get('/login', function (Request $request, Response $response, $args) use ($viewPath) {
    ob_start();
    include $viewPath . '/auth/login.php';
    $content = ob_get_clean();

    ob_start();
    include $viewPath . '/layout.php';
    $html = ob_get_clean();

    $response->getBody()->write($html);
    return $response;
});

$app->post('/login', [AuthController::class, 'login']);

$app->get('/register', function (Request $request, Response $response, $args) use ($viewPath) {
    ob_start();
    include $viewPath . '/auth/register.php';
    $content = ob_get_clean();

    ob_start();
    include $viewPath . '/layout.php';
    $html = ob_get_clean();

    $response->getBody()->write($html);
    return $response;
});

// 404 Route Handler
$app->map(['GET', 'POST'], '/{routes:.+}', function (Request $request, Response $response, $args) use ($viewPath) {
    ob_start();
    include $viewPath . '/404.php';
    $html = ob_get_clean();

    $response->getBody()->write($html);
    return $response->withStatus(404);
});
