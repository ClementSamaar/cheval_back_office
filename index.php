<?php
require 'Core/Autoload.php';
require 'vendor/autoload.php';
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

$ctrlName = $_GET['ctrl'] ?? null;
$actionName = $_GET['action'] ?? null;

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['envUsernameVar']) and $ctrlName != 'log' and ($actionName != 'displayLogin' or $actionName != 'login')) {
    header('Location: ?ctrl=log&action=displayLogin');
    exit();
}

else if (isset($_SESSION['envUsernameVar']) and $ctrlName == 'log' and ($actionName == 'displayLogin' or $actionName == 'login')) {
    header('Location: ?ctrl=table');
    exit();
}

View::openBuffer();

$ctrl = new Controller($ctrlName, $actionName);
$ctrl->callCtrl();

$S_View = View::getBufferContent();
echo $S_View;