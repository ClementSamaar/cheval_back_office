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
                echo '<li><a href="?ctrl=table&action=selectAllRows&table=' . $table . '&page=1">' . $table . '</a></li>';
            }
            echo '</ul>';
        }
        else echo '<p>Cet utilisateur n\'a accès à aucune table</p>';
    }

    private function showTableAction(array $privileges, ?array $attributes, ?array $rows, ?string $name, ?string $pk, ?int $rowAmount, ?int $limit, ?int $page) {
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
                'limit'           => $limit,
                'page'            => $page
            ]
        ];

        View::show('common/template', $A_content);
    }

    public function selectAllRowsAction() {
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
            10,
            $_GET['page'] ?? 1
        );
    }

    /*public function selectRowsAction() {
        $user = new User($_ENV[$_SESSION['envUsernameVar']]);
        $user->fetchGrants();
        $pdo = new PDOConnect($_ENV[$_SESSION['envUsernameVar']], $_ENV[$_SESSION['envPasswordVar']]);
        $pdo->connect();
        $table = new Table($pdo, $_GET['table']);
        if (!empty($user->getPrivileges()) and in_array('Select', $user->getPrivileges())){
            if (isset($_GET['table']) and in_array($_GET['table'], $user->getGrantedTables())) {
                $table->selectByAttributes($pdo, $_POST['whereClause'],10, $_GET['page']);
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
    }*/

    public function orderRowsAction() {
        $pdo = new PDOConnect($_ENV[$_SESSION['envUsernameVar']], $_ENV[$_SESSION['envPasswordVar']]);
        $pdo->connect();
        $user = new User($_ENV[$_SESSION['envUsernameVar']]);
        $user->fetchGrants();
        $table = new Table($pdo, $_GET['table']);
        if (!empty($user->getPrivileges()) and in_array('Select', $user->getPrivileges())){
            if (isset($_GET['table']) and in_array($_GET['table'], $user->getGrantedTables())) {
                $table->orderBy($pdo, $_GET['attribute'], $_GET['order'], 10, $_GET['page']);
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
            header('Location: ?ctrl=table&action=selectRows&table=' . $_GET['table'] . '&page=1');
            exit();
        }
        else echo 'Permission denied !';
    }

    public function showUpdateFormAction() {
        $pdo = new PDOConnect($_ENV[$_SESSION['envUsernameVar']], $_ENV[$_SESSION['envPasswordVar']]);
        $pdo->connect();
        $table = new Table($pdo, $_GET['table']);
        $row = $table->selectById($pdo, $_GET['id']);
        echo '<form method="POST" action="?ctrl=table&action=updateRow&table=' . $table->getName() . '&id=' . $_GET['id'] . '" class="form-container">';
        foreach ($table->getAttributes() as $attribute) {
            if ($attribute['COLUMN_NAME'] == $table->getPk() and $attribute['DATA_TYPE'] == 'bigint' or $attribute['DATA_TYPE'] == 'int')
                continue;
            elseif ($attribute['DATA_TYPE'] == 'enum') {
                echo '
                    <label for="' . $attribute['COLUMN_NAME'] . '"><b>' . $attribute['COLUMN_NAME'] . '</b></label>
                    <select name="' . $attribute['COLUMN_NAME'] . '" id="' . $attribute['COLUMN_NAME'] . '">';
                if ($attribute['IS_NULLABLE']) echo '<option value="NULL"></option>';
                if (!is_null($row[$attribute['COLUMN_NAME']]))
                    echo '<option value="' . $row[$attribute['COLUMN_NAME']] . '" selected>' . ucfirst($row[$attribute['COLUMN_NAME']]) . '</option>';
                preg_match_all("/'(\w+)'/", $attribute['COLUMN_TYPE'], $options);
                foreach ($options[1] as $option) {
                    if ($option != $row[$attribute['COLUMN_NAME']])
                        echo '<option value="' . $option . '">' . ucfirst($option) . '</option>';
                }
                echo '</select>';
            }
            else {
                echo '
                    <label for="' . $attribute['COLUMN_NAME'] . '"><b>' . $attribute['COLUMN_NAME'] . '</b></label>
                    <input type="' . Table::getInputType($attribute['DATA_TYPE']) . '" 
                           name="' . $attribute['COLUMN_NAME'] . '" 
                           id="' . $attribute['COLUMN_NAME'] . '"
                           value="' . $row[$attribute['COLUMN_NAME']] . '"';
                if (isset($attribute['CHARACTER_MAXIMUM_LENGTH'])) echo ' maxlength="' . $attribute['CHARACTER_MAXIMUM_LENGTH'] . '"';
                if ($attribute['IS_NULLABLE'] == 'NO') echo ' required';
                echo '>';
            }
        }
        echo '
            <button type="submit" class="btn">Mettre à jour</button>
            </form>';
    }

    public function updateRowAction() {
        $pdo = new PDOConnect($_ENV[$_SESSION['envUsernameVar']], $_ENV[$_SESSION['envPasswordVar']]);
        $pdo->connect();
        $user = new User($_ENV[$_SESSION['envUsernameVar']]);
        $user->fetchGrants();
        var_dump($_POST);
        if (in_array('Update', $user->getPrivileges())) {
            $table = new Table($pdo, $_GET['table']);
            $table->updateRow($pdo, $_GET['id'], $_POST);
            /*header('Location: ?ctrl=table&action=selectRows&table=' . $_GET['table'] . '&page=1');
            exit();*/
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