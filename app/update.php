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

if (isset($_POST['submit'])) {

  $name = h($_POST['name']);
  $email = h($_POST['email']);

  if (!empty($name)) {
    $update_name = $conn->prepare("UPDATE users SET name = ? WHERE id = ?");
    $update_name->execute([$name, $user_id]);
  }
  if (!empty($email)) {
    $select_email = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $select_email->execute([$email]);
    if ($select_email->rowCount() > 0) {
      $message[] = "このメールアドレスは使用できません。";
    } else {
      $update_email = $conn->prepare("UPDATE users SET email = ? WHERE id = ?");
      $update_email->execute([$email, $user_id]);
    }
  }

  //現在のパスワードを取得
  $select_prev_pass = $conn->prepare("SELECT password FROM users WHERE id = ?");
  $select_prev_pass->execute([$user_id]);
  $fetch_prev_pass = $select_prev_pass->fetch(PDO::FETCH_ASSOC);
  $prev_pass = $fetch_prev_pass['password'];

  $old_pass = h($_POST['old_pass']);
  $new_pass = h($_POST['new_pass']);
  $confirm_pass = h($_POST['confirm_pass']);

  if (!empty($old_pass)) {
    if (!password_verify($old_pass, $prev_pass)) {
      $message[] = '古いパスワードが一致しません。';
    } elseif ($new_pass != $confirm_pass) {
      $message[] = 'パスワードが一致しません。';
    } else {
      $update_pass = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
      $update_pass->execute([password_hash($confirm_pass, ENT_QUOTES), $user_id]);
      $message[] = 'パスワードを更新しました。';
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
  <title>プロフィールの更新</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
  <link rel="stylesheet" href="css/style.css">
</head>

<body>

  <?php include 'components/user_header.php' ?>

  <section class="form-container">
    <form action="" method="POST" autocomplete="off">
      <h3>プロフィールの更新</h3>
      <input type="text" name="name" maxlength="50" class="box" oninput="this.value = this.value.replace(/\s/g, '')" placeholder="<?php echo $fetch_profile['name']; ?>">
      <input type="email" name="email" maxlength="50" class="box" oninput="this.value = this.value.replace(/\s/g, '')" placeholder="<?php echo $fetch_profile['email']; ?>">
      <input type="password" name="old_pass" maxlength="50" placeholder="現在のパスワードを入力してください。" class="box" oninput="this.value = this.value.replace(/\s/g, '')">
      <input type="password" name="new_pass" maxlength="50" placeholder="新しいパスワードを入力してください。" class="box" oninput="this.value = this.value.replace(/\s/g, '')">
      <input type="password" name="confirm_pass" maxlength="50" placeholder="確認用に新しいパスワードを入力してください。" class="box" oninput="this.value = this.value.replace(/\s/g, '')">
      <input type="submit" value="更新" name="submit" class="btn">
    </form>
  </section>

  <?php include 'components/footer.php' ?>

  <!-- admin profile update section ends -->
  <script src="js/script.js"></script>
</body>

</html>