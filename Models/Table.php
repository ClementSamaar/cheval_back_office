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
    }

    private function initArrays(array $tableData) : void {
        if (!empty($tableData)){
            $this->_empty = false;
            $this->_rowAmount = sizeof($tableData);

            // INIT ATTRIBUTES
            foreach ($tableData[0] as $attribute => $firstRow){
                $this->_attributes[] = $attribute;
            }

            // INIT ROWS
            foreach ($tableData as $row){
                $this->_rows[] = $row;
            }
        }
        else $this->_empty = true;
    }

    public function insertRow(PDOConnect $pdo, array $values) : bool {
        $pdo->connect();
        if (sizeof($this->_attributes) == sizeof($values)) {
            $query = 'INSERT INTO ' . $this->_name . ' VALUES (';
            for ($i = 0; $i < sizeof($values); $i++) {
                if ($i > 0) $query .= ', ';
                $query .= $pdo->getPdo()->quote($values[$i]);
            }
            $insertStatement = $pdo->getPdo()->prepare($query);
            return $insertStatement->execute();
        }
        else return false;
    }

    public function selectAll(PDOConnect $pdo, int $limit, int $pageNumber) : void {
        $pdo->connect();
        $query = 'SELECT * FROM ' . $this->_name . ' LIMIT ' . $limit . ' OFFSET ' . $limit * ($pageNumber - 1);
        $tableData = $pdo->getPdo()->prepare($query);
        $tableData->execute();
        $tableData = $tableData->fetchAll(PDO::FETCH_ASSOC);
        $this->initArrays($tableData);
    }

    public function orderBy(PDOConnect $pdo, string $attribute, string $order, int $limit, int $pageNumber) : void {
        $pdo->connect();
        $query = 'SELECT * FROM ' . $this->_name . ' ORDER BY ' . $attribute . ' ' . $order .
                ' LIMIT ' . $limit . ' OFFSET ' . $limit * ($pageNumber - 1);
        $tableData = $pdo->getPdo()->prepare($query);
        $tableData->execute();
        $tableData = $tableData->fetchAll(PDO::FETCH_ASSOC);
        $this->initArrays($tableData);
    }

    public function updateRow(PDOConnect $pdo, int $pkValue, array $values) : bool {
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