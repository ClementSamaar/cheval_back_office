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

    private function showTableAction(array $privileges, ?array $attributes, ?array $rows, ?string $name, ?string $pk, ?int $rowAmount, ?int $page) {
        if (!empty($privileges) and in_array('Select', $privileges)) $select = true;
        else $select = false;
        $A_content = [
            'title' => ucfirst($name),
            'bodyView' => 'table/table',
            'bodyContent' => [
                'select'          => $select,
                'userPrivileges'  => $privileges,
                'tableAttributes' => $attributes,
                'tableRows'       => $rows,
                'tableName'       => $name,
                'pk'              => $pk,
                'rowAmount'       => $rowAmount,
                'page'            => $page
            ]
        ];

        View::show('common/template', $A_content);
    }

    public function selectRowsAction() {
        $user = new User($_ENV[$_SESSION['envUsernameVar']]);
        $user->fetchGrants();
        $pdo = new PDOConnect($_ENV[$_SESSION['envUsernameVar']], $_ENV[$_SESSION['envPasswordVar']]);
        $pdo->connect();
        $table = new Table($pdo, $_GET['table']);
        if (!empty($user->getPrivileges()) and in_array('Select', $user->getPrivileges())){
            if (isset($_GET['table']) and in_array($_GET['table'], $user->getGrantedTables())) {
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
            $table->getPk(),
            $table->getRowAmount() ?? null,
            $_GET['page'] ?? 1
        );
    }

    public function insertRowAction() {
        $pdo = new PDOConnect($_ENV[$_SESSION['envUsernameVar']], $_ENV[$_SESSION['envPasswordVar']]);
        $pdo->connect();
        $user = new User($_ENV[$_SESSION['envUsernameVar']]);
        $user->fetchGrants();
        if (in_array('Insert', $user->getPrivileges())) {
            $values = $_POST;
            $table = new Table($pdo, $_GET['table']);
            $table->insertRow($pdo);
        }
        else echo 'Permission denied !';
    }

    public function updateRowAction() {
        $pdo = new PDOConnect($_ENV[$_SESSION['envUsernameVar']], $_ENV[$_SESSION['envPasswordVar']]);
        $pdo->connect();
        $user = new User($_ENV[$_SESSION['envUsernameVar']]);
        $user->fetchGrants();
        if (in_array('Update', $user->getPrivileges())) {

        }
        else echo 'Permission denied !';
    }

    public function deleteRowAction() {
        $pdo = new PDOConnect($_ENV[$_SESSION['envUsernameVar']], $_ENV[$_SESSION['envPasswordVar']]);
        $pdo->connect();
        $user = new User($_ENV[$_SESSION['envUsernameVar']]);
        $user->fetchGrants();
        if (in_array('Delete', $user->getPrivileges())) {
            $table = new Table($pdo, $_GET['table']);
            $table->deleteRow($pdo, $_GET['id']);
            header('Location: ?ctrl=table&action=selectRows&table=' . $_GET['table'] . '&page=1');
            exit();
        }
        else echo 'Permission denied !';

    }
}