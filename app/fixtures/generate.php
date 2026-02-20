<?php

require_once 'vendor/autoload.php';

/**
 * @return PDO
 * @throws PDOException
 */
function getDbConnexion(): PDO {
    $host = 'php-oop-exercice-db';
    $db = 'blog';
    $user = 'root';
    $password = 'password';

    $pdon = "mysql:host=$host;dbname=$db;charset=UTF8";

    return new PDO($pdon, $user, $password);
}

$pdo = getDbConnexion();

$faker = Faker\Factory::create();

$users = [];
$posts = [];
$comments = [];

for ($i = 0; $i < 10; $i++) {
    $user = [
        'name' => $faker->name,
        'email' => $faker->email,
    ];

    $user['password'] = password_hash($user['email'], PASSWORD_DEFAULT);

    $sql = 'INSERT INTO users (name, email, password) VALUES (:name, :email, :password)';
    $query = $pdo->prepare($sql);
    $query->execute($user);

    $user['id'] = $pdo->lastInsertId();

    $users[] = $user;
}

foreach($users as $user) {
    for ($i = 0; $i < random_int(0, 10); $i++) {
        $post = [
            'title' => $faker->sentence,
            'content' => $faker->paragraph,
            'user_id' => $user['id'],
        ];

        $sql = 'INSERT INTO posts (title, content, user_id) VALUES (:title, :content, :user_id)';
        $query = $pdo->prepare($sql);
        $query->execute($post);

        $post['id'] = $pdo->lastInsertId();

        $posts[] = $post;
    }
}


foreach($posts as $post) {
    for ($i = 0; $i < random_int(0, 10); $i++) {
        $comment = [
            'content' => $faker->paragraph,
            'post_id' => $post['id'],
            'user_id' => $faker->randomElement($users)['id'],
        ];

        $sql = 'INSERT INTO comments (content, post_id, user_id) VALUES (:content, :post_id, :user_id)';
        $query = $pdo->prepare($sql);
        $query->execute($comment);

        $comments[] = $comment;
    }
}