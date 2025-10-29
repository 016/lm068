<?php

namespace App\Core;

use PDO;
use PDOException;
use PDOStatement;

class Database
{
    private static $instance = null;
    private $connection;
    private $host;
    private $database;
    private $username;
    private $password;
    private $charset;

    private function __construct()
    {
        // 优先使用 database.local.php，如果不存在则使用 database.php
        $localConfigPath = __DIR__ . '/../config/database.local.php';
        $defaultConfigPath = __DIR__ . '/../config/database.php';
        
        if (file_exists($localConfigPath)) {
            $config = require_once $localConfigPath;
        } else {
            $config = require_once $defaultConfigPath;
        }
        
        $this->host = $config['host'];
        $this->database = $config['database'];
        $this->username = $config['username'];
        $this->password = $config['password'];
        $this->charset = $config['charset'];

        try {
            $dsn = "mysql:host={$this->host};dbname={$this->database};charset={$this->charset}";
            $this->connection = new PDO($dsn, $this->username, $this->password, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]);
        } catch (PDOException $e) {
            throw new PDOException($e->getMessage(), (int)$e->getCode());
        }
    }

    public static function getInstance(): Database
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function getConnection(): PDO
    {
        return $this->connection;
    }

    public function query(string $sql, array $params = []): \PDOStatement
    {
        try {
            $stmt = $this->connection->prepare($sql);
            $stmt->execute($params);
            return $stmt;
        } catch (PDOException $e) {
            throw new PDOException($e->getMessage(), (int)$e->getCode());
        }
    }

    public function queryOne(string $sql, array $params = []): ?array
    {
        try {
            $stmt = $this->connection->prepare($sql);
            $stmt->execute($params);
            $result = $stmt->fetch(\PDO::FETCH_ASSOC);
            return $result !== false ? $result : null;
        } catch (\PDOException $e) {
            throw new \PDOException($e->getMessage(), (int)$e->getCode());
        }
    }


    /**
     * 准备 SQL 语句用于流式读取
     *
     * @param string $sql SQL 查询语句
     * @return PDOStatement
     * @throws PDOException
     */
    public function prepare(string $sql): PDOStatement
    {
        try {
            $stmt = $this->connection->prepare($sql);
            // 设置为非缓冲查询模式，实现真正的流式读取
            $stmt->setFetchMode(PDO::FETCH_ASSOC);
            return $stmt;
        } catch (PDOException $e) {
            throw new PDOException($e->getMessage(), (int)$e->getCode());
        }
    }

    /**
     * 执行一个 INSERT, UPDATE, DELETE 语句，并返回其影响的行数。
     *
     * @param string $sql
     * @param array $params
     * @return int 影响的行数
     * @throws PDOException
     */
    public function execute(string $sql, array $params = []): int
    {
        try {
            $stmt = $this->connection->prepare($sql);
            $stmt->execute($params);
            return $stmt->rowCount(); // 直接返回影响的行数
        } catch (PDOException $e) {
            throw new PDOException($e->getMessage(), (int)$e->getCode());
        }
    }

    public function fetchAll(string $sql, array $params = []): array
    {
        $stmt = $this->query($sql, $params);
        return $stmt->fetchAll();
    }

    public function fetch(string $sql, array $params = []): ?array
    {
        $stmt = $this->query($sql, $params);
        $result = $stmt->fetch();
        return $result ?: null;
    }

    public function insert(string $table, array $data): int
    {
        $keys = array_keys($data);
        $fields = implode(',', $keys);
        $placeholders = ':' . implode(', :', $keys);
        
        $sql = "INSERT INTO {$table} ({$fields}) VALUES ({$placeholders})";
        $this->query($sql, $data);
        
        return (int)$this->connection->lastInsertId();
    }

    public function update(string $table, array $data, string $where, array $whereParams = []): int
    {
        $fields = array_map(fn($key) => "{$key} = :{$key}", array_keys($data));
        $setClause = implode(', ', $fields);
        
        $sql = "UPDATE {$table} SET {$setClause} WHERE {$where}";
        $stmt = $this->query($sql, array_merge($data, $whereParams));
        
        return $stmt->rowCount();
    }

    public function delete(string $table, string $where, array $params = []): int
    {
        $sql = "DELETE FROM {$table} WHERE {$where}";
        $stmt = $this->query($sql, $params);
        
        return $stmt->rowCount();
    }

    public function beginTransaction(): bool
    {
        return $this->connection->beginTransaction();
    }

    public function commit(): bool
    {
        return $this->connection->commit();
    }

    public function rollback(): bool
    {
        return $this->connection->rollback();
    }

    private function __clone() {}
    public function __wakeup() {}
}