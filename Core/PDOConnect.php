<?php

class PDOConnect
{
    private $_pdo = null;

    private $_host;
    private $_database;
    private $_dbUsername;
    private $_dbPass;


    public function __construct(string $dbUsername, string $dbPass) {
        $this->_host = $_ENV['DB_HOST_NAME'];
        if ($dbUsername == 'adminer'){
            $this->_database = 'mysql';
        }
        else $this->_database = $_ENV['DB_DATABASE_NAME'];
        $this->_dbUsername = $dbUsername;
        $this->_dbPass = $dbPass;
    }

    public function connect()
    {
        if ($this->_pdo === null){
            try {
                $this->_pdo = new PDO(sprintf("mysql:host=%s;port=3306;dbname=%s", $this->_host, $this->_database),
                    $this->_dbUsername,
                    $this->_dbPass);

                $this->_pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            }
            catch(PDOException $e) {
                echo $e->getMessage();
            }
        }
    }

    /**
     * @return PDO|null
     */
    public function getPdo(): ?PDO { return $this->_pdo; }

    public function setDatabase(string $db): void {
        $this->_database = $db;
    }
}