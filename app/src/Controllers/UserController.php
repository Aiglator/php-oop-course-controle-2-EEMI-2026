<?php

namespace App\Controllers;

use App\Core\Request;
use App\Core\Response;
use App\Models\User;

class UserController extends Controller
{
    private User $userModel;

    public function __construct(Request $request, Response $response)
    {
        parent::__construct($request, $response);
        $this->userModel = new User();
    }

    // GET /api/users
    public function index(): void
    {
        ['limit' => $limit, 'offset' => $offset] = $this->getPagination();

        $users = $this->userModel->findAll($limit, $offset);
        foreach ($users as &$user) {
            unset($user['password']);
        }

        $this->response->json($users);
    }

    // GET /api/users/:id
    public function show(): void
    {
        $id   = (int) $this->request->Param('id');
        $user = $this->userModel->find($id);

        if (!$user) {
            $this->response->notFound('Utilisateur introuvable');
            return;
        }

        unset($user['password']);
        $this->response->json($user);
    }

    // PUT /api/users/:id
    public function update(): void
    {
        $this->requireAuth();

        $id = (int) $this->request->Param('id');

        if ($this->request->getAuthUserId() !== $id) {
            $this->response->error('Vous ne pouvez modifier que votre propre profil', 403);
            return;
        }

        $data = [];
        if ($name = $this->request->getBody('name')) {
            $data['name'] = $name;
        }
        if ($email = $this->request->getBody('email')) {
            $data['email'] = $email;
        }
        if ($password = $this->request->getBody('password')) {
            $data['password'] = password_hash($password, PASSWORD_BCRYPT);
        }

        if (empty($data)) {
            $this->response->error('Aucune donnée à mettre à jour', 422);
            return;
        }

        $this->userModel->update($id, $data);
        $user = $this->userModel->find($id);
        unset($user['password']);
        $this->response->json($user);
    }
}
