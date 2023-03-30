<?php

require __DIR__ . '/includes.php';

$pdo = new PDOConnect($_ENV['DB_CS_DEV_USERNAME'], $_ENV['DB_CS_DEV_PASS']);
$pdo->connect();

$tables = $pdo->getPdo()->query('SHOW TABLES')->fetchAll(PDO::FETCH_COLUMN);

$toLog = $toLog = date('[d-m-y/H:m:s]' . PHP_EOL);

foreach ($tables as $table) {
    $check = $pdo->getPdo()->prepare('CHECK TABLE ' . $table);
    $check->execute();
    $check = $check->fetch(PDO::FETCH_ASSOC);
    $toLog .= $table . ' - Status : ' . $check['Msg_text'] . PHP_EOL;
}

file_put_contents(__DIR__ . '/check.log', $toLog);
