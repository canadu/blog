<?php

require_once '../components/functions.php';
require_once '../components/connect.php';

session_start();

define('STATUS', array('active', 'deactive'));
$admin_id = $_SESSION['admin_id'];
$message = [];
if (!isset($admin_id)) {
    header('location:admin_login.php');
}

if (isset($_POST['publish'])) {
    //投稿
    Post($admin_id, STATUS[0], $conn);
}

if (isset($_POST['draft'])) {
    //下書き
    Post($admin_id, STATUS[1], $conn);
}

function Post(string $id, string $param_status, PDO $conn)
{
    global $message;
    $name = h($_POST['name']);
    $title = h($_POST['title']);
    $content = h($_POST['content']);
    $category = h($_POST['category']);
    $status = $param_status;

    $image = htmlspecialchars($_FILES['image']['name'], ENT_QUOTES);
    $image_size = $_FILES['image']['size'];
    $image_tmp_name = $_FILES['image']['tmp_name'];
    $image_folder = '../uploaded_img/' . $image;

    $select_image = $conn->prepare("SELECT * FROM posts WHERE image = ? AND admin_id = ?");
    $select_image->execute([$image, $id]);

    if (isset($image) && !empty($image)) {
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
        $message[] = '画像ファイルのファイル名を変更してください';
    } else {
        $insert_post = $conn->prepare("INSERT INTO posts(admin_id, name, title, content, category, image, status) VALUES(?,?,?,?,?,?,?)");
        $insert_post->execute([$id, $name, $title, $content, $category, $image, $status]);
        $message[] = $param_status == STATUS[0]  ? '投稿しました。' : '下書きに保存しました。';
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

    <section class="post-editor">
        <h1 class="heading">新規投稿</h1>
        <form action="" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="name" value="<?php echo $fetch_profile['name']; ?>">
            <p>投稿タイトル<span>*</span></p>
            <input type="text" name="title" maxlength="100" require placeholder="投稿タイトルを入力してください。" class="box">
            <p>投稿記事<span>*</span></p>
            <textarea name="content" class="box" required maxlength="10000" placeholder="記事を入力してください。" cols="30" rows="10"></textarea>
            <p>投稿カテゴリ<span>*</span></p>
            <select name="category" class="box" required>
                <option value="" selected disabled>-- カテゴリを選択</option>
                <?php foreach ($category_array as $key => $value) { ?>
                    <option value="<?php echo $key; ?>"><?php echo $value; ?></option>
                <?php } ?>
            </select>
            <p>投稿画像</p>
            <input type="file" name="image" class="box" accept="image/jpg, image/jpeg, image/png, image/webp">
            <div class="flex-btn">
                <input type="submit" value="投稿" name="publish" class="btn">
                <input type="submit" value="下書き" name="draft" class="option-btn">
            </div>
        </form>
    </section>
    <!-- custom js file link  -->
    <script src="../js/admin_script.js"></script>

</body>

</html>