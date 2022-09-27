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

if (isset($_POST['edit_comment'])) {
  $edit_comment_id = h($_POST['edit_comment_id']);
  $comment_edit_box = h($_POST['comment_edit_box']);
  $verify_comment = $conn->prepare("SELECT * FROM comments WHERE comment = ? AND id =?");
  $verify_comment->execute([$comment_edit_box, $edit_comment_id]);

  if ($verify_comment->rowCount() > 0) {
    $message[] = 'コメントは既に追加されています';
  } else {
    $update_comment = $conn->prepare("UPDATE comments SET comment = ? WHERE id = ?");
    $update_comment->execute([$comment_edit_box, $edit_comment_id]);
    $message[] = 'コメントを修正しました';
  }
}

if (isset($_POST['delete_comment'])) {
  $delete_comment_id = h($_POST['comment_id']);
  $delete_comment = $conn->prepare("DELETE FROM comments WHERE id = ?");
  $delete_comment->execute([$delete_comment_id]);
  $message[] = 'コメントを削除しました';
}

?>

<!DOCTYPE html>
<html lang="ja">

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>コメントを更新</title>

  <!-- font awesome cdn link  -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

  <!-- custom css file link  -->
  <link rel="stylesheet" href="css/style.css">

</head>

<body>

  <?php include 'components/user_header.php'; ?>

  <?php
  if (isset($_POST['open_edit_box'])) {
    $comment_id = h($_POST['comment_id']);
  ?>


    <section class="comment-edit-form">
      <p>コメントの編集</p>
      <div class="box-container">
        <?php
        $select_edit_comment = $conn->prepare("SELECT * FROM comments WHERE id = ?");
        $select_edit_comment->execute([$comment_id]);
        $fetch_edit_comment = $select_edit_comment->fetch(PDO::FETCH_ASSOC);
        ?>
        <form action="" method="POST">
          <input type="hidden" name="edit_comment_id" value="<?php echo $comment_id; ?>">
          <textarea name="comment_edit_box" required id="" cols="30" rows="10" placeholder="コメントを入力してください"><?php echo $fetch_edit_comment['comment']; ?></textarea>
          <button type="submit" class="inline-btn" name="edit_comment">編集</button>
          <div class="inline-option-btn" onclick="window.location.href='user_comments.php';">キャンセル</div>
        </form>
      </div>
    <?php
  }
    ?>
    </section>

    <section class="comments-container">
      <h1 class="heading">コメント</h1>
      <p class="comment-title">投稿に対するコメント</p>
      <div class="user-comments-container">
        <?php
        $select_comments = $conn->prepare("SELECT * FROM comments WHERE user_id = ?");
        $select_comments->execute([$user_id]);
        if ($select_comments->rowCount() > 0) {
          while ($fetch_comments = $select_comments->fetch(PDO::FETCH_ASSOC)) {
        ?>
            <div class="show-comments">
              <?php
              $select_posts = $conn->prepare("SELECT * FROM posts WHERE id = ?");
              $select_posts->execute([$fetch_comments['post_id']]);
              while ($fetch_posts = $select_posts->fetch(PDO::FETCH_ASSOC)) {
              ?>
                <div class="post-title">form : <span><?php echo $fetch_posts['title']; ?></span><a href="view_post.php?post_id=<?php echo $fetch_posts['id']; ?>"> 投稿を見る</a></div>
              <?php
              }
              ?>
              <div class="comment-box"><?php echo $fetch_comments['comment']; ?></div>
              <form action="" method="POST">
                <input type="hidden" name="comment_id" value="<?php echo $fetch_comments['id']; ?>">
                <button type="submit" class="inline-option-btn" name="open_edit_box">コメントを編集</button>
                <button type="submit" class="inline-delete-btn" name="delete_comment" onclick="return confirm('コメントを削除しますか？');">コメントを編集</button>
              </form>
            </div>
        <?php
          }
        } else {
          echo '<p class="empty>コメントはまだありません</p>';
        }
        ?>
      </div>
    </section>

    <?php include 'components/footer.php'; ?>
    <script src="js/script.js"></script>

</body>

</html>