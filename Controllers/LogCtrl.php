<?php

class LogCtrl
{
    public function displayLoginAction() {
        $error = $_GET['error'] ?? null;
        $A_content = [
            'title' => 'Login',
            'bodyView' => 'log/login',
            'bodyContent' => [
                'error' => $error
            ]
        ];

        View::show('common/template', $A_content);
    }

    public function loginAction() {
        if (isset($_POST['submit'])) {
            $pdo = new PDOConnect($_ENV['DB_ROOT_USERNAME'], $_ENV['DB_ROOT_PASS']);
            $pdo->connect();
            $credentials = $pdo->getPdo()->prepare('
                SELECT authentication_string 
                FROM user 
                WHERE User=:username
                AND authentication_string=PASSWORD(:password)');
            $credentials->bindParam(':username', $_POST['username']);
            $credentials->bindParam(':password', $_POST['password']);
            $credentials->execute();
            $credentials = $credentials->fetch();
            if (!isset($credentials) or !$credentials) {
                echo 'ERROR';
                header('Location: ?ctrl=log&action=displayLogin&error=1');
            }
            else {
                var_dump($credentials);
                $envUsernameVar = 'DB_' . strtoupper($_POST['username']) . '_USERNAME';
                $envPasswordVar = 'DB_' . strtoupper($_POST['username']) . '_PASS';
                if (!isset($_ENV[$envUsernameVar])){
                    putenv($envUsernameVar . '=' . $_POST['username']);
                }

                if (!isset($_ENV[$envPasswordVar])){
                    putenv($envPasswordVar . '=' . $_POST['password']);
                }

                $_SESSION['envUsernameVar'] = $envUsernameVar;
                $_SESSION['envPasswordVar'] = $envPasswordVar;
                header('Location: ?ctrl=table');
            }
            exit();
        }
    }

    public function logoutAction() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        session_destroy();
        header('Location: ?ctrl=log&action=displayLogin');
    }
}