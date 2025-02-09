<?php

namespace src\Middleware;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response;

class AuthMiddleware
{
    public function __invoke(Request $request, RequestHandler $handler): Response
    {
        // Check if the user is logged in
        if (!isset($_SESSION['user_id'])) {
            // User is not logged in, redirect to login page
            $response = new Response();
            return $response->withHeader('Location', '/login')->withStatus(302);
        }

        // User is logged in, proceed to the next middleware or route handler
        return $handler->handle($request);
    }
}