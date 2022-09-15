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
          $count_user_comments = $conn->prepare("SELECT * FROM comments WHERE user_id = ?");
          $count_user_comments->execute([$user_id]);
          $total_user_comments = $count_user_comments->rowCount();

          $count_user_likes = $conn->prepare("SELECT * FROM likes WHERE user_id = ?");
          $count_user_likes->execute([$user_id]);
          $total_user_likes = $count_user_likes->rowCount();
        }
        ?>
      </div>
    </div>
  </section>
</body>

</html>