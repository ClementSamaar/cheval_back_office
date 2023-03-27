<?php

class Table
{
    private string $_name;
    private string $_pk;
    private array  $_attributes;
    private array  $_rows;
    private int    $_rowAmount;
    private bool   $_empty;

    public function __construct(PDOConnect $pdo, string $tableName) {
        $this->_name = $tableName;
        $this->_attributes = [];
        $this->_rows = [];
        $pdo->connect();
        $pk = $pdo->getPdo()->prepare('SHOW KEYS FROM ' . $this->_name . ' WHERE Key_name = "PRIMARY"');
        $pk->execute();
        $pk = $pk->fetch(PDO::FETCH_ASSOC);
        $this->_pk = $pk['Column_name'];

        $pdo = new PDOConnect($_ENV[$_SESSION['envUsernameVar']], $_ENV[$_SESSION['envPasswordVar']]);
        $pdo->setDatabase('information_schema');
        $pdo->connect();
        $attributes = $pdo->getPdo()->prepare('SELECT COLUMN_NAME FROM COLUMNS WHERE TABLE_SCHEMA="cheval_simulator" AND TABLE_NAME= :table');
        $attributes->bindParam(':table', $tableName);
        $attributes->execute();
        $this->_attributes = $attributes->fetchAll(PDO::FETCH_COLUMN);
    }

    private function initData(array $tableData) : void {
        if (!empty($tableData)){
            $this->_empty = false;
            $this->_rowAmount = sizeof($tableData);

            // INIT ROWS
            foreach ($tableData as $row){
                $this->_rows[] = $row;
            }
        }
        else {
            $this->_empty = true;
            $this->_rowAmount = 0;
        }
    }

    public function insertRow(PDOConnect $pdo) : bool {
        var_dump($_POST);
        $pdo->connect();
        $query = 'INSERT INTO ' . $this->_name . ' VALUES (';
        foreach ($this->_attributes as $attribute) {
            if ($attribute == $this->_pk) $value = 1;
            else {
                if (!isset($_POST[$attribute]) or empty($_POST[$attribute]))  $value = 'NULL';
                else $value = $_POST[$attribute];
            }
            if ($attribute == 'date_de_naissance') $value = '2002-12-09';
            elseif ($attribute == 'date_inscription' or $attribute == 'derniere_connexion') $value = date('Y-m-d');
            $query .= $pdo->getPdo()->quote($value) . ', ';
        }
        $query = substr($query, 0, strlen($query) - 2);
        $query .= ')';
        echo $query;
        $insertStatement = $pdo->getPdo()->prepare($query);
        return $insertStatement->execute();
    }

    public function selectAll(PDOConnect $pdo, int $limit, int $pageNumber) : void {
        $pdo->connect();
        $query = 'SELECT * FROM ' . $this->_name . ' LIMIT ' . $limit . ' OFFSET ' . $limit * ($pageNumber - 1);
        $tableData = $pdo->getPdo()->prepare($query);
        $tableData->execute();
        $tableData = $tableData->fetchAll(PDO::FETCH_ASSOC);
        $this->initData($tableData);
    }

    public function orderBy(PDOConnect $pdo, string $attribute, string $order, int $limit, int $pageNumber) : void {
        $pdo->connect();
        $query = 'SELECT * FROM ' . $this->_name . ' ORDER BY ' . $attribute . ' ' . $order .
                ' LIMIT ' . $limit . ' OFFSET ' . $limit * ($pageNumber - 1);
        $tableData = $pdo->getPdo()->prepare($query);
        $tableData->execute();
        $tableData = $tableData->fetchAll(PDO::FETCH_ASSOC);
        $this->initData($tableData);
    }

    /*public function updateRow(PDOConnect $pdo, int $pkValue, array $values) : bool {
        $pdo->connect();
        if (sizeof($this->_attributes) == sizeof($values)) {
            $query = 'UPDATE ' . $this->_name . ' SET ';
            $firstValuePassed = false;
            for ($i = 0; $i < sizeof($values); $i++){
                if (!is_null($values[$i])) {
                    if ($i - 1 > 0 and $i > 0 and !is_null($values[$i - 1]) or $firstValuePassed) {
                        $query .= ', ';
                        $firstValuePassed = true;
                    }
                    $query .= $this->_attributes[$i] . '=' . $pdo->getPdo()->quote($values[$i]);
                }
            }
            $query .= ' WHERE ' . $this->_pk . '=' . $pdo->getPdo()->quote($pkValue);
            $updateStatement = $pdo->getPdo()->prepare($query);
            return $updateStatement->execute();
        }
        else return false;
    }*/

    public function updateRow(PDOConnect $pdo, int $pkValue, array $values) : bool {
        $pdo->connect();
        if (sizeof($this->_attributes) == sizeof($values)) {
            $query = 'UPDATE ' . $this->_name . ' SET ';
            $firstValuePassed = false;
            for ($i = 0; $i < sizeof($values); $i++){
                if ($i > 0) $query .= ', ';
                $query .= $this->_attributes[$i] . '=' . $pdo->getPdo()->quote($values[$i]);
            }
            $query .= ' WHERE ' . $this->_pk . '=' . $pdo->getPdo()->quote($pkValue);
            $updateStatement = $pdo->getPdo()->prepare($query);
            return $updateStatement->execute();
        }
        else return false;
    }

    public function deleteRow(PDOConnect $pdo, int $pkValue) : bool {
        $pdo->connect();
        $query = 'DELETE FROM ' . $this->_name . ' WHERE ' . $this->_pk . '=' . $pdo->getPdo()->quote($pkValue);
        $deleteStatement = $pdo->getPdo()->prepare($query);
        return $deleteStatement->execute();
    }

    public function deleteMultipleRows(PDOConnect $pdo, array $pkValues) : array {
        $pdo->connect();
        $output = [];
        foreach ($pkValues as $pkValue) {
            $query = 'DELETE FROM ' . $this->_name . ' WHERE ' . $this->_pk . '=' . $pdo->getPdo()->quote($pkValue);
            $deleteRow = $pdo->getPdo()->prepare($query);
            if ($deleteRow->execute()) $output[] = [$pkValues, true];
            else $output[] = [$pkValues, false];
        }
        return $output;
    }

    public function getAttributes(): array { return $this->_attributes; }
    public function getRows(): array       { return $this->_rows; }
    public function getRowAmount(): int    { return $this->_rowAmount; }
    public function getEmpty(): bool       { return $this->_empty; }
    public function getName(): string      { return $this->_name; }
}