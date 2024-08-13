<?php

class Database {
    
    public function __construct(
        private string $host, 
        /*private string $port,*/ 
        private string $name, 
        private string $user, 
        private string $password) {

    }

    public function getConnection(): PDO {
        $data_source_name = "mysql:host={$this->host};dbname={$this->name};charset-utf8";
        return new PDO($data_source_name, $this->user, $this->password, [
            PDO::ATTR_EMULATE_PREPARES => false,
            PDO::ATTR_STRINGIFY_FETCHES => false
        ]);
    }
}
