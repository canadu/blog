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
    <title>ユーザーアカウント</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

    <link rel="stylesheet" href="../css/admin_style.css">
</head>

<body>
    <?php include '../components/admin_header.php' ?>

    <section class="accounts">
        <h1 class="heading">ユーザーアカウント</h1>
        <div class="box-container">
            <?php
            $select_account = $conn->prepare("SELECT * FROM users");
            $select_account->execute();
            if ($select_account->rowCount() > 0) {
                while ($fetch_accounts = $select_account->fetch(PDO::FETCH_ASSOC)) {
                    $user_id = $fetch_accounts['id'];

                    $count_user_comments = $conn->prepare("SELECT * FROM comments WHERE user_id = :user_id");
                    $count_user_comments->bindValue(':user_id', $user_id, PDO::PARAM_INT);
                    $count_user_comments->execute();

                    $total_user_comments = $count_user_comments->rowCount();

                    $count_user_likes = $conn->prepare("SELECT * FROM likes WHERE user_id = :user_id");
                    $count_user_likes->bindValue(':user_id', $user_id, PDO::PARAM_INT);
                    $count_user_likes->execute();
                    $total_user_likes = $count_user_likes->rowCount();
            ?>
                    <div class="box">
                        <p>ユーザーID : <span><?php echo $user_id; ?></span></p>
                        <p>ユーザー名 : <span><?php echo $fetch_accounts['name']; ?></span></p>
                        <p>総コメント数 : <span><?php echo $total_user_comments; ?></span></p>
                        <p>総いいね数 : <span><?php echo $total_user_likes; ?></span></p>
                    </div>
            <?php
                }
            } else {
                echo '<p class="empty">利用ユーザーは居ません';
            }
            ?>
        </div>
    </section>
    <script src="../js/admin_script.js"></script>
</body>

</html>