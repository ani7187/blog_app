<?php

namespace src\Models;

use config\Constants;
use core\Model;
use PDO;

class Post extends Model
{
    /** @var int */
    private int $Id;

    /** @var string */
    private string $title;

    /** @var string */
    private string $content;

    /** @var int */
    private int $userID;

    /**
     * @param int $Id
     * @return $this
     */
    public function setId(int $Id): self
    {
        $this->Id = $Id;
        return $this;
    }

    /**
     * @param string $title
     * @return $this
     */
    public function setTitle(string $title): self
    {
        $this->title = $title;
        return $this;
    }

    /**
     * @param string $content
     * @return $this
     */
    public function setContent(string $content): self
    {
        $this->content = $content;
        return $this;
    }

    /**
     * @param int $userID
     * @return $this
     */
    public function setUserID(int $userID): self
    {
        $this->userID = $userID;
        return $this;
    }

    /**
     * @return bool
     */
    public function create(): bool
    {
        $stmt = $this->pdo->prepare("INSERT INTO posts (title, content, user_id) 
                                     VALUES (:title, :content, :user_id)");

        $stmt->bindValue(':title', $this->title);
        $stmt->bindValue(':content', $this->content);
        $stmt->bindValue(':user_id', $this->userID, PDO::PARAM_INT);

        return $stmt->execute();
    }

    /**
     * @return bool
     */
    public function update(): bool
    {
        $stmt = $this->pdo->prepare("UPDATE posts SET title = :title, content = :content WHERE id = :id");

        $stmt->bindValue(':title', $this->title);
        $stmt->bindValue(':content', $this->content);
        $stmt->bindValue(':id', $this->Id, PDO::PARAM_INT);

        return $stmt->execute();
    }

    /**
     * @param int $Id
     * @return array|null
     */
    public function getByID(int $Id): ?array
    {
        $stmt = $this->pdo->prepare("
            SELECT posts.*, user.username, user.email
            FROM posts
            INNER JOIN user ON (posts.user_id = user.id AND posts.del_status = :del_status)
            WHERE posts.id = :id
        ");

        $stmt->bindValue(':id', $Id, PDO::PARAM_INT);
        $stmt->bindValue(':del_status', Constants::DEL_STATUS_ACTIVE, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    /**
     * @param int $userId
     * @param int $page
     * @param int $limit
     * @return array
     */
    public function getUserPosts(int $userId, int $page = 1, int $limit = 5): array
    {
        $offset = ($page - 1) * $limit;

        // Query for getting posts with pagination
        $stmt = $this->pdo->prepare("
            SELECT posts.*, user.username, user.email
            FROM posts
            INNER JOIN user ON (posts.del_status = :del_status AND posts.user_id = user.id)
            WHERE posts.user_id = :user_id
            ORDER BY posts.created_at DESC
            LIMIT :limit OFFSET :offset
        ");

        $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->bindValue(':del_status', Constants::DEL_STATUS_ACTIVE, PDO::PARAM_INT);

        $stmt->execute();
        $posts = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Get the total number of posts (for pagination)
        $countStmt = $this->pdo->prepare("
            SELECT COUNT(*) FROM posts
            WHERE posts.user_id = :user_id AND posts.del_status = :del_status
        ");

        $countStmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
        $countStmt->bindValue(':del_status', Constants::DEL_STATUS_ACTIVE, PDO::PARAM_INT);
        $countStmt->execute();
        $postsCnt = $countStmt->fetchColumn();

        return [
            'posts' => $posts,
            'postsCnt' => $postsCnt
        ];
    }

    /**
     * @param int $page
     * @param int $limit
     * @param string $query
     * @return array
     */
    public function getPosts(int $page = 1, int $limit = 5, string $query = ""): array
    {
        $offset = ($page - 1) * $limit;

        // Get with pagination and search functionality
        $stmt = $this->pdo->prepare("
            SELECT posts.*, user.username, user.email
            FROM posts
            JOIN user ON ( posts.user_id = user.id AND posts.del_status = :del_status )
            WHERE posts.title LIKE :search_term OR posts.content LIKE :search_term
            ORDER BY posts.created_at DESC
            LIMIT :limit OFFSET :offset
        ");

        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->bindValue(':search_term', '%' . $query . '%', PDO::PARAM_STR);
        $stmt->bindValue(':del_status', Constants::DEL_STATUS_ACTIVE, PDO::PARAM_INT);

        $stmt->execute();
        $posts = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Get the total number of posts
        $countStmt = $this->pdo->prepare("
            SELECT COUNT(*) 
            FROM posts
            JOIN user ON (posts.user_id = user.id AND posts.del_status = :del_status)
            WHERE posts.title LIKE :search_term OR posts.content LIKE :search_term
        ");

        $countStmt->bindValue(':search_term', '%' . $query . '%');
        $countStmt->bindValue(':del_status', Constants::DEL_STATUS_ACTIVE, PDO::PARAM_INT);
        $countStmt->execute();
        $postsCnt = $countStmt->fetchColumn();

        return [
            'posts' => $posts,
            'postsCnt' => $postsCnt
        ];
    }

    /**
     * @param int $postID
     * @return bool
     * @throws \Exception
     */
    public function delete(int $postID): bool
    {
        $this->pdo->beginTransaction();

        try {
            $stmt = $this->pdo->prepare("UPDATE posts SET del_status = :del_status, del_date = :del_date WHERE id = :id");
            $stmt->bindValue(':id', $postID, PDO::PARAM_INT);
            $stmt->bindValue(':del_status', Constants::DEL_STATUS_DELETED, PDO::PARAM_INT);
            $stmt->bindValue(':del_date', date('Y-m-d H:i:s'));

            $stmt->execute();

            // Delete all comments associated with the post
            (new Comment())->deletePostComments($postID);

            $this->pdo->commit();

            return true;
        } catch (\PDOException $e) {
            $this->pdo->rollBack();
            throw new \Exception($e->getMessage());
        }
    }
}
