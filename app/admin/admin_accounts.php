<?php

require_once '../components/functions.php';
require_once '../components/connect.php';
session_start();
$admin_id = $_SESSION['admin_id'];

if (!isset($admin_id)) {
    header('location:admin_login.php');
}

if (isset($_POST['delete'])) {
    $delete_image = $conn->prepare("SELECT * FROM posts WHERE admin_id = ?");
    $delete_image->execute([$admin_id]);
    while ($fetch_delete_image = $delete_image->fetch(PDO::FETCH_ASSOC)) {
        //画像削除
        unlink('../uploaded_img/' . $fetch_delete_image['image']);
    }
    $delete_posts = $conn->prepare("DELETE FROM posts WHERE admin_id = ?");
    $delete_posts->execute([$admin_id]);

    $delete_likes = $conn->prepare("DELETE FROM likes WHERE admin_id = ?");
    $delete_likes->execute([$admin_id]);

    $delete_comments = $conn->prepare("DELETE FROM comments WHERE admin_id = ?");
    $delete_comments->execute([$admin_id]);

    $delete_admin = $conn->prepare("DELETE FROM admin WHERE admin_id = ?");
    $delete_admin->execute([$admin_id]);
    header('location:../components/admin_logout.php');
}

//管理者アカウントを取得して表示する
$select_account = $conn->prepare("SELECT * FROM admin");
$select_account->execute();
$admin_count =  $select_account->rowCount();
$admin_posts = array();

if ($admin_count > 0) {
    //投稿件数を取得する
    while ($fetch_accounts = $select_account->fetch(PDO::FETCH_ASSOC)) {
        $count_admin_posts = $conn->prepare("SELECT * FROM posts WHERE admin_id = :admin_id");
        $count_admin_posts->bindValue(':admin_id', $fetch_accounts['id'], PDO::PARAM_INT);
        $count_admin_posts->execute();
        $total_admin_posts = $count_admin_posts->rowCount();
        $admin_posts[] = ['id' => $fetch_accounts['id'], 'name' => $fetch_accounts['name'], 'total' => $total_admin_posts];
    }
}

include '../views/admin/view_admin_accounts.php';
