<?php

namespace App\Controllers;

use App\Core\Request;
use App\Core\Response;

abstract class Controller
{
    protected Request  $request;
    protected Response $response;

    public function __construct(Request $request, Response $response)
    {
        $this->request  = $request;
        $this->response = $response;
    }

    // Arrête l'exécution avec 401 si l'utilisateur n'est pas connecté
    protected function requireAuth(): void
    {
        if (!$this->request->isAuthenticated()) {
            $this->response->unauthorized();
            exit;
        }
    }

    // Lit ?page=1&limit=10 depuis la requête et renvoie offset calculé
    protected function getPagination(): array
    {
        $page  = max(1, (int) $this->request->getQuery('page', 1));
        $limit = min(100, max(1, (int) $this->request->getQuery('limit', 10)));
        return [
            'page'   => $page,
            'limit'  => $limit,
            'offset' => ($page - 1) * $limit,
        ];
    }
}
