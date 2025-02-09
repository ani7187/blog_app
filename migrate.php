<?php
require_once __DIR__ . '/vendor/autoload.php';

use core\Database;

$db = Database::getInstance();
$pdo = $db->getPdo();

$migrationFiles = glob('migrations/*.sql');

foreach ($migrationFiles as $migration) {
    $sql = file_get_contents($migration);

    try {
        echo "Running migration: $migration\n";
        $pdo->exec($sql);
        echo "Migration $migration completed.";
    } catch (PDOException $e) {
        echo "Migration $migration failed.";
        \helpers\Logger::error($e->getMessage());
        return;
    }

}
echo "Migrations completed.\n";
