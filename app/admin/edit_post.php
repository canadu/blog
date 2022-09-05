<?php
include '../components/connect.php';

session_start();

$admin_id = $_SESSION['admin_id'];

if (!isset($admin_id)) {
    header('location:admin_login.php');
}

if (isset($_POST['save'])) {
    //編集
    $post_id = $_GET['id'];
    $title = htmlspecialchars($_POST['title'], ENT_QUOTES);
    $content = htmlspecialchars($_POST['content'], ENT_QUOTES);
    $category = htmlspecialchars($_POST['category'], ENT_QUOTES);
    $status = htmlspecialchars($_POST['status'], ENT_QUOTES);

    $update_post = $conn->prepare("UPDATE posts SET title=?, content=?, category=?, status=? WHERE id=?");
    $update_post->execute([$title, $content, $category, $status, $post_id]);
    $message[] = '更新しました。';

    $old_image = $_POST['old_image'];
    $image = htmlspecialchars($_FILES['image']['name'], ENT_QUOTES);
    $image_size = $_FILES['image']['size'];
    $image_tmp_name = $_FILES['image']['tmp_name'];
    $image_folder = '../uploaded_img/' . $image;
    $select_image = $conn->prepare("SELECT * FROM posts WHERE image = ? AND admin_id = ?");
    $select_image->execute([$image, $admin_id]);

    if (!empty($image)) {
        if ($image_size > 2000000) {
            $message[] = '画像サイズが大きすぎます。';
        } elseif ($select_image->rowCount() > 0 and $image != '') {
            $message[] = '画像ファイル名が同じです。';
        } else {
            $update_image = $conn->prepare("UPDATE posts SET image = ? WHERE id = ?");
            move_uploaded_file($image_tmp_name, $image_folder);
            $update_image->execute([$image, $post_id]);
            if ($old_image != $image and $old_image != '') {
                unlink('../uploaded_img/' . $old_image);
            }
            $message[] = '画像を更新しました。';
        }
    }
}

if (isset($_POST['delete_post'])) {
    //削除
    $post_id = htmlspecialchars($_POST['post_id'], ENT_QUOTES);
    $delete_image = $conn->prepare("SELECT * FROM posts WHERE id = ?");
    $delete_image->execute([$post_id]);
    $fetch_delete_image = $delete_image->fetch(PDO::FETCH_ASSOC);
    if ($fetch_delete_image['image'] != '') {
        unlink('../uploaded_img/' . $fetch_delete_image['image']);
    }
    $delete_post = $conn->prepare("DELETE FROM posts WHERE id = ?");
    $delete_post->execute([$post_id]);
    $delete_comments = $conn->prepare("DELETE FROM comments WHERE post_id = ?");
    $delete_comments->execute([$post_id]);
    $message[] = '投稿を削除しました。';
}

if (isset($_POST['delete_image'])) {
    //画像削除
    $empty_image = '';
    $post_id = htmlspecialchars($_POST['post_id'], ENT_QUOTES);

    $delete_image = $conn->prepare("SELECT * FROM posts WHERE id = ?");
    $delete_image->execute([$post_id]);
    $fetch_delete_image = $delete_image->fetch(PDO::FETCH_ASSOC);
    if ($fetch_delete_image['image'] != '') {
        unlink('../uploaded_img/' . $fetch_delete_image['image']);
    }
    $unset_image = $conn->prepare('UPDATE posts SET image = ? WHERE id = ?');
    $unset_image->execute([$empty_image, $post_id]);
    $message[] = '画像を削除しました。';
}

?>

<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>投稿記事の修正</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    <link rel="stylesheet" href="../css/admin_style.css">
</head>

<body>
    <?php include '../components/admin_header.php' ?>
    <section class="post-editor">
        <h1 class="heading">投稿記事の修正</h1>
        <?php
        $post_id = $_GET['id'];
        $select_posts = $conn->prepare("SELECT * FROM posts WHERE id = ?");
        $select_posts->execute([$post_id]);
        if ($select_posts->rowCount() > 0) :
            while ($fetch_posts = $select_posts->fetch(PDO::FETCH_ASSOC)) {
        ?>
                <form action="" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="old_image" value="<?php echo $fetch_posts['image']; ?>">
                    <input type="hidden" name="post_id" value="<?php echo $fetch_posts['id']; ?>">

                    <p>投稿ステータス <span>*</span></p>
                    <select name="status" class="box" required>
                        option value="<?php echo $fetch_posts['status']; ?>" selected><?php echo $fetch_posts['status'] == 'active' ? '公開' : '非公開'; ?></option>
                        <option value="active">公開</option>
                        <option value="deactive">非公開</option>
                    </select>

                    <p>投稿タイトル<span>*</span></p>
                    <input type="text" name="title" maxlength="100" required placeholder="投稿タイトルを入力してください。" class="box" value="<?php echo $fetch_posts['title']; ?>">

                    <p>投稿記事<span>*</span></p>
                    <textarea name="content" class="box" required maxlength="10000" placeholder="記事を入力してください。" cols="30" rows="10"><?php echo $fetch_posts['content']; ?></textarea>

                    <p>投稿カテゴリ<span>*</span></p>
                    <select name="category" class="box" required>
                        <option value="<?php echo $fetch_posts['category']; ?>" selected><?php echo $fetch_posts['category']; ?></option>
                        <option value="nature">自然</option>
                        <option value="education">教育</option>
                        <option value="pets and animals">ペットや動物</option>
                        <option value="technology">テクノロジー</option>
                        <option value="fashion">ファッション</option>
                        <option value="entertainment">娯楽</option>
                        <option value="movies and animations">映画</option>
                        <option value="gaming">ゲーム</option>
                        <option value="music">音楽</option>
                        <option value="sports">スポーツ</option>
                        <option value="news">ニュース</option>
                        <option value="travel">旅行</option>
                        <option value="comedy">お笑い</option>
                        <option value="design and development">デザインや開発</option>
                        <option value="food and drinks">食べ物</option>
                        <option value="lifestyle">生活</option>
                        <option value="personal">人物</option>
                        <option value="health and fitness">健康</option>
                        <option value="business">仕事</option>
                        <option value="shopping">買い物</option>
                        <option value="animations">アニメ</option>
                    </select>

                    <p>投稿画像</p>
                    <input type="file" name="image" class="box" accept="image/jpg, image/jpeg, image/png, image/webp">
                    <?php if ($fetch_posts['image'] != '') : ?>
                        <img src="../uploaded_img/<?php echo $fetch_posts['image']; ?>" class="image" alt="">
                        <input type="submit" value="画像削除" class="inline-delete-btn" name="delete_image">
                    <?php endif; ?>

                    <div class="flex-btn">
                        <input type="submit" value="編集" name="save" class="btn">
                        <a href="view_posts.php" class="option-btn">戻る</a>
                        <input type="submit" value="投稿を削除" class="delete-btn" name="delete_post">
                    </div>
                </form>
            <?php } ?>
        <?php else : ?>
            <?php echo '<p class="empty">投稿がありません。</p>'; ?>
            <div class="flex-btn">
                <a href="view_posts.php" class="option-btn">投稿を見る</a>
                <a href="add_posts.php" class="option-btn">投稿する</a>
            </div>
        <?php endif; ?>
    </section>
    <!-- custom js file link  -->
    <script src="../js/admin_script.js"></script>

</body>

</html>