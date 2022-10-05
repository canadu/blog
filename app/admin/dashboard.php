<?php

require_once '../components/functions.php';
require_once '../components/connect.php';

session_start();
$admin_id = $_SESSION['admin_id'];
if (!isset($admin_id)) {
    header('location:admin_login.php');
}

// 投稿総数
$select_posts = $conn->prepare("SELECT * FROM posts WHERE admin_id = :admin_id");
$select_posts->bindValue(':admin_id', $admin_id, PDO::PARAM_INT);
$select_posts->execute();
$number_of_posts = $select_posts->rowCount();

// 公開されている投稿
$select_active_posts = $conn->prepare("SELECT * FROM posts WHERE admin_id = :admin_id AND status = :status");
$select_active_posts->bindValue(':admin_id', $admin_id, PDO::PARAM_INT);
$select_active_posts->bindValue(':status', 'active', PDO::PARAM_STR);
$select_active_posts->execute();
$number_of_active_posts = $select_active_posts->rowCount();

// 非公開な投稿
$select_deactive_posts = $conn->prepare("SELECT * FROM posts WHERE admin_id = :admin_id AND status = :status");
$select_deactive_posts->bindValue(':admin_id', $admin_id, PDO::PARAM_INT);
$select_deactive_posts->bindValue(':status', 'deactive', PDO::PARAM_STR);
$select_deactive_posts->execute();
$number_of_deactive_posts = $select_deactive_posts->rowCount();

//ユーザーアカウント
$select_users = $conn->prepare("SELECT * FROM users");
$select_users->execute();
$number_of_users = $select_users->rowCount();

//管理者アカウント
$select_admins = $conn->prepare("SELECT * FROM admin");
$select_admins->execute();
$number_of_admins = $select_admins->rowCount();

//コメント
$select_comments = $conn->prepare("SELECT * FROM comments WHERE admin_id = :admin_id");
$select_comments->bindValue(':admin_id', $admin_id, PDO::PARAM_INT);
$select_comments->execute();
$number_of_comments = $select_comments->rowCount();

//総いいね
$select_likes = $conn->prepare("SELECT * FROM likes WHERE admin_id = :admin_id");
$select_likes->bindValue(':admin_id', $admin_id, PDO::PARAM_INT);
$select_likes->execute();
$number_of_likes = $select_likes->rowCount();

include '../views/admin/view_dashboard.php';
