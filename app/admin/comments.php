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
    $delete_comment = $conn->prepare("DELETE FROM comments WHERE id = ?");
    $delete_comment->execute([$comment_id]);
    $message[] = 'コメントを削除しました。';
}

?>

<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>コメントページ</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

    <link rel="stylesheet" href="../css/admin_style.css">
</head>

<body>
    <?php include '../components/admin_header.php' ?>
    <section class="comments">
        <h1 class="heading">投稿コメント</h1>
        <p class="comment-title">投稿コメント</p>
        <div class="box-container">
            <?php
            $select_comments = $conn->prepare("SELECT * FROM comments WHERE admin_id = ?");
            $select_comments->execute([$admin_id]);
            if ($select_comments->rowCount() > 0) {
                while ($fetch_comments = $select_comments->fetch(PDO::FETCH_ASSOC)) {
                    $select_posts = $conn->prepare("SELECT * FROM posts WHERE id = ?");
                    $select_posts->execute([$fetch_comments['post_id']]);
                    while ($fetch_posts = $select_posts->fetch(PDO::FETCH_ASSOC)) {
            ?>
                        <div class="post-title">from : <span><?php echo $fetch_posts['title']; ?></span><a href="read_post.php?post_id=<?php echo $fetch_posts['id']; ?>">投稿を閲覧</a>
                        <?php
                    }
                        ?>
                        <div class="box">
                            <div class="user">
                                <i class="fas fa-user"></i>
                                <div class="user-info">
                                    <span><?php echo $fetch_comments['user_name']; ?></span>
                                    <span><?php echo $fetch_comments['date']; ?></span>
                                </div>
                            </div>
                            <div class="text"><?php echo $fetch_comments['comment'] ?></div>
                            <form action="" method="POST">
                                <input type="hidden" name="comment_id" value="<?php echo $fetch_comments['id']; ?>">
                                <button type="submit" class="inline-delete-btn" name="delete_comment" onclick="return confirm('コメントを削除しますか?');">コメント削除</button>
                            </form>
                        </div>
                <?php
                }
            } else {
                echo '<p class="empty">コメントはまだありません</p>';
            }
                ?>
                        </div>
    </section>

    <!-- admins accounts section ends -->
    <script src="../js/admin_script.js"></script>
</body>

</html>