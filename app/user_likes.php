<?php
require_once 'components/connect.php';
require_once 'components/functions.php';

session_start();

if (isset($_SESSION['user_id'])) {
  $user_id = $_SESSION['user_id'];
} else {
  $user_id = '';
  header('location:home.php');
}

include 'components/like_post.php';
?>
<!DOCTYPE html>
<html lang="ja">

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>プロフィールを更新</title>

  <!-- font awesome cdn link  -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

  <!-- custom css file link  -->
  <link rel="stylesheet" href="css/style.css">

</head>

<body>
  <?php include 'components/user_header.php'; ?>

  <section class="posts-container">
    <h1 class="heading">いいね</h1>
    <div class="box-container">
      <?php
      $select_likes = $conn->prepare("SELECT * FROM likes WHERE user_id = ?");
      $select_likes->execute([$user_id]);
      if ($select_likes->rowCount() > 0) {

        //いいねの件数が1以上の場合
        while ($fetch_like = $select_likes->fetch(PDO::FETCH_ASSOC)) {
          //いいねをした投稿記事を取得
          $select_posts = $conn->prepare("SELECT * FROM posts WHERE id = ?");
          $select_posts->execute([$fetch_like['post_id']]);

          if ($select_posts->rowCount() > 0) {
            while ($fetch_post = $select_posts->fetch(PDO::FETCH_ASSOC)) {
              if ($fetch_post['status'] != 'deactive') {

                //記事が公開されている場合
                $post_id = $fetch_post['id'];

                //いいねの件数を取得
                $count_post_likes = $conn->prepare("SELECT * FROM likes WHERE post_id = ?");
                $count_post_likes->execute([$post_id]);
                $total_post_likes = $count_post_likes->rowCount();

                //コメントの件数を取得
                $count_post_comments = $conn->prepare("SELECT * FROM comments WHERE post_id = ?");
                $count_post_comments->execute([$post_id]);
                $total_post_comments = $count_post_comments->rowCount();
      ?>
                <form method="POST" class="box">
                  <input type="hidden" name="post_id" value="<?php echo $post_id; ?>">
                  <input type="hidden" name="admin_id" value="<?php echo $fetch_post['admin_id']; ?>">
                  <div class="post-admin">
                    <i class="fas fa-user"></i>
                    <div>
                      <a href="author_posts.php?author=<?php echo $fetch_posts['name']; ?>"><?php echo $fetch_posts['name']; ?></a>
                      <div><?php echo $fetch_posts['date']; ?></div>
                    </div>
                  </div>
                  <?php if ($fetch_posts['image'] != '') : ?>
                    <img src="uploaded_img/<?php echo $fetch_post['image']; ?>" class="post-image" alt="">
                  <?php endif; ?>
                  <div class="post-title"><?php echo $fetch_posts['title']; ?></div>
                  <div class="post-content content-150"><?php echo $fetch_posts['content']; ?></div>
                  <a href="view_post.php?post_id=<?php echo $post_id ?>" class="inline-btn">もっと見る</a>
                  <div class="icons">
                    <a href="view_post.php?post_id=<?php echo $post_id; ?>"><i class="fas fa-comment"></i><span>(<?php echo $total_post_likes; ?>)</span></a>
                    <button type="submit" name="like_post"><i class="fas fa-heart" style="<?php if ($total_post_likes > 0 and $user_id != '') {
                                                                                            echo 'color:red;';
                                                                                          }; ?>"></i><span>(<?= $total_post_likes; ?>)</span></button>
                  </div>
                </form>
      <?php
              }
            }
          } else {
            echo '<p class="empty">このカテゴリに該当する記事はありません</p>';
          }
        }
      } else {
        echo '<p class="empty">いいね！を押した投稿はありません</p>';
      }
      ?>
    </div>
  </section>

  <?php include 'components/footer.php'; ?>
  <script src="js/script.js"></script>

</body>

</html>