<?php


require_once '../components/functions.php';
require_once '../components/connect.php';

session_start();
$admin_id = $_SESSION['admin_id'];

if (!isset($admin_id)) {
    header('location:admin_login.php');
    exit();
}

if (isset($_POST['submit'])) {

    //ユーザー名の更新
    $name = h($_POST['name']);
    if (!empty($name)) {

        $select_name = $conn->prepare("SELECT * FROM admin WHERE name = :name");
        $select_name->bindValue(':name', $name, PDO::PARAM_STR);
        $select_name->execute();

        if ($select_name->rowCount() > 0) {
            $message[] = 'ユーザー名は既に利用されています。';
        } else {

            $update_name = $conn->prepare("UPDATE admin SET name = :name WHERE id = :id");
            $update_name->bindValue(':name', $name, PDO::PARAM_STR);
            $update_name->bindValue(':id', $admin_id, PDO::PARAM_INT);
            $update_name->execute();

            $message[] = 'ユーザー名を更新しました。';
        }
    }

    //現在のパスワードを取得
    $select_old_pass = $conn->prepare("SELECT password FROM admin WHERE id = :id");
    $select_old_pass->bindValue(':id', $admin_id, PDO::PARAM_INT);
    $select_old_pass->execute();

    $fetch_prev_pass = $select_old_pass->fetch(PDO::FETCH_ASSOC);
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
            $update_pass = $conn->prepare("UPDATE admin SET password = :password WHERE id = :id");
            $update_pass->bindValue(':password', password_hash(($confirm_pass), ENT_QUOTES), PDO::PARAM_STR);
            $update_pass->bindValue(':id', $admin_id, PDO::PARAM_INT);
            $update_pass->execute();
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

    <link rel="stylesheet" href="../css/admin_style.css">
</head>

<body>
    <?php include '../components/admin_header.php' ?>

    <!-- admin profile update section starts -->

    <section class="form-container">
        <form action="" method="POST">
            <h3>プロフィールの更新</h3>
            <input type="text" name="name" maxlength="20" class="box" oninput="this.value = this.value.replace(/\s/g, '')" placeholder="<?php echo $fetch_profile['name']; ?>">
            <input type="password" name="old_pass" maxlength="20" placeholder="現在のパスワードを入力してください。" class="box" oninput="this.value = this.value.replace(/\s/g, '')">
            <input type="password" name="new_pass" maxlength="20" placeholder="新しいパスワードを入力してください。" class="box" oninput="this.value = this.value.replace(/\s/g, '')">
            <input type="password" name="confirm_pass" maxlength="20" placeholder="確認用に新しいパスワードを入力してください。" class="box" oninput="this.value = this.value.replace(/\s/g, '')">
            <input type="submit" value="更新" name="submit" class="btn">
        </form>
    </section>

    <!-- admin profile update section ends -->
    <script src="../js/admin_script.js"></script>
</body>

</html>