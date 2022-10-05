<?php
$title = '投稿記事の修正';
include 'admin_header.php';
?>
<section class="post-editor">
  <h1 class="heading">投稿記事の修正</h1>
  <?php

  if (!empty($posts_data)) :
    foreach ($posts_data as $post_data) {
  ?>
      <form action="" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="old_image" value="<?php echo $post_data['image']; ?>">
        <input type="hidden" name="post_id" value="<?php echo $post_data['id']; ?>">

        <p>投稿ステータス <span>*</span></p>
        <select name="status" class="box" required>
          <option value="<?php echo $post_data['status']; ?>" selected><?php echo $post_data['status'] == 'active' ? '公開' : '非公開'; ?></option>
          <option value="active">公開</option>
          <option value="deactive">非公開</option>
        </select>

        <p>投稿タイトル<span>*</span></p>
        <input type="text" name="title" maxlength="100" required placeholder="投稿タイトルを入力してください。" class="box" value="<?php echo $post_data['title']; ?>">

        <p>投稿記事<span>*</span></p>
        <textarea name="content" class="box" required maxlength="10000" placeholder="記事を入力してください。" cols="30" rows="10"><?php echo $post_data['content']; ?></textarea>

        <p>投稿カテゴリ<span>*</span></p>
        <select name="category" class="box" required>
          <option value="<?php echo $post_data['category']; ?>" selected><?php echo $category_array[$post_data['category']]; ?></option>
          <?php foreach ($category_array as $key => $value) { ?>
            <?php if ($key != $post_data['category']) : ?>
              <option value="<?php echo $key; ?>"><?php echo $value; ?></option>
            <?php endif; ?>
          <?php } ?>
        </select>

        <p>投稿画像</p>
        <input type="file" name="image" class="box" accept="image/jpg, image/jpeg, image/png, image/webp">
        <?php if ($post_data['image'] != '') : ?>
          <img src="../uploaded_img/<?php echo $post_data['image']; ?>" class="image" alt="">
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
<?php include 'admin_footer.php'; ?>