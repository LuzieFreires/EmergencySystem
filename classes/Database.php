<?php
require_once __DIR__ . '/../config.php';

class Database {
    private $host = 'localhost';
    private $dbName = 'emergency_system';
    private $username = 'root';
    private $password = '';
    private $conn = null;
    private $debug = false; // Set to true for development only

    public function __construct() {
        $this->connect();
    }

    public function connect() {
        if ($this->conn) {
            return $this->conn;
        }

        try {
            $dsn = "mysql:host={$this->host};dbname={$this->dbName};charset=utf8mb4";
            $options = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ];

            $this->conn = new PDO($dsn, $this->username, $this->password, $options);
            return $this->conn;

        } catch (PDOException $e) {
            error_log('Database Connection Error: ' . $e->getMessage());
            $message = $this->debug 
                ? 'Database connection failed: ' . $e->getMessage() 
                : 'Database connection failed';
            throw new Exception($message);
        }
    }

    public function beginTransaction() {
        return $this->conn->beginTransaction();
    }

    public function commit() {
        return $this->conn->commit();
    }

    public function rollBack() {
        return $this->conn->rollBack();
    }

    public function execute($sql, $params = []) {
        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->execute($params);
            return $stmt;
        } catch (PDOException $e) {
            error_log('SQL Execute Error: ' . $e->getMessage());
            $message = $this->debug 
                ? 'Database query failed: ' . $e->getMessage() 
                : 'Database query failed';
            throw new Exception($message);
        }
    }

    public function query($sql, $params = []) {
        return $this->execute($sql, $params);
    }

    public function lastInsertId() {
        return $this->conn->lastInsertId();
    }

    public function disconnect() {
        $this->conn = null;
    }

    public function __destruct() {
        $this->disconnect();
    }
}
