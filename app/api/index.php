<?php

session_start();

require_once __DIR__ . '/../vendor/autoload.php';

use App\Core\Request;
use App\Core\Response;
use App\Core\Router;
use App\Controllers\AuthController;
use App\Controllers\UserController;
use App\Controllers\PostController;
use App\Controllers\CommentController;

$request  = new Request();
$response = new Response();
$router   = new Router();

// ── Auth ──────────────────────────────────────────────
$router->post('/api/auth/register', function (Request $req, Response $res) {
    (new AuthController($req, $res))->register();
});
$router->post('/api/auth/login', function (Request $req, Response $res) {
    (new AuthController($req, $res))->login();
});
$router->post('/api/auth/logout', function (Request $req, Response $res) {
    (new AuthController($req, $res))->logout();
});
$router->get('/api/auth/me', function (Request $req, Response $res) {
    (new AuthController($req, $res))->me();
});

// ── Users ─────────────────────────────────────────────
$router->get('/api/users', function (Request $req, Response $res) {
    (new UserController($req, $res))->index();
});
$router->get('/api/users/:id', function (Request $req, Response $res) {
    (new UserController($req, $res))->show();
});
$router->put('/api/users/:id', function (Request $req, Response $res) {
    (new UserController($req, $res))->update();
});

// ── Posts ─────────────────────────────────────────────
$router->get('/api/posts', function (Request $req, Response $res) {
    (new PostController($req, $res))->index();
});
$router->get('/api/posts/:id', function (Request $req, Response $res) {
    (new PostController($req, $res))->show();
});
$router->post('/api/posts', function (Request $req, Response $res) {
    (new PostController($req, $res))->create();
});
$router->put('/api/posts/:id', function (Request $req, Response $res) {
    (new PostController($req, $res))->update();
});
$router->delete('/api/posts/:id', function (Request $req, Response $res) {
    (new PostController($req, $res))->delete();
});

// ── Comments ──────────────────────────────────────────
$router->get('/api/posts/:postId/comments', function (Request $req, Response $res) {
    (new CommentController($req, $res))->index();
});
$router->post('/api/posts/:postId/comments', function (Request $req, Response $res) {
    (new CommentController($req, $res))->create();
});
$router->delete('/api/posts/:postId/comments/:id', function (Request $req, Response $res) {
    (new CommentController($req, $res))->delete();
});

// ── Dispatch ──────────────────────────────────────────
$router->dispatch($request, $response);
