<?php

class Controller {

    public function __construct(private Schema $schema) {}

    public function createTable(?string $table_name, array $props) : void {

        if (!$table_name) {
            http_response_code(500);
            echo json_encode([
                'message' => 'table name not specified! please check again.',
                'status' => 'failed'
            ]);
            exit;
        }
        $mapping = [
            'id' => 'INT AUTO_INCREMENT PRIMARY KEY',
            'int' => 'INT',
            'date' => 'DATE',
            'string' => 'VARCHAR(255)',
            'bool' => 'BOOLEAN',
            'float' => 'DECIMAL(10, 2)',
            'timestamp' => 'TIMESTAMP DEFAULT CURRENT_TIMESTAMP'
        ];

        $attributes = '';
        foreach ($props as $key => $value) {
            if (array_key_exists($key, $mapping)) {
                $attributes .= $value . ' '. $mapping[$key] . ','; 
            }
            else {
                http_response_code(500);
                echo json_encode([
                    'message' => 'unknown data type found! please check again.',
                    'status' => 'failed'
                ]);
                exit;
            }
        }

        $modified_attributes = rtrim($attributes, ',');

        $check = $this->schema->create_table($table_name, $modified_attributes);
        
        if (!$check) {
            http_response_code(500);
            echo json_encode([
                'message' => 'table name exists! try again.',
                'status' => 'failed'
            ]);
            exit;
        }
        http_response_code(201);
        echo json_encode([
            'message' => "$table_name table created.",
            'status' => 'success'
        ]);
    }

    public function dropTable(?string $table_name) : void {

        if (!$table_name) {
            http_response_code(500);
            echo json_encode([
                'message' => 'table name not specified! please check again.',
                'status' => 'failed'
            ]);
            exit;
        }
        $check = $this->schema->drop_table($table_name);
        if (!$check) {
            http_response_code(500);
            echo json_encode([
                'message' => "$table_name table doesn't exist! try again.",
                'status' => 'failed'
            ]);
            exit;
        }
        http_response_code(200);
        echo json_encode([
            'message' => "$table_name table removed.",
            'status' => 'success'
        ]);
    }

    public function getRequest(string $method, string $table, ?string $id) : void {
        if ($id) {
            $this->itemRequest($method, 'users', $id);
        }
        else {
            $this->collectionRequest($method, 'users');
        }
    }

    public function itemRequest(string $method, string $table, string $id) : void {
        switch ($method) {
            case 'GET':
                echo json_encode($this->schema->getAll($table));
                break;
            case 'POST':
                echo json_encode([
                    'id' => $id,
                     'method' => $method.' method.'
                    ]);
                break;
            case 'PUT':
                echo json_encode([
                    'id' => $id,
                     'method' => $method.' method.'
                    ]);
                break;
            case 'DELETE':
                echo json_encode([
                    'id' => $id,
                     'method' => $method.' method.'
                    ]);
                break;
            default:
                echo json_encode([
                    'message' => 'unknown method.',
                    'status' => 'failed'
                ], 404);
                break;
        }
    }

    public function collectionRequest(string $method, string $table) : void {

        echo json_encode($this->schema->getAll($table));
    }
}
