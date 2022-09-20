<?php

require_once 'components/functions.php';
require_once 'components/connect.php';

session_start();

if (isset($_SESSION['user_id'])) {
  $user_id = $_SESSION['user_id'];
} else {
  $user_id = '';
}

if (isset($_POST['name'])) {
  $name = $_POST['name'];
} else {
  $name = '';
}
if (isset($_POST['email'])) {
  $email = $_POST['email'];
} else {
  $email = '';
}

if (isset($_POST['submit'])) {

  $name = h($_POST['name']);
  $email = h($_POST['email']);
  $pass = h($_POST['pass']);
  $confirm_pass = h($_POST['confirm_pass']);

  //同一ユーザー名の存在チェック
  $select_user = $conn->prepare("SELECT * FROM users WHERE email = ?");
  $select_user->execute([$email]);
  $row = $select_user->fetch(PDO::FETCH_ASSOC);

  if ($select_user->rowCount() > 0) {
    $message[] = '同じユーザー名が登録されています。';
  } else {
    if ($pass == $confirm_pass) {

      //確認用パスワードも同じ値が入力されている場合
      $insert_user = $conn->prepare("INSERT INTO users(name, email, password) VALUES(?,?,?)");
      $insert_user->execute([$name, $email, password_hash($confirm_pass, ENT_QUOTES)]);

      $select_user = $conn->prepare("SELECT * FROM users WHERE email = ?");
      $select_user->execute([$email]);
      $row = $select_user->fetch(PDO::FETCH_ASSOC);

      if ($select_user->rowCount() > 0) {
        $_SESSION['user_id'] = $row['id'];
        header('location:home.php');
      }
    } else {
      $message[] = "パスワードが一致しません。";
    }
  }
}


?>
<!DOCTYPE html>
<html lang="ja">

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>ユーザー登録</title>

  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
  <link rel="stylesheet" href="css/style.css">
</head>

<body>

  <?php include 'components/user_header.php' ?>

  <section class="form-container">
    <form action="" method="POST">
      <h3>ユーザー登録</h3>
      <input type="text" name="name" maxlength="20" required value="<?php echo $name; ?>" placeholder="ユーザー名を入力して下さい。" class="box" oninput="this.value= this.replace(/\s/g,'')">
      <input type="email" name="email" maxlength="50" required value="<?php echo $email; ?>" placeholder="メールアドレスを入力して下さい。" class="box" oninput="this.value= this.replace(/\s/g,'')">
      <input type="password" name="pass" maxlength="50" required placeholder="パスワードを入力して下さい。" class="box" oninput="this.value= this.replace(/\s/g,'')">
      <input type="password" name="confirm_pass" maxlength="50" required placeholder="確認用にもう一度パスワードを入力して下さい。" class="box" oninput="this.value= this.replace(/\s/g,'')">
      <input type="submit" value="登録" name="submit" class="btn">
      <p>ログインは<a href="login.php">こちら</a></p>
    </form>
  </section>

  <?php include 'components/footer.php'; ?>

  <script src="js/script.js"></script>
</body>

</html>