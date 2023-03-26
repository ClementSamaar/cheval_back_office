<?php

class TableCtrl
{
    public function defaultAction() {
        $user = new User($_ENV[$_SESSION['envUsernameVar']]);
        $user->fetchGrants();
        echo '<h2>Table list</h2>';
        if (!empty($user->getGrantedTables())){
            echo '<ul>';
            foreach ($user->getGrantedTables() as $table){
                echo '<li><a href="?ctrl=table&action=showTable&table=' . $table . '&page=1">' . $table . '</a></li>';
            }
            echo '</ul>';
        }
        else echo '<p>Cet utilisateur n\'a accès à aucune table</p>';
    }

    public function showTableAction() {
        $user = new User($_ENV[$_SESSION['envUsernameVar']]);
        $user->fetchGrants();
        if (!empty($user->getPrivileges()) and in_array('Select', $user->getPrivileges())){
            if (isset($_GET['table']) and in_array($_GET['table'], $user->getGrantedTables())) {
                $pdo = new PDOConnect($_ENV[$_SESSION['envUsernameVar']], $_ENV[$_SESSION['envPasswordVar']]);
                $pdo->connect();
                $table = new Table($pdo, $_GET['table']);
                $table->selectAll($pdo, 10, $_GET['page']);

                $A_content = [
                    'title' => ucfirst($_GET['table']),
                    'bodyView' => 'table',
                    'bodyContent' => [
                        'userPrivileges'  => $user->getPrivileges(),
                        'tableAttributes' => $table->getAttributes(),
                        'tableRows'       => $table->getRows(),
                        'table'           => $table->getName(),
                        'rowAmount'       => $table->getRowAmount(),
                        'page'            => $_GET['page']
                    ]
                ];

                View::show('common/template', $A_content);
            }
            else {
                header('Location: ?ctrl=table');
                exit();
            }
        }
        else {
            $A_content = [
                'title' => ucfirst($_GET['table']),
                'bodyView' => 'table',
                'bodyContent' => [
                    'select' => false
                ]
            ];

            View::show('common/template', $A_content);
        }
    }

    public function updateRowAction() {

    }

    public function deleteRowAction() {
        $pdo = new PDOConnect($_ENV['DB_CS_COMMUNITY_MANAGER_USERNAME'], $_ENV['DB_CS_COMMUNITY_MANAGER_PASS']);
        $pdo->connect();
        $user = new User($_ENV[$_SESSION['envUsernameVar']]);
        $user->fetchGrants();
        if (in_array('Delete', $user->getPrivileges())) {
            $table = new Table($pdo, $_GET['table']);
            $table->deleteRow($pdo, $_GET['id']);
        }
        else echo 'Permission denied !';
    }
}