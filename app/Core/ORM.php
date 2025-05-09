<?php
namespace Rapulo\Core;

class ORM {
    private $pdo;
    private $table;
    private $query = '';
    private $bindings = [];

    public function __construct($table) {
        $this->table = $table;
        try {
            $config = require __DIR__ . '/../Config/database.php';
            $this->pdo = new \PDO(
                "{$config['driver']}:host={$config['host']};dbname={$config['database']};charset={$config['charset']}",
                $config['username'],
                $config['password'],
                [\PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION]
            );
        } catch (\PDOException $e) {
            $this->logError("Database connection failed: " . $e->getMessage());
            throw new \Exception("Failed to connect to database");
        }
    }

    public static function table($table) {
        return new static($table);
    }

    public function select($columns = '*') {
        $this->query = "SELECT $columns FROM {$this->table}";
        return $this;
    }

    public function where($column, $operator, $value) {
        $this->query .= empty($this->query) ? "SELECT * FROM {$this->table} WHERE" : " AND";
        $this->query .= " $column $operator ?";
        $this->bindings[] = $value;
        return $this;
    }

    public function get() {
        try {
            $stmt = $this->pdo->prepare($this->query);
            $stmt->execute($this->bindings);
            return $stmt->fetchAll(\PDO::FETCH_OBJ);
        } catch (\PDOException $e) {
            $this->logError("Query failed: " . $e->getMessage());
            throw new \Exception("Database query failed");
        }
    }

    public function create($data) {
        try {
            $columns = implode(', ', array_keys($data));
            $placeholders = implode(', ', array_fill(0, count($data), '?'));
            $this->query = "INSERT INTO {$this->table} ($columns) VALUES ($placeholders)";
            $this->bindings = array_values($data);
            $stmt = $this->pdo->prepare($this->query);
            return $stmt->execute($this->bindings);
        } catch (\PDOException $e) {
            $this->logError("Insert failed: " . $e->getMessage());
            throw new \Exception("Database insert failed");
        }
    }

    public function getPdo() {
        return $this->pdo;
    }

    private function logError($message) {
        $logFile = __DIR__ . '/../../storage/logs/app.log';
        $timestamp = date('Y-m-d H:i:s');
        file_put_contents($logFile, "[$timestamp] $message
", FILE_APPEND);
    }
}