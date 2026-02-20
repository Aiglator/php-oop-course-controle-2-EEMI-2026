<?php

namespace App\Models;

class Comment extends Model
{
    protected string $table = 'comments';

    public function findByPost(int $postId): array
    {
        $querycomment = $this->pdo->prepare(
            "SELECT comments.*, users.name AS commenter_name
             FROM comments
             JOIN users ON comments.user_id = users.id
             WHERE comments.post_id = :post_id
             ORDER BY comments.created_at ASC"
        );
        $querycomment->bindValue(':post_id', $postId, \PDO::PARAM_INT);
        $querycomment->execute();
        return $querycomment->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function countByPost(int $postId): int
    {
        $querycomment = $this->pdo->prepare(
            "SELECT COUNT(*) FROM {$this->table} WHERE post_id = :post_id"
        );
        $querycomment->bindValue(':post_id', $postId, \PDO::PARAM_INT);
        $querycomment->execute();
        return (int) $querycomment->fetchColumn();
    }
}
