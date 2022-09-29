<?php

require_once '../components/functions.php';
require_once '../components/connect.php';

session_start();
$admin_id = $_SESSION['admin_id'];
if (!isset($admin_id)) {
    header('location:admin_login.php');
}

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
    $message[] = '投稿を削除しました。';
}

?>
<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ページ検索</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

    <link rel="stylesheet" href="../css/admin_style.css">
</head>

<body>

    <?php include '../components/admin_header.php' ?>
    <section class="show-posts">
        <h1 class="heading">投稿</h1>
        <form action="search_page.php" method="POST" class="search-form">
            <input type="text" placeholder="検索" maxlength="100" name="search_box">
            <button class="fas fa-search" name="search_btn"></button>
        </form>
        <div class="box-container">
            <?php

            if (isset($_POST['search_box']) or isset($_POST['search_btn'])) {

                $search_box = $_POST['search_box'];
                //対象管理者の投稿を取得して表示する
                if (empty($search_box)) {
                    $select_posts = $conn->prepare("SELECT * FROM posts WHERE admin_id = ?");
                } else {
                    $select_posts = $conn->prepare("SELECT * FROM posts WHERE admin_id = ? and title LIKE " . "'%{$search_box}%'");
                }
                $select_posts->execute([$admin_id]);

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
                        <form method="post" class="box">
                            <input type="hidden" name="post_id" value="<?php echo $post_id; ?>">
                            <?php if ($fetch_posts['image'] != '') : ?>
                                <img src="../uploaded_img/<?php echo $fetch_posts['image']; ?>" class="image" alt="">
                            <?php endif; ?>
                            <div class="status" style="background-color:<?php if ($fetch_posts['status'] == 'active') {
                                                                            echo '#FFC107';
                                                                        } else {
                                                                            echo '#6C757D';
                                                                        }; ?>;"><?= $fetch_posts['status'] == 'active' ? '公開' : '非公開'; ?></div>
                            <div class="title"><?= $fetch_posts['title']; ?></div>
                            <div class="posts-content"><?php echo $fetch_posts['content']; ?></div>
                            <div class="icons">
                                <div class="likes"><i class="fas fa-heart"></i><span><?php echo $total_post_likes; ?></span></div>
                                <div class="comments"><i class="fas fa-comments"></i><span><?php echo $total_post_comments; ?></span></div>
                            </div>
                            <div class="flex-btn">
                                <a href="edit_post.php?id=<?php echo $post_id; ?>" class="option-btn">編集</a>
                                <button type="submit" name="delete" class="delete-btn" onclick="return confirm('この投稿を削除しますか？');">削除</button>
                            </div>
                            <a href="read_post.php?post_id=<?php echo $post_id; ?>" class="btn">投稿を見る</a>
                        </form>
            <?php
                    }
                } else {
                    //投稿がない場合
                    echo '<p class="empty">該当する投稿はありません。<a href="add_posts.php" class="btn" style="margin-top:1.5rem;">記事を投稿する</a></p>';
                }
            }

            ?>
        </div>
    </section>
    <!-- custom js file link  -->
    <script src="../js/admin_script.js"></script>
</body>

</html>