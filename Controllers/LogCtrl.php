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
        $pdo->connect();
        $prep = $pdo->getPdo()->prepare('SELECT authentication_string FROM user WHERE User=' . $pdo->getPdo()->quote($_POST['username']));
        $prep->execute();
        $binaryPass = pack('H*', bin2hex($_POST['password']));
        echo hash('sha256', $binaryPass) . '<br>';
        echo $prep->fetch()[0];

        $envUsernameVar = 'DB_' . strtoupper($_POST['username']) . '_USERNAME';
        $envPasswordVar = 'DB_' . strtoupper($_POST['username']) . '_PASSWORD';
        if (!isset($_ENV[$envUsernameVar])){
            putenv($envUsernameVar . '=' . $_POST['username']);
        }

        if (!isset($_ENV[$envPasswordVar])){
            putenv($envPasswordVar . '=' . $_POST['password']);
        }
    }
}