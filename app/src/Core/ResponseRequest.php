<?php

namespace App\Core;

// Standardise toutes les réponses JSON de l'API
class Response
{
    private function send(array $data, int $status): void
    {
        http_response_code($status);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }

    // 200 OK — réponse standard avec données
    public function json(array $data, int $status = 200): void
    {
        $this->send(['data' => $data], $status);
    }

    // 201 Created — ressource créée avec succès
    public function created(array $data): void
    {
        $this->send(['data' => $data], 201);
    }

    // 400 Bad Request — requête invalide (message en string, pas en array)
    public function error(string $message, int $status = 400): void
    {
        $this->send(['error' => $message], $status);
    }

    // 404 Not Found
    public function notFound(string $message = 'Ressource introuvable'): void
    {
        $this->send(['error' => $message], 404);
    }

    // 401 Unauthorized — non connecté
    public function unauthorized(string $message = 'Non autorisé'): void
    {
        $this->send(['error' => $message], 401);
    }

    // 204 No Content — opération réussie sans body à renvoyer
    public function noContent(): void
    {
        http_response_code(204);
        exit;
    }

    // Easter egg : 418 I'm a teapot
    public function teapot(): void
    {
        $this->send(['error' => "Je suis une théière, pas un serveur."], 418);
    }
}
