<?php

require_once 'components/functions.php';

if (isset($_POST['like_post'])) {

  if ($user_id != '') {

    $post_id = h($_POST['post_id']);
    $admin_id = h($_POST['admin_id']);

    $select_post_like = $conn->prepare("SELECT * FROM likes WHERE post_id = ? AND user_id = ?");
    $select_post_like->execute([$post_id, $user_id]);

    if ($select_post_like->rowCount() > 0) {
      //既にレコードがある場合(いいねが押されている場合)
      $remove_like = $conn->prepare("DELETE FROM likes WHERE post_id = ?");
      $remove_like->execute([$post_id]);
      $message[] = 'いいねを取り消しました';
    } else {
      //いいねが押されていない場合
      $add_like = $conn->prepare("INSERT INTO likes(user_id, post_id, admin_id) VALUES(?,?,?)");
      $add_likes->execute([$user_id, $post_id, $admin_id]);
      $message[] = 'いいねを追加しました';
    }
  } else {
    $message[] = '最初にログインしてください';
  }
}
