<?php

include '../components/connect.php';
session_start();

$admin_id = $_SESSION['admin_id'];

if (!isset($admin_id)) {
    header('location:admin_login.php');
}

if (isset($_POST['publish'])) {
    //投稿
    Post($admin_id, 'active', $conn);
}

if (isset($_POST['draft'])) {
    //下書き
    Post($admin_id, 'deactive', $conn);
}

function Post(string $id, string $param_status, PDO $conn)
{

    $name = htmlspecialchars($_POST['name'], ENT_QUOTES);
    $title = htmlspecialchars($_POST['title'], ENT_QUOTES);
    $content = htmlspecialchars($_POST['content'], ENT_QUOTES);
    $category = htmlspecialchars($_POST['category'], ENT_QUOTES);
    $status = $param_status;

    $image = htmlspecialchars($_FILES['image']['name'], ENT_QUOTES);
    $image_size = $_FILES['image']['size'];
    $image_tmp_name = $_FILES['image']['tmp_name'];
    $image_folder = '../uploaded_img/' . $image;

    $select_image = $conn->prepare("SELECT * FROM posts WHERE image = ? AND admin_id = ?");
    $select_image->execute([$image, $id]);

    if (isset($image)) {
        if ($select_image->rowCount() > 0 and $image != '') {
            $message[] = '画像ファイル名が同じです。';
        } elseif ($image_size > 2000000) {
            $message[] = '画像のファイルサイズが大きすぎます。';
        } else {
            move_uploaded_file($image_tmp_name, $image_folder);
        }
    } else {
        $image = '';
    }

    if ($select_image->rowCount() > 0 and $image != '') {
        $message[] = '画像ファイルをファイル名を変更してください';
    } else {
        $insert_post = $conn->prepare("INSERT INTO posts(admin_idm, name, title, content, category, image, status) VALUES(?,?,?,?,?,?,?)");
        $insert_post->execute([$id, $name, $title, $content, $category, $image, $status]);
        $message[] = $param_status == 'active'  ? '投稿しました。' : '下書きに保存しました。';
    }
}

?>
<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>新規投稿</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

    <link rel="stylesheet" href="../css/admin_style.css">
</head>

<body>

    <?php include '../components/admin_header.php' ?>

</body>

</html>