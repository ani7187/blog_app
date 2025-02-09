<?php

namespace src\Models;

use config\Constants;
use core\Model;
use PDO;

class User extends Model
{
    /** @var int */
    private int $Id;

    /** @var string */
    private string $username;

    /** @var string */
    private string $password;
    /** @var string */
    private string $email;

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
     * @param string $username
     * @return $this
     */
    public function setUsername(string $username): self
    {
        $this->username = $username;
        return $this;
    }

    /**
     * @param string $password
     * @return $this
     */
    public function setPassword(string $password): self
    {
        $this->password = password_hash($password, PASSWORD_BCRYPT);
        return $this;
    }

    /**
     * @param string $email
     * @return $this
     */
    public function setEmail(string $email): self
    {
        $this->email = $email;
        return $this;
    }

    /**
     * @return bool
     */
    public function create(): bool
    {
        $stmt = $this->pdo->prepare("INSERT INTO user (username, password, email) 
                                     VALUES (:username, :password, :email)");

        $stmt->bindValue(':username', $this->username);
        $stmt->bindValue(':password', $this->password);
        $stmt->bindValue(':email', $this->email);

        return $stmt->execute();
    }

    /**
     * @return bool
     */
    public function update(): bool
    {
        $stmt = $this->pdo->prepare("UPDATE user SET username = :username, password = :password, 
                                     email = :email WHERE id = :id");

        $stmt->bindValue(':id', $this->Id, PDO::PARAM_INT);
        $stmt->bindValue(':username', $this->username);
        $stmt->bindValue(':password', $this->password);
        $stmt->bindValue(':email', $this->email);

        return $stmt->execute();
    }

    /**
     * @param string $email
     * @return array|null
     */
    public function getByEmail(string $email): ?array
    {
        $stmt = $this->pdo->prepare("SELECT * FROM user WHERE del_status = 0 AND email = :email");
        $stmt->bindValue(':email', $email);

        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    /**
     * Get user by email or username.
     *
     * @param string $email
     * @param string $username
     * @return array|null
     */
    public function getByEmailOrUsername(string $email, string $username): ?array
    {
        $stmt = $this->pdo->prepare("
            SELECT * FROM user WHERE del_status = 0 AND (email = :email OR username = :username)
        ");

        $stmt->bindValue(':email', $email);
        $stmt->bindValue(':username', $username);

        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    /**
     * @param int $userId
     * @return bool
     */
    public function delete(int $userId): bool
    {
        $stmt = $this->pdo->prepare("UPDATE user SET del_status = :del_status WHERE id = :id");
        $stmt->bindValue(':id', $userId, PDO::PARAM_INT);
        $stmt->bindValue(':del_status', Constants::DEL_STATUS_DELETED, PDO::PARAM_INT);

        return $stmt->execute();
    }
}
