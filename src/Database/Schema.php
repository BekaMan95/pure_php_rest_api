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

    public function columns(string $table_name): array {

        if (!$this->tableExists($table_name)) {
            return [];
        }
        $sql = "SHOW COLUMNS FROM `$table_name`";
        $statement = $this->connection->query($sql);
        $columns = [];
        
        while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
            $columns[] = $row['Field'];
        }

        return $columns;
    }

    private function getPDOParamType($value): int {
        switch (true) {
            case is_int($value):
                return PDO::PARAM_INT;
            case is_bool($value):
                return PDO::PARAM_BOOL;
            case is_null($value):
                return PDO::PARAM_NULL;
            default:
                return PDO::PARAM_STR;
        }
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

    public function create(string $table, array $values) : string {
        
        $columns = $this->columns($table);
        $items = '';
        foreach ($columns as $name) {
            $items .= $name . ', ';
        }
        $column_names = rtrim($items, ', ');
        $items = '';
        foreach ($columns as $name) {
            $items .= ':'.$name . ', ';
        }
        $place_holders = rtrim($items, ', ');

        $sql = "INSERT INTO $table ($column_names) VALUES ($place_holders)";
        $statement = $this->connection->prepare($sql);

        foreach ($values as $key => $value) {
            if (!in_array($key, $columns)) {
                return '';
            }
        }

        foreach ($columns as $name) {
            $statement->bindValue(":$name", $values[$name]);
        }

        $statement->execute();
        return $this->connection->lastInsertId();
    }

    public function getItem(string $table, string $id) : array | false {

        $sql = "SELECT * FROM $table WHERE id = :id";
        $statement = $this->connection->prepare($sql);
        $statement->bindValue(":id", $id, PDO::PARAM_INT);
        $statement->execute();
        $value = $statement->fetch(PDO::FETCH_ASSOC);
        return $value;
    }

    public function update(string $table, array $current, array $new) : int {

        $sql = "UPDATE $table SET ";
        $setClauses = [];

        foreach ($current as $column => $value) {
            if ($column !== 'id') {
                $setClauses[] = "$column = :$column";
            }
        }
        
        $sql .= implode(', ', $setClauses) . " WHERE id = :id";

        $statement = $this->connection->prepare($sql);

        foreach ($current as $column => $value) {
            if ($column !== 'id') {
                $valueToBind = $new[$column] ?? $value;
                $statement->bindValue(":$column", $valueToBind, $this->getPDOParamType($valueToBind));
            }
        }

        $statement->bindValue(":id", $current['id'], $this->getPDOParamType($current['id']));
        $statement->execute();

        return $statement->rowCount();
    }

    public function delete(string $table, string $id) : bool {
        
        $sql = "DELETE FROM $table WHERE id = :id";
        $statement = $this->connection->prepare($sql);
        $statement->bindValue(":id", $id, $this->getPDOParamType($id));
        $statement->execute();
        return $statement->rowCount();
    }
}
