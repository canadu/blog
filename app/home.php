<?php

require_once 'components/functions.php';
require_once 'components/connect.php';

session_start();

if (isset($_SESSION['user_id'])) {
  $user_id = $_SESSION['user_id'];
} else {
  $user_id = '';
}

require_once 'components/like_post.php';

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
  <section class="home-grid">
    <div class="box-container">
      <div class="box">
        <?php
        $select_profile = $conn->prepare("SELECT * FROM users WHERE id =?");
        $select_profile->execute([$user_id]);
        if ($select_profile->rowCount() > 0) {
          $fetch_profile = $select_profile->fetch(PDO::FETCH_ASSOC);

          // ユーザーのコメント
          $count_user_comments = $conn->prepare("SELECT * FROM comments WHERE user_id = ?");
          $count_user_comments->execute([$user_id]);
          $total_user_comments = $count_user_comments->rowCount();

          // ユーザーのいいね
          $count_user_likes = $conn->prepare("SELECT * FROM likes WHERE user_id = ?");
          $count_user_likes->execute([$user_id]);
          $total_user_likes = $count_user_likes->rowCount();

        ?>
          <p>Welcome <span><?php echo $fetch_profile['name']; ?></span></p>
          <p>総コメント : <span><?php echo $total_user_comments; ?></span></p>
          <p>総いいね : <span><?php echo $total_user_likes; ?></span></p>
          <a href="update.php" class="btn">プロフィールを更新</a>
          <div class="flex-btn">
            <a href="user_likes.php" class="option_btn">いいね</a>
            <a href="user_comments.php" class="option_btn">コメント</a>
          </div>
        <?php
        } else {
        ?>
          <p class="name">ログイン or 登録</p>
          <div class="flex-btn">
            <a href="login.php" class="option-btn">ログイン</a>
            <a href="register.php" class="option-btn">登録</a>
          </div>
        <?php
        }
        ?>
      </div>

      <div class="box">
        <p>カテゴリー</p>
        <div class="flex-box">
          <?php foreach ($category_array as $key => $value) { ?>
            <a href="category.php?category=<?php echo $key; ?>" class="links"><?php echo $value; ?></a>
          <?php } ?>
          <a href="all_category.php" class="btn">全て見る</a>
        </div>
      </div>

      <div class="box">
        <p>著者</p>
        <div class="flex-box">
          <?php
          $select_authors = $conn->prepare("SELECT DISTINCT name FROM admin LIMIT 10");
          $select_authors->execute();
          if ($select_authors->rowCount() > 0) {
            while ($fetch_authors = $select_authors->fetch(PDO::FETCH_ASSOC)) {
          ?>
              <a href="author_posts.php?author=<?php echo $fetch_authors['name']; ?>" class="links"><?php echo $fetch_authors['name']; ?></a>
          <?php
            }
          } else {
            echo '<p class="empty">まだ投稿はありません。</p>';
          }
          ?>
          <a href="authors.php" class="btn">全て見る</a>
        </div>
      </div>
    </div>
  </section>

  <section class="posts-container">
    <h1 class="heading">最近の投稿</h1>
    <div class="box-container">
      <?php
      $select_posts = $conn->prepare("SELECT * FROM posts WHERE status =? LIMIT 6");
      $select_posts->execute(['active']);

      if ($select_posts->rowCount() > 0) {

        while ($fetch_posts = $select_posts->fetch(PDO::FETCH_ASSOC)) {;

          $post_id = $fetch_posts['id'];

          $count_post_comments = $conn->prepare("SELECT * FROM comments WHERE post_id = ?");
          $count_post_comments->execute([$post_id]);
          $total_post_comments = $count_post_comments->rowCount();

          $count_post_likes = $conn->prepare("SELECT * FROM likes WHERE post_id = ?");
          $count_post_likes->execute([$post_id]);
          $total_post_likes = $count_post_likes->rowCount();

          $confirm_likes = $conn->prepare("SELECT * FROM likes WHERE user_id = ? AND post_id = ?");
          $confirm_likes->execute([$user_id, $post_id]);

      ?>
          <form action="" class="box" method="post">
            <input type="hidden" name="post_id" value="<?php echo $post_id; ?>">
            <input type="hidden" name="admin_id" value="<?php echo $fetch_posts['admin_id']; ?>">
            <div class="post-admin">
              <i class="fas fa-user"></i>
              <div>
                <a href="author_posts.php?author=<?php echo $fetch_authors['name']; ?>"><?php echo $fetch_authors['name']; ?></a>
                <div><?php echo $fetch_posts['date']; ?></div>
              </div>
            </div>
            <?php if ($fetch_posts['image'] != '') : ?>
              <img src="uploaded_img/<?php echo $fetch_posts['image']; ?>" class="post-image" alt="">
            <?php endif; ?>
            <div class="post-title"><?php echo $fetch_posts['title']; ?></div>
            <div class="post-content content-150"><?php echo $fetch_posts['content']; ?></div>
            <a href="view_post.php?post_id=<?php echo $post_id; ?>" class="inline-btn">続きを読む</a>
            <a href="category.php?category=<?php echo $fetch_posts['category']; ?>" class="post-cat"><i class="fas fa-tag"></i><span><?php echo $fetch_posts['category']; ?></span></span></a>
            <div class="icons">
              <!-- コメント -->
              <a href="view_post.php?post_id=<?= $post_id; ?>"><i class="fas fa-comment"></i><span>(<?= $total_post_comments; ?>)</span></a>
              <!-- いいねボタン -->
              <button type="submit" name="like_post"><i class="fas fa-heart" style="<?php if ($confirm_likes->rowCount() > 0) {
                                                                                      echo 'color:var(--red);';
                                                                                    } ?>  "></i><span>(<?= $total_post_likes; ?>)</span></button>
            </div>
          </form>
      <?php
        }
      } else {
        echo '<p class="empty">まだ投稿はありません。</p>';
      }
      ?>
    </div>
  </section>
</body>

</html>