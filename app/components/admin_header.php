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
    <a href="dashboard.php" class="logo">Admin<span>Panel</span></a>
    <div class="profile">
        <?php
        // 管理者情報を取得
        $select_profile = $conn->prepare("SELECT * FROM admin WHERE id = ?");
        $select_profile->execute([$admin_id]);
        $fetch_profile = $select_profile->fetch(PDO::FETCH_ASSOC);
        ?>
        <p><?php echo $fetch_profile['name']; ?></p>
        <a href="update_profile.php" class="btn">プロフィールを更新</a>
    </div>
    <nav class="navbar">
        <a href="dashboard.php"><i class="fas fa-home"></i><span>ホーム</span></a>
        <a href="add_posts.php"><i class="fas fa-pen"></i><span>投稿</span></a>
        <a href="view_posts.php"><i class="fas fa-eye"></i><span>閲覧</span></a>
        <a href="admin_accounts.php" onclick="return confirm('logout from the website?');"><i class="fas fa-user"></i><span>アカウント</span></a>
        <a href="../components/admin_logout.php"><i class="fas fa-right-from-bracket"></i><span style="color:var(--red);">ログアウト</span></a>
    </nav>
    <div class="flex-btn">
        <a href="admin_login.php" class="option-btn">ログイン</a>
        <a href="register_admin.php" class="option-btn">登録</a>
    </div>
</header>

<div id="menu-btn" class="fas fa-bars"></div>