<?php

class LogCtrl
{
    public function displayLoginAction() {
        $A_content = [
            'title' => 'Login',
            'bodyView' => 'log/login',
            'bodyContent' => null
        ];

        View::show('common/template', $A_content);
    }

    public function loginAction() {
        $pdo = new PDOConnect($_ENV['DB_ROOT_USERNAME'], $_ENV['DB_ROOT_PASS']);
        $query = 'SELECT authentication_string 
                FROM user 
                WHERE User=' . $pdo->getPdo()->quote($_POST['username']) . ' 
                AND authentication_string=PASSWORD(' . $pdo->getPdo()->quote($_POST['password']) . ')';
        $pdo->connect();
        $credentials = $pdo->getPdo()->prepare($query);
        $credentials->execute();
        $credentials = $credentials->fetch();

        if (isset($credentials)) {
            $envUsernameVar = 'DB_' . strtoupper($_POST['username']) . '_USERNAME';
            $envPasswordVar = 'DB_' . strtoupper($_POST['username']) . '_PASSWORD';
            if (!isset($_ENV[$envUsernameVar])){
                putenv($envUsernameVar . '=' . $_POST['username']);
            }

            if (!isset($_ENV[$envPasswordVar])){
                putenv($envPasswordVar . '=' . $_POST['password']);
            }

            $_SESSION['username'] = $_POST['username'];
        }
    }

    public function logoutAction() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if ($_SESSION['username']) {
            session_destroy();
            header('Location: login.php');
        }
    }
}