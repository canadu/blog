<?php

require_once 'components/functions.php';
require_once 'components/connect.php';

session_start();

if (isset($_SESSION['user_id'])) {
  $user_id = $_SESSION['user_id'];
} else {
  $user_id = '';
};

require_once 'components/like_post.php';

$get_id =  $_GET['post_id'];

if (isset($_POST['add_comment'])) {

  $admin_id = h($_POST['admin_id']);
  $user_name = h($_POST['user_name']);
  $comment = h($_POST['comment']);

  $verify_comment = $conn->prepare("SELECT * FROM comments WHERE post_id = ? AND admin_id = ? AND user_id = ? AND user_name = ? AND comment = ?");
  $verify_comment->execute([$get_id, $admin_id, $user_id, $user_name, $comment]);

  if ($verify_comment->rowCount() > 0) {
    $message[] = 'コメントは既に追加されています';
  } else {
    $insert_comment = $conn->prepare("INSERT INTO comments(post_id, admin_id, user_id, user_name, comment) VALUES(?,?,?,?,?)");
    $insert_comment->execute([$get_id, $admin_id, $user_id, $user_name, $comment]);
    $message[] = "新しいコメントを追加しました";
  }
}

if (isset($_POST['edit_comment'])) {

  $edit_comment_id = h($_POST['edit_comment_id']);
  $comment_edit_box = h($_POST['comment_edit_box']);

  $verify_comment = $conn->prepare("SELECT * FROM comments WHERE comment = ? AND id = ?");
  $verify_comment->execute([$comment_edit_box, $edit_comment_id]);

  if ($verify_comment->rowCount() > 0) {
    $message[] = 'コメントは既に追加されています';
  } else {
    $update_comment = $conn->prepare("UPDATE comments SET comment = ? WHERE id = ?");
    $update_comment->execute([$comment_edit_box, $edit_comment_id]);
    $message[] = "コメントを修正しました";
  }
}

if (isset($_POST['delete_comment'])) {
  $delete_comment_id = h($_POST['comment_id']);
  $delete_comment = $conn->prepare("DELETE FROM comments WHERE id = ?");
  $delete_comment->execute([$delete_comment_id]);
  $message[] = 'コメントを削除しました。';
}

?>
<!DOCTYPE html>
<html lang="ja">

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>投稿を見る</title>

  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

  <link rel="stylesheet" href="css/style.css">
</head>

