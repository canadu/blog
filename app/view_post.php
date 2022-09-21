<?php

require_once 'components/functions.php';
require_once 'components/connect.php';

session_start();
$user_id = $_SESSION['user_id'];

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
  $delete_comment_id = $conn->prepare("DELETE FROM comments WHERE id = ?");
  $delete_comment->execute([$delete_comment_id]);
  $message[] = 'コメントを削除しました。';
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Document</title>
</head>

<body>

</body>

</html>