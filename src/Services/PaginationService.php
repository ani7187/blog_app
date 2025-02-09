<?php

namespace src\Services;

use src\Models\Post;

class PaginationService
{
    private Post $postModel;

    public function __construct(Post $postModel)
    {
        $this->postModel = $postModel;
    }

    /**
     * @param int $page
     * @param int $limit
     * @param string $query
     * @return array
     */
    public function getPaginatedPosts(int $page, int $limit, string $query = ""): array
    {
        $offset = ($page - 1) * $limit;
        $posts = $this->postModel->getPostsWithPagination($query, $offset, $limit);
        $totalPosts = $this->postModel->getTotalPosts($query);
        $totalPages = ceil($totalPosts / $limit);

        return [
            'posts' => $posts,
            'totalPosts' => $totalPosts,
            'totalPages' => $totalPages
        ];
    }

    /**
     * @return array{posts: mixed, totalPosts: mixed, totalPages: float}
     */
    public function getUserPaginatedPosts(int $userId, int $page, int $limit): array
    {
        $offset = ($page - 1) * $limit;
        $posts = $this->postModel->getUserPostsWithPagination($userId, $offset, $limit);
        $totalPosts = $this->postModel->getTotalUserPosts($userId);
        $totalPages = ceil($totalPosts / $limit);

        return [
            'posts' => $posts,
            'totalPosts' => $totalPosts,
            'totalPages' => $totalPages
        ];
    }
}