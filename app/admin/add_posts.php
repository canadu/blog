<?php

require_once '../components/functions.php';
require_once '../components/connect.php';
session_start();

$admin_id = $_SESSION['admin_id'];

$message = [];
if (!isset($admin_id)) {
    header('location:admin_login.php');
}

if (isset($_POST['publish'])) {
    //投稿
    Post($admin_id, STATUS[0], $conn);
}

if (isset($_POST['draft'])) {
    //下書き
    Post($admin_id, STATUS[1], $conn);
}

function Post(string $id, string $param_status, PDO $conn)
{
    global $message;
    $name = h($_POST['name']);
    $title = h($_POST['title']);
    $content = h($_POST['content']);
    $category = h($_POST['category']);
    $status = $param_status;
    $result = false;
    $generateImageName = '';

    if (!empty($_FILES['image']['name'])) {
        list($result, $errMessage) = validateImage();
        if ($result !== true) {
            $message[] = $errMessage;
        } else {
            $image = htmlspecialchars($_FILES['image']['name'], ENT_QUOTES);
            $generateImageName = generateImageName($image);
            $imagePath = '../uploaded_img/' . $generateImageName;
            move_uploaded_file($_FILES['image']['tmp_name'], $imagePath);
        }
    }
    if ($result) {
        $insert_post = $conn->prepare("INSERT INTO posts(admin_id, name, title, content, category, image, status) VALUES(:admin_id, :name, :title, :content, :category, :image, :status)");
        $insert_post->bindValue(':admin_id', $id, PDO::PARAM_INT);
        $insert_post->bindValue(':name', $name, PDO::PARAM_STR);
        $insert_post->bindValue(':title', $title, PDO::PARAM_STR);
        $insert_post->bindValue(':content', $content, PDO::PARAM_STR);
        $insert_post->bindValue(':category', $category, PDO::PARAM_STR);
        $insert_post->bindValue(':image', $generateImageName, PDO::PARAM_STR);
        $insert_post->bindValue(':status', $status, PDO::PARAM_STR);
        $insert_post->execute();
        $message[] = $param_status == STATUS[0]  ? '投稿しました。' : '下書きに保存しました。';
    }
}
include '../views/admin/view_add_posts.php';
