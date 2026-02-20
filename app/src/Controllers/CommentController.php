<?php

namespace App\Controllers;

use App\Core\Request;
use App\Core\Response;
use App\Models\Comment;
use App\Models\Post;

class CommentController extends Controller
{
    private Comment $commentModel;
    private Post    $postModel;

    public function __construct(Request $request, Response $response)
    {
        parent::__construct($request, $response);
        $this->commentModel = new Comment();
        $this->postModel    = new Post();
    }

    // GET /api/posts/:postId/comments
    public function index(): void
    {
        $postId = (int) $this->request->Param('postId');

        if (!$this->postModel->find($postId)) {
            $this->response->notFound('Post introuvable');
            return;
        }

        $comments = $this->commentModel->findByPost($postId);
        $this->response->json($comments);
    }

    // POST /api/posts/:postId/comments
    public function create(): void
    {
        $this->requireAuth();

        $postId  = (int) $this->request->Param('postId');
        $content = $this->request->getBody('content');

        if (!$this->postModel->find($postId)) {
            $this->response->notFound('Post introuvable');
            return;
        }

        if (!$content) {
            $this->response->error('content est requis', 422);
            return;
        }

        $id = $this->commentModel->insert([
            'content' => $content,
            'post_id' => $postId,
            'user_id' => $this->request->getAuthUserId(),
        ]);

        $comment = $this->commentModel->find($id);
        $this->response->created($comment);
    }

    // DELETE /api/posts/:postId/comments/:id
    public function delete(): void
    {
        $this->requireAuth();

        $id      = (int) $this->request->Param('id');
        $comment = $this->commentModel->find($id);

        if (!$comment) {
            $this->response->notFound('Commentaire introuvable');
            return;
        }

        if ($comment['user_id'] !== $this->request->getAuthUserId()) {
            $this->response->error('Vous ne pouvez supprimer que vos propres commentaires', 403);
            return;
        }

        $this->commentModel->delete($id);
        $this->response->noContent();
    }
}
