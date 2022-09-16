<?php
if (isset($message)) {
  foreach ($message as $message) {
    echo '
    <div class="message">
      <span>' . $message . '</span>
      <i class="fas fa-times" onclick="this.parentElement.remove();"></i>
    </div>
    ';
  }
}
?>
<header class="header">
  <section class="flex">

    <a href="home.php" class="logo">Blog</a>

    <!-- 検索フォーム -->
    <form action="search.php" method="post" class="search-form">
      <input type="text" name="search_box" class="box" maxlength="100" placeholder="検索" required>
      <button type="submit" class="fas fa-search" name="search_btn"></button>
    </form>

    <div class="icons">
      <div id="menu-btn" class="fas fa-bars"></div>
      <div id="search-btn" class="fas fa-search"></div>
      <div id="user-btn" class="fas fa-user"></div>
    </div>

    <nav class="navbar">
      <a href="home.php"><i class="fas fa-angle-right"></i>ホーム</a>
      <a href="posts.php"><i class="fas fa-angle-right"></i>投稿</a>
      <a href="all_category.php"><i class="fas fa-angle-right"></i>カテゴリ</a>
      <a href="authors.php"><i class="fas fa-angle-right"></i>著者</a>
      <a href="login.php"><i class="fas fa-angle-right"></i>ログイン</a>
      <a href="register.php"><i class="fas fa-angle-right"></i>登録</a>
    </nav>

    <div class="profile">
      <?php
      $select_profile = $conn->prepare("SELECT * FROM users WHERE id = ?");
      $select_profile->execute([$user_id]);
      if ($select_profile->rowCount() > 0) {
        $fetch_profile = $select_profile->fetch(PDO::FETCH_ASSOC);
      }
      ?>
      <p class="name"><?php echo $fetch_profile['name']; ?></p>
      <a href="update.php" class="btn">プロフィールを更新</a>
      <div class="flex-btn">
        <a href="login.php" class="option-btn">ログイン</a>
        <a href="register.php" class="option-btn">登録</a>
      </div>
    </div>
    <a href="components/admin_logout.php" style="color:var(--red);" onclick="return confirm('サイトからログアウトしますか？');" class="delete-btn">ログアウト</a>



  </section>
</header>