<?php
require_once __DIR__ . '/vendor/autoload.php';

use core\Database;
use helpers\seeder\SeederRunner;

$db = Database::getInstance();
$pdo = $db->getPdo();

$seederRunner = new SeederRunner($pdo);
$seederRunner->run();

echo "Seeder completed.\n";
