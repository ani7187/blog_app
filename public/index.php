<?php

session_set_cookie_params([
    'lifetime' => 3600, // 1 hour
    'path' => '/',
    'domain' => 'localhost',
    'secure' => true, // Only send over HTTPS
    'httponly' => true, // Prevent JavaScript access
    'samesite' => 'Strict' // Prevent CSRF attacks
]);
session_start();

use Slim\Factory\AppFactory;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use src\Controllers\AuthController;
use src\Controllers\CommentController;
use src\Controllers\PostController;
use src\Middleware\AuthMiddleware;

require __DIR__ . '/../vendor/autoload.php';

$app = AppFactory::create();

$app->get('/', [\src\Controllers\HomeController::class, 'index']);

$app->get('/login', [AuthController::class, 'loginForm']);
$app->post('/login', [AuthController::class, 'login']);
$app->get('/logout', [AuthController::class, 'logout']);

$app->get('/register', [AuthController::class, 'registerForm']);
$app->post('/register', [AuthController::class, 'register']);

$app->group('', function ($group) {
    $group->get('/posts', [PostController::class, 'index']);
    $group->get('/posts/create', [PostController::class, 'create']);
    $group->post('/posts/create', [PostController::class, 'create']);
    $group->get('/posts/{id}', [PostController::class, 'show']);
    $group->get('/my_posts', [PostController::class, 'myPosts']);

    $group->get('/posts/edit/{id}', [PostController::class, 'edit']);
    $group->post('/posts/edit/{id}', [PostController::class, 'edit']);
    $group->get('/posts/delete/{id}', [PostController::class, 'delete']);

    $group->post('/posts/{id}/comments', [CommentController::class, 'create']);
    $group->post('/posts/{post_id}/comments/{id}/delete', [CommentController::class, 'delete']);
})->add(new AuthMiddleware());

//rout for undefined routes
$app->map(['GET', 'POST'], '/{routes:.+}', function (Request $request, Response $response, $args) {
    ob_start();
    include  __DIR__ . '/../src/Views/404.php';
    $html = ob_get_clean();
    $response->getBody()->write($html);
    return $response->withStatus(404);
});

$app->run();