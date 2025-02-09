<?php

namespace core;

use helpers\Logger;
use PDO;
use PDOException;
class Database {
    /**
     * @var Database|null
     */
    private static ?Database $instance = null;

    /**
     * @var PDO
     */
    private PDO $pdo;

    private function __construct() {
        $cfg = include(__DIR__ . '/../config/database.php');
        $connectionRow = 'mysql:host=' . $cfg['host'] . ';dbname=' . $cfg['database'] . ';charset=' . $cfg['charset'];

        try {
            $this->pdo = new PDO($connectionRow, $cfg['username'], $cfg['password']);
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            Logger::error($e->getMessage());
        }
    }

    /**
     * @return Database|null
     */
    public static function getInstance(): ?Database
    {
        if (self::$instance === null) {
            $className = __CLASS__;
            self::$instance = new $className;
        }

        return self::$instance;
    }

    /**
     * @return PDO
     */
    public function getPdo(): PDO
    {
        return $this->pdo;
    }
}