<?php

class UserCtrl
{
    public function defaultAction() {
        $username = $_ENV[$_SESSION['envUsernameVar']];
        $user = new User($username);
        $user->fetchGrants();
        $A_content = [
            'title' => 'Compte ' . $username,
            'bodyView' => 'user/profile',
            'bodyContent' => [
                'userPrivileges' => $user->getPrivileges(),
                'userTable'      => $user->getGrantedTables()
            ]
        ];

        View::show('common/template', $A_content);
    }
}