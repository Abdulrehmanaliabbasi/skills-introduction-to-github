<?php
/**
 * InfinityBinary - Database Connection and Query Helper
 */

if (!defined('IB_INIT')) {
    die('Direct access not permitted');
}

class Database {
    private static $instance = null;
    private $conn = null;

    private function __construct() {
        try {
            $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=' . DB_CHARSET;
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES " . DB_CHARSET
            ];

            $this->conn = new PDO($dsn, DB_USER, DB_PASS, $options);
        } catch (PDOException $e) {
            if (IB_DEBUG) {
                die('Database connection failed: ' . $e->getMessage());
            } else {
                die('Database connection failed. Please try again later.');
            }
        }
    }

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function getConnection() {
        return $this->conn;
    }

    public function query($sql, $params = []) {
        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->execute($params);
            return $stmt;
        } catch (PDOException $e) {
            if (IB_DEBUG) {
                throw new Exception('Query failed: ' . $e->getMessage());
            }
            return false;
        }
    }

    public function fetch($sql, $params = []) {
        $stmt = $this->query($sql, $params);
        return $stmt ? $stmt->fetch() : false;
    }

    public function fetchAll($sql, $params = []) {
        $stmt = $this->query($sql, $params);
        return $stmt ? $stmt->fetchAll() : [];
    }

    public function insert($table, $data) {
        $fields = array_keys($data);
        $values = array_values($data);
        $placeholders = array_fill(0, count($fields), '?');

        $sql = "INSERT INTO `{$table}` (`" . implode('`, `', $fields) . "`) VALUES (" . implode(', ', $placeholders) . ")";

        $stmt = $this->query($sql, $values);
        return $stmt ? $this->conn->lastInsertId() : false;
    }

    public function update($table, $data, $where, $whereParams = []) {
        $set = [];
        $values = [];

        foreach ($data as $key => $value) {
            $set[] = "`{$key}` = ?";
            $values[] = $value;
        }

        $values = array_merge($values, $whereParams);

        $sql = "UPDATE `{$table}` SET " . implode(', ', $set) . " WHERE {$where}";

        return $this->query($sql, $values) !== false;
    }

    public function delete($table, $where, $params = []) {
        $sql = "DELETE FROM `{$table}` WHERE {$where}";
        return $this->query($sql, $params) !== false;
    }

    public function exists($table, $where, $params = []) {
        $sql = "SELECT COUNT(*) as count FROM `{$table}` WHERE {$where}";
        $result = $this->fetch($sql, $params);
        return $result && $result['count'] > 0;
    }

    public function count($table, $where = '1', $params = []) {
        $sql = "SELECT COUNT(*) as count FROM `{$table}` WHERE {$where}";
        $result = $this->fetch($sql, $params);
        return $result ? (int)$result['count'] : 0;
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
}

// Global database helper function
function db() {
    return Database::getInstance();
}
