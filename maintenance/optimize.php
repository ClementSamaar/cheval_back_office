<?php

require __DIR__ . 'includes.php';

$pdo = new PDOConnect($_ENV['DB_CS_COMMUNITY_MANAGER_USERNAME'], $_ENV['DB_CS_COMMUNITY_MANAGER_PASS']);
$pdo->connect();

//$toLog = date('d/M/y H:m:s');
$tables = $pdo->getPdo()->query('SHOW TABLES')->fetchAll(PDO::FETCH_COLUMN);
$pdo->getPdo()->exec('LOCK TABLES');

foreach ($tables as $table) {
    $optimize = $pdo->getPdo()->prepare('OPTIMIZE TABLE ' . $table);
    $optimize->execute();
    $optimize = $optimize->fetch(PDO::FETCH_ASSOC);
    //$toLog .= $repair['table'] . ' - Status : ' . $repair['msg_text'] . '\n';
}

$pdo->getPdo()->exec('UNLOCK TABLES');