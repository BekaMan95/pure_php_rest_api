# PHP REST API with Object-Oriented Programming

This project is a simple PHP-based RESTful API built with Object-Oriented Programming (OOP) principles. The API interacts with a MySQL database to perform CRUD (Create, Read, Update, Delete) operations.

## Features

- **Autoloading**: Automatically loads classes from the `src/Controller` and `src/Database` directories using `spl_autoload_register`.
- **Database Interaction**: Uses PDO (PHP Data Objects) for secure interaction with the MySQL database.
- **CRUD Operations**: Supports creating, reading, updating, and deleting records in the database.
- **Error Handling**: Implements generic error handling class for error handling and returns JSON-formatted error responses.

## Prerequisites

- **PHP 7.4+**
- **MySQL 5.7+**
- **Apache Web Server** (XAMPP or WAMPP recommended along with php)

## Installation

1. **Clone the Repository**

    ```bash
    git clone https://github.com/BekaMan95/pure_php_rest_api.git
    cd pure_php_rest_api
    ```

2. **Configure Database**

    Create a MySQL database and user, then update the `config.php` file with your database credentials:

    ```php
    // config.php
    $dbhost = 'localhost';
    $dbname = 'your_database_name';
    $dbuser = 'your_username';
    $dbpass = 'your_password';
    ```

3. **Set Up Autoloading**

    If not using Composer, the autoload function is already set up in `index.php`:

    ```php
    spl_autoload_register(function ($class) {
        $directories = [
            '/src/Controller/',
            '/src/Database/'
        ];

        foreach ($directories as $directory) {
            $file = __DIR__ . $directory . $class . '.php';
            if (file_exists($file)) {
                require_once $file;
                return;
            }
        }
    });
    ```

4. **Run the API**

    You can run this API on a local server using Apache server or a tool like XAMPP on local machine:

    for linux

    ```bash
    sudo /opt/lampp/lampp start
    ```

    for windows
    ```powershell
    cd C:\xampp
    xampp-control.exe
    ```

    Navigate to `http://localhost/pure_php_rest_api/` in your API request sending tool to interact with the API.

## Usage

### Endpoints

- **Create Table**

    ```http
    POST /create_table/{table_name}
    ```
    Example Payload:

    ```json
    {
        "id": "id",
        "string": "name",
        "int": "age",
        "float": "salary",
        "bool": "is_married",
        "timestamp": "created_at"
    }
    ```

- **Drop Table**

    ```http
    POST /remove_table/{table_name}
    ```

- **Get Records**

    ```http
    GET /{table_name}
    ```
    Example Response:

    ```json
    [
        {
            "id": 1,
            "name": "John Smith",
            "age": 30,
            "salary": 60000.00
        },
        {
            "id": 2,
            "name": "Jane Smith",
            "age": 28,
            "salary": 75000.00
        }
    ]
    ```

- **New Record**

    ```http
    POST /{table_name}
    ```
    Example Payload:

    ```json
    {
        "id": 1,
        "name": "John Smith",
        "age": 30
    }
    ```

- **Get a Record**

    ```http
    GET /{table_name}/{id}
    ```
    Example Response:

    ```json
    {
        "id": 1,
        "name": "John Smith",
        "age": 30,
        "salary": 60000.00
    }
    ```

- **Update Record**

    ```http
    PUT /{table_name}/{id}
    ```
    Example Payload:

    ```json
    {
        "name": "John Smith",
        "age": 30
    }
    ```

- **Delete Record**

    ```http
    DELETE /{table_name}/{id}
    ```

### Error Handling

The API returns a JSON response with appropriate HTTP status codes for errors:

```json
{
    "status": "error",
    "message": "Description of the error",
    "file": "/path/to/the/file.php",
    "line": 42
}
