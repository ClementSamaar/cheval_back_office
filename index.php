<?php
require 'Core/Autoload.php';
require 'vendor/autoload.php';
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

/*if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['username'])) {
    header('Location: ?ctrl=log&action=displayLogin');
}*/

$ctrlName = $_GET['ctrl'] ?? null;
$actionName = $_GET['action'] ?? null;

View::openBuffer();

$ctrl = new Controller($ctrlName, $actionName);
$ctrl->callCtrl();

$S_View = View::getBufferContent();
echo $S_View;