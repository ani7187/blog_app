<?php

namespace src\Controllers;

use core\Controller;
use helpers\ErrorFlow;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use src\Models\Comment;

class CommentController extends Controller
{
    public function create(Request $request, Response $response, $args): Response
    {
        $content = trim($request->getParsedBody()['content'] ?? '');
        $postId = (int)$args['id'];

        if (empty($content)) {
            ErrorFlow::addError('comment_error', "Title and content required");
        }

        (new Comment())
            ->setContent($content)
            ->setUserId($_SESSION['user_id'])
            ->setPostId($postId)
            ->create();

        ErrorFlow::addError('comment_error', "Success create");
        return $response->withHeader('Location', "/posts/$postId")->withStatus(302);
    }

    public function delete(Request $request, Response $response, $args): Response
    {
        $commentId = (int) $args['id'];
        $postId = (int) $args['post_id'];
        (new Comment())->delete($commentId);

        return $response->withHeader('Location', "/posts/$postId")->withStatus(302);
    }
}