<?php

namespace App\Core;

// ici c'est la ou il y a les méthodes pour récupérer les données comme put par exemple 
class Request
{
    private string $method;
    private string $path;
    private array  $body;
    private array  $query;
    private array  $params = [];  

    public function __construct()
    {
        $this->method = $_SERVER['REQUEST_METHOD'];
        $this->path   = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $this->body   = json_decode(file_get_contents('php://input'), true) ?? [];
        $this->query  = $_GET;
    }

    public function getMethod(): string
    {
        return $this->method;
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function getBody(?string $key = null, mixed $default = null): mixed
    {
        if ($key === null) {
            return $this->body;
        }
        return $this->body[$key] ?? $default;
    }

    public function getQuery(string $key, mixed $default = null): mixed
    {
        return $this->query[$key] ?? $default;
    }


    public function Param(string $key): mixed
    {
        return $this->params[$key] ?? null;
    }

    public function Params(array $params): void
    {
        $this->params = $params;
    }

    public function isAuthenticated(): bool
    {
        return isset($_SESSION['user_id']);
    }

    public function getAuthUserId(): ?int
    {
        return isset($_SESSION['user_id']) ? (int) $_SESSION['user_id'] : null;
    }
}
