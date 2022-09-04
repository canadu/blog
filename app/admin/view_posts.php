<?php
include '../components/connect.php';
session_start();
$admin_id = $_SESSION['admin_id'];

if (!isset($admin_id)) {
    header('location:admin_login.php');
}

if (isset($_POST['delete'])) {
    //削除処理
    $p_id = htmlspecialchars($_POST['post_id'], ENT_QUOTES);
    $delete_image = $conn->prepare("SELECT * FROM posts WHERE id = ?");
    $delete_image->execute([$p_id]);
    $fetch_delete_image = $delete_image->fetch(PDO::FETCH_ASSOC);
    if ($fetch_delete_image['image'] != '') {
        unlink('../uploaded_img/' . $fetch_delete_image['image']);
    }
    $delete_post = $conn->prepare("DELETE FROM posts WHERE id = ?");
    $delete_post->execute([$p_id]);
    $delete_comments = $conn->prepare("DELETE FROMo comments WHERE post_id = ?");
    $message[] = '投稿を削除しました。';
}

?>

<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>投稿を見る</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

    <link rel="stylesheet" href="../css/admin_style.css">
</head>

<body style="padding-left:0 !important;">

    <?php include '../comments/admin_header.php' ?>

    <section class="show-posts">
        <h1 class="heading">あなたの投稿</h1>
        <div class="box-container">
            <?php
            $select_posts = $conn->prepare("SELECT * FROM posts WHERE admin_id = ?");
            $select_posts->execute([$admin_id]);
            ?>
        </div>
    </section>
</body>

</html>