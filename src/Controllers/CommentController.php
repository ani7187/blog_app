<?php

namespace src\Controllers;

use core\Controller;
use helpers\ErrorFlow;
use helpers\Logger;
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
            ErrorFlow::addError('comment_error', "Content required");
            return $response->withHeader('Location', "/posts/$postId")->withStatus(302);
        }

        try {
            (new Comment())
                ->setContent($content)
                ->setUserId($_SESSION['user_id'])
                ->setPostId($postId)
                ->create();

            ErrorFlow::addError('comment_error', "Success comment added");
        } catch (\Exception $e) {
            Logger::error("Error creating comment: " . $e->getMessage());
            ErrorFlow::addError('comment_error', "An error occurred while adding the comment.");
        }
        return $response->withHeader('Location', "/posts/$postId")->withStatus(302);
    }

    public function delete(Request $request, Response $response, $args): Response
    {
        $commentId = (int) $args['id'];
        $postId = (int) $args['post_id'];

        $message = "Comment successful deleted";
        try {
            if (!(new Comment())->delete($commentId)) {
                $message = "Failed to delete comment";
                Logger::error("Failed to delete comment with ID: " . $commentId);
            }
        } catch (\Exception $e) {
            $message = "An error occurred while deleting the comment.";
            Logger::error("Error deleting comment: " . $e->getMessage()); // Log critical errors
        }

        ErrorFlow::addError('comment_error', $message);
        return $response->withHeader('Location', "/posts/$postId")->withStatus(302);
    }
}