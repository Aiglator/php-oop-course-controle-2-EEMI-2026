<?php

namespace App\Models;

class Post extends Model
{
    protected string $table = 'posts';

    public function findWithAuthor(int $id): array|false
    {
        $queryposter = $this->pdo->prepare(
            "SELECT posts.*, users.name AS author_name
             FROM posts
             JOIN users ON posts.user_id = users.id
             WHERE posts.id = :id"
        );
        $queryposter->bindValue(':id', $id, \PDO::PARAM_INT);
        $queryposter->execute();
        return $queryposter->fetch(\PDO::FETCH_ASSOC);
    }

    public function findAllWithAuthors(int $limit = 10, int $offset = 0): array
    {
        $queryposter = $this->pdo->prepare(
            "SELECT posts.*, users.name AS author_name
             FROM posts
             JOIN users ON posts.user_id = users.id
             ORDER BY posts.created_at DESC
             LIMIT :limit OFFSET :offset"
        );
        $queryposter->bindValue(':limit', $limit, \PDO::PARAM_INT);
        $queryposter->bindValue(':offset', $offset, \PDO::PARAM_INT);
        $queryposter->execute();
        return $queryposter->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function findByUser(int $userId, int $limit = 10, int $offset = 0): array
    {
        $queryposter = $this->pdo->prepare(
            "SELECT * FROM {$this->table}
             WHERE user_id = :user_id
             ORDER BY created_at DESC
             LIMIT :limit OFFSET :offset"
        );
        $queryposter->bindValue(':user_id', $userId, \PDO::PARAM_INT);
        $queryposter->bindValue(':limit', $limit, \PDO::PARAM_INT);
        $queryposter->bindValue(':offset', $offset, \PDO::PARAM_INT);
        $queryposter->execute();
        return $queryposter->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function countByUser(int $userId): int
    {
        $queryposter = $this->pdo->prepare(
            "SELECT COUNT(*) FROM {$this->table} WHERE user_id = :user_id"
        );
        $queryposter->bindValue(':user_id', $userId, \PDO::PARAM_INT);
        $queryposter->execute();
        return (int) $queryposter->fetchColumn();
    }
}
