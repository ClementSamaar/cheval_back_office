<?php

class User
{
    private string $username;
    private ?array $privileges;
    private array|string|null $grantedTables;

    public function __construct(string $username) {
        $this->username = $username;
        $this->privileges = null;
        $this->grantedTables = null;
    }

    public function fetchGrants() : void {
        $pdo = new PDOConnect($_ENV['DB_ROOT_USERNAME'], $_ENV['DB_ROOT_PASS']);
        $pdo->connect();
        $query = '
            SELECT Select_priv, Insert_priv, Update_priv, Delete_priv, Create_priv, Drop_priv, 
                   Reload_priv, File_priv, Grant_priv, References_priv, Index_priv, Alter_priv, 
                   Show_db_priv, Super_priv, Create_tmp_table_priv, Lock_tables_priv, Execute_priv, 
                   Repl_slave_priv, Repl_client_priv, 
                   Create_view_priv, Show_view_priv, Create_routine_priv, Alter_routine_priv, Create_user_priv, 
                   Event_priv, Trigger_priv, Create_tablespace_priv
            FROM user 
            WHERE User=' . $pdo->getPdo()->quote($this->username);
        $privileges = $pdo->getPdo()->prepare($query);
        $privileges->execute();
        $privileges = $privileges->fetchAll(PDO::FETCH_ASSOC);
        if (isset($privileges)){
            foreach ($privileges[0] as $privilege => $granted) {
                if ($granted == 'Y') {
                    $this->privileges[] = preg_replace('/_priv/', '', $privilege);
                    if ($privilege == 'Select_priv' or $privilege == 'Insert_priv' or $privilege == 'Update_priv' or $privilege == 'Delete_priv')
                        $this->grantedTables = '*';
                }
            }

            if ($this->grantedTables != '*') {
                $query = '
            SELECT Table_name, Table_priv
            FROM tables_priv
            WHERE Db="cheval_simulator" AND User=' . $pdo->getPdo()->quote($this->username);
                $specPrivileges = $pdo->getPdo()->prepare($query);
                $specPrivileges->execute();
                $specPrivileges = $specPrivileges->fetchAll(PDO::FETCH_ASSOC);
                $specPrivileges[0]['Table_priv'] = explode(',', $specPrivileges[0]['Table_priv']);

                foreach ($specPrivileges[0]['Table_priv'] as $specPrivilege){
                    $this->privileges[] = $specPrivilege;
                }

                foreach ($specPrivileges as $specPrivilege){
                    $this->grantedTables[] = $specPrivilege['Table_name'];
                }
            }
        }
    }

    public function getPrivileges(): array { return $this->privileges; }

    public function getGrantedTables(): array|string { return $this->grantedTables; }


}