<?php

namespace App\Core;

// Pattern Singleton : une seule connexion PDO partagée dans toute l'app
class Database
{
    private static ?Database $instance = null;
    private \PDO $pdo;

    private function __construct()
    {
        // Lit les variables d'environnement Docker, avec des valeurs par défaut
        $host = $_ENV['DB_HOST'] ?? 'php-oop-exercice-db';
        $name = $_ENV['DB_NAME'] ?? 'blog';
        $user = $_ENV['DB_USER'] ?? 'root';
        $pass = $_ENV['DB_PASS'] ?? 'password';

        $pdon = "mysql:host={$host};dbname={$name};charset=utf8";

        $this->pdo = new \PDO($pdon, $user, $pass, [
            \PDO::ATTR_ERRMODE            => \PDO::ERRMODE_EXCEPTION,
            \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
        ]);
    }

    // Retourne l'unique instance — en crée une si elle n'existe pas encore
    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function getConnection(): \PDO
    {
        return $this->pdo;
    }
}
