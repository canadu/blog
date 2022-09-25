<?php
require_once 'components/connect.php';
require_once 'components/functions.php';

session_start();

if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
} else {
    $user_id = '';
}

include 'components/like_post.php';

?>

<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
</head>

<body>
    <?php include 'components/user_header.php'; ?>

    <section class="authors">
        <h1 class="heading">管理者</h1>
        <div class="box-container">
            <?php
            $select_author = $conn->prepare("SELECT * FROM admin");
            $select_author->execute();
            if ($select_author->rowCount() > 0) {
                while ($fetch_authors = $select_author->fetch(PDO::FETCH_ASSOC)) {

                    //記事を取得
                    $count_admin_posts = $conn->prepare("SELECT * FROM posts WHERE admin_id = ? AND status = ?");
                    $count_admin_posts->execute([$fetch_authors['id'], 'active']);
                    $total_admin_posts = $count_admin_posts->rowCount();

                    //いいねの件数を取得
                    $count_admin_likes = $conn->prepare("SELECT * FROM likes WHERE admin_id = ?");
                    $count_admin_likes->execute([$fetch_authors['id']]);
                    $total_admin_likes = $count_admin_likes->rowCount();

                    //コメントの件数を取得
                    $count_admin_comments = $conn->prepare("SELECT * FROM comments WHERE admin_id = ?");
                    $count_admin_comments->execute([$fetch_authors['id']]);
                    $total_admin_comments = $count_post_comments->rowCount();

            ?>
                    <div class="box">
                        <p>管理者：<span><?php echo $fetch_authors['name']; ?></span></p>
                        <p>総投稿数：<span><?php echo $total_admin_posts; ?></span></p>
                        <p>いいね：<span><?php echo $total_admin_likes; ?></span></p>
                        <p>コメント：<span><?php echo $total_admin_comments ?></span></p>
                        <a href="author_posts.php?author=<?= $fetch_authors['name']; ?>" class="btn">投稿を見る</a>
                    </div>
            <?php
                }
            } else {
                echo '<p class="empty">まだ投稿はありません。</p>';
            }
            ?>
        </div>
    </section>
    <?php include 'components/footer.php'; ?>
    <script src="js/script.js"></script>
</body>

</html>