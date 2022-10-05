<?php

require_once '../components/functions.php';
require_once '../components/connect.php';
session_start();

$admin_id = $_SESSION['admin_id'];

if (!isset($admin_id)) {
    header('location:admin_login.php');
}

//投稿ID
$get_id = $_GET['post_id'];

if (isset($_POST['delete'])) {
    //削除処理
    $p_id = h($_POST['post_id']);

    $delete_image = $conn->prepare("SELECT * FROM posts WHERE id = :id");
    $delete_image->bindValue(':id', $p_id, PDO::PARAM_INT);
    $delete_image->execute();

    $fetch_delete_image = $delete_image->fetch(PDO::FETCH_ASSOC);
    if ($fetch_delete_image['image'] != '') {
        //ファイルを削除
        unlink('../uploaded_img/' . $fetch_delete_image['image']);
    }
    $delete_post = $conn->prepare("DELETE FROM posts WHERE id = :id");
    $delete_post->bindValue(':id', $p_id, PDO::PARAM_INT);
    $delete_post->execute();

    $delete_comments = $conn->prepare("DELETE FROM comments WHERE post_id = :post_id");
    $delete_comments->bindValue(':post_id', $p_id, PDO::PARAM_INT);
    $delete_comments->execute();
    header('location:view_posts.php');
}

if (isset($_POST['delete_comment'])) {
    $comment_id = h($_POST['comment_id']);
    $delete_comment = $conn->prepare("DELETE FROM `comments` WHERE id = :id");
    $delete_comment->bindValue(':id', $comment_id, PDO::PARAM_INT);
    $delete_comment->execute();
    $message[] = 'コメントを削除しました。';
}

//対象管理者の投稿を取得して表示する
$posts_data = array();

$select_posts = $conn->prepare("SELECT * FROM posts WHERE admin_id = :admin_id AND id = :id");
$select_posts->bindValue(':admin_id', $admin_id, PDO::PARAM_INT);
$select_posts->bindValue(':id', $get_id, PDO::PARAM_INT);
$select_posts->execute();

if ($select_posts->rowCount() > 0) {

    while ($fetch_posts = $select_posts->fetch(PDO::FETCH_ASSOC)) {

        $post_id = $fetch_posts['id'];

        //コメントを取得
        $count_post_comments = $conn->prepare("SELECT * FROM comments WHERE post_id = :post_id");
        $count_post_comments->bindValue(':post_id', $post_id, PDO::PARAM_INT);
        $count_post_comments->execute();

        $total_post_comments = $count_post_comments->rowCount();

        //いいねを取得
        $count_post_likes = $conn->prepare("SELECT * FROM likes WHERE post_id = :post_id");
        $count_post_likes->bindValue(':post_id', $post_id, PDO::PARAM_INT);
        $count_post_likes->execute();

        $total_post_likes = $count_post_likes->rowCount();

        $posts_data[] = [
            'post_id' => $post_id,
            'status' => $fetch_posts['status'],
            'image' => $fetch_posts['image'],
            'title' => $fetch_posts['title'],
            'content' => $fetch_posts['content'],
            'total_post_comments'  => $total_post_comments,
            'total_post_likes' => $total_post_likes
        ];
    }
}

//コメントデータの取得
$comments = array();
$select_comments = $conn->prepare("SELECT * FROM comments WHERE post_id = :post_id");
$select_comments->bindValue(':post_id', $get_id, PDO::PARAM_INT);
$select_comments->execute();
while ($fetch_comments = $select_comments->fetch(PDO::FETCH_ASSOC)) {
    $comments[] = [
        'id' => $fetch_comments['id'],
        'user_name' => $fetch_comments['user_name'],
        'date' => $fetch_comments['date'],
        'comment' => $fetch_comments['comment']
    ];
}
include '../views/admin/view_read_post.php';
