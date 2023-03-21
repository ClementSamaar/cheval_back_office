<?php
require 'Core/Autoload.php';
require 'vendor/autoload.php';
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

session_start();
if (!isset($_SESSION['username'])){
    unset($_SESSION);
    header('?ctrl=log&action=displayLogin');
}

$ctrlName = $_GET['ctrl'] ?? null;
$actionName = $_GET['action'] ?? null;

View::openBuffer();

$ctrl = new Controller($ctrlName, $actionName);
$ctrl->callCtrl();

$S_View = View::getBufferContent();
echo $S_View;

$pdo = new PDOConnect('cs_community_manager', 'password');
$pdo->connect();
$table = new Table($pdo, 'joueur');
$table->selectAll($pdo, 10, 1);
$table->updateRow($pdo, 0, [null, 'karla', 'karla@karl.com', null, 'Karla', 'SAMMAR', 'femme', null, null, null, null, null, null, null, null, null]);