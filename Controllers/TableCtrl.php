<?php

class TableCtrl
{
    public function defaultAction() {
        $user = new User('cs_community_manager');
        $user->fetchGrants();
        echo '<h2>Table list</h2><ul>';
        foreach ($user->getGrantedTables() as $table){
            echo '<li><a href="?ctrl=table&action=showTable&table=' . $table . '&page=1">' . $table . '</a></li>';
        }
    }

    public function showTableAction() {
        $user = new User('cs_community_manager');
        $user->fetchGrants();
        if (in_array('Select', $user->getPrivileges())){
            if (isset($_GET['table'])) {
                $pdo = new PDOConnect($_ENV['DB_CS_COMMUNITY_MANAGER_USERNAME'], $_ENV['DB_CS_COMMUNITY_MANAGER_PASS']);
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
        $user = new User('cs_community_manager');
        $user->fetchGrants();
        if (in_array('Delete', $user->getPrivileges())) {
            $table = new Table($pdo, $_GET['table']);
            $table->deleteRow($pdo, $_GET['id']);
        }
        else echo 'Permission denied !';
    }
}