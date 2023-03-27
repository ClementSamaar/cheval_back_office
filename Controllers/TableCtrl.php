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
                echo '<li><a href="?ctrl=table&action=selectRows&table=' . $table . '&page=1">' . $table . '</a></li>';
            }
            echo '</ul>';
        }
        else echo '<p>Cet utilisateur n\'a accès à aucune table</p>';
    }

    private function showTableAction(array $privileges, ?array $attributes, ?array $rows, ?string $name, ?int $rowAmount, ?int $page) {
        if (!empty($privileges) and in_array('Select', $privileges)) $select = true;
        else $select = false;
        $A_content = [
            'title' => ucfirst($_GET['table']),
            'bodyView' => 'table/table',
            'bodyContent' => [
                'select'          => $select,
                'userPrivileges'  => $privileges,
                'tableAttributes' => $attributes,
                'tableRows'       => $rows,
                'table'           => $name,
                'rowAmount'       => $rowAmount,
                'page'            => $page
            ]
        ];

        View::show('common/template', $A_content);
    }

    public function selectRowsAction() {
        $user = new User($_ENV[$_SESSION['envUsernameVar']]);
        $user->fetchGrants();
        if (!empty($user->getPrivileges()) and in_array('Select', $user->getPrivileges())){
            if (isset($_GET['table']) and in_array($_GET['table'], $user->getGrantedTables())) {
                $pdo = new PDOConnect($_ENV[$_SESSION['envUsernameVar']], $_ENV[$_SESSION['envPasswordVar']]);
                $pdo->connect();
                $table = new Table($pdo, $_GET['table']);
                $table->selectAll($pdo, 10, $_GET['page']);
            }
            else {
                header('Location: ?ctrl=table');
                exit();
            }
        }
        $this->showTableAction(
            $user->getPrivileges(),
            $table->getAttributes() ?? null,
            $table->getRows() ?? null,
            $_GET['table'] ?? null,
            $table->getRowAmount() ?? null,
            $_GET['page'] ?? 1
        );
    }

    public function insertRowAction() {

    }

    public function updateRowAction() {
        $pdo = new PDOConnect($_ENV['DB_CS_COMMUNITY_MANAGER_USERNAME'], $_ENV['DB_CS_COMMUNITY_MANAGER_PASS']);
        $pdo->connect();
        $user = new User($_ENV[$_SESSION['envUsernameVar']]);
        $user->fetchGrants();
        if (in_array('Update', $user->getPrivileges())) {

        }
        else echo 'Permission denied !';
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