<body>

  <?php include 'components/user_header.php'; ?>
  <?php
  if (isset($_POST['open_edit_box'])) {
    $comment_id = h($_POST['comment_id']);
  ?>

    <section class="comment-edit-form">
      <p>あなたのコメントを編集してください</p>
      <?php
      $select_edit_comment = $conn->prepare("SELECT * FROM comments WHERE id = ?");
      $select_edit_comment->execute([$comment_id]);
      $fetch_edit_comment = $select_edit_comment->fetch(PDO::FETCH_ASSOC);
      ?>
      <form action="" method="POST">
        <input type="hidden" name="edit_comment_id" value="<?php echo $comment_id; ?>>">
        <textarea name="comment_edit_box" required cols="30" rows="10" placeholder="コメントを入力してください。"><?php echo $fetch_edit_comment['comment']; ?></textarea>
        <button type=="submit" class="inline-btn" name="edit_comment">コメントの編集</button>
        <div class="inline-option-btn" onclick="window.location.href = 'view_post.php?post_id=<?php echo $get_id; ?>';">キャンセル</div>
      </form>
    </section>

  <?php
  }
  ?>

  <section class=" posts-container" style="padding-bottom:0;">
    <div class="box-container">
      <?php
      $select_posts = $conn->prepare("SELECT * FROM posts WHERE status = ? AND id = ?");
      $select_posts->execute(['active', $get_id]);
      if ($select_posts->rowCount() > 0) {
        while ($fetch_posts = $select_posts->fetch(PDO::FETCH_ASSOC)) {

          //記事が公開されている場合
          $post_id = $fetch_posts['id'];

          //いいねの件数を取得
          $count_post_likes = $conn->prepare("SELECT * FROM likes WHERE post_id = ?");
          $count_post_likes->execute([$post_id]);
          $total_post_likes = $count_post_likes->rowCount();

          //コメントの件数を取得
          $count_post_comments = $conn->prepare("SELECT * FROM comments WHERE post_id = ?");
          $count_post_comments->execute([$post_id]);
          $total_post_comments = $count_post_comments->rowCount();

          $confirm_likes = $conn->prepare("SELECT * FROM likes WHERE user_id = ? AND post_id = ?");
          $confirm_likes->execute([$user_id, $post_id]);
      ?>

          <form method="POST" class="box">
            <input type="hidden" name="post_id" value="<?php echo $post_id; ?>">
            <input type="hidden" name="admin_id" value="<?php echo $fetch_posts['admin_id']; ?>">

            <div class="post-admin">
              <i class="fas fa-user"></i>
              <div>
                <a href="author_posts.php?author=<?php echo $fetch_posts['name']; ?>"><?php echo $fetch_posts['name']; ?></a>
                <div><?php echo $fetch_posts['date']; ?></div>
              </div>
            </div>

            <?php if ($fetch_posts['image'] != '') : ?>
              <img src="uploaded_img/<?php echo $fetch_posts['image']; ?>" class="post-image" alt="">
            <?php endif; ?>

            <div class="post-title"><?php echo $fetch_posts['title']; ?></div>
            <div class="post-content content-150"><?php echo $fetch_posts['content']; ?></div>
            <a href="view_post.php?post_id=<?php echo $post_id ?>" class="inline-btn">もっと見る</a>
            <div class="icons">
              <div><i class="fas fa-comment"></i><span>(<?= $total_post_comments; ?>)</span></div>
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
  <section class="comments-container">
    <p class="comment-title">コメントを追加</p>
    <?php
    if ($user_id != '') {
      $select_admin_id = $conn->prepare("SELECT * FROM posts WHERE id =?");
      $select_admin_id->execute([$get_id]);
      $fetch_admin_id = $select_admin_id->fetch(PDO::FETCH_ASSOC);
    ?>
      <form action="" method="POST" class="add-comment">
        <input type="hidden" name="admin_id" value="<?php echo $fetch_admin_id['admin_id']; ?>">
        <input type="hidden" name="user_name" value="<?php echo $fetch_profile['name']; ?>">
        <p class="user"><i class="fas fa-user"></i><a href="update.php"><?php echo $fetch_profile['name']; ?></a></p>
        <textarea name="comment" maxlength="1000" class="comment-box" cols="30" rows="10" placeholder="コメントを入力してください。" required></textarea>
        <input type="submit" value="コメントを追加" class="inline-btn" name="add_comment">
      </form>
    <?php
    } else {
    ?>
      <div class="add-comment">
        <p>追加、編集するにはログインしてください</p>
        <a href="login.php" class="inline-btn">ログイン</a>
      </div>
    <?php
    }
    ?>
    <p class="comment-title">コメントを投稿</p>
    <div class="user-comments-container">
      <?php
      $select_comments = $conn->prepare("SELECT * FROM comments WHERE post_id = ?");
      $select_comments->execute([$get_id]);
      if ($select_comments->rowCount() > 0) {
        while ($fetch_comments = $select_comments->fetch(PDO::FETCH_ASSOC)) {
      ?>
          <div class="show-comments" style="<?php if ($fetch_comments['user_id'] == $user_id) {
                                              echo 'order:-1;';
                                            } ?>">
            <div class="comment-user">
              <i class="fas fa-user"></i>
              <div>
                <span><?php echo $fetch_comments['user_name']; ?></span>
                <div><?php echo $fetch_comments['date']; ?></div>
              </div>
            </div>
            <div class="comment-box" style="<?php if ($fetch_comments['user_id'] == $user_id) {
                                              echo 'color:var(--white); background:var(--black);';
                                            } ?>"><?= $fetch_comments['comment']; ?></div>
            <?php
            if ($fetch_comments['user_id'] == $user_id) {
            ?>
              <form action="" method="POST">
                <input type="hidden" name="comment_id" value="<?php echo $fetch_comments['id']; ?>">
                <button type="submit" class="inline-option-btn" name="open_edit_box">コメント編集</button>
                <button type="submit" class="inline-delete-btn" name="delete_comment" onclick="return confirm('削除しますか?');">コメント削除</button>
              </form>
            <?php
            }
            ?>
          </div>
      <?php
        }
      } else {
        echo '<p class="empty">まだコメントはまだありません。</p>';
      }
      ?>
    </div>
  </section>
  <?php include 'components/footer.php'; ?>
  <script src=" js/script.js"></script>
</body>

</html>