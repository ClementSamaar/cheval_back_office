<?php

require __DIR__ . '/includes.php';

$pdo = new PDOConnect($_ENV['DB_CS_COMMUNITY_MANAGER_USERNAME'], $_ENV['DB_CS_COMMUNITY_MANAGER_PASS']);
$pdo->connect();

$date = date('[d-m-y/H:m:s]' . PHP_EOL);
$pdo->getPdo()->exec('LOCK TABLES');

for ($i = 2; $i < sizeof($argv); $i++) {
    $repair = $pdo->getPdo()->prepare('REPAIR TABLE ' . $argv[$i] . ' ' . $argv[1]);
    $repair->execute();
    $repair = $repair->fetch(PDO::FETCH_ASSOC);
    $toLog = $date . ' ' . $repair['table'] . ' - Status : ' . $repair['msg_text'] . '\n';
    file_put_contents(__DIR__ . '/repair.log', $toLog, FILE_APPEND);
}

$pdo->getPdo()->exec('UNLOCK TABLES');