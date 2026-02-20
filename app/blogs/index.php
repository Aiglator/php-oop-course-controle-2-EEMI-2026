<?php
session_start();

require_once __DIR__ . '/../vendor/autoload.php';

use App\Models\Post;
use App\Models\Comment;

$postModel    = new Post();
$commentModel = new Comment();
$postId       = (int) ($_GET['id'] ?? 0);
$post         = $postModel->findWithAuthor($postId);

if (!$post) {
    http_response_code(404);
    echo 'Post introuvable';
    exit;
}

function isLoggedIn(): bool {
    return isset($_SESSION['user_id']);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isLoggedIn()) {
    $content = $_POST['comment'] ?? '';
    if ($content) {
        $commentModel->insert([
            'content' => $content,
            'post_id' => $postId,
            'user_id' => $_SESSION['user_id'],
        ]);
    }
    header('Location: /blogs/index.php?id=' . $postId);
    exit;
}

$comments = $commentModel->findByPost($postId);
?>

<!doctype html>
<html class="h-full">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body  class="min-h-full">
    <main class="min-h-full">
        <div class="flex flex-col min-h-full w-full items-center justify-start">
            <div class="flex flex-row w-full h-24 bg-gray-900 items-center justify-center">
                <div class="w-11/12 flex flex-row items-center justify-end space-x-4">
                    <a href="/" class="text-white">Homepage</a>
                    <?php if (isLoggedIn()): ?>
                        <a href="/blogs/new.php" class="text-white">Create post</a>
                        <a href="/profile.php" class="text-white">Profile</a>
                        <a href="/logout.php" class="text-white">Logout</a>
                    <?php else: ?>
                        <a href="/login.php"  class="text-white">Login</a>
                        <a href="/register.php"  class="text-white">Register</a>
                    <?php endif; ?>
                </div>
            </div>
            <div class="flex flex-col w-11/12 items-center justify-start">
                <h1 class="text-4xl"><?= $post['title'] ?></h1>
                <a href="/users.php?id=<?= $post['user_id'] ?>" class="p">By <?= $post['author_name'] ?></a>

                <div class="flex flex-col w-full items-center justify-start space-y-4">
                    <p><?= $post['content'] ?></p>
                    <h2 class="text-2xl">Comments</h2>
                    <?php if (isLoggedIn()): ?>
                        <form action="/blogs/index.php?id=<?= $postId ?>" method="post" class="flex flex-col w-1/2 space-y-4">
                            <input type="text" name="comment" placeholder="Comment" class="p-2 border border-gray-300 rounded">
                            <button type="submit" class="p-2 bg-blue-500 text-white rounded">Comment</button>
                        </form>
                    <?php endif; ?>
                    <?php foreach($comments as $comment): ?>
                        <div class="flex flex-col w-full items-center justify-start border border-gray-300 p-4">
                            <a href="/users.php?id=<?= $comment['user_id'] ?>" class="p">By <?= $comment['commenter_name'] ?></a>
                            <p><?= $comment['content'] ?></p>
                        </div>
                    <?php endforeach; ?>
                </div>

            </div>
        </div>
    </main>
</body>
</html>
