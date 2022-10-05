<?php

require_once '../components/functions.php';
require_once '../components/connect.php';

session_start();

$admin_id = $_SESSION['admin_id'];

if (!isset($admin_id)) {
    header('location:admin_login.php');
}

if (isset($_POST['save'])) {
    //編集
    $post_id = $_GET['id'];
    $title = h($_POST['title']);
    $content = h($_POST['content']);
    $category = h($_POST['category']);
    $status = h($_POST['status']);

    $update_post = $conn->prepare("UPDATE posts SET title=:title, content=:content, category=:category, status=:status WHERE id=:id");

    $update_post->bindValue(':title', $title, PDO::PARAM_STR);
    $update_post->bindValue(':content', $content, PDO::PARAM_STR);
    $update_post->bindValue(':category', $category, PDO::PARAM_STR);
    $update_post->bindValue(':status', $status, PDO::PARAM_STR);
    $update_post->bindValue(':id', $post_id, PDO::PARAM_INT);
    $update_post->execute();

    $message[] = '更新しました。';

    $old_image = $_POST['old_image'];
    $image = h($_FILES['image']['name']);
    $image_size = $_FILES['image']['size'];
    $image_tmp_name = $_FILES['image']['tmp_name'];
    $image_folder = '../uploaded_img/' . $image;
    $select_image = $conn->prepare("SELECT * FROM posts WHERE image = ? AND admin_id = ?");
    $select_image->execute([$image, $admin_id]);

    if (!empty($image)) {
        if ($image_size > 2000000) {
            $message[] = '画像サイズが大きすぎます。';
        } elseif ($select_image->rowCount() > 0 and $image != '') {
            $message[] = '画像ファイル名が同じです。';
        } else {
            $update_image = $conn->prepare("UPDATE posts SET image = ? WHERE id = ?");
            move_uploaded_file($image_tmp_name, $image_folder);
            $update_image->execute([$image, $post_id]);
            if ($old_image != $image and $old_image != '') {
                unlink('../uploaded_img/' . $old_image);
            }
            $message[] = '画像を更新しました。';
        }
    }
}

if (isset($_POST['delete_post'])) {
    //削除
    $post_id = htmlspecialchars($_POST['post_id'], ENT_QUOTES);
    $delete_image = $conn->prepare("SELECT * FROM posts WHERE id = :id");
    $delete_image->bindValue(':id', $post_id, PDO::PARAM_INT);
    $delete_image->execute();

    $fetch_delete_image = $delete_image->fetch(PDO::FETCH_ASSOC);
    if ($fetch_delete_image['image'] != '') {
        unlink('../uploaded_img/' . $fetch_delete_image['image']);
    }

    $delete_post = $conn->prepare("DELETE FROM posts WHERE id = :id");
    $delete_post->bindValue(':id', $post_id, PDO::PARAM_INT);
    $delete_post->execute();

    $delete_comments = $conn->prepare("DELETE FROM comments WHERE post_id = :post_id");
    $delete_comments->bindValue(':post_id', $post_id, PDO::PARAM_INT);
    $delete_comments->execute();
    $message[] = '投稿を削除しました。';
}

if (isset($_POST['delete_image'])) {
    //画像削除
    $empty_image = '';
    $post_id = htmlspecialchars($_POST['post_id'], ENT_QUOTES);

    $delete_image = $conn->prepare("SELECT * FROM posts WHERE id = :id");
    $delete_image->bindValue(':id', $post_id, PDO::PARAM_INT);
    $delete_image->execute();

    $fetch_delete_image = $delete_image->fetch(PDO::FETCH_ASSOC);
    if ($fetch_delete_image['image'] != '') {
        unlink('../uploaded_img/' . $fetch_delete_image['image']);
    }

    $unset_image = $conn->prepare('UPDATE posts SET image = :image WHERE id = :id');
    $unset_image->bindValue(':image', $empty_image, PDO::PARAM_STR);
    $unset_image->bindValue(':id', $post_id, PDO::PARAM_INT);
    $unset_image->execute();

    $message[] = '画像を削除しました。';
}

$post_id = $_GET['id'];

$posts_data = array();
$select_posts = $conn->prepare("SELECT * FROM posts WHERE id = :id");
$select_posts->bindValue(':id', $post_id, PDO::PARAM_INT);
$select_posts->execute();
while ($fetch_posts = $select_posts->fetch(PDO::FETCH_ASSOC)) {
    $posts_data[] = [
        'id' => $fetch_posts['id'],
        'status' => $fetch_posts['status'],
        'image' => $fetch_posts['image'],
        'title' => $fetch_posts['title'],
        'content' => $fetch_posts['content'],
        'category' => $fetch_posts['category'],
    ];
}
include '../views/admin/view_edit_post.php';
