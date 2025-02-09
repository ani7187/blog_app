<?php

namespace core;

use Psr\Http\Message\ResponseInterface as Response;

abstract class Controller
{
    /**
     * Render the view with a layout.
     */
    protected function render(Response $response, string $view, array $data = []): Response
    {
        $viewPath = __DIR__ . '/../src/Views';
        extract($data);

        ob_start();
        include $viewPath . "/$view";
        $content = ob_get_clean();

        ob_start();
        include $viewPath . '/layout.php';
        $html = ob_get_clean();

        $response->getBody()->write($html);
        return $response;
    }
}