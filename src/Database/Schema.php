<?php

class Schema {

    private PDO $conn;

    public function __constuct(Database $database) {
        $this->conn = $database->getConnection();
    }

    public function create_table(string $table_name, array $table_schema) : void {
        
    }

    public function drop_table(string $table) : void {
        $sql = 'DROP TABLE '. $table;
    }
}
