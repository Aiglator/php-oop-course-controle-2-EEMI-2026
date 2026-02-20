<?php

namespace App\Models;

use App\Core\Database;

abstract class Model
{
    protected \PDO $pdo;
    protected string $table;
    protected string $primaryKey = 'id';

    public function __construct()
    {
        $this->pdo = Database::getInstance()->getConnection();
    }

    public function find(int $id): array|false
    {
        $querymodele = $this->pdo->prepare(
            "SELECT * FROM {$this->table} WHERE {$this->primaryKey} = :id"
        );
        $querymodele->bindValue(':id', $id, \PDO::PARAM_INT);
        $querymodele->execute();
        return $querymodele->fetch(\PDO::FETCH_ASSOC);
    }

    public function findAll(int $limit = 10, int $offset = 0): array
    {
        $querymodele = $this->pdo->prepare(
            "SELECT * FROM {$this->table} LIMIT :limit OFFSET :offset"
        );
        $querymodele->bindValue(':limit', $limit, \PDO::PARAM_INT);
        $querymodele->bindValue(':offset', $offset, \PDO::PARAM_INT);
        $querymodele->execute();
        return $querymodele->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function count(): int
    {
        $querymodele = $this->pdo->prepare("SELECT COUNT(*) FROM {$this->table}");
        $querymodele->execute();
        return (int) $querymodele->fetchColumn();
    }

    public function insert(array $data): int
    {
        $columns      = implode(', ', array_keys($data));
        $placeholders = implode(', ', array_map(fn($k) => ":$k", array_keys($data)));
        $querymodele = $this->pdo->prepare(
            "INSERT INTO {$this->table} ({$columns}) VALUES ({$placeholders})"
        );
        foreach ($data as $key => $value) {
            $querymodele->bindValue(":$key", $value);
        }
        $querymodele->execute();
        return (int) $this->pdo->lastInsertId();
    }

    public function update(int $id, array $data): int
    {
        $set  = implode(', ', array_map(fn($k) => "$k = :$k", array_keys($data)));
        $querymodele = $this->pdo->prepare(
            "UPDATE {$this->table} SET {$set} WHERE {$this->primaryKey} = :id"
        );
        foreach ($data as $key => $value) {
            $querymodele->bindValue(":$key", $value);
        }
        $querymodele->bindValue(':id', $id, \PDO::PARAM_INT);
        $querymodele->execute();
        return $querymodele->rowCount();
    }

    public function delete(int $id): int
    {
        $querymodele = $this->pdo->prepare(
            "DELETE FROM {$this->table} WHERE {$this->primaryKey} = :id"
        );
        $querymodele->bindValue(':id', $id, \PDO::PARAM_INT);
        $querymodele->execute();
        return $querymodele->rowCount();
    }
}
