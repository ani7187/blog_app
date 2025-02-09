<?php

namespace src\Controllers;

use core\Controller;
use helpers\ErrorFlow;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use src\Models\Comment;
use src\Models\Post;

class PostController extends Controller
{
    /**
     * @param Request $request
     * @param Response $response
     * @param $args
     * @return Response
     */
    public function index(Request $request, Response $response, $args): Response
    {
        $page = isset($request->getQueryParams()['page']) ? (int)$request->getQueryParams()['page'] : 1;
        $query = $request->getQueryParams()['query'] ?? "";

        $limit = 3;
        $paginationData = (new Post())->getPosts($page, $limit, $query);

        $posts = $paginationData['posts'];
        $totalPosts = $paginationData['totalPosts'];
        $totalPages = ceil($totalPosts / $limit);

        $paginationLinks = $this->generatePaginationLinks($totalPages, $query);

        return $this->render($response, 'posts/index.php', [
            'posts' => $posts,
            'totalPosts' => $totalPosts,
            'paginationLinks' => $paginationLinks,
            'totalPages' => $totalPages,
        ]);
    }

    /**
     * @param Request $request
     * @param Response $response
     * @param $args
     * @return Response
     */
    public function show(Request $request, Response $response, $args): Response
    {
        $postId = (int)$args['id'];
        $post = (new Post())->getByID($postId) ?? [];

        $page = isset($request->getQueryParams()['page']) ? (int)$request->getQueryParams()['page'] : 1;
        $limit = 5;
        $offset = ($page - 1) * $limit;

        $commentModel = new Comment();
        $comments = $commentModel->getComments($postId, $limit, $offset);
        $totalComments = $commentModel->countComments($postId);
        $totalPages = ceil($totalComments / $limit);

        return $this->render($response, 'posts/show.php', [
            'post' => $post,
            'comments' => $comments,
            'totalPages' => $totalPages
        ]);
    }

    /**
     * @param Request $request
     * @param Response $response
     * @param $args
     * @return Response
     */
    public function myPosts(Request $request, Response $response, $args): Response
    {
        $page = isset($request->getQueryParams()['page']) ? (int)$request->getQueryParams()['page'] : 1;
        $userId = $_SESSION['user_id'] ?? null;

        $limit = 3;
        $paginationData = (new Post())->getUserPosts($userId, $page, $limit);

        $totalPosts = $paginationData['totalPosts'] ?? [];
        $totalPages = ceil($totalPosts / $limit);

        $paginationLinks = $this->generatePaginationLinks($totalPages);

        return $this->render($response, 'posts/my_posts.php', [
            'posts' => $paginationData['posts'],
            'totalPosts' => $totalPosts,
            'paginationLinks' => $paginationLinks,
            'totalPages' => $totalPages
        ]);
    }

    /**
     * @param Request $request
     * @param Response $response
     * @param $args
     * @return Response
     */
    public function create(Request $request, Response $response, $args): Response
    {
        // If the form was submitted to create the post
        if ($request->getMethod() === 'POST') {
            $title = trim($request->getParsedBody()['title']);
            $content = trim($request->getParsedBody()['content']);

            if (empty($title) || empty($content)) {
                ErrorFlow::addError('post_error', "Title and content required");
                return $response->withHeader('Location', '/posts/create')->withStatus(302);
            }

            (new Post())
                ->setTitle($title)
                ->setContent($content)
                ->setUserID($_SESSION['user_id'])
                ->create();

            ErrorFlow::addError('post_error', "Success create");
            return $response->withHeader('Location', '/my_posts')->withStatus(302);
        }

        return $this->render($response, 'posts/create.php');
    }

    /**
     * @param Request $request
     * @param Response $response
     * @param $args
     * @return Response
     */
    public function edit(Request $request, Response $response, $args): Response
    {
        $postId = (int) $args['id'];
        $post = (new Post())->getByID($postId);

        if (!$post) {
            ErrorFlow::addError('post_error', "Post not found");
            return $response->withHeader('Location', '/my_posts')->withStatus(302);
        }

        // If the form was submitted to update the post
        if ($request->getMethod() === 'POST') {
            $title = $request->getParsedBody()['title'];
            $content = $request->getParsedBody()['content'];

            if (empty($title) || empty($content)) {
                ErrorFlow::addError('post_error', "Title and content required");
                return $response->withHeader('Location', '/posts/create')->withStatus(302);
            }

            // Update the post
            (new Post())
                ->setID($postId)
                ->setTitle($title)
                ->setContent($content)
                ->update();

            ErrorFlow::addError('post_error', "Success update");
            return $response->withHeader('Location', '/my_posts')->withStatus(302);
        }

        return $this->render($response, 'posts/edit.php', ['post' => $post]);
    }

    /**
     * @param Request $request
     * @param Response $response
     * @param $args
     * @return Response
     */
    public function delete(Request $request, Response $response, $args): Response
    {
        $postId = (int) $args['id'];
        (new Post())->delete($postId);

        return $response->withHeader('Location', '/my_posts')->withStatus(302);
    }

    /**
     * Generate pagination links.
     */
    private function generatePaginationLinks(int $totalPages, string $query = ''): string
    {
        $paginationLinks = '';
        for ($i = 1; $i <= $totalPages; $i++) {
            $paginationLinks .= '<a href="/posts?page=' . $i . '&query=' . urlencode($query) . '" class="page-link d-inline">' . $i . '</a> ';
        }
        return $paginationLinks;
    }
}
