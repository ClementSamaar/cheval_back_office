<?php

require __DIR__ . '/includes.php';

$pdo = new PDOConnect($_ENV['DB_CS_COMMUNITY_MANAGER_USERNAME'], $_ENV['DB_CS_COMMUNITY_MANAGER_PASS']);
$pdo->connect();

$tables = $pdo->getPdo()->query('SHOW TABLES')->fetchAll(PDO::FETCH_COLUMN);

$toLog = $toLog = date('d/M/y H:m:s');

foreach ($tables as $table) {
    $check = $pdo->getPdo()->prepare('CHECK TABLE ' . $table);
    $check->execute();
    $check = $check->fetch(PDO::FETCH_ASSOC);
    $toLog .= $table . ' - Status : ' . $check['Msg_text'] . '\n';
}

echo $toLog;
