<?php

namespace src\Controllers;

use core\Controller;
use helpers\CacheHelper;
use helpers\ErrorFlow;
use helpers\Logger;
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

        $cacheKey = "posts_page_{$page}_query_" . md5($query);
        $cacheHelper = new \helpers\CacheHelper($cacheKey);
        $cachedData = $cacheHelper->readCache();

        if ($cachedData !== null) {
            $posts = $cachedData['posts'];
            $pagesCnt = $cachedData['pagesCnt'];
        } else {
            $limit = 3;
            $paginationData = (new \src\Models\Post())->getPosts($page, $limit, $query);
            $posts = $paginationData['posts'];
            $postsCnt = $paginationData['postsCnt'];
            $pagesCnt = ceil($postsCnt / $limit);

            $cacheHelper->writeCache([
                'posts' => $posts,
                'pagesCnt' => $pagesCnt
            ]);
        }

        // Generate pagination links
        $paginationLinks = $this->generatePaginationLinks($pagesCnt, $query);
        return $this->render($response, 'posts/index.php', [
            'posts' => $posts,
            'paginationLinks' => $paginationLinks,
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

        $postsCnt = $paginationData['postsCnt'] ?? [];
        $pagesCnt = ceil($postsCnt / $limit);

        $paginationLinks = $this->generatePaginationLinks($pagesCnt);

        return $this->render($response, 'posts/my_posts.php', [
            'posts' => $paginationData['posts'],
            'pagesCnt' => $pagesCnt,
            'paginationLinks' => $paginationLinks,
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

            try {
                (new Post())
                    ->setTitle($title)
                    ->setContent($content)
                    ->setUserID($_SESSION['user_id'])
                    ->create();

                $this->invalidateAllCache();
                ErrorFlow::addError('post_error', "Success create");
            } catch (\Exception $e) {
                Logger::error("Error on post creation:" . $e->getMessage());
                ErrorFlow::addError('post_error', "An error occurred during post creation.");
            }

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

            try {
                // Update the post
                (new Post())
                    ->setID($postId)
                    ->setTitle($title)
                    ->setContent($content)
                    ->update();

                $this->invalidateAllCache();
                ErrorFlow::addError('post_error', "Success update");
            } catch (\Exception $e) {
                Logger::error("Error on post update:" . $e->getMessage());
                ErrorFlow::addError('post_error', "An error occurred during post update.");
            }
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

        try {
            if (!(new Post())->delete($postId)) {
                ErrorFlow::addError('post_error', "Failed to delete post");
                Logger::error("Failed to delete post with ID: " . $postId);
                return $response->withHeader('Location', '/my_posts')->withStatus(302);
            }

            ErrorFlow::addError('post_error', "Post successful deleted");
            $this->invalidateAllCache();
        } catch (\Exception $e) {
            Logger::error("Error deleting post: " . $e->getMessage()); // Log critical errors
            ErrorFlow::addError('post_error', "An error occurred while deleting the post.");
        }

        return $response->withHeader('Location', '/my_posts')->withStatus(302);
    }

    /**
     * Generate pagination links.
     */
    private function generatePaginationLinks(int $pagesCnt, string $query = ''): string
    {
        $paginationLinks = '';
        for ($i = 1; $i <= $pagesCnt; $i++) {
            $paginationLinks .= '<a href="/posts?page=' . $i . '&query=' . urlencode($query) . '" class="page-link d-inline">' . $i . '</a> ';
        }
        return $paginationLinks;
    }

    private function invalidateAllCache(): void
    {
        $cacheHelper = new \helpers\CacheHelper('');
        $cacheHelper->clearAllCaches();
    }
}
