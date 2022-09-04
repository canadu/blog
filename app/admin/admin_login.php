<?php

include '../components/connect.php';
session_start();

$message = [];

if (isset($_POST['submit'])) {

  $name = htmlspecialchars($_POST['name'], ENT_QUOTES);
  $pass = htmlspecialchars($_POST['pass'], ENT_QUOTES);

  $select_admin = $conn->prepare("SELECT * FROM admin WHERE name = ?");
  $select_admin->execute([$name]);

  if ($select_admin->rowCount() > 0) {
    $fetch_admin_id = $select_admin->fetch(PDO::FETCH_ASSOC);
    //入力したパスワードと登録済みのパスワードの比較
    if (password_verify($pass, $fetch_admin_id['password']) === TRUE) {
      $_SESSION['admin_id'] = $fetch_admin_id['id'];
      header('location:dashboard.php');
    } else {
      $message[] = 'ユーザー名かパスワードが違います。';
    }
  } else {
    $message[] = 'ユーザー名かパスワードが違います。';
  }
}
?>
<!DOCTYPE html>
<html lang="ja">

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>ログイン</title>

  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
  <link rel="stylesheet" href="../css/admin_style.css">

</head>

<body style="padding-left:0 !important;">
  <?php
  // エラーメッセージを表示する
  if (isset($message)) {
    foreach ($message as $message) {
      echo '
      <div class="message">
        <span>' . $message . '</span>
        <i class="fas fa-times" onclick="this.parentElement.remove();"></i>
      </div>
      ';
    }
  }
  ?>

  <!-- admin login form section starts -->
  <section class="form-container">
    <form action="" method="POST">
      <h3>login</h3>
      <input type="text" name="name" maxlength="20" required placeholder="ユーザー名を入力して下さい。" class="box" oninput="this.value= this.replace(/\s/g,'')">
      <input type="password" name="pass" maxlength="20" required placeholder="パスワードを入力して下さい。" class="box" oninput="this.value= this.replace(/\s/g,'')">
      <input type="submit" value="ログイン" name="submit" class="btn">
    </form>
  </section>
  <!-- admin login form section ends -->

</body>

</html>