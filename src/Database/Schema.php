<?php

class Schema {

    private PDO $connection;

    public function __construct(Database $database) {
        
        $this->connection = $database->getConnection();
    }

    public function tableExists(string $table_name): bool {
        $sql = "SHOW TABLES LIKE '$table_name'";
        $statement = $this->connection->query($sql);
        return $statement->rowCount() > 0;
    }

    public function create_table(string $table_name, string $table_attributes) : bool {

        if ($this->tableExists($table_name)) {
            return false;
        }
        $sql = "CREATE TABLE $table_name ( $table_attributes )";
        $this->connection->exec($sql);
        return true;
    }

    public function drop_table(string $table_name) : bool {
        
        if (!$this->tableExists($table_name)) {
            return false;
        }
        $sql = "DROP TABLE $table_name";
        $this->connection->exec($sql);
        return true;
    }

    public function getAll(string $table) : array {
        $sql = "SELECT * FROM $table";
        $statement = $this->connection->query($sql);
        $values = [];
        while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
            $values[] = $row;
        }
        return $values;
    }
}
