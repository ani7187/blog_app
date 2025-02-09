<?php

namespace src\Models;

use config\Constants;
use core\Model;
use PDO;

class Comment extends Model
{
    /** @var int */
    private int $postId;

    /** @var int */
    private int $userId;

    /** @var string */
    private string $content;

    /**
     * @param string $content
     * @return $this
     */
    public function setContent(string $content): Comment
    {
        $this->content = $content;
        return $this;
    }

    /**
     * @param int $userId
     * @return $this
     */
    public function setUserId(int $userId): Comment
    {
        $this->userId = $userId;
        return $this;
    }

    /**
     * @param int $postId
     * @return $this
     */
    public function setPostId(int $postId): Comment
    {
        $this->postId = $postId;
        return $this;
    }

    /**
     * Fetch comments for a post with pagination.
     */
    public function getComments(int $postId, int $limit, int $offset): array
    {
        $stmt = $this->pdo->prepare("
            SELECT * FROM comments 
            WHERE del_status = :del_status
            AND post_id = :post_id 
            ORDER BY created_at 
            DESC LIMIT :limit OFFSET :offset
        ");

        $stmt->bindValue(':post_id', $postId, PDO::PARAM_INT);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->bindValue(':del_status', Constants::DEL_STATUS_ACTIVE, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Count total comments for a post.
     */
    public function countComments(int $postId): int
    {
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM comments WHERE del_status = :del_status AND post_id = :post_id");
        $stmt->bindValue(':post_id', $postId, PDO::PARAM_INT);
        $stmt->bindValue(':del_status', Constants::DEL_STATUS_ACTIVE, PDO::PARAM_INT);

        $stmt->execute();

        return (int) $stmt->fetchColumn();
    }

    /**
     * Create a new comment.
     */
    public function create(): bool
    {
        $stmt = $this->pdo->prepare("
            INSERT INTO comments (post_id, user_id, content) 
            VALUES (:post_id, :user_id, :content)
        ");

        $stmt->bindValue(':post_id', $this->postId, PDO::PARAM_INT);
        $stmt->bindValue(':user_id', $this->userId, PDO::PARAM_INT);
        $stmt->bindValue(':content', $this->content);

        return $stmt->execute();
    }

    /**
     * Delete a comment.
     */
    public function delete(int $commentId): bool
    {
        $stmt = $this->pdo->prepare("UPDATE comments SET del_status = :del_status, del_date = :del_date WHERE id = :id");

        $stmt->bindValue(':id', $commentId, PDO::PARAM_INT);
        $stmt->bindValue(':del_status', Constants::DEL_STATUS_DELETED, PDO::PARAM_INT);
        $stmt->bindValue(':del_date', date('Y-m-d H:i:s'));

        return $stmt->execute();
    }

    /**
     * Delete a comment.
     */
    public function deletePostComments(int $postId): bool
    {
        $stmt = $this->pdo->prepare("UPDATE comments SET del_status = :del_status, del_date = :del_date WHERE post_id = :post_id");

        $stmt->bindValue(':post_id', $postId, PDO::PARAM_INT);
        $stmt->bindValue(':del_status', Constants::DEL_STATUS_DELETED, PDO::PARAM_INT);
        $stmt->bindValue(':del_date', date('Y-m-d H:i:s'));

        return $stmt->execute();
    }
}