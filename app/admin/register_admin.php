<?php

include '../components/connect.php';
session_start();

$message = [];

$admin_id = $_SESSION['admin_id'];

if (!isset($admin_id)) {
    header('location:admin_login.php');
}

if (isset($_POST['submit'])) {

    $name = htmlspecialchars($_POST['name'], ENT_QUOTES);
    $pass = htmlspecialchars($_POST['pass'], ENT_QUOTES);
    $confirm_pass = htmlspecialchars($_POST['confirm_pass'], ENT_QUOTES);

    //同一ユーザー名の存在チェック
    $select_admin = $conn->prepare("SELECT * FROM admin WHERE name = ?");
    $select_admin->execute([$name]);

    if ($select_admin->rowCount() > 0) {
        $message[] = '同じユーザー名が登録されています。';
    } else {
        if ($pass == $confirm_pass) {
            $insert_admin = $conn->prepare("INSERT INTO admin(name,password) VALUES(?,?)");
            $insert_admin->execute([$name, password_hash($confirm_pass, ENT_QUOTES)]);
            $message[] = '新しい管理者を登録しました。';
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
    <title>管理者登録</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

    <link rel="stylesheet" href="../css/admin_style.css">
</head>

<body>

    <?php include '../components/admin_header.php' ?>

    <!-- admin register form section starts -->
    <section class="form-container">
        <form action="" method="POST">
            <h3>管理者登録</h3>
            <input type="text" name="name" maxlength="20" required placeholder="ユーザー名を入力して下さい。" class="box" oninput="this.value= this.replace(/\s/g,'')">
            <input type="password" name="pass" maxlength="20" required placeholder="パスワードを入力して下さい。" class="box" oninput="this.value= this.replace(/\s/g,'')">
            <input type="password" name="confirm_pass" maxlength="20" required placeholder="確認用にもう一度パスワードを入力して下さい。" class="box" oninput="this.value= this.replace(/\s/g,'')">
            <input type="submit" value="登録" name="submit" class="btn">
        </form>
    </section>
    <!-- admin register form section ends -->

</body>

</html>