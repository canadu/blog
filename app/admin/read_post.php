<?php
include '../components/connect.php';

session_start();

$admin_id = $_SESSION['admin_id'];

if (!isset($admin_id)) {
    header('location:admin_login.php');
}

//投稿ID
$get_id = $_GET['post_id'];

if (isset($_POST['delete'])) {
    //削除処理
    $p_id = htmlspecialchars($_POST['post_id'], ENT_QUOTES);
    $delete_image = $conn->prepare("SELECT * FROM posts WHERE id = ?");
    $delete_image->execute([$p_id]);
    $fetch_delete_image = $delete_image->fetch(PDO::FETCH_ASSOC);
    if ($fetch_delete_image['image'] != '') {
        //ファイルを削除
        unlink('../uploaded_img/' . $fetch_delete_image['image']);
    }
    $delete_post = $conn->prepare("DELETE FROM posts WHERE id = ?");
    $delete_post->execute([$p_id]);
    $delete_comments = $conn->prepare("DELETE FROM comments WHERE post_id = ?");
    header('location:view_posts.php');
}

if (isset($_POST['delete_comment'])) {
    $comment_id = htmlspecialchars($_POST['comment_id'], ENT_QUOTES);
    $delete_comment = $conn->prepare("DELETE FROM `comments` WHERE id = ?");
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
    <title>投稿を読む</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

    <link rel="stylesheet" href="../css/admin_style.css">
</head>

<body>
    <?php include '../components/admin_header.php'; ?>
</body>
<section class="read-post">
    <?php
    //対象管理者の投稿を取得して表示する
    $select_posts = $conn->prepare("SELECT * FROM posts WHERE admin_id = ? AND id = ?");
    $select_posts->execute([$admin_id, $get_id]);
    if ($select_posts->rowCount() > 0) {
        while ($fetch_posts = $select_posts->fetch(PDO::FETCH_ASSOC)) {
            $post_id = $fetch_posts['id'];
            //コメントを取得
            $count_post_comments = $conn->prepare("SELECT * FROM comments WHERE post_id = ?");
            $count_post_comments->execute([$post_id]);
            $total_post_comments = $count_post_comments->rowCount();
            //いいねを取得
            $count_post_likes = $conn->prepare("SELECT * FROM likes WHERE post_id = ?");
            $count_post_likes->execute([$post_id]);
            $total_post_likes = $count_post_likes->rowCount();
    ?>
            <form method="post">
                <input type="hidden" name="post_id" value="<?php echo $post_id; ?>">
                <?php if ($fetch_posts['image'] != '') { ?>
                    <img src="../uploaded_img/<?php echo $fetch_posts['image']; ?>" class="image" alt="">
                <?php } ?>
                <div class="status" style="background-color:<?php if ($fetch_posts['status'] == 'active') {
                                                                echo 'limegreen';
                                                            } else {
                                                                echo 'coral';
                                                            }; ?>;"><?= $fetch_posts['status'] == 'active' ? '公開' : '非公開'; ?></div>
                <div class="title"><?= $fetch_posts['title']; ?></div>
                <div class="content"><?php echo $fetch_posts['content']; ?></div>
                <div class="icons">
                    <div class="likes"><i class="fas fa-heart"></i><span><?php echo $total_post_likes; ?></span></div>
                    <div class="comments"><i class="fas fa-comments"></i><span><?php echo $total_post_comments; ?></span></div>
                </div>
                <div class="flex-btn">
                    <a href="edit_post.php?id=<?php echo $post_id; ?>" class="inline-option-btn">編集</a>
                    <button type="submit" name="delete" class="inline-delete-btn" onclick="return confirm('この投稿を削除しますか？');">削除</button>
                    <a href="view_posts.php" class="inline-option-btn">戻る</a>
                </div>
            </form>
    <?php
        }
    } else {
        //投稿がない場合
        echo '<p class="empty">まだ投稿はありません。<a href="add_posts.php" class="btn" style="margin-top:1.5rem;">記事を投稿する</a></p>';
    }
    ?>
</section>

<!-- 記事に投稿されたコメント -->
<section class="comments" style="padding-top:0;">
    <p class="comment-title">投稿コメント</p>
    <div class="box-container">
        <?php
        $select_comments = $conn->prepare("SELECT * FROM comments WHERE post_id = ?");
        $select_comments->execute([$get_id]);
        if ($select_comments->rowCount() > 0) {
            while ($fetch_comments = $select_comments->fetch(PDO::FETCH_ASSOC)) {
        ?>
                <div class="box">
                    <div class="user">
                        <i class="fas fa-user"> </i>
                        <div class="user-info">
                            <span><?php echo $fetch_comments['user_name']; ?></span>
                            <div><?php echo $fetch_comments['date'] ?></div>
                        </div>
                    </div>
                    <div class="text"><?php echo $fetch_comments['comment']; ?></div>
                    <form action="" method="POST">
                        <input type="hidden" name="comment_id" value="<?php echo $fetch_comments['id']; ?>">
                        <button type="submit" class="inline-delete-btn" name="delete_comment" onclick="return confirm('コメントを削除しますか?');">コメント削除</button>
                    </form>
                </div>
    </div>
<?php
            }
        } else {
            echo '<p class="empty">まだコメントはありません</p>';
        }
?>
</section>

</html>