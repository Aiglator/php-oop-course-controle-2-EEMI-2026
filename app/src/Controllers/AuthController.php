<?php

namespace App\Controllers;

use App\Core\Request;
use App\Core\Response;
use App\Models\User;

class AuthController extends Controller
{
    private User $userModel;

    public function __construct(Request $request, Response $response)
    {
        parent::__construct($request, $response);
        $this->userModel = new User();
    }

    // POST /api/auth/register
    public function register(): void
    {
        $name     = $this->request->getBody('name');
        $email    = $this->request->getBody('email');
        $password = $this->request->getBody('password');

        if (!$name || !$email || !$password) {
            $this->response->error('name, email et password sont requis', 422);
            return;
        }

        if ($this->userModel->emailExists($email)) {
            $this->response->error('Email déjà utilisé', 409);
            return;
        }

        if ($this->userModel->nameExists($name)) {
            $this->response->error("Nom d'utilisateur déjà pris", 409);
            return;
        }

        $id   = $this->userModel->insert([
            'name'     => $name,
            'email'    => $email,
            'password' => $this->userModel->hashPassword($password),
        ]);

        $user = $this->userModel->find($id);
        unset($user['password']);
        $this->response->created($user);
    }

    // POST /api/auth/login
    public function login(): void
    {
        $email    = $this->request->getBody('email');
        $password = $this->request->getBody('password');

        if (!$email || !$password) {
            $this->response->error('email et password sont requis', 422);
            return;
        }

        $user = $this->userModel->findByEmail($email);
        if (!$user) {
            $this->response->unauthorized('Identifiants incorrects');
            return;
        }

        if (!$this->userModel->verifyPassword($password, $user['password'])) {
            $this->response->unauthorized('Identifiants incorrects');
            return;
        }

        $_SESSION['user_id'] = $user['id'];
        unset($user['password']);
        $this->response->json($user);
    }

    // POST /api/auth/logout
    public function logout(): void
    {
        session_destroy();
        $this->response->noContent();
    }

    // GET /api/auth/me
    public function me(): void
    {
        $this->requireAuth();

        $user = $this->userModel->find($this->request->getAuthUserId());
        if (!$user) {
            $this->response->notFound('Utilisateur introuvable');
            return;
        }

        unset($user['password']);
        $this->response->json($user);
    }
}
