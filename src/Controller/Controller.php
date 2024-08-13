<?php

class Controller {
    public function getRequest(string $method, ?string $id) : void {
        if ($id) {
            $this->itemRequest($method, $id);
        }
        else {
            $this->collectionRequest($method);
        }
    }

    public function itemRequest(string $method, string $id) : void {

    }

    public function collectionRequest(string $method) : void {
        
        switch ($method) {
            case 'GET':
                echo json_encode([
                    'id' => 123,
                     'method' => $method.' method.'
                    ]);
                break;
            case 'POST':
                echo json_encode([
                    'id' => 123,
                     'method' => $method.' method.'
                    ]);
                break;
            case 'PUT':
                echo json_encode([
                    'id' => 123,
                     'method' => $method.' method.'
                    ]);
                break;
            case 'DELETE':
                echo json_encode([
                    'id' => 123,
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
}
