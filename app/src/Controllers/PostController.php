<?php

namespace App\Controllers;

use App\Core\Request;
use App\Core\Response;
use App\Models\Post;

class PostController extends Controller
{
    private Post $postModel;

    public function __construct(Request $request, Response $response)
    {
        parent::__construct($request, $response);
        $this->postModel = new Post();
    }

    // GET /api/posts
    public function index(): void
    {
        ['limit' => $limit, 'offset' => $offset, 'page' => $page] = $this->getPagination();

        $posts = $this->postModel->findAllWithAuthors($limit, $offset);
        $total = $this->postModel->count();

        $this->response->json([
            'posts' => $posts,
            'total' => $total,
            'page'  => $page,
            'limit' => $limit,
        ]);
    }

    // GET /api/posts/:id
    public function show(): void
    {
        $id   = (int) $this->request->Param('id');
        $post = $this->postModel->findWithAuthor($id);

        if (!$post) {
            $this->response->notFound('Post introuvable');
            return;
        }

        $this->response->json($post);
    }

    // POST /api/posts
    public function create(): void
    {
        $this->requireAuth();

        $title   = $this->request->getBody('title');
        $content = $this->request->getBody('content');

        if (!$title || !$content) {
            $this->response->error('title et content sont requis', 422);
            return;
        }

        $id   = $this->postModel->insert([
            'title'   => $title,
            'content' => $content,
            'user_id' => $this->request->getAuthUserId(),
        ]);

        $post = $this->postModel->findWithAuthor($id);
        $this->response->created($post);
    }

    // PUT /api/posts/:id
    public function update(): void
    {
        $this->requireAuth();

        $id   = (int) $this->request->Param('id');
        $post = $this->postModel->find($id);

        if (!$post) {
            $this->response->notFound('Post introuvable');
            return;
        }

        if ($post['user_id'] !== $this->request->getAuthUserId()) {
            $this->response->error('Vous ne pouvez modifier que vos propres posts', 403);
            return;
        }

        $data = [];
        if ($title = $this->request->getBody('title')) {
            $data['title'] = $title;
        }
        if ($content = $this->request->getBody('content')) {
            $data['content'] = $content;
        }

        if (empty($data)) {
            $this->response->error('Aucune donnée à mettre à jour', 422);
            return;
        }

        $this->postModel->update($id, $data);
        $this->response->json($this->postModel->findWithAuthor($id));
    }

    // DELETE /api/posts/:id
    public function delete(): void
    {
        $this->requireAuth();

        $id   = (int) $this->request->Param('id');
        $post = $this->postModel->find($id);

        if (!$post) {
            $this->response->notFound('Post introuvable');
            return;
        }

        if ($post['user_id'] !== $this->request->getAuthUserId()) {
            $this->response->error('Vous ne pouvez supprimer que vos propres posts', 403);
            return;
        }

        $this->postModel->delete($id);
        $this->response->noContent();
    }
}
