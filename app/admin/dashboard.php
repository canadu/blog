<?php

require_once '../components/functions.php';
require_once '../components/connect.php';

session_start();
$admin_id = $_SESSION['admin_id'];
if (!isset($admin_id)) {
    header('location:admin_login.php');
}
?>
<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <!-- font awesome cdn link -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.2/css/all.min.css">
    <!-- custom css file link -->
    <link rel="stylesheet" href="../css/admin_style.css">
</head>

<body>
    <!-- header section start -->
    <?php include '../components/admin_header.php'; ?>
    <!-- header section ends -->

    <!-- dashboard section starts -->
    <section class="dashboard">
        <h1 class="heading">dashboard</h1>
        <div class="box-container">
            <div class="box">
                <h3>welcome</h3>
                <p><?php echo $fetch_profile['name']; ?></p>
                <a href="update_profile.php" class="btn">プロフィールを更新</a>
            </div>

            <!-- 投稿総数  -->
            <div class="box">
                <?php
                $select_posts = $conn->prepare("SELECT * FROM posts WHERE admin_id = :admin_id");
                $select_posts->bindValue(':admin_id', $admin_id, PDO::PARAM_INT);
                $select_posts->execute();
                $number_of_posts = $select_posts->rowCount();
                ?>
                <h3><?php echo $number_of_posts; ?></h3>
                <p>投稿総数</p>
                <a href="add_posts.php" class="btn">新規に投稿</a>
            </div>

            <!-- 公開されている投稿  -->
            <div class="box">
                <?php
                $select_active_posts = $conn->prepare("SELECT * FROM posts WHERE admin_id = :admin_id AND status = :status");
                $select_active_posts->bindValue(':admin_id', $admin_id, PDO::PARAM_INT);
                $select_active_posts->bindValue(':status', 'active', PDO::PARAM_STR);
                $select_active_posts->execute();
                $number_of_active_posts = $select_active_posts->rowCount();
                ?>
                <h3><?php echo $number_of_active_posts; ?></h3>
                <p>公開件数</p>
                <a href="view_posts.php?status=active" class="btn">公開されている投稿</a>
            </div>

            <!-- 非公開な投稿  -->
            <div class="box">
                <?php
                $select_deactive_posts = $conn->prepare("SELECT * FROM posts WHERE admin_id = :admin_id AND status = :status");
                $select_deactive_posts->bindValue(':admin_id', $admin_id, PDO::PARAM_INT);
                $select_deactive_posts->bindValue(':status', 'deactive', PDO::PARAM_STR);
                $select_deactive_posts->execute();
                $number_of_deactive_posts = $select_deactive_posts->rowCount();
                ?>
                <h3><?php echo $number_of_deactive_posts; ?></h3>
                <p>非公開件数</p>
                <a href="view_posts.php?status=deactive" class="btn">非公開の投稿</a>
            </div>

            <!-- ユーザーアカウント -->
            <div class="box">
                <?php
                $select_users = $conn->prepare("SELECT * FROM users");
                $select_users->execute();
                $number_of_users = $select_users->rowCount();
                ?>
                <h3><?php echo $number_of_users; ?></h3>
                <p>ユーザーアカウント</p>
                <a href="users_accounts.php" class="btn">ユーザーを確認</a>
            </div>

            <!-- 管理者アカウント  -->
            <div class="box">
                <?php
                $select_admins = $conn->prepare("SELECT * FROM admin");
                $select_admins->execute();
                $number_of_admins = $select_admins->rowCount();
                ?>
                <h3><?php echo $number_of_admins; ?></h3>
                <p>管理者アカウント</p>
                <a href="admin_accounts.php" class="btn">管理者を確認</a>
            </div>

            <!-- コメント  -->
            <div class="box">
                <?php
                $select_comments = $conn->prepare("SELECT * FROM comments WHERE admin_id = :admin_id");
                $select_comments->bindValue(':admin_id', $admin_id, PDO::PARAM_INT);
                $select_comments->execute();
                $number_of_comments = $select_comments->rowCount();
                ?>
                <h3><?php echo $number_of_comments; ?></h3>
                <p>総コメント数</p>
                <a href="comments.php" class="btn">コメントを確認</a>
            </div>
            <!-- いいね  -->
            <div class="box">
                <?php
                $select_likes = $conn->prepare("SELECT * FROM likes WHERE admin_id = :admin_id");
                $select_likes->bindValue(':admin_id', $admin_id, PDO::PARAM_INT);
                $select_likes->execute();
                $number_of_likes = $select_likes->rowCount();
                ?>
                <h3><?php echo $number_of_likes; ?></h3>
                <p>総いいね数</p>
                <a href="view_posts.php" class="btn">投稿を確認</a>
            </div>

        </div>
    </section>
    <!-- dashboard section ends -->

    <!-- custom js file link -->
    <script src="../js/admin_script.js"></script>
</body>

</html>