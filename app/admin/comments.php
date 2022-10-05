<?php

require_once '../components/functions.php';
require_once '../components/connect.php';

session_start();
$admin_id = $_SESSION['admin_id'];

if (!isset($admin_id)) {
    header('location:admin_login.php');
}

if (isset($_POST['delete_comment'])) {
    $comment_id = h($_POST['comment_id']);
    $delete_comment = $conn->prepare("DELETE FROM comments WHERE id = :id");
    $delete_comment->bindValue(':id', $comment_id, PDO::PARAM_INT);
    $delete_comment->execute();
    $message[] = 'コメントを削除しました。';
}

//管理者コメントの件数を取得
$select_comments = $conn->prepare("SELECT * FROM comments WHERE admin_id = :admin_id");
$select_comments->bindValue(':admin_id', $admin_id, PDO::PARAM_INT);
$select_comments->execute();
$count_comments = $select_comments->rowCount();

$comments = array();

if ($count_comments > 0) {

    while ($fetch_comments = $select_comments->fetch(PDO::FETCH_ASSOC)) {

        //コメントした投稿を取得
        $select_posts = $conn->prepare("SELECT * FROM posts WHERE id = :id");
        $select_posts->bindValue(':id', $fetch_comments['post_id'], PDO::PARAM_INT);
        $select_posts->execute();
        $fetch_posts = $select_posts->fetch(PDO::FETCH_ASSOC);

        // while ($fetch_posts = $select_posts->fetch(PDO::FETCH_ASSOC)) {
        //     $posts[] = ['id' => $fetch_posts['id'], 'title' => $fetch_posts['title']];
        // }

        $comments[] = [
            'post_id' => $fetch_posts['id'],
            'post_title' => $fetch_posts['title'],
            'id' => $fetch_comments['id'],
            'user_name' => $fetch_comments['user_name'],
            'date' => $fetch_comments['date'],
            'comment' => $fetch_comments['comment']
        ];
    }
}

include '../views/admin/view_comments.php';
