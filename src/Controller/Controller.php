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
            if (array_key_exists($value, $mapping)) {
                $attributes .= $key . ' '. $mapping[$value] . ', '; 
            }
            else {
                http_response_code(500);
                echo json_encode([
                    'message' => 'Unknown data type found! please check again.',
                    "column" => $value,
                    'status' => 'failed'
                ]);
                exit;
            }
        }

        $modified_attributes = rtrim($attributes, ', ');

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

    public function getRequest($request, string $method, string $table, ?string $id) : void {
        if (!$this->schema->tableExists($table)) {
            http_response_code(500);
            echo json_encode([
                "message" => "$table table doesn't exist! try again.",
                "status" => "failed"
            ]);
            exit;
        }
        if ($id) {
            $this->itemRequest($request, $method, $table, $id);
        }
        else {
            $this->collectionRequest($request, $method, $table);
        }
    }

    public function itemRequest($request, string $method, string $table, string $id) : void {
        $item = $this->schema->getItem($table, $id);
        if (!$item) {
            http_response_code(404);
            echo json_encode([
                "message" => "Specified id not found! try again!",
                "id" => $id,
                "status" => "failed"
            ]);
            exit;
        }
        switch ($method) {
            case 'GET':
                echo json_encode($item);
                break;
            case 'PUT':
                if (!$request) {
                    echo json_encode($request);
                    exit;
                }
                $rows = $this->schema->update($table, $item, $request);
                http_response_code(201);
                echo json_encode([
                    "message" => "$table $id updated.",
                    "rows affected" => $rows,
                    "status" => "success"
                ]);
                break;
            case 'DELETE':
                $this->schema->delete($table, $id);
                echo json_encode([
                    "message" => "$table $id deleted.",
                     "status" => "sucess"
                    ]);
                break;
            default:
                http_response_code(405);
                header("Allow: GET, PUT, DELETE");
                echo json_encode([
                    'message' => 'unknown method.',
                    'status' => 'failed'
                ], 404);
                break;
        }
    }

    public function collectionRequest($request, string $method, string $table) : void {

        switch ($method) {
            case 'GET':
                echo json_encode($this->schema->getAll($table));
                break;
            case 'POST':
                if (!$request) {
                    echo json_encode($request);
                    exit;
                }
                $id = $this->schema->create($table, $request);
                if (!$id) {
                    http_response_code(500);
                    echo json_encode([
                        "message" => "Unknown column name found!",
                        "status" => "failed"
                    ]);
                    exit;
                }
                http_response_code(201);
                echo json_encode([
                    "message" => "New $table created at id $id.",
                    "status" => "success"
                ]);
                break;
            default:
                http_response_code(405);
                header("Allow: GET, POST");
                echo json_encode([
                    'message' => 'unknown method.',
                    'status' => 'failed'
                ], 404);
                break;
        }
    }
}
