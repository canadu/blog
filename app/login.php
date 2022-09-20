<?php
require_once('components/connect.php');
require_once('components/functions.php');

session_start();

if (isset($_SESSION['user_id'])) {
  $user_id = $_SESSION['user_id'];
} else {
  $user_id = '';
};

if (isset($_POST['email'])) {
  $email = $_POST['email'];
} else {
  $email = '';
};

if (isset($_POST['submit'])) {

  $email = h($_POST['email']);
  $pass = h($_POST['pass']);

  $select_user = $conn->prepare("SELECT * FROM users WHERE email = ?");
  $select_user->execute([$email]);

  if ($select_user->rowCount() > 0) {
    $row = $select_user->fetch(PDO::FETCH_ASSOC);
    //入力したパスワードと登録済みのパスワードの比較
    if (password_verify($pass, $row['password'])) {
      $_SESSION['user_id'] = $row['id'];
      header('location:home.php');
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
  <link rel="stylesheet" href="css/style.css">

</head>

<body>

  <?php include 'components/user_header.php'; ?>

  <section class="form-container">
    <form action="" method="POST">
      <h3>login</h3>
      <input type="email" name="email" maxlength="50" value="<?php echo $email; ?>" required placeholder="メールアドレスを入力して下さい。" class="box" oninput="this.value= this.replace(/\s/g,'')">
      <input type="password" name="pass" maxlength="50" required placeholder="パスワードを入力して下さい。" class="box" oninput="this.value= this.replace(/\s/g,'')">
      <input type="submit" value="ログイン" name="submit" class="btn">
      <p>アカウント登録は<a href="register.php">こちら</a></p>
    </form>
  </section>

  <?php include 'components/footer.php'; ?>

  <script src="js/script.js"></script>
</body>

</html>